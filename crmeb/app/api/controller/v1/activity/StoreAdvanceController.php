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

namespace app\api\controller\v1\activity;

use app\Request;
use app\services\activity\advance\StoreAdvanceServices;

/**
 * Controller đặt trước
 * Class StoreAdvanceController
 * @package app\api\controller\v1\activity
 */
class StoreAdvanceController
{
    /**
     * StoreAdvanceController constructor.
     * @param StoreAdvanceServices $services
     */
    public function __construct(StoreAdvanceServices $services)
    {
        $this->services = $services;
    }

    /**
     * Danh sách đặt trước
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(Request $request)
    {
        $where = $request->getMore([
            ['time_type', 0]
        ]);
        $where['status'] = 1;
        $data = $this->services->getList($where);
        return app('json')->success($data);
    }

    /**
     * Chi tiết sản phẩm đặt trước
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail(Request $request, $id)
    {
        $data = $this->services->getAdvanceinfo($request, $id);
        return app('json')->success($data);
    }
}
