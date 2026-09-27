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

namespace app\dao\activity\coupon;

use app\dao\BaseDao;
use app\model\activity\coupon\StoreCouponIssueUser;

/**
 *
 * Class StoreCouponIssueUserDao
 * @package app\dao\coupon
 */
class StoreCouponIssueUserDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreCouponIssueUser::class;
    }

    /**
     * Lấy danh sách phiếu giảm giá đã nhận
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, int $page, int $limit)
    {
        return $this->search($where)->with('userInfo')->page($page, $limit)->select()->toArray();
    }

    /**
     * Xóa phiếu giảm giá người dùng đã nhận
     * @param $where
     * @return bool
     */
    public function delIssueUserCoupon($where)
    {
        return $this->getModel()->where($where)->delete();
    }

    public function getIssueUserCount($uid, $coupon_id)
    {
        return $this->getModel()->where('uid', $uid)->where('issue_coupon_id', $coupon_id)->count();
    }
}
