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
            $fieldInfo = Db::query("SHOW FULL COLUMNS FROM `{$table}` WHERE Field = '{$field}'");
            $sql = "ALTER TABLE $table MODIFY COLUMN ";
            $sql .= $field . ' ' . $type . ' ';
            if ($fieldInfo[0]['Null'] == 'NO') {
                $sql .= 'NOT NULL ';
                if (!is_null($fieldInfo[0]['Default'])) {
                    $sql .= "DEFAULT '" . $fieldInfo[0]['Default'] . "' ";
                }
            }
            if ($fieldInfo[0]['Extra']) {
                $sql .= $fieldInfo[0]['Extra'] . ' ';
            }
            $sql .= "COMMENT '$mark'";
        }
        Db::execute($sql);
        return app('json')->success('Ghi chú thành công');
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
        return app('json')->success($res ? 'Tối ưu hóa thành công' : 'Tối ưu hóa thất bại');
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
        return app('json')->success($res ? 'Sửa chữa thành công' : 'Sửa chữa thất bại');
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
        return app('json')->success('Sao lưu thành công');
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
        return app('json')->success('Xóa thành công');
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
                return app('json')->success('Khởi tạo hoàn tất', array('part' => 1, 'start' => 0));
            } else {
                return app('json')->fail('Tệp sao lưu có thể đã bị hỏng, vui lòng kiểm tra');
            }
        } else if (is_numeric($part) && is_numeric($start) && $part && $start) {
            $list = session::get('backup_list');
            $start = $db->setFile($list)->import($start);
            if (false === $start) {
                return app('json')->fail('Lỗi khi khôi phục dữ liệu');
            } elseif (0 === $start) {
                if (isset($list[++$part])) {
                    $data = array('part' => $part, 'start' => 0);
                    return app('json')->success('Đang khôi phục...', $data);
                } else {
                    session::delete('backup_list');
                    return app('json')->success('Khôi phục hoàn tất');
                }
            } else {
                $data = array('part' => $part, 'start' => $start[0]);
                if ($start[1]) {
                    $rate = floor(100 * ($start[0] / $start[1]));
                    return app('json')->success('Đang khôi phục...', $data);
                } else {
                    $data['gz'] = 1;
                    return app('json')->success('Đang khôi phục...', $data);
                }
            }
        } else {
            return app('json')->fail('Tham số không hợp lệ');
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
