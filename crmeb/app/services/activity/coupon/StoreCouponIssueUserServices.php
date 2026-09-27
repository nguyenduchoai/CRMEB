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

namespace app\services\activity\coupon;

use app\services\BaseServices;
use app\dao\activity\coupon\StoreCouponIssueUserDao;

/**
 * Class StoreCouponIssueUserServices
 * @package app\services\coupon
 * @method  getColumn(array $where, string $field, ?string $key = '')
 * @method  delIssueUserCoupon(array $where)
 */
class StoreCouponIssueUserServices extends BaseServices
{

    /**
     * StoreCouponIssueUserServices constructor.
     * @param StoreCouponIssueUserDao $dao
     */
    public function __construct(StoreCouponIssueUserDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @return array
     */
    public function issueLog(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, $page, $limit);
        $site_url = sys_config('site_url');
        foreach ($list as &$item) {
            $item['avatar'] = strpos($item['avatar'], 'http') === false ? ($site_url . $item['avatar']) : $item['avatar'];
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }
}
