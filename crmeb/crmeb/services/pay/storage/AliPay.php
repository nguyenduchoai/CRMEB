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

namespace crmeb\services\pay\storage;


use Alipay\EasySDK\Payment\Common\Models\AlipayTradeFastpayRefundQueryResponse;
use Alipay\EasySDK\Payment\Common\Models\AlipayTradeRefundResponse;
use Alipay\EasySDK\Payment\Wap\Models\AlipayTradeWapPayResponse;
use crmeb\services\pay\BasePay;
use crmeb\services\pay\PayInterface;
use crmeb\services\AliPayService;

/**
 * Thanh toán Alipay
 * Class AliPay
 * @package crmeb\services\pay\storage
 */
class AliPay extends BasePay implements PayInterface
{

    protected function initialize(array $config)
    {
        // TODO: Implement initialize() method.
    }

    /**
     * Tạo đơn hàng và khởi tạo thanh toán
     * @param string $orderId
     * @param string $totalFee
     * @param string $attach
     * @param string $body
     * @param string $detail
     * @param string|null $tradeType
     * @param array $options
     * @return AlipayTradeWapPayResponse|mixed
     */
    public function create(string $orderId, string $totalFee, string $attach, string $body, string $detail, array $options = [])
    {
        $code = false;
        if (request()->isPC() || request()->isRoutine()) {
            $code = true;
        }

        return AliPayService::instance()->create($body, $orderId, $totalFee, $attach, $options['quitUrl'] ?? '', $options['returnUrl'] ?? '', $code);
    }

    /**
     * Doanh nghiệp trả tiền vào số dư
     * @param string $openid
     * @param string $orderId
     * @param string $amount
     * @param array $options
     * @return bool|mixed
     */
    public function merchantPay(string $openid, string $orderId, string $amount, array $options = [])
    {
        return false;
    }

    /**
     * Hoàn tiền
     * @param string $outTradeNo
     * @param string $totalAmount
     * @param string $refund_id
     * @param array $options
     * @return AlipayTradeRefundResponse|mixed
     */
    public function refund(string $outTradeNo, array $options = [])
    {
        return AliPayService::instance()->refund($outTradeNo, $options['totalAmount'], $options['refund_id']);
    }

    /**
     * Truy vấn hoàn tiền
     * @param string $outTradeNo
     * @param string $outRequestNo
     * @param array $other
     * @return AlipayTradeFastpayRefundQueryResponse|mixed
     */
    public function queryRefund(string $outTradeNo, string $outRequestNo, array $other = [])
    {
        return AliPayService::instance()->queryRefund($outTradeNo, $outRequestNo);
    }

    /**
     * Callback thanh toán bất đồng bộ
     * @return mixed|string
     */
    public function handleNotify()
    {
        return AliPayService::handleNotify();
    }
}
