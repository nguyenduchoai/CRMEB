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
use app\model\order\OtherOrder;

class OtherOrderDao extends BaseDao
{
    /** Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        // TODO: Implement setModel() method.
        return OtherOrder::class;
    }

    /**
     * Lấy tổng số người dùng đang ở trạng thái thành viên trả phí tại một thời điểm
     * @param $time
     * @param string $channel_type
     * @return int|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPayUserCount(int $time, string $channel_type = '')
    {
        return $this->getModel()->when($channel_type != '', function ($query) use ($channel_type) {
            $query->where('channel_type', $channel_type);
        })->field('distinct(uid),add_time')
            ->group('uid')->having('add_time < ' . $time)
            ->order('add_time desc')
            ->select()->toArray();
    }

    /**
     * Lấy biểu đồ đường VIP
     * @param $time
     * @param $type
     * @param $timeType
     * @return mixed
     */
    public function getTrendData($time, $type, $timeType)
    {
        return $this->getModel()->where('member_type', '<>', 0)->when($type != '', function ($query) use ($type) {
            $query->where('channel_type', $type);
        })->where(function ($query) use ($time) {
            if ($time[0] == $time[1]) {
                $query->whereDay('add_time', $time[0]);
            } else {
                $time[1] = date('Y/m/d', strtotime($time[1]) + 86400);
                $query->whereTime('add_time', 'between', $time);
            }
        })->field("FROM_UNIXTIME(add_time,'$timeType') as days,count(uid) as num")
            ->group('days')->select()->toArray();
    }

    /**Tính tổng giá trị của một trường
     * @param array $where
     * @param string $sumField
     * @return float
     */
    public function getWhereSumField(array $where, string $sumField)
    {
        return $this->search($where)
            ->when(isset($where['timeKey']), function ($query) use ($where) {
                $query->whereBetweenTime('pay_time', $where['timeKey']['start_time'], $where['timeKey']['end_time']);
            })
            ->sum($sumField);
    }

    /**Truy vấn nhóm theo một trường
     * @param array $where
     * @param string $field
     * @param string $group
     * @return mixed
     */
    public function getGroupField(array $where, string $field, string $group)
    {
        return $this->search($where)
            ->when(isset($where['timeKey']), function ($query) use ($where, $field, $group) {
                $query->whereBetweenTime('pay_time', $where['timeKey']['start_time'], $where['timeKey']['end_time']);
                $timeUinx = "%H";
                if ($where['timeKey']['days'] == 1) {
                    $timeUinx = "%H";
                } elseif ($where['timeKey']['days'] == 30) {
                    $timeUinx = "%Y-%m-%d";
                } elseif ($where['timeKey']['days'] == 365) {
                    $timeUinx = "%Y-%m";
                } elseif ($where['timeKey']['days'] > 1 && $where['timeKey']['days'] < 30) {
                    $timeUinx = "%Y-%m-%d";
                } elseif ($where['timeKey']['days'] > 30 && $where['timeKey']['days'] < 365) {
                    $timeUinx = "%Y-%m";
                }
                $query->field("sum($field) as number,FROM_UNIXTIME($group, '$timeUinx') as time");
                $query->group("FROM_UNIXTIME($group, '$timeUinx')");
            })
            ->order('add_time ASC')->select()->toArray();
    }

    /**Lấy một thông tin theo điều kiện
     * @param array $where
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */

    public function getOneByWhere(array $where)
    {
        return $this->getModel()->where($where)->find();
    }

    /**Đơn thu ngân
     * @param array $where
     * @param int $page
     * @param int $limit
     * @param string $order
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getScanOrderList(array $where = [], int $page = 0, int $limit = 0, string $order = '')
    {
        foreach ($where as $k => $v) {
            if ($v == "") unset($where[$k]);
        }
        return $this->search($where)
            ->order(($order ? $order . ' ,' : '') . 'id desc')
            ->page($page, $limit)->select()->toArray();
    }

    /**Lấy lịch sử thành viên
     * @param array $where
     * @param int $page
     * @param int $limit
     * @param string $order
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMemberRecord(array $where = [], int $page = 0, int $limit = 0, string $order = '')
    {
        return $this->search($where)
            ->with(['user'])
            ->order(($order ? $order . ' ,' : '') . 'id desc')
            ->page($page, $limit)->select()->toArray();
    }

    /**
     * Tìm kiếm đơn hàng khác
     * @param array $where
     * @param bool $search
     * @return \crmeb\basic\BaseModel
     * @throws \ReflectionException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/20
     */
    public function search(array $where = [], bool $search = false)
    {
        return parent::search($where, $search)->when(isset($where['name']) && $where['name'], function ($query) use ($where) {
            $query->where('uid', 'in', function ($que) use ($where) {
                $nickname = trim($where['name']);
                $que->name('user')->where('nickname', 'like', $nickname . '%')->field(['uid'])->select();
            });
        });
    }

    /**
     * @param array $where
     * @param bool $search
     * @return int
     * @throws \ReflectionException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/04/11
     */
    public function count(array $where = [], bool $search = true)
    {
        return $this->search($where, $search)->count();
    }
}
