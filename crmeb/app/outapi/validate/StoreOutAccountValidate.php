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
        'appid.require' => '400133',
        'appid.account' => '400255',
        'appsecret.min' => '410011',
        'appsecret.max' => '410011',
        'title.max' => '400761',
    ];

    protected $scene = [
        'save' => ['appid', 'appsecret', 'title'],
        'update' => ['appsecret', 'title'],
    ];
}
