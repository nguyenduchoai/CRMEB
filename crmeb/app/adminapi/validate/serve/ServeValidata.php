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
        'phone.require' => 'Vui lòng điền số điện thoại',
        'phone.number' => 'Số điện thoại bạn nhập phải là số',
        'phone.mobile' => 'Số điện thoại sai định dạng',
        'password.require' => 'Mật khẩu là bắt buộc',
        'verify_code.require' => 'Vui lòng điền mã xác thực',
        'verify_code.number' => 'Mã xác thực SMS phải là số',
        'account.require' => 'Vui lòng điền tài khoản',
    ];

    protected $scene = [
        'login' => ['password', 'account'],
        'phone' => ['phone']
    ];
}
