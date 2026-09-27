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

namespace app\kefuapi\controller;


use think\facade\App;
use app\services\kefu\ProductServices;

/**
 * Class Product
 * @package app\kefuapi\controller
 */
class Product extends AuthController
{
    /**
     * Product constructor.
     * @param App $app
     * @param ProductServices $services
     */
    public function __construct(App $app, ProductServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy lịch sử mua hàng của người dùng
     * @param $uid
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCartProductList($uid, string $store_name = '')
    {
        return app('json')->success(get_thumb_water($this->services->getProductCartList((int)$uid, $store_name)));
    }

    /**
     * Lịch sử xem của người dùng
     * @param $uid
     * @param string $store_name
     * @return mixed
     */
    public function getVisitProductList($uid, string $store_name = '')
    {
        return app('json')->success(get_thumb_water($this->services->getVisitProductList((int)$uid, $store_name)));
    }

    /**
     * Lấy sản phẩm bán chạy người dùng đã mua
     * @param $uid
     * @param string $store_name
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductHotSale($uid, string $store_name = '')
    {
        return app('json')->success(get_thumb_water($this->services->getProductHotSale((int)$uid, $store_name)));
    }

    /**
     * Chi tiết sản phẩm
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductInfo($id)
    {
        return app('json')->success(get_thumb_water($this->services->getProductInfo((int)$id), 'big', ['image', 'slider_image']));
    }
}
