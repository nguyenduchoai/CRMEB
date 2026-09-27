<?php

namespace app\model\product\product;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * Model danh mục nhãn sản phẩm
 * @author wuhaotian
 * @email 442384644@qq.com
 * @date 2024/12/19
 */
class StoreProductLabelCate extends BaseModel
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
    protected $name = 'store_product_label_cate';
}
