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

namespace app\services\order;


use app\dao\order\StoreOrderDao;
use app\services\activity\bargain\StoreBargainServices;
use app\services\activity\combination\StoreCombinationServices;
use app\services\activity\combination\StorePinkServices;
use app\services\activity\seckill\StoreSeckillServices;
use app\services\BaseServices;
use app\services\user\member\MemberCardServices;
use app\services\user\UserBillServices;
use app\services\user\UserBrokerageServices;
use app\services\user\UserServices;
use crmeb\exceptions\ApiException;
use crmeb\utils\Str;
use think\facade\Log;

/**
 * Xác nhận đã nhận hàng
 * Class StoreOrderTakeServices
 * @package app\services\order
 * @method get(int $id, ?array $field = []) Lấy một dòng
 */
class StoreOrderTakeServices extends BaseServices
{
    /**
     * Phương thức khởi tạo
     * StoreOrderTakeServices constructor.
     * @param StoreOrderDao $dao
     */
    public function __construct(StoreOrderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Service đơn hàng Mini Program - nhận hàng
     * @param $merchant_trade_no
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     *
     * @date 2023/05/18
     * @author yyw
     */
    public function miniOrderTakeOrder($merchant_trade_no)
    {
        //Tìm thông tin đơn hàng
        $order = $this->dao->getOne(['order_id' => $merchant_trade_no]);
        if (!$order) {
            return true;
        }
        if ($order['pid'] == -1) {  // Có đơn hàng con
            // Tìm đơn hàng con đang chờ nhận hàng
            $son_order_list = $this->dao->getSubOrderNotSendList((int)$order['id']);
            foreach ($son_order_list as $son_order) {
                $this->takeOrder($son_order['order_id'], $son_order['uid']);
            }
        } else {
            $this->takeOrder($merchant_trade_no, $order['uid']);
        }

        return true;
    }

    /**
     * Người dùng xác nhận nhận hàng đơn hàng
     * @param $uni
     * @param $uid
     * @return bool
     */
    public function takeOrder(string $uni, int $uid)
    {
        $order = $this->dao->getUserOrderDetail($uni, $uid);
        if (!$order) {
            throw new ApiException(410173);
        }
        $refundServices = app()->make(StoreOrderRefundServices::class);
        $orderIsRefund = $refundServices->orderIsRefund((int)$order['id']);
        if($orderIsRefund){
            throw new ApiException('Đơn hàng đang hoàn tiền, không thể nhận hàng');
        }
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $order = $orderServices->tidyOrder($order);
        if ($order['_status']['_type'] != 2) {
            throw new ApiException(410266);
        }
        //Có giao hàng theo tách đơn thì cần nhận hàng riêng
        if ($this->dao->count(['pid' => $order['id']])) {
            throw new ApiException(410266);
        }
        $order->status = 2;
        $res = $order->save() && $this->storeProductOrderUserTakeDelivery($order);
        if (!$res) {
            throw new ApiException(410205);
        }
        return $order;
    }

    /**
     * Xác nhận đã nhận hàng cho đơn hàng
     * @param $order
     * @return bool
     */
    public function storeProductOrderUserTakeDelivery($order, bool $isTran = true)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->get((int)$order['uid']);
        //Lấy tên sản phẩm trong giỏ hàng
        /** @var StoreOrderCartInfoServices $orderInfoServices */
        $orderInfoServices = app()->make(StoreOrderCartInfoServices::class);
        $storeName = $orderInfoServices->getCarIdByProductTitle((int)$order['id']);
        $storeTitle = Str::substrUTf8($storeName, 20, 'UTF-8', '');

        $res = $this->transaction(function () use ($order, $userInfo, $storeTitle) {
            //Tặng điểm thưởng
            $res1 = $this->gainUserIntegral($order, $userInfo, $storeTitle);
            //Hoa hồng
            $res2 = $this->backOrderBrokerage($order, $userInfo);
            //điểm kinh nghiệm
            $res3 = $this->gainUserExp($order, $userInfo);
            //Đại lý khu vực
            $res4 = $this->divisionBrokerage($order, $userInfo);
            if (!($res1 && $res2 && $res3 && $res4)) {
                throw new ApiException(410205);
            }
            return true;
        }, $isTran);

        if ($res) {
            try {
                // Hàng đợi sau khi nhận hàng thành công
                event('OrderTakeListener', [$order, $userInfo, $storeTitle]);
                //Gửi tin nhắn cho người dùng khi nhận hàng
                event('NoticeListener', [['order' => $order, 'storeTitle' => $storeTitle], 'order_take']);
                //Nhận hàng thì gửi tin nhắn cho CSKH
                event('NoticeListener', [['order' => $order, 'storeTitle' => $storeTitle], 'send_admin_confirm_take_over']);
                //Tin nhắn tùy chỉnh - nhận hàng đơn hàng
                $order['storeTitle'] = $storeTitle;
                $order['time'] = date('Y-m-d H:i:s');
                $order['phone'] = $order['user_phone'];
                event('CustomNoticeListener', [$order['uid'], $order, 'order_take']);

                //Sự kiện tùy chỉnh - nhận hàng/xác nhận sử dụng đơn hàng
                event('CustomEventListener', ['order_take', [
                    'uid' => $order['uid'],
                    'id' => (int)$order['id'],
                    'order_id' => $order['order_id'],
                    'real_name' => $order['real_name'],
                    'user_phone' => $order['user_phone'],
                    'user_address' => $order['user_address'],
                    'total_num' => $order['total_num'],
                    'pay_price' => $order['pay_price'],
                    'pay_postage' => $order['pay_postage'],
                    'deduction_price' => $order['deduction_price'],
                    'coupon_price' => $order['coupon_price'],
                    'store_name' => $storeTitle,
                    'add_time' => date('Y-m-d H:i:s', $order['add_time']),
                ]]);


            } catch (\Throwable $exception) {

            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Tặng điểm thưởng
     * @param $order
     * @param $userInfo
     * @param $storeTitle
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function gainUserIntegral($order, $userInfo, $storeTitle)
    {
        $res1 = $res2 = $res3 = false;
        $integral = 0;
        if (!$userInfo) {
            return true;
        }
        // Sản phẩm marketing tặng điểm thưởng
        if (isset($order['combination_id']) && $order['combination_id']) {
            return true;
        }
        if (isset($order['seckill_id']) && $order['seckill_id']) {
            return true;
        }
        if (isset($order['bargain_id']) && $order['bargain_id']) {
            return true;
        }
        /** @var UserBillServices $userBillServices */
        $userBillServices = app()->make(UserBillServices::class);
        if ($order['gain_integral'] > 0) {
            $res2 = false != $userBillServices->income('pay_give_integral', $order['uid'], (int)$order['gain_integral'], $userInfo['integral'] + $order['gain_integral'], $order['id']);
            $integral = $userInfo['integral'] + $order['gain_integral'];
            $userInfo->integral = $integral;
            $res1 = false != $userInfo->save();
        } else {
            $res2 = true;
        }
        $order_integral = 0;

        $order_give_integral = sys_config('order_give_integral');
        if ($order['pay_price'] && $order_give_integral) {
            //Thành viên tiêu dùng được nhân đôi điểm thưởng hoàn lại
            if ($userInfo['is_money_level'] > 0) {
                //Kiểm tra có mở thưởng nhân đôi điểm hoàn khi chi tiêu hay không
                /** @var MemberCardServices $memberCardService */
                $memberCardService = app()->make(MemberCardServices::class);
                $integral_rule_number = $memberCardService->isOpenMemberCard('integral');
                if ($integral_rule_number) {
                    $order_integral = bcmul((string)$order['pay_price'], (string)$integral_rule_number, 2);
                }
            }
            $order_integral = bcmul((string)$order_give_integral, (string)($order_integral ?: $order['pay_price']), 0);
            $res3 = false != $userBillServices->income('order_give_integral', $order['uid'], $order_integral, $userInfo['integral'] + $order_integral, $order['id']);
            $integral = $userInfo['integral'] + $order_integral;
            $userInfo->integral = $integral;
            $res1 = false != $userInfo->save();
        }
        $give_integral = $order_integral + $order['gain_integral'];
        if ($give_integral > 0 && $res1 && $res2 && $res3) {
            /** @var StoreOrderServices $orderServices */
            $orderServices = app()->make(StoreOrderServices::class);
            $orderServices->update($order['id'], ['gain_integral' => $give_integral], 'id');
            event('NoticeListener', [['order' => $order, 'storeTitle' => $storeTitle, 'give_integral' => $give_integral, 'integral' => $integral], 'integral_accout']);

            //Tin nhắn tùy chỉnh - điểm thưởng vào tài khoản
            event('CustomNoticeListener', [$order['uid'], [
                'uid' => $order['uid'],
                'phone' => $userInfo['phone'],
                'storeTitle' => $storeTitle,
                'give_integral' => $give_integral,
                'integral' => $integral,
                'time' => date('Y-m-d H:i:s'),
            ], 'point_received']);

            //Sự kiện tùy chỉnh - điểm thưởng vào tài khoản
            event('CustomEventListener', ['order_point', [
                'uid' => $order['uid'],
                'order_id' => $order['order_id'],
                'phone' => $userInfo['phone'],
                'storeTitle' => $storeTitle,
                'give_integral' => $give_integral,
                'integral' => $integral,
                'add_time' => date('Y-m-d H:i:s'),
            ]]);

            return true;
        }
        return true;
    }

    /**
     * Trả hoa hồng đại lý khu vực
     * @param $orderInfo
     * @param $userInfo
     * @return bool
     */
    public function divisionBrokerage($orderInfo, $userInfo)
    {
        // Đơn hàng hiện tại | người dùng không tồn tại thì trả về ngay
        if (!$orderInfo || !$userInfo) {
            return true;
        }
        // Sản phẩm marketing không trả hoa hồng
        if (isset($orderInfo['combination_id']) && $orderInfo['combination_id']) {
            //Kiểm tra mua chung có tham gia trả hoa hồng không
            /** @var StoreCombinationServices $combinationServices */
            $combinationServices = app()->make(StoreCombinationServices::class);
            $isCommission = $combinationServices->value(['id' => $orderInfo['combination_id']], 'is_commission');
            if (!$isCommission) {
                return true;
            }
        }
        if (isset($orderInfo['seckill_id']) && $orderInfo['seckill_id']) {
            return true;
        }
        if (isset($orderInfo['bargain_id']) && $orderInfo['bargain_id']) {
            return true;
        }
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        if ($orderInfo['staff_id'] && $orderInfo['staff_brokerage'] > 0) {
            $spreadPrice = $userServices->value(['uid' => $orderInfo['staff_id']], 'brokerage_price');
            $balance = bcadd($spreadPrice, $orderInfo['staff_brokerage'], 2);
            $userServices->bcInc($orderInfo['staff_id'], 'brokerage_price', $orderInfo['staff_brokerage'], 'uid');
            //Thời gian đóng băng
            $broken_time = intval(sys_config('extract_time'));
            $frozen_time = time() + $broken_time * 86400;
            // Thêm bản ghi hoa hồng
            /** @var UserBrokerageServices $userBrokerageServices */
            $userBrokerageServices = app()->make(UserBrokerageServices::class);
            $userBrokerageServices->income('get_staff_brokerage', $orderInfo['staff_id'], [
                'nickname' => $userInfo['nickname'],
                'pay_price' => floatval($orderInfo['pay_price']),
                'number' => floatval($orderInfo['staff_brokerage']),
                'frozen_time' => $frozen_time
            ], $balance, $orderInfo['id']);
        }
        if ($orderInfo['agent_id'] && $orderInfo['agent_brokerage'] > 0) {
            $spreadPrice = $userServices->value(['uid' => $orderInfo['agent_id']], 'brokerage_price');
            $balance = bcadd($spreadPrice, $orderInfo['agent_brokerage'], 2);
            $userServices->bcInc($orderInfo['agent_id'], 'brokerage_price', $orderInfo['agent_brokerage'], 'uid');
            //Thời gian đóng băng
            $broken_time = intval(sys_config('extract_time'));
            $frozen_time = time() + $broken_time * 86400;
            // Thêm bản ghi hoa hồng
            /** @var UserBrokerageServices $userBrokerageServices */
            $userBrokerageServices = app()->make(UserBrokerageServices::class);
            $userBrokerageServices->income('get_agent_brokerage', $orderInfo['agent_id'], [
                'nickname' => $userInfo['nickname'],
                'pay_price' => floatval($orderInfo['pay_price']),
                'number' => floatval($orderInfo['agent_brokerage']),
                'frozen_time' => $frozen_time
            ], $balance, $orderInfo['id']);
        }
        if ($orderInfo['division_id'] && $orderInfo['division_brokerage'] > 0) {
            $spreadPrice = $userServices->value(['uid' => $orderInfo['division_id']], 'brokerage_price');
            $balance = bcadd($spreadPrice, $orderInfo['division_brokerage'], 2);
            $userServices->bcInc($orderInfo['division_id'], 'brokerage_price', $orderInfo['division_brokerage'], 'uid');
            //Thời gian đóng băng
            $broken_time = intval(sys_config('extract_time'));
            $frozen_time = time() + $broken_time * 86400;
            // Thêm bản ghi hoa hồng
            /** @var UserBrokerageServices $userBrokerageServices */
            $userBrokerageServices = app()->make(UserBrokerageServices::class);
            $userBrokerageServices->income('get_division_brokerage', $orderInfo['division_id'], [
                'nickname' => $userInfo['nickname'],
                'pay_price' => floatval($orderInfo['pay_price']),
                'number' => floatval($orderInfo['division_brokerage']),
                'frozen_time' => $frozen_time
            ], $balance, $orderInfo['id']);
        }
        return true;
    }

    /**
     * Hoa hồng cấp 1
     * @param $orderInfo
     * @param $userInfo
     * @return bool
     */
    public function backOrderBrokerage($orderInfo, $userInfo)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        // Đơn hàng hiện tại | người dùng không tồn tại thì trả về ngay
        if (!$orderInfo || !$userInfo) {
            return true;
        }
        //Chức năng phân phối của cửa hàng có mở không, 0 là tắt, 1 là mở
        if (!sys_config('brokerage_func_status')) return true;

        // Sản phẩm marketing không trả hoa hồng
        if (isset($orderInfo['combination_id']) && $orderInfo['combination_id']) {
            //Kiểm tra mua chung có tham gia trả hoa hồng không
            /** @var StoreCombinationServices $combinationServices */
            $combinationServices = app()->make(StoreCombinationServices::class);
            $combinationInfo = $combinationServices->getOne(['id' => $orderInfo['combination_id']], 'is_commission,head_commission');
            if ($combinationInfo['head_commission']) {
                /** @var StorePinkServices $pinkServices */
                $pinkServices = app()->make(StorePinkServices::class);
                $pinkMasterUid = $pinkServices->value(['id' => $orderInfo['pink_id']], 'uid');
                if ($orderInfo['uid'] == $pinkMasterUid && $userServices->checkUserPromoter($pinkMasterUid)) {
                    $pinkMasterPrice = bcmul((string)$orderInfo['pay_price'], bcdiv((string)$combinationInfo['head_commission'], 100, 2), 2);
                    $userServices->bcInc($pinkMasterUid, 'brokerage_price', $pinkMasterPrice, 'uid');
                    //Thời gian đóng băng
                    $broken_time = intval(sys_config('extract_time'));
                    $frozen_time = time() + $broken_time * 86400;
                    // Thêm bản ghi hoa hồng
                    /** @var UserBrokerageServices $userBrokerageServices */
                    $userBrokerageServices = app()->make(UserBrokerageServices::class);
                    //Trả hoa hồng trưởng nhóm mua chung
                    $userBrokerageServices->income('get_pink_master_brokerage', $pinkMasterUid, [
                        'number' => floatval($pinkMasterPrice),
                        'frozen_time' => $frozen_time
                    ], bcadd((string)$userInfo['brokerage_price'], $pinkMasterPrice, 2), $orderInfo['id']);
                }
            }
            if (!$combinationInfo['is_commission']) {
                return true;
            }
        }
        if (isset($orderInfo['seckill_id']) && $orderInfo['seckill_id']) {
            $seckill_commission = app()->make(StoreSeckillServices::class)->value(['id' => $orderInfo['seckill_id']], 'is_commission');
            if (!$seckill_commission) return true;
        }
        if (isset($orderInfo['bargain_id']) && $orderInfo['bargain_id']) {
            $bargain_commission = app()->make(StoreBargainServices::class)->value(['id' => $orderInfo['bargain_id']], 'is_commission');
            if (!$bargain_commission) return true;
        }
        //Liên kết hết hiệu lực
        if (isset($orderInfo['spread_uid']) && $orderInfo['spread_uid'] == -1) {
            return true;
        }
        //Có mở trả hoa hồng tự mua không
        $isSelfBrokerage = sys_config('is_self_brokerage', 0);
        if (!isset($orderInfo['spread_uid']) || !$orderInfo['spread_uid']) {//Tương thích trường hợp bảng đơn hàng trước đây không có spread_uid
            //Không mở trả hoa hồng tự mua, không có cấp trên, hoặc khi cấp trên chính là bản thân thì trả về ngay
            if (!$isSelfBrokerage && (!$userInfo['spread_uid'] || $userInfo['spread_uid'] == $orderInfo['uid'])) {
                return true;
            }
            $one_spread_uid = $isSelfBrokerage ? $userInfo['uid'] : $userInfo['spread_uid'];
        } else {
            $one_spread_uid = $orderInfo['spread_uid'];
        }
        //Kiểm tra có phải cộng tác viên không
        if (!$userServices->checkUserPromoter($one_spread_uid)) {
            return $this->backOrderBrokerageTwo($orderInfo, $userInfo, $isSelfBrokerage);
        }
        $brokeragePrice = $orderInfo['one_brokerage'] ?? 0;
        // Số tiền trả hoa hồng nhỏ hơn hoặc bằng 0 thì trả về ngay, không trả hoa hồng
        if ($brokeragePrice <= 0) {
            return true;
        }
        // Lấy thông tin người giới thiệu cấp trên
        $spreadPrice = $userServices->value(['uid' => $one_spread_uid], 'brokerage_price');
        // Số tiền sau khi trả hoa hồng cho người giới thiệu cấp trên
        $balance = bcadd($spreadPrice, $brokeragePrice, 2);
        // Thêm hoa hồng người dùng
        $res1 = $userServices->bcInc($one_spread_uid, 'brokerage_price', $brokeragePrice, 'uid');
        if ($res1) {
            //Thời gian đóng băng
            $frozen_time = time() + intval(sys_config('extract_time')) * 86400;
            // Thêm bản ghi hoa hồng
            /** @var UserBrokerageServices $userBrokerageServices */
            $userBrokerageServices = app()->make(UserBrokerageServices::class);
            //Trả hoa hồng tự mua || cấp trên
            $type = $one_spread_uid == $orderInfo['uid'] ? 'get_self_brokerage' : 'get_brokerage';
            $userBrokerageServices->income($type, $one_spread_uid, [
                'nickname' => $userInfo['nickname'],
                'pay_price' => floatval($orderInfo['pay_price']),
                'number' => floatval($brokeragePrice),
                'frozen_time' => $frozen_time
            ], $balance, $orderInfo['id']);

            //Gửi tin nhắn mẫu nhận hoa hồng cho cấp trên
            $this->sendBackOrderBrokerage($orderInfo, $one_spread_uid, $brokeragePrice);
        }
        // Trả hoa hồng cấp 1 thành công thì chuyển sang trả hoa hồng cấp 2
        $res = $res1 && $this->backOrderBrokerageTwo($orderInfo, $userInfo, $isSelfBrokerage, $frozen_time);
        return $res;
    }


    /**
     * Trả hoa hồng giới thiệu cấp 2
     * @param $orderInfo
     * @param $userInfo
     * @param $isSelfbrokerage
     * @param $frozenTime
     * @return bool
     */
    public function backOrderBrokerageTwo($orderInfo, $userInfo, $isSelfbrokerage = 0, $frozenTime = 0)
    {
        //Liên kết hết hiệu lực
        if (isset($orderInfo['spread_two_uid']) && $orderInfo['spread_two_uid'] == -1) {
            return true;
        }
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        if (isset($orderInfo['spread_two_uid']) && $orderInfo['spread_two_uid']) {
            $spread_two_uid = $orderInfo['spread_two_uid'];
        } else {
            // Lấy người giới thiệu cấp trên
            $userInfoTwo = $userServices->get((int)$userInfo['spread_uid']);
            // Đơn hàng | người giới thiệu cấp trên không tồn tại thì trả về ngay
            if (!$orderInfo || !$userInfoTwo) {
                return true;
            }
            //Không mở trả hoa hồng tự mua, hoặc người giới thiệu cấp trên không có cấp trên, hoặc khi cấp trên của cấp trên chính là bản thân thì trả về ngay
            if (!$isSelfbrokerage && (!$userInfoTwo['spread_uid'] || $userInfoTwo['spread_uid'] == $orderInfo['uid'])) {
                return true;
            }
            $spread_two_uid = $isSelfbrokerage ? $userInfoTwo['uid'] : $userInfoTwo['spread_uid'];
        }
        // Lấy loại phân phối ở trang quản trị, 1 là phân phối chỉ định, 2 là phân phối toàn dân
        if (!$userServices->checkUserPromoter($spread_two_uid)) {
            return true;
        }
        $brokeragePrice = $orderInfo['two_brokerage'] ?? 0;
        // Số tiền trả hoa hồng nhỏ hơn hoặc bằng 0 thì trả về ngay, không trả hoa hồng
        if ($brokeragePrice <= 0) {
            return true;
        }
        // Lấy thông tin người giới thiệu cấp trên của cấp trên
        $spreadPrice = $userServices->value(['uid' => $spread_two_uid], 'brokerage_price');
        // Lấy số dư sau khi trả hoa hồng của người giới thiệu cấp trên của cấp trên
        $balance = bcadd($spreadPrice, $brokeragePrice, 2);

        // Thêm bản ghi hoa hồng
        /** @var UserBrokerageServices $userBrokerageServices */
        $userBrokerageServices = app()->make(UserBrokerageServices::class);
        //Thời gian đóng băng
        $frozenTime = time() + intval(sys_config('extract_time')) * 86400;
        $res1 = $userBrokerageServices->income('get_two_brokerage', $spread_two_uid, [
            'nickname' => $userInfo['nickname'],
            'pay_price' => floatval($orderInfo['pay_price']),
            'number' => floatval($brokeragePrice),
            'frozen_time' => $frozenTime
        ], $balance, $orderInfo['id']);

        // Thêm số dư người dùng
        $res2 = $userServices->bcInc($spread_two_uid, 'brokerage_price', $brokeragePrice, 'uid');
        //Gửi tin nhắn mẫu nhận hoa hồng cho cấp trên
        $this->sendBackOrderBrokerage($orderInfo, $spread_two_uid, $brokeragePrice);
        return $res1 && $res2;
    }

    /**
     * Gửi tin nhắn mẫu khi hoa hồng vào tài khoản
     * @param $orderInfo
     * @param $spread_uid
     * @param $brokeragePrice
     */
    public function sendBackOrderBrokerage($orderInfo, $spread_uid, $brokeragePrice, string $type = 'order')
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userType = $userServices->value(['uid' => $spread_uid], 'user_type');
        $goodsPrice = 0;
        $goodsName = 'Nhận hoa hồng giới thiệu người dùng';
        if ($type == 'order') {
            /** @var StoreOrderCartInfoServices $storeOrderCartInfoService */
            $storeOrderCartInfoService = app()->make(StoreOrderCartInfoServices::class);
            $cartInfo = $storeOrderCartInfoService->getOrderCartInfo($orderInfo['id']);
            if ($cartInfo) {
                $cartInfo = array_column($cartInfo, 'cart_info');
                $goodsPrice = 0;
                $goodsName = "";
                foreach ($cartInfo as $k => $v) {
                    $goodsName .= $v['productInfo']['store_name'];
                    $goodsPrice += $v['productInfo']['price'];
                }
            }
        } else {
            $goodsName = 'Nhận hoa hồng giới thiệu người dùng';
            $goodsPrice = $brokeragePrice;
        }
        //Đẩy thông báo nhắc nhở
        event('NoticeListener', [['spread_uid' => $spread_uid, 'userType' => $userType, 'brokeragePrice' => $brokeragePrice, 'goodsName' => $goodsName, 'goodsPrice' => $goodsPrice, 'add_time' => $orderInfo['add_time'] ?? time()], 'order_brokerage']);

        $spreadPhone = app()->make(UserServices::class)->value($spread_uid, 'phone');

        //Tin nhắn tùy chỉnh - hoa hồng vào tài khoản
        event('CustomNoticeListener', [$spread_uid, [
            'uid' => $spread_uid,
            'phone' => $spreadPhone,
            'brokeragePrice' => $brokeragePrice,
            'goodsName' => $goodsName,
            'goodsPrice' => $goodsPrice,
            'time' => date('Y-m-d H:i:s')
        ], 'brokerage_received']);

        //Sự kiện tùy chỉnh - hoa hồng vào tài khoản
        event('CustomEventListener', ['order_brokerage', [
            'uid' => $spread_uid,
            'order_id' => $orderInfo['order_id'] ?? '',
            'phone' => $spreadPhone,
            'brokeragePrice' => $brokeragePrice,
            'goodsName' => $goodsName,
            'goodsPrice' => $goodsPrice,
            'add_time' => date('Y-m-d H:i:s')
        ]]);
    }


    /**
     * Tặng điểm kinh nghiệm
     * @param $order
     * @param $userInfo
     * @return bool
     */
    public function gainUserExp($order, $userInfo)
    {
        if (!$userInfo) {
            return true;
        }
        //Hạng người dùng có mở không
        if (!sys_config('member_func_status', 1)) {
            return true;
        }
        /** @var UserBillServices $userBillServices */
        $userBillServices = app()->make(UserBillServices::class);
        $order_exp = 0;
        $res3 = true;
        $order_give_exp = sys_config('order_give_exp');
        if ($order['pay_price'] && $order_give_exp) {
            $order_exp = bcmul($order_give_exp, (string)$order['pay_price'], 2);
            $res3 = false != $userBillServices->income('order_give_exp', $order['uid'], $order_exp, bcadd((string)$userInfo['exp'], (string)$order_exp, 2), $order['id']);
        }
        $res = true;
        if ($order_exp > 0) {
            $exp = $userInfo['exp'] + $order_exp;
            $userInfo->exp = $exp;
            $res1 = false != $userInfo->save();
            $res = $res1 && $res3;
        }

        //Event nâng cấp người dùng
        event('UserLevelListener', [$order['uid']]);

        return $res;
    }

    /**
     * Tự động nhận hàng
     * @return bool
     */
    public function autoTakeOrder()
    {
        //Timestamp 7 ngày trước
        $systemDeliveryTime = sys_config('system_delivery_time', 0);
        //0 là hủy chức năng tự động nhận hàng
        if ($systemDeliveryTime == 0) {
            return true;
        }
        $sevenDay = bcsub((string)time(), bcmul((string)$systemDeliveryTime, '86400'));
        /** @var StoreOrderStoreOrderStatusServices $service */
        $service = app()->make(StoreOrderStoreOrderStatusServices::class);
        $orderList = $service->getTakeOrderIds([
            'change_time' => $sevenDay,
            'is_del' => 0,
            'paid' => 1,
            'status' => 1,
            'change_type' => ['delivery_goods', 'delivery_fictitious', 'delivery']
        ]);
        foreach ($orderList as $order) {
            if ($order['status'] == 2) {
                continue;
            }
            if ($order['paid'] == 1 && $order['status'] == 1) {
                $data['status'] = 2;
            } else if ($order['pay_type'] == 'offline') {
                $data['status'] = 2;
            } else {
                continue;
            }
            try {
                $this->transaction(function () use ($order, $data) {
                    /** @var StoreOrderStatusServices $statusService */
                    $statusService = app()->make(StoreOrderStatusServices::class);
                    $res = $this->dao->update($order['id'], $data) && $statusService->save([
                            'oid' => $order['id'],
                            'change_type' => 'take_delivery',
                            'change_message' => 'Đã nhận hàng [Tự động nhận hàng]',
                            'change_time' => time()
                        ]);
                    $res = $res && $this->storeProductOrderUserTakeDelivery($order, false);
                    if (!$res) {
                        Log::error('Mã đơn hàng' . $order['order_id'] . 'tự động nhận hàng thất bại');
                    }
                });
            } catch (\Throwable $e) {
                Log::error('Tự động nhận hàng thất bại, lý do:' . $e->getMessage() . '|' . $e->getFile() . '|' . $e->getLine());
            }

        }
    }

    /**
     * Kiểm tra đơn hàng chính có cần cập nhật trạng thái không
     * @param $pid
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkMaster($pid)
    {
        $p_order = $this->dao->get((int)$pid, ['id,pid,status']);
        //Đơn hàng chính đã giao hàng toàn bộ, và đơn hàng con không có chờ nhận hàng nhưng có chờ đánh giá
        if ($p_order['status'] == 1 && !$this->dao->count(['pid' => $pid, 'status' => 2]) && $this->dao->count(['pid' => $pid, 'status' => 3])) {
            $this->dao->update($p_order['id'], ['status' => 2]);
            /** @var StoreOrderStatusServices $statusService */
            $statusService = app()->make(StoreOrderStatusServices::class);
            $statusService->save([
                'oid' => $p_order['id'],
                'change_type' => 'take_delivery',
                'change_message' => 'Đã nhận hàng',
                'change_time' => time()
            ]);
        }
    }
}
