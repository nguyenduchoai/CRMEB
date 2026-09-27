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

namespace app\services\product\product;


use app\dao\product\product\StoreProductCateDao;
use app\services\BaseServices;

/**
 * Class StoreProductCateService
 * @package app\services\product\product
 * @method productIdByCateId(array $productId) Lấy id danh mục theo id sản phẩm
 * @method cateIdByProduct(array $cate_id) Lấy id sản phẩm theo danh mục
 */
class StoreProductCateServices extends BaseServices
{
    public function __construct(StoreProductCateDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Thêm sửa liên kết danh mục khi thêm sửa sản phẩm
     * @param $id
     * @param $cateData
     */
    public function change($id, $cateData)
    {
        $this->dao->delete(['product_id' => $id]);
        $this->dao->saveAll($cateData);
    }


}
