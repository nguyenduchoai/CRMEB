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

namespace app\api\validate\user;


use think\Validate;

class StoreServiceFeedbackValidate extends Validate
{
    protected $regex = ['phone' => '/^1[3456789]\d{9}$/'];

    protected $rule = [
        'phone' => 'require|regex:phone',
        'rela_name' => 'require',
        'content' => 'require',
    ];

    protected $message = [
        'phone.require' => '410157',
        'phone.regex' => '410158',
        'content.require' => '410163',
        'rela_name.require' => '410155',
    ];
}
