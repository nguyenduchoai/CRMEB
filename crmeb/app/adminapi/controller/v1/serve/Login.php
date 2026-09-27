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

namespace app\adminapi\controller\v1\serve;


use app\adminapi\controller\AuthController;
use app\adminapi\validate\serve\ServeValidata;
use app\Request;
use app\services\yihaotong\SmsAdminServices;
use crmeb\services\CacheService;
use app\services\serve\ServeServices;
use think\facade\App;

/**
 * Đăng nhập dịch vụ
 * Class Login
 * @package app\adminapi\controller\v1\serve
 */
class Login extends AuthController
{

    public function __construct(App $app, ServeServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Gửi mã xác thực
     * @param string $phone
     * @return mixed
     */
    public function captcha(string $phone)
    {
        $this->validate(['phone' => $phone], ServeValidata::class, 'phone');
        return app('json')->success('Gửi thành công', $this->services->user()->code($phone));
    }

    /**
     * Xác thực mã xác thực
     * @param string $phone
     * @param $code
     * @return mixed
     */
    public function checkCode()
    {
        [$phone, $verify_code] = $this->request->postMore([
            ['phone', ''],
            ['verify_code', ''],
        ], true);
        $this->validate(['phone' => $phone], ServeValidata::class, 'phone');
        return app('json')->success('success', $this->services->user()->checkCode($phone, $verify_code));
    }

    /**
     * Đăng ký dịch vụ
     * @param Request $request
     * @param SmsAdminServices $services
     * @return mixed
     */
    public function register(Request $request, SmsAdminServices $services)
    {
        $data = $request->postMore([
            ['phone', ''],
            ['account', ''],
            ['password', ''],
            ['verify_code', ''],
        ]);

        $data['account'] = $data['phone'];
        $this->validate($data, ServeValidata::class);
        $data['password'] = md5($data['password']);
        $res = $this->services->user()->register($data);
        if ($res) {
            $services->updateSmsConfig($data['account'], md5($data['account'] . md5($data['password'])));
            return app('json')->success(400170);
        } else {
            return app('json')->fail(400171);
        }
    }

    /**
     * Đăng nhập nền tảng
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function login(SmsAdminServices $services)
    {
        [$account, $password] = $this->request->postMore([
            ['account', ''],
            ['password', '']
        ], true);

        $this->validate(['account' => $account, 'password' => $password], ServeValidata::class, 'login');

        $password = md5($account . md5($password));

        $res = $this->services->user()->login($account, $password);
        if ($res) {
            CacheService::clear();
            CacheService::set('sms_account', $account);
            $services->updateSmsConfig($account, $password);
            return app('json')->success(400139, $res);
        } else {
            return app('json')->fail(400172);
        }
    }
}
