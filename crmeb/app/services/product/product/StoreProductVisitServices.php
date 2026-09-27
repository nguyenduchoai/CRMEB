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


use app\dao\product\product\StoreProductVisitDao;
use app\services\BaseServices;

/**
 * Class StoreProductVisitServices
 * @package app\services\product\product
 * @method getUserVisitProductList(array $where, int $page, int $limit) Lịch sử xem sản phẩm của người dùng
 */
class StoreProductVisitServices extends BaseServices
{

    /**
     * StoreProductVisitServices constructor.
     * @param StoreProductVisitDao $dao
     */
    public function __construct(StoreProductVisitDao $dao)
    {
        $this->dao = $dao;
    }
}
