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
            $sevenDay = $sevenDay + 180;
            TakeOrderJob::dispatchSecs((int)$sevenDay, [$orderInfo->id]);
        }
    }
}
