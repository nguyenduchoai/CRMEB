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
namespace app\outapi\controller;

use app\services\order\OutStoreOrderServices;
use app\services\shipping\ExpressServices;
use crmeb\services\CacheService;
use think\facade\App;

/**
 * Quản lý đơn hàng
 * Class StoreOrder
 * @package app\outapi\controller
 */
class StoreOrder extends AuthController
{
    /**
     * StoreOrder constructor.
     * @param App $app
     * @param OutStoreOrderServices $service
     * @method temp
     */
    public function __construct(App $app, OutStoreOrderServices $service)
    {
        parent::__construct($app);
        $this->services = $service;
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
            ['is_del', ''],
            ['data', '', '', 'time'],
            ['type', ''],
            ['pay_type', ''],
            ['order', ''],
            ['field_key', ''],
            ['paid', '']
        ]);
        $where['is_system_del'] = 0;
        $where['pid'] = 0;
        return app('json')->success($this->services->getOrderList($where));
    }

    /**
     * Danh sách đơn vị vận chuyển
     * @return mixed
     */
    public function express(ExpressServices $services)
    {
        [$status] = $this->request->getMore([
            ['status', ''],
        ], true);
        if ($status != '') $data['status'] = $status;
        $data['is_show'] = 1;
        $list = CacheService::remember('EXPRESS_LIST', function () use ($services, $data) {
            return $services->express($data);
        }, 86400);
        return app('json')->success($list);
    }

    /**
     * Giao đơn hàng
     * @param string $order_id Mã đơn hàng
     * @return mixed
     */
    public function delivery(string $order_id)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        $data = $this->request->postMore([
            ['delivery_name', ''],//Tên đơn vị vận chuyển
            ['delivery_id', ''],//Mã vận đơn
            ['delivery_code', ''],//Mã đơn vị vận chuyển
        ]);
        $data['express_record_type'] = 1;
        $data['type'] = 1;
        return app('json')->success('Thao tác thành công', $this->services->delivery($order_id, $data));
    }

    /**
     * Lấy danh sách sản phẩm có thể tách để giao trong đơn hàng
     * @param string $order_id Mã đơn hàng
     * @return mixed
     */
    public function splitCartInfo(string $order_id)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        return app('json')->success($this->services->getCartList($order_id));
    }

    /**
     * Tách đơn để giao hàng
     * @param string $order_id Mã đơn hàng
     * @return mixed
     */
    public function splitDelivery(string $order_id)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        $data = $this->request->postMore([
            ['delivery_name', ''],//Tên đơn vị vận chuyển
            ['delivery_id', ''],//Mã vận đơn
            ['delivery_code', ''],//Mã đơn vị vận chuyển
            ['fictitious_content', ''],//Nội dung giao hàng ảo
            ['cart_ids', []]
        ]);

        if (!$data['cart_ids']) {
            return app('json')->fail('Vui lòng chọn sản phẩm cần giao');
        }
        foreach ($data['cart_ids'] as &$cart) {
            if (!isset($cart['cart_id']) || !$cart['cart_id'] || !isset($cart['cart_num']) || !$cart['cart_num']) {
                return app('json')->fail('Vui lòng chọn lại sản phẩm cần giao hoặc số lượng giao');
            }
            $cart['cart_id'] = (int)$cart['cart_id'];
            $cart['cart_num'] = (int)$cart['cart_num'];
        }
        $data['express_record_type'] = 1;
        $data['type'] = 1;

        $this->services->splitDelivery($order_id, $data);
        return app('json')->success('Thao tác thành công');
    }

    /**
     * Xác nhận đã nhận hàng
     * @param string $order_id Mã đơn hàng
     * @return mixed
     * @throws \Exception
     */
    public function receive(string $order_id)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        $this->services->receive($order_id);
        return app('json')->success('Xác nhận nhận hàng thành công');
    }

    /**
     * Thiết lập thông tin hóa đơn
     * @param string $order_id Mã đơn hàng
     * @return mixed
     */
    public function setInvoice(string $order_id)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        $data = $this->request->postMore([
            [['header_type', 'd'], 1],
            [['type', 'd'], 1],
            ['drawer_phone', ''],
            ['email', ''],
            ['name', ''],
            ['duty_number', ''],
            ['tell', ''],
            ['address', ''],
            ['bank', ''],
            ['card_number', ''],
        ]);

        if (!$data['drawer_phone']) return app('json')->fail('Vui lòng nhập số điện thoại nhận hóa đơn');
        if (!check_phone($data['drawer_phone'])) return app('json')->fail('Số điện thoại không đúng định dạng');
        if (!$data['name']) return app('json')->fail('Vui lòng nhập tiêu đề hóa đơn (tên doanh nghiệp được xuất hóa đơn)');
        if (!in_array($data['header_type'], [1, 2])) {
            $data['header_type'] = empty($data['duty_number']) ? 1 : 2;
        }
        if ($data['header_type'] == 1 && !preg_match('/^[\x80-\xff]{2,60}$/', $data['name'])) {
            return app('json')->fail('Vui lòng nhập đúng tiêu đề hóa đơn (tên doanh nghiệp được xuất hóa đơn)');
        }
        if ($data['header_type'] == 2 && !preg_match('/^[0-9a-zA-Z&\(\)\（\）\x80-\xff]{2,150}$/', $data['name'])) {
            return app('json')->fail('Vui lòng nhập đúng tiêu đề hóa đơn (tên doanh nghiệp được xuất hóa đơn)');
        }
        if ($data['header_type'] == 2 && !$data['duty_number']) {
            return app('json')->fail('Vui lòng nhập mã số thuế trên hóa đơn');
        }
        if ($data['header_type'] == 2 && !preg_match('/^[A-Z0-9]{15}$|^[A-Z0-9]{17}$|^[A-Z0-9]{18}$|^[A-Z0-9]{20}$/', $data['duty_number'])) {
            return app('json')->fail('Vui lòng nhập đúng mã số thuế trên hóa đơn');
        }
        if ($data['card_number'] && !preg_match('/^[1-9]\d{11,19}$/', $data['card_number'])) {
            return app('json')->fail('Vui lòng nhập đúng số thẻ ngân hàng');
        }

        if ($this->services->setInvoice($order_id, $data)) {
            return app('json')->success('Sửa thành công');
        } else {
            return app('json')->fail('Thao tác thất bại');
        }
    }

    /**
     * Đặt trạng thái hóa đơn
     * @param string $order_id Mã đơn hàng
     * @return mixed
     */
    public function setInvoiceStatus(string $order_id)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        $data = $this->request->postMore([
            ['is_invoice', 0],
            ['invoice_number', 0],
            ['remark', '']
        ]);

        if ($data['is_invoice'] == 1 && !$data['invoice_number']) {
            return app('json')->fail('Vui lòng điền số hóa đơn');
        }

        $this->services->setInvoice($order_id, $data);
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Chi tiết đơn hàng
     * @param string $order_id Mã đơn hàng
     * @return mixed
     */
    public function read(string $order_id)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        return app('json')->success($this->services->getInfo($order_id));
    }

    /**
     * Sửa ghi chú
     * @param string $order_id Mã đơn hàng
     * @return mixed
     */
    public function remark(string $order_id)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        $data = $this->request->postMore([['remark', '']]);
        if (!$data['remark']) return app('json')->fail('Ghi chú không được để trống');

        if (!$order = $this->services->get(['order_id' => $order_id])) {
            return app('json')->fail('Đơn hàng không tồn tại');
        }
        $order->remark = $data['remark'];
        if ($order->save()) {
            return app('json')->success('Ghi chú thành công');
        } else
            return app('json')->fail('Ghi chú thất bại');
    }

    /**
     * Sửa thông tin giao hàng
     * @param string $order_id Mã đơn hàng
     * @return mixed
     */
    public function updateDistribution(string $order_id)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        $data = $this->request->postMore([['delivery_name', ''], ['delivery_code', ''], ['delivery_id', '']]);

        $this->services->updateDistribution($order_id, $data);
        return app('json')->success('Thao tác thành công');
    }
}
