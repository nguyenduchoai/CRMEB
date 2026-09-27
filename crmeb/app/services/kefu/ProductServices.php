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

namespace app\services\kefu;


use app\services\BaseServices;
use app\services\product\product\StoreProductCateServices;
use crmeb\exceptions\ApiException;
use app\dao\product\product\StoreProductDao;
use app\services\order\StoreOrderStoreOrderCartInfoServices;
use app\services\product\product\StoreProductVisitServices;

/**
 * Class ProductServices
 * @package app\services\kefu
 */
class ProductServices extends BaseServices
{

    /**
     * ProductServices constructor.
     * @param StoreProductDao $dao
     */
    public function __construct(StoreProductDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy lịch sử mua hàng của người dùng
     * @param int $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductCartList(int $uid, string $storeName = '')
    {
        [$page, $limit] = $this->getPageValue();
        /** @var StoreOrderStoreOrderCartInfoServices $services */
        $services = app()->make(StoreOrderStoreOrderCartInfoServices::class);
        $where['id'] = $services->getUserCartProductIds(['uid' => $uid]);
        $where['store_name'] = $storeName;
        return $this->dao->getProductCartList($where, $page, $limit, ['id', 'IFNULL(sales,0) + IFNULL(ficti,0) as sales', 'store_name', 'image', 'stock', 'price']);
    }

    /**
     * Lấy lịch sử xem của người dùng
     * @param int $uid
     * @return mixed
     */
    public function getVisitProductList(int $uid, string $storeName = '')
    {
        [$page, $limit] = $this->getPageValue();
        /** @var StoreProductVisitServices $service */
        $service = app()->make(StoreProductVisitServices::class);
        return $service->getUserVisitProductList(['uid' => $uid, 'store_name' => $storeName], $page, $limit);
    }

    /**
     * Lấy 20 sản phẩm bán chạy đầu tiên
     * @param int $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductHotSale(int $uid, string $storeName = '')
    {
        [$page, $limit] = $this->getPageValue();
        /** @var StoreOrderStoreOrderCartInfoServices $services */
        $services = app()->make(StoreOrderStoreOrderCartInfoServices::class);
        $productIds = $services->getUserCartProductIds(['uid' => $uid]);
        /** @var StoreProductCateServices $cateService */
        $cateService = app()->make(StoreProductCateServices::class);
        $where['id'] = $cateService->cateIdByProduct($cateService->productIdByCateId($productIds));
        $where['store_name'] = $storeName;
        return $this->dao->getUserProductHotSale($where, $page, $limit);
    }

    /**
     * Lấy chi tiết sản phẩm
     * @param int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductInfo(int $id)
    {
        $productInfo = $this->dao->get($id, ['store_name', 'IFNULL(sales,0) + IFNULL(ficti,0) as sales', 'image',
            'slider_image', 'price', 'vip_price', 'ot_price', 'stock', 'id'], ['description']);
        if (!$productInfo) {
            throw new ApiException('Không tìm thấy sản phẩm');
        }
        return $productInfo->toArray();
    }
}
