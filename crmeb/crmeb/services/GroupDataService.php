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

namespace crmeb\services;

use app\services\system\config\SystemGroupDataServices;

/**
 * Lấy cấu hình dữ liệu tổ hợp
 * Class GroupDataService
 * @package crmeb\services
 */
class GroupDataService
{
    /**
     * Lấy một giá trị
     * @param string $config_name Tên cấu hình
     * @param int $limit Cắt lấy bao nhiêu bản ghi
     * @param bool $isCaChe Có đọc cache không
     * @return array
     */
    public static function getData(string $config_name, int $limit = 0, bool $isCaChe = false): array
    {
        $callable = function () use ($config_name, $limit) {
            try {
                /** @var SystemGroupDataServices $service */
                $service = app()->make(SystemGroupDataServices::class);
                return $service->getConfigNameValue($config_name, $limit);
            } catch (\Exception $e) {
                return [];
            }
        };
        try {
            $cacheName = $limit ? "data_{$config_name}_{$limit}" : "data_{$config_name}";

            if ($isCaChe)
                return $callable();

            return CacheService::remember($cacheName, $callable);

        } catch (\Throwable $e) {
            return $callable();
        }
    }

    /**
     * Lấy một giá trị theo id
     * @param int $id
     * @param bool $isCaChe Có đọc cache không
     * @return array
     */
    public static function getDataNumber(int $id, bool $isCaChe = false): array
    {
        $callable = function () use ($id) {
            try {

                /** @var SystemGroupDataServices $service */
                $service = app()->make(SystemGroupDataServices::class);
                $data = $service->getDateValue($id);
                if (is_object($data))
                    $data = $data->toArray();
                return $data;
            } catch (\Exception $e) {
                return [];
            }
        };
        try {
            $cacheName = "data_number_{$id}";

            if ($isCaChe)
                return $callable();

            return CacheService::remember($cacheName, $callable);

        } catch (\Throwable $e) {
            return $callable();
        }
    }

    public static function getDataNumbers($ids)
    {
        try {
            if (is_string($ids)) $ids = explode(',', $ids);
            /** @var SystemGroupDataServices $service */
            $service = app()->make(SystemGroupDataServices::class);
            $data = $service->getGroupDataColumn($ids);
            if (is_object($data))
                $data = $data->toArray();
            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }
}
