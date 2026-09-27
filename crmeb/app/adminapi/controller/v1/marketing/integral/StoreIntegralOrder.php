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
namespace app\adminapi\controller\v1\marketing\integral;

use app\adminapi\controller\AuthController;
use app\services\serve\ServeServices;
use app\services\activity\integral\{
    StoreIntegralOrderServices,
    StoreIntegralOrderStatusServices
};
use app\services\order\StoreOrderDeliveryServices;
use app\services\shipping\ExpressServices;
use app\services\user\UserServices;
use think\facade\App;

/**
 * Quản lý đơn hàng
 * Class StoreOrder
 * @package app\controller\admin\v1\order
 */
class StoreIntegralOrder extends AuthController
{
    /**
     * StoreIntegralOrder constructor.
     * @param App $app
     * @param StoreIntegralOrderServices $service
     * @method temp
     */
    public function __construct(App $app, StoreIntegralOrderServices $service)
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
            ['product_id', '']
        ]);
        $data = $this->services->orderCount($where);
        return app('json')->success($data);
    }

    /**
     * Lấy danh sách đơn hàng
     * @return mixed
     */
    public function lst()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['real_name', ''],
            ['data', '', '', 'time'],
            ['order', ''],
            ['field_key', ''],
            ['product_id', '']
        ]);
        $where['is_system_del'] = 0;
        return app('json')->success($this->services->getOrderList($where, ['*']));
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
        $data['is_show'] = 1;
        return app('json')->success($services->express($data));
    }

    /**
     * Xóa theo lô các đơn hàng người dùng đã xóa
     * @return mixed
     */
    public function del_orders()
    {
        [$ids, $all, $where] = $this->request->postMore([
            ['ids', []],
            ['where', []],
        ], true);
        if ($this->services->delOrders($ids)) {
            return app('json')->success(100002);
        } else {
            return app('json')->fail(100008);
        }
    }

    /**
     * Xóa đơn hàng
     * @param $id
     * @return mixed
     */
    public function del($id)
    {
        if ($this->services->delOrder($id)) {
            return app('json')->success(100002);
        } else {
            return app('json')->fail(100008);
        }
    }

    /**
     * Thực hiện giao đơn hàng
     * @param $id
     * @return mixed
     */
    public function update_delivery($id)
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
        $this->services->delivery((int)$id, $data);
        return app('json')->success(100010);
    }

    /**
     * Xác nhận đã nhận hàng
     * @param $id
     * @return mixed
     */
    public function take_delivery($id)
    {
        if (!$id) return app('json')->fail(100100);
        $order = $this->services->get($id);
        if (!$order)
            return app('json')->fail(100026);
        if ($order['status'] == 3)
            return app('json')->fail(400114);
        if ($order['status'] == 2)
            $data['status'] = 3;
        else
            return app('json')->fail(400115);

        if (!$this->services->update($id, $data)) {
            return app('json')->fail(400116);
        } else {
            //Thêm trạng thái đơn hàng đã nhận
            /** @var StoreIntegralOrderStatusServices $statusService */
            $statusService = app()->make(StoreIntegralOrderStatusServices::class);
            $statusService->save([
                'oid' => $order['id'],
                'change_type' => 'take_delivery',
                'change_message' => 'Đã nhận hàng',
                'change_time' => time()
            ]);
            return app('json')->success(400117);
        }
    }

    /**
     * Chi tiết đơn hàng
     * @param $id ID đơn hàng
     * @return mixed
     */
    public function order_info($id)
    {
        if (!$id || !($orderInfo = $this->services->get($id))) {
            return app('json')->fail(400118);
        }
        /** @var UserServices $services */
        $services = app()->make(UserServices::class);
        $userInfo = $services->get($orderInfo['uid']);
        if (!$userInfo) return app('json')->fail(400119);
        $userInfo = $userInfo->hidden(['pwd', 'add_ip', 'last_ip', 'login_type']);
        $orderInfo = $this->services->tidyOrder($orderInfo->toArray());
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
            return app('json')->fail(400118);
        if ($orderInfo['delivery_type'] != 'express' || !$orderInfo['delivery_id'])
            return app('json')->fail(400120);

        $cacheName = 'integral' . $orderInfo['order_id'] . $orderInfo['delivery_id'];

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
    public function distribution($id)
    {
        if (!$id) {
            return app('json')->fail(400118);
        }
        return app('json')->success($this->services->distributionForm((int)$id));
    }

    /**
     * Sửa thông tin giao hàng
     * @param $id  ID đơn hàng
     * @return mixed
     */
    public function update_distribution($id)
    {
        $data = $this->request->postMore([['delivery_name', ''], ['delivery_code', ''], ['delivery_id', '']]);
        if (!$id) return app('json')->fail(100100);
        $this->services->updateDistribution($id, $data);
        return app('json')->success(100001);
    }


    /**
     * Sửa ghi chú
     * @param $id
     * @return mixed
     */
    public function remark($id)
    {
        $data = $this->request->postMore([['remark', '']]);
        if ($this->services->remark($id, $data['remark'])) {
            return app('json')->success(100024);
        } else {
            return app('json')->fail(100025);
        }
    }

    /**
     * Lấy danh sách trạng thái đơn hàng có phân trang
     * @param $id
     * @return mixed
     */
    public function status(StoreIntegralOrderStatusServices $services, $id)
    {
        if (!$id) return app('json')->fail(100100);
        return app('json')->success($services->getStatusList(['oid' => $id])['list']);
    }

    /**
     * In bằng máy in Yilianyun
     * @param $id
     * @return mixed
     */
    public function order_print($id)
    {
        if (!$id) return app('json')->fail(100100);
        $order = $this->services->get($id);
        if (!$order) {
            return app('json')->fail(400118);
        }
        $res = $this->services->orderPrint($order);
        if ($res) {
            return app('json')->success(400121);
        } else {
            return app('json')->fail(400122);
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
            return app('json')->fail(400123);
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
        $tpd = $services->express()->temp($data['com']);
        return app('json')->success($tpd['data']);
    }

    /**
     * In vận đơn điện tử sau khi đơn hàng được giao
     * @param $order_id
     * @param StoreOrderDeliveryServices $storeOrderDeliveryServices
     * @return mixed
     */
    public function order_dump($order_id, StoreOrderDeliveryServices $storeOrderDeliveryServices)
    {
        return app('json')->success($storeOrderDeliveryServices->orderDump($order_id, 'integral_order'));

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

}
