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

declare (strict_types=1);

namespace app\services\user;

use app\services\BaseServices;
use app\dao\user\UserUserBrokerageDao;

/**
 * Hoa hồng liên kết với người dùng
 * Class UserUserBrokerageServices
 * @package app\services\user
 */
class UserUserBrokerageServices extends BaseServices
{

    /**
     * UserUserBrokerageServices constructor.
     * @param UserUserBrokerageDao $dao
     */
    public function __construct(UserUserBrokerageDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách hoa hồng
     * @param array $where
     * @param string $field
     * @param string $order
     * @param int $limit
     * @return array
     */
    public function getBrokerageList(array $where, string $field = '*', string $order = '', int $limit = 0)
    {
        if ($limit) {
            [$page] = $this->getPageValue();
        } else {
            [$page, $limit] = $this->getPageValue();
        }
        $list = $this->dao->getList($where, $field, $order, $page, $limit);
        $count = $this->dao->getCount($where);
        return [$count, $list];
    }
}
