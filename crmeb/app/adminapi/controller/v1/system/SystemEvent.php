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
use app\services\system\SystemEventServices;
use think\facade\App;
use think\facade\Env;

class SystemEvent extends AuthController
{
    public function __construct(App $app, SystemEventServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Loại sự kiện tùy chỉnh
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/7
     */
    public function getMarkList()
    {
        return app('json')->success($this->services->getMarkList());
    }

    /**
     * Danh sách sự kiện tùy chỉnh
     * @return \think\Response
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/7
     */
    public function getEventList()
    {
        return app('json')->success($this->services->getEventList());
    }

    /**
     * Chi tiết sự kiện tùy chỉnh
     * @param $id
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/7
     */
    public function getEventInfo($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        return app('json')->success($this->services->getEventInfo($id));
    }

    /**
     * Thêm/sửa sự kiện tùy chỉnh
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/7
     */
    public function saveEvent()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['name', ''],
            ['mark', ''],
            ['content', ''],
            ['is_open', 0],
            ['customCode', ''],
            ['password', ''],
        ]);
        if ($data['name'] == '') return app('json')->fail('Vui lòng nhập tên sự kiện');
        if ($data['mark'] == '') return app('json')->fail('Vui lòng chọn loại sự kiện');
        if (!Env::get('app_debug', false)) return app('json')->fail('Không thể thêm mới và sửa nội dung tùy chỉnh trong môi trường production, nếu cần sửa, vui lòng đặt mục app_debug trong tệp .env thành true');
        if ($data['password'] === '') return app('json')->fail('Mật khẩu không được để trống');
        if (config('filesystem.password') !== $data['password']) return app('json')->fail('Mật khẩu không đúng');
        $adminInfo = $this->request->adminInfo();
        if (!$adminInfo) return app('json')->fail('Thao tác không hợp lệ');
        if ($adminInfo['level'] != 0) return app('json')->fail('Chỉ quản trị viên cấp cao nhất mới được thao tác tác vụ định kỳ');
        if (!$this->isSafePhpCode($data['customCode'])) return app('json')->fail('Nội dung tùy chỉnh chứa mã nguy hiểm, vui lòng kiểm tra lại mã');
        $this->services->saveEvent($data);
        return app('json')->success('Lưu thành công');
    }

    /**
     * Kiểm tra có chứa từ khóa của các thao tác như xóa bảng, xóa dữ liệu bảng, xóa file, sửa nội dung và phần mở rộng file, thực thi lệnh... không
     * @param $code
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/7
     */
    function isSafePhpCode($code)
    {
        // Kiểm tra có chứa từ khóa của các thao tác như xóa bảng, xóa dữ liệu bảng, xóa file, sửa nội dung và phần mở rộng file, thực thi lệnh... không
        $dangerous_keywords = [
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
        ];
        foreach ($dangerous_keywords as $keyword) {
            if (strpos($code, $keyword) !== false) {
                return false;
            }
        }
        return true; // Nếu vượt qua tất cả kiểm tra an toàn, trả về true
    }

    /**
     * Công tắc bật/tắt sự kiện tùy chỉnh
     * @param $id
     * @param $is_open
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/7
     */
    public function setEventStatus($id, $is_open)
    {
        $this->services->setEventStatus($id, $is_open);
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Xóa sự kiện tùy chỉnh
     * @param $id
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/7
     */
    public function delEvent($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $this->services->eventDel($id);
        return app('json')->success('Xóa thành công');
    }
}