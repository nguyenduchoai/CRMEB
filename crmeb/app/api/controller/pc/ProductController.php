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

namespace app\api\controller\pc;


use app\Request;
use app\services\pc\ProductServices;
use app\services\product\product\StoreCategoryServices;
use app\services\product\product\StoreProductServices;

class ProductController
{
    protected $services;

    public function __construct(ProductServices $services)
    {
        $this->services = $services;
    }

    /**
     * Lấy danh sách sản phẩm
     * @param Request $request
     * @param StoreCategoryServices $services
     * @return mixed
     */
    public function getProductList(Request $request, StoreCategoryServices $services)
    {
        $where = $request->getMore([
            [['sid', 'd'], 0],
            [['cid', 'd'], 0],
            ['keyword', '', '', 'store_name'],
            ['priceOrder', ''],
            ['salesOrder', ''],
            [['news', 'd'], 0, '', 'timeOrder'],
            [['type', 0], 0],
            ['ids', ''],
            [['selectId', 'd'], 0]
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
        }
        if (!$where['ids']) {
            unset($where['ids']);
        }
        return app('json')->success($this->services->getProductList($where, $request->uid()));
    }

    /**
     * Mã Mini Program cho trang chi tiết sản phẩm bản PC
     * @param Request $request
     * @return mixed
     */
    public function getProductRoutineCode(Request $request)
    {
        list($product_id, $type) = $request->getMore([
            ['product_id', 0],
            ['type', 'product'],
        ], true);
        $routineCode = $this->services->getProductRoutineCode((int)$product_id, $type);
        return app('json')->success(['routineCode' => $routineCode]);
    }

    /**
     * Sản phẩm đề xuất
     * @param Request $request
     * @param $type
     * @return mixed
     */
    public function getRecommendList(Request $request, $type)
    {
        /** @var StoreProductServices $product */
        $product = app()->make(StoreProductServices::class);
        $data = [];
        $data['list'] = [];
        $where['is_show'] = 1;
        $where['is_del'] = 0;
        if ($type == 1) {//TODO Đề xuất sản phẩm chất lượng
            $data['list'] = $product->getRecommendProduct($request->uid(), 'is_best', 0, 'mid');//TODO Số lượng đề xuất sản phẩm chất lượng
            $where['is_best'] = 1;
        } else if ($type == 2) {//TODO Bảng xếp hạng phổ biến
            $data['list'] = $product->getRecommendProduct($request->uid(), 'is_hot', 0, 'mid');//TODO Bảng xếp hạng phổ biến - Có thể bạn thích
            $where['is_hot'] = 1;
        } else if ($type == 3) {//TODO Sản phẩm mới ra mắt
            $data['list'] = $product->getRecommendProduct($request->uid(), 'is_new', 0, 'mid');//TODO Sản phẩm mới ra mắt
            $where['is_new'] = 1;
        } else if ($type == 4) {//TODO Sản phẩm khuyến mãi
            $data['list'] = $product->getRecommendProduct($request->uid(), 'is_benefit', 0, 'mid');//TODO Sản phẩm khuyến mãi
            $where['is_benefit'] = 1;
        }
        foreach ($data['list'] as &$item) {
            if (count($item['star'])) {
                $item['star'] = bcdiv((string)array_sum(array_column($item['star'], 'product_score')), (string)count($item['star']), 1);
            } else {
                $item['star'] = '5.0';
            }
        }
        $data['count'] = $product->getCount($where);
        return app('json')->success($data);
    }

    /**
     * Lấy sản phẩm tốt được đề xuất
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGoodProduct()
    {
        /** @var StoreProductServices $product */
        $product = app()->make(StoreProductServices::class);
        $list = get_thumb_water($product->getProducts(['is_good' => 1, 'is_del' => 0, 'is_show' => 1]), 'mid');
        return app('json')->success(compact('list'));
    }
}
