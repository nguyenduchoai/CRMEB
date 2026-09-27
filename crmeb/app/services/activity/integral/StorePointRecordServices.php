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
namespace app\services\activity\integral;

use app\dao\user\UserBillDao;
use app\services\BaseServices;
use app\services\order\StoreOrderServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;

class StorePointRecordServices extends BaseServices
{
    /**
     * UserBillServices constructor.
     * @param UserBillDao $dao
     */
    public function __construct(UserBillDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lịch sử điểm thưởng
     * @param $where
     * @return array
     */
    public function pointRecord($where)
    {
        $where['category'] = 'integral';
        $status = [
            'invite_user' => 'Thưởng mời người mới',
            'system_add' => 'Hệ thống cộng điểm thưởng',
            'system_sub' => 'Hệ thống trừ điểm thưởng',
            'gain' => 'Tặng điểm thưởng khi đặt hàng',
            'product_gain' => 'Tặng điểm thưởng khi mua sản phẩm',
            'deduction' => 'Khấu trừ điểm thưởng khi đặt hàng',
            'lottery_use' => 'Dùng điểm thưởng tham gia quay thưởng',
            'lottery_add' => 'Tặng điểm thưởng khi trúng quay thưởng',
            'order_deduction' => 'Trừ điểm thưởng đã tặng khi đặt hàng',
            'storeIntegral_use' => 'Đổi điểm lấy sản phẩm',
            'pay_product_integral_back' => 'Hoàn lại điểm thưởng đã dùng khi đặt hàng',
            'sign' => 'Nhận điểm thưởng khi điểm danh',
        ];
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, '*', $page, $limit);
        //Người dùng liên kết
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $uids = array_column($list, 'uid');
        $nicknameArr = $userServices->getColumn([['uid', 'in', $uids]], 'nickname', 'uid');
        //Đơn hàng liên quan
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        /** @var StoreIntegralOrderServices $integralOrderServices */
        $integralOrderServices = app()->make(StoreIntegralOrderServices::class);
        foreach ($list as &$item) {
            $item['nickname'] = $nicknameArr[$item['uid']] ?? 'Người dùng không xác định';
            if ($item['type'] == 'gain' || $item['type'] == 'deduction' || $item['type'] == 'product_deduction' || $item['type'] == 'pay_product_integral_back') {
                $item['relation'] = $orderServices->value(['id' => $item['link_id']], 'order_id');
            } elseif ($item['type'] == 'storeIntegral_use') {
                $item['relation'] = $integralOrderServices->value(['id' => $item['link_id']], 'order_id');
            } else {
                $item['relation'] = $status[$item['type']];
            }
            $item['type_name'] = $status[$item['type']];
        }
        $count = $this->dao->count($where);
        return compact('list', 'count', 'status');
    }

    /**
     * Ghi chú lịch sử điểm thưởng
     * @param $data
     * @return bool
     */
    public function recordRemark($id, $mark)
    {
        if (!$id) throw new AdminException('Tham số không hợp lệ');
        if ($mark === '') throw new AdminException('Ghi chú không được để trống');
        if ($this->dao->update($id, ['mark' => $mark])) {
            return true;
        } else {
            throw new AdminException('Ghi chú thất bại');
        }
    }

    /**
     * Cơ sở thống kê đơn hàng
     * @param $where
     * @return array
     */
    public function getBasic($where)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $data['now_point'] = $userServices->sum(['status' => 1], 'integral', true);
        $data['all_point'] = $this->dao->sum(['category' => 'integral', 'pm' => 1, 'time' => $where['time']], 'number', true);
        $data['pay_point'] = $this->dao->sum(['category' => 'integral', 'pm' => 0, 'time' => $where['time']], 'number', true);
        return $data;
    }

    /**
     * Xu hướng đơn hàng
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
     * Xu hướng đơn hàng
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
        $point_add = array_column($this->dao->getPointTrend($time, $timeType, 'add_time', 'sum(number)', 'add'), 'num', 'days');
        $point_sub = array_column($this->dao->getPointTrend($time, $timeType, 'add_time', 'sum(number)', 'sub'), 'num', 'days');
        $data = $series = [];
        foreach ($xAxis as $item) {
            $data['Điểm tích lũy'][] = isset($point_add[$item]) ? floatval($point_add[$item]) : 0;
            $data['Điểm thưởng đã dùng'][] = isset($point_sub[$item]) ? floatval($point_sub[$item]) : 0;
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
     * Nguồn đơn hàng
     * @param $where
     * @return array
     */
    public function getChannel($where)
    {
        $bing_xdata = ['Tặng theo đơn hàng', 'Tặng theo sản phẩm', 'Tặng từ trang quản trị', 'Nhận từ điểm danh', 'Quay thưởng 9 ô'];
        $color = ['#64a1f4', '#3edeb5', '#70869f', '#ffc653', '#fc7d6a'];
        $data = ['gain', 'product_gain', 'system_add', 'sign', 'lottery_add'];
        $bing_data = [];
        foreach ($data as $key => $item) {
            $bing_data[] = [
                'name' => $bing_xdata[$key],
                'value' => $this->dao->sum(['pm' => 1, 'type' => $item, 'category' => 'integral', 'time' => $where['time']], 'number', true),
                'itemStyle' => ['color' => $color[$key]]
            ];
        }
        $list = [];
        $count = array_sum(array_column($bing_data, 'value'));
        foreach ($bing_data as $key => $item) {
            $list[] = [
                'name' => $item['name'],
                'value' => $item['value'],
                'percent' => $count != 0 ? round(bcmul((string)bcdiv((string)$item['value'], (string)$count, 4), '100', 2), 1) : 0,
            ];
        }
        array_multisort(array_column($list, 'value'), SORT_DESC, $list);
        return compact('bing_xdata', 'bing_data', 'list');
    }

    /**
     * Loại đơn hàng
     * @param $where
     * @return array
     */
    public function getType($where)
    {
        $bing_xdata = ['Khấu trừ khi đặt hàng', 'Quay thưởng 9 ô', 'Trừ từ trang quản trị', 'Hoàn điểm do hoàn tiền', 'Đổi sản phẩm'];
        $color = ['#64a1f4', '#3edeb5', '#70869f', '#ffc653', '#fc7d6a'];
        $data = ['deduction', 'lottery_use', 'system_sub', 'order_deduction', 'storeIntegral_use'];
        $bing_data = [];
        foreach ($data as $key => $item) {
            $bing_data[] = [
                'name' => $bing_xdata[$key],
                'value' => $this->dao->sum(['pm' => 0, 'type' => $item, 'category' => 'integral', 'time' => $where['time']], 'number', true),
                'itemStyle' => ['color' => $color[$key]]
            ];
        }

        $list = [];
        $count = array_sum(array_column($bing_data, 'value'));
        foreach ($bing_data as $key => $item) {
            $list[] = [
                'name' => $item['name'],
                'value' => $item['value'],
                'percent' => $count != 0 ? round(bcmul((string)bcdiv((string)$item['value'], (string)$count, 4), '100', 2), 1) : 0,
            ];
        }
        array_multisort(array_column($list, 'value'), SORT_DESC, $list);
        return compact('bing_xdata', 'bing_data', 'list');
    }
}
