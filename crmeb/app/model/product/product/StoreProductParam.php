<?php

namespace app\model\product\product;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * Model tham số sản phẩm
 * @author wuhaotian
 * @email 442384644@qq.com
 * @date 2024/12/17
 */
class StoreProductParam extends BaseModel
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
    protected $name = 'store_product_param';
}
