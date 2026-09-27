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

namespace app\services\activity\coupon;


use app\dao\activity\coupon\StoreCouponUserCouponDao;
use app\services\BaseServices;

/**
 * Lấy phiếu giảm giá người dùng có thể sử dụng theo số tiền đặt hàng
 * Class StoreCouponUserCouponServices
 * @package app\services\coupon
 * @method getUidCouponList(int $uid, string $truePrice, int $productId)
 * @method getUidCouponMinList($uid, $price, $value = '', int $type = 1) Lấy phiếu giảm giá trong phạm vi số tiền mua tối thiểu được áp dụng
 */
class StoreCouponUserCouponServices extends BaseServices
{
    /**
     * StoreCouponUserCouponServices constructor.
     * @param StoreCouponUserCouponDao $dao
     */
    public function __construct(StoreCouponUserCouponDao $dao)
    {
        $this->dao = $dao;
    }

}
