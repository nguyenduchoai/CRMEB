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

namespace app\jobs;

use app\services\diy\ThemeDownloadServices;
use app\services\diy\ThemeServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

/**
 * Tác vụ hàng đợi xuất chủ đề
 * Class ThemeExportJob
 * @package app\jobs
 */
class ThemeExportJob extends BaseJobs
{
    use QueueTrait;

    /**
     * Thực thi tác vụ xuất chủ đề
     * 1. Đóng gói file chủ đề thành zip
     * 2. Cập nhật download_url vào bản ghi eb_theme_download
     *
     * @param $info Thông tin chủ đề
     * @param int $recordId eb_theme_download ID bản ghi
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/3/10
     */
    public function export($info, int $recordId): bool
    {
        try {
            /** @var ThemeServices $themeServices */
            $themeServices = app()->make(ThemeServices::class);
            /** @var ThemeDownloadServices $themeDownloadServices */
            $themeDownloadServices = app()->make(ThemeDownloadServices::class);

            $downloadUrl = $themeServices->exportThemePackage($info);

            // Cập nhật địa chỉ tải xuống vừa tạo vào bản ghi tải xuống
            $themeDownloadServices->updateDownloadUrl($recordId, $downloadUrl);
        } catch (\Throwable $e) {
            Log::error('Hàng đợi xuất chủ đề thất bại, nguyên nhân:' . $e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine());
            return false;
        }
        return true;
    }
}
