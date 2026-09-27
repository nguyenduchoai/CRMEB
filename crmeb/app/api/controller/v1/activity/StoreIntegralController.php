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
use app\services\activity\integral\StoreIntegralServices;

class StoreIntegralController
{
    protected $services;

    public function __construct(StoreIntegralServices $services)
    {
        $this->services = $services;
    }

    /**
     * Dữ liệu trang chủ cửa hàng đổi điểm
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $data['banner'] = sys_data('integral_shop_banner') ?? [];//TODO Banner cửa hàng đổi điểm
        $where = ['is_show' => 1];
        $where['is_host'] = 1;
        $data['list'] = $this->services->getIntegralList($where);
        return app('json')->success(get_thumb_water($data, 'big'));
    }

    /**
     * Danh sách sản phẩm
     * @param Request $request
     * @return mixed
     */
    public function lst(Request $request)
    {
        $where = $request->getMore([
            ['store_name', ''],
            ['priceOrder', ''],
            ['salesOrder', ''],
        ]);
        $where['is_show'] = 1;
        $list = $this->services->getIntegralList($where);
        return app('json')->success(get_thumb_water($list, 'mid'));
    }

    /**
     * Chi tiết sản phẩm đổi điểm
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function detail(Request $request, $id)
    {
        $data = $this->services->integralDetail($request, $id);
        return app('json')->success($data);
    }
}
