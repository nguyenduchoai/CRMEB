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
use crmeb\services\CacheService;
use think\facade\{App, Config};

/**
 * Class SystemAdmin
 * @package app\adminapi\controller\v1\setting
 */
class SystemAdmin extends AuthController
{
    /**
     * SystemAdmin constructor.
     * @param App $app
     * @param SystemAdminServices $services
     */
    public function __construct(App $app, SystemAdminServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Hiển thị danh sách resource quản trị viên
     *
     * @return \think\Response
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['name', '', '', 'account_like'],
            ['roles', ''],
            ['is_del', 1],
            ['status', '']
        ]);
        $where['level'] = $this->adminInfo['level'] + 1;
        return app('json')->success($this->services->getAdminList($where));
    }

    /**
     * Tạo form
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        return app('json')->success($this->services->createForm($this->adminInfo['level'] + 1));
    }

    /**
     * Lưu quản trị viên
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['account', ''],
            ['conf_pwd', ''],
            ['pwd', ''],
            ['real_name', ''],
            ['roles', []],
            ['status', 0],
        ]);

        $this->validate($data, \app\adminapi\validate\setting\SystemAdminValidata::class);

        $data['level'] = $this->adminInfo['level'] + 1;
        $this->services->create($data);
        return app('json')->success('Lưu thành công');
    }

    /**
     * Hiển thị trang form sửa resource.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if (!$id) {
            return app('json')->fail('Đọc thông tin quản trị viên thất bại');
        }

        return app('json')->success($this->services->updateForm($this->adminInfo['level'] + 1, (int)$id));
    }

    /**
     * Sửa thông tin quản trị viên
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['account', ''],
            ['conf_pwd', ''],
            ['pwd', ''],
            ['real_name', ''],
            ['roles', []],
            ['status', 0],
        ]);

        $this->validate($data, \app\adminapi\validate\setting\SystemAdminValidata::class, 'update');

        if ($this->services->save((int)$id, $data)) {
            return app('json')->success('Sửa thành công');
        } else {
            return app('json')->fail('Sửa thất bại');
        }
    }

    /**
     * Xóa quản trị viên
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        if ($this->services->update((int)$id, ['is_del' => 1, 'status' => 0]))
            return app('json')->success('Xóa thành công');
        else
            return app('json')->fail('Xóa thất bại');
    }

    /**
     * Sửa trạng thái
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_status($id, $status)
    {
        $this->services->update((int)$id, ['status' => $status]);
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Lấy thông tin quản trị viên đang đăng nhập
     * @return mixed
     */
    public function info()
    {
        return app('json')->success($this->adminInfo);
    }

    /**
     * Sửa thông tin admin đang đăng nhập
     * @return mixed
     */
    public function update_admin()
    {
        $data = $this->request->postMore([
            ['real_name', ''],
            ['head_pic', ''],
            ['pwd', ''],
            ['new_pwd', ''],
            ['conf_pwd', ''],
        ]);

        if ($data['pwd']) {
            if (!preg_match('/^(?![^a-zA-Z]+$)(?!\D+$).{6,}$/', $data['new_pwd'])) {
                return app('json')->fail('Mật khẩu quá đơn giản, vui lòng nhập mật khẩu phức tạp hơn');
            }
        }

        if ($this->services->updateAdmin($this->adminId, $data))
            return app('json')->success('Sửa thành công');
        else
            return app('json')->fail('Sửa thất bại');
    }

    /**
     * Sửa mật khẩu quản lý file của admin đang đăng nhập
     * @return mixed
     */
    public function set_file_password()
    {
        $data = $this->request->postMore([
            ['file_pwd', ''],
            ['conf_file_pwd', ''],
        ]);
        if (!preg_match('/^(?![^a-zA-Z]+$)(?!\D+$).{6,}$/', $data['file_pwd'])) {
            return app('json')->fail('Mật khẩu quá đơn giản, vui lòng nhập mật khẩu phức tạp hơn');
        }
        if ($this->services->setFilePassword($this->adminId, $data))
            return app('json')->success('Sửa thành công');
        else
            return app('json')->fail('Sửa thất bại');
    }

    /**
     * Đăng xuất
     * @return mixed
     */
    public function logout()
    {
        $key = trim(ltrim($this->request->header(Config::get('cookie.token_name')), 'Bearer'));
        CacheService::delete(md5($key));
        return app('json')->success();
    }
}
