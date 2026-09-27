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

namespace app\kefuapi\validate;


use think\Validate;

class LoginValidate extends Validate
{
    protected $regex = ['account' => '/^[a-zA-Z0-9]{4,30}$/'];
    /**
     * @var string[]
     */
    protected $rule = [
        'account' => 'require|account',
        'password' => 'require',
    ];

    /**
     * @var string[]
     */
    protected $message = [
        'account.require' => 'Vui lòng nhập tài khoản và mật khẩu',
        'account.account' => 'Vui lòng nhập tài khoản và mật khẩu',
        'password.regex' => 'Vui lòng nhập tài khoản và mật khẩu',
    ];
}
