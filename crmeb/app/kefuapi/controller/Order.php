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

namespace app\kefuapi\controller;


use app\Request;
use app\services\order\StoreOrderWriteOffServices;
use think\facade\App;
use app\services\order\DeliveryServiceServices;
use app\services\product\product\StoreProductServices;
use app\services\serve\ServeServices;
use app\services\shipping\ExpressServices;
use app\services\user\UserServices;
use app\services\order\StoreOrderServices;
use app\services\order\StoreOrderRefundServices;
use app\services\order\StoreOrderDeliveryServices;
use app\services\system\store\SystemStoreServices;
use app\adminapi\validate\order\StoreOrderValidate;
use app\services\kefu\service\StoreServiceRecordServices;

/**
 * Class Order
 * @package app\kefuapi\controller
 */
class Order extends AuthController
{

    /**
     * Order constructor.
     * @param App $app
     * @param StoreOrderServices $services
     */
    public function __construct(App $app, StoreOrderServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy danh sách đơn hàng
     * @param Request $request
     * @param $uid
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserOrderList(Request $request, StoreServiceRecordServices $services, $uid)
    {
        $where = $request->getMore([
            ['type', '', '', 'status'],
            ['search', '', '', 'real_name'],
        ]);
        $where['uid'] = $uid;
        $where['is_del'] = 0;
        $where['is_system_del'] = 0;
        if ($where['status'] == -1) $where['refund_type'] = [1, 3, 6];
        if (!$services->count(['to_uid' => $uid])) {
            return app('json')->fail('uid người dùng không nằm trong phạm vi người dùng trò chuyện hiện tại');
        }
        if ($where['status'] == -1) {
            unset($where['status']);
            $where['is_cancel'] = 0;
            $refundServices = app()->make(StoreOrderRefundServices::class);
            $data = $refundServices->refundList($where)['list'];
        } else {
            $data = $this->services->getOrderApiList($where);
        }
        return app('json')->success($data);
    }

    /**
     * Giao đơn hàng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delivery_keep(StoreOrderDeliveryServices $services, $id)
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

            ['fictitious_content', '']//Nội dung giao hàng ảo
        ]);
        $services->delivery((int)$id, $data);
        return app('json')->success('Giao hàng thành công');
    }

    /**
     * Cập nhật số tiền thanh toán, v.v.
     * @param $id
     * @return mixed|\think\response\Json|void
     */
    public function edit($id)
    {
        if (!$id) {
            return app('json')->fail('Dữ liệu không tồn tại');
        }
        return app('json')->success($this->services->updateForm($id));
    }

    /**
     * Chỉnh sửa đơn hàng
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        $data = $this->request->postMore([
            ['order_id', ''],
            ['total_price', 0],
            ['total_postage', 0],
            ['pay_price', 0],
            ['pay_postage', 0],
            ['gain_integral', 0],
        ]);

        validate(StoreOrderValidate::class)->check($data);

        if ($data['total_price'] < 0) {
            return app('json')->fail('Vui lòng nhập giá đơn hàng');
        }
        if ($data['pay_price'] < 0) {
            return app('json')->fail('Vui lòng nhập giá đơn hàng');
        }

        $this->services->updateOrder((int)$id, $data);
        return app('json')->success('Sửa thành công');
    }

    /**
     * Ghi chú đơn hàng
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function remark(Request $request)
    {
        [$order_id, $remark] = $request->postMore([
            ['order_id', ''],
            ['remark', '']
        ], true);
        $order = $this->services->getOne(['order_id' => $order_id], 'id,remark');
        if (!$order) {
            return app('json')->fail('Đơn hàng không tồn tại');
        }
        if (!strlen(trim($remark))) {
            return app('json')->fail('Vui lòng nhập nội dung ghi chú');
        }
        $order->remark = $remark;
        if (!$order->save()) {
            return app('json')->fail('Ghi chú thất bại');
        }
        return app('json')->success('Ghi chú thành công');

    }

    /**
     * Tạo form hoàn tiền
     * @param $id ID đơn hàng
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function refundForm(StoreOrderRefundServices $services, $id)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        return app('json')->success($services->refundOrderForm((int)$id));
    }

    /**
     * Hoàn tiền đơn hàng
     * @param Request $request
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function refund(Request $request, StoreOrderRefundServices $services)
    {
        [$orderId, $price, $type] = $request->postMore([
            ['order_id', ''],
            ['price', '0'],
            ['type', 1],
        ], true);
        if (!strlen(trim($orderId))) return app('json')->fail('Tham số không hợp lệ');
        $orderInfo = $this->services->getOne(['order_id' => $orderId]);
        if (!$orderInfo) return app('json')->fail('Dữ liệu không tồn tại');
        //Loại chỉ hoàn tiền
        if ($orderInfo['refund_type'] != 1) {
            return app('json')->fail('Vui lòng vào danh sách đơn hậu mãi trong trang quản trị để xử lý');
        }
        if ($type == 1) {
            $data['refund_status'] = 2;
            $data['refund_type'] = 6;
        } else if ($type == 2) {
            $data['refund_status'] = 0;
            $data['refund_type'] = 3;
        } else {
            return app('json')->fail('Lỗi thay đổi trạng thái hoàn tiền');
        }
        if ($orderInfo['pay_price'] == 0 || $type == 2) {
            $orderInfo->refund_status = $data['refund_status'];
            $orderInfo->save();
            return app('json')->success('Cập nhật trạng thái hoàn tiền thành công');
        }
        if ($orderInfo['pay_price'] == $orderInfo['refund_price']) return app('json')->fail('Đã hoàn hết số tiền thanh toán, không thể hoàn tiền thêm');
        if (!$price) {
            return app('json')->fail('Vui lòng nhập số tiền hoàn');
        }
        $data['refund_price'] = bcadd($price, $orderInfo['refund_price'], 2);
        $bj = bccomp((float)$orderInfo['pay_price'], (float)$data['refund_price'], 2);
        if ($bj < 0) {
            return app('json')->fail('Số tiền hoàn lớn hơn số tiền đã thanh toán, vui lòng sửa lại số tiền hoàn');
        }
        $refundData['pay_price'] = $orderInfo['pay_price'];
        $refundData['refund_price'] = $price;
        if ($orderInfo['refund_price'] > 0) {
            $refundData['refund_id'] = $orderInfo['order_id'] . rand(100, 999);
        }
        //Xử lý hoàn tiền
        $services->payOrderRefund($type, $orderInfo, $refundData);
        //Sửa trạng thái hoàn tiền đơn hàng
        if ($this->services->update((int)$orderInfo['id'], $data)) {
            $services->storeProductOrderRefundY($data, $orderInfo, $price);
            return app('json')->success('Hoàn tiền thành công');
        } else {
            $services->storeProductOrderRefundYFasle((int)$orderInfo['id'], $price);
            return app('json')->fail('Hoàn tiền thất bại');
        }
    }

    /**
     * Chi tiết đơn hàng
     * @param $id ID đơn hàng
     * @return mixed
     */
    public function orderInfo(StoreProductServices $productServices, $id)
    {
        if (!$id || !($orderInfo = $this->services->get($id))) {
            return app('json')->fail('Đơn hàng không tồn tại');
        }
        /** @var UserServices $services */
        $services = app()->make(UserServices::class);
        $userInfo = $services->get($orderInfo['uid']);
        if (!$userInfo) {
            return app('json')->fail('Người dùng không tồn tại');
        }
        $userInfo = $userInfo->hidden(['pwd', 'add_ip', 'last_ip', 'login_type']);
        $userInfo['spread_name'] = '';
        if ($userInfo['spread_uid'])
            $userInfo['spread_name'] = $services->value(['uid' => $userInfo['spread_uid']], 'nickname');
        $orderInfo = $this->services->tidyOrder($orderInfo->toArray(), true);
        $productId = array_column($orderInfo['cartInfo'], 'product_id');
        $cateData = $productServices->productIdByProductCateName($productId);
        foreach ($orderInfo['cartInfo'] as &$item) {
            $item['class_name'] = $cateData[$item['product_id']] ?? '';
        }
        if ($orderInfo['store_id'] && $orderInfo['shipping_type'] == 2) {
            /** @var  $storeServices */
            $storeServices = app()->make(SystemStoreServices::class);
            $orderInfo['_store_name'] = $storeServices->value(['id' => $orderInfo['store_id']], 'name');
        } else {
            $orderInfo['_store_name'] = '';
        }
        $userInfo = $userInfo->toArray();
        return app('json')->success(compact('orderInfo', 'userInfo'));
    }

    /**
     * Lấy thông tin vận chuyển
     * @param ExpressServices $services
     * @return mixed
     */
    public function export(ExpressServices $services)
    {
        return app('json')->success($services->express());
    }

    /**
     *
     * Lấy thông tin vận đơn
     * @param string $com
     * @return mixed
     */
    public function getExportTemp(ServeServices $services)
    {
        [$com] = $this->request->getMore([
            ['com', ''],
        ], true);
        return app('json')->success($services->express()->temp($com));
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
     * Xác nhận sử dụng tại cửa hàng
     * @param Request $request
     */
    public function order_verific(StoreOrderWriteOffServices $services, $id)
    {

        $orderInfo = $this->services->get(['id' => $id], ['verify_code', 'uid']);
        if (!$orderInfo) {
            return app('json')->fail('Đơn hàng không tồn tại');
        }
        if (!$orderInfo->verify_code) {
            return app('json')->fail('Xác nhận sử dụng thất bại');
        }
        $services->writeOffOrder($orderInfo->verify_code, 1, $orderInfo->uid);
        return app('json')->success('Xác nhận sử dụng thành công');
    }

}
