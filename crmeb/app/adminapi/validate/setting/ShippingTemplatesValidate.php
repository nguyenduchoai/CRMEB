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
        'name.require' => 'Vui lòng điền tên mẫu phí vận chuyển',
        'region_info.array' => 'Thông tin phí vận chuyển phải là mảng',
        'appoint_info.array' => 'Thông tin miễn phí vận chuyển phải là mảng',
        'no_delivery_info.array' => 'Thông tin khu vực không giao hàng phải là mảng',
        'type.number' => 'Dữ liệu type sai định dạng, phải là 1, 2 hoặc 3',
        'appoint.number' => 'Dữ liệu appoint sai định dạng, phải là 0 hoặc 1',
        'no_delivery.number' => 'Dữ liệu no_delivery sai định dạng, phải là 0 hoặc 1',
        'sort.number' => 'Dữ liệu sort sai định dạng, phải là số nguyên',
    ];

    protected $scene = [
        'save' => ['name', 'type', 'appoint', 'sort', 'region_info', 'appoint_info', 'no_delivery_info', 'no_delivery'],
    ];
}
