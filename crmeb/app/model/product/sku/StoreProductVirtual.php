<?php


namespace app\model\product\sku;


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class StoreProductVirtual extends BaseModel
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
    protected $name = 'store_product_virtual';

    /**
     * Bộ lọc số thẻ
     * @param $query
     * @param $value
     */
    public function searchCardNoAttr($query, $value)
    {
        $query->where('card_no', $value);
    }

    /**
     * Bộ lọc mã thẻ
     * @param $query
     * @param $value
     */
    public function searchCardPwdAttr($query, $value)
    {
        $query->where('card_pwd', $value);
    }

    /**
     * Bộ lọc sản phẩm
     * @param $query
     * @param $value
     */
    public function searchProductIdAttr($query, $value)
    {
        $query->where('product_id', $value);
    }

    /**
     * Bộ lọc người dùng
     * @param $query
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        $query->where('uid', $value);
    }

    /**
     * Bộ lọc đơn hàng
     * @param $query
     * @param $value
     */
    public function searchOrderIdAttr($query, $value)
    {
        $query->where('order_id', $value);
    }
}
