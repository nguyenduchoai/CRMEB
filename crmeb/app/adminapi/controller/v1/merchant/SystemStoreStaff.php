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
                return app('json')->fail(400250);
            }
            if ($this->services->count(['uid' => $data['image']['uid']])) {
                return app('json')->fail(400126);
            }
            $data['uid'] = $data['image']['uid'];
            $data['avatar'] = $data['image']['image'];
        } else {
            $data['avatar'] = $data['image'];
        }
        if ($data['uid'] == 0) {
            return app('json')->fail(400250);
        }
        if ($data['store_id'] == '') {
            return app('json')->fail(400127);
        }
        if ($data['staff_name'] == ''){
            return app('json')->fail(400128);
        }
        if ($data['phone'] == ''){
            return app('json')->fail(400129);
        }
        unset($data['image']);
        if ($id) {
            $res = $this->services->update($id, $data);
            if ($res) {
                return app('json')->success(100001);
            } else {
                return app('json')->fail(100007);
            }
        } else {
            $data['add_time'] = time();
            $res = $this->services->save($data);
            if ($res) {
                return app('json')->success(400130);
            } else {
                return app('json')->fail(400131);
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
            app('json')->fail(100100);
        }
        $res = $this->services->update($id, ['status' => (int)$is_show]);
        if ($res) {
            return app('json')->success(100014);
        } else {
            return app('json')->fail(100015);
        }
    }

    /**
     * Xóa nhân viên cửa hàng
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail(100100);
        if (!$this->services->delete($id))
            return app('json')->fail(100008);
        else
            return app('json')->success(100002);
    }
}
