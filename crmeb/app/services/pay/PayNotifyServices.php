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

namespace app\services\pay;

use app\services\order\OtherOrderServices;
use app\services\order\StoreOrderSuccessServices;
use app\services\user\UserRechargeServices;

/**
 * Callback thanh toán thành công, tất cả callback thông báo bất đồng bộ đều đi qua ba phương thức dưới đây, không còn phân biệt callback WeChat Pay/Alipay
 * Class PayNotifyServices
 * @package app\services\pay
 */
class PayNotifyServices
{

    /**
     * Sau khi đơn hàng thanh toán thành công
     * @param string|null $order_id ID đơn hàng
     * @param string|null $trade_no
     * @param string $payType
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function wechatProduct(string $order_id = null, string $trade_no = null, string $payType = PayServices::WEIXIN_PAY)
    {
        try {
            /** @var StoreOrderSuccessServices $services */
            $services = app()->make(StoreOrderSuccessServices::class);
            $orderInfo = $services->getOne(['order_id' => $order_id]);
            if (!$orderInfo) return true;
            if ($orderInfo->paid) return true;
            return $services->paySuccess($orderInfo->toArray(), $payType, ['trade_no' => $trade_no]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sau khi nạp tiền thành công
     * @param string|null $order_id ID đơn hàng
     * @return bool
     */
    public function wechatUserRecharge(string $order_id = null, string $trade_no = null, string $payType = PayServices::WEIXIN_PAY)
    {
        try {
            /** @var UserRechargeServices $userRecharge */
            $userRecharge = app()->make(UserRechargeServices::class);
            if ($userRecharge->be(['order_id' => $order_id, 'paid' => 1])) return true;
            return $userRecharge->rechargeSuccess($order_id, ['trade_no' => $trade_no, 'pay_type' => $payType]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Mua gói thành viên
     * @param string|null $order_id
     * @return bool
     */
    public function wechatMember(string $order_id = null, string $trade_no = null, string $payType = PayServices::WEIXIN_PAY)
    {
        try {
            /** @var OtherOrderServices $services */
            $services = app()->make(OtherOrderServices::class);
            $orderInfo = $services->getOne(['order_id' => $order_id]);
            if (!$orderInfo) return true;
            if ($orderInfo->paid) return true;
            return $services->paySuccess($orderInfo->toArray(), $payType, ['trade_no' => $trade_no]);
        } catch (\Exception $e) {
            return false;
        }
    }

}
