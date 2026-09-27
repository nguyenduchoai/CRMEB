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

namespace crmeb\services\pay\storage;


use crmeb\exceptions\AdminException;
use crmeb\services\pay\BasePay;
use crmeb\exceptions\PayException;
use crmeb\services\pay\PayInterface;
use crmeb\services\app\MiniProgramService;
use crmeb\services\app\WechatService;
use crmeb\services\SystemConfigService;
use EasyWeChat\Payment\API;
use EasyWeChat\Payment\Order;
use EasyWeChat\Support\Collection;
use Psr\Http\Message\ResponseInterface;

/**
 * WeChat Pay
 * Class WechatPay
 * @package crmeb\services\pay\storage
 */
class WechatPay extends BasePay implements PayInterface
{

    protected function initialize(array $config)
    {
        // TODO: Implement initialize() method.
    }

    /**
     * Tạo đơn hàng để thanh toán
     * @param string $orderId
     * @param string $totalFee
     * @param string $attach
     * @param string $body
     * @param string $detail
     * @param array $options
     * @return array|mixed|string
     */
    public function create(string $orderId, string $totalFee, string $attach, string $body, string $detail, array $options = [])
    {
        $this->authSetPayType();

        switch ($this->payType) {
            case Order::NATIVE:
                return WechatService::nativePay(null, $orderId, $totalFee, $attach, $body, $detail);
            case Order::APP:
                return WechatService::appPay($options['openid'], $orderId, $totalFee, $attach, $body, $detail);
            case Order::JSAPI:
                if (empty($options['openid'])) {
                    throw new PayException('Thiếu openid');
                }
                if (request()->isRoutine()) {
                    // Lấy cấu hình, kiểm tra có phải thanh toán mới không
                    if ($options['pay_new_weixin_open']) {
                        return MiniProgramService::newJsPay($options['openid'], $orderId, $totalFee, $attach, $body, $detail, $options);
                    }
                    return MiniProgramService::jsPay($options['openid'], $orderId, $totalFee, $attach, $body, $detail);
                }
                return WechatService::jsPay($options['openid'], $orderId, $totalFee, $attach, $body, $detail);
            case 'h5':
                return WechatService::paymentPrepare(null, $orderId, $totalFee, $attach, $body, $detail, 'MWEB');
            default:
                throw new PayException('WeChat Pay: loại thanh toán không hợp lệ');
        }
    }

    /**
     * Thanh toán vào số dư
     * @param string $openid
     * @param string $orderId
     * @param string $amount
     * @param array $options
     * @return bool|mixed
     */
    public function merchantPay(string $openid, string $orderId, string $amount, array $options = [])
    {
        return WechatService::merchantPay($openid, $orderId, $amount, $options['desc'] ?? '');
    }

    /**
     * Hoàn tiền
     * @param string $outTradeNo
     * @param array $opt
     * @return Collection|mixed|ResponseInterface
     */
    public function refund(string $outTradeNo, array $opt = [])
    {
        if (!isset($opt['pay_price'])) throw new PayException(400730);
        $totalFee = floatval(bcmul($opt['pay_price'], 100, 0));
        $refundFee = isset($opt['refund_price']) ? floatval(bcmul($opt['refund_price'], 100, 0)) : null;
        $refundReason = $opt['desc'] ?? '';
        $refundNo = $opt['refund_id'] ?? $outTradeNo;
        $opUserId = $opt['op_user_id'] ?? null;
        $type = $opt['type'] ?? 'out_trade_no';
        /**
         * Chỉ dùng cho merchant dòng tiền cũ
         * REFUND_SOURCE_UNSETTLED_FUNDS---hoàn tiền từ quỹ chưa thanh toán (mặc định dùng quỹ chưa thanh toán để hoàn tiền)
         * REFUND_SOURCE_RECHARGE_FUNDS---hoàn tiền từ số dư khả dụng
         */
        $refundAccount = $opt['refund_account'] ?? 'REFUND_SOURCE_UNSETTLED_FUNDS';
        if (isset($opt['wechat'])) {
            $result = WechatService::refund($outTradeNo, $refundNo, $totalFee, $refundFee, $opUserId, $refundReason, $type, $refundAccount);
        } else {
            if ($opt['pay_new_weixin_open']) {
                $result = MiniProgramService::miniRefund($outTradeNo, $totalFee, $refundFee, $opt);
            } else {
                $result = MiniProgramService::refund($outTradeNo, $refundNo, $totalFee, $refundFee, $opUserId, $refundReason, $type, $refundAccount);
            }
        }
        if (!empty($opt['pay_new_weixin_open'])) {
            if ($result['errcode'] != 0) throw new AdminException($result['errmsg']);
        } else {
            if (isset($result['return_code']) && $result['return_code'] != 'SUCCESS') throw new AdminException($result['return_msg']);
            if (isset($result['result_code']) && $result['result_code'] != 'SUCCESS') throw new AdminException($result['err_code_des']);
            if (isset($result['status']) && $result['status'] != 'SUCCESS') throw new AdminException($result['status']);
        }
    }

    /**
     * Tra cứu đơn hàng hoàn tiền
     * @param string $outTradeNo
     * @param string $outRequestNo
     * @param array $other
     * @return Collection|mixed|ResponseInterface
     */
    public function queryRefund(string $outTradeNo, string $outRequestNo, array $other = [])
    {
        return WechatService::queryRefund($outTradeNo, $other['type'] ?? API::OUT_TRADE_NO);
    }

    /**
     * Callback bất đồng bộ
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Core\Exceptions\FaultException
     */
    public function handleNotify()
    {
        return WechatService::handleNotify();
    }
}
