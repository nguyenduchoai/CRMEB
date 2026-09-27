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

namespace app\services\pay;


use app\services\activity\combination\StorePinkServices;
use app\services\BaseServices;
use app\services\order\StoreOrderDeliveryServices;
use app\services\order\StoreOrderInvoiceServices;
use app\services\order\StoreOrderServices;
use app\services\order\StoreOrderStatusServices;
use app\jobs\ProductLogJob;
use app\services\user\UserServices;
use app\services\statistic\CapitalFlowServices;
use crmeb\exceptions\ApiException;

/**
 * Thanh toán ngoại tuyến
 * Class OrderOfflineServices
 * @package app\services\pay
 */
class OrderOfflineServices extends BaseServices
{

    /**
     * Thanh toán ngoại tuyến
     * @param int $id
     * @return mixed
     */
    public function orderOffline(int $id)
    {
        /** @var StoreOrderServices $orderSerives */
        $orderSerives = app()->make(StoreOrderServices::class);
        $orderInfo = $orderSerives->get($id);
        if (!$orderInfo) {
            throw new ApiException('Đơn hàng không tồn tại');
        }

        if ($orderInfo->paid) {
            throw new ApiException('Đơn hàng đã được thanh toán');
        }
        $orderInfo->paid = 1;
        $orderInfo->pay_time = time();
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        $res = $statusService->save([
            'oid' => $id,
            'change_type' => 'offline',
            'change_message' => 'Thanh toán ngoại tuyến',
            'change_time' => time()
        ]);
        //Cập nhật trạng thái thanh toán của dữ liệu xuất hóa đơn
        $orderInvoiceServices = app()->make(StoreOrderInvoiceServices::class);
        $orderInvoiceServices->update(['order_id' => $orderInfo['id']], ['is_pay' => 1]);

        /** @var CapitalFlowServices $capitalFlowServices */
        $capitalFlowServices = app()->make(CapitalFlowServices::class);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->get($orderInfo['uid']);
        $orderInfo['nickname'] = $userInfo['nickname'];
        $orderInfo['phone'] = $userInfo['phone'];
        $capitalFlowServices->setFlow($orderInfo, 'order');

        // Đơn hàng mua chung tạo nhóm mua chung
        if ($orderInfo['combination_id']) {
            $tidyOrder = app()->make(StoreOrderServices::class)->tidyOrder($orderInfo->toArray(), true);
            app()->make(StorePinkServices::class)->createPink($tidyOrder);
        }

        //Tự động giao hàng cho sản phẩm ảo
        if (in_array($orderInfo['virtual_type'], [1, 2])) {
            /** @var StoreOrderDeliveryServices $orderDeliveryServices */
            $orderDeliveryServices = app()->make(StoreOrderDeliveryServices::class);
            $orderDeliveryServices->virtualSend($orderInfo);
        }

        //Bản ghi thanh toán
        ProductLogJob::dispatch(['pay', ['uid' => $orderInfo['uid'], 'order_id' => $orderInfo['id']]]);
        return $res && $orderInfo->save();
    }
}
