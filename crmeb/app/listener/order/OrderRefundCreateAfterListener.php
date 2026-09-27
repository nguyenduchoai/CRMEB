<?php

namespace app\listener\order;

use app\jobs\RefundOrderJob;
use app\services\order\OutStoreOrderRefundServices;
use crmeb\interfaces\ListenerInterface;

/**
 * Tạo đơn hậu mãi
 * Class orderRefundCreateAfter
 * @package app\listener\order
 */
class OrderRefundCreateAfterListener implements ListenerInterface
{
    public function handle($event): void
    {
        [$order] = $event;
    }
}
