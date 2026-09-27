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
namespace app\api\controller\v1\store;

use app\Request;
use app\services\activity\combination\StorePinkServices;
use app\services\order\StoreCartServices;

/**
 * Lớp giỏ hàng
 * Class StoreCartController
 * @package app\api\controller\store
 */
class StoreCartController
{
    protected $services;

    public function __construct(StoreCartServices $services)
    {
        $this->services = $services;
    }

    /**
     * Giỏ hàng - Danh sách
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lst(Request $request)
    {
        [$status] = $request->postMore([
            ['status', 1],//Trạng thái sản phẩm trong giỏ hàng
        ], true);
        return app('json')->success($this->services->getUserCartList($request->uid(), $status));
    }

    /**
     * Giỏ hàng - Thêm
     * @param Request $request
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add(Request $request)
    {
        $where = $request->postMore([
            [['productId', 'd'], 0],//Mã sản phẩm thường
            [['cartNum', 'd'], 1], //Số lượng trong giỏ hàng
            ['uniqueId', ''],//Giá trị duy nhất của thuộc tính
            [['new', 'd'], 0],// 1 là thêm vào giỏ hàng và mua ngay, 0 là thêm vào giỏ hàng
            [['is_new', 'd'], 0],// 1 là thêm vào giỏ hàng và mua ngay, 0 là thêm vào giỏ hàng
            [['combinationId', 'd'], 0],//Mã sản phẩm mua chung
            [['secKillId', 'd'], 0],//Mã sản phẩm flash sale
            [['bargainId', 'd'], 0],//Mã sản phẩm săn giảm giá
            [['advanceId', 'd'], 0],//Mã sản phẩm đặt trước
            [['pinkId', 'd'], 0],//ID nhóm mua chung
        ]);
        if ($where['is_new'] || $where['new']) $new = true;
        else $new = false;
        /** @var StoreCartServices $cartService */
        $cartService = app()->make(StoreCartServices::class);
        if (!$where['productId'] || !is_numeric($where['productId'])) return app('json')->fail(100100);
        $type = 0;
        if ($where['secKillId']) {
            $type = 1;
        } elseif ($where['bargainId']) {
            $type = 2;
        } elseif ($where['combinationId']) {
            $type = 3;
            if ($where['pinkId']) {
                /** @var StorePinkServices $pinkServices */
                $pinkServices = app()->make(StorePinkServices::class);
                if ($pinkServices->isPinkStatus($where['pinkId'])) return app('json')->fail(410315);
            }
        } elseif ($where['advanceId']) {
            $type = 6;
        }
        if ($type == 0) $cartService->checkVipGoodsBuy($request->user(), $where['productId']);
        $res = $cartService->setCart($request->uid(), $where['productId'], $where['cartNum'], $where['uniqueId'], $type, $new, $where['combinationId'], $where['secKillId'], $where['bargainId'], $where['advanceId']);
        if (!$res) return app('json')->fail(100022);
        else  return app('json')->success(['cartId' => $res]);
    }

    /**
     * Giỏ hàng - Xóa sản phẩm
     * @param Request $request
     * @return mixed
     */
    public function del(Request $request)
    {
        $where = $request->postMore([
            ['ids', ''],//Mã giỏ hàng
        ]);
        $where['ids'] = is_array($where['ids']) ? $where['ids'] : explode(',', $where['ids']);
        if (!count($where['ids']))
            return app('json')->fail(100100);
        if ($this->services->removeUserCart((int)$request->uid(), $where['ids']))
            return app('json')->success(100002);
        return app('json')->fail(100008);
    }

    /**
     * Giỏ hàng - Sửa số lượng sản phẩm
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function num(Request $request)
    {
        $where = $request->postMore([
            ['id', 0],//Mã giỏ hàng
            ['number', 0],//Mã giỏ hàng
        ]);
        if (!$where['id'] || !is_numeric($where['id'])) return app('json')->fail(100100);
        if (!$where['number'] || !is_numeric($where['number'])) return app('json')->fail(100007);
        $res = $this->services->changeUserCartNum($where['id'], $where['number'], $request->uid());
        if ($res) return app('json')->success(100001);
        else return app('json')->fail(100007);
    }

    /**
     * Giỏ hàng - Thống kê số lượng, giá
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function count(Request $request)
    {
        [$numType] = $request->postMore([
            ['numType', true],//Mã giỏ hàng
        ], true);
        $uid = (int)$request->uid();
        return app('json')->success($this->services->getUserCartCount($uid, $numType));
    }

    /**
     * Chọn lại giỏ hàng
     * @param Request $request
     * @return mixed
     */
    public function reChange(Request $request)
    {
        [$cart_id, $product_id, $unique] = $request->postMore([
            ['cart_id', 0],
            ['product_id', 0],
            ['unique', '']
        ], true);
        $this->services->modifyCart($cart_id, $product_id, $unique);
        return app('json')->success(410225);
    }
}
