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

namespace app\services\order;

use app\dao\order\StoreOrderDao;
use app\services\activity\combination\StorePinkServices;
use app\services\BaseServices;
use app\services\pay\PayServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;

/**
 * Class OutStoreOrderServices
 * @package app\services\order
 * @method getOrderIdsCount(array $ids) Lấy số lượng đơn hàng chưa xóa theo id đơn hàng
 * @method StoreOrderDao getUserOrderDetail(string $key, int $uid, array $with) Lấy chi tiết đơn hàng
 * @method chartTimePrice($start, $stop) Lấy số tiền thanh toán từ thời điểm hiện tại đến thời điểm chỉ định (quản trị viên)
 * @method chartTimeNumber($start, $stop) Lấy số đơn hàng đã thanh toán từ thời điểm hiện tại đến thời điểm chỉ định (quản trị viên)
 * @method together(array $where, string $field, string $together = 'sum') Truy vấn tổng hợp
 * @method getBuyCount($uid, $type, $typeId) Lấy số lượng sản phẩm của hoạt động này mà người dùng đã mua
 * @method getDistinctCount(array $where, $field, ?bool $search = true)
 * @method getTrendData($time, $type, $timeType, $str) Xu hướng người dùng
 * @method getRegion($time, $channelType) Thống kê theo khu vực
 * @method getProductTrend($time, $timeType, $field, $str) Xu hướng sản phẩm
 */
class OutStoreOrderServices extends BaseServices
{

    /**
     * Hình thức giao hàng
     * @var string[]
     */
    public $deliveryType = ['send' => 'Cửa hàng tự giao', 'express' => 'Giao qua đơn vị vận chuyển', 'fictitious' => 'Giao hàng ảo', 'delivery_part_split' => 'Tách đơn, đã giao một phần', 'delivery_split' => 'Tách đơn, đã giao xong'];

    /**
     * StoreOrderProductServices constructor.
     * @param StoreOrderDao $dao
     */
    public function __construct(StoreOrderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOrderList(array $where)
    {
        $where['order_status'] = $where['status'];
        unset($where['status']);
        if (!is_numeric($where['paid'])) {
            $where['paid'] = -1;
        }
        [$page, $limit] = $this->getPageValue();
        $field = ['id', 'pid', 'order_id', 'trade_no', 'uid', 'freight_price', 'real_name', 'user_phone', 'user_address', 'total_num',
            'total_price', 'total_postage', 'pay_price', 'coupon_price', 'deduction_price', 'paid', 'pay_time', 'pay_type', 'add_time',
            'shipping_type', 'status', 'refund_status', 'delivery_name', 'delivery_code', 'delivery_id'];
        $data = $this->dao->getOutOrderList($where, $field, $page, $limit);
        $count = $this->dao->count($where);
        $list = $this->tidyOrderList($data);
        return compact('list', 'count');
    }

    /**
     * Chuyển đổi dữ liệu
     * @param array $data
     * @return array
     */
    public function tidyOrderList(array $data)
    {
        /** @var StoreOrderCartInfoServices $services */
        $services = app()->make(StoreOrderCartInfoServices::class);
        foreach ($data as &$item) {
            $list = [];
            $carts = $services->getOrderCartInfo((int)$item['id']);
            foreach ($carts as $key => $cart) {
                $list = $this->tidyCartList($cart['cart_info'], $list, $key);
            }
            $item['pay_type_name'] = PayServices::PAY_TYPE[$item['pay_type']] ?? 'Phương thức khác';
            $item['items'] = $list;
            unset($item['refund_status'], $item['shipping_type']);
        }
        return $data;
    }

    /**
     * Chi tiết đơn hàng
     * @param string $orderId Mã đơn hàng
     * @param int $id ID đơn hàng
     * @return mixed
     */
    public function getInfo(string $orderId = '', int $id = 0)
    {
        $field = ['id', 'pid', 'order_id', 'trade_no', 'uid', 'freight_price', 'real_name', 'user_phone', 'user_address', 'total_num',
            'total_price', 'total_postage', 'pay_price', 'coupon_price', 'deduction_price', 'paid', 'pay_time', 'pay_type', 'add_time',
            'shipping_type', 'status', 'refund_status', 'delivery_name', 'delivery_code', 'delivery_id', 'refund_type', 'delivery_type', 'pink_id', 'use_integral', 'back_integral'];

        if ($id > 0) {
            $where = $id;
        } else {
            $where = ['order_id' => $orderId];
        }

        if (!$orderInfo = $this->dao->get($where, $field, ['invoice'])) {
            throw new ApiException('Đơn hàng không tồn tại');
        }

        if (!$orderInfo['invoice']) {
            $orderInfo['invoice'] = new \StdClass();
        } else {
            $orderInfo['invoice']->hidden(['uid', 'category', 'id', 'order_id', 'add_time']);
        }

        $orderInfo = $this->tidyOrder($orderInfo->toArray(), true);
        //Tính toán số tiền ưu đãi
        $vipTruePrice = array_column($orderInfo['items'], 'vip_sum_truePrice');
        $vipTruePrice = round(array_sum($vipTruePrice), 2);
        $orderInfo['vip_true_price'] = sprintf("%.2f", $vipTruePrice ?: '0.00');
        $orderInfo['total_price'] = bcadd($orderInfo['total_price'], $orderInfo['vip_true_price'], 2);
        return $orderInfo;
    }

    /**
     * Định dạng dữ liệu chi tiết đơn hàng
     * @param $order
     * @param bool $detail Có cần chi tiết sản phẩm trong đơn hàng không
     * @return mixed
     */
    public function tidyOrder($order, bool $detail = false)
    {
        if ($detail == true && isset($order['id'])) {
            /** @var StoreOrderCartInfoServices $cartServices */
            $cartServices = app()->make(StoreOrderCartInfoServices::class);
            $carts = $cartServices->getOrderCartInfo((int)$order['id']);

            $list = [];
            foreach ($carts as $key => $cart) {
                $list = $this->tidyCartList($cart['cart_info'], $list, $key);
            }
            $order['items'] = $list;
        }

        $order['pay_type_name'] = PayServices::PAY_TYPE[$order['pay_type']] ?? 'Phương thức khác';

//        if (!$order['paid'] && $order['pay_type'] == 'offline' && !$order['status'] >= 2) {
//            $order['status_name'] = 'Thanh toán ngoại tuyến, chưa thanh toán';
//        } else if (!$order['paid']) {
//            $order['status_name'] = 'Chưa thanh toán';
//        } else if ($order['status'] == 4) {
//            if ($order['delivery_type'] == 'send') {
//                $order['status_name'] = 'Chờ nhận hàng';
//            } elseif ($order['delivery_type'] == 'express') {
//                $order['status_name'] = 'Chờ nhận hàng';
//            } elseif ($order['delivery_type'] == 'split') {//giao hàng theo phần tách đơn
//                $order['status_name'] = 'Chờ nhận hàng';
//            } else {
//                $order['status_name'] = 'Chờ nhận hàng';
//            }
//        } else if ($order['refund_status'] == 1) {
//            if (in_array($order['refund_type'], [0, 1, 2])) {
//                $order['status_name'] = 'Đang yêu cầu hoàn tiền';
//            } elseif ($order['refund_type'] == 4) {
//                $order['status_name'] = 'Đang yêu cầu hoàn tiền';
//            } elseif ($order['refund_type'] == 5) {
//                $order['status_name'] = 'Đang yêu cầu hoàn tiền';
//            }
//        } else if ($order['refund_status'] == 2 || $order['refund_type'] == 6) {
//            $order['status_name'] = 'Đã hoàn tiền';
//        } else if ($order['refund_status'] == 3) {
//            $order['status_name'] = 'Hoàn tiền một phần (đơn hàng con)';
//        } else if ($order['refund_status'] == 4) {
//            $order['status_name'] = 'Tất cả đơn hàng con đang yêu cầu hoàn tiền';
//        } else if (!$order['status']) {
//            if ($order['pink_id']) {
//                /** @var StorePinkServices $pinkServices */
//                $pinkServices = app()->make(StorePinkServices::class);
//                if ($pinkServices->getCount(['id' => $order['pink_id'], 'status' => 1])) {
//                    $order['status_name'] = 'Đang mua chung';
//                } else {
//                    $order['status_name'] = 'Chưa giao hàng';
//                }
//            } else {
//                if ($order['shipping_type'] === 1) {
//                    $order['status_name'] = 'Chưa giao hàng';
//                } else {
//                    $order['status_name'] = 'Chờ xác nhận sử dụng';
//                }
//            }
//        } else if ($order['status'] == 1) {
//            if ($order['delivery_type'] == 'send') {//TODO giao hàng
//                $order['status_name'] = 'Chờ nhận hàng';
//            } elseif ($order['delivery_type'] == 'express') {//TODO giao hàng
//                $order['status_name'] = 'Chờ nhận hàng';
//            } elseif ($order['delivery_type'] == 'split') {//giao hàng theo phần tách đơn
//                $order['status_name'] = 'Chờ nhận hàng';
//            } else {
//                $order['status_name'] = 'Chờ nhận hàng';
//            }
//        } else if ($order['status'] == 2) {
//            $order['status_name'] = 'Chờ đánh giá';
//        } else if ($order['status'] == 3) {
//            $order['status_name'] = 'Giao dịch hoàn tất';
//        }
        // Xử lý trạng thái chưa thanh toán
        if (!$order['paid']) {
            if ($order['pay_type'] == 'offline') {
                $order['status_name'] = 'Thanh toán ngoại tuyến, chưa thanh toán';
            } else {
                $order['status_name'] = 'Chưa thanh toán';
            }
        } elseif ($order['status'] == 4 || $order['status'] == 1) { // Gộp logic chờ nhận hàng
            $order['status_name'] = 'Chờ nhận hàng';
        } elseif ($order['refund_status'] == 1) {
            if (in_array($order['refund_type'], [0, 1, 2, 4, 5])) {
                $order['status_name'] = 'Đang yêu cầu hoàn tiền';
            }
        } elseif ($order['refund_status'] == 2 || $order['refund_type'] == 6) {
            $order['status_name'] = 'Đã hoàn tiền';
        } elseif ($order['refund_status'] == 3) {
            $order['status_name'] = 'Hoàn tiền một phần (đơn con)';
        } elseif ($order['refund_status'] == 4) {
            $order['status_name'] = 'Tất cả đơn con đang yêu cầu hoàn tiền';
        } elseif (!$order['status']) {
            if ($order['pink_id']) {
                /** @var StorePinkServices $pinkServices */
                $pinkServices = app()->make(StorePinkServices::class);
                if ($pinkServices->getCount(['id' => $order['pink_id'], 'status' => 1])) {
                    $order['status_name'] = 'Đang mua chung';
                } else {
                    $order['status_name'] = 'Chưa giao hàng';
                }
            } else {
                if ($order['shipping_type'] === 1) {
                    $order['status_name'] = 'Chưa giao hàng';
                } else {
                    $order['status_name'] = 'Chờ xác nhận sử dụng';
                }
            }
        } elseif ($order['status'] == 2) {
            $order['status_name'] = 'Chờ đánh giá';
        } elseif ($order['status'] == 3) {
            $order['status_name'] = 'Giao dịch hoàn tất';
        } else {
            // Xử lý trạng thái không xác định
            $order['status_name'] = 'Trạng thái không xác định';
        }
        unset($order['pink_id'], $order['refund_type']);
        return $order;
    }

    /**
     * Định dạng sản phẩm trong đơn hàng
     * @param array $cartInfo
     * @param array $list
     * @return array
     */
    public function tidyCartList(array $cartInfo, array $list, $cartId = 0): array
    {
        $list[] = [
            'cart_id' => $cartId,
            'store_name' => $cartInfo['productInfo']['store_name'] ?? '',
            'suk' => $cartInfo['productInfo']['attrInfo']['suk'] ?? '',
            'image' => $cartInfo['productInfo']['attrInfo']['image'] ?: $cartInfo['productInfo']['image'],
            'price' => sprintf("%.2f", $cartInfo['truePrice'] ?? '0.00'),
            'cart_num' => $cartInfo['cart_num'] ?? 0,
            'surplus_num' => $cartInfo['surplus_num'] ?? 0,
            'refund_num' => $cartInfo['refund_num'] ?? 0
        ];
        return $list;
    }

    /**
     * Lấy thông tin sản phẩm có thể tách khỏi đơn hàng
     * @param string $orderId Mã đơn hàng
     * @return array
     */
    public function getCartList(string $orderId): array
    {
        $order = $this->dao->get(['order_id' => $orderId]);
        if (!$order) {
            throw new ApiException('Đơn hàng không tồn tại');
        }

        $list = [];
        /** @var StoreOrderCartInfoServices $services */
        $services = app()->make(StoreOrderCartInfoServices::class);
        $carts = $services->getSplitCartList((int)$order['id']);
        foreach ($carts as $key => $cart) {
            $list = $this->tidyCartList($cart['cart_info'], $list, $key);
        }
        return $list;
    }

    /**
     * Xác nhận đã nhận hàng
     * @param string $orderId Mã đơn hàng
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function receive(string $orderId): bool
    {
        $order = $this->dao->get(['order_id' => $orderId]);
        if (!$order) {
            throw new ApiException('Đơn hàng không tồn tại');
        }

        if ($order['status'] == 2) {
            throw new ApiException('Không thể xác nhận nhận hàng nhiều lần');
        }

        if (($order['paid'] == 1 && $order['status'] == 1) || $order['pay_type'] == 'offline') {
            $data['status'] = 2;
        } else {
            throw new ApiException('Vui lòng giao hàng hoặc giao tận nơi trước');
        }

        if (!$this->dao->update($order['id'], $data)) {
            throw new ApiException('Xác nhận nhận hàng thất bại, vui lòng thử lại sau');
        }

        /** @var StoreOrderTakeServices $takeServices */
        $takeServices = app()->make(StoreOrderTakeServices::class);
        if (!$takeServices->storeProductOrderUserTakeDelivery($order)) {
            throw new ApiException('Xác nhận nhận hàng thất bại, vui lòng thử lại sau');
        }
        return true;
    }

    /**
     * Giao hàng
     * @param string $orderId Mã đơn hàng
     * @param array $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delivery(string $orderId, array $data)
    {
        $orderInfo = $this->dao->get(['order_id' => $orderId]);
        if (!$orderInfo) {
            throw new ApiException('Không tìm thấy đơn hàng, không thể giao hàng');
        }

        /** @var StoreOrderDeliveryServices $deliveryServices */
        $deliveryServices = app()->make(StoreOrderDeliveryServices::class);
        return $deliveryServices->delivery((int)$orderInfo['id'], $data);
    }

    /**
     * Tách đơn để giao hàng
     * @param string $orderId Mã đơn hàng
     * @param array $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function splitDelivery(string $orderId, array $data): bool
    {
        $orderInfo = $this->dao->get(['order_id' => $orderId]);
        if (!$orderInfo) {
            throw new ApiException('Không tìm thấy đơn hàng, không thể giao hàng');
        }

        /** @var StoreOrderDeliveryServices $deliveryServices */
        $deliveryServices = app()->make(StoreOrderDeliveryServices::class);
        return $deliveryServices->splitDelivery((int)$orderInfo['id'], $data);
    }

    /**
     * Đặt hóa đơn
     * @param string $orderId Mã đơn hàng
     * @param array $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setInvoice(string $orderId, array $data): bool
    {
        $orderInfo = $this->dao->get(['order_id' => $orderId], ['id'], ['invoice']);
        if (!$orderInfo) {
            throw new AdminException('Đơn hàng không tồn tại');
        }

        if (!$orderInfo->invoice || !$invoiceId = $orderInfo->invoice->id) {
            throw new ApiException('Dữ liệu không tồn tại');
        }

        /** @var StoreOrderInvoiceServices $invoiceServices */
        $invoiceServices = app()->make(StoreOrderInvoiceServices::class);
        return $invoiceServices->setInvoice($invoiceId, $data);
    }

    /**
     * Sửa thông tin giao hàng
     * @param string $orderId Mã đơn hàng
     * @param array $data
     * @return mixed
     */
    public function updateDistribution(string $orderId, array $data)
    {
        $orderInfo = $this->dao->get(['order_id' => $orderId]);
        if (!$orderInfo) {
            throw new AdminException('Đơn hàng không tồn tại');
        }

        /** @var StoreOrderDeliveryServices $deliveryServices */
        $deliveryServices = app()->make(StoreOrderDeliveryServices::class);
        return $deliveryServices->updateDistribution($orderInfo['id'], $data);
    }

    /**
     * Đẩy thông báo đơn hàng
     * @param int $id
     * @param string $pushUrl
     * @return bool
     */
    public function orderCreatePush(int $id, string $pushUrl): bool
    {
        $orderInfo = $this->getInfo('', $id);
        return out_push($pushUrl, $orderInfo, 'Đơn hàng');
    }

    /**
     * Đẩy thông báo thanh toán
     * @param int $id
     * @param string $pushUrl
     * @return bool
     */
    public function paySuccessPush(int $id, string $pushUrl): bool
    {
        $orderInfo = $this->getInfo('', $id);
        return out_push($pushUrl, $orderInfo, 'Thanh toán đơn hàng');
    }
}
