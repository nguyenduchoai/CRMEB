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

namespace app\services\order;

use app\jobs\MiniOrderJob;
use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\activity\integral\StoreIntegralOrderServices;
use app\services\BaseServices;
use app\dao\order\StoreOrderDao;
use app\services\message\MessageSystemServices;
use app\services\product\sku\StoreProductAttrValueServices;
use app\services\product\sku\StoreProductVirtualServices;
use app\services\serve\ServeServices;
use app\services\wechat\WechatUserServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\services\FormBuilder as Form;
use app\services\shipping\ExpressServices;
use think\facade\Log;

/**
 * Giao đơn hàng
 * Class StoreOrderDeliveryServices
 * @package app\services\order
 */
class StoreOrderDeliveryServices extends BaseServices
{
    /**
     * Phương thức khởi tạo
     * StoreOrderDeliveryServices constructor.
     * @param StoreOrderDao $dao
     */
    public function __construct(StoreOrderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Giao đơn hàng
     * @param int $id
     * @param array $data
     * @return array
     */
    public function delivery(int $id, array $data)
    {
        $orderInfo = $this->dao->get($id, ['*'], ['pink']);
        if (!$orderInfo) {
            throw new AdminException(400470);
        }
        if ($orderInfo->is_del) {
            throw new AdminException(400471);
        }
        if ($orderInfo->status) {
            throw new AdminException(400472);
        }
        if ($orderInfo->shipping_type == 2) {
            throw new AdminException(400473);
        }
        if (isset($orderInfo['pinkStatus']) && $orderInfo['pinkStatus'] != 2) {
            throw new AdminException(400474);
        }

        if ($data['type'] == 1) {
            // Kiểm tra mã đơn vị vận chuyển
            /** @var ExpressServices $expressServices */
            $expressServices = app()->make(ExpressServices::class);
            if (!$expressServices->be(['code' => $data['delivery_code']])) {
                throw new AdminException(410324);
            }
        }

        /** @var StoreOrderRefundServices $storeOrderRefundServices */
        $storeOrderRefundServices = app()->make(StoreOrderRefundServices::class);
        if ($storeOrderRefundServices->count(['store_order_id' => $id, 'refund_type' => [1, 2, 4, 5], 'is_cancel' => 0, 'is_del' => 0])) {
            throw new AdminException(400475);
        }
        return $this->doDelivery($id, $orderInfo, $data);
    }

    /**
     * Đơn hàng giao qua vận chuyển
     * @param int $id
     * @param array $data
     */
    public function orderDeliveryGoods(int $id, array $data, $orderInfo, $storeTitle)
    {
        /** @var StoreOrderCartInfoServices $orderInfoServices */
        $orderInfoServices = app()->make(StoreOrderCartInfoServices::class);
        if (!$data['delivery_name']) {
            throw new AdminException(400007);
        }
        $data['delivery_type'] = 'express';
        if ($data['express_record_type'] == 2) {//Vận đơn điện tử
            if (!$data['delivery_code']) {
                throw new AdminException(400476);
            }
            if (!$data['express_temp_id']) {
                throw new AdminException(400527);
            }
            if (!$data['to_name']) {
                throw new AdminException(400008);
            }
            if (!$data['to_tel']) {
                throw new AdminException(400477);
            }
            if (!$data['to_addr']) {
                throw new AdminException(400478);
            }
            /** @var ServeServices $expressService */
            $expressService = app()->make(ServeServices::class);
            $expData['com'] = $data['delivery_code'];
            $expData['to_name'] = $orderInfo->real_name;
            $expData['to_tel'] = $orderInfo->user_phone;
            $expData['to_addr'] = $orderInfo->user_address;
            $expData['from_name'] = $data['to_name'];
            $expData['from_tel'] = $data['to_tel'];
            $expData['from_addr'] = $data['to_addr'];
            $expData['siid'] = sys_config('config_export_siid');
            $expData['temp_id'] = $data['express_temp_id'];
            $expData['count'] = $orderInfo->total_num;
            $expData['cargo'] = $orderInfoServices->getCarIdByProductTitle((int)$orderInfo->id, true);
            $expData['order_id'] = $orderInfo->order_id;
            if (!sys_config('config_export_open', 0)) {
                throw new AdminException(400528);
            }
            $dump = $expressService->express()->dump($expData);
            $orderInfo->delivery_id = $dump['kuaidinum'];
            $data['express_dump'] = json_encode([
                'com' => $expData['com'],
                'from_name' => $expData['from_name'],
                'from_tel' => $expData['from_tel'],
                'from_addr' => $expData['from_addr'],
                'temp_id' => $expData['temp_id'],
                'cargo' => $expData['cargo'],
            ]);
            $data['delivery_id'] = $dump['kuaidinum'];
        } else {
            if (!$data['delivery_id']) {
                throw new AdminException(400531);
            }
            $orderInfo->delivery_id = $data['delivery_id'];
        }
        $data['status'] = 1;
        $orderInfo->delivery_type = $data['delivery_type'];
        $orderInfo->delivery_name = $data['delivery_name'];
        $orderInfo->status = $data['status'];
        /** @var StoreOrderStatusServices $services */
        $services = app()->make(StoreOrderStatusServices::class);
        $this->transaction(function () use ($id, $data, $services) {
            $res = $this->dao->update($id, $data);
            $res = $res && $services->save([
                    'oid' => $id,
                    'change_time' => time(),
                    'change_type' => 'delivery_goods',
                    'change_message' => 'Đã giao hàng. Đơn vị vận chuyển:' . $data['delivery_name'] . ' Mã vận đơn:' . $data['delivery_id']
                ]);
            if (!$res) {
                throw new AdminException(400529);
            }
        });
        return true;
    }


    /**
     * Vận chuyển đơn hàng
     * @param int $id
     * @param array $data
     */
    public function orderDelivery(int $id, array $data, $orderInfo, string $storeTitle)
    {
        $data['delivery_type'] = 'send';
        $data['delivery_name'] = $data['sh_delivery_name'];
        $data['delivery_id'] = $data['sh_delivery_id'];
        $data['delivery_uid'] = $data['sh_delivery_uid'];
        $data['shipping_type'] = 1;
        //Lấy mã xác nhận
        /** @var StoreOrderCreateServices $storeOrderCreateService */
        $storeOrderCreateService = app()->make(StoreOrderCreateServices::class);
        $data['verify_code'] = $storeOrderCreateService->getStoreCode();
        unset($data['sh_delivery_name'], $data['sh_delivery_id'], $data['sh_delivery_uid']);
        if (!$data['delivery_name']) {
            throw new AdminException(400523);
        }
        if (!$data['delivery_id']) {
            throw new AdminException(400524);
        }
        if (!$data['delivery_uid']) {
            throw new AdminException(400525);
        }
        if (!preg_match("/^1[3456789]{1}\d{9}$/", $data['delivery_id'])) {
            throw new AdminException(400526);
        }
        $data['status'] = 1;
        $orderInfo->delivery_type = $data['delivery_type'];
        $orderInfo->delivery_name = $data['delivery_name'];
        $orderInfo->delivery_id = $data['delivery_id'];
        $orderInfo->status = $data['status'];
        /** @var StoreOrderStatusServices $services */
        $services = app()->make(StoreOrderStatusServices::class);
        $this->transaction(function () use ($id, $data, $services) {
            $this->dao->update($id, $data);
            //Ghi lại trạng thái đơn hàng
            $services->save([
                'oid' => $id,
                'change_type' => 'delivery',
                'change_time' => time(),
                'change_message' => 'Đã giao hàng, người giao:' . $data['delivery_name'] . ' SĐT người giao:' . $data['delivery_id']
            ]);
        });
        return true;
    }

    /**
     * Giao hàng ảo
     * @param int $id
     * @param array $data
     */
    public function orderVirtualDelivery(int $id, array $data)
    {
        $data['delivery_type'] = 'fictitious';
        $data['status'] = 1;
        unset($data['sh_delivery_name'], $data['sh_delivery_id'], $data['delivery_name'], $data['delivery_id']);
        //Lưu thông tin
        /** @var StoreOrderStatusServices $services */
        $services = app()->make(StoreOrderStatusServices::class);
        $this->transaction(function () use ($id, $data, $services) {
            $this->dao->update($id, $data);
            $services->save([
                'oid' => $id,
                'change_type' => 'delivery_fictitious',
                'change_message' => 'Đã giao hàng ảo',
                'change_time' => time()
            ]);
        });
    }

    /**
     * Lấy cấu trúc form sửa thông tin giao hàng
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function distributionForm(int $id)
    {
        if (!$orderInfo = $this->dao->get($id))
            throw new AdminException(400118);

        $f[] = Form::input('order_id', 'Mã đơn hàng', $orderInfo->getData('order_id'))->disabled(1);

        switch ($orderInfo['delivery_type']) {
            case 'send':
                $f[] = Form::input('delivery_name', 'Họ tên người giao hàng', $orderInfo->getData('delivery_name'))->required('Vui lòng nhập họ tên người giao hàng');
                $f[] = Form::input('delivery_id', 'Số điện thoại người giao hàng', $orderInfo->getData('delivery_id'))->required('Vui lòng nhập số điện thoại người giao hàng');
                break;
            case 'express':
                /** @var ExpressServices $expressServices */
                $expressServices = app()->make(ExpressServices::class);
                $f[] = Form::select('delivery_code', 'Đơn vị vận chuyển', (string)$orderInfo->getData('delivery_code'))->setOptions($expressServices->expressSelectForm(['is_show' => 1]))->required('Vui lòng chọn đơn vị vận chuyển')->filterable(true);
                $f[] = Form::input('delivery_id', 'Mã vận đơn', $orderInfo->getData('delivery_id'))->required('Vui lòng nhập mã vận đơn');
                break;
        }
        return create_form('Thông tin giao hàng', $f, $this->url('/order/distribution/' . $id), 'PUT');
    }

    /**
     * Sửa thông tin giao hàng
     * @param int $id ID đơn hàng
     * @return mixed
     */
    public function updateDistribution(int $id, array $data)
    {
        $order = $this->dao->get($id);
        if (!$order) {
            throw new AdminException(100026);
        }
        switch ($order['delivery_type']) {
            case 'send':
                if (!$data['delivery_name']) {
                    throw new AdminException(400523);
                }
                if (!$data['delivery_id']) {
                    throw new AdminException(400524);
                }
                if (!preg_match("/^1[3456789]{1}\d{9}$/", $data['delivery_id'])) {
                    throw new AdminException(400526);
                }
                break;
            case 'express':
                if (!$data['delivery_id']) {
                    throw new AdminException(400531);
                }
                // Kiểm tra mã đơn vị vận chuyển
                /** @var ExpressServices $expressServices */
                $expressServices = app()->make(ExpressServices::class);
                if ($name = $expressServices->value(['code' => $data['delivery_code']], 'name')) {
                    $data['delivery_name'] = $name;
                } else {
                    throw new AdminException(410324);
                }
                break;
            case 'fictitious':
                throw new AdminException(400479);
                break;
            default:
                throw new AdminException(400480);
                break;
        }
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        $statusService->save([
            'oid' => $id,
            'change_type' => 'distribution',
            'change_message' => 'Sửa thông tin giao hàng thành:' . $data['delivery_name'] . ', số:' . $data['delivery_id'],
            'change_time' => time()
        ]);
        return $this->dao->update($id, $data);
    }

    /**In vận đơn điện tử sau khi đơn hàng được giao
     * @param $orderId
     * @return bool|mixed
     */
    public function orderDump($orderId, $type = 'order')
    {
        if (!$orderId) throw new AdminException(10100);
//        /** @var StoreOrderServices $orderService */
//        $orderService = app()->make(StoreOrderServices::class);
//        $orderInfo = $orderService->getOne(['id' => $orderId]);
if ($type == 'order') {
    /** @var StoreOrderServices $orderService */
    $orderService = app()->make(StoreOrderServices::class);
    $orderInfo = $orderService->getOne(['id' => $orderId]);
} else {
    /** @var StoreIntegralOrderServices $integralOrderService */
    $integralOrderService = app()->make(StoreIntegralOrderServices::class);
    $orderInfo = $integralOrderService->getOne(['id' => $orderId]);
}
        if (!$orderInfo) throw new AdminException(400118);
        if ($orderInfo->shipping_type != 1) throw new AdminException(400481);
        if (!$orderInfo->express_dump) throw new AdminException(400482);
        if (!sys_config('config_export_open', 0)) {
            throw new AdminException(400483);
        }
        $dumpInfo = json_decode($orderInfo->express_dump, true);
        /** @var ServeServices $expressService */
        $expressService = app()->make(ServeServices::class);
        $expData['com'] = $dumpInfo['com'];
        $expData['to_name'] = $orderInfo->real_name;
        $expData['to_tel'] = $orderInfo->user_phone;
        $expData['to_addr'] = $orderInfo->user_address;
        $expData['from_name'] = $dumpInfo['from_name'];
        $expData['from_tel'] = $dumpInfo['from_tel'];
        $expData['from_addr'] = $dumpInfo['from_addr'];
        $expData['siid'] = sys_config('config_export_siid');
        $expData['temp_id'] = $dumpInfo['temp_id'];
        $expData['cargo'] = $dumpInfo['cargo'];
        $expData['count'] = $orderInfo->total_num;
        $expData['order_id'] = $orderInfo->order_id;
        $expData['weight'] = 1;

        return $expressService->express()->dump($expData);
    }

    /**
     * Tách đơn hàng để giao hàng
     * @param int $id
     * @param array $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/02/21
     */
    public function splitDelivery(int $id, array $data, $delivery_code = true)
    {
        $orderInfo = $this->dao->get($id, ['*'], ['pink']);
        if (!$orderInfo) {
            throw new AdminException(400470);
        }
        if ($orderInfo->is_del) {
            throw new AdminException(400471);
        }
        if ($orderInfo->shipping_type == 2) {
            throw new AdminException(400473);
        }
        if (isset($orderInfo['pinkStatus']) && $orderInfo['pinkStatus'] != 2) {
            throw new AdminException(400474);
        }
        /** @var StoreOrderRefundServices $storeOrderRefundServices */
        $storeOrderRefundServices = app()->make(StoreOrderRefundServices::class);
        if ($storeOrderRefundServices->count(['store_order_id' => $id, 'refund_type' => [1, 2, 4, 5], 'is_cancel' => 0, 'is_del' => 0])) {
            throw new AdminException(400475);
        }

        if ($data['type'] == 1 && $delivery_code) {
            // Kiểm tra mã đơn vị vận chuyển
            /** @var ExpressServices $expressServices */
            $expressServices = app()->make(ExpressServices::class);
            if (!$expressServices->be(['code' => $data['delivery_code']])) {
                throw new AdminException(410324);
            }
        }

        $cart_ids = $data['cart_ids'];
        unset($data['cart_ids']);
        $this->transaction(function () use ($id, $cart_ids, $orderInfo, $data) {
            /** @var StoreOrderSplitServices $storeOrderSplitServices */
            $storeOrderSplitServices = app()->make(StoreOrderSplitServices::class);
            //Tách đơn hàng
            [$splitOrderInfo, $otherOrder] = $storeOrderSplitServices->equalSplit($id, $cart_ids, $orderInfo);
            if ($splitOrderInfo) {
                $splitOrderInfo['refund_status'] = 0;
                //Thực hiện giao hàng cho đơn hàng đã tách
                $this->doDelivery((int)$splitOrderInfo->id, $splitOrderInfo, $data);
                /** @var StoreOrderStatusServices $services */
                $services = app()->make(StoreOrderStatusServices::class);
                //Ghi lại trạng thái đơn hàng gốc
                $status_data = ['oid' => $id, 'change_time' => time()];
                $status_data['change_type'] = 'delivery_split';
                $status_data['change_message'] = 'Đã tách đơn và giao hàng';
                $services->save($status_data);
            } else {
                $this->doDelivery($id, $orderInfo, $data);
            }
        });
        return true;
    }

    /**
     * Thực hiện giao hàng cụ thể
     * @param int $id
     * @param $orderInfo
     * @param array $data
     * @return array
     */
    public function doDelivery(int $id, $orderInfo, array $data)
    {
        $type = (int)$data['type'];
        unset($data['type']);
        //Lấy tên sản phẩm trong giỏ hàng
        /** @var StoreOrderCartInfoServices $orderInfoServices */
        $orderInfoServices = app()->make(StoreOrderCartInfoServices::class);
        $storeName = $orderInfoServices->getCarIdByProductTitle((int)$orderInfo->id);

        if (isset($data['pickup_time']) && count($data['pickup_time']) == 2) {
            $data['pickup_start_time'] = $data['pickup_time'][0];
            $data['pickup_end_time'] = $data['pickup_time'][1];
        } else {
            $data['pickup_start_time'] = '';
            $data['pickup_end_time'] = '';
        }

        // Nhập thông tin giao hàng
        $res = [];
        switch ($type) {
            case 1://Giao hàng qua vận chuyển
                $res = $this->orderDeliverGoods($id, $data, $orderInfo, $storeName);
                event('NoticeListener', [['orderInfo' => $orderInfo, 'storeName' => $storeName, 'data' => $data], 'order_postage_success']);

                //Tin nhắn tùy chỉnh - giao hàng qua vận chuyển
                $orderInfo['storeName'] = $storeName;
                $orderInfo['delivery_name'] = $data['delivery_name'];
                $orderInfo['delivery_id'] = $data['delivery_id'];
                $orderInfo['time'] = date('Y-m-d H:i:s');
                $orderInfo['phone'] = $orderInfo['user_phone'];
                event('CustomNoticeListener', [$orderInfo['uid'], $orderInfo, 'order_express_success']);
                break;
            case 2://Giao hàng
                $this->orderDelivery($id, $data, $orderInfo, $storeName);
                event('NoticeListener', [['orderInfo' => $orderInfo, 'storeName' => $storeName, 'data' => $data], 'order_deliver_success']);

                //Tin nhắn tùy chỉnh - người giao hàng giao hàng
                $orderInfo['storeName'] = $storeName;
                $orderInfo['delivery_name'] = $data['delivery_name'];
                $orderInfo['delivery_id'] = $data['delivery_id'];
                $orderInfo['time'] = date('Y-m-d H:i:s');
                $orderInfo['phone'] = $orderInfo['user_phone'];
                event('CustomNoticeListener', [$orderInfo['uid'], $orderInfo, 'order_send_success']);
                break;
            case 3://Giao hàng ảo
                $this->orderVirtualDelivery($id, $data, $orderInfo, $storeName);
                break;
            default:
                throw new AdminException(400522);
        }
        if (!$data['delivery_id'] && !empty($res['kuaidinum'])) {
            $data['delivery_id'] = $res['kuaidinum'];
        }
        if (!$data['delivery_id']) {
            $data['delivery_id'] = uniqid();
        }
        // Quản lý đơn hàng Mini Program
        event('OrderShippingListener', ['product', $orderInfo, $type, $data['delivery_id'], $data['delivery_code']]);
        //Tự động xác nhận nhận hàng khi hết hạn
        event('OrderDeliveryListener', [$orderInfo, $storeName, $data, $type]);

        //Sự kiện tùy chỉnh - giao hàng đơn hàng
        event('CustomEventListener', ['admin_order_express', [
            'uid' => $orderInfo['uid'],
            'real_name' => $orderInfo['real_name'],
            'user_phone' => $orderInfo['user_phone'],
            'user_address' => $orderInfo['user_address'],
            'order_id' => $orderInfo['order_id'],
            'delivery_name' => $orderInfo['delivery_name'],
            'delivery_id' => $orderInfo['delivery_id'],
            'express_time' => date('Y-m-d H:i:s'),
        ]]);

        return $res;
    }

    /**
     * Đơn hàng giao qua vận chuyển
     * @param int $id
     * @param array $data
     */
    public function orderDeliverGoods(int $id, array $data, $orderInfo, $storeTitle)
    {
        /** @var StoreOrderCartInfoServices $orderInfoServices */
        $orderInfoServices = app()->make(StoreOrderCartInfoServices::class);
        if (!$data['delivery_name']) {
            throw new AdminException(400007);
        }
        $dump = [];
        $data['delivery_type'] = 'express';
        if ($data['express_record_type'] == 2) {//Vận đơn điện tử
            if (!$data['delivery_code']) {
                throw new AdminException(400476);
            }
            if (!$data['express_temp_id']) {
                throw new AdminException(400527);
            }
            if (!$data['to_name']) {
                throw new AdminException(400008);
            }
            if (!$data['to_tel']) {
                throw new AdminException(400477);
            }
            if (!$data['to_addr']) {
                throw new AdminException(400478);
            }
            /** @var ServeServices $expressService */
            $expressService = app()->make(ServeServices::class);
            $expData['com'] = $data['delivery_code'];
            $expData['to_name'] = $orderInfo->real_name;
            $expData['to_tel'] = $orderInfo->user_phone;
            $expData['to_addr'] = $orderInfo->user_address;
            $expData['from_name'] = $data['to_name'];
            $expData['from_tel'] = $data['to_tel'];
            $expData['from_addr'] = $data['to_addr'];
            $expData['siid'] = sys_config('config_export_siid');
            $expData['temp_id'] = $data['express_temp_id'];
            $expData['count'] = $orderInfo->total_num;
            $expData['weight'] = $this->getOrderSumWeight($id);
            $expData['cargo'] = $orderInfoServices->getCarIdByProductTitle((int)$orderInfo->id, true);
            $expData['order_id'] = $orderInfo->order_id;
            if (!sys_config('config_export_open', 0)) {
                throw new AdminException(400528);
            }
            $dump = $expressService->express()->dump($expData);
            $orderInfo->delivery_id = $dump['kuaidinum'];
            $data['express_dump'] = json_encode([
                'com' => $expData['com'],
                'from_name' => $expData['from_name'],
                'from_tel' => $expData['from_tel'],
                'from_addr' => $expData['from_addr'],
                'temp_id' => $expData['temp_id'],
                'cargo' => $expData['cargo'],
            ]);
            $data['delivery_id'] = $dump['kuaidinum'];
            if (!empty($dump['label'])) {
                $data['kuaidi_label'] = $dump['label'];
            }
        } else if ($data['express_record_type'] == 3) {
            //Người bán gửi hàng
            if (!$data['delivery_code']) {
                throw new AdminException(400476);
            }
            if (!$data['express_temp_id']) {
                throw new AdminException(400527);
            }
            if (!$data['to_name']) {
                throw new AdminException(400008);
            }
            if (!$data['to_tel']) {
                throw new AdminException(400477);
            }
            if (!$data['to_addr']) {
                throw new AdminException(400478);
            }
            /** @var ServeServices $expressService */
            $expressService = app()->make(ServeServices::class);
            $expData['kuaidicom'] = $data['delivery_code'];
            $expData['man_name'] = $orderInfo->real_name;
            $expData['phone'] = $orderInfo->user_phone;
            $expData['address'] = $orderInfo->user_address;
            $expData['send_real_name'] = $data['to_name'];
            $expData['send_phone'] = $data['to_tel'];
            $expData['send_address'] = $data['to_addr'];
            $expData['temp_id'] = $data['express_temp_id'];
            $expData['weight'] = $this->getOrderSumWeight($id);
            $expData['cargo'] = $orderInfoServices->getCarIdByProductTitle((int)$orderInfo->id, true);
            $expData['day_type'] = $data['day_type'];
            $expData['pickup_start_time'] = $data['pickup_start_time'];
            $expData['pickup_end_time'] = $data['pickup_end_time'];
//            if (!sys_config('config_shippment_open', 0)) {
//                throw new AdminException('Cửa hàng chưa mở gửi hàng nên không thể gửi');
//            }
            $dump = $expressService->express()->shippmentCreateOrder($expData);
            Log::error('Dữ liệu trả về khi người bán gửi hàng:' . json_encode($dump));
            $orderInfo->delivery_id = $dump['kuaidinum'] ?? '';
            $data['express_dump'] = json_encode([
                'com' => $expData['kuaidicom'],
                'from_name' => $expData['send_real_name'],
                'from_tel' => $expData['send_phone'],
                'from_addr' => $expData['send_address'],
                'temp_id' => $expData['temp_id'],
                'cargo' => $expData['cargo'],
            ]);
            $data['delivery_id'] = $dump['kuaidinum'] ?? '';
            $data['kuaidi_label'] = $dump['label'] ?? '';
            $data['kuaidi_task_id'] = $dump['task_id'] ?? '';
            $data['kuaidi_order_id'] = $dump['order_id'] ?? '';
        } else {
            if (!$data['delivery_id']) {
                throw new AdminException(400531);
            }
            $orderInfo->delivery_id = $data['delivery_id'];
        }
        if (true) {
            $data['status'] = 1;
            $orderInfo->delivery_type = $data['delivery_type'];
            $orderInfo->delivery_name = $data['delivery_name'];
            $orderInfo->status = $data['status'];
            /** @var StoreOrderStatusServices $services */
            $services = app()->make(StoreOrderStatusServices::class);
            $this->transaction(function () use ($id, $data, $services) {
                $res = $this->dao->update($id, $data);
                $res = $res && $services->save([
                        'oid' => $id,
                        'change_time' => time(),
                        'change_type' => 'delivery_goods',
                        'change_message' => 'Đã giao hàng. Đơn vị vận chuyển:' . $data['delivery_name'] . ' Mã vận đơn:' . $data['delivery_id']
                    ]);
                if (!$res) {
                    throw new AdminException(400529);
                }
            });
        } else {

            $update = [
                'is_stock_up' => 1,
                'delivery_type' => $data['delivery_type'],
                'delivery_name' => $data['delivery_name'],
                'delivery_code' => $data['delivery_code'],
                'delivery_id' => $data['delivery_id'],
                'kuaidi_label' => $data['kuaidi_label'],
                'kuaidi_task_id' => $data['kuaidi_task_id'],
                'kuaidi_order_id' => $data['kuaidi_order_id'],
                'express_dump' => $data['express_dump']
            ];

            /** @var StoreOrderStatusServices $services */
            $services = app()->make(StoreOrderStatusServices::class);
            $this->transaction(function () use ($id, $data, $services, $update) {
                $res = $this->dao->update($id, $update);
                $res = $res && $services->save([
                        'oid' => $id,
                        'change_time' => time(),
                        'change_type' => 'stock_up_goods',
                        'change_message' => 'Đang chuẩn bị hàng, đơn vị vận chuyển:' . $data['delivery_name'] . ' Mã vận đơn:' . $data['delivery_id']
                    ]);
                if (!$res) {
                    throw new AdminException(400529);
                }
            });
        }
        return $dump;
    }

    /**
     * Trả về tổng khối lượng sản phẩm trong đơn hàng
     * @param int $id
     * @return int|string
     */
    public function getOrderSumWeight(int $id, $default = false)
    {
        /** @var StoreOrderCartInfoServices $services */
        $services = app()->make(StoreOrderCartInfoServices::class);
        $orderGoodInfo = $services->getOrderCartInfo((int)$id);
        $weight = 0;
        foreach ($orderGoodInfo as $cartInfo) {
            $cart = $cartInfo['cart_info'] ?? [];
            if ($cart) {
                $weight = bcadd((string)$weight, (string)bcmul((string)$cart['cart_num'] ?? '0', (string)$cart['productInfo']['attrInfo']['weight'] ?? '0', 4), 2);
            }
        }
        return $weight ?: ($default === false ? 0 : $default);
    }

    /**
     * Tự động giao hàng cho sản phẩm ảo
     * @param $orderInfo
     * @throws \ReflectionException
     */
    public function virtualSend($orderInfo)
    {
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        /** @var StoreOrderCartInfoServices $services */
        $services = app()->make(StoreOrderCartInfoServices::class);
        $orderInfo['cart_info'] = $services->getOrderCartInfo((int)$orderInfo['id']);
        $activityStatus = $orderInfo['combination_id'] || $orderInfo['seckill_id'] || $orderInfo['bargain_id'];
        if ($orderInfo['virtual_type'] == 1) {
            /** @var StoreOrderServices $orderService */
            $orderService = app()->make(StoreOrderServices::class);
            $sku = $orderInfo['cart_info'][$orderInfo['cart_id'][0]]['cart_info']['productInfo']['attrInfo']['suk'];
            if ($activityStatus) {
                $product_id = $orderInfo['cart_info'][$orderInfo['cart_id'][0]]['cart_info']['productInfo']['product_id'];
                /** @var StoreProductAttrValueServices $attrValue */
                $attrValue = app()->make(StoreProductAttrValueServices::class);
                $disk_info = $attrValue->value(['product_id' => $product_id, 'suk' => $sku, 'type' => 0, 'is_virtual' => 1], 'disk_info');
            } else {
                $disk_info = $orderInfo['cart_info'][$orderInfo['cart_id'][0]]['cart_info']['productInfo']['attrInfo']['disk_info'];
            }
            if ($disk_info != '') {
                $orderService->update(['id' => $orderInfo['id']], ['status' => 1, 'delivery_type' => 'fictitious', 'virtual_info' => $disk_info, 'remark' => 'Tự động gửi mã khóa:' . $disk_info]);
                $this->SystemSend($orderInfo['uid'], [
                    'mark' => 'virtual_info',
                    'title' => 'Gửi mã khóa ảo',
                    'content' => 'Sản phẩm mã khóa bạn mua đã thanh toán thành công, số tiền thanh toán' . $orderInfo['pay_price'] . 'đ, mã đơn hàng:' . $orderInfo['order_id'] . ', mã khóa:' . $disk_info . ', cảm ơn bạn đã mua hàng!'
                ]);
            } else {
                if ($activityStatus) {
                    $product_id = $orderInfo['cart_info'][$orderInfo['cart_id'][0]]['cart_info']['productInfo']['product_id'];
                    /** @var StoreProductAttrValueServices $attrValue */
                    $attrValue = app()->make(StoreProductAttrValueServices::class);
                    $unique = $attrValue->value(['product_id' => $product_id, 'suk' => $sku, 'type' => 0, 'is_virtual' => 1], 'unique');
                } else {
                    $unique = $orderInfo['cart_info'][$orderInfo['cart_id'][0]]['cart_info']['productInfo']['attrInfo']['unique'];
                }
                /** @var StoreProductVirtualServices $virtualService */
                $virtualService = app()->make(StoreProductVirtualServices::class);
                $virtual = $virtualService->get(['attr_unique' => $unique, 'uid' => 0]);
                if (!$virtual) throw new ApiException(100026);
                $virtual->order_id = $orderInfo['order_id'];
                $virtual->uid = $orderInfo['uid'];
                $virtual->save();
                $orderService->update(['id' => $orderInfo['id']], ['status' => 1, 'delivery_type' => 'fictitious', 'virtual_info' => $virtual->card_unique, 'remark' => 'Mã thẻ đã được gửi tự động, số thẻ:' . $virtual->card_no . '; mật khẩu:' . $virtual->card_pwd]);
                $this->SystemSend($orderInfo['uid'], [
                    'mark' => 'virtual_info',
                    'title' => 'Gửi mã thẻ ảo',
                    'content' => 'Sản phẩm mã thẻ bạn mua đã thanh toán thành công, số tiền thanh toán' . $orderInfo['pay_price'] . 'đ, mã đơn hàng:' . $orderInfo['order_id'] . ', số thẻ:' . $virtual->card_no . '; mật khẩu:' . $virtual->card_pwd . ', cảm ơn bạn đã mua hàng!'
                ]);
            }
            $statusService->save([
                'oid' => $orderInfo['id'],
                'change_type' => 'delivery_fictitious',
                'change_message' => 'Tự động giao mã thẻ',
                'change_time' => time()
            ]);
        } elseif ($orderInfo['virtual_type'] == 2) {
            if ($activityStatus) {
                $sku = $orderInfo['cart_info'][$orderInfo['cart_id'][0]]['cart_info']['productInfo']['attrInfo']['suk'];
                $product_id = $orderInfo['cart_info'][$orderInfo['cart_id'][0]]['cart_info']['productInfo']['product_id'];
                /** @var StoreProductAttrValueServices $attrValue */
                $attrValue = app()->make(StoreProductAttrValueServices::class);
                $coupon_id = $attrValue->value(['product_id' => $product_id, 'suk' => $sku, 'type' => 0, 'is_virtual' => 1], 'coupon_id');
            } else {
                $coupon_id = $orderInfo['cart_info'][$orderInfo['cart_id'][0]]['cart_info']['productInfo']['attrInfo']['coupon_id'];
            }
            /** @var StoreCouponIssueServices $issueService */
            $issueService = app()->make(StoreCouponIssueServices::class);
            $coupon = $issueService->get($coupon_id);
            if ($issueService->setCoupon($coupon, [$orderInfo['uid']])) {
                /** @var StoreOrderServices $orderService */
                $orderService = app()->make(StoreOrderServices::class);
                $orderService->update(['id' => $orderInfo['id']], ['status' => 1, 'delivery_type' => 'fictitious', 'virtual_info' => $coupon_id, 'remark' => 'Phiếu giảm giá đã được gửi tự động']);
                $this->SystemSend($orderInfo['uid'], [
                    'mark' => 'virtual_info',
                    'title' => 'Gửi phiếu giảm giá đã mua',
                    'content' => 'Phiếu giảm giá bạn mua đã thanh toán thành công, số tiền thanh toán' . $orderInfo['pay_price'] . 'đ, mã đơn hàng' . $orderInfo['order_id'] . 'vui lòng xem trong mục Phiếu giảm giá ở trang cá nhân, cảm ơn bạn đã mua hàng!'
                ]);
            } else {
                throw new ApiException(410323);
            }
            $statusService->save([
                'oid' => $orderInfo['id'],
                'change_type' => 'delivery_fictitious',
                'change_message' => 'Tự động giao phiếu giảm giá',
                'change_time' => time()
            ]);
        }
        if ($orderInfo['is_channel'] == 1 && $orderInfo['pay_type'] == 'weixin') {
            MiniOrderJob::dispatchSecs(10, 'doJob', [
                $orderInfo['order_id'],
                3,
                [['item_desc' => $orderInfo['virtual_type'] == 1 ? 'Tự động giao mã thẻ' : 'Tự động giao phiếu giảm giá']],
                app()->make(WechatUserServices::class)->uidToOpenid($orderInfo['uid'], 'routine'),
                'pages/goods/order_details/index?order_id=' . $orderInfo['order_id']
            ]);
        }
    }

    /**
     * Thông báo nội bộ cho sản phẩm ảo
     * @param int $uid
     * @param array $noticeInfo
     */
    public function SystemSend(int $uid, array $noticeInfo)
    {
        /** @var MessageSystemServices $MessageSystemServices */
        $MessageSystemServices = app()->make(MessageSystemServices::class);
        $data = [];
        $data['mark'] = $noticeInfo['mark'];
        $data['uid'] = $uid;
        $data['title'] = $noticeInfo['title'];
        $data['content'] = $noticeInfo['content'];
        $data['type'] = 1;
        $data['add_time'] = time();
        $MessageSystemServices->save($data);
    }
}
