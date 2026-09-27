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
use app\services\activity\integral\StoreIntegralOrderServices;
use app\services\activity\integral\StoreIntegralServices;
use app\services\product\sku\StoreProductAttrValueServices;
use app\services\shipping\ExpressServices;

class StoreIntegralOrderController
{
    protected $services;

    public function __construct(StoreIntegralOrderServices $services)
    {
        $this->services = $services;
    }

    /**
     * Xác nhận đơn hàng
     * @param Request $request
     * @return mixed
     */
    public function confirm(Request $request)
    {
        [$unique, $num] = $request->postMore([
            'unique',
            'num'
        ], true);
        if (!$unique) {
            return app('json')->fail('Vui lòng chọn sản phẩm cần mua');
        }
        $user = $request->user()->toArray();
        return app('json')->success($this->services->getOrderConfirmData($user, $unique, $num));
    }

    /**
     * Tạo đơn hàng
     * @param Request $request
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function create(Request $request, StoreProductAttrValueServices $storeProductAttrValueServices, StoreIntegralServices $storeIntegralServices)
    {
        $uid = (int)$request->uid();
        [$addressId, $mark, $unique, $num] = $request->postMore([
            [['addressId', 'd'], 0],
            ['mark', ''],
            ['unique', ''],
            [['num', 'd'], 0]
        ], true);
        $productInfo = $storeProductAttrValueServices->uniqueByField($unique);
        if (!$productInfo || !isset($productInfo['storeIntegral']) || !$productInfo['storeIntegral']) {
            return app('json')->fail('Sản phẩm không tồn tại, vui lòng chọn lại sản phẩm để đặt hàng');
        }
        $productInfo = is_object($productInfo) ? $productInfo->toArray() : $productInfo;

        $num = (int)$num;
        //Kiểm tra giới hạn số lượng sản phẩm đổi điểm
        $storeIntegralServices->checkoutProductStock($uid, $productInfo['product_id'], $num, $unique);
        $order = $this->services->createOrder($uid, $addressId, $mark, $request->user()->toArray(), $num, $productInfo);
        return app('json')->status('success', 'Tạo đơn hàng thành công', ['orderId' => $order['order_id']]);
    }

    /**
     * Chi tiết đơn hàng
     * @param Request $request
     * @param $uni
     * @return mixed
     */
    public function detail(Request $request, $uni)
    {
        if (!strlen(trim($uni))) return app('json')->fail('Tham số không hợp lệ');
        $order = $this->services->getOne(['order_id' => $uni, 'is_del' => 0]);
        if (!$order) return app('json')->fail('Đơn hàng không tồn tại');
        $order = $order->toArray();
        $orderData = $this->services->tidyOrder($order);
        return app('json')->success($orderData);
    }

    /**
     * Danh sách đơn hàng
     * @param Request $request
     * @return mixed
     */
    public function lst(Request $request)
    {
        $where['uid'] = $request->uid();
        $where['is_del'] = 0;
        $where['is_system_del'] = 0;
        $list = $this->services->getOrderApiList($where);
        return app('json')->success($list);
    }

    /**
     * Xác nhận đã nhận hàng
     * @param Request $request
     * @return mixed
     */
    public function take(Request $request)
    {
        list($order_id) = $request->postMore([
            ['order_id', ''],
        ], true);
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        $order = $this->services->takeOrder($order_id, (int)$request->uid());
        if ($order) {
            return app('json')->success('Xác nhận nhận hàng thành công');
        } else
            return app('json')->fail('Nhận hàng thất bại');
    }

    /**
     * Đơn hàng - Xem vận chuyển
     * @param Request $request
     * @param ExpressServices $expressServices
     * @param $uni
     * @return mixed
     */
    public function express(Request $request, ExpressServices $expressServices, $uni)
    {
        if (!$uni || !($order = $this->services->getUserOrderDetail($uni, $request->uid()))) return app('json')->fail('Đơn hàng không tồn tại');
        if ($order['delivery_type'] != 'express' || !$order['delivery_id']) return app('json')->fail('Mã vận đơn không tồn tại');
        $order['price'] = (int)$order['price'];
        $order['total_price'] = (int)$order['total_price'];
        $cacheName = 'integral' . $order['order_id'] . $order['delivery_id'];
        return app('json')->success([
            'order' => $order,
            'express' => [
                'result' => ['list' => $expressServices->query($cacheName, $order['delivery_id'], $order['delivery_code'], $order['user_phone'])
                ]
            ]
        ]);
    }

    /**
     * Xóa đơn hàng
     * @param Request $request
     * @return mixed
     */
    public function del(Request $request)
    {
        [$order_id] = $request->postMore([
            ['order_id', ''],
        ], true);
        if (!$order_id) return app('json')->fail('Tham số không hợp lệ');
        $res = $this->services->removeOrder($order_id, (int)$request->uid());
        if ($res) {
            return app('json')->success('Xóa thành công');
        } else {
            return app('json')->fail('Xóa thất bại');
        }
    }
}
