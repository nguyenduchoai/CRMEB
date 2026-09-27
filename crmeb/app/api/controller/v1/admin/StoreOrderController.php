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
namespace app\api\controller\v1\admin;

use app\Request;
use app\services\order\DeliveryServiceServices;
use app\services\order\StoreOrderCartInfoServices;
use app\services\order\StoreOrderCreateServices;
use app\services\order\StoreOrderDeliveryServices;
use app\services\order\StoreOrderEconomizeServices;
use app\services\order\StoreOrderRefundServices;
use app\services\order\StoreOrderServices;
use app\services\order\StoreOrderWapServices;
use app\services\order\StoreOrderWriteOffServices;
use app\services\pay\OrderOfflineServices;
use app\services\serve\ServeServices;
use app\services\user\UserServices;
use app\services\shipping\ExpressServices;

/**
 * Lớp đơn hàng
 * Class StoreOrderController
 * @package app\api\controller\admin\order
 */
class StoreOrderController
{
    /**
     * @var StoreOrderWapServices
     */
    protected $service;

    /**
     * StoreOrderController constructor.
     * @param StoreOrderWapServices $services
     */
    public function __construct(StoreOrderWapServices $services)
    {
        $this->service = $services;
    }


    /**
     * Đơn hàng - Xem vận chuyển
     * @param StoreOrderCartInfoServices $services
     * @param ExpressServices $expressServices
     * @param $uni
     * @param string $type
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function express(StoreOrderServices $orderServices, StoreOrderCartInfoServices $services, ExpressServices $expressServices, $uni, $type = '')
    {
        if ($type == 'refund') {
            /** @var StoreOrderRefundServices $refundService */
            $refundService = app()->make(StoreOrderRefundServices::class);
            $order = $refundService->refundDetail($uni);
            $express = $order['refund_express'];
            $cacheName = $uni . $express;
            $orderInfo = [];
            $info = [];
            $cartNew = [];
            foreach ($order['cart_info'] as $k => $cart) {
                $cartNew['cart_num'] = $cart['cart_num'];
                $cartNew['truePrice'] = $cart['truePrice'];
                $cartNew['postage_price'] = $cart['postage_price'];
                $cartNew['productInfo']['image'] = $cart['productInfo']['image'];
                $cartNew['productInfo']['store_name'] = $cart['productInfo']['store_name'];
                $cartNew['productInfo']['unit_name'] = $cart['productInfo']['unit_name'] ?? '';
                array_push($info, $cartNew);
                unset($cart);
            }
            $orderInfo['cartInfo'] = $info;
            $orderInfo['delivery_id'] = $express;
            $orderInfo['delivery_name'] = $order['refund_express_name'];
            $orderInfo['delivery_code'] = '';
        } else {
            if (!$uni || !($order = $orderServices->getUserOrderDetail($uni, 0, []))) {
                return app('json')->fail(410173);
            }
            if ($type != 'refund' && ($order['delivery_type'] != 'express' || !$order['delivery_id'])) {
                return app('json')->fail(410206);
            }
            $express = $type == 'refund' ? $order['refund_express'] : $order['delivery_id'];
            $cacheName = $uni . $express;
            $orderInfo = [];
            $cartInfo = $services->getCartColunm(['oid' => $order['id']], 'cart_info', 'unique');
            $info = [];
            $cartNew = [];
            foreach ($cartInfo as $k => $cart) {
                $cart = json_decode($cart, true);
                $cartNew['cart_num'] = $cart['cart_num'];
                $cartNew['truePrice'] = $cart['truePrice'];
                $cartNew['postage_price'] = $cart['postage_price'];
                $cartNew['productInfo']['image'] = $cart['productInfo']['image'];
                $cartNew['productInfo']['store_name'] = $cart['productInfo']['store_name'];
                $cartNew['productInfo']['unit_name'] = $cart['productInfo']['unit_name'] ?? '';
                array_push($info, $cartNew);
                unset($cart);
            }
            $orderInfo['delivery_id'] = $express;
            $orderInfo['delivery_name'] = $type == 'refund' ? 'Người dùng gửi trả' : $order['delivery_name'];;
            $orderInfo['delivery_code'] = $type == 'refund' ? '' : $order['delivery_code'];
            $orderInfo['delivery_type'] = $order['delivery_type'];
            $orderInfo['user_address'] = $order['user_address'];
            $orderInfo['user_mark'] = $order['mark'];
            $orderInfo['cartInfo'] = $info;
        }
        return app('json')->success([
            'order' => $orderInfo,
            'express' => [
                'result' => ['list' => $expressServices->query($cacheName, $orderInfo['delivery_id'], $orderInfo['delivery_code'], $order['user_phone'])
                ]
            ]
        ]);
    }

    /**
     * Thống kê dữ liệu đơn hàng
     * @param StoreOrderServices $services
     * @return mixed
     */
    public function statistics(StoreOrderServices $services)
    {
        $dataCount = $services->getOrderData();
        $dataPrice = $this->service->getOrderTimeData();
        $data = array_merge($dataCount, $dataPrice);
        return app('json')->success($data);
    }

    /**
     * Dữ liệu thống kê đơn hàng hằng tháng
     * @param Request $request
     * @return mixed
     */
    public function data(Request $request)
    {
        [$start, $stop] = $request->getMore([
            ['start', 0],
            ['stop', 0]
        ], true);
        return app('json')->success($this->service->getOrderDataPriceCount(['time' => [$start, $stop]]));
    }

    /**
     * Danh sách đơn hàng
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lst(Request $request)
    {
        $where = $request->getMore([
            ['status', ''],
            ['is_del', 0],
            ['data', '', '', 'time'],
            ['type', ''],
            ['field_key', ''],
            ['field_value', ''],
            ['keywords', '', '', 'real_name']
        ]);
        $where['is_system_del'] = 0;
        if (!in_array($where['status'], [-1, -2, -3])) {
            $where['pid'] = 0;
        }
        return app('json')->success($this->service->getWapAdminOrderList($where));
    }

    /**
     * Chi tiết đơn hàng
     * @param Request $request
     * @param StoreOrderServices $services
     * @param UserServices $userServices
     * @param $orderId
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail(Request $request, StoreOrderServices $services, UserServices $userServices, $orderId)
    {
        $economizeServices = app()->make(StoreOrderEconomizeServices::class);
        $orderData = $services->getUserOrderByKey($economizeServices, $orderId, 0);
        $orderData['nickname'] = $userServices->value(['uid' => $orderData['uid']], 'nickname');
        return app('json')->success($orderData);
    }

    /**
     * Lấy thông tin đơn hàng để giao hàng
     * @param UserServices $userServices
     * @param $orderId
     * @return mixed
     */
    public function delivery_gain(UserServices $userServices, $orderId)
    {
        $order = $this->service->getOne(['order_id' => $orderId], 'real_name,user_phone,user_address,order_id,uid,status,paid,id');
        if (!$order) return app('json')->fail(410173);
        if ($order['paid']) {
            $order['nickname'] = $userServices->value(['uid' => $order['uid']], 'nickname');
            $order['config_export_open'] = (bool)((int)sys_config('config_export_open'));
            $order = $order->hidden(['uid', 'status', 'paid'])->toArray();
            return app('json')->success($order);
        }
        return app('json')->fail(100016);
    }

    /**
     * Giao đơn hàng
     * @param Request $request
     * @param StoreOrderDeliveryServices $services
     * @param $id
     * @return mixed
     */
    public function delivery_keep(Request $request, StoreOrderDeliveryServices $services, $id)
    {
        $data = $request->postMore([
            ['type', 1],
            ['delivery_name', ''],//ID đơn vị vận chuyển
            ['delivery_id', ''],//Mã vận đơn
            ['delivery_code', ''],//Mã đơn vị vận chuyển
            ['delivery_type', ''],//Tên đơn vị vận chuyển

            ['express_record_type', 2],//Loại bản ghi giao hàng
            ['express_temp_id', ""],//Mẫu vận đơn điện tử
            ['to_name', ''],//Họ tên người gửi
            ['to_tel', ''],//Số điện thoại người gửi
            ['to_addr', ''],//Địa chỉ người gửi

            ['sh_delivery_name', ''],//Họ tên người giao hàng
            ['sh_delivery_id', ''],//Số điện thoại người giao hàng
            ['sh_delivery_uid', ''],//ID người giao hàng

            ['fictitious_content', ''],//Nội dung giao hàng ảo
            ['pickup_time', []]
        ]);
        if ($data['delivery_type']) {
            $data['delivery_name'] = $data['delivery_type'];
            unset($data['delivery_type']);
        }
        $services->delivery((int)$id, $data);
        return app('json')->success(410273);
    }

    /**
     * Sửa giá đơn hàng
     * @param Request $request
     * @param StoreOrderServices $services
     * @return mixed
     * @throws \Exception
     */
    public function price(Request $request, StoreOrderServices $services)
    {
        [$order_id, $price] = $request->postMore([
            ['order_id', ''],
            ['price', '']
        ], true);
        $order = $this->service->getOne(['order_id' => $order_id], 'id,user_phone,id,paid,pay_price,order_id,total_price,total_postage,pay_postage,gain_integral');
        if (!$order) return app('json')->fail(410173);
        if ($order['paid']) {
            return app('json')->fail(410174);
        }
        if ($price === '') return app('json')->fail(410175);
        if ($price < 0) return app('json')->fail(410176);
        if ($order['pay_price'] == $price) return app('json')->success(100001, ['order_id' => $order_id]);
        $order_id = $services->updateOrder($order['id'], ['total_price' => $order['total_price'], 'pay_price' => $price]);
        return app('json')->success(100001, ['order_id' => $order_id]);
    }

    /**
     * Ghi chú đơn hàng
     * @param Request $request
     * @return mixed
     */
    public function remark(Request $request)
    {
        [$order_id, $remark] = $request->postMore([
            ['order_id', ''],
            ['remark', '']
        ], true);
        $order = $this->service->getOne(['order_id' => $order_id], 'id,remark');
        if (!$order) return app('json')->fail(410173);
        if (!strlen(trim($remark))) return app('json')->fail(410177);
        $order->remark = $remark;
        if (!$order->save())
            return app('json')->fail(100025);
        return app('json')->success(100024);
    }

    /**
     * Thống kê theo thời gian giá trị giao dịch/số lượng đơn hàng
     * @param Request $request
     * @return bool
     */
    public function time(Request $request)
    {
        list($start, $stop, $type) = $request->getMore([
            ['start', strtotime(date('Y-m'))],
            ['stop', time()],
            ['type', 1]
        ], true);
        $start = strtotime(date('Y-m-d 00:00:00', (int)$start));
        $stop = strtotime(date('Y-m-d 23:59:59', (int)$stop));
        if ($start > $stop) {
            $middle = $stop;
            $stop = $start;
            $start = $middle;
        }
        $space = bcsub($stop, $start, 0);//Khoảng thời gian giãn cách
        $front = bcsub($start, $space, 0) - 1;//Khoảng thời gian đầu tiên
        /** @var StoreOrderServices $orderService */
        $orderService = app()->make(StoreOrderServices::class);
        $order_where = [
            'pid' => 0,
            'paid' => 1,
            'refund_status' => [0, 3],
            'is_del' => 0,
            'is_system_del' => 0
        ];

        if ($type == 1) {//Doanh số
            $frontPrice = $orderService->sum($order_where + ['time' => [$front, $start - 1]], 'pay_price', true);
            $afterPrice = $orderService->sum($order_where + ['time' => [$start, $stop]], 'pay_price', true);
            $chartInfo = $orderService->chartTimePrice($start, $stop);
            $data['chart'] = $chartInfo;//Dữ liệu biểu đồ doanh thu
            $data['time'] = $afterPrice;//Doanh thu trong khoảng thời gian
            $increase = (float)bcsub((string)$afterPrice, (string)$frontPrice, 2); //Doanh thu tăng so với khoảng thời gian trước
            $growthRate = abs($increase);
            if ($growthRate == 0) $data['growth_rate'] = 0;
            else if ($frontPrice == 0) $data['growth_rate'] = (int)bcmul($growthRate, 100, 0);
            else $data['growth_rate'] = (int)bcmul((string)bcdiv((string)$growthRate, (string)$frontPrice, 2), '100', 0);//Tỷ lệ tăng trưởng theo khoảng thời gian
            $data['increase_time'] = abs($increase); //Doanh thu tăng so với khoảng thời gian trước
            $data['increase_time_status'] = $increase >= 0 ? 1 : 2; //Doanh thu tăng so với khoảng thời gian trước: 1 là tăng, 2 là giảm
        } else {//Số đơn hàng
            $frontNumber = $orderService->count($order_where + ['time' => [$front, $start - 1]]);
            $afterNumber = $orderService->count($order_where + ['time' => [$start, $stop]]);
            $chartInfo = $orderService->chartTimeNumber($start, $stop);
            $data['chart'] = $chartInfo;//Dữ liệu biểu đồ số lượng đơn hàng
            $data['time'] = $afterNumber;//Số lượng đơn hàng trong khoảng thời gian
            $increase = $afterNumber - $frontNumber; //Số lượng đơn hàng tăng so với khoảng thời gian trước
            $growthRate = abs($increase);
            if ($growthRate == 0) $data['growth_rate'] = 0;
            else if ($frontNumber == 0) $data['growth_rate'] = (int)bcmul($growthRate, 100, 0);
            else $data['growth_rate'] = (int)bcmul((string)bcdiv((string)$growthRate, (string)$frontNumber, 2), '100', 0);//Tỷ lệ tăng trưởng theo khoảng thời gian
            $data['increase_time'] = abs($increase); //Doanh thu tăng so với khoảng thời gian trước
            $data['increase_time_status'] = $increase >= 0 ? 1 : 2; //Doanh thu tăng so với khoảng thời gian trước: 1 là tăng, 2 là giảm
        }
        return app('json')->success($data);
    }

    /**
     * Thanh toán đơn hàng
     * @param Request $request
     * @param OrderOfflineServices $services
     * @return mixed
     */
    public function offline(Request $request, OrderOfflineServices $services)
    {
        [$orderId] = $request->postMore([['order_id', '']], true);
        $orderInfo = $this->service->getOne(['order_id' => $orderId], 'id');
        if (!$orderInfo) return app('json')->fail(100100);
        $id = $orderInfo->id;
        $services->orderOffline((int)$id);
        return app('json')->success(100010);

    }

    /**
     * Hoàn tiền đơn hàng
     * @param Request $request
     * @param StoreOrderRefundServices $services
     * @param StoreOrderServices $orderServices
     * @param StoreOrderCartInfoServices $storeOrderCartInfoServices
     * @param StoreOrderCreateServices $storeOrderCreateServices
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function refund(Request $request, StoreOrderRefundServices $services, StoreOrderServices $orderServices, StoreOrderCartInfoServices $storeOrderCartInfoServices, StoreOrderCreateServices $storeOrderCreateServices)
    {
        list($orderId, $price, $type) = $request->postMore([
            ['order_id', ''],
            ['price', '0'],
            ['type', 1],
        ], true);
        if (!strlen(trim($orderId))) return app('json')->fail(100100);
        //Chi tiết đơn hoàn tiền
        $orderRefund = $services->getOne(['order_id' => $orderId]);
        $is_admin = 0;
        if (!$orderRefund) {
            //Chi tiết đơn hàng chính khi chủ động hoàn tiền
            $orderRefund = $orderServices->getOne(['order_id' => $orderId]);
            $is_admin = 1;
            if ($services->count(['store_order_id' => $orderRefund['id'], 'refund_type' => [0, 1, 2, 4, 5], 'is_cancel' => 0, 'is_del' => 0])) {
                return app('json')->fail(410178);
            }
        }
        if (!$is_admin) {
            if (!$orderRefund) {
                return app('json')->fail(100026);
            }
            if ($orderRefund['is_cancel'] == 1) {
                return app('json')->fail(410179);
            }
            $orderInfo = $this->service->get((int)$orderRefund['store_order_id']);
            if (!$orderInfo) {
                return app('json')->fail(100026);
            }
            if (!in_array($orderRefund['refund_type'], [1, 2, 5])) {
                return app('json')->fail(410180);
            }

            if ($type == 1) {
                $data['refund_type'] = 6;
            } else if ($type == 2) {
                $data['refund_type'] = 3;
            } else {
                return app('json')->fail(410181);
            }
            $data['refunded_time'] = time();
            //Từ chối hoàn tiền
            if ($type == 2) {
                $services->refuseRefund((int)$orderRefund['id'], $data, $orderRefund);
                return app('json')->success(410182);
            } else {
                if ($orderRefund['refund_price'] == $orderInfo['refunded_price']) return app('json')->fail(410183);
                if (!$price) {
                    return app('json')->fail(410184);
                }
                $data['refunded_price'] = bcadd($price, $orderRefund['refunded_price'], 2);
                $bj = bccomp((float)$orderRefund['refund_price'], (float)$data['refunded_price'], 2);
                if ($bj < 0) {
                    return app('json')->fail(410185);
                }
                $refundData['pay_price'] = $orderInfo['pay_price'];
                $refundData['refund_price'] = $price;
                $refundData['order_id'] = $orderId;


                //Sửa trạng thái hoàn tiền đơn hàng
                if ($services->agreeRefund((int)$orderRefund['id'], $refundData)) {
                    $services->update((int)$orderRefund['id'], $data);
                    return app('json')->success(410186);
                } else {
                    $services->storeProductOrderRefundYFasle((int)$orderInfo['id'], $price);
                    return app('json')->fail(410187);
                }
            }
        } else {
            $order = $orderRefund;
            $data['refund_price'] = $price;
            $data['type'] = $type;
            $id = $order['id'];
            //Hoàn tiền 0đ
            if ($order['pay_price'] == 0 && in_array($order['refund_status'], [0, 1])) {
                $refund_price = 0;
            } else {
                if ($order['pay_price'] == $order['refund_price']) {
                    return app('json')->fail(410183);
                }
                if (!$data['refund_price']) {
                    return app('json')->fail(410184);
                }
                $refund_price = $data['refund_price'];
                $data['refund_price'] = bcadd($data['refund_price'], $order['refund_price'], 2);
                $bj = bccomp((string)$order['pay_price'], (string)$data['refund_price'], 2);
                if ($bj < 0) {
                    return app('json')->fail(410185);
                }
            }
            if ($data['type'] == 1) {
                $data['refund_status'] = 2;
                $data['refund_type'] = 6;
            } else if ($data['type'] == 2) {
                $data['refund_status'] = 0;
                $data['refund_type'] = 3;
            }
            $type = $data['type'];
            //Từ chối hoàn tiền
            if ($type == 2) {
                $this->service->update((int)$order['id'], ['refund_status' => 0, 'refund_type' => 3]);
                return app('json')->success(410182);
            } else {
                unset($data['type']);
                $refund_data['pay_price'] = $order['pay_price'];
                $refund_data['refund_price'] = $refund_price;

                //Chủ động hoàn tiền, xóa đơn hoàn tiền ban đầu
                $services->delete(['store_order_id' => $id]);
                //Tạo đơn hoàn tiền
                $refundOrderData['uid'] = $order['uid'];
                $refundOrderData['store_id'] = $order['store_id'];
                $refundOrderData['store_order_id'] = $id;
                $refundOrderData['refund_num'] = $order['total_num'];
                $refundOrderData['refund_type'] = $data['refund_type'];
                $refundOrderData['refund_price'] = $order['pay_price'];
                $refundOrderData['refunded_price'] = $refund_price;
                $refundOrderData['refunded_reason'] = 'Quản trị viên hoàn tiền thủ công';
                $refundOrderData['order_id'] = $storeOrderCreateServices->getNewOrderId('');
                $refundOrderData['refunded_time'] = time();
                $refundOrderData['add_time'] = time();
                $cartInfos = $storeOrderCartInfoServices->getCartColunm(['oid' => $id], 'id,cart_id,cart_num,cart_info');
                foreach ($cartInfos as &$cartInfo) {
                    $cartInfo['cart_info'] = is_string($cartInfo['cart_info']) ? json_decode($cartInfo['cart_info'], true) : $cartInfo['cart_info'];
                }
                $refundOrderData['cart_info'] = json_encode(array_column($cartInfos, 'cart_info'));
                $res = $services->save($refundOrderData);
                $refund_data['order_id'] = $refundOrderData['order_id'];

                //Sửa trạng thái hoàn tiền đơn hàng
                if ($services->agreeRefund((int)$res->id, $refund_data)) {
                    $this->service->update($id, $data);
                    return app('json')->success(410186);
                } else {
                    $services->storeProductOrderRefundYFasle((int)$id, $refund_price);
                    return app('json')->fail(410187);
                }
            }
        }

    }

    /**
     * Xác nhận sử dụng tại cửa hàng
     * @param Request $request
     * @param StoreOrderWriteOffServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function order_verific(Request $request, StoreOrderWriteOffServices $services)
    {
        list($verifyCode, $isConfirm) = $request->postMore([
            ['verify_code', ''],
            ['is_confirm', 0]
        ], true);
        if (!$verifyCode) return app('json')->fail(410188);
        $uid = $request->uid();
        $orderInfo = $services->writeOffOrder($verifyCode, (int)$isConfirm, $uid);
        if ($isConfirm == 0) {
            return app('json')->success($orderInfo);
        }
        return app('json')->success(410189);
    }

    /**
     * Lấy danh sách tất cả người giao hàng
     * @param DeliveryServiceServices $services
     * @return mixed
     */
    public function getDeliveryAll(DeliveryServiceServices $services)
    {
        $list = $services->getDeliveryList();
        return app('json')->success($list['list']);
    }

    /**
     * Lấy thông tin cấu hình
     * @return mixed
     */
    public function getDeliveryInfo()
    {
        return app('json')->success([
            'express_temp_id' => sys_config('config_export_temp_id'),
            'to_name' => sys_config('config_export_to_name'),
            'id' => sys_config('config_export_id'),
            'to_tel' => sys_config('config_export_to_tel'),
            'to_add' => sys_config('config_export_to_address')
        ]);
    }

    /**
     * Lấy thông tin vận đơn
     * @param Request $request
     * @param ServeServices $services
     * @return mixed
     */
    public function getExportTemp(Request $request, ServeServices $services)
    {
        [$com] = $request->getMore([
            ['com', ''],
        ], true);
        return app('json')->success($services->express()->temp($com));
    }

    /**
     * Đơn vị vận chuyển
     * @param ExpressServices $services
     * @return mixed
     */
    public function getExportAll(ExpressServices $services)
    {
        return app('json')->success($services->expressList());
    }

    /**
     * Danh sách hoàn tiền trong quản lý đơn hàng trên di động
     * @param Request $request
     * @param StoreOrderRefundServices $services
     * @return mixed
     */
    public function refundOrderList(Request $request, StoreOrderRefundServices $services)
    {
        $where = $request->getMore([
            ['order_id', ''],
            ['time', ''],
            ['refund_type', 0],
            ['keywords', '', '', 'real_name'],
        ]);
        $where['is_cancel'] = 0;
        $data = $services->refundList($where)['list'];
        return app('json')->success($data);
    }

    /**
     * Chi tiết đơn hàng
     * @param StoreOrderRefundServices $services
     * @param $uni
     * @return mixed
     */
    public function refundOrderDetail(StoreOrderRefundServices $services, $uni)
    {
        $data = $services->refundDetail($uni);
        return app('json')->success($data);
    }

    /**
     * Ghi chú hoàn tiền
     * @param StoreOrderRefundServices $services
     * @param Request $request
     * @return mixed
     */
    public function refundRemark(StoreOrderRefundServices $services, Request $request)
    {
        [$remark, $order_id] = $request->postMore([
            ['remark', ''],
            ['order_id', ''],
        ], true);
        if (!$remark)
            return app('json')->fail(410177);
        if (!$order_id)
            return app('json')->fail(100100);

        if (!$order = $services->get(['order_id' => $order_id])) {
            return app('json')->fail(410173);
        }
        $order->remark = $remark;
        if ($order->save()) {
            return app('json')->success(100024);
        } else
            return app('json')->fail(100025);
    }

    /**
     * Đồng ý trả hàng
     * @param StoreOrderRefundServices $services
     * @param Request $request
     * @return mixed
     */
    public function agreeExpress(StoreOrderRefundServices $services, Request $request)
    {
        [$id] = $request->postMore([
            ['id', ''],
        ], true);
        $services->agreeExpress($id);
        return app('json')->success(100010);
    }
}
