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

namespace app\jobs;

use app\services\order\OtherOrderServices;
use app\services\order\StoreOrderEconomizeServices;
use app\services\user\member\MemberCardServices;
use app\services\user\UserServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

/**
 * Hàng đợi tin nhắn đơn hàng
 * Class OrderJob
 * @package crmeb\jobs
 */
class OtherOrderJob extends BaseJobs
{
    use QueueTrait;

    /**
     * Thực hiện gửi tin nhắn khi đơn hàng thanh toán thành công
     * @param $order
     * @return bool
     */
    public function doJob($order)
    {
        //Cập nhật số đơn hàng đã thanh toán của người dùng
        try {
            $this->setUserPayCountAndPromoter($order);
        } catch (\Throwable $e) {
            Log::error('Cập nhật số đơn hàng của người dùng thất bại, nguyên nhân:' . $e->getMessage());
        }

        // Tính số tiền tiết kiệm của người dùng
        try {
            $this->setEconomizeMoney($order);
        } catch (\Throwable $e) {
            Log::error('Tính số tiền tiết kiệm thất bại, nguyên nhân:' . $e->getMessage());
        }

        //Tặng điểm thưởng cho đơn hàng thu ngân
        try {
            $this->sendMemberIntegral($order);
        } catch (\Throwable $e) {
            Log::error('Hoàn trả điểm thưởng đã dùng thất bại, nguyên nhân:' . $e->getMessage());
        }
        return true;
    }

    /**
     * Thiết lập số lần mua của người dùng và kiểm tra thời điểm trở thành người giới thiệu
     * @param $order
     */
    public function setUserPayCountAndPromoter($order)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->get($order['uid']);
        if ($userInfo) {
            $userInfo->pay_count = $userInfo->pay_count + 1;
            if (!$userInfo->is_promoter) {
                /** @var OtherOrderServices $orderServices */
                $orderServices = app()->make(OtherOrderServices::class);
                $price = $orderServices->sum(['paid' => 1, 'uid' => $userInfo['uid']], 'pay_price');
                $status = is_brokerage_statu($price);
                if ($status) {
                    $userInfo->is_promoter = 1;
                }
            }
            $userInfo->save();
        }
    }

    /** Tặng điểm thưởng khi thanh toán ngoại tuyến
     * @param $order
     * @return bool
     */
    public function sendMemberIntegral($order)
    {
        //Chỉ tặng điểm khi thanh toán ngoại tuyến
        if ($order['type'] == 3) {
            $order_give_integral = sys_config('order_give_integral');
            $order_integral = bcmul($order_give_integral, (string)$order['pay_price'], 0);
            /** @var UserServices $userService */
            $userService = app()->make(UserServices::class);
            $userInfo = $userService->getUserInfo($order['uid']);
            if (!$userInfo) return false;
            if ($userInfo['is_money_level'] > 0) {
                //Kiểm tra có mở thưởng nhân đôi điểm hoàn khi chi tiêu hay không
                /** @var MemberCardServices $memberCardService */
                $memberCardService = app()->make(MemberCardServices::class);
                $integral_rule_number = $memberCardService->isOpenMemberCard('integral');
                if ($integral_rule_number) {
                    $order_integral = bcadd($order_integral, $integral_rule_number, 2);
                }
            }
            if ($order_integral > 0) {
                $integral = bcadd(abs($userInfo['integral']), abs($order_integral), 2);
                $userService->update(['uid' => $order['uid']], ['integral' => $integral]);
            }
        }
    }

    /**
     * Tính số tiền tiết kiệm
     * @param $order
     */
    public function setEconomizeMoney($order)
    {
        //Chỉ tính tiết kiệm khi thanh toán ngoại tuyến
        if ($order['type'] == 3) {
            /** @var StoreOrderEconomizeServices $economizeService */
            $economizeService = app()->make(StoreOrderEconomizeServices::class);
            /** @var MemberCardServices $memberRightService */
            $memberRightService = app()->make(MemberCardServices::class);
            $isOpenOfflin = $memberRightService->isOpenMemberCard('offline');
            if ($isOpenOfflin) {
                $save = [
                    'uid' => $order['uid'],
                    'order_id' => $order['order_id'],
                    'order_type' => 2,
                    'pay_price' => $order['pay_price'],
                    'offline_price' => bcsub($order['money'], $order['pay_price'], 2)
                ];
                $economizeService->addEconomize($save);
            }

        }
    }
}
