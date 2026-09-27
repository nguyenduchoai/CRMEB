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
namespace app\api\controller\v1\user;

use app\Request;
use app\services\product\product\StoreProductRelationServices;


/**
 * Yêu thích của người dùng
 * Class UserCollectController
 * @package app\api\controller\v1\user
 */
class UserCollectController
{
    protected $services = NUll;

    /**
     * UserCollectController constructor.
     * @param StoreProductRelationServices $services
     */
    public function __construct(StoreProductRelationServices $services)
    {
        $this->services = $services;
    }


    /**
     * Lấy sản phẩm yêu thích
     * @param Request $request
     * @return mixed
     */
    public function collect_user(Request $request)
    {
        $uid = (int)$request->uid();
        return app('json')->success($this->services->getUserCollectProduct($uid));
    }

    /**
     * Thêm yêu thích
     * @param Request $request
     * @return mixed
     */
    public function collect_add(Request $request)
    {
        [$id, $category] = $request->postMore([
            ['id', 0],
            ['category', 'product']
        ], true);
        if (!$id || !is_numeric($id)) return app('json')->fail(100100);
        $res = $this->services->productRelation((int)$id, $request->uid(), 'collect', $category);
        if (!$res) {
            return app('json')->fail(410130);
        } else {
            return app('json')->success(410129);
        }
    }

    /**
     * Bỏ yêu thích
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function collect_del(Request $request)
    {
        [$id, $category] = $request->postMore([
            ['id', []],
            ['category', 'product']
        ], true);
        $uid = (int)$request->uid();
        $res = $this->services->unProductRelation($id, $uid, 'collect', $category);
        if (!$res) return app('json')->fail(100020);
        else return app('json')->success(100019);
    }

    /**
     * Yêu thích theo lô
     * @param Request $request
     * @return mixed
     */
    public function collect_all(Request $request)
    {
        $collectInfo = $request->postMore([
            ['id', ''],
            ['category', 'product'],
        ]);
        $collectInfo['id'] = explode(',', $collectInfo['id']);
        if (!count($collectInfo['id'])) {
            return app('json')->fail(100100);
        }
        $uid = (int)$request->uid();
        $productIdS = $collectInfo['id'];
        $res = $this->services->productRelationAll($productIdS, $uid, 'collect', $collectInfo['category']);
        if (!$res) {
            return app('json')->fail(410130);
        } else {
            return app('json')->success(410129);
        }
    }
}
