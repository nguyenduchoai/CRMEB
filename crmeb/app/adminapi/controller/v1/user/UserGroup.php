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
namespace app\adminapi\controller\v1\user;

use app\adminapi\controller\AuthController;
use app\services\user\UserGroupServices;
use think\facade\App;

/**
 * Cài đặt thành viên
 * Class UserLevel
 * @package app\admin\controller\user
 */
class UserGroup extends AuthController
{
    /**
     * user constructor.
     * @param App $app
     * @param UserGroupServices $services
     */
    public function __construct(App $app, UserGroupServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách nhóm
     */
    public function index()
    {
        return app('json')->success($this->services->getGroupList('*', true));
    }

    /**
     * Trang thêm/sửa nhóm
     * @param int $id
     * @return string
     */
    public function add()
    {
        $data = $this->request->getMore([
            ['id', 0],
        ]);
        return app('json')->success($this->services->add((int)$data['id']));
    }

    /**
     *
     * @param int $id
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['group_name', ''],
        ]);
        if (!$data['group_name']) {
            return app('json')->fail('Vui lòng nhập tên nhóm');
        }
        $this->services->save((int)$data['id'], $data);
        return app('json')->success('Gửi thành công');
    }

    /**
     * Xóa
     * @param $id
     * @throws \Exception
     */
    public function delete()
    {
        $data = $this->request->getMore([
            ['id', 0],
        ]);
        if (!$data['id']) return app('json')->fail('Tham số không hợp lệ');
        return app('json')->success($this->services->delGroup((int)$data['id']));
    }
}
