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
namespace app\adminapi\controller\v1\order;

use app\adminapi\controller\AuthController;
use app\Request;
use app\services\order\StoreOrderRefundServices;
use app\services\order\StoreOrderServices;
use app\services\user\UserServices;
use app\services\wechat\WechatUserServices;
use think\facade\App;

/**
 * Đơn hoàn tiền
 * Class RefundOrder
 * @package app\adminapi\controller\v1\order
 */
class RefundOrder extends AuthController
{

    /**
     * RefundOrder constructor.
     * @param App $app
     * @param StoreOrderRefundServices $service
     * @method temp
     */
    public function __construct(App $app, StoreOrderRefundServices $service)
    {
        parent::__construct($app);
        $this->services = $service;
    }

    /**
     * Danh sách đơn hoàn tiền
     * @return mixed
     */
    public function getRefundList()
    {
        $where = $this->request->getMore([
            ['order_id', ''],
            ['time', ''],
            ['refund_type', 0]
        ]);
        $where['is_cancel'] = 0;
        $where['is_system_del'] = 0;
        return app('json')->success($this->services->refundList($where));
    }

    /**
     * Chi tiết đơn hàng
     * @param $uni
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/02
     */
    public function getRefundInfo($uni)
    {
        $data['orderInfo'] = $this->services->refundDetail($uni);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $data['userInfo'] = $userServices->get($data['orderInfo']['uid']);
        return app('json')->success($data);
    }

    /**
     * Người bán đồng ý hoàn tiền
     * @return mixed
     */
    public function agreeExpress($id)
    {
        $this->services->agreeExpress($id);
        return app('json')->success(100010);
    }

    /**
     * Sửa ghi chú
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function remark($id)
    {
        [$remark] = $this->request->postMore([['remark', '']], true);

        $this->services->updateRemark((int)$id, $remark);
        return app('json')->success(100024);
    }

    /**
     * Tạo form hoàn tiền
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function refund($id)
    {
        if (!$id) return app('json')->fail(100100);
        return app('json')->success($this->services->refundOrderForm((int)$id));
    }

    /**
     * Hoàn tiền đơn hàng (sản phẩm)
     * @param Request $request
     * @param StoreOrderServices $services
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function refundPrice(Request $request, StoreOrderServices $services, $id)
    {
        $data = $request->postMore([
            ['refund_price', 0],
            ['type', 1]
        ]);
        if (!$id) {
            return app('json')->fail(100100);
        }
        $orderRefund = $this->services->get($id);
        if (!$orderRefund) {
            return app('json')->fail(100026);
        }
        if ($orderRefund['is_cancel'] == 1) {
            return app('json')->fail(400118);
        }
        $order = $services->get((int)$orderRefund['store_order_id']);
        if (!$order) {
            return app('json')->fail(100026);
        }
        if (!in_array($orderRefund['refund_type'], [1, 5])) {
            return app('json')->fail(400144);
        }

        if ($data['type'] == 1 || $data['type'] == 5) {
            $data['refund_type'] = 6;
        } else if ($data['type'] == 2) {
            $data['refund_type'] = 3;
        }
        $data['refunded_time'] = time();
        $type = $data['type'];
        //Từ chối hoàn tiền
        if ($type == 2) {
            $this->services->refuseRefund((int)$id, $data, $orderRefund);
            return app('json')->success(400145);
        } else {
            //Hoàn tiền 0đ
            if ($orderRefund['refund_price'] == 0 && in_array($orderRefund['refund_type'], [1, 5])) {
                $refund_price = 0;
            } else {
                if (!$data['refund_price']) {
                    return app('json')->fail(400146);
                }
                if ($orderRefund['refund_price'] == $orderRefund['refunded_price']) {
                    return app('json')->fail(400147);
                }
                $refund_price = $data['refund_price'];
            }

            $data['refunded_price'] = bcadd($data['refund_price'], $orderRefund['refunded_price'], 2);
            $bj = bccomp((string)$orderRefund['refund_price'], (string)$data['refunded_price'], 2);
            if ($bj < 0) {
                return app('json')->fail(400148);
            }

            unset($data['type']);
            $refund_data['pay_price'] = $order['pay_price'];
            $refund_data['refund_price'] = $refund_price;
            if ($order['refund_price'] > 0) {
                mt_srand();
                $refund_data['refund_id'] = $order['order_id'] . rand(100, 999);
            }
            ($order['pid'] > 0) ? $refund_data['order_id'] = $services->value(['id' => (int)$order['pid']], 'order_id') : $refund_data['order_id'] = $order['order_id'];
            /** @var WechatUserServices $wechatUserServices */
            $wechatUserServices = app()->make(WechatUserServices::class);
            $refund_data['open_id'] = $wechatUserServices->uidToOpenid((int)$order['uid'], 'routine') ?? '';
            $refund_data['refund_no'] = $orderRefund['order_id'];
            $refund_data['order_id'] = $orderRefund['order_id'];
            //Sửa trạng thái hoàn tiền đơn hàng
//            $data['refund_price'] = $data['refunded_price'];
            unset($data['refund_price']);
            if ($this->services->agreeRefund($id, $refund_data)) {
                $this->services->update($id, $data);
                return app('json')->success(400149);
            } else {
                $this->services->storeProductOrderRefundYFasle((int)$id, $refund_price);
                return app('json')->fail(400150);
            }
        }
    }

    /**
     * Cấu trúc form không hoàn tiền
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function noRefund($id)
    {
        if (!$id) return app('json')->fail(100100);
        return app('json')->success($this->services->noRefundForm((int)$id));
    }

    /**
     * Đơn hàng không hoàn tiền
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function refuseRefund($id)
    {
        [$refund_reason] = $this->request->postMore([['refund_reason', '']], true);
        $this->services->refuse($id, $refund_reason);
        return app('json')->success(100010);
    }
}
