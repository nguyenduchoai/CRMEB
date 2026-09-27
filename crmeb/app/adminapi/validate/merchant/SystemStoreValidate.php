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
namespace app\adminapi\validate\merchant;

use think\Validate;

class SystemStoreValidate extends Validate
{
    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require',
        'introduction' => 'require',
        'phone' => 'require',
        'address' => 'require',
        'image' => 'require',
        'oblong_image' => 'require',
        'detailed_address' => 'require',
        'latlng' => 'require',
        'day_time' => 'require',
    ];
    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '400378',
        'introduction.require' => '400379',
        'phone.require' => '400380',
        'image.require' => '400381',
        'oblong_image.require' => '400382',
        'address.require' => '400383',
        'detailed_address.require' => '400384',
        'latlng.require' => '400385',
        'day_time.require' => '400386',
    ];

    protected $scene = [
        'save' => ['name', 'phone', 'address', 'detailed_address', 'latlng', 'day_time', 'image', 'oblong_image'],
    ];
}
