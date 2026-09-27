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
namespace app\adminapi\controller\v1\order;

use app\adminapi\controller\AuthController;
use app\adminapi\validate\order\StoreOrderValidate;
use app\jobs\MiniOrderJob;
use app\jobs\OrderExpressJob;
use app\services\serve\ServeServices;
use app\services\wechat\WechatUserServices;
use crmeb\services\FileService;
use app\services\order\{StoreOrderCartInfoServices,
    StoreOrderDeliveryServices,
    StoreOrderRefundServices,
    StoreOrderStatusServices,
    StoreOrderTakeServices,
    StoreOrderWriteOffServices,
    StoreOrderServices
};
use app\services\pay\OrderOfflineServices;
use app\services\shipping\ExpressServices;
use app\services\system\store\SystemStoreServices;
use app\services\user\UserServices;
use think\facade\App;

/**
 * Quản lý đơn hàng
 * Class StoreOrder
 * @package app\adminapi\controller\v1\order
 */
class StoreOrder extends AuthController
{
    /**
     * StoreOrder constructor.
     * @param App $app
     * @param StoreOrderServices $service
     * @method temp
     */
    public function __construct(App $app, StoreOrderServices $service)
    {
        parent::__construct($app);
        $this->services = $service;
    }

    /**
     * Lấy số lượng theo loại đơn hàng
     * @return mixed
     */
    public function chart()
    {
        $where = $this->request->getMore([
            ['data', '', '', 'time'],
            ['type', ''],
            ['pay_type', ''],
            ['field_key', 'all'],
            ['real_name', ''],
        ]);
        $data = $this->services->orderCount($where);
        return app('json')->success($data);
    }

    /**
     * Danh sách đơn hàng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lst()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['real_name', ''],
            ['is_del', ''],
            ['data', '', '', 'time'],
            ['type', ''],
            ['pay_type', ''],
            ['order', ''],
            ['field_key', ''],
        ]);
        $where['is_system_del'] = 0;
        $where['pid'] = 0;
        if ($where['status'] == 1) $where = $where + ['shipping_type' => 1];
        return app('json')->success($this->services->getOrderList($where, ['*'], ['split' => function ($query) {
            $query->field('id,pid');
        }, 'pink', 'invoice', 'division']));
    }

    /**
     * Xác nhận sử dụng bằng mã xác nhận
     * @param StoreOrderWriteOffServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function write_order(StoreOrderWriteOffServices $services)
    {
        [$code, $confirm] = $this->request->getMore([
            ['code', ''],
            ['confirm', 0]
        ], true);
        if (!$code) return app('json')->fail('Tham số không hợp lệ');
        $orderInfo = $services->writeOffOrder($code, (int)$confirm);
        if ($confirm == 0) {
            return app('json')->success('Xác thực thành công', $orderInfo);
        }
        return app('json')->success('Xác nhận sử dụng thành công');
    }

    /**
     * Xác nhận sử dụng theo mã đơn hàng
     * @param StoreOrderWriteOffServices $services
     * @param $order_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function write_update(StoreOrderWriteOffServices $services, $order_id)
    {
        $orderInfo = $this->services->getOne(['order_id' => $order_id, 'is_del' => 0]);
        if ($orderInfo->shipping_type != 2 && $orderInfo->delivery_type != 'send') {
            return app('json')->fail('Không tìm thấy đơn hàng cần xác nhận sử dụng');
        } else {
            if (!$orderInfo->verify_code) {
                return app('json')->fail('Tham số không hợp lệ');
            }
            $orderInfo = $services->writeOffOrder($orderInfo->verify_code, 1);
            if ($orderInfo) {
                return app('json')->success('Xác thực thành công');
            } else {
                return app('json')->fail('Xác nhận sử dụng thất bại');
            }
        }
    }

    /**
     * Form đổi giá đơn hàng
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        return app('json')->success($this->services->updateForm($id));
    }

    /**
     * Sửa giá đơn hàng
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function update($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $data = $this->request->postMore([
            ['order_id', ''],
            ['total_price', 0],
            ['total_postage', 0],
            ['pay_price', 0],
            ['pay_postage', 0],
            ['gain_integral', 0],
        ]);

        $this->validate($data, StoreOrderValidate::class);

        if ($data['total_price'] < 0) return app('json')->fail('Số tiền đơn hàng là bắt buộc');
        if ($data['pay_price'] < 0) return app('json')->fail('Số tiền đơn hàng là bắt buộc');

        $this->services->updateOrder((int)$id, $data);
        return app('json')->success('Sửa thành công');
    }

    /**
     * Lấy đơn vị vận chuyển
     * @return mixed
     */
    public function express(ExpressServices $services)
    {
        [$status] = $this->request->getMore([
            ['status', ''],
        ], true);
        if ($status != '') $data['status'] = $status;
        if ($status == 'undefined') $data['status'] = 1;
        $data['is_show'] = 1;
        return app('json')->success($services->express($data));
    }

    /**
     * Xóa theo lô các đơn hàng người dùng đã xóa
     * @return mixed
     */
    public function del_orders()
    {
        [$ids] = $this->request->postMore([
            ['ids', []],
        ], true);
        if (!count($ids)) return app('json')->fail('Vui lòng chọn đơn hàng cần xóa');
        if ($this->services->getOrderIdsCount($ids))
            return app('json')->fail('Trong các đơn hàng bạn chọn có đơn người dùng chưa xóa');
        if ($this->services->batchUpdate($ids, ['is_system_del' => 1]))
            return app('json')->success('Xóa thành công');
        else
            return app('json')->fail('Xóa thất bại');
    }

    /**
     * Xóa đơn hàng
     * @param $id
     * @return mixed
     */
    public function del($id)
    {
        if (!$id || !($orderInfo = $this->services->get($id)))
            return app('json')->fail('Đơn hàng không tồn tại');
        if (!$orderInfo->is_del)
            return app('json')->fail('Trong các đơn hàng bạn chọn có đơn người dùng chưa xóa');
        $orderInfo->is_system_del = 1;
        if ($orderInfo->save()) {
            /** @var StoreOrderRefundServices $refundServices */
            $refundServices = app()->make(StoreOrderRefundServices::class);
            $refundServices->update(['store_order_id' => $id], ['is_system_del' => 1]);
            return app('json')->success('Xóa thành công');
        } else
            return app('json')->fail('Xóa thất bại');
    }

    /**
     * Thực hiện giao đơn hàng
     * @param $id
     * @param StoreOrderDeliveryServices $services
     * @return mixed
     */
    public function update_delivery($id, StoreOrderDeliveryServices $services)
    {
        $data = $this->request->postMore([
            ['type', 1],
            ['delivery_name', ''],//Tên đơn vị vận chuyển
            ['delivery_id', ''],//Mã vận đơn
            ['delivery_code', ''],//Mã đơn vị vận chuyển

            ['express_record_type', 2],//Loại lịch sử giao hàng: 2 = vận đơn điện tử; 3 = shop tự gửi hàng
            ['express_temp_id', ""],//Mẫu vận đơn điện tử
            ['to_name', ''],//Họ tên người gửi
            ['to_tel', ''],//Số điện thoại người gửi
            ['to_addr', ''],//Địa chỉ người gửi

            ['sh_delivery_name', ''],//Họ tên người giao hàng
            ['sh_delivery_id', ''],//Số điện thoại người giao hàng
            ['sh_delivery_uid', ''],//ID người giao hàng

            ['fictitious_content', ''],//Nội dung giao hàng ảo

            ['day_type', 0], //Tham số gửi SF Express: 0 là hôm nay, 1 là ngày mai, 2 là chọn ở backend
            ['pickup_time', []],//Thời gian bắt đầu 9:00, thời gian kết thúc 10:00. Khoảng giữa thời gian bắt đầu và kết thúc không được nhỏ hơn 1 giờ
        ]);
        return app('json')->success('Thao tác thành công', $services->delivery((int)$id, $data));
    }

    /**
     * Tách đơn để giao hàng
     * @param $id
     * @param StoreOrderDeliveryServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function split_delivery($id, StoreOrderDeliveryServices $services)
    {
        $data = $this->request->postMore([
            ['type', 1],
            ['delivery_name', ''],//Tên đơn vị vận chuyển
            ['delivery_id', ''],//Mã vận đơn
            ['delivery_code', ''],//Mã đơn vị vận chuyển

            ['express_record_type', 2],//Loại bản ghi giao hàng
            ['express_temp_id', ""],//Mẫu vận đơn điện tử
            ['to_name', ''],//Họ tên người gửi
            ['to_tel', ''],//Số điện thoại người gửi
            ['to_addr', ''],//Địa chỉ người gửi

            ['sh_delivery_name', ''],//Họ tên người giao hàng
            ['sh_delivery_id', ''],//Số điện thoại người giao hàng
            ['sh_delivery_uid', ''],//ID người giao hàng

            ['fictitious_content', ''],//Nội dung giao hàng ảo

            ['cart_ids', []],

            ['day_type', 0], //Tham số gửi SF Express: 0 là hôm nay, 1 là ngày mai, 2 là chọn ở backend
            ['pickup_time', []],//Thời gian bắt đầu 9:00, thời gian kết thúc 10:00. Khoảng giữa thời gian bắt đầu và kết thúc không được nhỏ hơn 1 giờ
            ['service_type', ''],//Loại nghiệp vụ vận chuyển
        ]);
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        if (!$data['cart_ids']) {
            return app('json')->fail('Vui lòng chọn sản phẩm cần giao');
        }
        foreach ($data['cart_ids'] as $cart) {
            if (!isset($cart['cart_id']) || !$cart['cart_id'] || !isset($cart['cart_num']) || !$cart['cart_num']) {
                return app('json')->fail('Vui lòng chọn lại sản phẩm cần giao hoặc số lượng giao');
            }
        }
        return app('json')->success('Thao tác thành công', $services->splitDelivery((int)$id, $data));
    }

    /**
     * Lấy số tiền tạm giữ khi gửi hàng
     * @param ServeServices $services
     * @return \think\Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/6/16
     */
    public function getPrice(ServeServices $services)
    {
        $data = $this->request->postMore([
            ['kuaidicom', ''],
            ['send_address', ''],
            ['orderId', ''],
            ['service_type', ''],
            ['cart_ids', []],
        ]);

        $orderInfo = $this->services->get($data['orderId'], ['user_address', 'cart_id']);
        if (!$orderInfo) {
            return app('json')->fail('Không tìm thấy đơn hàng');
        }
        $weight = '0';
        if ($data['cart_ids']) {
            $cartIds = array_column($data['cart_ids'], 'cart_id');
            $cartList = app()->make(StoreOrderCartInfoServices::class)->getColumn([
                ['cart_id', 'in', $cartIds]
            ], 'cart_info', 'cart_id');
            foreach ($data['cart_ids'] as $cart) {
                if (!isset($cart['cart_id']) || !$cart['cart_id'] || !isset($cart['cart_num']) || !$cart['cart_num']) {
                    return app('json')->fail('Vui lòng chọn lại sản phẩm cần giao hoặc số lượng giao');
                }
                if (isset($cartList[$cart['cart_id']])) {
                    $value = is_string($cartList[$cart['cart_id']]) ? json_decode($cartList[$cart['cart_id']], true) : $cartList[$cart['cart_id']];
                    $weightnew = bcmul($value['attrInfo']['weight'], (string)$cart['cart_num'], 2);
                    $weight = bcadd($weightnew, $weight, 2);
                }
            }
        } else {
            $orderCartInfoList = app()->make(StoreOrderCartInfoServices::class)->getCartInfoPrintProduct($data['orderId']);
            foreach ($orderCartInfoList as $item) {
                $weightnew = bcmul($item['attrInfo']['weight'], (string)$item['cart_num'], 2);
                $weight = bcadd($weightnew, $weight, 2);
            }
        }
        $data['address'] = $orderInfo['user_address'];
        if ($weight > 0) {
            $data['weight'] = $weight;
        }
        return app('json')->success($services->express()->getPrice($data));
    }

    /**
     * Lấy danh sách sản phẩm có thể tách để giao trong đơn hàng
     * @param $id
     * @param StoreOrderCartInfoServices $services
     * @return mixed
     */
    public function split_cart_info($id, StoreOrderCartInfoServices $services)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        return app('json')->success($services->getSplitCartList((int)$id));
    }

    /**
     * Lấy danh sách đơn con đã tách của đơn hàng
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function split_order($id)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        return app('json')->success($this->services->getSplitOrderList(['pid' => $id, 'is_system_del' => 0], ['*'], ['split', 'pink', 'invoice']));
    }


    /**
     * Xác nhận đã nhận hàng
     * @param $id ID đơn hàng
     * @return mixed
     * @throws \Exception
     */
    public function take_delivery(StoreOrderTakeServices $services, $id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $order = $this->services->get($id);
        if (!$order)
            return app('json')->fail('Đơn hàng không tồn tại');
        if ($order['status'] == 2)
            return app('json')->fail('Không thể xác nhận nhận hàng nhiều lần');
        if ($order['paid'] == 1 && $order['status'] == 1)
            $data['status'] = 2;
        else if ($order['pay_type'] == 'offline')
            $data['status'] = 2;
        else
            return app('json')->fail('Vui lòng giao hàng hoặc giao tận nơi trước');

        if (!$this->services->update($id, $data)) {
            return app('json')->fail('Xác nhận nhận hàng thất bại, vui lòng thử lại sau');
        } else {
            $services->storeProductOrderUserTakeDelivery($order);
            return app('json')->success('Xác nhận nhận hàng thành công');
        }
    }


    /**
     * Lấy thông tin cấu hình
     * @return mixed
     */
    public function getDeliveryInfo()
    {
        return app('json')->success([
            'express_temp_id' => sys_config('config_export_temp_id'),
            'id' => sys_config('config_export_id'),
            'to_name' => sys_config('config_export_to_name'),
            'to_tel' => sys_config('config_export_to_tel'),
            'to_add' => sys_config('config_export_to_address'),
            'export_open' => (bool)((int)sys_config('config_export_open'))
        ]);
    }

    /**
     * Tạo form hoàn tiền
     * @param $id ID đơn hàng
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function refund(StoreOrderRefundServices $services, $id)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        return app('json')->success($services->refundOrderForm((int)$id, 'order'));
    }

    /**
     * Hoàn tiền đơn hàng
     * @param $id ID đơn hàng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function update_refund(StoreOrderRefundServices $services, $id)
    {
        $data = $this->request->postMore([
            ['refund_price', 0],
            ['cart_ids', []]
        ]);
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        $order = $this->services->get($id);
        if (!$order) {
            return app('json')->fail('Đơn hàng không tồn tại');
        }

        $refundData = [
            'refund_reason' => 'Hoàn tiền chủ động từ trang quản trị',
            'refund_explain' => 'Hoàn tiền chủ động từ trang quản trị',
            'refund_img' => json_encode([]),
        ];

        $res = $services->applyRefund((int)$id, $order['uid'], $order, $data['cart_ids'], 1, (float)$data['refund_price'], $refundData);

        if (!$res) {
            return app('json')->fail('Tạo phiếu hoàn tiền thất bại');
        }

        $orderRefund = $services->getOrderOne(['store_order_id' => $id]);


        $data['refund_status'] = 2;
        $data['refund_type'] = 6;
        $data['refunded_time'] = time();

        //Hoàn tiền 0đ
        if ($orderRefund['refund_price'] == 0 && in_array($orderRefund['refund_type'], [1, 5])) {
            $refund_price = 0;
        } else {
            if (!$data['refund_price']) {
                return app('json')->fail('Vui lòng nhập số tiền hoàn');
            }
            if ($orderRefund['refund_price'] == $orderRefund['refunded_price']) {
                return app('json')->fail('Đã hoàn hết số tiền thanh toán, không thể hoàn tiền thêm');
            }
            $refund_price = $data['refund_price'];
        }

        $data['refunded_price'] = bcadd($data['refund_price'], $orderRefund['refunded_price'], 2);
        $bj = bccomp((string)$orderRefund['refund_price'], (string)$data['refunded_price'], 2);
        if ($bj < 0) {
            return app('json')->fail('Số tiền hoàn lớn hơn số tiền đã thanh toán, vui lòng sửa lại số tiền hoàn');
        }

        $refund_data['pay_price'] = $order['pay_price'];
        $refund_data['refund_price'] = $refund_price;
        if ($order['refund_price'] > 0) {
            mt_srand();
            $refund_data['refund_id'] = $order['order_id'] . rand(100, 999);
        }
        ($order['pid'] > 0) ? $refund_data['order_id'] = $this->services->value(['id' => (int)$order['pid']], 'order_id') : $refund_data['order_id'] = $order['order_id'];
        /** @var WechatUserServices $wechatUserServices */
        $wechatUserServices = app()->make(WechatUserServices::class);
        $refund_data['open_id'] = $wechatUserServices->uidToOpenid((int)$order['uid'], 'routine') ?? '';
        $refund_data['refund_no'] = $orderRefund['order_id'];
        $refund_data['order_id'] = $orderRefund['order_id'];
        //Sửa trạng thái hoàn tiền đơn hàng
        unset($data['refund_price']);
        if ($services->agreeRefund($orderRefund['id'], $refund_data)) {
            $services->update($orderRefund['id'], $data);
            return app('json')->success('Hoàn tiền thành công');
        } else {
            $services->storeProductOrderRefundYFasle((int)$orderRefund['id'], $refund_price);
            return app('json')->fail('Hoàn tiền thất bại');
        }
    }

    /**
     * Chi tiết đơn hàng
     * @param $id ID đơn hàng
     * @return mixed
     * @throws \ReflectionException
     */
    public function order_info($id)
    {
        if (!$id || !($orderInfo = $this->services->get($id, [], ['refund', 'invoice']))) {
            return app('json')->fail('Đơn hàng không tồn tại');
        }
        /** @var UserServices $services */
        $services = app()->make(UserServices::class);
        $userInfo = $services->get($orderInfo['uid']);
        if (!$userInfo) return app('json')->fail('Thông tin người dùng không tồn tại');
        $userInfo = $userInfo->hidden(['pwd', 'add_ip', 'last_ip', 'login_type']);
        $userInfo['spread_name'] = 'Không có';
        if ($userInfo['spread_uid']) {
            $spreadName = $services->value(['uid' => $userInfo['spread_uid']], 'nickname');
            if ($spreadName) {
                $userInfo['spread_name'] = $spreadName;
            } else {
                $userInfo['spread_uid'] = '';
            }
        } else {
            $userInfo['spread_uid'] = '';
        }

        $orderInfo = $this->services->tidyOrder($orderInfo->toArray(), true, true);
        //Tính toán số tiền ưu đãi
        $vipTruePrice = $levelPrice = $memberPrice = 0;
        foreach ($orderInfo['cartInfo'] as $cart) {
            $vipTruePrice = bcadd((string)$vipTruePrice, (string)$cart['vip_sum_truePrice'], 2);
            if ($cart['price_type'] == 'member') $memberPrice = bcadd((string)$memberPrice, (string)$cart['vip_sum_truePrice'], 2);
            if ($cart['price_type'] == 'level') $levelPrice = bcadd((string)$levelPrice, (string)$cart['vip_sum_truePrice'], 2);
        }
        $orderInfo['vip_true_price'] = $vipTruePrice;
        $orderInfo['levelPrice'] = $levelPrice;
        $orderInfo['memberPrice'] = $memberPrice;
        $orderInfo['total_price'] = bcadd($orderInfo['total_price'], $orderInfo['vip_true_price'], 2);
        if ($orderInfo['store_id'] && $orderInfo['shipping_type'] == 2) {
            /** @var  $storeServices */
            $storeServices = app()->make(SystemStoreServices::class);
            $orderInfo['_store_name'] = $storeServices->value(['id' => $orderInfo['store_id']], 'name');
        } else
            $orderInfo['_store_name'] = '';
        $orderInfo['spread_name'] = $services->value(['uid' => $orderInfo['spread_uid']], 'nickname') ?? 'Không có';
        $orderInfo['_info'] = app()->make(StoreOrderCartInfoServices::class)->getOrderCartInfo((int)$orderInfo['id']);
        $cart_num = 0;
        $refund_num = array_sum(array_column($orderInfo['refund'], 'refund_num'));
        foreach ($orderInfo['_info'] as $items) {
            $cart_num += $items['cart_info']['cart_num'];
        }
        $orderInfo['is_all_refund'] = $refund_num == $cart_num;
        $userInfo = $userInfo->toArray();
        return app('json')->success(compact('orderInfo', 'userInfo'));
    }

    /**
     * Truy vấn thông tin vận chuyển
     * @param $id ID đơn hàng
     * @return mixed
     */
    public function get_express($id, ExpressServices $services)
    {
        if (!$id || !($orderInfo = $this->services->get($id)))
            return app('json')->fail('Đơn hàng không tồn tại');
        if ($orderInfo['delivery_type'] != 'express' || !$orderInfo['delivery_id'])
            return app('json')->fail('Mã vận đơn không tồn tại');

        $cacheName = $orderInfo['order_id'] . $orderInfo['delivery_id'];

        $data['delivery_name'] = $orderInfo['delivery_name'];
        $data['delivery_id'] = $orderInfo['delivery_id'];
        $data['result'] = $services->query($cacheName, $orderInfo['delivery_id'], $orderInfo['delivery_code'] ?? null, $orderInfo['user_phone']);
        return app('json')->success($data);
    }


    /**
     * Lấy cấu trúc form sửa thông tin giao hàng
     * @param $id ID đơn hàng
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function distribution(StoreOrderDeliveryServices $services, $id)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        return app('json')->success($services->distributionForm((int)$id));
    }

    /**
     * Sửa thông tin giao hàng
     * @param $id  ID đơn hàng
     * @return mixed
     */
    public function update_distribution(StoreOrderDeliveryServices $services, $id)
    {
        $data = $this->request->postMore([['delivery_name', ''], ['delivery_code', ''], ['delivery_id', '']]);
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $services->updateDistribution($id, $data);
        return app('json')->success('Thao tác thành công');
    }

    /**
     * Cấu trúc form không hoàn tiền
     * @param StoreOrderRefundServices $services
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function no_refund(StoreOrderRefundServices $services, $id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        return app('json')->success($services->noRefundForm((int)$id));
    }

    /**
     * Đơn hàng không hoàn tiền
     * @param StoreOrderRefundServices $services
     * @param $id
     * @return mixed
     */
    public function update_un_refund(StoreOrderRefundServices $services, $id)
    {
        if (!$id || !($orderInfo = $this->services->get($id)))
            return app('json')->fail('Đơn hàng không tồn tại');
        [$refund_reason] = $this->request->postMore([['refund_reason', '']], true);
        if (!$refund_reason) {
            return app('json')->fail('Lý do từ chối không được để trống');
        }
        $orderInfo->refund_reason = $refund_reason;
        $orderInfo->refund_status = 0;
        $orderInfo->refund_type = 3;
        $orderInfo->save();
        if ($orderInfo->pid > 0) {
            $res1 = $this->services->getCount([
                ['pid', '=', $orderInfo->pid],
                ['refund_type', '>', 0],
                ['refund_type', '<>', 3],
            ]);
            if ($res1 == 0) {
                $this->services->update($orderInfo->pid, ['refund_status' => 0]);
            }
        }
        $services->storeProductOrderRefundNo((int)$id, $refund_reason);
        //Đẩy thông báo nhắc nhở
        event('NoticeListener', [['orderInfo' => $orderInfo], 'send_order_refund_no_status']);

        //Thông báo tùy chỉnh - Đơn hàng từ chối hoàn tiền
        $orderInfo['time'] = date('Y-m-d H:i:s');
        $orderInfo['phone'] = $orderInfo['user_phone'];
        event('CustomNoticeListener', [$orderInfo['uid'], $orderInfo, 'order_refund_fail']);

        return app('json')->success('Thao tác thành công');
    }

    /**
     * Thanh toán ngoại tuyến
     * @param $id ID đơn hàng
     * @return mixed
     */
    public function pay_offline(OrderOfflineServices $services, $id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $res = $services->orderOffline((int)$id);
        if ($res) {
            return app('json')->success('Thao tác thành công');
        } else {
            return app('json')->fail('Thao tác thất bại');
        }
    }

    /**
     * Lấy form trả điểm thưởng
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function refund_integral(StoreOrderRefundServices $services, $id)
    {
        if (!$id)
            return app('json')->fail('Tham số không hợp lệ');
        return app('json')->success($services->refundIntegralForm((int)$id));
    }

    /**
     * Lưu trả điểm thưởng
     * @param $id
     * @return mixed
     */
    public function update_refund_integral(StoreOrderRefundServices $services, $id)
    {
        [$back_integral] = $this->request->postMore([['back_integral', 0]], true);
        if (!$id || !($orderInfo = $this->services->get($id))) {
            return app('json')->fail('Đơn hàng không tồn tại');
        }
        if ($orderInfo->is_del) {
            return app('json')->fail('Đơn hàng đã bị xóa, không thể hoàn điểm thưởng');
        }
        if ($back_integral <= 0) {
            return app('json')->fail('Vui lòng nhập điểm thưởng');
        }
        if ($orderInfo['use_integral'] == $orderInfo['back_integral']) {
            return app('json')->fail('Đã hoàn hết điểm thưởng');
        }

        $data['back_integral'] = bcadd((string)$back_integral, (string)$orderInfo['back_integral'], 2);
        $bj = bccomp((string)$orderInfo['use_integral'], (string)$data['back_integral'], 2);
        if ($bj < 0) {
            return app('json')->fail('Số điểm hoàn lớn hơn số điểm đã thanh toán, vui lòng sửa lại số điểm hoàn');
        }
        //Xử lý hoàn điểm thưởng
        $orderInfo->back_integral = $data['back_integral'];
        if ($services->refundIntegral($orderInfo, $back_integral)) {
            return app('json')->success('Hoàn điểm thưởng thành công');
        } else {
            return app('json')->fail('Hoàn điểm thưởng thất bại');
        }
    }

    /**
     * Sửa ghi chú
     * @param $id
     * @return mixed
     */
    public function remark($id)
    {
        $data = $this->request->postMore([['remark', '']]);
        if (!$data['remark'])
            return app('json')->fail('Ghi chú không được để trống');
        if (!$id)
            return app('json')->fail('Tham số không hợp lệ');

        if (!$order = $this->services->get($id)) {
            return app('json')->fail('Đơn hàng không tồn tại');
        }
        $order->remark = $data['remark'];
        if ($order->save()) {
            return app('json')->success('Ghi chú thành công');
        } else
            return app('json')->fail('Ghi chú thất bại');
    }

    /**
     * Lấy danh sách trạng thái đơn hàng có phân trang
     * @param $id
     * @return mixed
     */
    public function status(StoreOrderStatusServices $services, $id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        return app('json')->success($services->getStatusList(['oid' => $id])['list']);
    }

    /**
     * In bằng máy in hóa đơn (biên lai)
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function order_print($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $res = $this->services->orderPrintTicket($id, true);
        if ($res) {
            return app('json')->success('Thao tác thành công');
        } else {
            return app('json')->fail('Thao tác thất bại');
        }
    }

    /**
     * Mẫu vận đơn điện tử
     * @param $com
     * @return mixed
     */
    public function expr_temp(ServeServices $services, $com)
    {
        if (!$com) {
            return app('json')->fail('Thiếu mã đơn vị vận chuyển');
        }
        $list = $services->express()->temp($com);
        return app('json')->success($list);
    }

    /**
     * Lấy mẫu
     */
    public function express_temp(ServeServices $services)
    {
        $data = $this->request->getMore([['com', '']]);
        if (!$data['com']) {
            return app('json')->fail('Thiếu mã đơn vị vận chuyển');
        }
        $tpd = $services->express()->temp($data['com']);
        return app('json')->success($tpd['data']);
    }

    /**
     * In vận đơn điện tử sau khi đơn hàng được giao
     * @param $orderId
     * @param StoreOrderDeliveryServices $storeOrderDeliveryServices
     * @return mixed
     */
    public function order_dump($order_id, StoreOrderDeliveryServices $storeOrderDeliveryServices)
    {
        $storeOrderDeliveryServices->orderDump($order_id);
        return app('json')->success('In thành công');
    }

    /**
     * Lấy thông tin chuyển phát
     * @param ServeServices $services
     * @return \think\Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/15
     */
    public function getKuaidiComs(ServeServices $services)
    {
        MiniOrderJob::dispatch('syncOrderShipping');
        return app('json')->success($services->express()->getKuaidiComs());
    }

    /**
     * Hủy yêu cầu gửi hàng của người bán
     * @param $id
     * @return \think\Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/15
     */
    public function shipmentCancelOrder($id)
    {
        if (!$id) {
            return app('json')->fail('Thiếu tham số');
        }

        $msg = $this->request->post('msg', '');
        if (!$msg) {
            return app('json')->fail('Vui lòng nhập lý do hủy gửi hàng');
        }
        if ($this->services->shipmentCancelOrder((int)$id, $msg)) {
            return app('json')->success('Hủy thành công');
        } else {
            return app('json')->fail('Hủy thất bại');
        }
    }

    /**
     * Nhập giao hàng theo lô
     * @return \think\Response|void
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function importExpress()
    {
        [$file] = $this->request->getMore([
            ['file', '']
        ], true);
        if (!$file) return app('json')->fail('Vui lòng tải lên tệp');
        $file = public_path() . substr($file, 1);
        // Lấy phần mở rộng của file
        $suffix = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($suffix, ['xls', 'xlsx'])) {
            return app('json')->fail('Định dạng tệp không đúng, vui lòng tải lên tệp định dạng xls hoặc xlsx!');
        }
        $expressData = app()->make(FileService::class)->readExcel($file, 'express', 2, ucfirst($suffix));
        foreach ($expressData as $item) {
            OrderExpressJob::dispatch([$item]);
        }
        return app('json')->success('Giao hàng hàng loạt thành công');
    }

    /**
     * Phiếu soạn hàng
     * @param $order_id
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/10/11
     */
    public function printShipping($order_id)
    {
        if (!$order_id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        $data = $this->services->printShippingData($order_id);
        return app('json')->success($data);
    }

    /**
     * Sửa địa chỉ nhận hàng
     * @param $id
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/9/8
     */
    public function editAddress($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $data = $this->request->postMore([
            ['real_name', ''],
            ['user_phone', ''],
            ['user_address', '']
        ]);
        if (!$data['real_name']) return app('json')->fail('Vui lòng nhập họ tên người nhận');
        if (!$data['user_phone']) return app('json')->fail('Vui lòng nhập số điện thoại người nhận');
        if (!$data['user_address']) return app('json')->fail('Vui lòng nhập địa chỉ người nhận');
        $this->services->editAddress($id, $data);
        return app('json')->success('Sửa thành công');
    }
}
