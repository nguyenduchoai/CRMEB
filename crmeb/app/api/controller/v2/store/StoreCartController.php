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

namespace app\api\controller\v2\store;


use app\services\order\StoreCartServices;
use app\Request;

class StoreCartController
{
    protected $services;

    public function __construct(StoreCartServices $services)
    {
        $this->services = $services;
    }

    /**
     * Chọn lại giỏ hàng
     * @param Request $request
     * @return mixed
     */
    public function resetCart(Request $request)
    {
        list($id, $unique, $num, $product_id) = $request->postMore([
            ['id', 0],
            ['unique', ''],
            ['num', 1],
            ['product_id', 0]
        ], true);
        $this->services->resetCart($id, $request->uid(), $product_id, $unique, $num);
        return app('json')->success(100001);
    }

    /**
     * Lấy giỏ hàng của người dùng
     * @param Request $request
     * @return mixed
     */
    public function getCartList(Request $request)
    {
        $uid = (int)$request->uid();
        $data = $this->services->getCartList(['uid' => $uid, 'is_del' => 0, 'is_new' => 0, 'is_pay' => 0, 'combination_id' => 0, 'seckill_id' => 0, 'bargain_id' => 0], 0, 0, ['productInfo', 'attrInfo']);
        [$data, $valid, $invalid] = $this->services->handleCartList($uid, $data);
        return app('json')->success($data);
    }

    /**
     * Thêm vào giỏ hàng từ trang chủ
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setCartNum(Request $request)
    {
        list($product_id, $num, $unique, $type) = $request->postMore([
            ['product_id', 0],
            ['num', 1],
            ['unique', ''],
            ['type', -1]
        ], true);
        /** @var StoreCartServices $cartService */
        $cartService = app()->make(StoreCartServices::class);
        if (!$product_id || !is_numeric($product_id)) return app('json')->fail(100100);
        $res = $cartService->setCartNum($request->uid(), $product_id, $num, $unique, $type);
        if ($res) return app('json')->success('Thêm vào giỏ hàng thành công!');
        return app('json')->fail('Thêm vào giỏ hàng thất bại!');
    }
}
