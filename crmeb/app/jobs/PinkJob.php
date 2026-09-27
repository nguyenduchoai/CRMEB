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


use app\services\activity\combination\StorePinkServices;
use app\services\order\StoreOrderRefundServices;
use app\services\order\StoreOrderServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class PinkJob extends BaseJobs
{
    use QueueTrait;

    public function doJob($pinkId)
    {
        /** @var StorePinkServices $pinkService */
        $pinkService = app()->make(StorePinkServices::class);
        $people = $pinkService->value(['id' => $pinkId], 'people');
        $count = $pinkService->count(['k_id' => $pinkId, 'is_refund' => 0]) + 1;
        $orderIds = $pinkService->getColumn([['id|k_id', '=', $pinkId]], 'order_id_key', 'uid');
        if ($people > $count) {
            $virtual = $pinkService->virtualCombination($pinkId);
            if ($virtual) return true;
            $refundData = [
                'refund_reason' => 'Hết thời gian mua chung',
                'refund_explain' => 'Hết thời gian mua chung',
                'refund_img' => json_encode([]),
            ];
            foreach ($orderIds as $key => $item) {
                /** @var StoreOrderServices $orderService */
                $orderService = app()->make(StoreOrderServices::class);
                $order = $orderService->get($item);

                /** @var StoreOrderRefundServices $orderRefundService */
                $orderRefundService = app()->make(StoreOrderRefundServices::class);
                $orderRefundService->applyRefund((int)$order['id'], (int)$order['uid'], $order, [], 1, (float)$order['pay_price'], $refundData, 1);

                $pinkService->update([['id|k_id', '=', $pinkId]], ['status' => 3]);
                $pinkService->orderPinkAfterNo($key, $pinkId, false, $order->is_channel);
            }
            return true;
        }
        return true;
    }
}
