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

namespace app\model\product\product;


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * Class StoreProductCoupon
 * @package app\model\product\product
 */
class StoreProductCoupon extends BaseModel
{
    use  ModelTrait;

    /**
     * Khóa chính bảng dữ liệu
     * @var string
     */
    protected $pk = 'id';

    /**
     * Tên model
     * @var string
     */
    protected $name = 'store_product_coupon';


    public function searchProductIdAttr($query, $value)
    {
        if(is_array($value))
            $query->whereIn('product_id',$value);
        else
            $query->where('product_id',$value);
    }

}
