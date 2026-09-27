<?php


namespace app\listener\order;


use app\jobs\notice\PrintJob;
use app\jobs\OrderCreateAfterJob;
use app\jobs\OrderJob;
use app\jobs\ProductLogJob;
use app\jobs\UnpaidOrderCancelJob;
use app\jobs\UnpaidOrderSend;
use app\services\order\StoreOrderCreateServices;
use app\services\order\StoreOrderStatusServices;
use crmeb\interfaces\ListenerInterface;
use crmeb\services\CacheService;
use crmeb\services\SystemConfigService;
use crmeb\utils\Arr;

/**
 * Event sau khi tạo đơn hàng
 * Class OrderCreateAfterListener
 * @package app\listener\order
 */
class OrderCreateAfterListener implements ListenerInterface
{
    public function handle($event): void
    {
        [$order, $group, $uid, $key, $combinationId, $seckillId, $bargainId] = $event;

        //Sau khi tạo dữ liệu đơn hàng: tính số tiền thực tế sản phẩm, tính hoa hồng, tính chiết khấu ưu đãi, thiết lập địa chỉ mặc định, dọn giỏ hàng
        /** @var StoreOrderCreateServices $orderCreate */
        $orderCreate = app()->make(StoreOrderCreateServices::class);
        $orderCreate->orderCreateAfter($order, $group, $combinationId || $seckillId || $bargainId);

        //Xóa bộ nhớ đệm đơn hàng
        CacheService::delete('user_order_' . $uid . $key);

        //Ghi vào bảng lịch sử đơn hàng
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        $statusService->save([
            'oid' => $order['id'],
            'change_type' => 'cache_key_create_order',
            'change_message' => 'Tạo đơn hàng',
            'change_time' => time()
        ]);

        //Tự động hủy đơn hàng
        $this->pushJob($order['id'], $combinationId, $seckillId, $bargainId);

        //Tính số tiền thực tế của đơn hàng
        //OrderCreateAfterJob::dispatch([$order, $group, $combinationId || $seckillId || $bargainId]);

        //Lịch sử đặt hàng
        ProductLogJob::dispatch(['order', ['uid' => $uid, 'order_id' => $order['id']]]);

        //In biên lai
        PrintJob::dispatch([$order['id'], 2]);
    }

    /**
     * Thêm hủy đơn hàng tự động vào hàng đợi tin nhắn trễ
     * @param int $orderId
     * @param int $combinationId
     * @param int $seckillId
     * @param int $bargainId
     * @return mixed
     */
    public function pushJob(int $orderId, int $combinationId, int $seckillId, int $bargainId)
    {
        //Khoảng thời gian hủy đơn hàng do hệ thống định trước
        $keyValue = ['order_cancel_time', 'order_activity_time', 'order_bargain_time', 'order_seckill_time', 'order_pink_time'];
        //Lấy cấu hình
        $systemValue = SystemConfigService::more($keyValue);
        //Định dạng dữ liệu
        $systemValue = Arr::setValeTime($keyValue, is_array($systemValue) ? $systemValue : []);
        if ($combinationId) {
            $secs = $systemValue['order_pink_time'] ?: $systemValue['order_activity_time'];
        } elseif ($seckillId) {
            $secs = $systemValue['order_seckill_time'] ?: $systemValue['order_activity_time'];
        } elseif ($bargainId) {
            $secs = $systemValue['order_bargain_time'] ?: $systemValue['order_activity_time'];
        } else {
            $secs = $systemValue['order_cancel_time'];
        }
        //Gửi SMS sau 10 phút chưa thanh toán
        UnpaidOrderSend::dispatchSecs(600, [$orderId]);
        //Chưa thanh toán thì hủy đơn hàng theo event cấu hình hệ thống
        UnpaidOrderCancelJob::dispatchSecs((int)($secs * 3600), [$orderId]);
    }
}
