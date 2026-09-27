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

use app\dao\system\log\SystemFileMd5Dao;
use app\services\BaseServices;
use think\facade\Config;
use think\facade\Db;

class SystemFileMd5Services extends BaseServices
{
    public function __construct(SystemFileMd5Dao $dao)
    {
        $this->dao = $dao;
    }

    public function saveMd5List(array $list)
    {
        if (empty($list)) return true;
        return (bool)$this->transaction(function () use ($list) {
            return $this->dao->saveAll($list);
        });
    }

    public function clearMd5List()
    {
        $prefix = Config::get('database.connections.' . Config::get('database.default') . '.prefix');
        Db::execute('TRUNCATE TABLE `' . $prefix . 'system_file_md5`');
    }

    public function checkFile()
    {
        $rootPath = app()->getRootPath();
        $files = array_merge(
            $this->getDir($rootPath . 'app'),
            $this->getDir($rootPath . 'crmeb')
        );
        // Chỉ tìm file .php
        $files = array_filter($files, function ($path) {
            return pathinfo($path, PATHINFO_EXTENSION) === 'php';
        });
        $len = strlen($rootPath);
        // Danh sách hiện tại
        $list = $this->dao->getColumn([], 'md5', 'filename');
        $currentList = [];
        foreach ($files as $path) {
            $currentList[substr($path, $len)] = md5_file($path);
        }

        // Bỏ qua các thay đổi của file app/adminapi/controller/UpgradeController.php
        unset($currentList['app/adminapi/controller/UpgradeController.php']);
        unset($list['app/adminapi/controller/UpgradeController.php']);

        $diffFiles = [];
        $newFiles = array_keys(array_diff_key($currentList, $list));
        $delFiles = array_keys(array_diff_key($list, $currentList));
        $changedFiles = array_keys(array_diff_assoc($currentList, $list));
        return array_values(array_unique(array_merge($newFiles, $delFiles, $changedFiles)));
    }

    public function getDir($dir)
    {
        $data = [];
        $this->searchDir($dir, $data);
        return $data;
    }

    public function searchDir($path, &$data)
    {
        if (is_dir($path) && !strpos($path, 'uploads')) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $this->searchDir($path . '/' . $file, $data);
                }
            }
        }
        if (is_file($path)) {
            $data[] = $path;
        }
    }
}
