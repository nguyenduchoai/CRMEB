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

/**
 * Lớp xác thực địa chỉ người dùng
 * Class AddressValidate
 * @package app\http\validates\user
 */
class AddressValidate extends Validate
{
    //Di chuyển
    protected $regex = ['phone' => '/^1[3456789]\d{9}|([0-9]{3,4}-)?[0-9]{7,8}$/'];

    protected $rule = [
        'real_name' => 'require|max:25',
        'phone' => 'require|regex:phone',
        'province' => 'require',
        'city' => 'require',
        'district' => 'require',
        'detail' => 'require',
    ];

    protected $message = [
        'real_name.require' => '410155',
        'real_name.max' => '410156',
        'phone.require' => '410157',
        'phone.regex' => '410158',
        'province.require' => '410159',
        'city.require' => '410160',
        'district.require' => '410161',
        'detail.require' => '410162',
    ];
}
