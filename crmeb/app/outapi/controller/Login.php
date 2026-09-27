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
namespace app\outapi\controller;

use app\outapi\validate\LoginValidate;
use app\Request;
use think\facade\App;
use app\services\out\OutAccountServices;

/**
 * Class Login
 * @package app\out\controller
 */
class Login extends AuthController
{
    /**
     * OutAccount constructor.
     * @param App $app
     * @param OutAccountServices $services
     */
    public function __construct(App $app, OutAccountServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    protected function initialize()
    {
        // TODO: Implement initialize() method.
    }

    /**
     * Đăng nhập CSKH
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getToken(Request $request)
    {
        [$appid, $appsecret] = $request->postMore([
            ['appid', ''],
            ['appsecret', ''],
        ], true);
        $this->validate(['appid' => $appid, 'appsecret' => $appsecret], LoginValidate::class);

        $token = $this->services->authLogin($appid, $appsecret);

        return app('json')->success(100010, $token);
    }

    /**
     * Làm mới token
     * @return void
     */
    public function refreshToken(Request $request)
    {
        [$token] = $request->postMore([
            ['access_token', ''],
        ], true);
        $token = $this->services->refresh($token);
        return app('json')->success(100010, $token);
    }

}
