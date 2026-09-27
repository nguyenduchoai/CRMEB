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
namespace app\services\admin;

use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\activity\coupon\StoreCouponUserServices;
use app\services\BaseServices;
use app\services\order\StoreOrderCreateServices;
use app\services\order\StoreOrderRefundServices;
use app\services\order\StoreOrderServices;
use app\services\product\product\StoreCategoryServices;
use app\services\product\product\StoreProductLabelServices;
use app\services\product\product\StoreProductServices;
use app\services\product\sku\StoreProductAttrValueServices;
use app\services\system\SystemUserLevelServices;
use app\services\user\UserLabelRelationServices;
use app\services\user\UserBillServices;
use app\services\user\UserGroupServices;
use app\services\user\UserLabelCateServices;
use app\services\user\UserMoneyServices;
use app\services\user\UserRechargeServices;
use app\services\user\UserServices;
use app\services\user\UserVisitServices;
use crmeb\exceptions\ApiException;

class StoreManageServices extends BaseServices
{
    /**
     * Thống kê cửa hàng
     * @return array
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function statistics()
    {
        $storeOrderServices = app()->make(StoreOrderServices::class);
        $userVisitServices = app()->make(UserVisitServices::class);
        $storeOrderRefundServices = app()->make(StoreOrderRefundServices::class);
        $storeProductServices = app()->make(StoreProductServices::class);
        // Tổng tiền đơn hàng hôm nay, loại trừ đơn hàng người dùng đã hủy, đơn đã xóa, và phải là đơn hàng đã thanh toán
        $todayOrderPrice = $storeOrderServices->sum([
            ['add_time', '>', strtotime(date('Y-m-d'))],
            ['is_del', '=', 0],
            ['is_cancel', '=', 0],
            ['paid', '=', 1],
            ['pid', '>=', 0],
        ], 'pay_price', false);
        // Tổng số đơn hàng hôm nay, loại trừ đơn hàng người dùng đã hủy, đơn đã xóa
        $todayOrderCount = $storeOrderServices->count([
            ['add_time', '>', strtotime(date('Y-m-d'))],
            ['is_del', '=', 0],
            ['is_cancel', '=', 0],
            ['pid', '>=', 0],
        ]);
        // Số người thanh toán hôm nay, loại trừ đơn hàng người dùng đã hủy, đơn đã xóa, phải là đơn hàng đã thanh toán, loại bỏ trùng lặp
        $todayOrderUserCount = $storeOrderServices->getDistinctCount([
            ['add_time', '>', strtotime(date('Y-m-d'))],
            ['is_del', '=', 0],
            ['is_cancel', '=', 0],
            ['paid', '=', 1],
            ['pid', '<=', 0],
        ], 'uid', false);
        // Lượt xem hôm nay
        $todayVisitCount = $userVisitServices->count([['add_time', '>', strtotime(date('Y-m-d'))]]);
        // Số đơn hàng chờ giao hàng, toàn bộ thời gian
        $unDeliveryOrderCount = $storeOrderServices->count(['status' => 1, 'shipping_type' => 1, 'pid' => 0]);
        // Số yêu cầu hoàn tiền hôm nay, toàn bộ thời gian
        $refundingCount = $storeOrderRefundServices->count(['is_cancel' => 0, 'refund_type' => [1, 2, 4, 5]]);
        // Số sản phẩm đã hết hàng
        $outOfStock = $storeProductServices->getCount(['type' => 4]);
        // Số sản phẩm chạm ngưỡng cảnh báo tồn kho
        $policeForce = $storeProductServices->getCount(['type' => 5, 'store_stock' => sys_config('store_stock') > 0 ? sys_config('store_stock') : 2]);
        return compact('todayOrderCount', 'todayOrderPrice', 'todayOrderUserCount', 'todayVisitCount', 'unDeliveryOrderCount', 'refundingCount', 'outOfStock', 'policeForce');
    }

    /**
     * Danh sách sản phẩm
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function product($where)
    {
        $storeProductServices = app()->make(StoreProductServices::class);
        $where['is_del'] = 0;
        return $storeProductServices->getList($where);
    }

    /**
     * Đăng bán/ngừng bán sản phẩm
     * @param $id
     * @param $isShow
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function productShow($id, $isShow)
    {
        $storeProductServices = app()->make(StoreProductServices::class);
        $del = $storeProductServices->value(['id' => $id], 'is_del');
        if ($del == 1) throw new ApiException('Sản phẩm đã bị xóa');
        $storeProductServices->setShow([$id], $isShow);
        return true;
    }

    /**
     * Danh sách nhãn sản phẩm
     * @return array
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function productLabel()
    {
        $storeProductLabelServices = app()->make(StoreProductLabelServices::class);
        return $storeProductLabelServices->labelUseList();
    }

    /**
     * Lưu nhãn sản phẩm
     * @param $ids
     * @param $label_list
     * @return bool
     * @throws \Exception
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function saveProductLabel($ids, $label_list)
    {
        $data['ids'] = $ids;
        $data['label_list'] = $label_list;
        $data['type'] = 9;
        $storeProductServices = app()->make(StoreProductServices::class);
        $storeProductServices->batchSetting($data);
        return true;
    }

    /**
     * Danh sách danh mục sản phẩm
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function productCate()
    {
        $storeCategoryServices = app()->make(StoreCategoryServices::class);
        return $storeCategoryServices->cascaderList(1, 1);
    }

    /**
     * Lưu danh mục sản phẩm
     * @param $ids
     * @param $cate_id
     * @return bool
     * @throws \Exception
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function saveProductCate($ids, $cate_id)
    {
        $data['ids'] = $ids;
        $data['cate_id'] = $cate_id;
        $data['type'] = 1;
        $storeProductServices = app()->make(StoreProductServices::class);
        $storeProductServices->batchSetting($data);
        return true;
    }

    /**
     * Thuộc tính sản phẩm
     * @param $id
     * @return array
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function productAttr($id)
    {
        $storeProductAttrValueServices = app()->make(StoreProductAttrValueServices::class);
        return $storeProductAttrValueServices->selectList(['product_id' => $id, 'type' => 0])->toArray();
    }

    /**
     * Lưu thuộc tính sản phẩm
     * @param $id
     * @param $attr_value
     * @return bool|\think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/13
     */
    public function saveProductAttr($id, $attr_value)
    {
        $storeProductServices = app()->make(StoreProductServices::class);
        $storeProductAttrValueServices = app()->make(StoreProductAttrValueServices::class);

        if (!$id) {
            return app('json')->fail('Vui lòng chọn sản phẩm');
        }
        if (!$attr_value) {
            return app('json')->fail('Vui lòng nhập giá trị thuộc tính');
        }

        //Kiểm tra giá trị thuộc tính của quy cách có tồn tại không
        $requiredKeys = ['unique', 'price', 'stock', 'cost', 'ot_price'];
        foreach ($attr_value as $attr) {
            $missingKeys = array_diff($requiredKeys, array_keys($attr));
            if (!empty($missingKeys)) {
                throw new ApiException('Vui lòng sửa lại tồn kho của phân loại');
            }
        }

        $product_stock = $product_price = $product_ot_price = $product_cost = 0;
        $attrs = $storeProductAttrValueServices->selectList(['product_id' => $id, 'type' => 0])->toArray();
        $attr_value = array_combine(array_column($attr_value, 'unique'), $attr_value);
        foreach ($attrs as $item) {
            $attr = $attr_value[$item['unique']] ?? [];
            if ($attr) {
                $storeProductAttrValueServices->update($item['id'], [
                    'price' => $attr['price'],
                    'stock' => $attr['stock'],
                    'cost' => $attr['cost'],
                    'ot_price' => $attr['ot_price']
                ]);
            }

            $product_array = $attr ?: $item;
            // Tính tồn kho sản phẩm
            $product_stock = bcadd((string)$product_stock, (string)$product_array['stock'], 0);
            // Cập nhật giá sản phẩm
            $product_price = max($product_price, $product_array['price']);
            // Cập nhật giá gốc sản phẩm
            $product_ot_price = max($product_ot_price, $product_array['ot_price']);
            // Cập nhật giá vốn sản phẩm
            $product_cost = max($product_cost, $product_array['cost']);
        }
        // Sửa tồn kho và các thông tin khác của sản phẩm
        $storeProductServices->update($id, [
            'stock' => $product_stock,
            'price' => $product_price,
            'ot_price' => $product_ot_price,
            'cost' => $product_cost,
        ]);
        return true;
    }

    /**
     * Tạo sản phẩm
     * @param $data
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/9
     */
    public function createProduct($data)
    {
        $data['attr']['brokerage'] = 0;
        $data['attr']['brokerage_two'] = 0;
        $data['attr']['vip_price'] = 0;
        $data['attr']['virtual_list'] = [];
        $data['attr']['coupon_id'] = 0;
        $saveData = [
            'virtual_type' => 0,
            'cate_id' => $data['cate_id'],
            'store_name' => $data['store_name'],
            'keyword' => '',
            'unit_name' => $data['unit_name'],
            'store_info' => '',
            'slider_image' => $data['slider_image'],
            'video_open' => 0,
            'video_link' => '',
            'spec_type' => 0,
            'items' => [],
            'attrs' => [$data['attr']],
            'description' => $data['content'],
            'description_images' => [],
            'logistics' => $data['logistics'],
            'freight' => $data['freight'],
            'postage' => $data['postage'],
            'temp_id' => $data['temp_id'],
            'give_integral' => 0,
            'presale' => 0,
            'presale_time' => 0,
            'presale_day' => 1,
            'vip_product' => 0,
            'vip_product_type' => 0,
            'is_sub' => [],
            'recommend' => [],
            'activity' => ['Mặc định', 'Flash sale', 'Săn giảm giá', 'Mua chung'],
            'recommend_list' => [],
            'coupon_ids' => [],
            'label_id' => [],
            'command_word' => '',
            'is_show' => 0,
            'ficti' => 0,
            'sort' => 0,
            'recommend_image' => '',
            'sales' => 0,
            'custom_form' => [],
            'type' => 0,
            'is_copy' => 0,
            'is_limit' => 0,
            'limit_type' => 0,
            'limit_num' => 0,
            'min_qty' => 1,
            'params_list' => [],
            'label_list' => [],
            'protection_list' => [],
            'is_gift' => 0,
            'gift_price' => 0,
        ];
        $storeProductServices = app()->make(StoreProductServices::class);
        $storeProductServices->save(0, $saveData);
        return true;
    }

    /**
     * Danh sách người dùng
     * @param $where
     * @return array
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function user($where)
    {
        $userServices = app()->make(UserServices::class);
        return $userServices->index($where);
    }

    /**
     * Chi tiết thông tin người dùng
     * @param $uid
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function userInfo($uid)
    {
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->get($uid);
        if (!$userInfo) throw new ApiException('Người dùng không tồn tại');
        $userInfo = $userInfo->toArray();
        $userInfo['avatar'] = set_file_url($userInfo['avatar']);
        $userInfo['birthday'] = $userInfo['birthday'] != 0 ? date('Y-m-d', $userInfo['birthday']) : '';
        // Số lượng phiếu giảm giá
        $userInfo['coupon_num'] = app()->make(StoreCouponUserServices::class)->getUserValidCouponCount((int)$uid);
        // Nhãn người dùng
        $label_list = app()->make(UserLabelRelationServices::class)->getUserLabelList([$uid]);
        $label_id = [];
        $userInfo['label_list'] = '';
        if ($label_list) {
            $userInfo['label_list'] = implode(',', array_column($label_list, 'label_name'));
            foreach ($label_list as $item) {
                $label_id[] = [
                    'id' => $item['label_id'],
                    'label_name' => $item['label_name']
                ];
            }
        }
        $userInfo['label_id'] = $label_id;
        // Tổng tiền và số lượng đơn hàng của người dùng
        $orderServices = app()->make(StoreOrderServices::class);
        $userInfo['order_total_price'] = $orderServices->sum(['uid' => $uid, 'paid' => 1, 'refund_status' => 0], 'pay_price');
        $userInfo['order_total_count'] = $orderServices->count(['uid' => $uid, 'paid' => 1, 'refund_status' => 0]);
        // Thành viên
        $userInfo['isMember'] = $userInfo['is_money_level'] > 0 ? 1 : 0;
        if ($userInfo['is_ever_level'] == 1) {
            $userInfo['svip_overdue_time'] = $userInfo['svip_over_day'] = 'Vĩnh viễn';
        } else {
            if ($userInfo['is_money_level'] > 0 && $userInfo['overdue_time'] > 0) {
                $userInfo['svip_over_day'] = ceil(($userInfo['overdue_time'] - time()) / 86400);
                $userInfo['svip_overdue_time'] = date('Y-m-d', $userInfo['overdue_time']);
            }
        }
        return $userInfo;
    }

    /**
     * Nhóm người dùng
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function userGroup()
    {
        $userGroupServices = app()->make(UserGroupServices::class);
        return $userGroupServices->getGroupList();
    }

    /**
     * Hạng người dùng
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function userLevel()
    {
        $systemUserLevelServices = app()->make(SystemUserLevelServices::class);
        return $systemUserLevelServices->getLevelList([], 'id,name,icon,image');
    }

    /**
     * Nhãn người dùng
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function userLabel($uid)
    {
        $userLabelCateServices = app()->make(UserLabelCateServices::class);
        return $userLabelCateServices->getUserLabel($uid);
    }

    /**
     * Phiếu giảm giá của người dùng
     * @param $where
     * @return mixed
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function userCoupon($where)
    {
        if ($where['uid'] == 0) {
            $page = $where['page'] ?? 1;
            $limit = $where['limit'] ?? 10;
            unset($where['page'], $where['limit'], $where['uid']);
            $where['receive_types'] = 3;
            $where['is_del'] = 0;
            $where['status'] = 1;
            return app()->make(StoreCouponIssueServices::class)->getList($where, $page, $limit);
        } else {
            return app()->make(StoreCouponUserServices::class)->getUserCouponList($where['uid'])['list'];
        }
    }

    /**
     * Sửa dữ liệu người dùng
     * @param $uid
     * @param $data
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/11/17
     */
    public function userUpdate($uid, $data)
    {
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->getUserInfo($uid);
        switch ($data['type']) {
            case 0: // Số dư
                /** @var UserMoneyServices $userMoneyServices */
                $userMoneyServices = app()->make(UserMoneyServices::class);
                if ($data['status'] == 1) { //Tăng
                    $edit['now_money'] = bcadd($userInfo['now_money'], $data['number'], 2);
                    $userMoneyServices->income('system_add', $uid, $data['number'], $edit['now_money'], 0, 'Quản lý cửa hàng trên di động cộng số dư');
                    //Thêm bản ghi nạp tiền
                    $recharge_data = [
                        'order_id' => app()->make(StoreOrderCreateServices::class)->getNewOrderId('cz'),
                        'uid' => $uid,
                        'price' => $data['number'],
                        'recharge_type' => 'system',
                        'paid' => 1,
                        'add_time' => time(),
                        'give_price' => 0,
                        'channel_type' => 'system',
                        'pay_time' => time(),
                    ];
                    app()->make(UserRechargeServices::class)->save($recharge_data);
                } else { //Giảm
                    if ($userInfo['now_money'] > $data['number']) {
                        $edit['now_money'] = bcsub($userInfo['now_money'], $data['number'], 2);
                    } else {
                        $edit['now_money'] = 0;
                        $data['number'] = $userInfo['now_money'];
                    }
                    $userMoneyServices->income('system_sub', $uid, $data['number'], $edit['now_money'], 0, 'Quản lý cửa hàng trên di động trừ số dư');
                }
                $userServices->update($uid, $edit);
                break;
            case 1: // Điểm thưởng
                /** @var UserBillServices $userBill */
                $userBill = app()->make(UserBillServices::class);
                $integral_data = ['link_id' => 0, 'number' => $data['number']];
                if ($data['status'] == 1) { //Tăng
                    $edit['integral'] = bcadd($userInfo['integral'], $data['number'], 2);
                    $integral_data['balance'] = $edit['integral'];
                    $integral_data['title'] = 'Hệ thống cộng điểm thưởng';
                    $integral_data['mark'] = 'Hệ thống đã cộng' . floatval($data['number']) . 'Điểm thưởng';
                    $userBill->incomeIntegral($uid, 'system_add', $integral_data);
                } else { //Giảm
                    $edit['integral'] = bcsub($userInfo['integral'], $data['number'], 2);
                    $integral_data['balance'] = $edit['integral'];
                    $integral_data['title'] = 'Hệ thống trừ điểm thưởng';
                    $integral_data['mark'] = 'Hệ thống đã trừ' . floatval($data['number']) . 'Điểm thưởng';
                    $userBill->expendIntegral($uid, 'system_sub', $integral_data);
                }
                $userServices->update($uid, $edit);
                break;
            case 2: // Cấp bậc
                $userServices->saveGiveLevel((int)$uid, (int)$data['level']);
                break;
            case 3: // Thành viên trả phí
                $userServices->saveGiveLevelTime((int)$uid, (int)$data['days']);
                break;
            case 4: // Phiếu giảm giá
                /** @var StoreCouponIssueServices $issueService */
                $issueService = app()->make(StoreCouponIssueServices::class);
                $coupon = $issueService->get($data['coupon_id']);
                if (!$coupon) {
                    throw new ApiException('Phiếu giảm giá không tồn tại');
                } else {
                    $coupon = $coupon->toArray();
                }
                $issueService->setCoupon($coupon, [$uid]);
                break;
            case 5: // Nhóm
                $userServices->saveSetGroup([$uid], $data['group_id']);
                break;
            case 6: // Nhãn người dùng
                $userServices->saveSetLabel([$uid], $data['label_id'], 0);
                break;
        }
        return true;
    }
}
