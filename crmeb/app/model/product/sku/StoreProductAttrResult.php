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
 * Class StoreProductAttrResult
 * @package app\common\model\product
 */
class StoreProductAttrResult extends BaseModel
{

    use ModelTrait;

    /**
     * Tên model
     * @var string
     */
    protected $name = 'store_product_attr_result';

    protected $insert = ['change_time'];

    /**
     * Tự động thêm thời gian thay đổi
     * @param $value
     * @return int
     */
    protected static function setChangeTimeAttr($value)
    {
        return time();
    }

    /**
     * Chuyển dữ liệu thành JSON
     * @param $value
     * @return false|string
     */
    protected static function setResultAttr($value)
    {
        return is_array($value) ? json_encode($value) : $value;
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
