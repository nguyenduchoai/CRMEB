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
namespace app\adminapi\controller\v1\diy;

use app\adminapi\controller\AuthController;
use app\jobs\ThemeExportJob;
use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\article\ArticleServices;
use app\services\diy\ThemeDownloadServices;
use app\services\diy\ThemeServices;
use app\services\product\product\StoreProductServices;
use SplFileInfo;
use think\facade\App;

/**
 * Controller quản lý chủ đề
 * Xử lý các chức năng như danh sách, chi tiết, lưu, nhập/xuất chủ đề
 * @author wuhaotian
 * @email 442384644@qq.com
 * @date 2025/12/18
 */
class Theme extends AuthController
{

    /**
     * @var ThemeServices Lớp dịch vụ chủ đề
     */
    protected $services;

    /**
     * Phương thức khởi tạo
     * Inject service ThemeServices
     * @param App $app Instance container của ứng dụng
     * @param ThemeServices $services Instance service chủ đề
     */
    public function __construct(App $app, ThemeServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy danh sách chủ đề
     * Hỗ trợ lọc theo tiêu đề, loại, trạng thái
     * @return \think\Response Phản hồi dạng JSON
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/18
     */
    public function getThemeList()
    {
        // Lấy tham số yêu cầu, thiết lập giá trị mặc định
        $where = $this->request->getMore([
            ['title', ''],
            ['type', ''],
            ['page_type', ''],
            ['is_del', 0],
        ]);
        // Gọi tầng service để lấy dữ liệu danh sách
        $data = $this->services->getThemeList($where);
        return app('json')->success($data);
    }

    /**
     * Lấy chi tiết chủ đề
     * @param int $id ID chủ đề
     * @param string $type Loại truy vấn (tùy chọn)
     * @return \think\Response Phản hồi dạng JSON
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/18
     */
    public function getThemeInfo($id, $type = '')
    {
        $data = $this->services->getThemeInfo($id, $type);
        return app('json')->success($data);
    }

    /**
     * Lưu thông tin cơ bản của chủ đề
     * @param int $id ID chủ đề
     * @return \think\Response Phản hồi dạng JSON
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/18
     */
    public function saveTheme($id)
    {
        $data = $this->request->getMore([
            ['tid', 0],
            ['title', ''],
            ['type', ''],
            ['value', ''],
            ['page_type', 'theme'],
        ]);
        $id = $this->services->saveTheme($id, $data);
        return app('json')->success('Lưu thành công', ['id' => $id]);
    }

    /**
     * Lưu thông tin tiêu đề của chủ đề
     * @param int $id ID chủ đề
     * @return \think\Response Phản hồi dạng JSON
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/18
     */
    public function saveThemeTitle($id)
    {
        $data = $this->request->getMore([
            ['tid', 0],
            ['title', ''],
            ['info', ''],
            ['page_type', 'theme'],
        ]);
        $id = $this->services->saveThemeTitle($id, $data);
        return app('json')->success('Lưu thành công', ['id' => $id]);
    }

    /**
     * Lưu thông tin hình ảnh của chủ đề
     * @param int $id ID chủ đề
     * @return \think\Response Phản hồi dạng JSON
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/18
     */
    public function saveThemeImage($id)
    {
        $data = $this->request->getMore([
            ['image', ''],
            ['type', ''],
        ]);
        $id = $this->services->saveThemeImage($id, $data);
        return app('json')->success('Lưu thành công', ['id' => $id]);
    }

    /**
     * Lấy thành phần tùy chỉnh - danh sách bài viết
     * Dùng làm nguồn dữ liệu khi chọn thành phần bài viết trên trang DIY
     * @return \think\Response Phản hồi dạng JSON
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/1/12
     */
    public function getThemeArticleList()
    {
        $where = $this->request->getMore([
            ['ids', ''],
            ['cid', ''],
            ['order', 0],
            ['sort', 0],
            ['limit', 10],
        ]);
        $data = app()->make(ArticleServices::class)->getThemeArticle($where);
        return app('json')->success($data);
    }

    /**
     * Lấy thành phần tùy chỉnh - danh sách phiếu giảm giá
     * Dùng làm nguồn dữ liệu khi chọn thành phần phiếu giảm giá trên trang DIY
     * @return \think\Response Phản hồi dạng JSON
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/1/13
     */
    public function getThemeCouponList()
    {
        $where = $this->request->getMore([
            ['ids', ''],
            ['type', ''],
            ['user_type', ''],
            ['send_type', ''],
            ['is_min_price', 0],
            ['min_price', 0],
            ['start_time', ''],
            ['end_time', ''],
            ['order', 0],
            ['sort', 0],
            ['limit', 10],
        ]);
        $data = app()->make(StoreCouponIssueServices::class)->getThemeCoupon($where);
        return app('json')->success($data);
    }

    /**
     * Lấy thành phần tùy chỉnh - danh sách sản phẩm
     * Dùng làm nguồn dữ liệu khi chọn thành phần sản phẩm trên trang DIY
     * @return \think\Response Phản hồi dạng JSON
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/1/13
     */
    public function getThemeProductList()
    {
        $where = $this->request->getMore([
            ['ids', ''],
            ['cate_ids', ''],
            ['order', 0],
            ['sort', 0],
            ['limit', 10],
        ]);
        $data = app()->make(StoreProductServices::class)->getThemeProduct($where);
        return app('json')->success($data);
    }

    /**
     * Xuất dữ liệu chủ đề
     * 1. Ghi một bản ghi chờ xử lý vào eb_theme_download (chưa có download_url)
     * 2. Đẩy tác vụ đóng gói thực tế vào hàng đợi để thực thi bất đồng bộ
     * 3. Sau khi hàng đợi hoàn tất sẽ điền bổ sung download_url
     *
     * @param int $id ID chủ đề
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/3/10
     */
    public function exportTheme($id)
    {
        // Kiểm tra có dùng cache Redis và đã bật hàng đợi tin nhắn chưa
        $queueEnabled = sys_config('queue_open', 0) == 1 && \think\facade\Env::get('cache.driver', 'file') == 'redis';
        if (!$queueEnabled) {
            return app('json')->fail('Tính năng xuất cần bật bộ nhớ đệm Redis và hàng đợi tin nhắn, vui lòng vào cài đặt hệ thống để bật cấu hình tương ứng trước');
        }

        $id = (int)$id;

        // 1. Truy vấn thông tin cơ bản của chủ đề, lấy tiêu đề
        $info = $this->services->getThemeInfo($id);

        // 2. Tạo thư mục đóng gói chủ đề
        $dir = public_path() . 'theme/download/' . $id . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        // 3. Dọn dẹp thư mục đóng gói, tránh lẫn dữ liệu
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                @rmdir($fileInfo->getRealPath());
            } else {
                @unlink($fileInfo->getRealPath());
            }
        }

        // 4. Tạo thư mục hình ảnh của chủ đề
        $imagesDir = $dir . 'images/';
        if (!is_dir($imagesDir)) mkdir($imagesDir, 0755, true);

        // 5. Ghi bản ghi chờ xử lý vào eb_theme_download (tạm thời chưa điền download_url)
        /** @var ThemeDownloadServices $themeDownloadServices */
        $themeDownloadServices = app()->make(ThemeDownloadServices::class);
        $recordId = $themeDownloadServices->addDownloadRecord($id, $info['title'], '');

        // 6. Đẩy tác vụ đóng gói vào hàng đợi
        ThemeExportJob::dispatch('export', [$info, $recordId]);

        return app('json')->success('Đang xuất dữ liệu, vui lòng không thao tác trên trang!', ['record_id' => $recordId]);
    }

    /**
     * Tra cứu lịch sử xuất chủ đề
     * Frontend gọi API này định kỳ (polling), khi download_url không còn rỗng nghĩa là hàng đợi đã hoàn tất
     *
     * @param int $record_id ID bản ghi tải xuống
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/3/10
     */
    public function getExportRecord($record_id)
    {
        /** @var ThemeDownloadServices $themeDownloadServices */
        $themeDownloadServices = app()->make(ThemeDownloadServices::class);
        $record = $themeDownloadServices->getDownloadInfo((int)$record_id);
        return app('json')->success([
            'download_url' => $record['download_url'] ?? '',
        ]);
    }

    /**
     * Nhập chủ đề
     * Tải lên gói Zip, giải nén và khôi phục cấu hình chủ đề
     * 1. Giải nén gói Zip vào theme/import/
     * 2. Đọc config.json
     * 3. Trích xuất hình ảnh trong gói và di chuyển vào thư mục uploads/theme/
     * 4. Duyệt đệ quy cấu hình, sửa đường dẫn hình ảnh và thay thế tên miền
     *
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/1/15
     */
    public function importTheme()
    {
        // 1 Lấy file
        [$importUrl] = $this->request->postMore([
            ['url', ''],
        ], true);
        $realPath = public_path() . $importUrl;
        if (!file_exists($realPath)) return app('json')->fail('Tệp không tồn tại');

        // 2. Giải nén file vào thư mục theme/import/
        $dir = 'theme/import/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        // Dọn dẹp thư mục nhập, tránh lẫn dữ liệu
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        /** @var SplFileInfo $fileInfo */
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                @rmdir($fileInfo->getRealPath());
            } else {
                @unlink($fileInfo->getRealPath());
            }
        }
        $zip = new \ZipArchive();
        $zip->open($realPath);
        $zip->extractTo($dir);
        $zip->close();

        // 3. Đọc file config.json sau khi giải nén
        $configPath = $dir . 'config.json';
        if (!file_exists($configPath)) return app('json')->fail('Tệp không tồn tại');
        $config = json_decode(file_get_contents($configPath), true);
        if (!is_array($config)) return app('json')->fail('Nội dung tệp không hợp lệ');

        // 4. Xử lý di chuyển tài nguyên hình ảnh
        // Di chuyển tất cả hình ảnh trong gói nén vào thư mục uploads/theme/{timestamp}/
        $timestamp = date('YmdHis');
        $themeDir = 'uploads/theme/' . $timestamp . '/';
        if (!is_dir($themeDir)) mkdir($themeDir, 0755, true);

        $rootPath = realpath($dir);
        $imageMap = []; // Ghi lại ánh xạ từ đường dẫn tương đối sang đường dẫn tải lên mới

        // Quét đệ quy tất cả hình ảnh trong thư mục giải nén
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir()) continue;
            $filePath = $fileInfo->getRealPath();
            // Bỏ qua file cấu hình
            if (basename($filePath) === 'config.json') continue;
            // Chỉ xử lý hình ảnh có phần mở rộng được chỉ định
            if (!preg_match('/\.(png|jpe?g|gif|webp|svg)$/i', $filePath)) continue;

            // Lấy đường dẫn tương đối của file so với thư mục gốc giải nén
            $relative = ltrim(str_replace($rootPath, '', $filePath), DIRECTORY_SEPARATOR);
            $basename = basename($filePath);

            // Đường dẫn đích
            $target = $themeDir . $basename;
            // Di chuyển/sao chép file
            if (@copy($filePath, $target)) {
                $imageMap[$relative] = $target; // Ghi ánh xạ: đường dẫn tương đối trong gói => đường dẫn mới trong hệ thống
            }
        }

        // 5. Chuẩn bị URL gốc để thay thế tên miền
        $siteUrl = rtrim(sys_config('site_url'), '/');
        $siteParts = parse_url($siteUrl);
        $base = '';
        if ($siteParts && isset($siteParts['host'])) {
            $scheme = $siteParts['scheme'] ?? 'http';
            $base = $scheme . '://' . $siteParts['host'];
            if (isset($siteParts['port'])) {
                $base .= ':' . $siteParts['port'];
            }
        }

        $rewriteArray = function (&$data) use (&$rewriteArray, $imageMap, $base) {
            if (!is_array($data)) return;

            foreach ($data as $k => &$v) {
                if (is_array($v)) {
                    $rewriteArray($v);
                } elseif (is_string($v)) {
                    $decoded = json_decode($v, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $rewriteArray($decoded);
                        $v = json_encode($decoded, JSON_UNESCAPED_UNICODE);
                        continue;
                    }

                    // Kiểm tra có trong bảng ánh xạ hình ảnh không (xử lý hình ảnh nhập cục bộ)
                    // Nếu có trong bảng ánh xạ, nghĩa là hình ảnh đã được giải nén từ gói nén và tải lên thư mục uploads/theme/
                    // Lúc này cần thay đường dẫn của nó bằng URL đầy đủ kèm tên miền của trang hiện tại
                    if (isset($imageMap[$v])) {
                        if ($base !== '') {
                            // Ghép tên miền + đường dẫn mới
                            $v = rtrim($base, '/') . '/' . ltrim($imageMap[$v], '/');
                        } else {
                            // Nếu không lấy được tên miền thì chỉ dùng đường dẫn tương đối
                            $v = $imageMap[$v];
                        }
                        continue;
                    }

                    // Xử lý tương thích tiền tố theme/download/ được thêm vào khi xuất bằng exportTheme
                    // Khi xuất, các trường như home_image được gán giá trị theme/download/xxx.png
                    // Nhưng file trong gói nén thực chất là xxx.png, khiến việc khớp trực tiếp với imageMap thất bại
                    // Vì vậy cần bỏ tiền tố theme/download/ rồi thử khớp lại
                    if (strpos($v, 'theme/download/') === 0) {
                        $rel = substr($v, strlen('theme/download/'));
                        if (isset($imageMap[$rel])) {
                            if ($base !== '') {
                                // Tương tự, ghép tên miền + đường dẫn mới
                                $v = rtrim($base, '/') . '/' . ltrim($imageMap[$rel], '/');
                            } else {
                                $v = $imageMap[$rel];
                            }
                            continue;
                        }
                    }

                    // Xử lý thay thế tên miền (thay liên kết của tên miền cũ bằng tên miền của trang hiện tại)
                    // Tránh trường hợp cấu hình chủ đề được nhập chứa tên miền của trang cũ, khiến hình ảnh không tải được

                    $parts = parse_url($v);
                    if (!$parts || !isset($parts['host']) || $base === '') {
                        continue;
                    }
                    $path = $parts['path'] ?? '';
                    $query = isset($parts['query']) ? '?' . $parts['query'] : '';
                    $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
                    $v = rtrim($base, '/') . $path . $query . $fragment;
                }
            }
            unset($v);
        };

        // Thực thi logic thay thế
        $rewriteArray($config);

        // Thực hiện ghi dữ liệu
        $themeId = $this->services->importThemeData($config);

        // Trả về thành công
        return app('json')->success('Nhập thành công', ['theme_id' => $themeId]);
    }

    /**
     * @description: Áp dụng chủ đề
     * @param int $id ID chủ đề
     * @return array
     */
    public function useTheme(int $id)
    {
        $this->services->useTheme($id);
        return app('json')->success('Áp dụng thành công');
    }

    /**
     * @description: Áp dụng dữ liệu chủ đề
     * @param array $data Dữ liệu chủ đề
     * @return array
     */
    public function useThemeData($id)
    {
        [$theme_id, $type] = $this->request->getMore([
            ['theme_id', 0],
            ['type', ''],
        ], true);
        $this->services->useThemeData($id, $theme_id, $type);
        return app('json')->success('Áp dụng thành công');
    }

    /**
     * @description: Lấy chủ đề đang sử dụng
     * @return array
     */
    public function getUsingTheme()
    {
        $theme = $this->services->getUsingTheme();
        return app('json')->success($theme);
    }

    /**
     * @description: Khôi phục chủ đề
     * @param int $id ID chủ đề
     * @return array
     */
    public function restoreTheme(int $id)
    {
        $this->services->restoreTheme($id);
        return app('json')->success('Khôi phục thành công');
    }

    /**
     * @description: Xóa chủ đề
     * @param int $id ID chủ đề
     * @return array
     */
    public function deleteTheme(int $id)
    {
        $this->services->deleteTheme($id);
        return app('json')->success('Xóa thành công');
    }

    /**
     * Lấy danh sách dữ liệu trang tùy chỉnh
     * @return \think\Response Phản hồi dạng JSON
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/1/20
     */
    public function getMicroPageList()
    {
        $data = $this->services->getMicroPageList();
        return app('json')->success($data);
    }
}
