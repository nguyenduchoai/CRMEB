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

use app\adminapi\controller\AuthController;
use app\services\system\crontab\SystemCrontabServices;
use think\facade\App;
use think\facade\Env;

class SystemCrontab extends AuthController
{
    public function __construct(App $app, SystemCrontabServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy danh sách tác vụ định kỳ
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTimerList()
    {
        $where = $this->request->getMore([
            ['custom', 0],
        ]);
        $where['is_del'] = 0;
        return app('json')->success($this->services->getTimerList($where));
    }

    /**
     * Lấy chi tiết tác vụ định kỳ
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTimerInfo($id)
    {
        return app('json')->success($this->services->getTimerInfo($id));
    }

    /**
     * Lấy loại tác vụ định kỳ
     * @return mixed
     */
    public function getMarkList()
    {
        return app('json')->success($this->services->getMarkList());
    }

    /**
     * Lưu tác vụ định kỳ
     * @return mixed
     */
    public function saveTimer()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['name', ''],
            ['mark', ''],
            ['content', ''],
            ['type', 0],
            ['is_open', 0],
            ['month', 0],
            ['week', 0],
            ['day', 0],
            ['hour', 0],
            ['minute', 0],
            ['second', 0],
            ['customCode', ''],
            ['password', ''],
        ]);
        if ($data['mark'] == 'customTimer') {
            if (!Env::get('app_debug', false)) return app('json')->fail('Không thể thêm mới và sửa nội dung tùy chỉnh trong môi trường production, nếu cần sửa, vui lòng đặt mục app_debug trong tệp .env thành true');
            if ($data['password'] === '') return app('json')->fail('Mật khẩu không được để trống');
            if (config('filesystem.password') !== $data['password']) return app('json')->fail('Mật khẩu không đúng');
            $adminInfo = $this->request->adminInfo();
            if (!$adminInfo) return app('json')->fail('Thao tác không hợp lệ');
            if ($adminInfo['level'] != 0) return app('json')->fail('Chỉ quản trị viên cấp cao nhất mới được thao tác tác vụ định kỳ');
            if (!$this->isSafePhpCode($data['customCode'])) return app('json')->fail('Nội dung tùy chỉnh chứa mã nguy hiểm, vui lòng kiểm tra lại mã');
        }
        $this->services->saveTimer($data);
        return app('json')->success('Lưu thành công');
    }

    /**
     * Xóa tác vụ định kỳ
     * @param $id
     * @return mixed
     */
    public function delTimer($id)
    {
        $this->services->delTimer($id);
        return app('json')->success('Xóa thành công');
    }

    /**
     * Thiết lập trạng thái tác vụ định kỳ
     * @param $id
     * @param $is_open
     * @return mixed
     */
    public function setTimerStatus($id, $is_open)
    {
        $this->services->setTimerStatus($id, $is_open);
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Kiểm tra có chứa từ khóa của các thao tác như xóa bảng, xóa dữ liệu bảng, xóa file, sửa nội dung và phần mở rộng file, thực thi lệnh... không
     * @param $code
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/6
     */
    function isSafePhpCode($code)
    {
        // Kiểm tra có chứa từ khóa của các thao tác như xóa bảng, xóa dữ liệu bảng, xóa file, sửa nội dung và phần mở rộng file, thực thi lệnh... không
        $dangerous_keywords = array(
            'delete',
            'destroy',
            'DROP TABLE',
            'DELETE FROM',
            'unlink(',
            'fwrite(',
            'shell_exec(',
            'exec(',
            'system(',
            'passthru('
        );
        foreach ($dangerous_keywords as $keyword) {
            if (strpos($code, $keyword) !== false) {
                return false;
            }
        }
        return true; // Nếu vượt qua tất cả kiểm tra an toàn, trả về true
    }

}