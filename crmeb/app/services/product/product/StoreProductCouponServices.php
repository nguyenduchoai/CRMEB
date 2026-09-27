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
declare (strict_types=1);

namespace app\services\product\product;

use app\services\BaseServices;
use app\dao\product\product\StoreProductCouponDao;
use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\order\StoreOrderCartInfoServices;
use app\services\order\StoreOrderServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\services\CacheService;

/**
 *
 * Class StoreProductCouponServices
 * @package app\services\coupon
 * @method delete($id, ?string $key = null) Xóa
 */
class StoreProductCouponServices extends BaseServices
{

    /**
     * StoreProductCouponServices constructor.
     * @param StoreProductCouponDao $dao
     */
    public function __construct(StoreProductCouponDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Phiếu giảm giá liên kết với sản phẩm
     * @param int $id
     * @param array $coupon_ids
     * @return bool
     */
    public function setCoupon(int $id, array $coupon_ids)
    {
        $this->dao->delete(['product_id' => $id]);
        if ($coupon_ids) {
            $data = $data_all = [];
            $data['product_id'] = $id;
            $data['add_time'] = time();
            foreach ($coupon_ids as $cid) {
                if (!empty($cid) && (int)$cid) {
                    $data['issue_coupon_id'] = $cid;
                    $data_all[] = $data;
                }
            }
            $res = true;
            if ($data_all) {
                $res = $this->dao->saveAll($data_all);
            }
            if (!$res) throw new AdminException(400561);
        }
        return true;
    }

    /**
     * Lấy phiếu giảm giá tặng khi đặt hàng
     * @param int $uid
     * @param $orderId
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getOrderProductCoupon(int $uid, $orderId)
    {
        /** @var StoreOrderServices $storeOrder */
        $storeOrder = app()->make(StoreOrderServices::class);
        $order = $storeOrder->getOne(['order_id' => $orderId]);
        if (!$order || $order['uid'] != $uid) {
            throw new ApiException(410173);
        }
        $key = 'order_product_coupon_' . $uid . '_' . $order['id'];
        return CacheService::get($key, []);
    }

    /**
     * Tặng phiếu giảm giá khi đặt hàng
     * @param int $uid
     * @param $orderId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function giveOrderProductCoupon(int $uid, $orderId)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $user = $userServices->getUserInfo($uid);
        if (!$user) {
            throw new ApiException(410032);
        }
        /** @var StoreOrderServices $storeOrder */
        $storeOrder = app()->make(StoreOrderServices::class);
        $order = $storeOrder->getOne(['id' => $orderId]);
        if (!$order || $order['uid'] != $uid) {
            throw new ApiException(410173);
        }
        /** @var StoreOrderCartInfoServices $storeOrderCartInfo */
        $storeOrderCartInfo = app()->make(StoreOrderCartInfoServices::class);
        $productIds = $storeOrderCartInfo->getColumn(['oid' => $order['id']], 'product_id');
        $list = [];
        if ($productIds) {
            $couponList = $this->dao->getProductCoupon($productIds);
            if ($couponList) {
                /** @var StoreCouponIssueServices $storeCoupon */
                $storeCoupon = app()->make(StoreCouponIssueServices::class);
                $list = $storeCoupon->orderPayGiveCoupon($uid, array_column($couponList, 'issue_coupon_id'));
                foreach ($list as &$item) {
                    $item['add_time'] = date('Y-m-d', $item['add_time']);
                    $item['end_time'] = date('Y-m-d', $item['end_time']);
                }
            }
        }
        $key = 'order_product_coupon_' . $uid . '_' . $orderId;
        CacheService::set($key, $list, 7200);
        return true;
    }
}
