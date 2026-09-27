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
namespace app\api\validate\user;


use think\Validate;

/**
 * Xác thực đăng ký
 * Class RegisterValidates
 * @package app\http\validates\user
 */
class RegisterValidates extends Validate
{
    protected $regex = ['phone' => '/^1[3456789]\d{9}$/'];

    protected $rule = [
        'phone' => 'require|regex:phone',
        'account' => 'require|regex:phone',
        'captcha' => 'require|length:6',
        'password' => 'require',
    ];

    protected $message = [
        'phone.require' => 'Vui lòng nhập số điện thoại',
        'phone.regex' => 'Số điện thoại không đúng định dạng',
        'account.require' => 'Vui lòng nhập số điện thoại',
        'account.regex' => 'Số điện thoại không đúng định dạng',
        'captcha.require' => 'Vui lòng nhập mã xác thực',
        'captcha.length' => 'Mã xác thực không đúng',
        'password.require' => 'Mật khẩu phải dài từ 6 đến 16 ký tự',
    ];


    public function sceneCode()
    {
        return $this->only(['phone']);
    }


    public function sceneRegister()
    {
        return $this->only(['account', 'captcha', 'password']);
    }
}
