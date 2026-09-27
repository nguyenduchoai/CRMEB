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

namespace app\dao\system\statistics;

use app\dao\BaseDao;
use app\model\system\statistics\CapitalFlow;

class CapitalFlowDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return CapitalFlow::class;
    }

    /**
     * Dòng tiền
     * @param $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList($where, $page = 0, $limit = 0)
    {
        return $this->search($where)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order('id desc')->select()->toArray();
    }

    /**
     * Lịch sử sao kê
     * @param $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getRecordList($where, int $page = 0, int $limit = 0)
    {
        $timeUnix = "%Y-%m-%d";
        switch ($where['type']) {
            case "day" :
                $timeUnix = "%Y-%m-%d";
                break;
            case "week" :
                $timeUnix = "%u";
                break;
            case "month" :
                $timeUnix = "%Y-%m";
                break;
        }
        $model = $this->search($where, false)
            ->when(isset($where['type']) && $where['type'] !== '', function ($query) use ($where, $timeUnix) {
                $query->field("FROM_UNIXTIME(add_time,'$timeUnix') as day,sum(if(price >= 0,price,0)) as income_price,sum(if(price < 0,price,0)) as exp_price,add_time");
                $query->group("FROM_UNIXTIME(add_time, '$timeUnix')");
            });
        $count = $model->count();
        $list = $model->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order('add_time desc')->select()->toArray();
        foreach ($list as &$item) {
            if ($where['type'] == 'day') {
                $item['ids'] = array_merge($this->getModel()->whereDay('add_time', $item['day'])->column('id'));
            } elseif ($where['type'] == 'week') {
                $day = $this->weekDayTime(date('Y'), $item['day']);
                $item['ids'] = array_merge($this->getModel()->whereWeek('add_time', $day)->column('id'));
            } elseif ($where['type'] == 'month') {
                $item['ids'] = array_merge($this->getModel()->whereMonth('add_time', $item['day'])->column('id'));
            }
        }
        return compact('list', 'count');
    }

    /**
     * Lấy ngày bắt đầu của tuần thứ mấy trong một năm
     * @param int $year
     * @param int $week
     * @return array|false|string
     */
    public function weekDayTime(int $year, int $week = 1)
    {
        $year_start = mktime(0, 0, 0, 1, 1, $year);
        // Kiểm tra ngày đầu tiên có phải là ngày bắt đầu của tuần đầu tiên không
        if (intval(date('W', $year_start)) === 1) {
            $start = $year_start;//Lấy ngày đầu tiên làm ngày bắt đầu của tuần đầu tiên
        } else {
            $start = strtotime('+1 monday', $year_start);//Lấy thứ Hai đầu tiên làm ngày bắt đầu
        }
        // Thời gian bắt đầu của tuần thứ mấy
        if ($week === 1) {
            $weekday = $start;
        } else {
            $weekday = strtotime('+' . ($week - 0) . ' monday', $start);
        }
        return date('Y-m-d', $weekday);
    }
}
