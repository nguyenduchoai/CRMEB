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
namespace app\adminapi\controller\v1\notification\sms;

use app\adminapi\controller\AuthController;
use app\services\yihaotong\SmsAdminServices;
use think\facade\App;

/**
 * Tài khoản SMS
 * Class SmsAdmin
 * @package app\adminapi\controller\v1\sms
 */
class SmsAdmin extends AuthController
{
    /**
     * Phương thức khởi tạo
     * SmsAdmin constructor.
     * @param App $app
     * @param SmsAdminServices $services
     */
    public function __construct(App $app, SmsAdminServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Gửi mã xác thực
     * @return mixed
     */
    public function captcha()
    {
        if (!request()->isPost()) {
            return app('json')->fail('Gửi thất bại');
        }
        $phone = request()->param('phone');
        if (!trim($phone)) {
            return app('json')->fail('Vui lòng điền số điện thoại');
        }
        return app('json')->success($this->services->captcha($phone));
    }

    /**
     * Sửa/đăng ký tài khoản nền tảng SMS
     * @return mixed
     */
    public function save()
    {
        [$account, $password, $phone, $code, $url, $sign] = $this->request->postMore([
            ['account', ''],
            ['password', ''],
            ['phone', ''],
            ['code', ''],
            ['url', ''],
            ['sign', ''],
        ], true);
        $signLen = mb_strlen(trim($sign));
        if (!strlen(trim($account))) return app('json')->fail('Vui lòng điền tài khoản');
        if (!strlen(trim($password))) return app('json')->fail('Vui lòng điền mật khẩu');
        if (!$signLen) return app('json')->fail('Vui lòng điền chữ ký SMS');
        if ($signLen > 8) return app('json')->fail('Chữ ký SMS tối đa 8 ký tự');
        if (!strlen(trim($code))) return app('json')->fail('Vui lòng điền mã xác thực');
        if (!strlen(trim($url))) return app('json')->fail('Vui lòng điền tên miền');
        $status = $this->services->register($account, $password, $url, $phone, $code, $sign);
        return app('json')->success($status['msg']);
    }
}
