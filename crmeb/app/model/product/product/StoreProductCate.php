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
use think\Model;

/**
 *  Model liên kết danh mục sản phẩm
 * Class StoreProductCate
 * @package app\model\product\product
 */
class StoreProductCate extends Model
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
    protected $name = 'store_product_cate';

    /**
     * Lấy tên danh mục (liên kết một-một)
     * @return \think\model\relation\HasOne
     */
    public function cateName()
    {
        return $this->hasOne(StoreCategory::class, 'id', 'cate_id')->bind([
            'cate_name' => 'cate_name'
        ]);
    }

}
