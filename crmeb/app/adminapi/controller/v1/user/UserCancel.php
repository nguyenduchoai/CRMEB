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
use app\services\user\UserCancelServices;
use think\facade\App;

class UserCancel extends AuthController
{
    /**
     * UserCancel constructor.
     * @param App $app
     * @param UserCancelServices $services
     */
    public function __construct(App $app, UserCancelServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy danh sách hủy tài khoản
     * @return mixed
     */
    public function getCancelList()
    {
        $where = $this->request->postMore([
            ['status', 0],
            ['keywords', ''],
        ]);
        $data = $this->services->getCancelList($where);
        return app('json')->success($data);
    }

    /**
     * Ghi chú
     * @return mixed
     */
    public function setMark()
    {
        [$id, $mark] = $this->request->postMore([
            ['id', 0],
            ['mark', ''],
        ], true);
        $this->services->serMark($id, $mark);
        return app('json')->success('Ghi chú thành công');
    }

    public function agreeCancel($id)
    {
        return app('json')->success('Hủy tài khoản thành công');
    }

    public function refuseCancel($id)
    {
        return app('json')->success('Từ chối hủy tài khoản');
    }
}
