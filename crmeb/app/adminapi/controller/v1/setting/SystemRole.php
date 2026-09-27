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
namespace app\adminapi\controller\v1\setting;

use app\adminapi\controller\AuthController;
use app\services\system\admin\SystemAdminServices;
use app\services\system\admin\SystemRoleServices;
use app\services\system\SystemMenusServices;
use crmeb\services\CacheService;
use think\facade\App;

/**
 * Class SystemRole
 * @package app\adminapi\controller\v1\setting
 */
class SystemRole extends AuthController
{
    /**
     * SystemRole constructor.
     * @param App $app
     * @param SystemRoleServices $services
     */
    public function __construct(App $app, SystemRoleServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Hiển thị danh sách resource
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['role_name', ''],
        ]);
        $where['level'] = $this->adminInfo['level'] + 1;
        return app('json')->success($this->services->getRoleList($where));
    }

    /**
     * Hiển thị trang form tạo resource
     * @param SystemMenusServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function create(SystemMenusServices $services)
    {
        $menus = $services->getmenus($this->adminInfo['level'] == 0 ? [] : $this->adminInfo['roles']);
        return app('json')->success(compact('menus'));
    }

    /**
     * Lưu resource mới tạo
     *
     * @return \think\Response
     */
    public function save($id)
    {
        $data = $this->request->postMore([
            'role_name',
            ['status', 0],
            ['checked_menus', [], '', 'rules']
        ]);
        if (!$data['role_name']) return app('json')->fail('Vui lòng nhập tên vai trò');
        if (!is_array($data['rules']) || !count($data['rules']))
            return app('json')->fail('Vui lòng chọn ít nhất một quyền');

        $data['rules'] = implode(',', $data['rules']);
        if ($id) {
            if (!$this->services->update($id, $data)) return app('json')->fail('Sửa thất bại');
            CacheService::clear();
            return app('json')->success('Sửa thành công');
        } else {
            $data['level'] = $this->adminInfo['level'] + 1;
            if (!$this->services->save($data)) return app('json')->fail('Thêm vai trò thất bại');
            CacheService::clear();
            return app('json')->success('Thêm vai trò thành công');
        }
    }

    /**
     * Hiển thị trang form sửa resource
     * @param SystemMenusServices $services
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit(SystemMenusServices $services, $id)
    {
        $role = $this->services->get($id);
        if (!$role) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        $menus = $services->getMenus($this->adminInfo['level'] == 0 ? [] : $this->adminInfo['roles'], explode(',', $role['rules']));
        return app('json')->success(['role' => $role->toArray(), 'menus' => $menus]);
    }

    /**
     * Xóa resource được chỉ định
     * @param SystemAdminServices $adminServices
     * @param $id
     * @return mixed
     */
    public function delete(SystemAdminServices $adminServices, $id)
    {
        if ($adminServices->checkRoleUse($id)) {
            return app('json')->fail('Vai trò đang được sử dụng, không thể xóa');
        }
        if (!$this->services->delete($id))
            return app('json')->fail('Xóa thất bại');
        else {
            CacheService::clear();
            return app('json')->success('Xóa thành công');
        }
    }

    /**
     * Sửa trạng thái
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_status($id, $status)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        $role = $this->services->get($id);
        if (!$role) {
            return app('json')->fail('Không tìm thấy vai trò này');
        }
        $role->status = $status;
        if ($role->save()) {
            CacheService::clear();
            return app('json')->success('Sửa thành công');
        } else {
            return app('json')->fail('Sửa thất bại');
        }
    }
}
