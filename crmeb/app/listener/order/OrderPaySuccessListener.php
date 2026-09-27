<?php


namespace app\listener\order;


use app\jobs\AgentJob;
use app\jobs\notice\PrintJob;
use app\jobs\OrderInvoiceJob;
use app\jobs\OrderJob;
use app\jobs\ProductLogJob;
use app\services\activity\seckill\StoreSeckillServices;
use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\order\StoreOrderCartInfoServices;
use app\services\order\StoreOrderDeliveryServices;
use app\services\order\StoreOrderInvoiceServices;
use app\services\order\StoreOrderServices;
use app\services\order\StoreOrderStatusServices;
use app\services\pay\PayServices;
use app\services\product\product\StoreProductCouponServices;
use app\services\product\sku\StoreProductAttrValueServices;
use app\services\product\sku\StoreProductVirtualServices;
use app\services\message\MessageSystemServices;
use app\services\statistic\CapitalFlowServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;
use crmeb\interfaces\ListenerInterface;
use think\facade\Log;

/**
 * Sau khi đơn hàng thanh toán thành công
 * Class OrderPaySuccessListener
 * @package app\listener\order
 */
class OrderPaySuccessListener implements ListenerInterface
{
    public function handle($event): void
    {
        [$orderInfo] = $event;

        //Event ghi nhận trạng thái đơn hàng
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        $statusService->save([
            'oid' => $orderInfo['id'],
            'change_type' => 'pay_success',
            'change_message' => 'Người dùng thanh toán thành công',
            'change_time' => time()
        ]);

        //Tặng phiếu giảm giá cho sản phẩm đã mua, chỉ tặng với đơn hàng sản phẩm thường
        if (!$orderInfo['seckill_id'] && !$orderInfo['bargain_id'] && !$orderInfo['combination_id']) {
            /** @var StoreProductCouponServices $storeProductCouponServices */
            $storeProductCouponServices = app()->make(StoreProductCouponServices::class);
            $storeProductCouponServices->giveOrderProductCoupon((int)$orderInfo['uid'], $orderInfo['id']);
        }

        //Cập nhật trạng thái thanh toán của dữ liệu xuất hóa đơn
        $orderInvoiceServices = app()->make(StoreOrderInvoiceServices::class);
        $invoiceInfo = $orderInvoiceServices->get(['order_id' => $orderInfo['id']]);
        if ($invoiceInfo) {
            $invoiceInfo->is_pay = 1;
            if ($invoiceInfo->save() && sys_config('elec_invoice', 1) == 1 && sys_config('auto_invoice', 1) == 1) {
                //Tự động xuất hóa đơn
                OrderInvoiceJob::dispatchSecs(10, 'autoInvoice', [$invoiceInfo['id']]);
            }
        }

        //Tự động giao hàng cho sản phẩm ảo
        if (in_array($orderInfo['virtual_type'], [1, 2]) && $orderInfo['combination_id'] == 0) {
            /** @var StoreOrderDeliveryServices $orderDeliveryServices */
            $orderDeliveryServices = app()->make(StoreOrderDeliveryServices::class);
            $orderDeliveryServices->virtualSend($orderInfo);
        }

        // Ghi vào dòng tiền
        if (in_array($orderInfo['pay_type'], ['weixin', 'alipay', 'allinpay'])) {
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $userInfo = $userServices->get($orderInfo['uid']);
            /** @var CapitalFlowServices $capitalFlowServices */
            $capitalFlowServices = app()->make(CapitalFlowServices::class);
            $orderInfo['nickname'] = $userInfo['nickname'];
            $orderInfo['phone'] = $userInfo['phone'];
            $capitalFlowServices->setFlow($orderInfo, 'order');
        }

        //In biên lai
        PrintJob::dispatch([$orderInfo['id'], 1]);

        //Gửi tin nhắn sau khi thanh toán thành công
        OrderJob::dispatch([$orderInfo]);

        //Sau khi thanh toán thành công, xử lý nâng hạng cộng tác viên cho bản thân và cấp trên
        AgentJob::dispatch([(int)$orderInfo['uid']]);

        //Ghi log sản phẩm và lịch sử thanh toán
        ProductLogJob::dispatch(['pay', ['uid' => $orderInfo['uid'], 'order_id' => $orderInfo['id']]]);
    }
}
