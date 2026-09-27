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
namespace app\model\order;

use app\model\product\product\StoreProduct;
use app\model\product\sku\StoreProductAttrValue;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Model giỏ hàng
 * Class StoreCart
 * @package app\model\order
 */
class StoreCart extends BaseModel
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
    protected $name = 'store_cart';

    /**
     * Tự động thêm trường
     * @var string[]
     */
    protected $insert = ['add_time'];

    /**
     * Setter thời gian thêm
     * @return int
     */
    protected function setAddTimeAttr()
    {
        return time();
    }

    /**
     * Liên kết một-một
     * Giỏ hàng liên kết chi tiết sản phẩm
     * @return \think\model\relation\HasOne
     */
    public function productInfo()
    {
        return $this->hasOne(StoreProduct::class, 'id', 'product_id');
    }

    /**
     * Liên kết một-một
     * Giỏ hàng liên kết phân loại sản phẩm
     * @return \think\model\relation\HasOne
     */
    public function attrInfo()
    {
        return $this->hasOne(StoreProductAttrValue::class, 'unique', 'product_attr_unique');
    }


    /**
     * Bộ lọc loại
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchTypeAttr($query, $value, $data)
    {
        $query->where('type', $value);
    }

    /**
     * Đã thanh toán
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsPayAttr($query, $value, $data)
    {
        $query->where('is_pay', $value);
    }

    /**
     * Đã xóa
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsDelAttr($query, $value, $data)
    {
        $query->where('is_del', $value);
    }

    /**
     * Có thanh toán ngay hay không
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsNewAttr($query, $value, $data)
    {
        $query->where('is_new', $value);
    }

    /**
     * Tra cứu giỏ hàng người dùng
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchUidAttr($query, $value, $data)
    {
        $query->where('uid', $value);
    }

    /**
     * Bộ lọc ID sản phẩm
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchProductIdAttr($query, $value, $data)
    {
        if (is_array($value)) {
            $query->whereIn('product_id', $value);
        } else {
            $query->where('product_id', $value);
        }
    }

    /**
     * Bộ lọc giá trị duy nhất của phân loại sản phẩm
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchProductAttrUniqueAttr($query, $value, $data)
    {
        $query->where('product_attr_unique', $value);
    }

    /**
     * Bộ lọc ID mua chung
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchCombinationIdAttr($query, $value, $data)
    {
        $query->where('combination_id', $value);
    }

    /**
     * Bộ lọc ID săn giảm giá
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchBargainIdAttr($query, $value, $data)
    {
        $query->where('bargain_id', $value);
    }

    /**
     * Bộ lọc ID flash sale
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchSeckillIdAttr($query, $value, $data)
    {
        $query->where('seckill_id', $value);
    }

    /**
     * Liên kết một-nhiều
     * Sản phẩm liên kết id mẫu phiếu giảm giá
     * @return \think\model\relation\HasMany
     */
    public function product()
    {
        return $this->hasMany(StoreProduct::class, 'id', 'product_id');

    }
}
