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
namespace crmeb\services\pay\extend\allinpay;


/**
 *
 * Class AllinPay
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2022/12/27
 * @package crmeb\services\pay\extend\allinpay
 */
class AllinPay extends Client
{

    //API thanh toán thống nhất
    const UNITODER_PAY_API = 'unitorder/pay';

    //API quét mã thống nhất
    const UNITORDER_SCANQRPAY = 'unitorder/scanqrpay';

    //Hoàn tiền
    const UNITODER_TRANX_REFUND = 'tranx/refund';

    //Truy vấn đơn hàng
    const  UNITODER_TRANX_QUERY = 'tranx/query';

    const  UNITODER_QPAY_AGREEAPPLY = 'qpay/agreeapply';

    //Địa chỉ yêu cầu thanh toán trong OA WeChat
    const UNITODER_H5UNIONPAY = 'https://vsp.allinpay.com/apiweb/h5unionpay/unionorder';

    /**
     * @var string
     */
    protected $api = '';

    /**
     * Loại thanh toán
     * @var string
     */
    protected $payType = '';

    /**
     * @var string
     */
    protected $version = '';

    /**
     * @var string
     */
    protected $orderKey = 'reqsn';

    /**
     * @var int
     */
    protected $validtime = 720;

    /**
     * Tạo đơn hàng thanh toán
     * @param string $trxamt
     * @param string $orderId
     * @param string $body
     * @param string|null $openId
     * @param string|null $appid
     * @param string $frontUrl
     * @param string $remark
     * @return mixed
     */
    public function create(string $trxamt, string $orderId, string $body, string $returl = null, string $openId = null, string $appid = null, string $frontUrl = '', string $remark = '')
    {
        $totalFee = (int)bcmul($trxamt, '100');

        $data = [
            'trxamt' => $totalFee,
            $this->orderKey => $orderId,
            'validtime' => $this->validtime,
            'notify_url' => $this->notifyUrl
        ];

        if ($body) {
            $data['body'] = $body;
        }

        if ($remark) {
            $data['remark'] = $remark;
        }

        if ($this->payType) {
            $data['paytype'] = $this->payType;
        }

        if ($returl) {
            $data['returl'] = $returl;
        }

        if ($openId) {
            $data['acct'] = $openId;
        }

        if ($appid) {
            $data['sub_appid'] = $appid;
        }

        if ($frontUrl) {
            $data['front_url'] = $frontUrl;
        }

        if ($this->version) {
            $data['version'] = $this->version;
        }

        $form = !$this->api;
        $api = $this->api;

        $this->version = '';
        $this->payType = '';
        $this->api = '';
        $this->orderKey = 'reqsn';
        $this->validtime = 720;

        return $this->send($api, ['data' => $data, 'form' => $form]);
    }

    /**
     * Thanh toán WeChat H5
     * @param string $trxamt
     * @param string $orderId
     * @param string $body
     * @param string $returl
     * @param string $remark
     * @return mixed
     */
    public function h5Pay(string $trxamt, string $orderId, string $body, string $returl, string $remark = '')
    {
        $this->version = self::VERSION_NUM_12;
        return $this->create($trxamt, $orderId, $body, $returl, null, null, '', $remark);
    }

    /**
     * Thanh toán WeChat js
     * @param string $trxamt
     * @param string $orderId
     * @param string $body
     * @param string $openId
     * @param string $appId
     * @param string $frontUrl
     * @param string $remark
     * @return mixed
     */
    public function wechatPay(string $trxamt, string $orderId, string $body, string $openId, string $appId, string $frontUrl, string $remark = '')
    {
        $this->api = self::UNITODER_PAY_API;
        return $this->create($trxamt, $orderId, $body, null, $openId, $appId, $frontUrl, $remark);
    }

    /**
     * Thanh toán app - WeChat và Alipay
     * @param string $trxamt
     * @param string $orderId
     * @param string $body
     * @param bool $isWechat
     * @param string $remark
     * @return mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/1/14
     */
    public function appPay(string $trxamt, string $orderId, string $body, string $appid, string $openid = null, bool $isWechat = true, string $remark = '')
    {
        $this->api = self::UNITODER_PAY_API;
        $this->payType = $isWechat ? 'A02' : 'A01';
        $this->version = self::VERSION_NUM_11;
        return $this->create($trxamt, $orderId, $body, null, $openid, $appid, '', $remark);
    }

    /**
     * Thanh toán quầy thu ngân PC
     * @param string $trxamt
     * @param string $orderId
     * @param string $body
     * @return mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/1/15
     */
    public function pcPay(string $trxamt, string $orderId, string $body, string $remark, bool $isWechat = true)
    {
        $this->api = self::UNITODER_PAY_API;
        $this->payType = $isWechat ? 'W01' : 'A01';
        $this->version = self::VERSION_NUM_11;
        $res = $this->create($trxamt, $orderId, $body, null, null, null, '', $remark);
        $invalid = time() + 60;
        if ($isWechat) {
            $key = 'code_url';
        } else {
            $key = 'qrCode';
        }
        return ['invalid' => $invalid, 'logo' => sys_config('wap_login_logo'), $key => $res['payinfo']];
    }

    /**
     * @param string $trxamt
     * @param string $orderId
     * @param string $body
     * @param string $returl
     * @param string $remark
     * @return array
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/1/15
     */
    public function miniproPay(string $trxamt, string $orderId, string $body, string $remark = '')
    {
        $totalFee = bcmul($trxamt, '100');

        $data = [
            'paytype' => 'W06',
            'trxamt' => $totalFee,
            'reqsn' => $orderId,
            'notify_url' => $this->notifyUrl,
            'body' => $body,
            'validtime' => (string)$this->validtime,
            'cusid' => $this->cusid,
            'appid' => $this->appid,
            'signtype' => $this->signType,
            'randomstr' => uniqid(),
            'remark' => $remark,
            'version' => self::VERSION_NUM_12
        ];

        $data['sign'] = $this->sign($data);

        return $data;
    }

    public function agreeapply()
    {
        $data = [
            'meruserid' => 'eesssxxx',
            'accttype' => '00',
            'acctno' => '',
            'idtype' => 0,
            'idno' => '',
            'acctname' => '',
            'mobile' => '',
            'cvv2' => '',
            'reqip' => request()->ip(),
            'reqtime' => date('Y-m-d H:i:s'),
            'version' => self::VERSION_NUM_11,
        ];

        $data['cusid'] = $this->cusid;
        $data['appid'] = $this->appid;
        $data['signtype'] = $this->signType;
        $data['randomstr'] = uniqid();
        $data['sign'] = $this->sign($data);

        return $this->request(self::UNITODER_QPAY_AGREEAPPLY, $data);
    }


    /**
     * Truy vấn đơn hàng
     * @param string $reqsn
     * @return array|mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/1/15
     */
    public function query(string $reqsn)
    {
        $data = [
            'reqsn' => $reqsn
        ];

        return $this->send(self::UNITODER_TRANX_QUERY, ['data' => $data]);
    }

    /**
     * Khởi tạo hoàn tiền
     * @param string $trxamt
     * @param string $reqsn
     * @return array|mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/1/15
     */
    public function refund(string $trxamt, string $orderId, string $reqsn)
    {
        $totalFee = (int)bcmul($trxamt, '100');

        $data = [
            'trxamt' => $totalFee,
            'reqsn' => $orderId,
            'oldtrxid' => $reqsn,
        ];

        return $this->send(self::UNITODER_TRANX_REFUND, ['data' => $data]);
    }

    /**
     * Callback bất đồng bộ
     * @param callable $callback
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/1/15
     */
    public function handleNotify(callable $callback)
    {
        $params = [];
        foreach (request()->post() as $key => $val) {
            $params[$key] = $val;
        }

        $this->debugLog('Dữ liệu callback Allinpay' . json_encode($params));

        if (count($params) < 1) {
            //Nếu tham số trống thì không xử lý
            return "error";
        }

        $res = $this->validSign($params);

        $this->debugLog('Xác minh callback Allinpay:' . json_encode(['res' => $res]));

        if ($res && isset($params['trxstatus']) && $params['trxstatus'] === '0000') {
            //Xác minh chữ ký thành công
            return $callback($params) ? 'success' : 'error';
        } else {
            return "error";
        }
    }
}
