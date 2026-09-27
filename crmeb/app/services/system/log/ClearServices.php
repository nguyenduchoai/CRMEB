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

namespace app\services\system\log;


use app\services\BaseServices;
use crmeb\services\CacheService;

/**
 * Class ClearServices
 * @package app\services\system\log
 */
class ClearServices extends BaseServices
{
    /** Xóa file theo kiểu đệ quy
     * @param $dirName
     * @param bool $subdir
     */
    protected function delDirAndFile($dirName)
    {
        $list = glob($dirName . '*');
        foreach ($list as $file) {
            if (is_dir($file))
                $this->delDirAndFile($file . DS);
            else
                @unlink($file);
        }
        @rmdir($dirName);
    }

    /**
     * Xóa log
     */
    public function deleteLog()
    {
        $root = app()->getRootPath() . 'runtime' . DS;
        $this->delDirAndFile($root . 'admin' . DS . 'log' . DS);
        $this->delDirAndFile($root . 'api' . DS . 'log' . DS);
        $this->delDirAndFile($root . 'log' . DS);
    }

    /**
     * Làm mới cache dữ liệu
     */
    public function refresCache()
    {
        $root = app()->getRootPath() . 'runtime' . DS;
        $adminRoute = $root . 'admin';
        $apiRoute = $root . 'api';
        $cacheRoute = $root . 'cache';
        $cache = [];

        if (is_dir($adminRoute))
            $cache[$adminRoute] = scandir($adminRoute);
        if (is_dir($apiRoute))
            $cache[$apiRoute] = scandir($apiRoute);
        if (is_dir($cacheRoute))
            $cache[$cacheRoute] = scandir($cacheRoute);

        foreach ($cache as $p => $list) {
            foreach ($list as $file) {
                if (!in_array($file, ['.', '..', 'log', 'schema', 'route.php'])) {
                    $path = $p . DS . $file;
                    if (is_file($path)) {
                        @unlink($path);
                    } else {
                        $this->delDirAndFile($path . DS);
                    }
                }
            }
        }
        CacheService::clearAll();
    }
}
