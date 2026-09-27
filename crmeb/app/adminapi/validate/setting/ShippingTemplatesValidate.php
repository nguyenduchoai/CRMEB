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

class ShippingTemplatesValidate extends Validate
{
    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require',
        'region_info' => 'array',
        'appoint_info' => 'array',
        'no_delivery_info' => 'array',
        'type' => 'number',
        'appoint' => 'number',
        'no_delivery' => 'number',
        'sort' => 'number'
    ];

    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '400025',
        'region_info.array' => '400026',
        'appoint_info.array' => '400027',
        'no_delivery_info.array' => '400028',
        'type.number' => '400029',
        'appoint.number' => '400030',
        'no_delivery.number' => '400031',
        'sort.number' => '400032',
    ];

    protected $scene = [
        'save' => ['name', 'type', 'appoint', 'sort', 'region_info', 'appoint_info', 'no_delivery_info', 'no_delivery'],
    ];
}
