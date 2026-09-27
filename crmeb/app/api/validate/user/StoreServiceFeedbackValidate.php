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

class StoreServiceFeedbackValidate extends Validate
{
    protected $regex = ['phone' => '/^1[3456789]\d{9}$/'];

    protected $rule = [
        'phone' => 'require|regex:phone',
        'rela_name' => 'require',
        'content' => 'require',
    ];

    protected $message = [
        'phone.require' => 'Số điện thoại là bắt buộc',
        'phone.regex' => 'Số điện thoại sai định dạng',
        'content.require' => 'Vui lòng nhập nội dung phản hồi',
        'rela_name.require' => 'Tên là bắt buộc',
    ];
}
