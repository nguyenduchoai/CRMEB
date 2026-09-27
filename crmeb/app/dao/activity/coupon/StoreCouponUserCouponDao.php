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

namespace app\dao\activity\coupon;


use app\dao\BaseDao;
use app\model\activity\coupon\StoreCoupon;
use app\model\activity\coupon\StoreCouponUser;

/**
 * Class StoreCouponUserCouponDao
 * @package app\dao\coupon
 */
class StoreCouponUserCouponDao extends BaseDao
{
    /**
     * Alias bảng chính
     * @var string
     */
    protected $alias = 'a';

    /**
     * Alias bảng liên kết
     * @var string
     */
    protected $joinAlis = 'b';

    /**
     * Model bảng chính
     * @return string
     */
    public function setModel(): string
    {
        return StoreCouponUser::class;
    }

    /**
     * Tên bảng liên kết
     * @return string
     */
    public function setJoinModel(): string
    {
        return StoreCoupon::class;
    }

    /**
     * Thiết lập model
     * @return \crmeb\basic\BaseModel
     */
    public function getModel()
    {
        /** @var StoreCoupon $joinModel */
        $joinModel = app()->make($this->setJoinModel());
        $name = $joinModel->getName();
        return parent::getModel()->alias($this->alias)->join($name . ' ' . $this->joinAlis, $this->joinAlis . '.id=' . $this->alias . '.cid');
    }

    /**
     * Lấy phiếu giảm giá người dùng có thể sử dụng theo số tiền đặt hàng
     * @param int $uid
     * @param string $truePrice
     * @param int $productId
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUidCouponList(int $uid, string $truePrice, int $productId)
    {
        return $this->getModel()
            ->where($this->alias . '.uid', $uid)
            ->where($this->alias . '.is_fail', 0)
            ->where($this->alias . '.status', 0)
            ->where($this->alias . '.use_min_price', '<=', $truePrice)
            ->whereFindinSet($this->joinAlis . '.product_id', $productId)
            ->where($this->joinAlis . '.type', 2)
            ->field($this->alias . '.*,' . $this->joinAlis . '.type')
            ->order($this->alias . '.coupon_price', 'DESC')
            ->select()
            ->hidden(['status', 'is_fail'])
            ->toArray();
    }

    /**
     * Lấy phiếu giảm giá trong phạm vi số tiền mua tối thiểu được áp dụng
     * @param $uid
     * @param $price
     * @param $value
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUidCouponMinList($uid, $price, $value = '', int $type = 1)
    {
        return $this->getModel()->where($this->alias . '.uid', $uid)
            ->where($this->alias . '.is_fail', 0)
            ->where($this->alias . '.status', 0)
            ->where($this->alias . '.use_min_price', '<=', $price)
            ->when($value, function ($query) use ($value) {
                $query->whereFindinSet($this->joinAlis . '.category_id', $value);
            })
            ->where($this->joinAlis . '.type', $type)
            ->field($this->alias . '.*,' . $this->joinAlis . '.type')
            ->order($this->alias . '.coupon_price', 'DESC')
            ->select()
            ->hidden(['status', 'is_fail'])
            ->toArray();
    }
}
