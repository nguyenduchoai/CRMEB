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

namespace app\model\activity\coupon;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * TODO Model mẫu phiếu giảm giá
 * Class StoreCoupon
 * @package app\model\coupon
 */
class StoreCoupon extends BaseModel
{
    use ModelTrait;

    /**
     * Khóa chính bảng dữ liệu
     * @var string
     */
    protected $pk = 'id';

    /**
     * Tên model
     * @var string
     */
    protected $name = 'store_coupon';

    /**
     * Loại phiếu giảm giá
     * @var string[]
     */
    protected $couponType = [0 => 'Phiếu toàn cửa hàng', 1 => 'Phiếu theo danh mục', 2 => 'Phiếu theo sản phẩm'];

    /**
     * Liên kết một-nhiều
     * @return \think\model\relation\HasMany
     */
    public function productId()
    {
        return $this->hasMany(StoreCouponProduct::class, 'coupon_id', 'id');
    }

    /**
     * Getter loại phiếu giảm giá
     * @param $value
     * @return string
     */
    public function getTypeAttr($value)
    {
        return $this->couponType[$value];
    }

    /**
     * Bộ lọc tiêu đề mẫu phiếu giảm giá
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchTitleAttr($query, $value, $data)
    {
        if ($value) $query->where('title', 'like', '%' . $value . '%');
    }

    /**
     * Bộ lọc trạng thái mẫu phiếu giảm giá
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchStatusAttr($query, $value, $data)
    {
        if ($value != '') $query->where('status', $value);
    }

    /**
     * Bộ lọc số tiền tiêu tối thiểu
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchUseMinPriceAttr($query, $value, $data)
    {
        $query->where('use_min_price', $value);
    }

    /**
     * Bộ lọc giá trị phiếu giảm giá
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchCouponPriceAttr($query, $value, $data)
    {
        $query->where('coupon_price', $value);
    }

    /**
     * Bộ lọc đã xóa hay chưa
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsDelAttr($query, $value, $data)
    {
        $query->where('is_del', $value ?? 0);
    }

    /**
     * Bộ lọc loại phiếu giảm giá
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchTypeAttr($query, $value, $data)
    {
        $query->where('type', $value ?? 0);
    }

    /**
     * Bộ lọc ID danh mục
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchCategoryIdAttr($query, $value, $data)
    {
        $query->where('category_id', $value);
    }
}
