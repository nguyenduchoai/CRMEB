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

namespace app\dao\product\product;

use app\dao\BaseDao;
use app\model\product\product\StoreProductCate;

/**
 * Class StoreProductCateDao
 * @package app\dao\product\product
 */
class StoreProductCateDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreProductCate::class;
    }

    /**
     * Lưu dữ liệu
     * @param array $data
     * @return mixed|void
     */
    public function saveAll(array $data)
    {
        $this->getModel()->insertAll($data);
    }

    /**
     * Lấy id danh mục theo id sản phẩm
     * @param array $productId
     * @return array
     */
    public function productIdByCateId(array $productId)
    {
        return $this->getModel()->whereIn('product_id', $productId)->column('cate_id');
    }

    /**
     * Lấy id sản phẩm theo danh mục
     * @param array $cate_id
     * @return array
     */
    public function cateIdByProduct(array $cate_id)
    {
        return $this->getModel()->whereIn('cate_id', $cate_id)->column('product_id');
    }
}
