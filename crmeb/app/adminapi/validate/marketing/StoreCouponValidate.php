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
namespace app\adminapi\validate\marketing;

use think\Validate;

class StoreCouponValidate extends Validate
{
    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'title' => 'require',
        'image' => 'require',
        'category_id' => 'require',
        'coupon_price' => 'require',
        'use_min_price' => 'require',
        'coupon_time' => 'require',
        'status' => 'In:0,1',
        'type' => ['require', 'In:0,1,2'],
    ];

    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [
        'title.require' => '400372',
        'image.require' => '400337',
        'category_id.require' => '400373',
        'coupon_price.require' => '400374',
        'use_min_price.require' => '400375',
        'coupon_time.require' => '400376',
    ];

    protected $scene = [
        'save' => ['title', 'coupon_price', 'use_min_price', 'coupon_time'],
        'type' => ['title', 'category_id', 'coupon_price', 'use_min_price', 'coupon_time'],
        'product' => ['title', 'image', 'coupon_price', 'use_min_price', 'coupon_time'],
    ];
}
