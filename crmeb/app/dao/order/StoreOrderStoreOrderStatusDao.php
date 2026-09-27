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

namespace app\dao\order;


use app\dao\BaseDao;
use app\model\order\StoreOrder;
use app\model\order\StoreOrderStatus;

/**
 * Class StoreOrderStoreOrderStatusDao
 * @package app\dao\order
 */
class StoreOrderStoreOrderStatusDao extends BaseDao
{
    protected $alias = 'o';

    protected $joinAlis = 's';

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreOrder::class;
    }

    /**
     * Thiết lập model bảng liên kết
     * @return string
     */
    protected function setJoinModel(): string
    {
        return StoreOrderStatus::class;
    }

    /**
     * Thiết lập model
     * @return \crmeb\basic\BaseModel
     */
    protected function getModel()
    {
        $name = app()->make($this->setJoinModel())->getName();
        return parent::getModel()->join($name . ' ' . $this->joinAlis, $this->joinAlis . '.oid = ' . $this->alias . '.id')->alias($this->alias);
    }

    /**
     * Tìm kiếm
     * @param array $where
     * @return \crmeb\basic\BaseModel|mixed|\think\Model
     */
    public function search(array $where = [], bool $search = false)
    {
        return $this->getModel()->when(isset($where['paid']), function ($query) use ($where) {
            $query->where($this->alias . '.paid', $where['paid']);
        })->when(isset($where['status']), function ($query) use ($where) {
            $query->where($this->alias . '.status', $where['status']);
        })->when(isset($where['refund_status']), function ($query) use ($where) {
            $query->where($this->alias . '.refund_status', $where['refund_status']);
        })->when(isset($where['is_del']), function ($query) use ($where) {
            $query->where($this->alias . '.is_del', $where['is_del']);
        })->when(isset($where['change_type']), function ($query) use ($where) {
            $query->whereIn($this->joinAlis . '.change_type', $where['change_type']);
        })->when(isset($where['change_time']), function ($query) use ($where) {
            $query->where($this->joinAlis . '.change_time', '<', $where['change_time']);
        });
    }

    /**
     * Lấy id đơn hàng đã xác nhận nhận hàng
     * @param array $where
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function getTakeOrderIds(array $where, int $limit = 0)
    {
        return $this->search($where)->whereIn('refund_type', [0, 3])->where('pid', '<>', -1)->field([$this->alias . '.*'])
            ->when($limit != 0, function ($query) use ($limit) {
                $query->limit($limit);
            })->select()->toArray();
    }
}
