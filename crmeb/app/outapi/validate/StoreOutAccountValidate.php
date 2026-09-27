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

namespace app\outapi\validate;

use think\Validate;

class StoreOutAccountValidate extends Validate
{
    protected $regex = ['account' => '/^[a-zA-Z0-9]{4,30}$/'];

    /**
     * @var string[]
     */
    protected $rule = [
        'appid' => 'require|account',
        'appsecret' => 'min:6|max:32',
        'title' => 'max:120'
    ];

    /**
     * @var string[]
     */
    protected $message = [
        'appid.require' => 'Vui lòng điền tài khoản',
        'appid.account' => 'Tài khoản phải gồm 4-30 ký tự chữ số hoặc chữ cái',
        'appsecret.min' => 'Mật khẩu phải dài từ 6 đến 16 ký tự',
        'appsecret.max' => 'Mật khẩu phải dài từ 6 đến 16 ký tự',
        'title.max' => 'Nội dung mô tả không được vượt quá 120 ký tự',
    ];

    protected $scene = [
        'save' => ['appid', 'appsecret', 'title'],
        'update' => ['appsecret', 'title'],
    ];
}
