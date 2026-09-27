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
namespace app\adminapi\controller\v1\system;

use think\facade\App;
use think\facade\Db;
use think\facade\Session;
use app\adminapi\controller\AuthController;
use app\services\system\SystemDatabackupServices;


/**
 * Sao lưu dữ liệu
 * Class SystemDatabackup
 * @package app\admin\controller\system
 *
 */
class SystemDatabackup extends AuthController
{
    /**
     * Phương thức khởi tạo
     * SystemDatabackup constructor.
     * @param App $app
     * @param SystemDatabackupServices $services
     */
    public function __construct(App $app, SystemDatabackupServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy bảng cơ sở dữ liệu
     */
    public function index()
    {
        return app('json')->success($this->services->getDataList());
    }

    /**
     * Xem chi tiết cấu trúc bảng
     */
    public function read()
    {
        [$tablename] = $this->request->getMore([
            ['tablename', ''],
        ], true);
        return app('json')->success($this->services->getRead($tablename));
    }

    /**
     * Cập nhật ghi chú bảng dữ liệu hoặc trường của bảng
     * @return \think\Response
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/04/11
     */
    public function updateMark()
    {
        [$table, $field, $type, $mark, $is_field] = $this->request->postMore([
            ['table', ''],
            ['field', ''],
            ['type', ''],
            ['mark', ''],
            ['is_field', 0],
        ], true);
        if ($is_field == 0) {
            $sql = "ALTER TABLE $table COMMENT '$mark'";
        } else {
            $sql = "ALTER TABLE $table MODIFY COLUMN $field $type COMMENT '$mark'";
        }
        Db::execute($sql);
        return app('json')->success(100024);
    }

    /**
     * Tối ưu bảng
     */
    public function optimize()
    {
        [$tables] = $this->request->postMore([
            ['tables', ''],
        ], true);
        $res = $this->services->getDbBackup()->optimize($tables);
        return app('json')->success($res ? 100047 : 100048);
    }

    /**
     * Sửa chữa bảng
     */
    public function repair()
    {
        [$tables] = $this->request->postMore([
            ['tables', ''],
        ], true);
        $res = $this->services->getDbBackup()->repair($tables);
        return app('json')->success($res ? 100049 : 100050);
    }

    /**
     * Bảng backup
     */
    public function backup()
    {
        [$tables] = $this->request->postMore([
            ['tables', ''],
        ], true);
        $data = $this->services->backup($tables);
        return app('json')->success(100051);
    }

    /**
     * Lấy bảng lịch sử backup
     */
    public function fileList()
    {
        return app('json')->success($this->services->getBackup());
    }

    /**
     * Xóa bảng lịch sử backup
     */
    public function delFile()
    {
        $filename = intval(request()->post('filename'));
        $files = $this->services->getDbBackup()->delFile($filename);
        return app('json')->success(100002);
    }

    /**
     * Nhập bảng lịch sử backup
     */
    public function import()
    {
        [$part, $start, $time] = $this->request->postMore([
            [['part', 'd'], 0],
            [['start', 'd'], 0],
            [['time', 'd'], 0],
        ], true);
        $db = $this->services->getDbBackup();
        if (is_numeric($time) && !$start) {
            $list = $db->getFile('timeverif', $time);
            if (is_array($list)) {
                session::set('backup_list', $list);
                return app('json')->success(400307, array('part' => 1, 'start' => 0));
            } else {
                return app('json')->fail(400308);
            }
        } else if (is_numeric($part) && is_numeric($start) && $part && $start) {
            $list = session::get('backup_list');
            $start = $db->setFile($list)->import($start);
            if (false === $start) {
                return app('json')->fail(400309);
            } elseif (0 === $start) {
                if (isset($list[++$part])) {
                    $data = array('part' => $part, 'start' => 0);
                    return app('json')->success(400310, $data);
                } else {
                    session::delete('backup_list');
                    return app('json')->success(400311);
                }
            } else {
                $data = array('part' => $part, 'start' => $start[0]);
                if ($start[1]) {
                    $rate = floor(100 * ($start[0] / $start[1]));
                    return app('json')->success(400310, $data);
                } else {
                    $data['gz'] = 1;
                    return app('json')->success(400310, $data);
                }
            }
        } else {
            return app('json')->fail(100100);
        }
    }

    /**
     * Tải bảng lịch sử backup
     */
    public function downloadFile()
    {
        $time = intval(request()->param('time'));
        return app('json')->success(['key' => $this->services->getDbBackup()->downloadFile($time, 0, true)]);
    }

}
