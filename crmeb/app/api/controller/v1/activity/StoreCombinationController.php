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
use app\services\activity\combination\StoreCombinationServices;
use app\services\activity\combination\StorePinkServices;
use app\services\other\QrcodeServices;

/**
 * Lớp mua chung
 * Class StoreCombinationController
 * @package app\api\controller\activity
 */
class StoreCombinationController
{
    protected $services;

    public function __construct(StoreCombinationServices $services)
    {
        $this->services = $services;
    }

    /**
     * Danh sách mua chung
     * @return mixed
     */
    public function lst()
    {
        $list = $this->services->getCombinationList();
        return app('json')->success(get_thumb_water($list));
    }


    /**
     * Chi tiết sản phẩm mua chung
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function detail(Request $request, $id)
    {
        $data = $this->services->combinationDetail($request, $id);
        return app('json')->success($data);
    }

    /**
     * Mua chung - Mở nhóm
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function pink(Request $request, $id)
    {
        $data = $this->services->getPinkInfo($request, (int)$id);
        return app('json')->success($data);
    }

    /**
     * Mua chung - Hủy mở nhóm
     * @param Request $request
     * @return mixed
     */
    public function remove(Request $request)
    {
        list($id, $cid) = $request->postMore([
            ['id', 0],
            ['cid', 0],
        ], true);
        if (!$id || !$cid) return app('json')->fail('Tham số không hợp lệ');
        /** @var StorePinkServices $pinkService */
        $pinkService = app()->make(StorePinkServices::class);
        $pinkService->removePink($request->uid(), $cid, $id);
        return app('json')->success('Thao tác thành công');
    }


    /**
     * Poster mua chung
     * @param Request $request
     * @return mixed
     */
    public function poster(Request $request)
    {
        list($pinkId, $from) = $request->postMore([
            ['id', 0],
            ['from', 'wechat']
        ], true);
        if (!$pinkId) return app('json')->fail('Tham số không hợp lệ');
        $user = $request->user();
        /** @var StorePinkServices $pinkService */
        $pinkService = app()->make(StorePinkServices::class);
        $res = $pinkService->getPinkPoster($pinkId, $from, $user);
        return app('json')->success(['url' => $res]);
    }

    /**
     * Lấy chi tiết poster mua chung
     * @param Request $request
     * @param StorePinkServices $services
     * @param $id
     * @return mixed
     */
    public function posterInfo(Request $request, StorePinkServices $services, $id)
    {
        return app('json')->success($services->posterInfo((int)$id, $request->user()));
    }

    /**
     * Lấy mã QR Mini Program flash sale
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function code(Request $request, $id)
    {
        /** @var QrcodeServices $qrcodeService */
        $qrcodeService = app()->make(QrcodeServices::class);
        $url = $qrcodeService->getRoutineQrcodePath($id, $request->uid(), 1);
        if ($url) {
            return app('json')->success(['code' => $url]);
        } else {
            return app('json')->success(['code' => '']);
        }
    }

    /**
     * Lấy ảnh trình chiếu (banner) danh sách mua chung
     */
    public function banner_list()
    {
        $banner = sys_data('combination_banner') ?? [];
        return app('json')->success($banner);
    }
}
