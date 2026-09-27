<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
namespace app\services\wechat;

use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FileService;
use think\facade\Log;

/**
 * Lớp service cốt lõi CI (Continuous Integration) của Mini Program
 * 
 * Tổng quan chức năng:
 * Lớp service này đóng gói logic gọi công cụ miniprogram-ci chính thức của WeChat,
 * thực hiện chức năng tự động tải lên và xem trước code Mini Program.
 * 
 * Chức năng chính:
 * 1. Quản lý khóa tải lên - Lưu/xóa khóa tải lên code Mini Program
 * 2. Chuẩn bị dự án - Sao chép mã nguồn và thay thế các cấu hình như AppId, URL
 * 3. Tải lên code - Gọi lệnh miniprogram-ci upload để tải lên code
 * 4. Mã QR xem trước - Gọi lệnh miniprogram-ci preview để tạo mã xem trước
 * 
 * Công cụ phụ thuộc:
 * - Node.js >= 14.0.0
 * - npm (trình quản lý gói Node.js)
 * - miniprogram-ci (cài đặt toàn cục: npm install miniprogram-ci -g)
 * 
 * Lấy khóa:
 * Nền tảng WeChat Official Accounts -> Quản lý phát triển -> Cài đặt phát triển -> Tải lên code Mini Program
 * 
 * @see https://developers.weixin.qq.com/miniprogram/dev/devtools/ci.html
 * @package app\services\wechat
 */
class RoutineCIServices extends BaseServices
{
    /**
     * Đường dẫn lưu trữ file dự án Mini Program
     * 
     * Trước khi tải lên, mã nguồn sẽ được sao chép vào thư mục này và thay thế cấu hình rồi mới tải lên.
     * Đường dẫn mặc định: public/statics/download
     * 
     * @var string
     */
    protected $projectPath;

    /**
     * Đường dẫn lưu trữ file khóa tải lên code Mini Program
     * 
     * File private key RSA, dùng để xác thực cho miniprogram-ci.
     * Đường dẫn mặc định: config/routine_private.key
     * 
     * Lưu ý bảo mật: File này chứa thông tin nhạy cảm, cần đặt quyền file phù hợp,
     * và loại trừ trong .gitignore để tránh bị commit lên hệ thống quản lý phiên bản.
     * 
     * @var string
     */
    protected $privateKeyPath;

    /**
     * AppId Mini Program
     * 
     * Đọc từ cấu hình hệ thống, mục cấu hình là routine_appId.
     * Dùng để xác định Mini Program đích, khi tải lên phải trùng với Mini Program tương ứng với khóa.
     * 
     * @var string
     */
    protected $appId;

    /**
     * Hàm khởi tạo - Khởi tạo các đường dẫn cấu hình
     * 
     * Khởi tạo các cấu hình đường dẫn cần thiết cho việc tải lên Mini Program:
     * - Đường dẫn lưu trữ file dự án
     * - Đường dẫn lưu trữ file khóa
     * - Đọc AppId từ cấu hình hệ thống
     */
    public function __construct()
    {
        // Đặt thư mục lưu trữ file dự án (public/statics/download)
        $this->projectPath = public_path() . 'statics' . DIRECTORY_SEPARATOR . 'download';
        // Đặt đường dẫn lưu trữ file khóa (config/routine_private.key)
        $this->privateKeyPath = app()->getRootPath() . 'config' . DIRECTORY_SEPARATOR . 'routine_private.key';
        // Đọc AppId Mini Program từ cấu hình hệ thống
        $this->appId = sys_config('routine_appId', '');
    }

    /**
     * Lấy thông tin trạng thái cấu hình tải lên
     * 
     * Trả về toàn bộ trạng thái cấu hình liên quan đến việc tải lên Mini Program hiện tại,
     * frontend dựa vào các thông tin này để hiển thị trạng thái cấu hình và hướng dẫn người dùng hoàn tất cấu hình.
     * 
     * @return array Thông tin trạng thái cấu hình, gồm:
     *               - app_id: AppId của Mini Program
     *               - app_id_configured: AppId đã được cấu hình chưa
     *               - private_key_exists: File khóa có tồn tại không
     *               - private_key_path: Đường dẫn file khóa
     *               - project_path: Đường dẫn file dự án
     *               - project_exists: thư mục dự án có tồn tại không
     */
    public function getUploadConfig(): array
    {
        return [
            'app_id' => $this->appId,                           // AppId Mini Program
            'app_id_configured' => !empty($this->appId),        // AppId đã được cấu hình chưa
            'private_key_exists' => file_exists($this->privateKeyPath), // File khóa có tồn tại không
            'private_key_path' => $this->privateKeyPath,        // Đường dẫn đầy đủ của file khóa
            'project_path' => $this->projectPath,               // Đường dẫn lưu trữ file dự án
            'project_exists' => is_dir($this->projectPath),     // Thư mục dự án có tồn tại không
        ];
    }

    /**
     * Lưu khóa tải lên mã Mini Program
     * 
     * Lưu nội dung khóa tải về từ nền tảng WeChat Official Accounts lên máy chủ.
     * Khóa dùng để xác thực cho công cụ miniprogram-ci.
     * 
     * Quy trình xử lý:
     * 1. Xác minh định dạng khóa (phải bắt đầu bằng -----BEGIN RSA PRIVATE KEY-----)
     * 2. Đảm bảo thư mục lưu trữ khóa tồn tại
     * 3. Ghi nội dung khóa vào file
     * 4. Đặt quyền file là 0600 (chỉ chủ sở hữu được đọc/ghi)
     * 
     * @param string $keyContent Nội dung khóa (private key RSA định dạng PEM)
     * @return bool Lưu thành công thì trả về true
     * @throws AdminException Ném ngoại lệ khi định dạng khóa sai hoặc lưu thất bại
     */
    public function savePrivateKey(string $keyContent): bool
    {
        // Xác minh định dạng khóa: Phải là private key RSA định dạng PEM
        if (strpos($keyContent, '-----BEGIN RSA PRIVATE KEY-----') === false) {
            throw new AdminException('Định dạng khóa không hợp lệ, vui lòng tải lên đúng khóa tải lên mã nguồn Mini Program');
        }

        // Đảm bảo thư mục lưu trữ khóa tồn tại
        $keyDir = dirname($this->privateKeyPath);
        if (!is_dir($keyDir)) {
            mkdir($keyDir, 0755, true);
        }

        // Ghi nội dung khóa vào file
        $result = file_put_contents($this->privateKeyPath, $keyContent);
        if ($result === false) {
            throw new AdminException('Lưu khóa thất bại, vui lòng kiểm tra quyền thư mục');
        }

        // Đặt quyền file là 0600 (chỉ chủ sở hữu được đọc/ghi) để tăng cường bảo mật
        chmod($this->privateKeyPath, 0600);

        return true;
    }

    /**
     * Xóa khóa tải lên code Mini Program
     * 
     * Xóa file khóa đã lưu trên máy chủ.
     * Nếu file khóa không tồn tại thì trả về thành công ngay.
     * 
     * @return bool Trả về true khi xóa thành công hoặc file không tồn tại
     */
    public function deletePrivateKey(): bool
    {
        if (file_exists($this->privateKeyPath)) {
            return unlink($this->privateKeyPath);
        }
        return true;
    }

    /**
     * Chuẩn bị file dự án Mini Program
     * 
     * Công việc chuẩn bị dự án trước khi tải lên, bao gồm sao chép mã nguồn và thay thế cấu hình:
     * 1. Dọn dẹp file dự án cũ (nếu có)
     * 2. Sao chép mã nguồn Mini Program từ thư mục mp_view sang thư mục download
     * 3. Thay thế appid và projectname trong project.config.json
     * 4. Tùy theo có bật livestream hay không để quyết định có gỡ bỏ cấu hình plugin livestream không
     * 5. Thay domain API trong code thành domain của máy chủ hiện tại
     * 
     * @param bool $isLive Có bật chức năng livestream không, mặc định tắt
     *                     Khi tắt sẽ gỡ bỏ cấu hình plugin livestream trong app.json
     * @return string Đường dẫn dự án sau khi chuẩn bị xong
     * @throws AdminException AppId chưa được cấu hình hoặc quá trình chuẩn bị gặp lỗi thì ném ngoại lệ
     */
    public function prepareProject(bool $isLive = false): string
    {
        // Kiểm tra AppId đã được cấu hình chưa
        if (empty($this->appId)) {
            throw new AdminException('Vui lòng cấu hình AppId Mini Program trước');
        }

        try {
            // Bước 1: Dọn dẹp file dự án cũ
            if (is_dir($this->projectPath)) {
                $this->deleteDirectory($this->projectPath);
            }

            // Bước 2: Sao chép mã nguồn Mini Program vào thư mục đích
            // Thư mục nguồn: public/statics/mp_view (mã nguồn Mini Program sau khi biên dịch)
            /** @var FileService $fileService */
            $fileService = app(FileService::class);
            $fileService->copyDir(public_path() . 'statics/mp_view', $this->projectPath);

            // Bước 3: Thay thế appid và tên dự án trong project.config.json
            $this->updateConfigJson($this->appId, sys_config('routine_name', ''));

            // Bước 4: Nếu không bật livestream, gỡ bỏ cấu hình plugin livestream trong app.json
            if (!$isLive) {
                $this->updateAppJson();
            }

            // Bước 5: Thay domain API trong code thành domain của máy chủ hiện tại
            $this->updateUrl('https://' . $_SERVER['HTTP_HOST']);

            return $this->projectPath;
        } catch (\Throwable $e) {
            throw new AdminException('Chuẩn bị dự án thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Tải mã Mini Program lên WeChat dưới dạng bản phát triển
     * 
     * Gọi lệnh miniprogram-ci upload để tải code Mini Program lên máy chủ WeChat.
     * Sau khi tải lên thành công, có thể xem phiên bản mới trong mục quản lý phiên bản trên nền tảng WeChat Official Accounts.
     * 
     * Quy trình thực thi:
     * 1. Kiểm tra môi trường chạy (Node.js, file khóa, miniprogram-ci)
     * 2. Chuẩn bị file dự án
     * 3. Tạo và thực thi lệnh upload
     * 4. Ghi log output của lệnh
     * 5. Trả về kết quả tải lên
     * 
     * @param string $version Số phiên bản, định dạng x.x.x (ví dụ 1.0.0)
     * @param string $desc Mô tả phiên bản, mặc định là "Phiên bản {version}"
     * @param bool $isLive Có bật chức năng livestream không, ảnh hưởng đến việc chuẩn bị dự án
     * @return array Kết quả tải lên, gồm: success, version, desc, message, output
     * @throws AdminException Ném ngoại lệ khi kiểm tra môi trường thất bại hoặc tải lên thất bại
     */
    public function upload(string $version, string $desc = '', bool $isLive = false): array
    {
        // Kiểm tra môi trường chạy có đáp ứng yêu cầu không
        $this->checkEnvironment();

        // Chuẩn bị file dự án (sao chép, thay thế cấu hình)
        $projectPath = $this->prepareProject($isLive);

        // Tạo lệnh miniprogram-ci upload
        $command = $this->buildUploadCommand($version, $desc);

        // Ghi log lệnh
        Log::info('miniprogram-ci upload command: ' . $command);

        // Thực thi lệnh (command)
        $output = [];
        $returnCode = 0;
        exec($command . ' 2>&1', $output, $returnCode);

        $outputStr = implode("\n", $output);
        Log::info('miniprogram-ci upload output: ' . $outputStr);

        // Kiểm tra kết quả thực thi, mã trả về khác 0 nghĩa là thất bại
        if ($returnCode !== 0) {
            throw new AdminException('Tải lên thất bại: ' . $outputStr);
        }

        return [
            'success' => true,
            'version' => $version,
            'desc' => $desc,
            'message' => 'Tải lên thành công',
            'output' => $outputStr,
        ];
    }

    /**
     * Tạo mã QR xem trước Mini Program
     * 
     * Gọi lệnh miniprogram-ci preview để tạo mã QR xem trước.
     * Quét mã QR để xem trước Mini Program trên điện thoại.
     * 
     * Quy trình thực thi:
     * 1. Kiểm tra môi trường chạy
     * 2. Chuẩn bị file dự án
     * 3. Tạo và thực thi lệnh preview
     * 4. Tạo ảnh mã QR và lưu lại
     * 5. Trả về URL ảnh mã QR
     * 
     * @param string $pagePath Đường dẫn trang cần xem trước (ví dụ pages/index/index)
     *                         Để trống thì mặc định xem trước trang chủ Mini Program
     * @return array Kết quả xem trước, gồm: success, qrcode_url, message, output
     * @throws AdminException Ném ngoại lệ khi kiểm tra môi trường thất bại hoặc xem trước thất bại
     */
    public function preview(string $pagePath = ''): array
    {
        // Kiểm tra môi trường chạy có đáp ứng yêu cầu không
        $this->checkEnvironment();

        // Chuẩn bị file dự án
        $projectPath = $this->prepareProject();

        // Đặt đường dẫn lưu ảnh mã QR
        $qrcodePath = public_path() . 'statics' . DIRECTORY_SEPARATOR . 'routine_preview.jpg';

        // Tạo lệnh miniprogram-ci preview
        $command = $this->buildPreviewCommand($qrcodePath, $pagePath);

        // Ghi log lệnh
        Log::info('miniprogram-ci preview command: ' . $command);

        // Thực thi lệnh (command)
        $output = [];
        $returnCode = 0;
        exec($command . ' 2>&1', $output, $returnCode);

        $outputStr = implode("\n", $output);
        Log::info('miniprogram-ci preview output: ' . $outputStr);

        // Kiểm tra kết quả thực thi
        if ($returnCode !== 0) {
            throw new AdminException('Xem trước thất bại: ' . $outputStr);
        }

        // Ghép URL truy cập ảnh mã QR, thêm timestamp để tránh cache
        $qrcodeUrl = sys_config('site_url') . '/statics/routine_preview.jpg?t=' . time();

        return [
            'success' => true,
            'qrcode_url' => $qrcodeUrl,
            'message' => 'Tạo mã QR xem trước thành công',
            'output' => $outputStr,
        ];
    }

    /**
     * Tạo lệnh miniprogram-ci upload
     * 
     * Tạo chuỗi dòng lệnh dùng để tải lên code Mini Program.
     * 
     * Giải thích tham số lệnh:
     * - --pp: Đường dẫn dự án (project path)
     * - --pkp: Đường dẫn file khóa (private key path)
     * - --appid: AppId Mini Program
     * - --uv: Số phiên bản tải lên (upload version)
     * - -r: Số hiệu robot tải lên, mặc định 1
     * - --desc: Mô tả phiên bản
     * 
     * @param string $version Số phiên bản
     * @param string $desc Mô tả phiên bản, mặc định là "Phiên bản {version}"
     * @return string Chuỗi dòng lệnh hoàn chỉnh
     */
    protected function buildUploadCommand(string $version, string $desc = ''): string
    {
        // Mô tả phiên bản mặc định
        $desc = $desc ?: 'Phiên bản ' . $version;

        // Tạo lệnh miniprogram-ci upload
        $command = sprintf(
            'miniprogram-ci upload --pp "%s" --pkp "%s" --appid "%s" --uv "%s" -r 1 --desc "%s"',
            $this->projectPath,    // Đường dẫn project
            $this->privateKeyPath, // Đường dẫn tệp khóa
            $this->appId,          // AppId Mini Program
            $version,              // Số phiên bản
            addslashes($desc)      // Mô tả phiên bản (thoát ký tự đặc biệt)
        );

        return $command;
    }

    /**
     * Tạo lệnh miniprogram-ci preview
     * 
     * Tạo chuỗi dòng lệnh dùng để sinh mã QR xem trước.
     * 
     * Giải thích tham số lệnh:
     * - --pp: đường dẫn dự án
     * - --pkp: đường dẫn tệp khóa
     * - --appid: AppId Mini Program
     * - --qrcode-format: định dạng xuất mã QR (image)
     * - --qrcode-output-dest: đường dẫn xuất mã QR
     * - --compile-condition: điều kiện biên dịch, dùng để chỉ định trang xem trước
     * 
     * @param string $qrcodePath Đường dẫn lưu ảnh mã QR
     * @param string $pagePath Đường dẫn trang xem trước (tùy chọn)
     * @return string Chuỗi dòng lệnh hoàn chỉnh
     */
    protected function buildPreviewCommand(string $qrcodePath, string $pagePath = ''): string
    {
        // Tạo lệnh preview cơ bản
        $command = sprintf(
            'miniprogram-ci preview --pp "%s" --pkp "%s" --appid "%s" --qrcode-format image --qrcode-output-dest "%s"',
            $this->projectPath,    // Đường dẫn project
            $this->privateKeyPath, // Đường dẫn tệp khóa
            $this->appId,          // AppId Mini Program
            $qrcodePath            // Đường dẫn xuất mã QR
        );

        // Nếu đã chỉ định trang xem trước thì thêm tham số điều kiện biên dịch
        if ($pagePath) {
            $command .= sprintf(' --compile-condition \'{"pathName":"%s"}\'', addslashes($pagePath));
        }

        return $command;
    }

    /**
     * Kiểm tra môi trường chạy có đáp ứng yêu cầu không
     * 
     * Kiểm tra các điều kiện môi trường cần thiết trước khi tải lên hoặc xem trước:
     * 1. Đã cấu hình AppId của Mini Program
     * 2. Tệp khóa tải lên đã tồn tại
     * 3. Công cụ miniprogram-ci đã được cài đặt toàn cục
     * 
     * @throws AdminException Ném ngoại lệ khi có bất kỳ điều kiện nào không thỏa mãn
     */
    protected function checkEnvironment(): void
    {
        // Kiểm tra 1: AppId đã được cấu hình chưa
        if (empty($this->appId)) {
            throw new AdminException('Vui lòng cấu hình AppId Mini Program trước');
        }

        // Kiểm tra 2: tệp khóa có tồn tại không
        if (!file_exists($this->privateKeyPath)) {
            throw new AdminException('Vui lòng tải lên khóa tải lên mã nguồn Mini Program trước');
        }

        // Kiểm tra 3: miniprogram-ci đã được cài đặt toàn cục chưa
        $output = [];
        exec('which miniprogram-ci 2>&1', $output, $returnCode);
        if ($returnCode !== 0) {
            throw new AdminException('miniprogram-ci chưa được cài đặt, vui lòng cài đặt môi trường chạy trước');
        }
    }

    /**
     * Thay thế tên miền API trong mã dự án
     * 
     * Thay tên miền API mặc định trong mã Mini Program (https://demo.crmeb.com)
     * bằng tên miền của máy chủ hiện tại, đảm bảo Mini Program gọi đúng API backend.
     * 
     * @param string $url Tên miền mới cần thay vào (ví dụ https://your-domain.com)
     */
    protected function updateUrl(string $url): void
    {
        // Tạo đường dẫn tệp vendor.js (chứa cấu hình tên miền API)
        $fileUrl = $this->projectPath . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'vendor.js';
        if (!file_exists($fileUrl)) {
            return;
        }

        // Đọc nội dung tệp
        $string = file_get_contents($fileUrl);
        // Thay tên miền mặc định bằng tên miền của máy chủ hiện tại
        $string = str_replace('https://demo.crmeb.com', $url, $string);
        // Ghi lại vào tệp
        file_put_contents($fileUrl, $string);
    }

    /**
     * Cập nhật cấu hình app.json - gỡ plugin livestream
     * 
     * Khi không cần tính năng livestream, gỡ cấu hình plugin live-player-plugin trong app.json.
     * Nhờ đó tránh nạp các phụ thuộc không cần thiết khi không dùng livestream.
     */
    protected function updateAppJson(): void
    {
        // Đường dẫn tệp app.json
        $fileUrl = $this->projectPath . DIRECTORY_SEPARATOR . 'app.json';
        if (!file_exists($fileUrl)) {
            return;
        }

        $string = file_get_contents($fileUrl);
        // Dùng biểu thức chính quy để gỡ cấu hình plugin live-player-plugin
        // Định dạng khớp: , "plugins": { "live-player-plugin": { ... } }
        $pattern = '/,\s*"plugins"\s*:\s*\{\s*"live-player-plugin"\s*:\s*\{[^}]*\}\s*\}/s';
        $string = preg_replace($pattern, '', $string);
        file_put_contents($fileUrl, $string);
    }

    /**
     * Cập nhật cấu hình project.config.json
     * 
     * Thay thế appid và projectname trong tệp cấu hình dự án,
     * đảm bảo Mini Program được tải lên dùng đúng định danh.
     * 
     * @param string $appId AppId Mini Program
     * @param string $projectName Tên dự án (tùy chọn)
     */
    protected function updateConfigJson(string $appId, string $projectName = ''): void
    {
        // Đường dẫn tệp project.config.json
        $fileUrl = $this->projectPath . DIRECTORY_SEPARATOR . 'project.config.json';
        if (!file_exists($fileUrl)) {
            return;
        }

        $string = file_get_contents($fileUrl);

        // Thay thế appid
        $appIdPattern = '/"appid"\s*:\s*"[^"]*"/';
        $string = preg_replace($appIdPattern, '"appid": "' . $appId . '"', $string);

        // Thay thế tên dự án (nếu có cung cấp)
        if ($projectName) {
            $namePattern = '/"projectname"\s*:\s*"[^"]*"/';
            $string = preg_replace($namePattern, '"projectname": "' . $projectName . '"', $string);
        }

        file_put_contents($fileUrl, $string);
    }

    /**
     * Xóa đệ quy thư mục và toàn bộ nội dung bên trong
     * 
     * Dùng để dọn dẹp các tệp dự án cũ trước khi chuẩn bị dự án mới.
     * Sẽ xóa đệ quy toàn bộ tệp và thư mục con trong thư mục được chỉ định.
     * 
     * @param string $dir Đường dẫn thư mục cần xóa
     * @return bool Trả về true khi xóa thành công
     */
    protected function deleteDirectory(string $dir): bool
    {
        // Thư mục không tồn tại thì trả về thành công luôn
        if (!is_dir($dir)) {
            return true;
        }

        // Duyệt toàn bộ tệp và thư mục con trong thư mục (bỏ qua . và ..)
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            // Xóa đệ quy thư mục con, xóa trực tiếp tệp
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        // Xóa thư mục rỗng
        return rmdir($dir);
    }

    /**
     * Lấy lịch sử tải lên (chưa triển khai)
     * 
     * Phương thức này dùng để trả về lịch sử tải lên mã Mini Program,
     * bao gồm các thông tin như số phiên bản, thời gian tải lên, người tải lên.
     * 
     * @return array Mảng lịch sử tải lên
     */
    public function getUploadHistory(): array
    {
        // TODO: triển khai chức năng lịch sử tải lên
        // Có thể lưu bản ghi tải lên vào cơ sở dữ liệu, bao gồm:
        // - Số phiên bản, mô tả phiên bản
        // - Thời gian tải lên, người tải lên
        // - Kết quả tải lên (thành công/thất bại)
        // - Log đầu ra của lệnh
        return [];
    }
}
