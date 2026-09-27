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

use app\services\BaseServices;
use app\services\order\OtherOrderServices;
use app\services\order\StoreOrderSuccessServices;
use app\services\user\UserMoneyServices;
use app\services\user\UserServices;
use crmeb\exceptions\ApiException;

/**
 * Thanh toán bằng số dư
 * Class YuePayServices
 * @package app\services\pay
 */
class YuePayServices extends BaseServices
{

    /**
     * Đơn hàng thanh toán bằng số dư
     * @param $order_id
     * @param $uid
     * @return bool
     */
    public function yueOrderPay(array $orderInfo, $uid)
    {
        if (!$orderInfo) {
            throw new ApiException(410173);
        }
        if ($orderInfo['paid']) {
            throw new ApiException(410174);
        }
        $type = 'pay_product';
        if (isset($orderInfo['member_type'])) {
            $type = 'pay_member';
        }
        /** @var UserServices $services */
        $services = app()->make(UserServices::class);
        $userInfo = $services->getUserInfo($uid);
        if ($userInfo['now_money'] < $orderInfo['pay_price']) {
            return ['status' => 'pay_deficiency', 'msg' => 'Số dư không đủ' . floatval($orderInfo['pay_price'])];
        }
        $this->transaction(function () use ($services, $orderInfo, $userInfo, $type) {
            $res = false !== $services->bcDec($userInfo['uid'], 'now_money', $orderInfo['pay_price'], 'uid');
            /** @var UserMoneyServices $userMoneyServices */
            $userMoneyServices = app()->make(UserMoneyServices::class);
            //Ghi bản ghi số dư
            $now_money = bcsub((string)$userInfo['now_money'], (string)$orderInfo['pay_price'], 2);
            $number = $orderInfo['pay_price'];
            switch ($type) {
                case 'pay_product'://Thanh toán sản phẩm bằng số dư
                    $res = $res && $userMoneyServices->income('pay_product', $userInfo['uid'], $number, $now_money, $orderInfo['id']);
                    /** @var StoreOrderSuccessServices $orderServices */
                    $orderServices = app()->make(StoreOrderSuccessServices::class);
                    $res = $res && $orderServices->paySuccess($orderInfo, PayServices::YUE_PAY);//Thanh toán bằng số dư thành công
                    break;
                case 'pay_member'://Thanh toán bằng thẻ thành viên
                    $res = $res && $userMoneyServices->income('pay_member', $userInfo['uid'], $number, $now_money, $orderInfo['id']);
                    /** @var OtherOrderServices $OtherOrderServices */
                    $OtherOrderServices = app()->make(OtherOrderServices::class);
                    $res = $res && $OtherOrderServices->paySuccess($orderInfo, PayServices::YUE_PAY);//Thanh toán bằng số dư thành công
                    break;
            }
            if (!$res) {
                throw new ApiException(410279);
            }
        });
        return ['status' => true];
    }
}
