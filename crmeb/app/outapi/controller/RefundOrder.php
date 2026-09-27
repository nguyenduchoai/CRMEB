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

use app\Request;
use app\services\order\OutStoreOrderRefundServices;
use think\facade\App;

/**
 * Controller đơn hậu mãi
 * Class RefundOrder
 * @package app\outapi\controller
 */
class RefundOrder extends AuthController
{
    /**
     * RefundOrder constructor.
     * @param App $app
     * @param OutStoreOrderRefundServices $service
     * @method temp
     */
    public function __construct(App $app, OutStoreOrderRefundServices $service)
    {
        parent::__construct($app);
        $this->services = $service;
    }

    /**
     * Lấy danh sách đơn hậu mãi
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lst()
    {
        $where = $this->request->getMore([
            ['order_id', ''],
            ['time', ''],
            ['refund_type', 0]
        ]);
        $where['is_cancel'] = 0;

        return app('json')->success($this->services->refundList($where));
    }

    /**
     * Sửa ghi chú
     * @param string $order_id Mã đơn hậu mãi
     * @return mixed
     */
    public function remark(string $order_id)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        [$remark] = $this->request->postMore([['remark', '']], true);

        $this->services->remark($order_id, $remark);
        return app('json')->success('Ghi chú thành công');
    }

    /**
     * Đồng ý hoàn tiền
     * @param string $order_id Mã đơn hậu mãi
     * @return mixed
     */
    public function agree(string $order_id)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
       $this->services->agree($order_id);
        return app('json')->success('Thao tác thành công');
    }

    /**
     * Đơn hàng không hoàn tiền
     * @param string $order_id Mã đơn hậu mãi
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function refuse(string $order_id)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        [$refund_reason] = $this->request->postMore([['refund_reason', '']], true);

        $this->services->refuse($order_id, $refund_reason);
        return app('json')->success('Thao tác thành công');
    }

    /**
     * Chi tiết đơn hàng
     * @param string $order_id Mã đơn hậu mãi
     * @return mixed
     */
    public function read(string $order_id)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        $data = $this->services->getInfo($order_id);
        return app('json')->success($data);
    }

    /**
     * Hoàn tiền đơn hàng
     * @param string $order_id Mã đơn hậu mãi
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function refundPrice(string $order_id, Request $request)
    {
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        [$refund_price] = $request->postMore([['refund_price', '']], true);
        $this->services->refundPrice($order_id, $refund_price);
        return app('json')->success('Hoàn tiền thành công');
    }

}
