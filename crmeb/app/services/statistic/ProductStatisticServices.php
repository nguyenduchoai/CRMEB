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

namespace app\services\statistic;


use app\services\BaseServices;
use app\services\order\StoreOrderRefundServices;
use app\services\other\export\ExportServices;
use app\services\order\StoreCartServices;
use app\services\order\StoreOrderServices;
use app\services\product\product\StoreProductLogServices;
use app\services\product\product\StoreVisitServices;
use app\services\user\UserBillServices;
use crmeb\exceptions\AdminException;

/**
 * Class ProductStatisticServices
 * @package app\services\statistic
 */
class ProductStatisticServices extends BaseServices
{
    /**
     * Thông tin cơ bản sản phẩm
     * @param $where
     * @return array
     */
    public function getBasic($where)
    {
        $time = explode('-', $where['time']);
        if (count($time) != 2) throw new AdminException('Vui lòng chọn thời gian');
        //Dữ liệu hiện tại
        $now = $this->basicInfo($where, $time);

        //Dữ liệu so với kỳ trước
        $dayNum = bcadd(bcdiv(bcsub(strtotime($time[1]), strtotime($time[0])), '86400'), '1');
        $lastTime = array(
            date("Y/m/d", strtotime("-$dayNum days", strtotime($time[0]))),
            date("Y/m/d", strtotime("-1 days", strtotime($time[0])))
        );
        $where['time'] = implode('-', $lastTime);
        $last = $this->basicInfo($where, $lastTime);

        //Kết hợp dữ liệu, tính tỷ lệ so với kỳ trước
        $data = [];
        foreach ($now as $key => $item) {
            $data[$key]['num'] = $item;
            $num = $last[$key] > 0 ? $last[$key] : 1;
            $data[$key]['percent'] = bcmul((string)bcdiv((string)($item - $last[$key]), (string)$num, 4), 100, 2);
        }
        return $data;
    }

    /**
     * Dữ liệu cơ bản sản phẩm
     * @param $where
     * @param $time
     * @return mixed
     */
    public function basicInfo($where, $time)
    {
        /** @var StoreVisitServices $storeVisit */
        $storeVisit = app()->make(StoreVisitServices::class);
        /** @var StoreCartServices $storeCart */
        $storeCart = app()->make(StoreCartServices::class);
        /** @var StoreOrderServices $storeOrder */
        $storeOrder = app()->make(StoreOrderServices::class);
        /** @var StoreProductLogServices $productLog */
        $productLog = app()->make(StoreProductLogServices::class);

        $data['browse'] = $productLog->count($where + ['type' => 'visit']);//Lượt xem sản phẩm
        $data['user'] = $productLog->getDistinctCount($where + ['type' => 'visit'], 'uid');//Số khách truy cập sản phẩm
        $data['cart'] = $storeCart->getSum($where, 'cart_num');//Số lượng thêm vào giỏ hàng
        $data['order'] = $storeOrder->sum($where + ['pid' => 0], 'total_num', true);//Số lượng đặt hàng
        $data['pay'] = $storeOrder->sum([
            ['paid', '=', 1],
            ['pay_time', '>=', strtotime($time[0])],
            ['pay_time', '<', strtotime($time[1]) + 86400],
            ['pid', '>=', 0]
        ], 'total_num');//Số lượng thanh toán
        $data['payPrice'] = $storeOrder->sum([
            ['paid', '=', 1],
            ['pay_time', '>=', strtotime($time[0])],
            ['pay_time', '<', strtotime($time[1]) + 86400],
            ['pid', '>=', 0]
        ], 'pay_price');//Số tiền thanh toán
        $data['cost'] = $storeOrder->sum([
            ['paid', '=', 1],
            ['pay_time', '>=', strtotime($time[0])],
            ['pay_time', '<', strtotime($time[1]) + 86400],
            ['pid', '>=', 0]
        ], 'cost');//Tổng giá vốn
        $data['refundPrice'] = $storeOrder->sum($where + ['refund_status' => 2], 'pay_price', true);//Số tiền hoàn
        $data['refund'] = $storeOrder->sum($where + ['refund_status' => 2], 'total_num', true);//Số lượng hoàn tiền
        $payPeople = $storeOrder->getDistinctCount($where + ['paid' => 1], 'uid');//Số khách hàng đã mua
        $data['payPercent'] = $data['user'] > 0 ? bcmul(bcdiv($payPeople, $data['user'], 4), 100, 2) : 0;//Tỷ lệ chuyển đổi truy cập - thanh toán
        return $data;
    }

    /**
     * Xu hướng sản phẩm
     * @param $where
     * @param $excel
     * @return array
     */
    public function getTrend($where, $excel = false)
    {
        $time = explode('-', $where['time']);
        if (count($time) != 2) throw new AdminException('Tham số không hợp lệ');
        $dayCount = bcadd(bcdiv(bcsub(strtotime($time[1]), strtotime($time[0])), '86400'), '1');
        $data = [];
        if ($dayCount == 1) {
            $data = $this->trend($time, 0, $excel);
        } elseif ($dayCount > 1 && $dayCount <= 31) {
            $data = $this->trend($time, 1, $excel);
        } elseif ($dayCount > 31 && $dayCount <= 92) {
            $data = $this->trend($time, 3, $excel);
        } elseif ($dayCount > 92) {
            $data = $this->trend($time, 30, $excel);
        }
        return $data;
    }

    /**
     * Xu hướng sản phẩm
     * @param $time
     * @param $num
     * @param $excel
     * @return array
     */
    public function trend($time, $num, $excel = false)
    {
        /** @var StoreOrderRefundServices $orderRefund */
        $orderRefund = app()->make(StoreOrderRefundServices::class);
        /** @var StoreOrderServices $storeOrder */
        $storeOrder = app()->make(StoreOrderServices::class);
        /** @var StoreCartServices $storeCart */
        $storeCart = app()->make(StoreCartServices::class);
        /** @var StoreProductLogServices $productLog */
        $productLog = app()->make(StoreProductLogServices::class);

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
        $browse = array_column($productLog->getProductTrend($time, $timeType, 'sum(visit_num)'), 'num', 'days');
        $user = array_column($productLog->getProductTrend($time, $timeType, 'count(distinct(uid))'), 'num', 'days');
        $pay = array_column($storeOrder->getProductTrend($time, $timeType, 'pay_time', 'sum(pay_price)'), 'num', 'days');
        $refund = array_column($orderRefund->getProductTrend($time, $timeType, 'refunded_time', 'sum(refunded_price)'), 'num', 'days');
        if ($excel) {
            $cart = array_column($storeCart->getProductTrend($time, $timeType, 'sum(cart_num)'), 'num', 'days');
            $order = array_column($storeOrder->getProductTrend($time, $timeType, 'add_time', 'sum(total_num)'), 'num', 'days');
            $payNum = array_column($storeOrder->getProductTrend($time, $timeType, 'pay_time', 'sum(total_num)'), 'num', 'days');
            $payCountNum = array_column($storeOrder->getProductTrend($time, $timeType, 'pay_time', 'count(distinct(uid))'), 'num', 'days');
            $cost = array_column($storeOrder->getProductTrend($time, $timeType, 'pay_time', 'sum(cost)'), 'num', 'days');
            $refundNum = array_column($orderRefund->getProductTrend($time, $timeType, 'refunded_time', 'sum(refund_num)'), 'num', 'days');
            $data = [];
            foreach ($xAxis as &$item) {
                if (isset($user[$item]) && isset($payCountNum[$item])) {
                    $changes = bcmul(bcdiv((string)$payCountNum[$item], (string)$user[$item], 4), 100, 2);
                    $changes = $changes > 100 ? 100 : $changes;
                } else {
                    $changes = 0;
                }
                $data[] = [
                    'time' => $item,
                    'browse' => $browse[$item] ?? 0,
                    'user' => $user[$item] ?? 0,
                    'pay' => $pay[$item] ?? 0,
                    'refund' => $refund[$item] ?? 0,
                    'cart' => $cart[$item] ?? 0,
                    'order' => $order[$item] ?? 0,
                    'payNum' => $payNum[$item] ?? 0,
                    'cost' => $cost[$item] ?? 0,
                    'refundNum' => $refundNum[$item] ?? 0,
                    'changes' => $changes
                ];
            }
            /** @var ExportServices $exportService */
            $exportService = app()->make(ExportServices::class);
            $url = $exportService->productTrade($data);
            return compact('url');
        } else {
            $data = $series = [];
            foreach ($xAxis as $item) {
                $data['Lượt xem sản phẩm'][] = isset($browse[$item]) ? floatval($browse[$item]) : 0;
                $data['Lượt khách truy cập sản phẩm'][] = isset($user[$item]) ? floatval($user[$item]) : 0;
                $data['Số tiền thanh toán'][] = isset($pay[$item]) ? floatval($pay[$item]) : 0;
                $data['Số tiền hoàn'][] = isset($refund[$item]) ? floatval($refund[$item]) : 0;
            }
            foreach ($data as $key => $item) {
                if ($key == 'Lượt xem sản phẩm' || $key == 'Lượt khách truy cập sản phẩm') {
                    $series[] = [
                        'name' => $key,
                        'data' => $item,
                        'type' => 'line',
                        'smooth' => 'true',
                        'yAxisIndex' => 1,
                    ];
                } else {
                    $series[] = [
                        'name' => $key,
                        'data' => $item,
                        'type' => 'bar',
                    ];
                }
            }
            return compact('xAxis', 'series');
        }

    }

    /**
     * Xếp hạng sản phẩm
     * @param $where
     * @return mixed
     */
    public function getProductRanking($where)
    {
        /** @var StoreProductLogServices $productLog */
        $productLog = app()->make(StoreProductLogServices::class);
        return $productLog->getRanking($where);
    }
}
