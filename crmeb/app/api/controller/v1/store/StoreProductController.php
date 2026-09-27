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
namespace app\api\controller\v1\store;

use app\Request;
use app\services\product\product\StoreCategoryServices;
use app\services\product\product\StoreProductReplyServices;
use app\services\product\product\StoreProductServices;
use app\services\user\UserServices;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * Lớp sản phẩm
 * Class StoreProductController
 * @package app\api\controller\store
 */
class StoreProductController
{
    /**
     * Service sản phẩm
     * @var StoreProductServices
     */
    protected $services;

    public function __construct(StoreProductServices $services)
    {
        $this->services = $services;
    }

    /**
     * Danh sách sản phẩm
     * @param Request $request
     * @param StoreCategoryServices $services
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function lst(Request $request, StoreCategoryServices $services)
    {
        $where = $request->getMore([
            [['sid', 'd'], 0],
            [['cid', 'd'], 0],
            ['keyword', '', '', 'store_name'],
            ['priceOrder', ''],
            ['salesOrder', ''],
            [['news', 'd'], 0, '', 'is_new'],
            [['type', 'd'], 0],
            ['ids', ''],
            [['selectId', 'd'], 0],
            [['productId', 'd'], 0],
            [['coupon_category_id', 'd'], 0],
            ['cate_id', ''],
            ['store_label_id', ''],
        ]);
        if ($where['selectId'] && (!$where['sid'] || !$where['cid'])) {
            if ($services->value(['id' => $where['selectId']], 'pid')) {
                $where['sid'] = $where['selectId'];
            } else {
                $where['cid'] = $where['selectId'];
            }
        }
        if ($where['ids'] && is_string($where['ids'])) {
            $where['ids'] = explode(',', $where['ids']);
            foreach ($where['ids'] as $key => &$item) {
                $where['ids'][$key] = (int)$item;
                if ($where['ids'][$key] == 0) unset($where['ids'][$key]);
            }
        }
        if (!$where['ids']) {
            unset($where['ids']);
        }
        if ($where['cate_id'] !== '') {
            $where['cate_id'] = explode(',', $where['cate_id']);
            foreach ($where['cate_id'] as $keys => &$items) {
                $where['cate_id'][$keys] = (int)$items;
            }
        } else {
            $where['cate_id'] = [];
        }
        if ($where['store_label_id'] !== '') {
            $where['store_label_id'] = explode(',', $where['store_label_id']);
            foreach ($where['store_label_id'] as $keys => &$items) {
                $where['store_label_id'][$keys] = (int)$items;
            }
        } else {
            $where['store_label_id'] = [];
        }
        $type = 'big';
        $field = ['image', 'recommend_image'];
        $list = $this->services->getGoodsList($where, (int)$request->uid());
        return app('json')->success(get_thumb_water($list, $type, $field));
    }

    /**
     * Mã QR chia sẻ sản phẩm - Cộng tác viên
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function code(Request $request, $id)
    {
        if ($request->uid()) {
            $user = $request->user();
        } else {
            $user = ['uid' => 0, 'is_promoter' => 0];
        }
        $code = $this->services->getCode((int)$id, $request->get('user_type', 'wechat'), $user);
        return app('json')->success(['code' => $code]);
    }

    /**
     * Chi tiết sản phẩm
     * @param Request $request
     * @param $id
     * @param int $type
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function detail(Request $request, $id, $type = 0)
    {
        $data = $this->services->productDetail($request, (int)$id, (int)$type);
        return app('json')->success($data);
    }

    /**
     * Gợi ý cho bạn
     * @param Request $request
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function product_hot(Request $request)
    {
        $vip_user = $request->uid() ? app()->make(UserServices::class)->value(['uid' => $request->uid()], 'is_money_level') : 0;
        $list = $this->services->getProducts(['is_hot' => 1, 'is_show' => 1, 'is_del' => 0, 'vip_user' => $vip_user]);
        return app('json')->success(get_thumb_water($list, 'mid'));
    }

    /**
     * Lấy ảnh trình chiếu và sản phẩm của các loại sản phẩm đề xuất trên trang chủ
     * @param Request $request
     * @param $type
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function groom_list(Request $request, $type)
    {
        $info['banner'] = [];
        $info['list'] = [];
        if ($type == 1) {//TODO Đề xuất sản phẩm chất lượng
            $info['banner'] = sys_data('routine_home_bast_banner') ?: [];//TODO Ảnh đề xuất sản phẩm chất lượng trang chủ
            $info['list'] = $this->services->getRecommendProduct($request->uid(), 'is_best');//TODO Số lượng đề xuất sản phẩm chất lượng
        } else if ($type == 2) {//TODO Bảng xếp hạng phổ biến
            $info['banner'] = sys_data('routine_home_hot_banner') ?: [];//TODO Ảnh đề xuất Bảng xếp hạng phổ biến - Có thể bạn thích
            $info['list'] = $this->services->getRecommendProduct($request->uid(), 'is_hot');//TODO Bảng xếp hạng phổ biến - Có thể bạn thích
        } else if ($type == 3) {//TODO Sản phẩm mới ra mắt
            $info['banner'] = sys_data('routine_home_new_banner') ?: [];//TODO Ảnh đề xuất sản phẩm mới ra mắt
            $info['list'] = $this->services->getRecommendProduct($request->uid(), 'is_new');//TODO Sản phẩm mới ra mắt
        } else if ($type == 4) {//TODO Sản phẩm khuyến mãi
            $info['banner'] = sys_data('routine_home_benefit_banner') ?: [];//TODO Ảnh đề xuất sản phẩm khuyến mãi
            $info['list'] = $this->services->getRecommendProduct($request->uid(), 'is_benefit');//TODO Sản phẩm khuyến mãi
        } else if ($type == 5) {//TODO Sản phẩm thành viên
            $info['list'] = $this->services->getRecommendProduct($request->uid(), 'is_vip');//TODO Sản phẩm thành viên
        }
        return app('json')->success($info);
    }

    /**
     * Số lượng đánh giá và tỷ lệ đánh giá tốt của sản phẩm
     * @param $id
     * @return mixed
     */
    public function reply_config($id)
    {
        /** @var StoreProductReplyServices $replyService */
        $replyService = app()->make(StoreProductReplyServices::class);
        $count = $replyService->productReplyCount($id);
        return app('json')->success($count);
    }

    /**
     * Lấy đánh giá sản phẩm
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function reply_list(Request $request, $id)
    {
        [$type] = $request->getMore([
            [['type', 'd'], 0]
        ], true);
        /** @var StoreProductReplyServices $replyService */
        $replyService = app()->make(StoreProductReplyServices::class);
        $list = $replyService->getProductReplyList($id, $type);
        return app('json')->success(get_thumb_water($list, 'big', ['pics']));
    }

    /**
     * Lấy danh sách đặt trước
     * @param Request $request
     * @return mixed
     */
    public function advanceList(Request $request)
    {
        $where = $request->getMore([
            [['time_type', 'd'], 0]
        ]);
        return app('json')->success($this->services->getAdvanceList($where));
    }

    /**
     * Lấy giá thực tế của sản phẩm
     * @param Request $request
     * @param $id
     * @param $unique
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/2/5
     */
    public function realPrice(Request $request, $id, $unique)
    {
        $uid = $request->uid() ?? 0;
        if (!$id || !$unique) return app('json')->fail('Thiếu tham số');
        return app('json')->success($this->services->realPrice($uid, $id, $unique));
    }
}
