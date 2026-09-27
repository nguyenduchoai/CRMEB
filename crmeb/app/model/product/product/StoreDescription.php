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

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 *  Model chi tiết sản phẩm
 * Class StoreDescription
 * @package app\model\product\product
 */
class StoreDescription extends BaseModel
{
    /**
     * Tên model
     * @var string
     */
    protected $name = 'store_product_description';

    use ModelTrait;

    public function getDescriptionAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * Bộ lọc ID sản phẩm
     * @param $query
     * @param $value
     */
    public function searchProductIdAttr($query, $value)
    {
        if ($value) $query->where('product_id', $value);
    }

    /**
     * Bộ lọc loại
     * @param $query
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        $query->where('type', $value);
    }
}
