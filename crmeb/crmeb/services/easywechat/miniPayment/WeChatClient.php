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
namespace crmeb\services\easywechat\miniPayment;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Kernel\Support;
use EasyWeChat\Kernel\Support\Collection;
use EasyWeChat\Kernel\Traits\HasHttpRequests;
use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Kernel\BaseClient;
use EasyWeChat\Payment\Merchant;

class WeChatClient extends AbstractAPI
{
    private $expire_time = 7000;


    /**
     * Tạo đơn hàng, thanh toán
     */
    const API_SET_CREATE_ORDER = 'https://api.weixin.qq.com/shop/pay/createorder';
    /**
     * Hoàn tiền
     */
    const API_SET_REFUND_ORDER = 'https://api.weixin.qq.com/shop/pay/refundorder';


    /**
     * Merchant instance.
     *
     * @var \EasyWeChat\Payment\Merchant
     */
    protected $merchant;

    /**
     * ProgramSubscribeService constructor.
     * @param AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken, Merchant $merchant)
    {
        parent::__construct($accessToken);
        $this->merchant = $merchant;
    }

    /**
     * Thanh toán
     * @param array $params [
     *                      'openid'=>'openid của người thanh toán',
     *                      'out_trade_no'=>'mã giao dịch tổng của đơn hợp nhất do merchant thanh toán',
     *                      'total_fee'=>'số tiền thanh toán',
     *                      'wx_out_trade_no'=>'mã giao dịch của merchant',
     *                      'body'=>'mô tả sản phẩm',
     *                      'attach'=>'loại thanh toán',  //product sản phẩm  member thành viên
     *                      ]
     * @param $isContract
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createorder($order)
    {
        $params = [
            'openid' => $order['openid'],    // openid của người thanh toán
            'combine_trade_no' => $order['out_trade_no'],  // Mã giao dịch tổng của đơn hợp nhất do merchant thanh toán
            'expire_time' => time() + $this->expire_time,
            'sub_orders' => [
                [
                    'mchid' => $this->merchant->merchant_id,
                    'amount' => (int)$order['total_fee'],
                    'trade_no' => $order['out_trade_no'],
                    'description' => $order['body']
                ]
            ]
        ];
        return $this->parseJSON('post', [self::API_SET_CREATE_ORDER, json_encode($params)]);
    }

    /**
     * Hoàn tiền
     * @param array $params [
     *                      'openid'=>'openid của người được hoàn tiền',
     *                      'trade_no'=>'mã giao dịch của merchant',
     *                      'transaction_id'=>'mã thanh toán',
     *                      'refund_no'=>'mã hoàn tiền của merchant',
     *                      'total_amount'=>'tổng số tiền đơn hàng',
     *                      'refund_amount'=>'số tiền hoàn trả',  //product sản phẩm  member thành viên
     *                      ]
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refundorder(array $order)
    {
        $params = [
            'openid' => $order['openid'],
            'mchid' => $this->merchant->merchant_id,
            'trade_no' => $order['trade_no'],
            'transaction_id' => $order['transaction_id'],
            'refund_no' => $order['refund_no'],
            'total_amount' => $order['total_amount'],
            'refund_amount' => $order['refund_amount'],
        ];
        return $this->parseJSON('post', [self::API_SET_REFUND_ORDER, json_encode($params)]);
    }


}