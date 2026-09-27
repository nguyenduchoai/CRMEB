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

namespace app\services\user;

use app\dao\user\UserMoneyDao;
use app\services\BaseServices;
use app\services\order\OtherOrderServices;
use app\services\order\StoreOrderServices;
use crmeb\exceptions\AdminException;

class UserMoneyServices extends BaseServices
{
    /**
     * Mẫu bản ghi người dùng
     * @var array[]
     */
    protected $incomeData = [
        'pay_product' => [
            'title' => 'Mua sản phẩm bằng số dư',
            'type' => 'pay_product',
            'mark' => 'Thanh toán {%num%}đ bằng số dư để mua sản phẩm',
            'status' => 1,
            'pm' => 0
        ],
        'pay_member' => [
            'title' => 'Mua gói thành viên bằng số dư',
            'type' => 'pay_member',
            'mark' => 'Thanh toán {%num%}đ bằng số dư để mua gói thành viên',
            'status' => 1,
            'pm' => 0
        ],
        'pay_product_refund' => [
            'title' => 'Hoàn tiền sản phẩm',
            'type' => 'pay_product_refund',
            'mark' => 'Hoàn tiền đơn hàng vào số dư {%num%}đ',
            'status' => 1,
            'pm' => 1
        ],
        'system_add' => [
            'title' => 'Hệ thống cộng số dư',
            'type' => 'system_add',
            'mark' => 'Hệ thống cộng {%num%} vào số dư',
            'status' => 1,
            'pm' => 1
        ],
        'system_sub' => [
            'title' => 'Hệ thống trừ số dư',
            'type' => 'system_sub',
            'mark' => 'Hệ thống trừ {%num%} từ số dư',
            'status' => 1,
            'pm' => 0
        ],
        'user_recharge' => [
            'title' => 'Người dùng nạp tiền vào số dư',
            'type' => 'recharge',
            'mark' => 'Nạp tiền vào số dư thành công {%price%}đ, được tặng {%give_price%}đ',
            'status' => 1,
            'pm' => 1
        ],
        'user_recharge_refund' => [
            'title' => 'Hoàn tiền nạp của người dùng',
            'type' => 'recharge_refund',
            'mark' => 'Hoàn tiền, trừ số dư {%num%}đ',
            'status' => 1,
            'pm' => 0
        ],
        'brokerage_to_nowMoney' => [
            'title' => 'Rút hoa hồng về số dư',
            'type' => 'extract',
            'mark' => 'Rút hoa hồng về số dư {%num%}đ',
            'status' => 1,
            'pm' => 1
        ],
        'lottery_use_money' => [
            'title' => 'Tham gia quay thưởng dùng số dư',
            'type' => 'lottery_use',
            'mark' => 'Tham gia quay thưởng dùng số dư {%num%}',
            'status' => 1,
            'pm' => 0
        ],
        'lottery_give_money' => [
            'title' => 'Quay trúng thưởng, được tặng số dư',
            'type' => 'lottery_add',
            'mark' => 'Quay trúng thưởng, được tặng số dư {%num%}',
            'status' => 1,
            'pm' => 1
        ],
        'register_system_add' => [
            'title' => 'Người dùng mới đăng ký được tặng số dư',
            'type' => 'register_system_add',
            'mark' => 'Người dùng mới đăng ký được tặng số dư {%num%}',
            'status' => 1,
            'pm' => 1
        ],
    ];

    /**
     * UserMoneyServices constructor.
     * @param UserMoneyDao $dao
     */
    public function __construct(UserMoneyDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Ghi bản ghi người dùng
     * @param string $type Loại ghi
     * @param int $uid
     * @param int|string|array $number
     * @param int|string $balance
     * @param $linkId
     * @param string $mark
     * @return bool|mixed
     */
    public function income(string $type, int $uid, $number, $balance, $linkId, string $mark = '')
    {
        $data = $this->incomeData[$type] ?? null;
        if (!$data) {
            return true;
        }
        $data['uid'] = $uid;
        $data['balance'] = $balance ?? 0;
        $data['link_id'] = $linkId;
        if (is_array($number)) {
            $key = array_keys($number);
            $key = array_map(function ($item) {
                return '{%' . $item . '%}';
            }, $key);
            $value = array_values($number);
            $data['number'] = $number['number'] ?? 0;
            $data['mark'] = $mark == '' ? str_replace($key, $value, $data['mark']) : $mark;
        } else {
            $data['number'] = $number;
            $data['mark'] = $mark == '' ? str_replace(['{%num%}'], $number, $data['mark']) : $mark;
        }
        $data['add_time'] = time();

        return $this->dao->save($data);
    }

    /**
     * Lịch sử số dư
     * @param $where
     * @return array
     */
    public function balanceList($where)
    {
        $status = [];
        foreach ($this->incomeData as $value) {
            $status[$value['type']] = $value['title'];
        }
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, $page, $limit);
        //Người dùng liên kết
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $uids = array_column($list, 'uid');
        $nicknameArr = $userServices->getColumn([['uid', 'in', $uids]], 'nickname', 'uid');
        //Đơn hàng liên quan
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        /** @var UserRechargeServices $rechargeServices */
        $rechargeServices = app()->make(UserRechargeServices::class);
        /** @var OtherOrderServices $otherOrderServices */
        $otherOrderServices = app()->make(OtherOrderServices::class);
        foreach ($list as &$item) {
            $item['nickname'] = $nicknameArr[$item['uid']];
            if ($item['type'] == 'pay_product' || $item['type'] == 'pay_product_refund') {
                $item['relation'] = $orderServices->value(['id' => $item['link_id']], 'order_id');
            } elseif ($item['type'] == 'recharge' || $item['type'] == 'recharge_refund') {
                $item['relation'] = $rechargeServices->value(['id' => $item['link_id']], 'order_id');
            } elseif ($item['type'] == 'pay_member') {
                $item['relation'] = $otherOrderServices->value(['id' => $item['link_id']], 'order_id');
            } else {
                $item['relation'] = $status[$item['type']];
            }
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['type_name'] = $status[$item['type']];
        }
        $count = $this->dao->count($where);
        return compact('list', 'count', 'status');
    }

    /**
     * Ghi chú lịch sử số dư
     * @param $data
     * @return bool
     */
    public function recordRemark($id, $mark)
    {
        if ($this->dao->update($id, ['mark' => $mark])) {
            return true;
        } else {
            throw new AdminException('Ghi chú thất bại');
        }
    }

    /**
     * Cơ sở thống kê số dư
     * @return array
     * @throws \ReflectionException
     */
    public function getBasic()
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $data['now_balance'] = $userServices->sum(['status' => 1], 'now_money', true);
        $data['add_balance'] = $this->dao->sum([
            ['pm', '=', 1],
            ['type', 'in', ['system_add', 'recharge', 'extract', 'lottery_add', 'register_system_add']]
        ], 'number', false);
        $data['sub_balance'] = bcsub($data['add_balance'], $data['now_balance'], 2);
        return $data;
    }

    /**
     * Xu hướng số dư
     * @param $where
     * @return array
     */
    public function getTrend($where)
    {
        $time = explode('-', $where['time']);
        if (count($time) != 2) throw new AdminException('Vui lòng chọn thời gian');
        $dayCount = (strtotime($time[1]) - strtotime($time[0])) / 86400 + 1;
        $data = [];
        if ($dayCount == 1) {
            $data = $this->trend($time, 0);
        } elseif ($dayCount > 1 && $dayCount <= 31) {
            $data = $this->trend($time, 1);
        } elseif ($dayCount > 31 && $dayCount <= 92) {
            $data = $this->trend($time, 3);
        } elseif ($dayCount > 92) {
            $data = $this->trend($time, 30);
        }
        return $data;
    }

    /**
     * Xu hướng số dư
     * @param $time
     * @param $num
     * @param false $excel
     * @return array
     */
    public function trend($time, $num, $excel = false)
    {
        if ($num == 0) {
            $xAxis = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];
            $timeType = '%H';
        } elseif ($num != 0) {
            $dt_start = strtotime($time[0]);
            $dt_end = strtotime($time[1]);
            while ($dt_start <= $dt_end) {
                if ($num == 30) {
                    $xAxis[] = date('Y-m', $dt_start);
                    $dt_start = strtotime("+1 month", $dt_start);
                    $timeType = '%Y-%m';
                } else {
                    $xAxis[] = date('m-d', $dt_start);
                    $dt_start = strtotime("+$num day", $dt_start);
                    $timeType = '%m-%d';
                }
            }
        }
        $time[1] = date("Y-m-d", strtotime("+1 day", strtotime($time[1])));
        $point_add = array_column($this->dao->getBalanceTrend($time, $timeType, 'add_time', 'sum(number)', 'add'), 'num', 'days');
        $point_sub = array_column($this->dao->getBalanceTrend($time, $timeType, 'add_time', 'sum(number)', 'sub'), 'num', 'days');
        $data = $series = [];
        foreach ($xAxis as $item) {
            $data['Số dư tích lũy'][] = isset($point_add[$item]) ? floatval($point_add[$item]) : 0;
            $data['Tiêu dùng số dư'][] = isset($point_sub[$item]) ? floatval($point_sub[$item]) : 0;
        }
        foreach ($data as $key => $item) {
            $series[] = [
                'name' => $key,
                'data' => $item,
                'type' => 'line',
            ];
        }
        return compact('xAxis', 'series');
    }

    /**
     * Nguồn số dư
     * @param $where
     * @return array
     */
    public function getChannel($where)
    {
        $bing_xdata = ['Hệ thống cộng', 'Nạp tiền người dùng', 'Rút tiền hoa hồng', 'Tặng từ quay thưởng', 'Hoàn tiền sản phẩm'];
        $color = ['#64a1f4', '#3edeb5', '#70869f', '#ffc653', '#fc7d6a'];
        $data = ['system_add', 'recharge', 'extract', 'lottery_add', 'pay_product_refund'];
        $bing_data = [];
        foreach ($data as $key => $item) {
            $bing_data[] = [
                'name' => $bing_xdata[$key],
                'value' => $this->dao->sum(['pm' => 1, 'type' => $item, 'time' => $where['time']], 'number', true),
                'itemStyle' => ['color' => $color[$key]]
            ];
        }

        $list = [];
        $count = array_sum(array_column($bing_data, 'value'));
        foreach ($bing_data as $key => $item) {
            $list[] = [
                'name' => $item['name'],
                'value' => $item['value'],
                'percent' => $count != 0 ? bcmul((string)bcdiv((string)$item['value'], (string)$count, 4), '100', 2) : 0,
            ];
        }
        array_multisort(array_column($list, 'value'), SORT_DESC, $list);
        return compact('bing_xdata', 'bing_data', 'list');
    }

    /**
     * Loại số dư
     * @param $where
     * @return array
     */
    public function getType($where)
    {
        $bing_xdata = ['Hệ thống trừ', 'Hoàn tiền nạp', 'Mua sản phẩm', 'Mua gói thành viên'];
        $color = ['#64a1f4', '#3edeb5', '#70869f', '#ffc653'];
        $data = ['system_sub', 'recharge_refund', 'pay_product', 'pay_member'];
        $bing_data = [];
        foreach ($data as $key => $item) {
            $bing_data[] = [
                'name' => $bing_xdata[$key],
                'value' => $this->dao->sum(['pm' => 0, 'type' => $item, 'time' => $where['time']], 'number', true),
                'itemStyle' => ['color' => $color[$key]]
            ];
        }

        $list = [];
        $count = array_sum(array_column($bing_data, 'value'));
        foreach ($bing_data as $key => $item) {
            $list[] = [
                'name' => $item['name'],
                'value' => $item['value'],
                'percent' => $count != 0 ? bcmul((string)bcdiv((string)$item['value'], (string)$count, 4), '100', 2) : 0,
            ];
        }
        array_multisort(array_column($list, 'value'), SORT_DESC, $list);
        return compact('bing_xdata', 'bing_data', 'list');
    }

    public function getMoneyList($uid, $type)
    {
        $where = [];
        $where['uid'] = $uid;
        [$page, $limit] = $this->getPageValue();
        if ($type == 1) {
            $where['pm'] = 0;
        } elseif ($type == 2) {
            $where['pm'] = 1;
            $where['not_type'] = ['pay_product_refund'];
        }
        $list = $this->dao->getList($where, $page, $limit);
        $count = $this->dao->count($where);
        $times = [];
        if ($list) {
            foreach ($list as &$item) {
                $item['time'] = $item['time_key'] = $item['add_time'] ? date('Y-m', (int)$item['add_time']) : '';
                $item['add_time'] = $item['add_time'] ? date('Y-m-d H:i', (int)$item['add_time']) : '';
            }
            $times = array_merge(array_unique(array_column($list, 'time_key')));
        }
        return ['list' => $list, 'time' => $times, 'count' => $count];
    }

    /**
     * Truy vấn số tiền nạp của người dùng
     * @param array $where
     * @param string $rechargeSumField
     * @param string $selectType
     * @param string $group
     * @return float|mixed
     */
    public function getRechargeMoneyByWhere(array $where, string $rechargeSumField, string $selectType, string $group = "")
    {
        switch ($selectType) {
            case "sum" :
                return $this->dao->getWhereSumField($where, $rechargeSumField);
            case "group" :
                return $this->dao->getGroupField($where, $rechargeSumField, $group);
        }
    }
}
