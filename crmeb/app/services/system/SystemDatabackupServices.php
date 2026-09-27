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

namespace app\services\system;


use app\services\BaseServices;
use crmeb\services\MysqlBackupService;
use think\facade\Db;
use think\facade\Env;

/**
 * Sao lưu cơ sở dữ liệu
 * Class SystemDatabackupServices
 * @package app\services\system
 */
class SystemDatabackupServices extends BaseServices
{

    /**
     *
     * @var MysqlBackupService
     */
    protected $dbBackup;

    /**
     * Phương thức khởi tạo
     * SystemDatabackupServices constructor.
     */
    public function __construct()
    {
        $this->dbBackup = app()->make(MysqlBackupService::class, [[
            //Kích thước volume backup cơ sở dữ liệu
            'compress' => 1,
            //File backup cơ sở dữ liệu có bật nén không, 0 là không nén, 1 là nén
            'level' => 5,
        ]]);
    }

    /**
     * Lấy danh sách cơ sở dữ liệu
     * @return array
     * @throws \think\db\exception\BindParamException
     */
    public function getDataList()
    {
        $list = $this->dbBackup->dataList();
        $count = count($list);
        return compact('list', 'count');
    }

    /**
     * Lấy chi tiết bảng
     * @param string $tablename
     * @return array
     */
    public function getRead(string $tablename)
    {
        $database = Env::get("database.database");
        $list = Db::query("select * from information_schema.columns where table_name = '" . $tablename . "' and table_schema = '" . $database . "'");
        $count = count($list);
        foreach ($list as $key => $f) {
            $list[$key]['EXTRA'] = ($f['EXTRA'] == 'auto_increment' ? 'Có' : ' ');
        }
        return compact('list', 'count');
    }

    /**
     * @return MysqlBackupService
     */
    public function getDbBackup()
    {
        return $this->dbBackup;
    }

    /**
     * Bảng backup
     * @param string $tables
     * @return string
     * @throws \think\db\exception\BindParamException
     */
    public function backup(string $tables)
    {
        $tables = explode(',', $tables);
        $data = '';
        ini_set ("memory_limit","-1");
        foreach ($tables as $t) {
            $res = $this->dbBackup->backup($t, 0);
            if ($res == false && $res != 0) {
                $data .= $t . '|';
            }
        }
        return $data;
    }

    /**
     * Lấy danh sách backup
     * @return array
     */
    public function getBackup()
    {
        $files = $this->dbBackup->fileList();
        $data = [];
        foreach ($files as $key => $t) {
            $data[$key]['filename'] = $t['filename'];
            $data[$key]['part'] = $t['part'];
            $data[$key]['size'] = $t['size'] . 'B';
            $data[$key]['compress'] = $t['compress'];
            $data[$key]['backtime'] = $key;
            $data[$key]['time'] = $t['time'];
        }
        krsort($data);//Sắp xếp giảm dần theo thời gian
        return ['count' => count($data), 'list' => array_values($data)];
    }

}
