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
namespace app\adminapi\controller\v1\marketing;

use app\adminapi\controller\AuthController;
use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\activity\coupon\StoreCouponUserServices;
use think\facade\App;

/**
 * Controller lịch sử phát phiếu giảm giá
 * Class StoreCategory
 * @package app\admin\controller\system
 */
class StoreCouponUser extends AuthController
{
    public function __construct(App $app, StoreCouponUserServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lịch sử nhận của người dùng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['coupon_title', ''],
            ['nickname', ''],
        ]);
        $list = $this->services->systemPage($where);
        return app('json')->success($list);
    }

    /**
     * Phát phiếu giảm giá cho cá nhân được chỉ định
     * @return mixed
     */
    public function grant()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['uid', '']
        ]);
        if (!$data['id']) return app('json')->fail(100100);
        /** @var StoreCouponIssueServices $issueService */
        $issueService = app()->make(StoreCouponIssueServices::class);
        $coupon = $issueService->get($data['id']);
        if (!$coupon) {
            return app('json')->fail(100026);
        } else {
            $coupon = $coupon->toArray();
        }
        $user = explode(',', $data['uid']);
        if (!$issueService->setCoupon($coupon, $user))
            return app('json')->fail(100031);
        else
            return app('json')->success(100030);

    }
}
