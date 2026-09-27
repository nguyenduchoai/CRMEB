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

use think\facade\Cache;
use think\facade\Config;
use think\cache\TagSet;

/**
 * Class cache CRMEB
 * Class CacheService
 * @package crmeb\services
 */
class CacheService
{
    /**
     * Thời gian hết hạn
     * @var int
     */
    protected static $expire;

    /**
     * Ghi cache
     * @param string $name Tên cache
     * @param mixed $value Giá trị cache
     * @param int|null $expire Thời gian cache, bằng 0 thì đọc thời gian cache của hệ thống
     */
    public static function set(string $name, $value, int $expire = 0, string $tag = 'crmeb')
    {
        try {
            return Cache::tag($tag)->set($name, $value, $expire);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Nếu không tồn tại thì ghi vào cache
     * @param string $name
     * @param mixed $default
     * @param int|null $expire
     * @param string $tag
     * @return mixed|string|null
     */
    public static function remember(string $name, $default = '', int $expire = 0, string $tag = 'crmeb')
    {
        try {
            return Cache::tag($tag)->remember($name, $default, $expire);
        } catch (\Throwable $e) {
            try {
                if (is_callable($default)) {
                    return $default();
                } else {
                    return $default;
                }
            } catch (\Throwable $e) {
                return null;
            }
        }
    }

    /**
     * Đọc cache
     * @param string $name
     * @param mixed $default
     * @return mixed|string
     */
    public static function get(string $name, $default = '')
    {
        return Cache::get($name) ?? $default;
    }

    /**
     * Xóa cache
     * @param string $name
     * @return bool
     */
    public static function delete(string $name)
    {
        return Cache::delete($name);
    }

    /**
     * Xóa toàn bộ cache pool
     * @return bool
     */
    public static function clear(string $tag = 'crmeb')
    {
        return Cache::tag($tag)->clear();
    }

    /**
     * Xóa toàn bộ cache
     * @return bool
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/12/19
     */
    public static function clearAll()
    {
        return Cache::clear();
    }

    /**
     * Kiểm tra cache có tồn tại không
     * @param string $key
     * @return bool
     */
    public static function has(string $key)
    {
        try {
            return Cache::has($key);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Chỉ định loại cache
     * @param string $type
     * @param string $tag
     * @return TagSet
     */
    public static function store(string $type = 'file', string $tag = 'crmeb')
    {
        return Cache::store($type)->tag($tag);
    }

    /**
     * Kiểm tra khóa (lock)
     * @param string $key
     * @param int $timeout
     * @return bool
     */
    public static function setMutex(string $key, int $timeout = 10): bool
    {
        $curTime = time();
        $readMutexKey = "redis:mutex:{$key}";
        $mutexRes = Cache::store('redis')->handler()->setnx($readMutexKey, $curTime + $timeout);
        if ($mutexRes) {
            return true;
        }
        //Dù có thoát bất ngờ, lần sau vào cũng sẽ kiểm tra key, tránh deadlock
        $time = Cache::store('redis')->handler()->get($readMutexKey);
        if ($curTime > $time) {
            Cache::store('redis')->handler()->del($readMutexKey);
            return Cache::store('redis')->handler()->setnx($readMutexKey, $curTime + $timeout);
        }
        return false;
    }

    /**
     * Xóa khóa (lock)
     * @param string $key
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/11/22
     */
    public static function delMutex(string $key)
    {
        $readMutexKey = "redis:mutex:{$key}";
        Cache::store('redis')->handler()->del($readMutexKey);
    }


    /**
     * Khóa cơ sở dữ liệu
     * @param $key
     * @param $fn
     * @param int $ex
     * @return mixed
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public static function lock($key, $fn, int $ex = 6)
    {
        if (Config::get('cache.default') == 'file') {
            return $fn();
        }
        return app()->make(LockService::class)->exec($key, $fn, $ex);
    }
}
