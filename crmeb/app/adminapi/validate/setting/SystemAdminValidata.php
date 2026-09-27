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
        'account.require' => 'Vui lòng điền tài khoản quản trị viên',
        'account.alphaDash' => 'Tài khoản quản trị viên phải là chữ cái tiếng Anh',
        'conf_pwd.require' => 'Vui lòng nhập mật khẩu xác nhận',
        'pwd.require' => 'Vui lòng nhập mật khẩu',
        'real_name.require' => 'Vui lòng nhập họ tên quản trị viên',
        'roles.require' => 'Vui lòng chọn vai trò quản trị viên',
        'roles.array' => 'Vai trò phải là mảng',
    ];

    protected $scene = [
        'get' => ['account', 'pwd'],
        'update' => ['account', 'roles'],
    ];


}
