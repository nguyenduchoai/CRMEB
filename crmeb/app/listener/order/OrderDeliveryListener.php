<?php


namespace app\listener\order;


use app\jobs\TakeOrderJob;
use crmeb\interfaces\ListenerInterface;

/**
 * Đơn hàng tự động xác nhận nhận hàng khi hết hạn
 * Class OrderDeliveryListener
 * @package app\listener\order
 */
class OrderDeliveryListener implements ListenerInterface
{
    public function handle($event): void
    {
        [$orderInfo, $storeTitle, $data, $type] = $event;

        //Tự động xác nhận nhận hàng khi hết hạn
        $time = sys_config('system_delivery_time') ?? 0;
        if ($time != 0) {
            $sevenDay = 24 * 3600 * $time;
            TakeOrderJob::dispatchSecs((int)$sevenDay, [$orderInfo->id]);
        }
    }
}
