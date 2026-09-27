# Tài liệu quy trình phát triển thanh toán

## 🎯 Tổng quan chức năng thanh toán

### Các tình huống thanh toán
- **Thanh toán mua sản phẩm**: Thanh toán đơn hàng sản phẩm thông thường
- **Thanh toán mua dịch vụ**: Dịch vụ ảo, mua gói thành viên
- **Thanh toán nạp tiền**: Nạp tiền vào số dư, nạp điểm thưởng
- **Thanh toán tiền ký quỹ**: Tiền ký quỹ của merchant, tiền ký quỹ dịch vụ
- **Xử lý hoàn tiền**: Hoàn tiền đơn hàng, xử lý đảo giao dịch (reversal)

### Các phương thức thanh toán được hỗ trợ
```php
const PAY_TYPE_WECHAT = 'wechat';      // WeChat Pay
const PAY_TYPE_ALIPAY = 'alipay';      // Alipay
const PAY_TYPE_BALANCE = 'balance';    // Thanh toán bằng số dư
const PAY_TYPE_INTEGRAL = 'integral';  // Thanh toán bằng điểm thưởng
const PAY_TYPE_UNION = 'union';        // UnionPay QuickPass
const PAY_TYPE_BANK = 'bank';          // Thanh toán bằng thẻ ngân hàng
```

## 🏗️ Kiến trúc hệ thống thanh toán

### Kiến trúc module thanh toán
```
┌─────────────────────────────────────────────────────┐
│                  Bộ điều khiển thanh toán (Controller)              │
│   - Xử lý điểm vào thanh toán                                   │
│   - Xác thực tham số                                       │
│   - Trả về kết quả                                       │
└─────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────┐
│                  Tầng dịch vụ thanh toán (PaymentService)          │
│   - Xử lý logic thanh toán                                   │
│   - Định tuyến phương thức thanh toán                                   │
│   - Quản lý trạng thái thanh toán                                   │
└─────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────┐
│              Tầng adapter kênh thanh toán (PaymentAdapter)          │
│   - Adapter WeChat Pay (WechatPayment)                  │
│   - Adapter Alipay (AlipayPayment)                    │
│   - Adapter thanh toán bằng số dư (BalancePayment)                 │
│   - Adapter thanh toán bằng điểm thưởng (IntegralPayment)                │
└─────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────┐
│                  SDK thanh toán bên thứ ba                       │
│   - SDK chính thức của WeChat Pay                                │
│   - SDK chính thức của Alipay                                  │
│   - API thanh toán ngân hàng                                   │
└─────────────────────────────────────────────────────┘
```

### Thiết kế cơ sở dữ liệu
```sql
-- Bảng lịch sử thanh toán
CREATE TABLE `store_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(32) NOT NULL COMMENT 'Mã đơn hàng',
  `out_trade_no` varchar(32) NOT NULL COMMENT 'Mã giao dịch thanh toán',
  `pay_type` varchar(20) NOT NULL COMMENT 'Phương thức thanh toán',
  `pay_price` decimal(10,2) NOT NULL COMMENT 'Số tiền thanh toán',
  `trade_no` varchar(64) DEFAULT NULL COMMENT 'Mã giao dịch của bên thanh toán thứ ba',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Trạng thái thanh toán',
  `pay_time` int(11) DEFAULT NULL COMMENT 'Thời gian thanh toán',
  `create_time` int(11) NOT NULL COMMENT 'Thời gian tạo',
  `update_time` int(11) NOT NULL COMMENT 'Thời gian cập nhật',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_out_trade_no` (`out_trade_no`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_pay_status` (`pay_status`)
) ENGINE=InnoDB COMMENT='Bảng lịch sử thanh toán';

-- Bảng lịch sử hoàn tiền
CREATE TABLE `store_refund` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(32) NOT NULL COMMENT 'Mã đơn hàng',
  `refund_no` varchar(32) NOT NULL COMMENT 'Mã giao dịch hoàn tiền',
  `refund_amount` decimal(10,2) NOT NULL COMMENT 'Số tiền hoàn',
  `refund_reason` varchar(255) DEFAULT NULL COMMENT 'Lý do hoàn tiền',
  `refund_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Trạng thái hoàn tiền',
  `refund_time` int(11) DEFAULT NULL COMMENT 'Thời gian hoàn tiền',
  `create_time` int(11) NOT NULL COMMENT 'Thời gian tạo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_refund_no` (`refund_no`),
  KEY `idx_order_id` (`order_id`)
) ENGINE=InnoDB COMMENT='Bảng lịch sử hoàn tiền';
```

## 🔧 Quy trình phát triển

### 1. Phân tích yêu cầu và thiết kế
```markdown
1. Xác định kịch bản và yêu cầu thanh toán
2. Chọn các phương thức thanh toán được hỗ trợ
3. Thiết kế quy trình thanh toán và luồng chuyển trạng thái
4. Xác định chính sách và quy tắc hoàn tiền
5. Thiết kế quy trình đối soát và quyết toán
```

### 2. Chuẩn bị môi trường
```bash
# Cài đặt dependency SDK thanh toán
composer require overtrue/wechat
composer require alipay-sdk/php-all

# Cấu hình tham số thanh toán
cp .env.example .env
# Cấu hình tham số WeChat Pay
WECHAT_APPID=your_appid
WECHAT_MCHID=your_mchid
WECHAT_KEY=your_key
WECHAT_APPSECRET=your_appsecret

# Cấu hình tham số Alipay
ALIPAY_APPID=your_appid
ALIPAY_PRIVATE_KEY=your_private_key
ALIPAY_PUBLIC_KEY=your_public_key
```

### 3. Phát triển controller thanh toán
```php
<?php
namespace app\api\controller\v1;

use app\services\PaymentService;
use app\validate\PaymentValidate;

class PaymentController
{
    /**
     * Khởi tạo thanh toán
     * @route('api/v1/payment/create')
     */
    public function createPayment()
    {
        // Xác thực tham số
        $params = $this->request->post();
        validate(PaymentValidate::class)->scene('create')->check($params);

        // Gọi dịch vụ thanh toán
        $paymentService = app()->make(PaymentService::class);
        $result = $paymentService->createPayment($params);

        return $this->success($result);
    }

    /**
     * Xử lý callback thanh toán
     * @route('api/v1/payment/notify/:type')
     */
    public function paymentNotify($type)
    {
        try {
            $paymentService = app()->make(PaymentService::class);
            $result = $paymentService->handleNotify($type, $this->request->post());

            // Trả về response thành công theo yêu cầu của bên thanh toán thứ ba
            return response($result['response']);
        } catch (\Exception $e) {
            Log::error('Xử lý callback thanh toán thất bại: ' . $e->getMessage());
            return response('FAIL');
        }
    }
}
```

### 4. Phát triển tầng service thanh toán
```php
<?php
namespace app\services;

use app\exception\PaymentException;
use app\services\payment\WechatPayment;
use app\services\payment\AlipayPayment;
use app\services\payment\BalancePayment;

class PaymentService extends BaseService
{
    /**
     * Tạo thanh toán
     */
    public function createPayment(array $params): array
    {
        // Xác thực trạng thái đơn hàng
        $orderInfo = $this->validateOrder($params['order_id']);

        // Chọn phương thức thanh toán
        $paymentAdapter = $this->getPaymentAdapter($params['pay_type']);

        // Tạo bản ghi thanh toán
        $payData = $this->createPayRecord($orderInfo, $params);

        // Gọi adapter thanh toán
        return $paymentAdapter->createPayment($payData);
    }

    /**
     * Lấy adapter thanh toán
     */
    protected function getPaymentAdapter(string $payType): PaymentInterface
    {
        switch ($payType) {
            case 'wechat':
                return app()->make(WechatPayment::class);
            case 'alipay':
                return app()->make(AlipayPayment::class);
            case 'balance':
                return app()->make(BalancePayment::class);
            default:
                throw new PaymentException('Phương thức thanh toán không được hỗ trợ', 5001);
        }
    }

    /**
     * Xử lý callback thanh toán
     */
    public function handleNotify(string $payType, array $notifyData): array
    {
        $paymentAdapter = $this->getPaymentAdapter($payType);
        $verifyResult = $paymentAdapter->verifyNotify($notifyData);

        if (!$verifyResult['success']) {
            throw new PaymentException('Xác thực callback thất bại', 5002);
        }

        // Cập nhật trạng thái thanh toán
        $this->updatePaymentStatus($verifyResult['out_trade_no'], $verifyResult);

        return ['response' => 'SUCCESS'];
    }
}
```

### 5. Phát triển adapter thanh toán (lấy WeChat Pay làm ví dụ)
```php
<?php
namespace app\services\payment;

use EasyWeChat\Factory;
use app\exception\PaymentException;

class WechatPayment implements PaymentInterface
{
    protected $app;

    public function __construct()
    {
        $config = [
            'app_id' => env('wechat.app_id'),
            'mch_id' => env('wechat.mch_id'),
            'key' => env('wechat.key'),
            'cert_path' => env('wechat.cert_path'),
            'key_path' => env('wechat.key_path'),
            'notify_url' => env('wechat.notify_url'),
        ];

        $this->app = Factory::payment($config);
    }

    /**
     * Tạo thanh toán
     */
    public function createPayment(array $payData): array
    {
        try {
            $result = $this->app->order->unify([
                'body' => $payData['body'],
                'out_trade_no' => $payData['out_trade_no'],
                'total_fee' => $payData['total_fee'],
                'trade_type' => 'JSAPI',
                'openid' => $payData['openid'],
            ]);

            if ($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
                return [
                    'pay_params' => $this->app->jssdk->bridgeConfig($result['prepay_id']),
                    'pay_type' => 'wechat'
                ];
            } else {
                throw new PaymentException($result['return_msg'] ?? 'Tạo thanh toán WeChat Pay thất bại', 5003);
            }
        } catch (\Exception $e) {
            throw new PaymentException('Ngoại lệ WeChat Pay: ' . $e->getMessage(), 5004);
        }
    }

    /**
     * Xác thực callback
     */
    public function verifyNotify(array $notifyData): array
    {
        try {
            $message = $this->app->handlePaidNotify(function($message, $fail) {
                // Xác thực trạng thái thanh toán
                if ($message['return_code'] !== 'SUCCESS') {
                    return $fail('Lỗi kết nối, vui lòng gửi thông báo lại sau');
                }

                if ($message['result_code'] !== 'SUCCESS') {
                    return $fail('Thanh toán thất bại, vui lòng gửi thông báo lại sau');
                }

                return [
                    'out_trade_no' => $message['out_trade_no'],
                    'trade_no' => $message['transaction_id'],
                    'pay_amount' => $message['total_fee'] / 100,
                    'pay_time' => strtotime($message['time_end']),
                    'success' => true
                ];
            });

            return $message;
        } catch (\Exception $e) {
            throw new PaymentException('Ngoại lệ khi xác thực callback: ' . $e->getMessage(), 5005);
        }
    }
}
```

### 6. Định nghĩa interface thanh toán
```php
<?php
namespace app\services\payment;

interface PaymentInterface
{
    /**
     * Tạo thanh toán
     */
    public function createPayment(array $payData): array;

    /**
     * Xử lý callback thanh toán
     */
    public function verifyNotify(array $notifyData): array;

    /**
     * Tra cứu trạng thái thanh toán
     */
    public function queryPayment(string $outTradeNo): array;

    /**
     * Yêu cầu hoàn tiền
     */
    public function refund(array $refundData): array;
}
```

## 🧪 Quy trình kiểm thử

### 1. Kiểm thử đơn vị
```php
<?php
namespace tests\services;

use app\services\PaymentService;
use app\services\payment\WechatPayment;
use think\testing\TestCase;

class PaymentServiceTest extends TestCase
{
    public function testCreatePayment()
    {
        // Giả lập tham số thanh toán
        $params = [
            'order_id' => 'TEST202401170001',
            'pay_type' => 'wechat',
            'pay_amount' => 100.00
        ];

        // Tạo instance dịch vụ thanh toán
        $paymentService = new PaymentService();

        // Kiểm thử tạo thanh toán
        $result = $paymentService->createPayment($params);

        $this->assertArrayHasKey('pay_params', $result);
        $this->assertEquals('wechat', $result['pay_type']);
    }

    public function testPaymentNotify()
    {
        // Giả lập dữ liệu callback WeChat Pay
        $notifyData = [
            'return_code' => 'SUCCESS',
            'result_code' => 'SUCCESS',
            'out_trade_no' => 'TEST202401170001',
            'transaction_id' => '420000202401170001',
            'total_fee' => '10000',
            'time_end' => '20240117153000'
        ];

        $paymentService = new PaymentService();
        $result = $paymentService->handleNotify('wechat', $notifyData);

        $this->assertEquals('SUCCESS', $result['response']);
    }
}
```

### 2. Kiểm thử tích hợp
```bash
# Chạy các bài test liên quan đến thanh toán
php think test tests/services/PaymentServiceTest
php think test tests/services/payment/WechatPaymentTest
php think test tests/services/payment/AlipayPaymentTest

# Tạo báo cáo độ bao phủ kiểm thử
php think test --coverage-html coverage/payment
```

### 3. Kiểm thử trong môi trường sandbox
```php
// Cấu hình môi trường sandbox
'wechat' => [
    'app_id' => 'appid sandbox',
    'mch_id' => 'mã merchant sandbox',
    'key' => 'key sandbox',
    'sandbox' => true, // Bật chế độ sandbox
]

// Cấu hình sandbox Alipay
'alipay' => [
    'app_id' => 'appid sandbox',
    'gateway_url' => 'https://openapi.alipaydev.com/gateway.do',
    'sign_type' => 'RSA2',
    'sandbox' => true,
]
```

## 🚀 Triển khai đưa vào hoạt động

### 1. Cấu hình môi trường production
```bash
# Thiết lập cấu hình môi trường production
cp .env.production .env

# Cấu hình tham số thanh toán cho môi trường production
WECHAT_APPID=appid môi trường production
WECHAT_MCHID=mã merchant môi trường production
WECHAT_KEY=key môi trường production
WECHAT_CERT_PATH=/path/to/cert/apiclient_cert.pem
WECHAT_KEY_PATH=/path/to/cert/apiclient_key.pem

ALIPAY_APPID=appid môi trường production
ALIPAY_PRIVATE_KEY=private key môi trường production
ALIPAY_PUBLIC_KEY=public key môi trường production
```

### 2. Cấu hình chứng chỉ SSL
```nginx
# Nginx (cấu hình SSL)
server {
    listen 443 ssl http2;
    server_name yourdomain.com;

    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/key.pem;

    # API callback thanh toán
    location /api/v1/payment/notify/ {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

### 3. Cấu hình giám sát và cảnh báo
```yaml
# Chỉ số giám sát thanh toán
payment:
  success_rate:
    type: gauge
    help: Tỷ lệ thanh toán thành công
    labels: [pay_type]

  response_time:
    type: histogram
    help: Phân bố thời gian phản hồi thanh toán
    labels: [pay_type]

  error_count:
    type: counter
    help: Số lần lỗi thanh toán
    labels: [pay_type, error_code]
```

## 📊 Giám sát vận hành

### 1. Giám sát log
```php
// Ghi log quan trọng về thanh toán
Log::info('Tạo thanh toán', [
    'order_id' => $orderId,
    'pay_type' => $payType,
    'amount' => $amount,
    'user_id' => $userId
]);

Log::error('Thanh toán thất bại', [
    'order_id' => $orderId,
    'error_code' => $errorCode,
    'error_msg' => $errorMsg,
    'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)
]);
```

### 2. Giám sát hiệu năng
```bash
# Giám sát hiệu năng API thanh toán
# Thời gian phản hồi trung bình < 200ms
# 95%thời gian phản hồi < 500ms
# Tỷ lệ lỗi < 0.1%

# Giám sát bằng Prometheus
payment_api_duration_seconds_bucket{pay_type="wechat",le="0.1"} 12345
payment_api_duration_seconds_bucket{pay_type="wechat",le="0.5"} 23456
```

### 3. Xử lý đối soát
```php
/**
 * Tác vụ đối soát hằng ngày
 */
public function dailyReconciliation()
{
    $yesterday = strtotime('yesterday');
    $today = strtotime('today');

    // Tra cứu bản ghi thanh toán cục bộ
    $localRecords = $this->payDao->getRecordsByTime($yesterday, $today);

    // Tra cứu bản ghi thanh toán của bên thứ ba
    foreach (['wechat', 'alipay'] as $payType) {
        $adapter = $this->getPaymentAdapter($payType);
        $thirdPartyRecords = $adapter->queryBill($yesterday, $today);

        // Xử lý đối soát
        $result = $this->reconcile($localRecords, $thirdPartyRecords);

        // Ghi nhận kết quả đối soát
        $this->saveReconciliationResult($result);
    }
}
```

## 🔧 Xử lý sự cố

### Xử lý sự cố thường gặp
```markdown
1. **Callback thanh toán thất bại**
   - Kiểm tra khả năng kết nối mạng
   - Xác thực hiệu lực chứng chỉ
   - Kiểm tra cấu hình địa chỉ callback

2. **Chữ ký thanh toán không đúng**
   - Kiểm tra cấu hình khóa bí mật
   - Xác thực thuật toán chữ ký
   - Kiểm tra encoding của tham số

3. **Số tiền thanh toán không khớp**
   - Kiểm tra đơn vị tiền (xu/đồng)
   - Xác thực logic tính số tiền
   - Kiểm tra khấu trừ phiếu giảm giá

4. **Thanh toán trùng lặp**
   - Kiểm tra việc xác thực trạng thái thanh toán
   - Thêm cơ chế chống trùng lặp
   - Xử lý bằng kiểm duyệt thủ công
```

### Quy trình xử lý khẩn cấp
```bash
# 1. Tạm dừng chức năng thanh toán
php think payment:disable wechat

# 2. Xem log thanh toán
tail -f runtime/log/payment.log

# 3. Rollback phiên bản có vấn đề
git revert <commit-hash>

# 4. Bật kênh thanh toán dự phòng
php think payment:enable alipay
```

---
**Phiên bản tài liệu**: v1.0  
**Cập nhật lần cuối**: 2024-01-17  
**Người bảo trì**: Đội ngũ phát triển thanh toán  

💡 **Lưu ý**: Chức năng thanh toán liên quan đến an toàn tài chính, vui lòng tuân thủ nghiêm ngặt quy chuẩn bảo mật và yêu cầu kiểm toán.

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
