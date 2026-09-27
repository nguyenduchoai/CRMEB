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
        'order_id.require'      => '400389',
        'order_id.length'       => '400390',
        'order_id.alphaNum'     => '400391',
        'total_price.require'   => '400392',
        'total_price.float'    => '400393',
        'pay_price.require'     => '400394',
        'pay_price.float'      => '400395',
        'pay_postage.require'   => '400396',
        'pay_postage.float'    => '400397',
        'gain_integral.float'  => '400398',
    ];

    protected $scene = [

    ];
}
