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
use app\services\activity\lottery\LuckLotteryServices;
use app\services\activity\combination\StorePinkServices;
use app\services\BaseServices;
use app\services\pay\PayServices;
use crmeb\exceptions\ApiException;

/**
 * Class StoreOrderSuccessServices
 * @package app\services\order
 * @method getOne(array $where, ?string $field = '*', array $with = []) Lấy một dòng dữ liệu
 */
class StoreOrderSuccessServices extends BaseServices
{
    /**
     *
     * StoreOrderSuccessServices constructor.
     * @param StoreOrderDao $dao
     */
    public function __construct(StoreOrderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Thanh toán 0đ
     * @param array $orderInfo
     * @param int $uid
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function zeroYuanPayment(array $orderInfo, int $uid, string $payType = PayServices::YUE_PAY)
    {
        if ($orderInfo['paid']) {
            throw new ApiException(410265);
        }
        return $this->paySuccess($orderInfo, $payType);//Thanh toán bằng số dư thành công
    }

    /**
     * Thanh toán thành công
     * @param array $orderInfo
     * @param string $paytype
     * @param array $other
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function paySuccess(array $orderInfo, string $paytype = PayServices::WEIXIN_PAY, array $other = [])
    {
        $updata = ['paid' => 1, 'pay_type' => $paytype, 'pay_time' => time()];
        $orderInfo['pay_time'] = $updata['pay_time'];
        $orderInfo['pay_type'] = $paytype;
        if ($other && isset($other['trade_no'])) {
            $updata['trade_no'] = $other['trade_no'];
        }
        /** @var StoreOrderCartInfoServices $orderInfoServices */
        $orderInfoServices = app()->make(StoreOrderCartInfoServices::class);
        $orderInfo['storeName'] = $orderInfoServices->getCarIdByProductTitle((int)$orderInfo['id']);
        $res1 = $this->dao->update($orderInfo['id'], $updata);
        $resPink = true;
        if ($orderInfo['combination_id'] && $res1 && !$orderInfo['refund_status']) {
            /** @var StorePinkServices $pinkServices */
            $pinkServices = app()->make(StorePinkServices::class);
            /** @var StoreOrderServices $orderServices */
            $orderServices = app()->make(StoreOrderServices::class);
            $resPink = $pinkServices->createPink($orderServices->tidyOrder($orderInfo, true));//Tạo mua chung
        }
        //Lưu tạm số lượt quay thưởng, trừ thanh toán ngoại tuyến
        if (isset($orderInfo['pay_type']) && $orderInfo['pay_type'] != 'offline') {
            /** @var LuckLotteryServices $luckLotteryServices */
            $luckLotteryServices = app()->make(LuckLotteryServices::class);
            $luckLotteryServices->setCacheLotteryNum((int)$orderInfo['uid'], 'order');
        }
        $orderInfo['send_name'] = $orderInfo['real_name'];
        //Event sau khi đơn hàng thanh toán thành công
        event('OrderPaySuccessListener', [$orderInfo]);
        //Sự kiện đẩy tin nhắn cho người dùng
        event('NoticeListener', [$orderInfo, 'order_pay_success']);
        //Thanh toán thành công thì gửi tin nhắn cho CSKH
        event('NoticeListener', [$orderInfo, 'admin_pay_success_code']);
        // Đẩy đơn hàng
        event('OutPushListener', ['order_pay_push', ['order_id' => (int)$orderInfo['id']]]);

        //Tin nhắn tùy chỉnh - đơn hàng thanh toán thành công
        $orderInfo['time'] = date('Y-m-d H:i:s');
        $orderInfo['phone'] = $orderInfo['user_phone'];
        event('CustomNoticeListener', [$orderInfo['uid'], $orderInfo, 'order_pay_success']);

        //Sự kiện tùy chỉnh - thanh toán đơn hàng
        event('CustomEventListener', ['order_pay', [
            'uid' => $orderInfo['uid'],
            'id' => (int)$orderInfo['id'],
            'order_id' => $orderInfo['order_id'],
            'real_name' => $orderInfo['real_name'],
            'user_phone' => $orderInfo['user_phone'],
            'user_address' => $orderInfo['user_address'],
            'total_num' => $orderInfo['total_num'],
            'pay_price' => $orderInfo['pay_price'],
            'pay_postage' => $orderInfo['pay_postage'],
            'deduction_price' => $orderInfo['deduction_price'],
            'coupon_price' => $orderInfo['coupon_price'],
            'store_name' => $orderInfo['storeName'],
            'add_time' => date('Y-m-d H:i:s', $orderInfo['add_time']),
        ]]);

        $res = $res1 && $resPink;
        return false !== $res;
    }

}
