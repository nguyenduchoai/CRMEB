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
        'title.require' => 'Vui lòng nhập tên phiếu giảm giá',
        'image.require' => 'Vui lòng chọn sản phẩm',
        'category_id.require' => 'Vui lòng chọn danh mục sản phẩm',
        'coupon_price.require' => 'Vui lòng nhập mệnh giá phiếu giảm giá',
        'use_min_price.require' => 'Vui lòng nhập số tiền tối thiểu để dùng phiếu giảm giá',
        'coupon_time.require' => 'Vui lòng nhập thời hạn hiệu lực của phiếu giảm giá',
    ];

    protected $scene = [
        'save' => ['title', 'coupon_price', 'use_min_price', 'coupon_time'],
        'type' => ['title', 'category_id', 'coupon_price', 'use_min_price', 'coupon_time'],
        'product' => ['title', 'image', 'coupon_price', 'use_min_price', 'coupon_time'],
    ];
}
