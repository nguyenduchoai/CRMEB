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


use app\services\activity\seckill\StoreSeckillServices;
use app\services\order\StoreOrderCartInfoServices;
use app\services\order\StoreOrderRefundServices;
use app\services\order\StoreOrderServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

/**
 * Đơn hàng chưa thanh toán tự hủy khi hết hạn
 * Class UnpaidOrderCancelJob
 * @package crmeb\jobs
 */
class UnpaidOrderCancelJob extends BaseJobs
{

    use QueueTrait;

    public function doJob($orderId)
    {
        /** @var StoreOrderServices $services */
        $services = app()->make(StoreOrderServices::class);
        $orderInfo = $services->get($orderId);
        if (!$orderInfo) {
            return true;
        }
        if ($orderInfo->paid) {
            return true;
        }
        if ($orderInfo->is_del) {
            return true;
        }
        if ($orderInfo->pay_type == 'offline') {
            return true;
        }
        if ($orderInfo->is_cancel == 1) {
            return true;
        }
        /** @var StoreOrderCartInfoServices $cartServices */
        $cartServices = app()->make(StoreOrderCartInfoServices::class);
        $cartInfo = $cartServices->getOrderCartInfo($orderId);
        /** @var StoreOrderRefundServices $refundServices */
        $refundServices = app()->make(StoreOrderRefundServices::class);

        try {
            $res = $refundServices->transaction(function () use ($orderInfo, $refundServices) {
                //Hoàn trả điểm thưởng và phiếu giảm giá
                $refundServices->integralAndCouponBack($orderInfo, 'cancel');
                //Hoàn trả tồn kho và lượt bán
                $refundServices->regressionStock($orderInfo);
                return true;
            });
            if ($res) {
                $orderInfo->is_cancel = 1;
                $orderInfo->mark = 'Đơn hàng chưa thanh toán đã quá thời gian hệ thống quy định';
                $orderInfo->save();
            }
            return $res;
        } catch (\Throwable $e) {
            Log::error('Tự động hủy đơn hàng thất bại, nguyên nhân:' . $e->getMessage());
            return false;
        }
    }
}
