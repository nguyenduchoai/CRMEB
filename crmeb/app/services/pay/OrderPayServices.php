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
use app\services\order\StoreOrderCartInfoServices;
use app\services\order\StoreOrderServices;
use app\services\wechat\WechatUserServices;
use crmeb\exceptions\ApiException;
use crmeb\services\CacheService;
use crmeb\services\pay\extend\allinpay\AllinPay;
use crmeb\utils\Str;
use think\exception\ValidateException;

/**
 * Đơn hàng khởi tạo thanh toán
 * Class OrderPayServices
 * @package app\services\pay
 */
class OrderPayServices
{
    /**
     * Thanh toán
     * @var PayServices
     */
    protected $payServices;

    public function __construct(PayServices $services)
    {
        $this->payServices = $services;
    }

    /**
     * Lấy phương thức thanh toán
     * @param string $payType
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/2/15
     */
    public function getPayType(string $payType)
    {
        //WeChat Pay chưa mở, Allinpay đã mở, khi người dùng truy cập ở Mini Program hoặc OA WeChat thì dùng WeChat H5 Pay của Allinpay
        if ($payType == PayServices::WEIXIN_PAY && !request()->isH5() && !request()->isApp()) {
            $payType = sys_config('pay_weixin_open', 0);
        }

        //Alipay chưa mở, Allinpay đã mở, khi người dùng thanh toán bằng Alipay và truy cập ở app thì dùng Alipay app của Allinpay
        if ($payType == PayServices::ALIAPY_PAY && request()->isApp()) {
            $payType = sys_config('ali_pay_status', 0);
        }

        return $payType;
    }

    /**
     * Lấy loại trả về
     * @param string $payType
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/2/15
     */
    public function payStatus(string $payType)
    {
        if ($payType == PayServices::WEIXIN_PAY) {
            if (request()->isH5()) {
                $payStstus = 'wechat_h5_pay';
            } else if (request()->isPc()) {
                $payStstus = 'wechat_pc_pay';
            } else {
                $payStstus = 'wechat_pay';
            }
        } else if ($payType == PayServices::ALIAPY_PAY) {
            $payStstus = 'alipay_pay';
        } else if ($payType == PayServices::ALLIN_PAY) {
            $payStstus = 'allinpay_pay';
        } else {
            throw new ValidateException('Lấy loại dữ liệu trả về của thanh toán thất bại');
        }
        return $payStstus;
    }

    /**
     * Trước khi khởi tạo thanh toán
     * @param array $orderInfo
     * @param string $payType
     * @param array $options
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/2/15
     */
    public function beforePay(array $orderInfo, string $payType, array $options = [])
    {
        $wechat = $payType == PayServices::WEIXIN_PAY;

        $payType = $this->getPayType($payType);

        if ($orderInfo['paid']) {
            throw new ApiException(410174);
        }
        if ($orderInfo['pay_price'] <= 0) {
            throw new ApiException(410274);
        }

        switch ($payType) {
            case PayServices::WEIXIN_PAY:
                $openid = '';
                if (request()->isWechat() || request()->isRoutine()) {
                    if (request()->isWechat()) {
                        $userType = 'wechat';
                    } else {
                        $userType = 'routine';
                    }
                    /** @var WechatUserServices $services */
                    $services = app()->make(WechatUserServices::class);
                    $openid = $services->uidToOpenid($orderInfo['pay_uid'] ?? $orderInfo['uid'], $userType);
                    if (!$openid) {
                        throw new ApiException(410275);
                    }
                }
                $options['openid'] = $openid;
                break;
            case PayServices::ALLIN_PAY:
                if ($wechat) {
                    $options['wechat'] = $wechat;
                }
                break;
            case PayServices::ALIAPY_PAY:
                if ($wechat) {
                    $options['returnUrl'] = sys_config('site_url') . '/pages/goods/order_pay_status/index?order_id=' . $orderInfo['order_id'];
                }
                break;
        }


        $site_name = sys_config('site_name');
        if (isset($orderInfo['member_type'])) {
            $body = Str::substrUTf8($site_name . '--' . $orderInfo['member_type'], 20);
            $successAction = "member";
            /** @var OtherOrderServices $otherOrderServices */
            $otherOrderServices = app()->make(OtherOrderServices::class);
            $otherOrderServices->update($orderInfo['id'], ['pay_type' => $payType]);
        } else {
            /** @var StoreOrderCartInfoServices $orderInfoServices */
            $orderInfoServices = app()->make(StoreOrderCartInfoServices::class);
            $body = $orderInfoServices->getCarIdByProductTitle((int)$orderInfo['id']);
            $body = Str::substrUTf8($site_name . '--' . $body, 20);
            $successAction = "product";
            /** @var StoreOrderServices $orderServices */
            $orderServices = app()->make(StoreOrderServices::class);
            $orderServices->update($orderInfo['id'], ['pay_type' => $payType]);
        }

        if (!$body) {
            throw new ApiException(410276);
        }

        //Khởi tạo thanh toán
        $jsConfig = $this->payServices->pay($payType, $orderInfo['order_id'], $orderInfo['pay_price'], $successAction, $body, $options);

        //Xử lý tham số trả về sau khi khởi tạo thanh toán
        $payInfo = $this->afterPay($orderInfo, $jsConfig, $payType);
        $statusType = $this->payStatus($payType);

        return [
            'status' => $statusType,
            'payInfo' => $payInfo,
        ];
    }

    /**
     * Xử lý tham số trả về sau khi thanh toán được khởi tạo
     * @param $order
     * @param $jsConfig
     * @param string $payType
     * @return array
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/2/15
     */
    public function afterPay($order, $jsConfig, string $payType)
    {
        $payKey = md5($order['order_id']);
        switch ($payType) {
            case PayServices::ALIAPY_PAY:
                if (request()->isPc()) $jsConfig->invalid = time() + 60;
                CacheService::set($payKey, ['order_id' => $order['order_id'], 'other_pay_type' => false], 300);
                break;
            case PayServices::ALLIN_PAY:
                if (request()->isWechat()) {
                    $payUrl = AllinPay::UNITODER_H5UNIONPAY;
                }
                break;
            case PayServices::WEIXIN_PAY:
                if (isset($jsConfig['mweb_url'])) {
                    $jsConfig['h5_url'] = $jsConfig['mweb_url'];
                }
        }

        return ['jsConfig' => $jsConfig, 'oid' => $order['id'], 'order_id' => $order['order_id'], 'pay_key' => $payKey, 'pay_url' => $payUrl ?? ''];
    }
}
