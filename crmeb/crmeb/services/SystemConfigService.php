<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace crmeb\services;

use app\services\system\config\SystemConfigServices;
use crmeb\utils\Arr;

/** Lấy class service cấu hình hệ thống
 * Class SystemConfigService
 * @package service
 */
class SystemConfigService
{
    const CACHE_SYSTEM = 'system_config';

    /**
     * Lấy một cấu hình đơn có hiệu suất cao hơn
     * @param string $key
     * @param $default
     * @param bool $isCaChe Có lấy cấu hình từ cache không
     * @return bool|mixed|string
     */
    public static function get(string $key, $default = '', bool $isCaChe = true)
    {
        $callable = function () use ($key) {
            return app()->make(SystemConfigServices::class)->getConfigValue($key);
        };

        try {
            if ($isCaChe) {
                return CacheService::remember(self::CACHE_SYSTEM . '_' . $key, $callable);
            }
            return $callable();
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Lấy nhiều cấu hình
     * @param array $keys Ví dụ [['appid','1'],'appkey']
     * @param bool $isCaChe Có lấy cấu hình từ cache không
     * @return array
     */
    public static function more(array $keys, bool $isCaChe = true)
    {
        $callable = function () use ($keys) {
            return Arr::getDefaultValue($keys, app()->make(SystemConfigServices::class)->getConfigAll($keys));
        };

        try {
            if ($isCaChe){
                return CacheService::remember(self::CACHE_SYSTEM . '_' . md5(implode(',', $keys)), $callable);
            }
            return $callable();
        } catch (\Throwable $e) {
            return Arr::getDefaultValue($keys);
        }
    }
}
