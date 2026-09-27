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

namespace app\dao\user;

use app\dao\BaseDao;
use app\model\user\User;

/**
 * Người dùng
 * Class UserDao
 * @package app\dao\user
 */
class UserDao extends BaseDao
{

    protected function setModel(): string
    {
        return User::class;
    }

    /**
     * Lấy danh sách người dùng
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, string $field = '*', int $page = 0, int $limit = 0): array
    {
        return $this->search($where)->field($field)->with(['label'])->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->select()->toArray();
    }

    /**
     * Lấy tổng số theo điều kiện cụ thể
     * @param array $where
     * @param bool $is_list
     * @return array|int
     */
    public function getCount(array $where, bool $is_list = false)
    {
        if ($is_list)
            return $this->getModel()->where($where)->group('uid')->fetchSql(true)->column('count(*) as user_count', 'uid');
        else
            return $this->getModel()->where($where)->count();
    }

    /**
     * Tăng số lần thanh toán thành công của người dùng
     * @param int $uid
     * @return mixed
     */
    public function incPayCount(int $uid)
    {
        return $this->getModel()->where('uid', $uid)->inc('pay_count', 1)->update();
    }

    /**
     * Cộng dồn một giá trị vào một trường
     * @param string $field
     * @param int $num
     */
    public function incField(int $uid, string $field, int $num = 1)
    {
        return $this->getModel()->where('uid', $uid)->inc($field, $num)->update();
    }

    /**
     * @param $uid
     * @param string $field
     * @return \think\Collection
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserLabel($uid, $field = '*')
    {
        return $this->search(['uid' => $uid])->field($field)->with(['label'])->select()->toArray();
    }

    /**
     * Lấy người dùng là cộng tác viên
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAgentUserList(array $where, string $field = '*', int $page, int $limit)
    {
        return $this->search($where)->field($field)->with([
            'extract' => function ($query) {
                $query->field('sum(extract_price) as extract_count_price,count(id) as extract_count_num,uid')->where('status', '1')->group('uid');
            }, 'order' => function ($query) {
                $query->field('sum(pay_price) as order_price,count(id) as order_count,uid')->where('paid', 1)->where('refund_status', 0)->whereIn('pid', [-1, 0])->group('uid');
            }, 'bill' => function ($query) {
                $query->field('sum(number) as brokerage_money,uid')->where('category', 'now_money')->where('type', 'brokerage')->where('status', 1)->where('pm', 1)->group('uid');
            }, 'spreadCount' => function ($query) {
                $query->field('count(*) as spread_count,spread_uid')->group('spread_uid');
            }, 'spreadUser' => function ($query) {
                $query->field('uid,phone,nickname');
            }, 'agentLevel' => function ($query) {
                $query->field('id,name');
            }
        ])->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order('uid desc')->select()->toArray();
    }

    /**
     * Lấy danh sách người giới thiệu
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSairList(array $where, string $field = '*', int $page, int $limit)
    {
        return $this->search($where)->field($field)->with([
            'order' => function ($query) {
                $query->field('sum(pay_price) as order_price,count(id) as order_count,uid')->where('paid', 1)->where('pid', '<=', 0)->where('refund_status', 0)->group('uid');
            }, 'spreadCount' => function ($query) {
                $query->field('count(*) as spread_count,spread_uid')->group('spread_uid');
            }, 'spreadUser' => function ($query) {
                $query->field('uid,phone,nickname');
            }
        ])->page($page, $limit)->order('uid desc')->select()->toArray();
    }

    /**
     * Lấy bảng xếp hạng người giới thiệu
     * @param array $time
     * @param string $field
     * @param int $page
     * @param int $limit
     */
    public function getAgentRankList(array $time, string $field = '*', int $page, int $limit)
    {
        return $this->getModel()->alias('t0')
            ->field($field)
            ->join('user t1', 't0.uid = t1.spread_uid', 'LEFT')
            ->where('t1.spread_uid', '<>', 0)
            ->order('count desc')
            ->order('t0.uid desc')
            ->where('t1.spread_time', 'BETWEEN', $time)
            ->where('t0.is_del', 0)
            ->page($page, $limit)
            ->group('t0.uid')
            ->select()->toArray();
    }

    /**
     * Lấy id các cộng tác viên
     * @param array $where
     * @return array
     * @throws \ReflectionException
     */
    public function getAgentUserIds(array $where)
    {
        return $this->search($where)->column('uid');
    }

    /**
     * Tổng giá trị một trường của người dùng theo một điều kiện
     * @param array $where
     * @param string $filed
     * @return float
     */
    public function getWhereSumField(array $where, string $filed)
    {
        return $this->search($where)->sum($filed);
    }

    /**
     * Tra cứu thông tin người dùng tương ứng theo điều kiện, trả về dạng mảng
     * @param array $where
     * @param string $field
     * @param string $key
     * @return array
     */
    public function getUserInfoArray(array $where, string $field, string $key)
    {
        return $this->search($where)->column($field, $key);
    }

    /**
     * Lấy lượt truy cập của người dùng trong thời gian cụ thể
     * @param $time
     * @param $week
     * @return int
     */
    public function todayLastVisit($time, $week)
    {
        switch ($week) {
            case 1:
                return $this->search(['time' => $time ?: 'today', 'timeKey' => 'last_time'])->count();
            case 2:
                return $this->search(['time' => $time ?: 'week', 'timeKey' => 'last_time'])->count();
        }
    }

    /**
     * Lấy lượt truy cập của người dùng trong thời gian cụ thể
     * @param $time
     * @param $week
     * @return int
     */
    public function todayAddVisit($time, $week)
    {
        switch ($week) {
            case 1:
                return $this->search(['time' => $time ?: 'today', 'timeKey' => 'add_time'])->count();
            case 2:
                return $this->search(['time' => $time ?: 'week', 'timeKey' => 'add_time'])->count();
        }
    }

    /**
     * Lấy danh sách người dùng trong khoảng thời gian cụ thể
     * @param $starday
     * @param $yesterday
     * @return mixed
     */
    public function userList($starday, $yesterday)
    {
        return $this->getModel()
            ->whereBetweenTime('add_time', $starday, $yesterday)
            ->field("FROM_UNIXTIME(add_time,'%m-%e') as day,count(*) as count")
            ->group("FROM_UNIXTIME(add_time, '%Y%m%e')")
            ->order('add_time asc')->select()->toArray();
    }

    /**
     * Số lượng người dùng theo phạm vi số lượng mua
     * @param $status
     * @return int
     */
    public function userCount($status)
    {
        switch ($status) {
            case 1:
                return $this->getModel()->where('pay_count', '>', 1)->where('pay_count', '<=', 4)->count();
            case 2:
                return $this->getModel()->where('pay_count', '>', 4)->count();
        }
    }

    /**
     * Lấy dữ liệu thống kê người dùng
     * @param $time
     * @param $type
     * @param $timeType
     * @return mixed
     */
    public function getTrendData($time, $type, $timeType)
    {
        return $this->getModel()->when($type != '', function ($query) use ($type) {
            $query->where('user_type', $type);
        })->where(function ($query) use ($time) {
            if ($time[0] == $time[1]) {
                $query->whereDay('add_time', $time[0]);
            } else {
                $time[1] = date('Y/m/d', strtotime($time[1]) + 86400);
                $query->whereTime('add_time', 'between', $time);
            }
        })->field("FROM_UNIXTIME(add_time,'$timeType') as days,count(uid) as num")->group('days')->select()->toArray();
    }

    /**
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserInfoList(array $where, $field = "*"): array
    {
        return $this->search($where)->field($field)->select()->toArray();
    }

    /**
     * Lấy số lượng người dùng là thành viên
     * @param $where (time  type)
     * @return int
     */
    public function getMemberCount($where, int $overdue_time = 0)
    {
        if (!$overdue_time) $overdue_time = time();
        return $this->search($where)->where('is_ever_level', 1)->whereOr(function ($qeury) use ($overdue_time) {
            $qeury->where('is_money_level', '>', 0)->where('overdue_time', '>', $overdue_time);
        })->count();
    }

    /**
     * Dùng bộ lọc tìm kiếm (searcher)
     * @param array $where
     * @return \crmeb\basic\BaseModel
     * @throws \ReflectionException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/10/10
     */
    public function getSearch($where = [])
    {
        return $this->search($where);
    }
}
