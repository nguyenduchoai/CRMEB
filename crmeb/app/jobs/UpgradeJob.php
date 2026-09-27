<?php

namespace app\jobs;

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
}