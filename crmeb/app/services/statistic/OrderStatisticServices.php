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

namespace app\services\statistic;

use app\services\BaseServices;
use app\services\order\StoreCartServices;
use app\services\order\StoreOrderServices;
use app\services\product\product\StoreVisitServices;
use app\services\user\UserBillServices;
use crmeb\exceptions\AdminException;


class OrderStatisticServices extends BaseServices
{
    /**
     * Cơ sở thống kê đơn hàng
     * @param $where
     * @return array
     */
    public function getBasic($where)
    {
        $time = explode('-', $where['time']);
        if (count($time) != 2) throw new AdminException('Vui lòng chọn thời gian');
        /** @var StoreOrderServices $orderService */
        $orderService = app()->make(StoreOrderServices::class);
        $data['pay_price'] = $orderService->sum(['paid' => 1, 'pid' => 0, 'time' => $where['time']], 'pay_price', true);
        $data['pay_count'] = $orderService->count(['paid' => 1, 'pid' => 0, 'time' => $where['time']]);
        $data['refund_price'] = $orderService->sum(['paid' => 1, 'pid' => 0, 'is_refund' => 1, 'time' => $where['time']], 'pay_price', true);
        $data['refund_count'] = $orderService->count(['paid' => 1, 'pid' => 0, 'is_refund' => 1, 'time' => $where['time']]);
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
        $dayCount = bcadd(bcdiv(bcsub(strtotime($time[1]), strtotime($time[0])), '86400'), '1');
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
    public function trend($time, $num)
    {
        /** @var StoreOrderServices $storeOrder */
        $storeOrder = app()->make(StoreOrderServices::class);

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
        $pay_price = array_column($storeOrder->getProductTrend($time, $timeType, 'add_time', 'sum(pay_price)', 'pay'), 'num', 'days');
        $pay_count = array_column($storeOrder->getProductTrend($time, $timeType, 'add_time', 'count(id)', 'pay'), 'num', 'days');
        $refund_price = array_column($storeOrder->getProductTrend($time, $timeType, 'add_time', 'sum(pay_price)', 'refund'), 'num', 'days');
        $refund_count = array_column($storeOrder->getProductTrend($time, $timeType, 'add_time', 'count(id)', 'refund'), 'num', 'days');
        $data = $series = [];
        foreach ($xAxis as $item) {
            $data['Số tiền đơn hàng'][] = isset($pay_price[$item]) ? floatval($pay_price[$item]) : 0;
            $data['Số đơn hàng'][] = isset($pay_count[$item]) ? floatval($pay_count[$item]) : 0;
            $data['Số tiền hoàn'][] = isset($refund_price[$item]) ? floatval($refund_price[$item]) : 0;
            $data['Số đơn hoàn tiền'][] = isset($refund_count[$item]) ? floatval($refund_count[$item]) : 0;
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
        /** @var StoreOrderServices $orderService */
        $orderService = app()->make(StoreOrderServices::class);

        $bing_xdata = ['OA WeChat', 'Mini Program', 'H5', 'PC', 'APP'];
        $color = ['#64a1f4', '#3edeb5', '#70869f', '#ffc653', '#fc7d6a'];
        $bing_data = [];
        foreach ($bing_xdata as $key => $item) {
            $bing_data[] = [
                'name' => $item,
                'value' => $orderService->count(['paid' => 1, 'pid' => 0, 'is_channel' => $key, 'time' => $where['time']]),
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
     * Loại đơn hàng
     * @param $where
     * @return array
     */
    public function getType($where)
    {
        /** @var StoreOrderServices $orderService */
        $orderService = app()->make(StoreOrderServices::class);

        $bing_xdata = ['Đơn hàng thường', 'Đơn flash sale', 'Đơn săn giảm giá', 'Đơn mua chung', 'Đơn đặt trước'];
        $model_checkbox = sys_config('model_checkbox', ['seckill', 'bargain', 'combination']);
        $color = ['#64a1f4', '#3edeb5', '#70869f', '#ffc653', '#fc7d6a'];
        $bing_data = [];
        foreach ($bing_xdata as $key => $item) {
            if (!in_array('seckill', $model_checkbox) && $key == 1) continue;
            if (!in_array('bargain', $model_checkbox) && $key == 2) continue;
            if (!in_array('combination', $model_checkbox) && $key == 3) continue;
            $bing_data[] = [
                'name' => $item,
                'value' => $orderService->together(['paid' => 1, 'pid' => 0, 'activity_type' => $key, 'time' => $where['time']], 'pay_price', 'sum'),
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
}
