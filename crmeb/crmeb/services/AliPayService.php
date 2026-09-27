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

namespace crmeb\services;

use Alipay\EasySDK\Payment\Wap\Models\AlipayTradeWapPayResponse;
use app\services\pay\PayServices;
use app\services\system\SystemPemServices;
use think\facade\Event;
use think\facade\Log;
use think\facade\Route as Url;
use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Kernel\Factory;
use crmeb\exceptions\PayException;
use Alipay\EasySDK\Kernel\Util\ResponseChecker;

/**
 * Class AliPayService
 * @package crmeb\services
 */
class AliPayService
{

    /**
     * Cấu hình
     * @var array
     */
    protected $config = [
        'appId' => '',
        'merchantPrivateKey' => '',//Private key của ứng dụng
        'alipayPublicKey' => '',//Khóa công khai Alipay
        'notifyUrl' => '',//Có thể đặt địa chỉ service nhận thông báo bất đồng bộ
        'encryptKey' => '',//Có thể đặt khóa AES, cần khi gọi interface liên quan đến mã hóa/giải mã AES (tùy chọn)
        'alipayCertPath' => '',//Đường dẫn chứng chỉ Alipay (tùy chọn)
        'alipayRootCertPath' => '',//Đường dẫn chứng chỉ gốc Alipay (tùy chọn)
        'merchantCertPath' => '',//Đường dẫn chứng chỉ merchant (tùy chọn)
    ];

    /**
     * @var ResponseChecker
     */
    protected $response;

    /**
     * @var static
     */
    protected static $instance;

    /**
     * AliPayService constructor.
     * @param array $config
     */
    protected function __construct(array $config = [])
    {
        if (!$config) {
            $config = [
                'appId' => sys_config('ali_pay_appid'),
                'merchantPrivateKey' => sys_config('alipay_merchant_private_key'),
                'alipayPublicKey' => sys_config('alipay_public_key'),
                'notifyUrl' => sys_config('site_url') . Url::buildUrl('/api/pay/notify/alipay'),
                'alipayCertPath' => $this->getPemPath('alipay_cert_path'),
                'alipayRootCertPath' => $this->getPemPath('alipay_root_cert_path'),
                'merchantCertPath' => $this->getPemPath('merchant_cert_path'),
            ];
        }
        $this->config = array_merge($this->config, $config);
        $this->initialize();
        $this->response = new ResponseChecker();
    }

    public function getPemPath(string $name)
    {
        $systemPemServices = app()->make(SystemPemServices::class);
        $path = $systemPemServices->getPemPath($name);
        if ($path) return $path;
        $path = sys_config($name);
        if (strstr($path, 'http://') || strstr($path, 'https://')) {
            $path = parse_url($path)['path'] ?? '';
        }
        $path = root_path('runtime/pem') . ltrim($path, '/');
        if (!file_exists($path)) {
            $path = public_path('uploads') . ltrim($path, '/');
        }
        return $path;
    }

    /**
     * Khởi tạo instance
     * @param array $config
     * @return static
     */
    public static function instance(array $config = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($config);
        }
        return self::$instance;
    }

    /**
     * Khởi tạo
     */
    protected function initialize()
    {
        Factory::setOptions($this->getOptions());
    }

    /**
     * Đặt cấu hình
     * @return Config
     */
    protected function getOptions()
    {
        $options = new Config();
        $options->protocol = 'https';
        $options->gatewayHost = 'openapi.alipay.com';
        $options->signType = 'RSA2';

        $options->appId = $this->config['appId'];
        // Để tránh private key bị lộ theo mã nguồn, nên đọc chuỗi private key từ file thay vì viết trực tiếp vào mã nguồn
        $options->merchantPrivateKey = $this->config['merchantPrivateKey'];

        if (sys_config('alipay_sign_type') == 0) {
            // Chế độ khóa
            $options->alipayPublicKey = $this->config['alipayPublicKey'];
        } else {
            // Chế độ chứng chỉ
            $options->alipayCertPath = $this->config['alipayCertPath'];
            $options->alipayRootCertPath = $this->config['alipayRootCertPath'];
            $options->merchantCertPath = $this->config['merchantCertPath'];
            $options->alipayPublicKey = '';
        }
        //Có thể đặt địa chỉ service nhận thông báo bất đồng bộ (tùy chọn)
        $options->notifyUrl = $this->config['notifyUrl'];
        //Có thể đặt khóa AES, cần khi gọi interface liên quan đến mã hóa/giải mã AES (tùy chọn)
        if ($this->config['encryptKey']) {
            $options->encryptKey = $this->config['encryptKey'];
        }

        return $options;
    }

    /**
     * Tạo đơn hàng
     * @param string $title Tên sản phẩm
     * @param string $orderId Mã đơn hàng
     * @param string $totalAmount Số tiền thanh toán
     * @param string $passbackParams Ghi chú
     * @param string $quitUrl Địa chỉ chuyển hướng đồng bộ
     * @param string $returnUrl
     * @param bool $isCode
     * @return AlipayTradeWapPayResponse
     */
    public function create(string $title, string $orderId, string $totalAmount, string $passbackParams, string $quitUrl = '', string $returnUrl = '', bool $isCode = false)
    {
        $title = trim($title);
        try {
            if ($isCode) {
                //Thanh toán mã QR
                $result = Factory::payment()->faceToFace()->optional('passback_params', $passbackParams)->precreate($title, $orderId, $totalAmount);
            } else if (request()->isApp()) {
                //Thanh toán app
                $result = Factory::payment()->app()->optional('passback_params', $passbackParams)->pay($title, $orderId, $totalAmount);
            } else {
                //Thanh toán h5
                $result = Factory::payment()->wap()->optional('passback_params', $passbackParams)->pay($title, $orderId, $totalAmount, $quitUrl, $returnUrl);
            }
            if ($this->response->success($result)) {
                return $result->body ?? $result;
            } else {
                throw new PayException('Lý do thất bại:' . $result->msg . ',' . $result->subMsg);
            }
        } catch (\Exception $e) {
            throw new PayException($e->getMessage());
        }
    }

    /**
     * Hoàn tiền đơn hàng
     * @param string $outTradeNo Mã đơn hàng
     * @param string $totalAmount Số tiền hoàn
     * @param string $refund_id Mã đơn hoàn tiền
     * @return \Alipay\EasySDK\Payment\Common\Models\AlipayTradeRefundResponse
     */
    public function refund(string $outTradeNo, string $totalAmount, string $refund_id)
    {
        try {
            $result = Factory::payment()->common()->refund($outTradeNo, $totalAmount, $refund_id);
            if ($this->response->success($result)) {
                return $result;
            } else {
                throw new PayException('Lý do thất bại:' . $result->msg . ',' . $result->subMsg);
            }
        } catch (\Exception $e) {
            throw new PayException($e->getMessage());
        }
    }

    /**
     * Truy vấn thông tin mã đơn hoàn tiền giao dịch
     * @param string $outTradeNo
     * @param string $outRequestNo
     * @return \Alipay\EasySDK\Payment\Common\Models\AlipayTradeFastpayRefundQueryResponse
     */
    public function queryRefund(string $outTradeNo, string $outRequestNo)
    {
        try {
            $result = Factory::payment()->common()->queryRefund($outTradeNo, $outRequestNo);
            if ($this->response->success($result)) {
                return $result;
            } else {
                throw new PayException('Lý do thất bại:' . $result->msg . ',' . $result->subMsg);
            }
        } catch (\Exception $e) {
            throw new PayException($e->getMessage());
        }
    }

    /**
     * Callback thanh toán bất đồng bộ
     * @return string
     */
    public static function handleNotify()
    {
        return self::instance()->notify(function ($notify) {
            if (isset($notify->out_trade_no)) {

                $data = [
                    'attach' => $notify->attach,
                    'out_trade_no' => $notify->out_trade_no,
                    'transaction_id' => $notify->trade_no
                ];

                return Event::until('NotifyListener', [$data, PayServices::ALIAPY_PAY]);
            }
            return false;
        });
    }

    /**
     * Callback bất đồng bộ
     * @param callable $notifyFn
     * @return string
     */
    public function notify(callable $notifyFn)
    {
        app()->request->filter(['trim']);
        $paramInfo = app()->request->param();
        if (isset($paramInfo['type'])) {
            unset($paramInfo['type']);
        }
        //Mã đơn hàng của merchant
        $postOrder['out_trade_no'] = $paramInfo['out_trade_no'] ?? '';
        //Mã giao dịch Alipay
        $postOrder['trade_no'] = $paramInfo['trade_no'] ?? '';
        //Trạng thái giao dịch
        $postOrder['trade_status'] = $paramInfo['trade_status'] ?? '';
        //Ghi chú
        $postOrder['attach'] = isset($paramInfo['passback_params']) ? urldecode($paramInfo['passback_params']) : '';
        if (in_array($paramInfo['trade_status'], ['TRADE_SUCCESS', 'TRADE_FINISHED']) && $this->verifyNotify($paramInfo)) {
            try {
                if ($notifyFn((object)$postOrder)) {
                    return 'success';
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                Log::error('Nhận callback bất đồng bộ từ Alipay thành công, lỗi khi thực thi hàm. Mã đơn lỗi:' . $postOrder['out_trade_no']);
            }
        }
        return 'fail';

    }

    /**
     * Xác minh chữ ký
     * @return bool
     */
    protected function verifyNotify(array $param)
    {
        try {
            return Factory::payment()->common()->verifyNotify($param);
        } catch (\Exception $e) {
            Log::error('Callback Alipay thành công, xảy ra lỗi khi xác minh chữ ký, lý do lỗi:' . $e->getMessage());
        }
        return false;
    }

    /**
     * Interface thanh toán merchant
     *
     * @param array $bizParams Tham số nghiệp vụ
     * @return mixed|false Kết quả thanh toán hoặc false
     * @throws PayException Ngoại lệ thanh toán
     */
    public function merchantPay(array $bizParams, $alipaySignType = 0)
    {
        try {
            // Gọi phương thức chung của class factory để thực hiện chuyển tiền Alipay
            $method = $alipaySignType == 0 ? 'alipay.fund.trans.toaccount.transfer' : 'alipay.fund.trans.uni.transfer';
            $result = Factory::util()->generic()->execute($method, [], $bizParams);
            // Kiểm tra thanh toán có thành công không
            if ($this->response->success($result)) {
                return $result;
            } else {
                Log::error('Chuyển khoản Alipay thất bại, lý do:' . $result->msg . ' | ' . $result->subCode . ' | ' . $result->subMsg);
                return false;
            }
        } catch (\Exception $e) {
            // Ghi log và trả về false
            Log::error('Chuyển khoản Alipay thất bại, lý do:' . $e->getMessage());
            return false;
        }
    }

}
