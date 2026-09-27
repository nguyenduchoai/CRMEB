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
use app\model\user\UserExtract;

/**
 *
 * Class UserExtractDao
 * @package app\dao\user
 */
class UserExtractDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return UserExtract::class;
    }

    /**
     * Lấy tổng số tiền rút theo một điều kiện
     * @param array $where
     * @return float
     */
    public function getWhereSum(array $where)
    {
        return $this->search($where)->sum('extract_price');
    }

    /**
     * Lấy danh sách tổ hợp tổng số theo một số điều kiện
     * @param array $where
     * @param string $field
     * @param string $key
     * @return mixed
     */
    public function getWhereSumList(array $where, string $field = 'extract_price', string $key = 'uid')
    {
        return $this->search($where)->group($key)->column('sum(' . $field . ')', $key);
    }

    /**
     * Lấy danh sách rút tiền
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getExtractList(array $where, string $field = '*', int $page, int $limit)
    {
        return $this->search($where)->field($field)->with([
            'user' => function ($query) {
                $query->field('uid,nickname');
            }])->page($page, $limit)->order('id desc')->select()->toArray();
    }

    /**
     * Lấy tổng giá trị của một trường
     * @param array $where
     * @param string $field
     * @return float
     */
    public function getWhereSumField(array $where, string $field)
    {
        return $this->search($where, false)
            ->when(isset($where['timeKey']), function ($query) use ($where) {
                $query->whereBetweenTime('add_time', $where['timeKey']['start_time'], $where['timeKey']['end_time']);
            })
            ->sum($field);
    }

    /**Truy vấn nhóm theo một trường
     * @param array $where
     * @param string $field
     * @param string $group
     * @return mixed
     */
    public function getGroupField(array $where, string $field, string $group)
    {
        return $this->search($where, false)
            ->when(isset($where['timeKey']), function ($query) use ($where, $field, $group) {
                $query->whereBetweenTime('add_time', $where['timeKey']['start_time'], $where['timeKey']['end_time']);
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

    /**
     * @param array $where
     * @param string $field
     * @return float
     */
    public function getExtractMoneyByWhere(array $where, string $field)
    {
        return $this->search($where)->sum($field);
    }

    public function getExtractByOrderId($uid, $order_id)
    {
        $info = $this->getModel()->where('uid', $uid)->where('wechat_order_id', $order_id)->find();
        return $info ? $info->toArray() : [];
    }
}
