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
namespace app\adminapi\validate\order;

use think\Validate;

/**
 *
 * Class StoreOrderValidate
 * @package app\adminapi\validates
 */
class StoreOrderValidate extends Validate
{

    protected $rule = [
        'order_id'      => ['require','length'=>'1,32','alphaNum'],
        'total_price'   => ['require','float'],
        'total_postage' => ['require','float'],
        'pay_price'     => ['require','float'],
        'pay_postage'   => ['require','float'],
        'gain_integral' => ['float'],
    ];

    protected $message = [
        'order_id.require'      => 'Mã đơn hàng là bắt buộc',
        'order_id.length'       => 'Mã đơn hàng không đúng',
        'order_id.alphaNum'     => 'Mã đơn hàng phải gồm chữ cái và chữ số',
        'total_price.require'   => 'Số tiền đơn hàng là bắt buộc',
        'total_price.float'    => 'Số tiền đơn hàng phải là số',
        'pay_price.require'     => 'Số tiền đơn hàng là bắt buộc',
        'pay_price.float'      => 'Số tiền đơn hàng phải là số',
        'pay_postage.require'   => 'Phí vận chuyển của đơn hàng là bắt buộc',
        'pay_postage.float'    => 'Phí vận chuyển của đơn hàng phải là số',
        'gain_integral.float'  => 'Điểm thưởng tặng kèm phải là số',
    ];

    protected $scene = [

    ];
}
