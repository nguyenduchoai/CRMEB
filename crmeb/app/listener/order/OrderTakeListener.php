<?php


namespace app\listener\order;

use app\services\order\StoreOrderStatusServices;
use app\services\order\StoreOrderTakeServices;
use app\services\user\UserBillServices;
use crmeb\interfaces\ListenerInterface;
use think\facade\Log;

/**
 * Xác nhận đã nhận hàng cho đơn hàng
 * Class OrderTakeListener
 * @package app\listener\order
 */
class OrderTakeListener implements ListenerInterface
{
    public function handle($event): void
    {
        [$order, $userInfo, $storeTitle] = $event;
        try {
            //Cập nhật trạng thái nhận hàng
            /** @var UserBillServices $userBillServices */
            $userBillServices = app()->make(UserBillServices::class);
            $userBillServices->takeUpdate((int)$order['uid'], (int)$order['id']);

            //Thêm trạng thái đơn hàng đã nhận
            /** @var StoreOrderStatusServices $statusService */
            $statusService = app()->make(StoreOrderStatusServices::class);
            $statusService->save([
                'oid' => $order['id'],
                'change_type' => 'take_delivery',
                'change_message' => 'Đã nhận hàng',
                'change_time' => time()
            ]);

            //Kiểm tra đơn hàng chính có cần cập nhật trạng thái không
            if ($order['pid'] > 0) {
                /** @var StoreOrderTakeServices $storeOrderTake */
                $storeOrderTake = app()->make(StoreOrderTakeServices::class);
                $storeOrderTake->checkMaster($order['pid']);
            }
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }
}
