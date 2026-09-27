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

namespace app\services\order;


use app\dao\order\OtherOrderStatusDao;
use app\services\BaseServices;

/**
 * Class OtherOrderStatusServices
 * @package app\services\order
 */
class OtherOrderStatusServices extends BaseServices
{

    /**
     * OtherOrderStatusServices constructor.
     * @param OtherOrderStatusDao $dao
     */
    public function __construct(OtherOrderStatusDao $dao)
    {
        $this->dao = $dao;
    }
}
