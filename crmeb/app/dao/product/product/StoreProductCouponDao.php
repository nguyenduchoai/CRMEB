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
declare (strict_types=1);

namespace app\dao\product\product;

use app\dao\BaseDao;
use app\model\product\product\StoreProductCoupon;

/**
 *
 * Class StoreProductCouponDao
 * @package app\dao\coupon
 */
class StoreProductCouponDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreProductCoupon::class;
    }

    /**
     * Lấy phiếu giảm giá liên kết với sản phẩm
     * @param array $product_ids
     * @param string $field
     * @return int|void
     */
    public function getProductCoupon(array $product_ids, string $field = '*')
    {
        return $this->search(['product_id' => $product_ids])->field($field)->select()->toArray();
    }

}
