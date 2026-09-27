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
use app\services\activity\coupon\StoreCouponIssueServices;

/**
 * Lớp phiếu giảm giá
 * Class StoreCouponsController
 * @package app\api\controller\store
 */
class StoreCouponsController
{
    protected $services;

    public function __construct(StoreCouponIssueServices $services)
    {
        $this->services = $services;
    }

    /**
     * Danh sách phiếu giảm giá có thể nhận
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lst(Request $request)
    {
        $where = $request->getMore([
            ['type', 0],
            ['product_id', 0],
            ['num', 0]
        ]);
        if ($request->getFromType() == 'pc') $where['type'] = -1;
        return app('json')->success($this->services->getIssueCouponList($request->uid(), $where)['list']);
    }

    /**
     * Nhận phiếu giảm giá
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function receive(Request $request)
    {
        list($couponId) = $request->getMore([
            ['couponId', 0]
        ], true);
        if (!$couponId || !is_numeric($couponId)) return app('json')->fail(100100);

        /** @var StoreCouponIssueServices $couponIssueService */
        $couponIssueService = app()->make(StoreCouponIssueServices::class);
        $couponIssueService->issueUserCoupon($couponId, $request->user(), true);
        return app('json')->success(410319);
    }

    /**
     * Phiếu giảm giá người dùng đã nhận
     * @param Request $request
     * @param $types
     * @return mixed
     */
    public function user(Request $request, $types)
    {
        $uid = (int)$request->uid();
        return app('json')->success($this->services->getUserCouponList($uid, $types));
    }

    /**
     * Phiếu giảm giá - Lấy theo đơn hàng
     * @param Request $request
     * @param StoreCouponIssueServices $service
     * @param $cartId
     * @param $new
     * @param $shippingType
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function order(Request $request, StoreCouponIssueServices $service, $cartId, $new, $shippingType)
    {
        return app('json')->success($service->beUsableCouponList((int)$request->uid(), $cartId, !!$new, (int)$shippingType));
    }
}
