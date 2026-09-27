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
use app\services\order\StoreOrderRefundServices;
use app\services\other\export\ExportServices;
use app\services\order\OtherOrderServices;
use app\services\order\StoreOrderServices;
use app\services\user\UserExtractServices;
use app\services\user\UserMoneyServices;
use app\services\user\UserRechargeServices;

/**
 * Class TradeStatisticServices
 * @package app\services\statistic
 */
class TradeStatisticServices extends BaseServices
{
    public $_day = ['00h', '01h', '02h', '03h', '04h', '05h', '06h', '07h', '08h', '09h', '10h', '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h', '21h', '22h', '23h', '24h'];

    /**
     * Tổng quan cơ bản
     * @param $where
     * @return mixed
     */
    public function getTopLeftTrade($where)
    {
        //Tổng giá trị giao dịch
        $selectType = "sum";
        $tradeTotalMoney = $this->tradeTotalMoney($where, $selectType);
        //Biểu đồ giao dịch
        $selectType = "group";
        $hourTotalMoney = $this->tradeGroupMoney($where, $selectType);
        return ['total_money' => $tradeTotalMoney, 'curve' => $hourTotalMoney];
    }

    public function getTopRightOneTrade()
    {
        /** @var StoreOrderServices $orderService */
        $orderService = app()->make(StoreOrderServices::class);
        /** Số đơn hàng theo ngày */
        //Số đơn hàng hôm nay
        $orderCountWhere['is_del'] = 0;
        $orderCountWhere['paid'] = 1;
        $orderCountWhere['pid'] = 0;
        $orderCountWhere['timeKey'] = $this->TimeConvert("today");
        $todayOrderCount = $orderService->getOrderCountByWhere($orderCountWhere);

        //Biểu đồ số đơn hàng hôm nay
        $todayHourOrderCount = $orderService->getOrderGroupCountByWhere($orderCountWhere);
        $todayHourOrderCount = $this->trendYdata($todayHourOrderCount, $orderCountWhere['timeKey']);
        //Số đơn hàng hôm qua
        $yestodayWhere['is_del'] = 0;
        $yestodayWhere['paid'] = 1;
        $yestodayWhere['pid'] = 0;
        $yestodayWhere['timeKey'] = $this->TimeConvert("yestoday");
        $yesTodayOrderCount = $orderService->getOrderCountByWhere($yestodayWhere);
        //Biểu đồ đơn hàng hôm qua
        // $yestodayHourOrderCount = $orderService->getOrderGroupCountByWhere($yestodayWhere);
        // $yestodayHourOrderCount = $this->trendYdata($yestodayHourOrderCount, 'day');
        //Tỷ lệ tăng trưởng số đơn hàng so với kỳ trước
        $orderCountDayChain = $this->countRate($todayOrderCount, $yesTodayOrderCount);
        $data[] = [
            'name' => "Số đơn hàng hôm nay",
            'now_value' => $todayOrderCount,
            'last_value' => $yesTodayOrderCount,
            'rate' => $orderCountDayChain,
            'curve' => $todayHourOrderCount
        ];
        /** Số người thanh toán theo ngày */
        //Số người thanh toán hôm nay
        $orderPeopleWhere['timeKey'] = $this->TimeConvert("today");
        $orderPeopleWhere['paid'] = 1;
        $orderPeopleWhere['pid'] = 0;
        $todayPayOrderPeople = count($orderService->getPayOrderPeopleByWhere($orderPeopleWhere));
        //Biểu đồ số người thanh toán hôm nay
        $todayHourOrderPeople = $orderService->getPayOrderGroupPeopleByWhere($orderPeopleWhere);
        $todayHourOrderPeople = $this->trendYdata($todayHourOrderPeople, $orderPeopleWhere['timeKey']);
        //Số người thanh toán hôm qua
        $yestodayOrderPeopleWhere['timeKey'] = $this->TimeConvert("yestoday");
        $yestodayOrderPeopleWhere['paid'] = 1;
        $yestodayPayOrderPeople = count($orderService->getPayOrderPeopleByWhere($yestodayOrderPeopleWhere));
        //Biểu đồ thanh toán hôm qua
        // $yestodayHourOrderPeople = $orderService->getPayOrderGroupPeopleByWhere($yestodayOrderPeopleWhere);
        // $yestodayHourOrderPeople = $this->trendYdata($yestodayHourOrderPeople, 'day');
        //Số người thanh toán đơn hàng so với kỳ trước
        $orderPeopleDayChain = $this->countRate($todayPayOrderPeople, $yestodayPayOrderPeople);
        $data[] = [
            'name' => "Số người thanh toán hôm nay",
            'now_value' => $todayPayOrderPeople,
            'last_value' => $yestodayPayOrderPeople,
            'rate' => $orderPeopleDayChain,
            'curve' => $todayHourOrderPeople
        ];
        $new_data = [];
        foreach ($data as $k => $v) {
            $new_data['x'] = $v['curve']['x'];
            $new_data['series'][$k]['name'] = $v['name'];
            $new_data['series'][$k]['now_money'] = $v['now_value'];
            $new_data['series'][$k]['last_money'] = $v['last_value'];
            $new_data['series'][$k]['rate'] = $v['rate'];
            $new_data['series'][$k]['value'] = array_values($v['curve']['y']);

        }

        return $new_data;
    }

    public function getTopRightTwoTrade()
    {
        /** @var StoreOrderServices $orderService */
        $orderService = app()->make(StoreOrderServices::class);
        /** Số đơn hàng theo tháng */
        $monthOrderCountWhere['is_del'] = 0;
        $monthOrderCountWhere['paid'] = 1;
        $monthOrderCountWhere['pid'] = 0;
        $monthOrderCountWhere['timeKey'] = $this->TimeConvert("month");
        $monthOrderCount = $orderService->getOrderCountByWhere($monthOrderCountWhere);
        //Biểu đồ số đơn hàng tháng này
        $monthCurveOrderCount = $orderService->getOrderGroupCountByWhere($monthOrderCountWhere);
        $monthCurveOrderCount = $this->trendYdata($monthCurveOrderCount, $monthOrderCountWhere['timeKey']);
        //Số đơn hàng tháng trước
        $lastOrderCountWhere['timeKey'] = $this->TimeConvert("last_month");
        $lastOrderCountWhere['is_del'] = 0;
        $lastOrderCountWhere['paid'] = 1;
        $lastOrderCountWhere['pid'] = 0;
        $lastOrderCount = $orderService->getOrderCountByWhere($lastOrderCountWhere);
        //Biểu đồ đơn hàng tháng trước
        // $lastCurveOrderCount = $orderService->getOrderGroupCountByWhere($lastOrderCountWhere);
        // $lastCurveOrderCount = $this->trendYdata($lastCurveOrderCount, 'month');
        //Tỷ lệ tăng trưởng số đơn hàng so với kỳ trước
        // $orderCountMonthChain = (($monthOrderCount - $lastOrderCount) / $lastOrderCount) * 100;
        $orderCountMonthChain = $this->countRate($monthOrderCount, $lastOrderCount);
        $data[] = [
            'name' => "Số đơn hàng tháng này",
            'now_value' => $monthOrderCount,
            'last_value' => $lastOrderCount,
            'rate' => $orderCountMonthChain,
            'curve' => $monthCurveOrderCount
        ];
        /** Số người đặt hàng theo tháng */
        //Số người thanh toán tháng này
        $monthOrderPeopleWhere['timeKey'] = $this->TimeConvert("month");;
        $monthOrderPeopleWhere['paid'] = 1;
        $monthPayOrderPeople = count($orderService->getPayOrderPeopleByWhere($monthOrderPeopleWhere));
        //Biểu đồ số người thanh toán tháng này
        $monthCurveOrderPeople = $orderService->getPayOrderGroupPeopleByWhere($monthOrderPeopleWhere);
        $monthCurveOrderPeople = $this->trendYdata($monthCurveOrderPeople, $monthOrderPeopleWhere['timeKey']);
        //Số người thanh toán tháng trước
        $lastOrderPeopleWhere['timeKey'] = $this->TimeConvert("last_month");
        $lastOrderPeopleWhere['paid'] = 1;
        $lastPayOrderPeople = count($orderService->getPayOrderPeopleByWhere($lastOrderPeopleWhere));
        //Biểu đồ thanh toán tháng trước
        // $lastCurveOrderPeople = $orderService->getPayOrderGroupPeopleByWhere($lastOrderPeopleWhere);
        // $lastCurveOrderPeople = $this->trendYdata($lastCurveOrderPeople, 'month');
        //Số người thanh toán đơn hàng so với kỳ trước
        $orderPeopleDayChain = $this->countRate($monthPayOrderPeople, $lastPayOrderPeople);
        $data[] = [
            'name' => "Số người thanh toán tháng này",
            'now_value' => $monthPayOrderPeople,
            'last_value' => $lastPayOrderPeople,
            'rate' => $orderPeopleDayChain,
            'curve' => $monthCurveOrderPeople
        ];
        $new_data = [];
        foreach ($data as $k => $v) {
            $new_data[$k]['name'] = $v['name'];
            $new_data[$k]['now_money'] = $v['now_value'];
            $new_data[$k]['last_money'] = $v['last_value'];
            $new_data[$k]['rate'] = $v['rate'];
            $new_data[$k]['value'] = $v['curve']['y'];
        }

        return $new_data;
    }

    /**
     * Tổng giá trị giao dịch
     * @param $where
     * @param $selectType
     * @return array|float|int|mixed
     */
    public function tradeTotalMoney($where, $selectType, $isNum = false)
    {
        /** Doanh thu thu vào */
        //Doanh thu đơn hàng sản phẩm
        $inOrderMoney = $this->getOrderTotalMoney($where, $selectType, "", $isNum);
        //Doanh thu nạp tiền người dùng
        $inRechargeMoneyHome = $this->getRechargeTotalMoney($where, $selectType, "", $isNum);
        $inrechgeMoneyAdmin = $this->getBillYeTotalMoney($where, $selectType, '', $isNum);
        $inRechargeMoney = bcadd($inRechargeMoneyHome, $inrechgeMoneyAdmin, 2);
        //Doanh thu mua thành viên
        $inMemberMoney = $this->getMemberTotalMoney($where, $selectType, "", $isNum);
        //Doanh thu thu tiền ngoại tuyến
        $inOfflineMoney = $this->getOfflineTotalMoney($where, $selectType, "", $isNum);
        //Tổng giá trị giao dịch
        $inTotalMoney = bcadd(bcadd($inOrderMoney, $inRechargeMoney, 2), bcadd($inMemberMoney, $inOfflineMoney, 2), 2);/* - $outExtractUserMoney*/
        return $inTotalMoney;
    }

    /**
     * Biểu đồ giá trị giao dịch
     * @param $where
     * @param $selectType
     * @return array
     */
    public function tradeGroupMoney($where, $selectType)
    {

        //Doanh thu đơn hàng sản phẩm
        $orderGroup = "add_time";
        $OrderMoney = $this->getOrderTotalMoney($where, $selectType, $orderGroup);
        //Doanh thu nạp tiền người dùng
        $rechargeGroup = "add_time";
        $RechargeMoneyHome = $this->getRechargeTotalMoney($where, $selectType, $rechargeGroup);
        $RechargeMoneyAdmin = $this->getBillYeTotalMoney($where, $selectType, $rechargeGroup);
        $RechargeMoney = $this->totalArrData([$RechargeMoneyHome, $RechargeMoneyAdmin]);
        //Doanh thu mua thành viên
        $memberGroup = "add_time";
        $MemberMoney = $this->getMemberTotalMoney($where, $selectType, $memberGroup);
        //Doanh thu thu tiền ngoại tuyến
        $offlineGroup = "add_time";
        $OfflineMoney = $this->getOfflineTotalMoney($where, $selectType, $offlineGroup);
        return $this->totalArrData([$OrderMoney, $RechargeMoney, $MemberMoney, $OfflineMoney]);
    }

    /**
     * Dữ liệu phần dưới
     * @param $where
     * @return array
     * @throws \Exception
     */
    public function getBottomTrade($where)
    {

        if (!$where['data']) {
            $where['time'] = ['start_time' => date('Y-m-d 00:00:00', time()), "end_time" => date('Y-m-d 23:59:59', time())];
        } else {
            $time = explode("-", $where['data']);
            $where['time'] = ['start_time' => date('Y-m-d 00:00:00', strtotime($time[0])), "end_time" => date('Y-m-d 23:59:59', strtotime($time[1]))];
        }
        unset($where['data']);
        /** @var ExportServices $exportService */
        $exportService = app()->make(ExportServices::class);
        $chainTime = $this->chainTime($where['time']);
        $isNum = false;
        if ($chainTime == "other") $isNum = true;
        $dateWhere['time'] = $isNum ? $where['time'] : $chainTime;
        $topData = array();
        $Chain = array();

        /** Số tiền thanh toán sản phẩm */
        $OrderMoney = $this->getOrderTotalMoney($where, "sum");
        $lastOrderMoney = $this->getOrderTotalMoney($dateWhere, "sum", "", $isNum);
        $OrderCurve = $this->getOrderTotalMoney($where, "group", "add_time");
        $OrderChain = $this->countRate($OrderMoney, $lastOrderMoney);
        $topData[1] = [
            'title' => 'Số tiền thanh toán sản phẩm',
            'desc' => 'Trong điều kiện đã chọn, số tiền người dùng thực tế thanh toán khi mua sản phẩm, bao gồm số tiền thanh toán qua WeChat Pay, số dư, Alipay và thanh toán ngoại tuyến (sản phẩm mua chung được tính sau khi thành nhóm, đơn thanh toán ngoại tuyến được tính sau khi xác nhận thanh toán trong trang quản trị)',
            'total_money' => $OrderMoney,
            'rate' => $OrderChain,
            'value' => $OrderCurve['y'],
            'type' => 1,
            'sign' => 'goods',
        ];

        $Chain['goods'] = $OrderCurve;

        /** Số tiền mua gói thành viên */
        $memberMoney = $this->getMemberTotalMoney($where, 'sum');
        $lastMemberMoney = $this->getMemberTotalMoney($dateWhere, 'sum', "", $isNum);
        $memberCurve = $this->getMemberTotalMoney($where, 'group', "pay_time");
        $MemberChain = $this->countRate($memberMoney, $lastMemberMoney);
        $topData[2] = [
            'title' => 'Số tiền mua gói thành viên',
            'desc' => 'Trong điều kiện đã chọn, số tiền người dùng mua thành công gói thành viên trả phí',
            'total_money' => $memberMoney,
            'rate' => $MemberChain,
            'value' => $memberCurve['y'],
            'type' => 1,
            'sign' => 'member',
        ];
        $Chain['member'] = $memberCurve;

        /** Số tiền nạp */
        $rechgeMoneyHome = $this->getRechargeTotalMoney($where, 'sum');
        $rechgeMoneyAdmin = $this->getBillYeTotalMoney($where, 'sum');
        $rechgeMoneyTotal = bcadd($rechgeMoneyHome, $rechgeMoneyAdmin, 2);
        $lastRechgeMoneyHome = $this->getRechargeTotalMoney($dateWhere, 'sum', "", $isNum);
        $lastRechgeMoneyAdmin = $this->getBillYeTotalMoney($dateWhere, 'sum', "", $isNum);
        $lastRechgeMoneyTotal = bcadd($lastRechgeMoneyHome, $lastRechgeMoneyAdmin, 2);
        $RechgeHomeCurve = $this->getRechargeTotalMoney($where, 'group', "pay_time");
        $RechgeAdminCurve = $this->getBillYeTotalMoney($where, 'group', "add_time");
        $RechgeTotalCurve = $this->totalArrData([$RechgeHomeCurve, $RechgeAdminCurve]);
        $RechgeChain = $this->countRate($rechgeMoneyTotal, $lastRechgeMoneyTotal);
        $topData[3] = [
            'title' => 'Số tiền nạp',
            'desc' => 'Trong điều kiện đã chọn, số tiền người dùng nạp thành công',
            'total_money' => $rechgeMoneyTotal,
            'rate' => $RechgeChain,
            'value' => $RechgeTotalCurve['y'],
            'type' => 1,
            'sign' => 'rechge',
        ];
        $Chain['rechage'] = $RechgeTotalCurve;

        /** Thu ngân tại quầy */
        $offlineMoney = $this->getOfflineTotalMoney($where, 'sum');
        $lastOfflineMoney = $this->getOfflineTotalMoney($dateWhere, 'sum', "", $isNum);
        $offlineCurve = $this->getOfflineTotalMoney($where, 'group', "pay_time");
        $offlineChain = $this->countRate($offlineMoney, $lastOfflineMoney);
        $topData[4] = [
            'title' => 'Số tiền thu ngân ngoại tuyến',
            'desc' => 'Trong điều kiện đã chọn, số tiền người dùng quét mã thanh toán ngoại tuyến',
            'total_money' => $offlineMoney,
            'rate' => $offlineChain,
            'value' => $offlineCurve['y'],
            'type' => 0,
            'sign' => 'offline',
        ];
        $Chain['offline'] = $offlineCurve;

        /**  Chi phí*/
        //Thanh toán sản phẩm bằng số dư
        $outYeOrderMoney = $this->getOrderTotalMoney(['pay_type' => "yue", 'time' => $where['time']], 'sum');
        $lastOutYeOrderMoney = $this->getOrderTotalMoney(['pay_type' => "yue", 'time' => $dateWhere['time']], 'sum', "", $isNum);
        $outYeOrderCurve = $this->getOrderTotalMoney(['pay_type' => "yue", 'time' => $where['time']], 'group', 'pay_time');
        $outYeOrderChain = $this->countRate($outYeOrderMoney, $lastOutYeOrderMoney);
        //Mua thành viên bằng số dư
        $outYeMemberMoney = $this->getMemberTotalMoney(['pay_type' => "yue", 'time' => $where['time']], 'sum');
        $lastOutYeMemberMoney = $this->getMemberTotalMoney(['pay_type' => "yue", 'time' => $dateWhere['time']], 'sum', "", $isNum);
        $outYeMemberCurve = $this->getMemberTotalMoney(['pay_type' => "yue", 'time' => $where['time']], 'group', "pay_time");
        $outYeMemberChain = $this->countRate($outYeMemberMoney, $lastOutYeMemberMoney);
        //Thanh toán bằng số dư
        $outYeMoney = bcadd($outYeOrderMoney, $outYeMemberMoney, 2);
        $lastOutYeMoney = bcadd($lastOutYeOrderMoney, $lastOutYeMemberMoney, 2);
        $outYeCurve = $this->totalArrData([$outYeOrderCurve, $outYeMemberCurve]);
        $outYeChain = $this->countRate($outYeOrderChain, $outYeMemberChain);
        $topData[6] = [
            'title' => 'Số tiền thanh toán bằng số dư',
            'desc' => 'Số tiền người dùng thực tế thanh toán bằng số dư khi đặt hàng',
            'total_money' => $outYeMoney,
            'rate' => $outYeChain,
            'value' => $outYeCurve['y'],
            'type' => 0,
            'sign' => 'yue',
        ];
        $Chain['out_ye'] = $outYeCurve;


        //Số tiền hoa hồng đã chi trả
        $outExtractMoney = $this->getExtractTotalMoney($where, 'sum');
        $lastOutExtractMoney = $this->getExtractTotalMoney($dateWhere, 'sum', "", $isNum);
        $OutExtractCurve = $this->getExtractTotalMoney($where, 'group', "add_time");
        $OutExtractChain = $this->countRate($outExtractMoney, $lastOutExtractMoney);
        $topData[7] = [
            'title' => 'Số tiền hoa hồng đã chi trả',
            'desc' => 'Hoa hồng giới thiệu do trang quản trị chi trả cho cộng tác viên, tính theo số tiền thực tế đã trả',
            'total_money' => $outExtractMoney,
            'rate' => $OutExtractChain,
            'value' => $OutExtractCurve['y'],
            'type' => 0,
            'sign' => 'yong',
        ];
        $Chain['extract'] = $OutExtractCurve;

        //Số tiền hoàn trả sản phẩm
        $outOrderRefund = $this->getOrderRefundTotalMoney(['refund_type' => 6, 'time' => $where['time']], 'sum');
        $lastOutOrderRefund = $this->getOrderRefundTotalMoney(['refund_type' => 6, 'time' => $dateWhere['time']], 'sum', "", $isNum);
        $outOrderRefundCurve = $this->getOrderRefundTotalMoney(['refund_type' => 6, 'time' => $where['time']], 'group', 'add_time');
        $orderRefundChain = $this->countRate($outOrderRefund, $lastOutOrderRefund);
        $topData[8] = [
            'title' => 'Số tiền hoàn trả sản phẩm',
            'desc' => 'Số tiền sản phẩm người dùng được hoàn thành công',
            'total_money' => $outOrderRefund,
            'rate' => $orderRefundChain,
            'value' => $outOrderRefundCurve['y'],
            'type' => 0,
            'sign' => 'refund',
        ];
        $Chain['refund'] = $outOrderRefundCurve;

        //Số tiền chi
        $outTotalMoney = bcadd(bcadd($outYeMoney, $outExtractMoney, 2), $outOrderRefund, 2);
        $lastOutTotalMoney = bcadd(bcadd($lastOutYeMoney, $lastOutExtractMoney, 2), $lastOutOrderRefund, 2);
        $outTotalCurve = $this->totalArrData([$outYeCurve, $OutExtractCurve, $outOrderRefundCurve]);
        $outTotalChain = $this->countRate($outTotalMoney, $lastOutTotalMoney);
        $topData[5] = [
            'title' => 'Số tiền chi',
            'desc' => 'Số tiền thanh toán bằng số dư, số tiền hoa hồng đã chi trả, số tiền hoàn trả sản phẩm',
            'total_money' => $outTotalMoney,
            'rate' => $outTotalChain,
            'value' => $outTotalCurve['y'],
            'type' => 1,
            'sign' => 'out',
        ];
        $Chain['out'] = $outTotalCurve;

//        /** Số tiền lợi nhuận gộp giao dịch*/
//        $jiaoyiMoney = $this->tradeTotalMoney($where, "sum");
//
//        $jiaoyiMoney = bcsub($jiaoyiMoney, $outTotalMoney, 2);
//        $lastJiaoyiMoney = $this->tradeTotalMoney($dateWhere, "sum", $isNum);
//        $lastJiaoyiMoney = bcsub($lastJiaoyiMoney, $lastOutTotalMoney, 2);
//        $jiaoyiCurve = $this->tradeGroupMoney($where, "group");
//        $jiaoyiCurve = $this->subdutionArrData($jiaoyiCurve, $outTotalCurve);
//        $jiaoyiChain = $this->countRate($jiaoyiMoney, $lastJiaoyiMoney);
//        $topData[1] = [
//            'title' => 'Số tiền lợi nhuận gộp giao dịch',
//            'desc' => 'Số tiền lợi nhuận gộp giao dịch = doanh thu - số tiền chi ra',
//            'total_money' => $jiaoyiMoney,
//            'rate' => $jiaoyiChain,
//            'value' => $jiaoyiCurve['y'],
//            'type' => 1,
//            'sign' => 'jiaoyi',
//        ];
//        $Chain['jiaoyi'] = $jiaoyiCurve;

        /** @var Doanh thu $inTotalMoney */
        $inTotalMoney = $this->tradeTotalMoney($where, "sum");
        $lastInTotalMoney = $this->tradeTotalMoney($dateWhere, "sum", $isNum);
        $inTotalCurve = $this->tradeGroupMoney($where, "group");
        $inTotalChain = $this->countRate($inTotalMoney, $lastInTotalMoney);
        $topData[0] = [
            'title' => 'Doanh thu',
            'desc' => 'Số tiền thanh toán sản phẩm, số tiền nạp, số tiền mua thành viên trả phí, số tiền thu ngân ngoại tuyến',
            'total_money' => $inTotalMoney,
            'rate' => $inTotalChain,
            'value' => $inTotalCurve['y'],
            'type' => 1,
            'sign' => 'in',
        ];
        ksort($topData);
        $data = [];
        foreach ($topData as $k => $v) {
            $data['x'] = $Chain['out']['x'];
            $data['series'][$k]['name'] = $v['title'];
            $data['series'][$k]['desc'] = $v['desc'];
            $data['series'][$k]['money'] = $v['total_money'];
            $data['series'][$k]['type'] = $v['type'];
            $data['series'][$k]['rate'] = $v['rate'];
            $data['series'][$k]['value'] = array_values($v['value']);
        }
        $export = $exportService->tradeData($data, 'Thống kê giao dịch', 2);
        $data['export'] = $export[0];
        return $data;
    }

    /**
     * Cộng nhiều mảng
     * @param array $arr
     * @return array|false
     */
    public function totalArrData(array $arr)
    {
        if (!$arr || !is_array($arr)) return false;
        $item = array();
        $y = array_column($arr, "y");
        $x = array_column($arr, "x")[0];
        foreach ($y as $key => $value) {
            foreach ($value as $k => $v) {
                if (isset($item[$k])) {
                    $item[$k] = bcadd($item[$k], $v, 2);
                } else {
                    $item[$k] = $v;
                }
            }
        }
        return ['x' => $x, 'y' => $item];
    }

    /**
     * Trừ mảng
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public function subdutionArrData(array $arr1, array $arr2)
    {
        $item = array();
        foreach ($arr1['y'] as $key => $value) {
            $item['y'][$key] = bcsub($value, $arr2['y'][$key], 2);
        }
        $item['x'] = $arr1['x'];
        return $item;
    }

    /**
     * Chuyển đổi thời gian tìm kiếm
     * @param $timeKey
     * @param false $isNum
     * @return array
     * @throws \Exception
     */
    public function TimeConvert($timeKey, $isNum = false)
    {
        switch ($timeKey) {
            case "today" :
                $data['start_time'] = date('Y-m-d 00:00:00', time());
                $data['end_time'] = date('Y-m-d 23:59:59', time());
                $data['days'] = 1;
                break;
            case "yestoday" :
                $data['start_time'] = date('Y-m-d 00:00:00', strtotime('-1 day'));
                $data['end_time'] = date('Y-m-d 23:59:59', strtotime('-1 day'));
                $data['days'] = 1;
                break;
            case "last_month" :
                $data['start_time'] = date('Y-m-01 00:00:00', strtotime('-1 month'));
                $data['end_time'] = date('Y-m-t 23:59:59', strtotime('-1 month'));
                $data['days'] = 30;
                break;
            case "month" :
                $data['start_time'] = $month_start_time = date('Y-m-01 00:00:00', strtotime(date("Y-m-d")));
                $data['end_time'] = date('Y-m-d 23:59:59', strtotime("$month_start_time +1 month -1 day"));
                $data['days'] = 30;
                break;
            case "year" :
                $data['start_time'] = date('Y-01-01 00:00:00', time());
                $data['end_time'] = date('Y-12-t 23:59:59', time());
                $data['days'] = 365;
                break;
            case "last_year" :
                $data['start_time'] = date('Y-01-01 00:00:00', strtotime('-1 year'));
                $data['end_time'] = date('Y-12-t 23:59:59', strtotime('-1 year'));
                $data['days'] = 365;
                break;
            case 30 :
            case 15 :
            case 7 :
                if (!$isNum) {
                    $data['start_time'] = date("Y-m-d 00:00:00", strtotime("-$timeKey day"));
                    $data['end_time'] = date('Y-m-d 23:59:59', time());
                    $data['days'] = $timeKey;
                } else {
                    $day = $timeKey * 2;
                    $data['start_time'] = date("Y-m-d 00:00:00", strtotime("-$day  day"));
                    $data['end_time'] = date("Y-m-d 23:59:59", strtotime("-$timeKey day"));
                    $data['days'] = $timeKey;
                }
                break;
            default:
                $datetime_start = new \DateTime($timeKey['start_time']);
                $datetime_end = new \DateTime($timeKey['end_time']);
                $days = $datetime_start->diff($datetime_end)->days;
                $days = $days > 0 ? $days : 1;
                if (!$isNum) {
                    $data['start_time'] = $timeKey['start_time'];
                    $data['end_time'] = $timeKey['end_time'];
                    $data['days'] = $days;
                } else {
                    $data['start_time'] = date("Y-m-d 00:00:00", strtotime("-$days day"));
                    $data['end_time'] = $timeKey['start_time'];
                    $data['days'] = $days;
                }

        }

        return $data;
    }

    /**
     * Lấy hoàn tiền đơn hàng
     * @param $where
     * @param string $selectType
     * @param string $group
     * @param bool $isNum
     * @return array|float|int
     * @throws \Exception
     */
    public function getOrderRefundTotalMoney($where, string $selectType, string $group = '', bool $isNum = false)
    {
        $orderSumField = isset($where['refund_type']) ? "refunded_price" : "refund_price";
        $whereOrderMoner['refund_type'] = isset($where['refund_type']) ? $where['refund_type'] : 6;
        $whereOrderMoner['is_cancel'] = 0;
        $whereOrderMoner['timeKey'] = $this->TimeConvert($where['time'], $isNum);

        /** @var StoreOrderRefundServices $storeOrderRefundServices */
        $storeOrderRefundServices = app()->make(StoreOrderRefundServices::class);
        $totalMoney = $storeOrderRefundServices->getOrderRefundMoneyByWhere($whereOrderMoner, $orderSumField, $selectType, $group);

        if ($group) {
            $totalMoney = $this->trendYdata($totalMoney, $whereOrderMoner['timeKey']);
        }
        return $totalMoney;
    }

    /**
     * Lấy doanh thu sản phẩm
     * @param $where
     * @param string $selectType
     * @param string $group
     * @param bool $isNum
     * @return array|float|int
     * @throws \Exception
     */
    public function getOrderTotalMoney($where, string $selectType, string $group = "", bool $isNum = false)
    {
        /** Số tiền thanh toán đơn hàng sản phẩm thường */
        /** @var StoreOrderServices $storeOrderService */
        $storeOrderService = app()->make(StoreOrderServices::class);
        $orderSumField = isset($where['refund_status']) ? "refund_price" : "pay_price";
        $whereOrderMoner['refund_status'] = isset($where['refund_status']) ? $where['refund_status'] : 0;
        $whereOrderMoner['paid'] = 1;
        $whereOrderMoner['pid'] = 0;

        if (isset($where['pay_type'])) {
            $whereOrderMoner['pay_type'] = $where['pay_type'];
        }
        $whereOrderMoner['timeKey'] = $this->TimeConvert($where['time'], $isNum);
        $totalMoney = $storeOrderService->getOrderMoneyByWhere($whereOrderMoner, $orderSumField, $selectType, $group);

        if ($group) {
            $totalMoney = $this->trendYdata($totalMoney, $whereOrderMoner['timeKey']);
        }
        return $totalMoney;
    }

    /**
     * Hoa hồng thanh toán
     * @param $where
     * @param string $selectType
     * @param string $group
     * @param bool $isNum
     * @return array|float|mixed
     * @throws \Exception
     */
    public function getExtractTotalMoney($where, string $selectType, string $group = "", bool $isNum = false)
    {
        /** Số tiền thanh toán đơn hàng sản phẩm thường */
        /** @var UserExtractServices $extractService */
        $extractService = app()->make(UserExtractServices::class);
        $orderSumField = "extract_price";
        $whereData['status'] = 1;
        $whereData['timeKey'] = $this->TimeConvert($where['time'], $isNum);
        $totalMoney = $extractService->getOutMoneyByWhere($whereData, $orderSumField, $selectType, $group);
        if ($group) {

            $totalMoney = $this->trendYdata($totalMoney, $whereData['timeKey']);

        }
        return $totalMoney;
    }

    /**
     * Lấy doanh thu nạp tiền người dùng
     * @param array $where
     * @param string $selectType
     * @param string $group
     * @param bool $isNum
     * @return array|float|int
     * @throws \Exception
     */
    public function getRechargeTotalMoney(array $where, string $selectType, string $group = "", bool $isNum = false)
    {
        /** @var UserRechargeServices $userRechageService */
        $userRechageService = app()->make(UserRechargeServices::class);
        $rechargeSumField = "price";
        $whereInRecharge['paid'] = 1;
        $whereInRecharge['refund_price'] = '0.00';
        $whereInRecharge['no_recharge_type'] = 'system';
        $whereInRecharge['timeKey'] = $this->TimeConvert($where['time'], $isNum);
        $whereInRecharge['store_id'] = 0;
        $totalMoney = $userRechageService->getRechargeMoneyByWhere($whereInRecharge, $rechargeSumField, $selectType, $group);
        if ($group) {
            $totalMoney = $this->trendYdata($totalMoney, $whereInRecharge['timeKey']);
        }
        return $totalMoney;
    }

    /**
     * Nạp tiền thủ công ở trang quản trị
     * @param array $where
     * @param string $selectType
     * @param string $group
     * @param bool $isNum
     * @return array|float|int
     * @throws \Exception
     */
    public function getBillYeTotalMoney(array $where, string $selectType, string $group = "", bool $isNum = false)
    {
        /** Số tiền nạp của người dùng ở trang quản trị */
        $rechargeSumField = "number";
        $whereInRecharge['pm'] = 1;
        $whereInRecharge['type'] = 'system_add';
        $whereInRecharge['timeKey'] = $this->TimeConvert($where['time'], $isNum);
        $whereInRecharge['store_id'] = 0;
        /** @var UserMoneyServices $userMoneyServices */
        $userMoneyServices = app()->make(UserMoneyServices::class);
        $totalMoney = $userMoneyServices->getRechargeMoneyByWhere($whereInRecharge, $rechargeSumField, $selectType, $group);
        if ($group) {
            $totalMoney = $this->trendYdata($totalMoney, $whereInRecharge['timeKey']);
        }
        return $totalMoney;
    }

    /**
     * Tổng số tiền mua thành viên
     * @param array $where
     * @param string $selectType
     * @param string $group
     * @param bool $isNum
     * @return array|mixed
     * @throws \Exception
     */
    public function getMemberTotalMoney(array $where, string $selectType, string $group = "", bool $isNum = false)
    {

        /** Mua gói thành viên */
        /** @var OtherOrderServices $otherOrderService */
        $otherOrderService = app()->make(OtherOrderServices::class);
        $memberSumField = "pay_price";
        $whereInMember['type'] = 1;
        $whereInMember['paid'] = 1;
        $whereInMember['store_id'] = 0;
        if (isset($where['pay_type'])) {
            $whereInMember['pay_type'] = $where['pay_type'];
        } else {
            //$whereInMember['pay_type_no'] = 'yue';
        }
        $whereInMember['timeKey'] = $this->TimeConvert($where['time'], $isNum);
        $totalMoney = $otherOrderService->getMemberMoneyByWhere($whereInMember, $memberSumField, $selectType, $group);
        if ($group) {
            $totalMoney = $this->trendYdata($totalMoney, $whereInMember['timeKey']);
        }
        return $totalMoney;

    }

    /**
     * Tổng số tiền thanh toán ngoại tuyến
     * @param array $where
     * @param string $selectType
     * @param string $group
     * @param bool $isNum
     * @return array|mixed
     * @throws \Exception
     */
    public function getOfflineTotalMoney(array $where, string $selectType, string $group = "", bool $isNum = false)
    {
        /** Tổng số tiền thanh toán ngoại tuyến */
        /** @var OtherOrderServices $otherOrderService */
        $otherOrderService = app()->make(OtherOrderServices::class);
        $offlineSumField = "pay_price";
        $whereOffline['type'] = 3;
        $whereOffline['paid'] = 1;
        $whereOffline['store_id'] = 0;
        // $whereOffline['pay_type_no'] = 'yue';
        $whereOffline['timeKey'] = $this->TimeConvert($where['time'], $isNum);
        $totalMoney = $otherOrderService->getMemberMoneyByWhere($whereOffline, $offlineSumField, $selectType, $group);
        if ($group) {
            $totalMoney = $this->trendYdata($totalMoney, $whereOffline['timeKey']);
        }
        return $totalMoney;
    }

    /**
     * Xử lý dữ liệu tọa độ Y
     * @param array $data
     * @param array $timeKey
     * @return array
     * @throws \Exception
     */
    public function trendYdata(array $data, array $timeKey)
    {
        $hourMoney = array();
        $timeData = array();
        //Lấy số ngày giữa hai ngày
        $getDayRange = function ($date, $timeKey) {
            $datearr = [];
            $stime = strtotime($timeKey['start_time']);
            $etime = strtotime($timeKey['end_time']);
            while ($stime <= $etime) {
                $datearr['x'][] = date($date, $stime);
                $datearr['y'][] = date($date, $stime);
                $stime = $stime + 86400;
            }
            return $datearr;
        };
        //Lấy số tháng giữa hai ngày
        $getMonthRange = function ($date, $timeKey) {
            $datearr = [];
            $stime = date('Y-m-d', strtotime($timeKey['start_time']));
            $etime = date('Y-m-d', strtotime($timeKey['end_time']));
            $start = new \DateTime($stime);
            $end = new \DateTime($etime);
            $interval = \DateInterval::createFromDateString('1 month');
            $period = new \DatePeriod($start, $interval, $end);
            foreach ($period as $dt) {
                $datearr['x'][] = $dt->format($date);
                $datearr['y'][] = $dt->format($date);
            }
            return $datearr;
        };
        if ($timeKey['days'] == 1) {
            for ($i = 0; $i <= 24; $i++) {
                $timeData['x'][] = (string)($i < 10 ? ('0' . $i) : $i);
                $timeData['y'][] = $i < 10 ? ('0' . $i) : $i;
                //$timeData['y'][] = $i < 10 ? ('0' . $i . ":00") : $i . ":00";
                //$timeData['x'][] = $i < 10 ? ('0' . $i . ":00") : $i . ":00";
            }
        } elseif ($timeKey['days'] == 30) {
            $timeData = $getDayRange('Y-m-d', $timeKey);
        } elseif ($timeKey['days'] == 365) {
            $timeData = $getMonthRange('Y-m', $timeKey);
        } elseif ($timeKey['days'] > 1 && $timeKey['days'] < 30) {
            $timeData = $getDayRange('Y-m-d', $timeKey);
        } elseif ($timeKey['days'] > 30 && $timeKey['days'] < 365) {
            $timeData = $getMonthRange('Y-m', $timeKey);
        }
        if ($data) {
            $hourMoney = array_column($data, 'number', 'time');
        }
        $y = array();
        foreach ($timeData['y'] as $k => $v) {
            if (array_key_exists($v, $hourMoney)) {
                $y[$v] = $hourMoney[$v];
            } else {
                $y[$v] = 0;
            }
        }
        return ['x' => $timeData['x'], 'y' => $y];
    }

    /**
     * Tính tỷ lệ tăng trưởng so với kỳ trước
     * @param $nowValue
     * @param $lastValue
     * @return float|int|string
     */
    public function countRate($nowValue, $lastValue)
    {
        if ($lastValue == 0 && $nowValue == 0) return 0;
        if ($lastValue == 0) return round(bcmul(bcdiv($nowValue, 1, 4), 100, 2), 2);
        if ($nowValue == 0) return -100;
        return bcmul(bcdiv((bcsub($nowValue, $lastValue, 2)), $lastValue, 2), 100, 2);
    }

    /**
     * Lấy loại thời gian so với kỳ trước
     * @param $timeKey
     * @return string
     */
    public function chainTime($timeKey)
    {
        switch ($timeKey) {
            case "today" :
                return "yestoday";
            case "month" :
                return "last_month";
            case "year" :
                return "last_year";
            default :
                return "other";
        }

    }


}
