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
namespace app\adminapi\controller\v1\merchant;

use app\services\system\store\SystemStoreServices;
use app\services\system\store\SystemStoreStaffServices;
use think\facade\App;
use app\adminapi\controller\AuthController;

/**
 * Nhân viên cửa hàng
 * Class SystemStoreStaff
 * @package app\adminapi\controller\v1\merchant
 */
class SystemStoreStaff extends AuthController
{
    /**
     * Phương thức khởi tạo
     * SystemStoreStaff constructor.
     * @param App $app
     * @param SystemStoreStaffServices $services
     */
    public function __construct(App $app, SystemStoreStaffServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy danh sách nhân viên cửa hàng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            [['store_id', 'd'], 0],
        ]);
        return app('json')->success($this->services->getStoreStaffList($where));
    }

    /**
     * Danh sách cửa hàng
     * @param SystemStoreServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function store_list(SystemStoreServices $services)
    {
        return app('json')->success($services->getStore());
    }

    /**
     * Form thêm nhân viên cửa hàng
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function create()
    {
        return app('json')->success($this->services->createForm());
    }

    /**
     * Form sửa nhân viên cửa hàng
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        [$id] = $this->request->getMore([
            [['id', 'd'], 0],
        ], true);
        return app('json')->success($this->services->updateForm($id));
    }

    /**
     * Lưu thông tin nhân viên cửa hàng
     * @param int $id
     * @return mixed
     */
    public function save($id = 0)
    {
        $data = $this->request->postMore([
            ['image', ''],
            ['uid', 0],
            ['avatar', ''],
            ['store_id', ''],
            ['staff_name', ''],
            ['phone', ''],
            ['verify_status', 1],
            ['status', 1],
        ]);
        if (!$id) {
            if ($data['image'] == '') {
                return app('json')->fail('Vui lòng chọn người dùng');
            }
            if ($this->services->count(['uid' => $data['image']['uid']])) {
                return app('json')->fail('Người dùng được thêm làm nhân viên xác nhận đã tồn tại');
            }
            $data['uid'] = $data['image']['uid'];
            $data['avatar'] = $data['image']['image'];
        } else {
            $data['avatar'] = $data['image'];
        }
        if ($data['uid'] == 0) {
            return app('json')->fail('Vui lòng chọn người dùng');
        }
        if ($data['store_id'] == '') {
            return app('json')->fail('Vui lòng chọn điểm nhận hàng trực thuộc');
        }
        if ($data['staff_name'] == ''){
            return app('json')->fail('Vui lòng điền tên nhân viên xác nhận');
        }
        if ($data['phone'] == ''){
            return app('json')->fail('Vui lòng điền số điện thoại nhân viên xác nhận');
        }
        unset($data['image']);
        if ($id) {
            $res = $this->services->update($id, $data);
            if ($res) {
                return app('json')->success('Sửa thành công');
            } else {
                return app('json')->fail('Sửa thất bại');
            }
        } else {
            $data['add_time'] = time();
            $res = $this->services->save($data);
            if ($res) {
                return app('json')->success('Thêm nhân viên xác nhận thành công');
            } else {
                return app('json')->fail('Thêm nhân viên xác nhận thất bại');
            }
        }
    }

    /**
     * Thiết lập bật/tắt cho một nhân viên cửa hàng
     * @param string $is_show
     * @param string $id
     * @return mixed
     */
    public function set_show($is_show = '', $id = '')
    {
        if ($is_show == '' || $id == '') {
            app('json')->fail('Tham số không hợp lệ');
        }
        $res = $this->services->update($id, ['status' => (int)$is_show]);
        if ($res) {
            return app('json')->success('Cài đặt thành công');
        } else {
            return app('json')->fail('Cài đặt thất bại');
        }
    }

    /**
     * Xóa nhân viên cửa hàng
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        if (!$this->services->delete($id))
            return app('json')->fail('Xóa thất bại');
        else
            return app('json')->success('Xóa thành công');
    }
}
