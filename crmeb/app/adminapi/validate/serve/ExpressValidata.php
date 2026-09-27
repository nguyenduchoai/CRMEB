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


class ExpressValidata extends Validate
{
    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'com' => 'require',
        'temp_id' => 'require',
        'to_name' => 'require',
        'to_tel' => 'require|mobile',
        'to_address' => 'require',
        'siid' => 'require',
    ];

    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [
        'com.require' => 'Vui lòng chọn đơn vị vận chuyển',
        'temp_id.number' => 'Vui lòng chọn mẫu phí vận chuyển',
        'to_name.require' => 'Vui lòng điền họ tên người gửi',
        'to_tel.require' => 'Vui lòng nhập số điện thoại người gửi',
        'to_tel.mobile' => 'Số điện thoại người gửi không đúng',
        'to_address.require' => 'Vui lòng điền địa chỉ chi tiết của người gửi',
        'siid.require' => 'Vui lòng điền mã máy in đám mây',
    ];
}
