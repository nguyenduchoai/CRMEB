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

namespace app\dao\activity\coupon;

use app\dao\BaseDao;
use app\model\activity\coupon\StoreCouponUser;

/**
 *
 * Class StoreCouponUserDao
 * @package app\dao\coupon
 */
class StoreCouponUserDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreCouponUser::class;
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, string $field = '*', array $with = ['issue'], int $page, int $limit)
    {
        return $this->search($where)->field($field)->with($with)->page($page, $limit)->order('id desc')->select()->toArray();
    }

    /**
     * Sử dụng phiếu giảm giá và cập nhật trạng thái phiếu giảm giá
     * @param $id
     * @return \think\Model|null
     */
    public function useCoupon(int $id)
    {
        return $this->getModel()->where('id', $id)->update(['status' => 1, 'use_time' => time()]);
    }

    /**
     * Lấy phiếu giảm giá theo id sản phẩm chỉ định
     * @param array $productIds
     * @param int $uid
     * @param string $price
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function productIdsByCoupon(array $productIds, int $uid, string $price)
    {
        return $this->getModel()->whereIn('cid', function ($query) use ($productIds) {
            $query->name('store_coupon_issue')->whereIn('id', function ($q) use ($productIds) {
                $q->name('store_coupon_product')->whereIn('product_id', $productIds)->field('coupon_id')->select();
            })->field(['id'])->select();
        })->with('issue')->where(['uid' => $uid, 'status' => 0])->order('coupon_price DESC')
            ->where('use_min_price', '<=', $price)->select()
            ->where('start_time', '<', time())->where('end_time', '>', time())
            ->hidden(['status', 'is_fail'])->toArray();
    }

    /**
     * Lấy theo id sản phẩm
     * @param array $cateIds
     * @param int $uid
     * @param string $price
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function cateIdsByCoupon(array $cateIds, int $uid, string $price)
    {
        return $this->getModel()->whereIn('cid', function ($query) use ($cateIds) {
            $query->name('store_coupon_issue')->whereIn('category_id', $cateIds)->where('type', 1)->field('id')->select();
        })->where(['uid' => $uid, 'status' => 0])->where('use_min_price', '<=', $price)
            ->where('start_time', '<', time())->where('end_time', '>', time())
            ->order('coupon_price DESC')->with('issue')->select()->hidden(['status', 'is_fail'])->toArray();
    }

    /**
     * Lấy phiếu giảm giá khả dụng của người dùng hiện tại
     * @param array $ids
     * @param int $uid
     * @param string $price
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserCoupon(array $ids, int $uid, string $price)
    {
        return $this->getModel()->where(['uid' => $uid, 'status' => 0])->when(count($ids) != 0, function ($query) use ($ids) {
            $query->whereNotIn('id', $ids);
        })->whereIn('cid', function ($query) {
            $query->name('store_coupon_issue')->where('type', 0)->field(['id'])->select();
        })->where('use_min_price', '<=', $price)
            ->where('start_time', '<', time())->where('end_time', '>', time())
            ->order('coupon_price DESC')->with('issue')->select()->hidden(['status', 'is_fail'])->toArray();
    }

    /**
     * Lấy tất cả phiếu giảm giá khả dụng của người dùng hiện tại
     * @param int $uid
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserAllCoupon(int $uid)
    {
        return $this->getModel()->where(['uid' => $uid, 'status' => 0, 'is_fail' => 0])
            ->where('start_time', '<', time())->where('end_time', '>', time())
            ->order('coupon_price DESC')->with('issue')->select()->hidden(['status', 'is_fail'])->toArray();
    }

    /**
     * Lấy danh sách kèm sắp xếp
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCouponListByOrder(array $where, $order, int $page = 0, int $limit = 0)
    {
        return $this->search($where, false)->with('issue')->when($page > 0 && $limit > 0, function ($qeury) use ($page, $limit) {
            $qeury->page($page, $limit);
        })->when($order != '', function ($query) use ($order) {
            $query->order($order);
        })->when(isset($where['coupon_ids']), function ($qeury) use ($where) {
            $qeury->whereIn('cid', $where['coupon_ids']);
        })->when(isset($where['status']), function ($qeury) use ($where) {
            if ($where['status'] == 1) {
                $qeury->where(function ($query) {
                    $query->where('status', 1)->whereOr('status', 2);
                });
            } else {
                $qeury->where('status', $where['status']);
            }
        })->select()->toArray();
    }

    /**
     * Tra cứu phiếu giảm giá người dùng nhận được theo tháng
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function memberCouponUserGroupBymonth(array $where)
    {
        return $this->search($where, false)
            ->whereMonth('add_time')
            ->whereIn('cid', $where['couponIds'])
            ->field('count(id) as num,FROM_UNIXTIME(add_time, \'%Y-%m\') as time')
            ->group("FROM_UNIXTIME(add_time, '%Y-%m')")
            ->select()->toArray();
    }

    /**
     * Tra cứu theo thời gian
     * @param array $where
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserCounponByMonth(array $where, string $field = '*')
    {
        return $this->search($where)->field($field)->whereMonth('add_time')->select()->toArray();
    }

    /**
     * Lấy phiếu giảm giá đã nhận trong tháng này
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getVipCouponList($uid)
    {
        return $this->getModel()->where('uid', $uid)->whereMonth('add_time')->select()->toArray();
    }

    /**
     * Xóa phiếu giảm giá người dùng đã nhận
     * @param $where
     * @return bool
     */
    public function delUserCoupon($where)
    {
        return $this->getModel()->where($where)->delete();
    }

    /**
     * Kiểm tra người dùng còn có thể nhận hay đã nhận nhưng chưa sử dụng
     * @param $uid
     * @param $coupon_id
     * @param $receive_limit
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/7/15
     */
    public function getUserCouponCanUse($uid, $coupon_id, $receive_limit)
    {
        $list = $this->getModel()->where(['uid' => $uid, 'cid' => $coupon_id])->select()->toArray();
        $count = count($list);
        if ($count < $receive_limit) {
            return true;
        }
        $noUserCount = 0;
        foreach ($list as $item) {
            if ($item['status'] == 'Chưa sử dụng') {
                $noUserCount++;
            }
        }
        if ($noUserCount > 0) {
            return true;
        } else {
            return false;
        }
    }
}
