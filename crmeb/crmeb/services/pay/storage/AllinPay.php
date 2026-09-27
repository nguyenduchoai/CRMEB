<?php
/**
 *  +----------------------------------------------------------------------
 *  | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
 *  +----------------------------------------------------------------------
 *  | Author: CRMEB Team <admin@crmeb.com>
 *  +----------------------------------------------------------------------
 */

namespace crmeb\services\pay\storage;

use app\services\pay\PayServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\PayException;
use crmeb\services\pay\BasePay;
use crmeb\services\pay\PayInterface;
use crmeb\services\pay\extend\allinpay\AllinPay as AllinPayService;
use EasyWeChat\Payment\Order;
use think\facade\Event;

/**
 * Allinpay
 * Class AllinPay
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/2/1
 * @package crmeb\services\pay\storage
 */
class AllinPay extends BasePay implements PayInterface
{

    /**
     * @var AllinPayService
     */
    protected $pay;

    /**
     * @param array $config
     * @return mixed|void
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/1/15
     */
    protected function initialize(array $config)
    {
        $this->pay = new AllinPayService([
            'appid' => sys_config('allin_appid'),
            'cusid' => sys_config('allin_cusid'),
            'privateKey' => sys_config('allin_private_key'),
            'publicKey' => sys_config('allin_public_key'),
            'notifyUrl' => trim(sys_config('site_url')) . '/api/pay/notify/allin',
            'isBeta' => false,
        ]);
    }

    /**
     * Tạo thanh toán
     * @param string $orderId
     * @param string $totalFee
     * @param string $attach
     * @param string $body
     * @param string $detail
     * @param array $options
     * @return array|mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/1/15
     */
    public function create(string $orderId, string $totalFee, string $attach, string $body, string $detail, array $options = [])
    {
        $this->authSetPayType();

        $options['returl'] = sys_config('site_url') . '/pages/index/index';
        if ($options['returl']) {
            $options['returl'] = str_replace('http://', 'https://', $options['returl']);
        }
        $options['appid'] = sys_config('routine_appId');


        $notifyUrl = trim(sys_config('site_url')) . '/api/pay/notify/allin' . $attach;
        $this->pay->setNotifyUrl($notifyUrl);

        switch ($this->payType) {
            case Order::APP:
                return $this->pay->appPay($totalFee, $orderId, $body, '', '', false, $attach);
            case Order::JSAPI:
                if (request()->isRoutine()) {
                    return $this->pay->miniproPay($totalFee, $orderId, $body, $attach);
                } else {
                    return $this->pay->h5Pay($totalFee, $orderId, $body, $options['returl'] ?? '', $attach);
                }
            case Order::NATIVE:
                return $this->pay->pcPay($totalFee, $orderId, $body, $attach, !empty($options['wechat']));
            default:
                throw new PayException('Allinpay: loại thanh toán không hợp lệ hoặc chưa hỗ trợ thanh toán trong môi trường này');
        }
    }

    public function merchantPay(string $openid, string $orderId, string $amount, array $options = [])
    {
        throw new PayException('Allinpay: chưa hỗ trợ chuyển khoản từ người bán');
    }

    /**
     * Khởi tạo hoàn tiền
     * @param string $outTradeNo
     * @param array $options
     * @return array|mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/1/15
     */
    public function refund(string $outTradeNo, array $options = [])
    {
        $result = $this->pay->refund($options['refund_price'], $options['order_id'], $outTradeNo);
        if ($result['retcode'] != 'SUCCESS') throw new AdminException($result['retmsg']);
    }

    public function queryRefund(string $outTradeNo, string $outRequestNo, array $other = [])
    {
        // TODO: Implement queryRefund() method.
    }

    /**
     * Callback bất đồng bộ
     * @return mixed|string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/1/15
     */
    public function handleNotify(string $attach = '')
    {
        $attach = str_replace('allin', '', $attach);

        return $this->pay->handleNotify(function ($notify) use ($attach) {

            if (isset($notify['cusorderid'])) {

                $data = [
                    'attach' => $attach,
                    'out_trade_no' => $notify['cusorderid'],
                    'transaction_id' => $notify['trxid']
                ];

                return Event::until('NotifyListener', [$data, PayServices::ALLIN_PAY]);
            }
            return false;
        });
    }
}
