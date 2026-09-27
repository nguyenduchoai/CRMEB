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

namespace app\services\order;


use app\dao\order\StoreOrderStoreOrderCartInfoDao;
use app\services\BaseServices;

/**
 * Class StoreOrderStoreOrderCartInfoServices
 * @package app\services\order
 * @method getUserCartProductIds(array $where) Lấy id sản phẩm người dùng đã mua
 */
class StoreOrderStoreOrderCartInfoServices extends BaseServices
{
    /**
     * StoreOrderStoreOrderCartInfoServices constructor.
     * @param StoreOrderStoreOrderCartInfoDao $dao
     */
    public function __construct(StoreOrderStoreOrderCartInfoDao $dao)
    {
        $this->dao = $dao;
    }

}
