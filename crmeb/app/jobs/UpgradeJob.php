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

use app\services\system\log\SystemFileMd5Services;
use app\services\system\UpgradeServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

/**
 * Gói nâng cấp
 * Class UpgradeJob
 * @package app\jobs
 */
class UpgradeJob extends BaseJobs
{
    use QueueTrait;

    /**
     * Tải xuống
     * @param $seq
     * @param $url
     * @param $filePath
     * @param $filename
     * @param $timeout
     * @return bool
     */
    public function download($seq, $url, $filePath, $filename, $timeout): bool
    {
        try {
            /** @var UpgradeServices $services */
            $services = app()->make(UpgradeServices::class);
            $services->download($seq, $url, $filePath, $filename, $timeout);
        } catch (\Exception $e) {
            Log::error('Tải xuống gói nâng cấp thất bại, nguyên nhân:' . $e->getMessage());
        }
        return true;
    }

    /**
     * Sao lưu cơ sở dữ liệu
     * @param $token
     * @return bool
     */
    public function databaseBackup($token): bool
    {
        try {
            /** @var UpgradeServices $services */
            $services = app()->make(UpgradeServices::class);
            $services->databaseBackup($token);
        } catch (\Exception $e) {
            Log::error('Sao lưu cơ sở dữ liệu thất bại, nguyên nhân:' . $e->getMessage());
        }
        return true;
    }

    /**
     * Sao lưu dự án
     * @param $token
     * @return bool
     */
    public function projectBackup($token): bool
    {
        try {
            /** @var UpgradeServices $services */
            $services = app()->make(UpgradeServices::class);
            $services->projectBackup($token);
        } catch (\Exception $e) {
            Log::error('Sao lưu dự án thất bại, nguyên nhân:' . $e->getMessage());
        }
        return true;
    }

    /**
     * Ghi đè file dự án
     * @param $token
     * @return bool
     */
    public function coverageProject($token): bool
    {
        try {
            /** @var UpgradeServices $services */
            $services = app()->make(UpgradeServices::class);
            $services->coverageProject($token);
        } catch (\Exception $e) {
            Log::error('Ghi đè dự án thất bại, nguyên nhân:' . $e->getMessage());
            // Khi thất bại cũng phải thiết lập trạng thái, tránh vòng lặp vô hạn
            \crmeb\services\CacheService::set($token . '_coverage_project', -1, 86400);
            \crmeb\services\CacheService::set($token . 'upgrade_status', -1, 86400);
            \crmeb\services\CacheService::set($token . 'upgrade_status_tip', 'Ghi đè dự án thất bại: ' . $e->getMessage(), 86400);
        }
        return true;
    }

    /**
     * Kiểm tra MD5 của file
     * @param $token
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/2/26
     */
    public function checkFileMd5($token)
    {
        try {
            /** @var SystemFileMd5Services $services */
            $services = app()->make(SystemFileMd5Services::class);
            $data = $services->checkFile();
            \crmeb\services\CacheService::set($token . '_check_md5_file', $data, 86400);
            \crmeb\services\CacheService::set($token . '_check_md5_status', empty($data) ? 2 : 1, 86400);
        } catch (\Exception $e) {
            Log::error('Kiểm tra MD5 của tệp thất bại, nguyên nhân:' . $e->getMessage());
        }
        return true;
    }
}
