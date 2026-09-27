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

namespace app\adminapi\validate\serve;


use think\Validate;

class ServeValidata extends Validate
{
    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'phone' => 'require|number|mobile',
        'password' => 'require',
        'verify_code' => 'require|number',
        'account' => 'require',
    ];

    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [
        'phone.require' => '400333',
        'phone.number' => '400019',
        'phone.mobile' => '400252',
        'password.require' => '400020',
        'verify_code.require' => '400137',
        'verify_code.number' => '400021',
        'account.require' => '400133',
    ];

    protected $scene = [
        'login' => ['password', 'account'],
        'phone' => ['phone']
    ];
}
