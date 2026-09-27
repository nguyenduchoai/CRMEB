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

namespace app\jobs;


use app\services\order\StoreOrderServices;
use app\services\order\StoreOrderStatusServices;
use app\services\order\StoreOrderTakeServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

/**
 * Hàng đợi tin nhắn tự động nhận hàng
 * Class TakeOrderJob
 * @package crmeb\jobs
 */
class TakeOrderJob extends BaseJobs
{
    use QueueTrait;

    /**
     * @param $id
     * @return bool
     */
    public function doJob($id)
    {
        /** @var StoreOrderTakeServices $services */
        $services  = app()->make(StoreOrderTakeServices::class);
        $orderInfo = $services->get($id);
        if (!$orderInfo) {
            return true;
        }
        if (!$orderInfo->paid) {
            return true;
        }
        if ($orderInfo->refund_status == 2) {
            return true;
        }
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $order         = $orderServices->tidyOrder($orderInfo);
        if ($order['_status']['_type'] != 2) {
            return true;
        }
        $orderInfo->status = 2;
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        $res           = $orderInfo->save() && $statusService->save([
                'oid'            => $orderInfo['id'],
                'change_type'    => 'take_delivery',
                'change_message' => 'Đã nhận hàng [Tự động nhận hàng]',
                'change_time'    => time()
            ]);
        try {
            $services->storeProductOrderUserTakeDelivery($order);
        } catch (\Throwable $e) {
            Log::error('Hàng đợi tin nhắn tự động nhận hàng thực thi thành công, thao tác sau khi nhận hàng thất bại, nguyên nhân:' . $e->getMessage());
        }
        return $res;
    }

}
