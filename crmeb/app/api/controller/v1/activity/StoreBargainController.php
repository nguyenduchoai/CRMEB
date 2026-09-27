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
namespace app\api\controller\v1\activity;

use app\services\activity\bargain\StoreBargainServices;
use app\services\activity\bargain\StoreBargainUserHelpServices;
use app\services\activity\bargain\StoreBargainUserServices;
use app\Request;
use app\services\user\UserServices;

/**
 * Lớp sản phẩm săn giảm giá
 * Class StoreBargainController
 * @package app\api\controller\activity
 */
class StoreBargainController
{
    protected $services;

    public function __construct(StoreBargainServices $services)
    {
        $this->services = $services;
    }

    /**
     * Ảnh đầu trang danh sách săn giảm giá
     * @return mixed
     */
    public function config()
    {
        $lovely = sys_data('routine_lovely') ?? [];//Ảnh banner
        $info = $lovely[2] ?? [];
        return app('json')->success($info);
    }

    /**
     * Danh sách sản phẩm săn giảm giá
     * @param Request $request
     * @return mixed
     */
    public function lst(Request $request)
    {
        $bargainList = $this->services->getBargainList();
        return app('json')->success(get_thumb_water($bargainList));
    }

    /**
     * Chi tiết săn giảm giá và thông tin người đang đăng nhập
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail(Request $request, $id)
    {
        list($bargainUid) = $request->getMore([
            ['bargainUid', 0]
        ], true);
        $data = $this->services->getBargain($request, $id, (int)$bargainUid);
        return app('json')->success($data);
    }

    /**
     * Săn giảm giá - Số lần xem/chia sẻ/tham gia
     * @param Request $request
     * @return mixed
     */
    public function share(Request $request)
    {
        /** @var StoreBargainUserHelpServices $bargainUserHelpService */
        $bargainUserHelpService = app()->make(StoreBargainUserHelpServices::class);
        list($bargainId) = $request->postMore([['bargainId', 0]], true);
        $data['lookCount'] = $this->services->sum([], 'look');//TODO Số người xem
        $data['userCount'] = $bargainUserHelpService->count([]);//TODO Số người tham gia
        if (!$bargainId) return app('json')->success($data);
        $this->services->addBargain($bargainId, 'share');
        $data['shareCount'] = $this->services->sum([], 'share');//TODO Số người chia sẻ
        return app('json')->success($data);
    }

    /**
     * Bắt đầu săn giảm giá
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function start(Request $request)
    {
        list($bargainId) = $request->postMore([
            ['bargainId', 0]
        ], true);
        return app('json')->success($this->services->setBargain($request->uid(), $bargainId));
    }

    /**
     * Săn giảm giá - Giúp bạn bè giảm giá
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function help(Request $request)
    {
        list($bargainId, $bargainUserUid) = $request->postMore([
            ['bargainId', 0],
            ['bargainUserUid', 0]
        ], true);
        return app('json')->success($this->services->setHelpBargain($request->uid(), (int)$bargainId, (int)$bargainUserUid));
    }

    /**
     * Săn giảm giá - Người giúp giảm giá
     * @param Request $request
     * @return mixed
     */
    public function help_list(Request $request)
    {
        list($bargainId, $bargainUserUid) = $request->postMore([
            ['bargainId', 0],
            ['bargainUserUid', 0],
        ], true);
        /** @var StoreBargainUserServices $bargainUser */
        $bargainUser = app()->make(StoreBargainUserServices::class);
        $bargainUserTableId = $bargainUser->getBargainUserTableId((int)$bargainId, (int)$bargainUserUid);

        /** @var StoreBargainUserHelpServices $bargainUserHelp */
        $bargainUserHelp = app()->make(StoreBargainUserHelpServices::class);
        [$page, $limit] = $this->services->getPageValue();
        $storeBargainUserHelp = $bargainUserHelp->getHelpList((int)$bargainUserTableId, $page, $limit);
        return app('json')->success($storeBargainUserHelp);
    }

    /**
     * Săn giảm giá - Thông tin người dùng bắt đầu săn giảm giá
     * @param Request $request
     * @return mixed
     */
    public function start_user(Request $request)
    {
        list($bargainId, $bargainUserUid) = $request->postMore([
            ['bargainId', 0],
            ['bargainUserUid', 0],
        ], true);
        if (!$bargainId || !$bargainUserUid) return app('json')->fail(100100);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->getUserInfo((int)$bargainUserUid);
        if (!$userInfo) {
            return app('json')->fail(410044);
        }
        return app('json')->success(['nickname' => $userInfo['nickname'], 'avatar' => $userInfo['avatar']]);
    }

    /**
     * Danh sách săn giảm giá (đã tham gia)
     * @param Request $request
     * @return mixed
     */
    public function user_list(Request $request)
    {
        $uid = $request->uid();
        /** @var StoreBargainUserServices $bargainUser */
        $bargainUser = app()->make(StoreBargainUserServices::class);
        $bargainUser->editBargainUserStatus($uid);// TODO Kiểm tra hoạt động săn giảm giá đã hết hạn
        $list = $bargainUser->getBargainUserAll($uid);
        if (count($list)) return app('json')->success(get_thumb_water($list));
        else return app('json')->success([]);
    }

    /**
     * Hủy săn giảm giá
     * @param Request $request
     * @return mixed
     */
    public function user_cancel(Request $request)
    {
        list($bargainId) = $request->postMore([['bargainId', 0]], true);
        if (!$bargainId) return app('json')->fail(100100);
        /** @var StoreBargainUserServices $bargainUser */
        $bargainUser = app()->make(StoreBargainUserServices::class);
        $res = $bargainUser->cancelBargain($bargainId, $request->uid());
        if ($res) return app('json')->success(100019);
        else return app('json')->success(100020);
    }

    /**
     * Poster săn giảm giá
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function poster(Request $request)
    {
        [$bargainId, $from] = $request->postMore([
            ['bargainId', ''],
            ['from', 'wechat']
        ], true);
        $posterUrl = $this->services->poster($bargainId, $request->user(), $from);
        if ($posterUrl) {
            return app('json')->success(['url' => $posterUrl]);
        } else {
            return app('json')->fail(410172);
        }
    }

    /**
     * Lấy thông tin poster chia sẻ
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function posterInfo(Request $request, $bargainId)
    {
        return app('json')->success($this->services->posterInfo((int)$bargainId, $request->user()));
    }
}
