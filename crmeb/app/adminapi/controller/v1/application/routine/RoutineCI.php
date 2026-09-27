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
namespace app\adminapi\controller\v1\application\routine;

use app\adminapi\controller\AuthController;
use app\services\system\NodeEnvironmentServices;
use app\services\wechat\RoutineCIServices;
use think\facade\App;

/**
 * Controller tự động tải lên Mini Program qua CI
 * 
 * Tổng quan chức năng:
 * Controller này cung cấp các API để tự động tải lên mã nguồn WeChat Mini Program, được xây dựng dựa trên công cụ miniprogram-ci chính thức của WeChat.
 * Thông qua các API này, quản trị viên có thể tải mã Mini Program lên máy chủ WeChat ngay trong trang quản trị mà không cần dùng công cụ WeChat DevTools.
 * 
 * Chức năng chính:
 * 1. Kiểm tra môi trường - kiểm tra máy chủ đã cài Node.js và miniprogram-ci chưa
 * 2. Hướng dẫn cài đặt - cung cấp hướng dẫn cài đặt môi trường cho các hệ điều hành khác nhau
 * 3. Quản lý cấu hình - quản lý khóa tải lên Mini Program và cấu hình AppId
 * 4. Tải mã lên - tải mã Mini Program lên WeChat dưới dạng bản phát triển
 * 5. Chức năng xem trước - tạo mã QR xem trước Mini Program để kiểm thử
 * 
 * Điều kiện sử dụng:
 * - Máy chủ đã cài Node.js (>=14.0.0) và npm
 * - Đã cài miniprogram-ci ở chế độ toàn cục (npm install miniprogram-ci -g)
 * - Đã lấy khóa tải lên mã Mini Program trên nền tảng WeChat Official Accounts
 * - Hàm exec() của PHP trên máy chủ chưa bị vô hiệu hóa
 * 
 * @see https://developers.weixin.qq.com/miniprogram/dev/devtools/ci.html Tài liệu CI chính thức của WeChat
 * @package app\adminapi\controller\v1\application\routine
 */
class RoutineCI extends AuthController
{
    /**
     * Instance service kiểm tra môi trường Node.js
     * 
     * Dùng để kiểm tra môi trường máy chủ có đáp ứng yêu cầu tải lên Mini Program không:
     * - Kiểm tra phiên bản Node.js
     * - Kiểm tra npm có khả dụng không
     * - Kiểm tra trạng thái cài đặt miniprogram-ci
     * - Nhận diện loại hệ điều hành
     * 
     * @var NodeEnvironmentServices
     */
    protected $envServices;

    /**
     * Instance service CI cốt lõi của Mini Program
     * 
     * Xử lý logic nghiệp vụ cốt lõi của việc tải mã Mini Program lên:
     * - Quản lý khóa tải lên
     * - Chuẩn bị file dự án
     * - Thực thi lệnh miniprogram-ci
     * - Tạo mã QR xem trước
     * 
     * @var RoutineCIServices
     */
    protected $ciServices;

    /**
     * Phương thức khởi tạo - khởi tạo các service phụ thuộc
     * 
     * Inject các instance lớp service cần thiết bằng cơ chế dependency injection,
     * container của ThinkPHP sẽ tự động phân giải và inject các phụ thuộc này.
     * 
     * @param App $app ThinkPHP Instance ứng dụng
     * @param NodeEnvironmentServices $envServices Service kiểm tra môi trường
     * @param RoutineCIServices $ciServices CI Service tải lên
     */
    public function __construct(App $app, NodeEnvironmentServices $envServices, RoutineCIServices $ciServices)
    {
        parent::__construct($app);
        $this->envServices = $envServices;
        $this->ciServices = $ciServices;
    }

    /**
     * Lấy trạng thái môi trường chạy của máy chủ
     * 
     * Kiểm tra và trả về toàn bộ thông tin môi trường cần cho việc tải lên Mini Program, frontend căn cứ vào kết quả trả về để
     * hiển thị trạng thái sẵn sàng của môi trường hoặc hướng dẫn người dùng hoàn tất cấu hình môi trường.
     * 
     * Cấu trúc dữ liệu trả về:
     * - os: thông tin hệ điều hành (family, type, version)
     * - node: trạng thái Node.js (installed, version, path, meets_requirement)
     * - npm: trạng thái npm (installed, version)
     * - miniprogram_ci: trạng thái công cụ CI (installed, version)
     * - ready: giá trị boolean, môi trường đã hoàn toàn sẵn sàng chưa
     * - can_install: có hỗ trợ cài đặt tự động không
     * - exec_enabled: hàm exec có dùng được không
     * - message: thông báo
     * 
     * @return mixed JSON response, gồm đầy đủ thông tin trạng thái môi trường
     */
    public function environment()
    {
        $data = $this->envServices->getEnvironmentStatus();
        return app('json')->success($data);
    }

    /**
     * Lấy hướng dẫn cài đặt môi trường
     * 
     * Trả về các bước cài đặt Node.js và miniprogram-ci tương ứng theo loại hệ điều hành của máy chủ.
     * Hệ điều hành được hỗ trợ: CentOS/RHEL, Ubuntu/Debian, macOS, Windows
     * 
     * Cấu trúc dữ liệu trả về:
     * - title: tiêu đề hướng dẫn (ví dụ “Hướng dẫn cài đặt CentOS/RHEL”)
     * - steps: mảng các bước cài đặt, kèm câu lệnh dòng lệnh
     * - script_url: địa chỉ URL của script cài đặt một chạm
     * 
     * @return mixed JSON response, gồm hướng dẫn cài đặt phù hợp với hệ thống hiện tại
     */
    public function installGuide()
    {
        $guide = $this->envServices->getInstallGuide();
        return app('json')->success($guide);
    }

    /**
     * Lấy trạng thái cấu hình tải lên Mini Program
     * 
     * Trả về thông tin cấu hình tải lên hiện tại, để frontend hiển thị trạng thái cấu hình và hướng dẫn quy trình cấu hình.
     * 
     * Cấu trúc dữ liệu trả về:
     * - app_id: AppId của Mini Program
     * - app_id_configured: AppId đã được cấu hình chưa
     * - private_key_exists: file khóa tải lên có tồn tại không
     * - private_key_path: đường dẫn lưu file khóa
     * - project_path: đường dẫn dự án Mini Program
     * - project_exists: thư mục dự án có tồn tại không
     * 
     * @return mixed JSON response, gồm thông tin trạng thái cấu hình tải lên
     */
    public function uploadConfig()
    {
        $config = $this->ciServices->getUploadConfig();
        return app('json')->success($config);
    }

    /**
     * Lưu khóa tải lên mã Mini Program
     * 
     * Nhận và lưu khóa tải lên mã Mini Program đã tải xuống từ nền tảng WeChat Official Accounts.
     * Khóa được dùng để xác thực danh tính cho công cụ miniprogram-ci, bảo đảm chỉ người dùng được ủy quyền mới có thể tải mã lên.
     * 
     * Tham số yêu cầu:
     * - key_content: string, bắt buộc, nội dung khóa riêng RSA (bắt đầu bằng -----BEGIN RSA PRIVATE KEY-----)
     * 
     * Cách lấy khóa:
     * Nền tảng WeChat Official Accounts -> Quản lý phát triển -> Cài đặt phát triển -> Tải lên mã Mini Program -> Tải khóa xuống
     * 
     * Lưu ý bảo mật:
     * - File khóa được lưu tại config/routine_private.key
     * - Quyền file được đặt là 0600, chỉ chủ sở hữu mới có quyền đọc/ghi
     * - Không commit file khóa vào hệ thống quản lý phiên bản
     * 
     * @return mixed JSON response, thành công thì trả về thông báo, thất bại thì trả về lý do lỗi
     */
    public function savePrivateKey()
    {
        // Lấy nội dung khóa trong yêu cầu POST
        $keyContent = $this->request->post('key_content', '');

        // Kiểm tra nội dung khóa không được để trống
        if (empty($keyContent)) {
            return app('json')->fail('Vui lòng cung cấp nội dung khóa');
        }

        // Gọi tầng service để lưu khóa (tầng service sẽ kiểm tra định dạng khóa)
        $this->ciServices->savePrivateKey($keyContent);
        return app('json')->success('Lưu khóa thành công');
    }

    /**
     * Tải mã Mini Program lên WeChat dưới dạng bản phát triển
     * 
     * Tải mã dự án Mini Program ở máy cục bộ lên máy chủ WeChat dưới dạng phiên bản phát triển.
     * Sau khi tải lên thành công, có thể thấy phiên bản phát triển vừa tải lên trong mục Quản lý phiên bản của nền tảng WeChat Official Accounts.
     * 
     * Tham số yêu cầu:
     * - version: string, bắt buộc, số phiên bản, định dạng x.x.x (ví dụ 1.0.0)
     * - desc: string, tùy chọn, mô tả phiên bản, mặc định là “Phiên bản {version}”
     * - is_live: int, tùy chọn, có bật chức năng livestream không, 0=tắt 1=bật, mặc định tắt
     * 
     * Quy trình thực thi:
     * 1. Kiểm tra định dạng số phiên bản
     * 2. Kiểm tra môi trường chạy (Node.js, file khóa, v.v.)
     * 3. Chuẩn bị file dự án (sao chép, thay thế cấu hình)
     * 4. Thực thi lệnh miniprogram-ci upload
     * 5. Trả về kết quả tải lên
     * 
     * Cấu trúc dữ liệu trả về:
     * - success: thành công hay không
     * - version: số phiên bản
     * - desc: mô tả phiên bản
     * - message: thông báo
     * - output: đầu ra khi thực thi lệnh
     * 
     * @return mixed JSON response, gồm thông tin kết quả tải lên
     */
    public function upload()
    {
        // Lấy hàng loạt tham số yêu cầu
        [$version, $desc, $isLive] = $this->request->postMore([
            ['version', ''],     // Số phiên bản
            ['desc', ''],        // Mô tả phiên bản
            ['is_live', 0],      // Có mở livestream không
        ], true);

        // Kiểm tra bắt buộc nhập số phiên bản
        if (empty($version)) {
            return app('json')->fail('Vui lòng nhập số phiên bản');
        }

        // Kiểm tra định dạng số phiên bản: phải theo định dạng x.x.x (ví dụ 1.0.0, 2.1.3)
        if (!preg_match('/^\d+\.\d+\.\d+$/', $version)) {
            return app('json')->fail('Số phiên bản sai định dạng, vui lòng dùng định dạng x.x.x');
        }

        // Gọi tầng service để thực hiện tải lên
        $result = $this->ciServices->upload($version, $desc, (bool)$isLive);
        return app('json')->success($result);
    }

    /**
     * Lấy mã QR xem trước Mini Program
     * 
     * Tạo mã QR xem trước Mini Program, sau khi quét mã có thể xem trước Mini Program trên điện thoại.
     * Bản xem trước không ảnh hưởng đến bản đang chạy chính thức, phù hợp để dùng khi phát triển và kiểm thử.
     * 
     * Tham số yêu cầu:
     * - page_path: string, tùy chọn, đường dẫn trang cần xem trước (ví dụ pages/index/index)
     *              Để trống thì mặc định xem trước trang chủ
     * 
     * Quy trình thực thi:
     * 1. Kiểm tra môi trường chạy
     * 2. Chuẩn bị file dự án
     * 3. Thực thi lệnh miniprogram-ci preview
     * 4. Tạo ảnh mã QR
     * 5. Trả về URL ảnh mã QR
     * 
     * Cấu trúc dữ liệu trả về:
     * - success: thành công hay không
     * - qrcode_url: URL truy cập ảnh mã QR
     * - message: thông báo
     * - output: đầu ra khi thực thi lệnh
     * 
     * Lưu ý:
     * - Mã QR xem trước có thời hạn hiệu lực ngắn, hết hạn cần tạo lại
     * - Chỉ nhà phát triển và người trải nghiệm (tester) của Mini Program mới có thể quét mã để xem trước
     * 
     * @return mixed JSON response, gồm thông tin mã QR xem trước
     */
    public function preview()
    {
        // Lấy tham số đường dẫn trang xem trước
        $pagePath = $this->request->post('page_path', '');
        
        // Gọi tầng service để tạo mã QR xem trước
        $result = $this->ciServices->preview($pagePath);
        return app('json')->success($result);
    }
}
