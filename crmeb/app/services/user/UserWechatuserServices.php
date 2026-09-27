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
declare (strict_types=1);

namespace app\services\user;

use app\services\BaseServices;
use app\dao\user\UserWechatUserDao;

/**
 *
 * Class UserWechatuserServices
 * @package app\services\user
 */
class UserWechatuserServices extends BaseServices
{

    /**
     * UserWechatuserServices constructor.
     * @param UserWechatUserDao $dao
     */
    public function __construct(UserWechatUserDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Truy vấn tổng số tùy chỉnh đơn giản
     * @param array $where
     * @return int
     */
    public function getCount(array $where): int
    {
        return $this->dao->getCount($where);
    }

    /**
     * Danh sách tìm kiếm theo điều kiện phức tạp
     * @param array $where
     * @param string $field
     * @return array
     */
    public function getWhereUserList(array $where, string $field): array
    {
        [$page, $limit] = $this->getPageValue();
        $order_string = '';
        $order_arr = ['ascending', 'descending'];
        if (isset($where['now_money']) && in_array($where['now_money'], $order_arr)) {
            $order_string = $where['now_money'] == 'ascending' ? 'now_money asc' : 'now_money desc';
        }
        $list = $this->dao->getListByModel($where, $field, $order_string, $page, $limit);
        $count = $this->dao->getCountByWhere($where);
        return [$list, $count];
    }
}
