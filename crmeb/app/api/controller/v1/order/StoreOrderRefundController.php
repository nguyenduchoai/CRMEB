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
namespace app\api\controller\v1\order;

use app\Request;
use app\services\order\StoreOrderRefundServices;
use app\services\order\StoreOrderServices;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class StoreOrderRefundController
{
    /**
     * @var StoreOrderRefundServices
     */
    protected $services;

    /**
     * StoreOrderRefundController constructor.
     * @param StoreOrderRefundServices $services
     */
    public function __construct(StoreOrderRefundServices $services)
    {
        $this->services = $services;
    }

    /**
     * Danh sách đơn hoàn tiền
     * @param Request $request
     * @return mixed
     */
    public function refundList(Request $request)
    {
        $where = $request->getMore([
            ['refund_status', ''],
        ]);
        $where['uid'] = $request->uid();
        $where['is_cancel'] = 0;
        $where['is_del'] = 0;
        $data = $this->services->refundList($where);
        return app('json')->success($data);
    }

    /**
     * Chi tiết đơn hoàn tiền
     * @param Request $request
     * @param $uni
     * @return mixed
     */
    public function refundDetail(Request $request, $uni)
    {
        $orderData = $this->services->refundDetail($uni);
        return app('json')->success($orderData);
    }

    /**
     * Hủy yêu cầu
     * @param Request $request
     * @param $uni
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function cancelApply(Request $request, $uni)
    {
        if (!strlen(trim($uni))) return app('json')->fail('Tham số không hợp lệ');
        $orderRefund = $this->services->get(['order_id' => $uni, 'is_cancel' => 0]);
        if (!$orderRefund || $orderRefund['uid'] != $request->uid()) {
            return app('json')->fail('Đơn hàng không tồn tại');
        }
        if (!in_array($orderRefund['refund_type'], [1, 2, 4, 5])) {
            return app('json')->fail('Trạng thái hiện tại không thể hủy yêu cầu');
        }
        $this->services->update($orderRefund['id'], ['is_cancel' => 1]);
        $this->services->cancelOrderRefundCartInfo((int)$orderRefund['id'], (int)$orderRefund['store_order_id'], $orderRefund);

        //Event tùy chỉnh - Người dùng hủy hoàn tiền
        event('CustomEventListener', ['order_refund_cancel', [
            'uid' => $orderRefund['uid'],
            'id' => $orderRefund['id'],
            'store_order_id' => $orderRefund['store_order_id'],
            'order_id' => $orderRefund['order_id'],
            'refund_num' => $orderRefund['refund_num'],
            'refund_price' => $orderRefund['refund_price'],
            'cancel_time' => date('Y-m-d H:i:s'),
        ]]);

        return app('json')->success('Hủy thành công');
    }

    /**
     * Người dùng gửi mã vận đơn khi trả hàng
     * @param Request $request
     * @return mixed
     */
    public function applyExpress(Request $request)
    {
        $data = $request->postMore([
            ['id', ''],
            ['refund_express', ''],
            ['refund_phone', ''],
            ['refund_express_name', ''],
            ['refund_img', ''],
            ['refund_explain', ''],
        ]);
        if ($data['id'] == '') return app('json')->fail('Tham số không hợp lệ');
        $res = $this->services->editRefundExpress($data);
        if ($res)
            return app('json')->success('Gửi thành công');
        else
            return app('json')->fail('Gửi thất bại');
    }

    /**
     * Xóa đơn hoàn tiền
     * @param Request $request
     * @param $uni
     * @return mixed
     */
    public function delRefund(Request $request, $uni)
    {
        $oid = $this->services->value(['order_id' => $uni, 'uid' => $request->uid()], 'store_order_id');
        $res = $this->services->update(['order_id' => $uni, 'uid' => $request->uid()], ['is_del' => 1]);
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $orderServices->update($oid, ['is_del' => 1], 'id');
        if ($res)
            return app('json')->success('Xóa thành công');
        else
            return app('json')->fail('Xóa thất bại');
    }
}
