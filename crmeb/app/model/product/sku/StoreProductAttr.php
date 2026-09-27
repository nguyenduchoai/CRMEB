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
namespace app\model\product\sku;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 *   Model thuộc tính sản phẩm
 * Class StoreProductAttr
 * @package app\common\model\product
 */
class StoreProductAttr extends BaseModel
{
    use ModelTrait;

    /**
     * Tên model
     * @var string
     */
    protected $name = 'store_product_attr';

    /**
     * Getter phân loại
     * @param $value
     * @return false|string[]
     */
    protected function getAttrValuesAttr($value)
    {
        return explode(',', $value);
    }

    /**
     * Setter phân loại
     * @param $value
     * @return string
     */
    protected function setAttrValuesAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    /**
     * Bộ lọc sản phẩm
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchProductIdAttr($query, $value)
    {
        $query->where('product_id', $value);
    }

    /**
     * Bộ lọc loại sản phẩm
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchTypeAttr($query, $value)
    {
        $query->where('type', $value);
    }
}
