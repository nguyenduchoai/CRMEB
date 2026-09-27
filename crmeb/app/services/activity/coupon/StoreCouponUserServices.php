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
declare (strict_types=1);

namespace app\services\activity\coupon;

use app\services\BaseServices;
use app\dao\activity\coupon\StoreCouponUserDao;
use app\services\product\product\StoreCategoryServices;
use app\services\product\product\StoreProductCateServices;
use app\services\user\UserServices;
use crmeb\utils\Arr;

/**
 *
 * Class StoreCouponUserServices
 * @package app\services\coupon
 * @method useCoupon(int $id) Sử dụng phiếu giảm giá và cập nhật trạng thái phiếu giảm giá
 * @method delUserCoupon(array $where)
 */
class StoreCouponUserServices extends BaseServices
{

    /**
     * StoreCouponUserServices constructor.
     * @param StoreCouponUserDao $dao
     */
    public function __construct(StoreCouponUserDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @return array
     */
    public function issueLog(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, 'uid,add_time', ['userInfo'], $page, $limit);
        foreach ($list as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function systemPage(array $where)
    {
        /** @var StoreCouponUserUserServices $storeCouponUserUserService */
        $storeCouponUserUserService = app()->make(StoreCouponUserUserServices::class);
        return $storeCouponUserUserService->getList($where);
    }

    /**
     * Lấy phiếu giảm giá của người dùng
     * @param int $id
     */
    public function getUserCouponList(int $id)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList(['uid' => $id], '*', ['issue'], $page, $limit);
        foreach ($list as &$item) {
            $item['_add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['_end_time'] = date('Y-m-d H:i:s', $item['end_time']);
            if (!$item['coupon_time']) {
                $item['coupon_time'] = bcdiv((string)($item['end_use_time'] - $item['start_use_time']), '86400', 0);
            }
        }
        $count = $this->dao->count(['uid' => $id]);
        return compact('list', 'count');
    }

    /**
     * Khôi phục phiếu giảm giá
     * @param int $id
     * @return bool|mixed
     */
    public function recoverCoupon(int $id)
    {
        $status = $this->dao->value(['id' => $id], 'status');
        if ($status) return $this->dao->update($id, ['status' => 0, 'use_time' => '']);
        else return true;
    }

    /**
     * Phiếu giảm giá hết hạn sẽ hết hiệu lực
     */
    public function checkInvalidCoupon()
    {
        $this->dao->update([['end_time', '<', time()], ['status', '=', '0']], ['status' => 2]);
    }

    /**
     * Lấy số lượng phiếu giảm giá còn hiệu lực của người dùng
     * @param int $uid
     * @return int
     */
    public function getUserValidCouponCount(int $uid)
    {
        $this->checkInvalidCoupon();
        return $this->dao->getCount(['uid' => $uid, 'status' => 0]);
    }

    /**
     * Hiển thị phiếu giảm giá có thể dùng ở trang đặt hàng
     * @param $uid
     * @param $cartGroup
     * @param $price
     * @return array
     */
    public function getUsableCouponList(int $uid, array $cartGroup)
    {
        $userCoupons = $this->dao->getUserAllCoupon($uid);
        $result = [];
        if ($userCoupons) {
            $cartInfo = $cartGroup['valid'];
            foreach ($userCoupons as $coupon) {
                $price = 0;
                $count = 0;
                switch ($coupon['applicable_type']) {
                    case 0:
                    case 3:
                        foreach ($cartInfo as $cart) {
                            $price += bcmul((string)$cart['truePrice'], (string)$cart['cart_num'], 2);
                            $count++;
                        }
                        break;
                    case 1://Phiếu theo danh mục
                        /** @var StoreCategoryServices $storeCategoryServices */
                        $storeCategoryServices = app()->make(StoreCategoryServices::class);
                        $coupon_category = explode(',', (string)$coupon['category_id']);
                        $category_ids = $storeCategoryServices->getAllById($coupon_category);
                        if ($category_ids) {
                            $cateIds = array_column($category_ids, 'id');
                            foreach ($cartInfo as $cart) {
                                if (isset($cart['productInfo']['cate_id']) && array_intersect(explode(',', $cart['productInfo']['cate_id']), $cateIds)) {
                                    $price += bcmul((string)$cart['truePrice'], (string)$cart['cart_num'], 2);
                                    $count++;
                                }
                            }
                        }
                        break;
                    case 2:
                        foreach ($cartInfo as $cart) {
                            if (isset($cart['product_id']) && in_array($cart['product_id'], explode(',', $coupon['product_id']))) {
                                $price += bcmul((string)$cart['truePrice'], (string)$cart['cart_num'], 2);
                                $count++;
                            }
                        }
                        break;
                }
                if ($count && $coupon['use_min_price'] <= $price) {
                    $coupon['start_time'] = $coupon['start_time'] ? date('Y/m/d', $coupon['start_time']) : date('Y/m/d', $coupon['add_time']);
                    $coupon['add_time'] = date('Y/m/d', $coupon['add_time']);
                    $coupon['end_time'] = date('Y/m/d', $coupon['end_time']);
                    $coupon['start_use_time'] = $coupon['start_use_time'] > 0 ? date('Y/m/d', $coupon['start_use_time']) : 0;
                    $coupon['end_use_time'] = $coupon['start_use_time'] > 0 ? date('Y/m/d', $coupon['end_use_time']) : 0;
                    $coupon['title'] = $coupon['coupon_title'];
                    $coupon['type'] = $coupon['applicable_type'];
                    $coupon['use_min_price'] = floatval($coupon['use_min_price']);
                    $coupon['coupon_price'] = floatval($coupon['coupon_price']);
                    $result[] = $coupon;
                }
            }
        }
        return $result;
    }

    /**
     * Hiển thị phiếu giảm giá có thể dùng ở trang đặt hàng
     * @param $uid
     * @param $cartGroup
     * @param $price
     * @return array
     */
    public function getOldUsableCouponList(int $uid, array $cartGroup)
    {
        $cartPrice = $cateIds = [];
        $productId = Arr::getUniqueKey($cartGroup['valid'], 'product_id');
        foreach ($cartGroup['valid'] as $value) {
            $cartPrice[] = bcmul((string)$value['truePrice'], (string)$value['cart_num'], 2);
        }
        $maxPrice = count($cartPrice) ? max($cartPrice) : 0;
        if ($productId) {
            /** @var StoreProductCateServices $productCateServices */
            $productCateServices = app()->make(StoreProductCateServices::class);
            $cateId = $productCateServices->productIdByCateId($productId);
            if ($cateId) {
                /** @var StoreCategoryServices $cateServices */
                $cateServices = app()->make(StoreCategoryServices::class);
                $catePids = $cateServices->cateIdByPid($cateId);
                $cateIds = array_merge($cateId, $catePids);
            } else {
                $cateIds = $cateId;
            }

        }
        $productCouponList = $this->dao->productIdsByCoupon($productId, $uid, (string)$maxPrice);
        $cateCouponList = $this->dao->cateIdsByCoupon($cateIds, $uid, (string)$maxPrice);
        $list = array_merge($productCouponList, $cateCouponList);
        $couponIds = Arr::getUniqueKey($list, 'id');
        $sumCartPrice = array_sum($cartPrice);
        $list1 = $this->dao->getUserCoupon($couponIds, $uid, (string)$sumCartPrice);
        $list = array_merge($list, $list1);
        foreach ($list as &$item) {
            $item['add_time'] = date('Y/m/d', $item['add_time']);
            $item['end_time'] = date('Y/m/d', $item['end_time']);
            $item['title'] = $item['coupon_title'];
            $item['type'] = $item['applicable_type'] ?? 0;
        }
        return $list;
    }

    /**
     * Người dùng nhận phiếu giảm giá
     * @param $uid
     * @param $issueCouponInfo
     * @param string $type
     * @return mixed
     */
    public function addUserCoupon($uid, $issueCouponInfo, $type = 'get')
    {
        $data = [];
        $data['cid'] = $issueCouponInfo['id'];
        $data['uid'] = $uid;
        $data['coupon_title'] = $issueCouponInfo['title'];
        $data['coupon_price'] = $issueCouponInfo['coupon_price'];
        $data['use_min_price'] = $issueCouponInfo['use_min_price'];
        $data['add_time'] = time();
        if ($issueCouponInfo['coupon_time']) {
            $data['start_time'] = $data['add_time'];
            $data['end_time'] = $data['add_time'] + $issueCouponInfo['coupon_time'] * 86400;
        } else {
            $data['start_time'] = $issueCouponInfo['start_use_time'];
            $data['end_time'] = $issueCouponInfo['end_use_time'];
        }
        $data['type'] = $type;
        return $this->dao->save($data);
    }

    /**Thành viên nhận phiếu giảm giá
     * @param $uid
     * @param $issueCouponInfo
     * @param string $type
     * @return mixed
     */
    public function addMemberUserCoupon($uid, $issueCouponInfo, $type = 'get')
    {
        $data = [];
        $data['cid'] = $issueCouponInfo['id'];
        $data['uid'] = $uid;
        $data['coupon_title'] = $issueCouponInfo['title'];
        $data['coupon_price'] = $issueCouponInfo['coupon_price'];
        $data['use_min_price'] = $issueCouponInfo['use_min_price'];
        $data['add_time'] = time();
        $data['start_time'] = strtotime(date('Y-m-d 00:00:00', time()));
        $data['end_time'] = strtotime(date('Y-m-d 23:59:59', strtotime('+30 day')));
        $data['type'] = $type;
        return $this->dao->save($data);
    }

    /**
     * Lấy phiếu giảm giá người dùng đã nhận
     * @param int $uid
     * @param $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserCounpon(int $uid, $type)
    {
        $where = [];
        $where['uid'] = $uid;
        switch ($type) {
            case 0:
            case '':
                break;
            case 1:
                $where['status'] = 0;
                break;
            case 2:
                $where['status'] = 1;
                break;
            default:
                $where['status'] = 1;
                break;
        }
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getCouponListByOrder($where, 'status ASC,add_time DESC', $page, $limit);
        return $list ? $this->tidyCouponList($list) : [];
    }

    /**
     * Định dạng phiếu giảm giá
     * @param $couponList
     * @return mixed
     */
    public function tidyCouponList($couponList)
    {
        $time = time();
        foreach ($couponList as &$coupon) {
            if ($coupon['status'] == 'Đã sử dụng') {
                $coupon['_type'] = 0;
                $coupon['_msg'] = 'Đã sử dụng';
                $coupon['pc_type'] = 0;
                $coupon['pc_msg'] = 'Đã sử dụng';
            } else if ($coupon['status'] == 'Đã hết hạn') {
                $coupon['is_fail'] = 1;
                $coupon['_type'] = 0;
                $coupon['_msg'] = 'Đã hết hạn';
                $coupon['pc_type'] = 0;
                $coupon['pc_msg'] = 'Đã hết hạn';
            } else if ($coupon['end_time'] < $time) {
                $coupon['is_fail'] = 1;
                $coupon['_type'] = 0;
                $coupon['_msg'] = 'Đã hết hạn';
                $coupon['pc_type'] = 0;
                $coupon['pc_msg'] = 'Đã hết hạn';
            } else if ($coupon['start_time'] > $time) {
                $coupon['_type'] = 0;
                $coupon['_msg'] = 'Chưa bắt đầu';
                $coupon['pc_type'] = 1;
                $coupon['pc_msg'] = 'Chưa bắt đầu';
            } else {
                if ($coupon['start_time'] + 3600 * 24 > $time) {
                    $coupon['_type'] = 2;
                    $coupon['_msg'] = 'Dùng ngay';
                    $coupon['pc_type'] = 1;
                    $coupon['pc_msg'] = 'Có thể sử dụng';
                } else {
                    $coupon['_type'] = 1;
                    $coupon['_msg'] = 'Dùng ngay';
                    $coupon['pc_type'] = 1;
                    $coupon['pc_msg'] = 'Có thể sử dụng';
                }
            }
            $coupon['add_time'] = $coupon['_add_time'] = $coupon['start_time'] ? date('Y/m/d', $coupon['start_time']) : date('Y/m/d', $coupon['add_time']);
            $coupon['end_time'] = $coupon['_end_time'] = date('Y/m/d', $coupon['end_time']);
            $coupon['use_min_price'] = floatval($coupon['use_min_price']);
            $coupon['coupon_price'] = floatval($coupon['coupon_price']);
        }
        return $couponList;
    }

    /** Lấy danh sách phiếu giảm giá thành viên
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMemberCoupon($uid)
    {
        if (!$uid) return [];
        /** @var StoreCouponIssueServices $couponIssueService */
        $couponIssueService = app()->make(StoreCouponIssueServices::class);
        $couponWhere['receive_type'] = 4;
        $couponInfo = $couponIssueService->getMemberCouponIssueList($couponWhere);
        $couponList = [];
        if ($couponInfo) {
            $couponIds = array_column($couponInfo, 'id');
            $couponType = array_column($couponInfo, 'type', 'id');
            $couponList = $this->dao->getCouponListByOrder(['uid' => $uid, 'coupon_ids' => $couponIds], 'add_time desc');
            if ($couponList) {
                foreach ($couponList as $k => $v) {
                    $couponList[$k]['coupon_type'] = $couponIssueService->_couponType[$couponType[$v['cid']]];
                }
            }
        }
        return $couponList ? $this->tidyCouponList($couponList) : [];
    }

    /**Xem tình hình phát phiếu giảm giá cho thành viên theo nhóm tháng
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function memberCouponUserGroupBymonth(array $where)
    {
        return $this->dao->memberCouponUserGroupBymonth($where);
    }

    /**Phiếu giảm giá thành viên hết hiệu lực
     * @param $coupon_user_id
     * @return bool|mixed
     */
    public function memberCouponIsFail($coupon_user)
    {
        if (!$coupon_user) return false;
        if ($coupon_user['use_time'] == 0) {
            return $this->dao->update($coupon_user['id'], ['is_fail' => 1, 'status' => 2]);
        }
    }

    /**Truy vấn phiếu giảm giá thành viên theo id
     * @param $id
     * @return array|bool|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCouponUserOne($id)
    {
        if (!$id) return false;
        return $this->dao->getOne(['id' => $id]);
    }

    /**
     * Truy vấn phiếu giảm giá người dùng theo thời gian
     * @param array $where
     * @return array|bool|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserCounponByMonth(array $where, string $field = '*')
    {
        if (!$where) return [];
        return $this->dao->getUserCounponByMonth($where, $field);
    }

    /**
     * Kiểm tra thành viên trả phí đã nhận phiếu giảm giá thành viên chưa
     * @param $uid
     * @param $vipCouponIds
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkHave($uid, $vipCouponIds)
    {
        $list = $this->dao->getVipCouponList($uid);
        $have = [];
        foreach ($list as $item) {
            if ($vipCouponIds && in_array($item['cid'], $vipCouponIds)) {
                $have[$item['cid']] = true;
            } else {
                $have[$item['cid']] = false;
            }
        }
        return $have;
    }

    public function autoReceiveCoupon($uid, $cartGroup)
    {
        $isMember = boolval(app()->make(UserServices::class)->value(['uid' => $uid], 'is_money_level'));
        $canReceiveCoupons = app()->make(StoreCouponIssueServices::class)->canReceiveCoupons($uid, $isMember);
        $cartInfo = $cartGroup['valid'];
        $canReceiveCoupon = [];
        if ($canReceiveCoupons) {
            foreach ($canReceiveCoupons as $canReceive) {
                $price = 0;
                $count = 0;
                switch ($canReceive['type']) {
                    case 0:
                    case 3:
                        foreach ($cartInfo as $cart) {
                            $price += bcmul((string)$cart['truePrice'], (string)$cart['cart_num'], 2);
                            $count++;
                        }
                        break;
                    case 1://Phiếu theo danh mục
                        /** @var StoreCategoryServices $storeCategoryServices */
                        $storeCategoryServices = app()->make(StoreCategoryServices::class);
                        $coupon_category = explode(',', (string)$canReceive['category_id']);
                        $category_ids = $storeCategoryServices->getAllById($coupon_category);
                        if ($category_ids) {
                            $cateIds = array_column($category_ids, 'id');
                            foreach ($cartInfo as $cart) {
                                if (isset($cart['productInfo']['cate_id']) && array_intersect(explode(',', $cart['productInfo']['cate_id']), $cateIds)) {
                                    $price += bcmul((string)$cart['truePrice'], (string)$cart['cart_num'], 2);
                                    $count++;
                                }
                            }
                        }
                        break;
                    case 2:
                        foreach ($cartInfo as $cart) {
                            if (isset($cart['product_id']) && in_array($cart['product_id'], explode(',', $canReceive['product_id']))) {
                                $price += bcmul((string)$cart['truePrice'], (string)$cart['cart_num'], 2);
                                $count++;
                            }
                        }
                        break;
                }
                if ($count && $canReceive['use_min_price'] <= $price && !count($canReceive['used'])) {
                    $canReceiveCoupon = $canReceive;
                    break;
                }
            }
        }
        if ($canReceiveCoupon) {
            $data = [];
            $issueData = [];
            /** @var StoreCouponIssueUserServices $storeCouponIssueUser */
            $storeCouponIssueUser = app()->make(StoreCouponIssueUserServices::class);
            $data['cid'] = $canReceiveCoupon['id'];
            $data['uid'] = $uid;
            $data['coupon_title'] = $canReceiveCoupon['title'];
            $data['coupon_price'] = $canReceiveCoupon['coupon_price'];
            $data['use_min_price'] = $canReceiveCoupon['use_min_price'];
            $data['add_time'] = time();
            if ($canReceiveCoupon['coupon_time']) {
                $data['start_time'] = $data['add_time'];
                $data['end_time'] = $data['add_time'] + $canReceiveCoupon['coupon_time'] * 86400;
            } else {
                $data['start_time'] = $canReceiveCoupon['start_use_time'];
                $data['end_time'] = $canReceiveCoupon['end_use_time'];
            }
            $data['type'] = 'get';
            $issueData['uid'] = $uid;
            $issueData['issue_coupon_id'] = $canReceiveCoupon['id'];
            $issueData['add_time'] = time();
            $this->dao->save($data);
            $storeCouponIssueUser->save($issueData);
        }
        return true;
    }
}
