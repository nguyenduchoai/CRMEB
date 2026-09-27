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

namespace app\api\controller\v2;


use app\Request;
use app\services\diy\DiyServices;
use app\services\product\product\StoreCategoryServices;
use app\services\product\product\StoreProductServices;
use app\services\user\UserServices;
use app\services\user\UserSignServices;
use app\services\wechat\WechatUserServices;

class PublicController
{
    /**
     * Lấy dữ liệu trang chủ
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $fastNumber = (int)sys_config('fast_number', 0);//TODO Số lượng danh mục chọn nhanh
        /** @var StoreCategoryServices $categoryService */
        $categoryService = app()->make(StoreCategoryServices::class);
        $info['fastList'] = $fastNumber ? $categoryService->byIndexList($fastNumber, 'id,cate_name,pid,pic') : [];//TODO Số lượng danh mục chọn nhanh
        /** @var StoreProductServices $storeProductServices */
        $storeProductServices = app()->make(StoreProductServices::class);
        //Lấy sản phẩm đề xuất
        [$baseList, $firstList, $benefit, $likeInfo, $vipList] = $storeProductServices->getRecommendProductArr((int)$request->uid(), ['is_best', 'is_new', 'is_benefit', 'is_hot']);
        $info['bastList'] = $baseList;//TODO Số lượng đề xuất sản phẩm chất lượng
        $info['firstList'] = $firstList;//TODO Số lượng sản phẩm mới ra mắt
        if ($request->uid()) {
            /** @var UserServices $userService */
            $userService = app()->make(UserServices::class);
            //Kiểm tra thành viên có hết hạn không
            $userService->offMemberLevel($request->uid());
            /** @var WechatUserServices $wechatUserService */
            $wechatUserService = app()->make(WechatUserServices::class);
            $subscribe = (bool)$wechatUserService->value(['uid' => $request->uid(), 'user_type' => 'wechat'], 'subscribe');
        } else {
            $subscribe = true;
        }
        $tengxun_map_key = sys_config('tengxun_map_key');
        $site_name = sys_config('site_name');
        return app('json')->success(compact('info', 'benefit', 'likeInfo', 'subscribe', 'tengxun_map_key', 'site_name'));
    }

    /**
     * Lấy dữ liệu trang
     * @param Request $request
     * @param string $name
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDiy(Request $request, $name = '')
    {
        list($id) = $request->getMore([
            ['id', 0]
        ], true);
        /** @var DiyServices $diyService */
        $diyService = app()->make(DiyServices::class);
        $data = $diyService->getDiy($id);
        return app('json')->success($data);
    }

    /**
     * @param int $id
     * @return mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/2/8
     */
    public function getVersion($id = 0)
    {
        /** @var DiyServices $diyService */
        $diyService = app()->make(DiyServices::class);
        $data = $diyService->getDiyVersion((int)$id);
        return app('json')->success(['version' => $data['version'] ?: '', 'is_diy' => $data['is_diy']]);
    }

    /**
     * Bắt buộc liên kết số điện thoại
     * @return mixed
     */
    public function bindPhoneStatus()
    {
        $status = (bool)sys_config('store_user_mobile');
        return app('json')->success(compact('status'));
    }

    /**
     * Có theo dõi không
     * @param Request $request
     * @param WechatServices $services
     * @return mixed
     */
    public function subscribe(Request $request, WechatUserServices $services)
    {
        return app('json')->success(['subscribe' => (bool)$services->value(['uid' => $request->uid(), 'user_type' => 'wechat'], 'subscribe')]);
    }

    /**
     * Lấy trạng thái mở nhận tại điểm nhận hàng
     * @return mixed
     */
    public function getStoreStatus()
    {
        $data['store_status'] = sys_config('store_self_mention', 0);
        return app('json')->success($data);
    }

    /**
     * Lấy lựa chọn màu và lựa chọn mẫu danh mục
     * @param DiyServices $services
     * @param $name
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function colorChange(DiyServices $services, $name)
    {
        $status = (int)$services->getColorChange((string)$name);
        $is_diy = $services->value(['status' => 1, 'is_del' => 0], 'is_diy');
        return app('json')->success(compact('status', 'is_diy'));
    }

    public function getDiySign(Request $request)
    {
        $uid = (int)$request->uid();
        return app('json')->success(app()->make(UserSignServices::class)->signConfig($uid, 1));
    }
}
