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

namespace app\services\pay;


use app\model\user\UserRecharge;
use app\services\wechat\WechatUserServices;
use crmeb\exceptions\ApiException;
use crmeb\services\pay\extend\allinpay\AllinPay;

/**
 *
 * Class RechargeServices
 * @package app\services\pay
 */
class RechargeServices
{
    protected $pay;

    /**
     * RechargeServices constructor.
     * @param PayServices $pay
     */
    public function __construct(PayServices $pay)
    {
        $this->pay = $pay;
    }

    public function recharge(UserRecharge $recharge)
    {
        if (!$recharge) {
            throw new ApiException(410173);
        }
        if ($recharge['paid'] == 1) {
            throw new ApiException(410174);
        }
        $payType = '';
        switch ($recharge['recharge_type']) {
            case 'weixin':
            case 'weixinh5':
            case 'routine':
                $payType = PayServices::WEIXIN_PAY;
                break;
            case PayServices::ALIAPY_PAY:
                $payType = PayServices::ALIAPY_PAY;
                break;
        }

        $payType = app()->make(OrderPayServices::class)->getPayType($payType);

        if (!$payType) {
            throw new ApiException(410278);
        }

        if ($recharge['recharge_type'] == PayServices::WEIXIN_PAY && !request()->isH5() && !request()->isApp()) {

            /** @var WechatUserServices $wechatUser */
            $wechatUser = app()->make(WechatUserServices::class);
            if (request()->isApp()) {
                $userType = 'app';
            } else if (request()->isRoutine()) {
                $userType = 'routine';
            } else if (request()->isWechat()) {
                $userType = 'wechat';
            } else {
                throw new ApiException(410275);
            }

            $openid = $wechatUser->uidToOpenid((int)$recharge['uid'], $userType);

            if (!$openid) {
                throw new ApiException(410275);
            }
        } else {
            $openid = '';
        }

        $res = $this->pay->pay($payType, $recharge['order_id'], $recharge['price'], 'user_recharge', 'Nạp tiền người dùng', ['openid' => $openid]);

        if ($payType == PayServices::WEIXIN_PAY) {
            if (request()->isH5()) {
                $payStstus = 'wechat_h5_pay';
            } else {
                $payStstus = 'wechat_pay';
            }
        } else if ($payType == PayServices::ALIAPY_PAY) {
            $payStstus = 'alipay_pay';
        } else if ($payType == PayServices::ALLIN_PAY) {
            $payStstus = 'allinpay_pay';
        }

        return ['pay_url' => AllinPay::UNITODER_H5UNIONPAY, 'jsConfig' => $res, 'pay_key' => md5($recharge['order_id']), 'order_id' => $recharge['order_id'], 'pay_type' => strtoupper($payStstus)];
    }

}
