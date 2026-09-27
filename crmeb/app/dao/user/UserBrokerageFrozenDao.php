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

namespace app\dao\user;


use app\dao\BaseDao;
use app\model\user\UserBrokerageFrozen;

/**
 * Đóng băng hoa hồng
 * Class UserBrokerageFrozenDao
 * @package app\dao\user
 */
class UserBrokerageFrozenDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return UserBrokerageFrozen::class;
    }

    /**
     * Tìm kiếm
     * @param array $where
     * @param bool $search
     * @return \crmeb\basic\BaseModel|mixed|\think\Model
     * @throws \ReflectionException
     */
    public function search(array $where = [], bool $search = false)
    {
        return parent::search($where, $search)->when(isset($where['isFrozen']), function ($query) use ($where) {
            if ($where['isFrozen']) {
                $query->where('frozen_time', '>', time());
            } else {
                $query->where('frozen_time', '<=', time());
            }
        });
    }

    /**
     * Lấy hoa hồng bị đóng băng theo một tài khoản
     * @param int $uid
     * @param bool $isFrozen Lấy tổng số tiền trước hoặc sau khi đóng băng
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserFrozenPrice(int $uid, bool $isFrozen = true)
    {
        return $this->search(['uid' => $uid, 'status' => 1, 'isFrozen' => $isFrozen])->column('price', 'id');
    }

    /**
     * Cập nhật trạng thái đóng băng hoa hồng
     * @param string $orderId
     * @return \crmeb\basic\BaseModel
     */
    public function updateFrozen(string $orderId)
    {
        return $this->search(['order_id' => $orderId, 'isFrozen' => true])->update(['status' => 0]);
    }

    /**
     * Lấy mảng hoa hồng bị đóng băng của người dùng
     * @return mixed
     */
    public function getFrozenBrokerage()
    {
        return $this->getModel()->where('frozen_time', '>', time())
            ->where('status', 1)
            ->group('uid')
            ->column('SUM(price) as sum_price', 'uid');
    }

    /**
     * @param $uids
     * @return float
     */
    public function getSumFrozenBrokerage($uids)
    {
        return $this->getModel()->whereIn('uid', $uids)->where('frozen_time', '>', time())->sum('price');
    }
}
