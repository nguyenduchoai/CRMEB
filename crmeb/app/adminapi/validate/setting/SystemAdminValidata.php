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
namespace app\adminapi\validate\setting;

use think\Validate;

class SystemAdminValidata extends Validate
{
    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'account' => ['require', 'alphaDash'],
        'conf_pwd' => 'require',
        'pwd' => 'require',
        'real_name' => 'require',
        'roles' => ['require', 'array'],
    ];

    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [
        'account.require' => '400033',
        'account.alphaDash' => '400034',
        'conf_pwd.require' => '400263',
        'pwd.require' => '400256',
        'real_name.require' => '400035',
        'roles.require' => '400036',
        'roles.array' => '400037',
    ];

    protected $scene = [
        'get' => ['account', 'pwd'],
        'update' => ['account', 'roles'],
    ];


}
