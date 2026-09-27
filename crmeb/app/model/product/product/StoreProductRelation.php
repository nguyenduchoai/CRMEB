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
namespace app\model\product\product;

use crmeb\traits\ModelTrait;
use crmeb\basic\BaseModel;
use think\Model;

/**
 *  Model lượt thích và yêu thích
 * Class StoreProductRelation
 * @package app\model\product\product
 */
class StoreProductRelation extends BaseModel
{
    use ModelTrait;

    /**
     * Tên model
     * @var string
     */
    protected $name = 'store_product_relation';

    /**
     * Liên kết sản phẩm
     */
    public function product()
    {
        return $this->hasOne(StoreProduct::class,'id','product_id');
    }
    /**
     * Bộ lọc người dùng
     * @param Model $query
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        $query->where('uid', $value);
    }

    /**
     * Bộ lọc sản phẩm
     * @param Model $query
     * @param $value
     */
    public function searchProductIdAttr($query, $value)
    {
        $query->where('product_id', $value);
    }

    /**
     * Bộ lọc loại
     * @param Model $query
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        $query->where('type', $value);
    }

    /**
     * Bộ lọc loại sản phẩm
     * @param Model $query
     * @param $value
     */
    public function searchCategoryAttr($query, $value)
    {
        $query->where('category', $value);
    }
}
