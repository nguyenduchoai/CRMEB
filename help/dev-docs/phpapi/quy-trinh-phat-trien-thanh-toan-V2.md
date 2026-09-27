# Tài liệu quy trình phát triển thanh toán CRMEB

## 🎯 Tổng quan chức năng thanh toán

Dựa trên phân tích hệ thống thanh toán của phiên bản CRMEB 5.6.4+, dự án áp dụng kiến trúc dịch vụ thanh toán thống nhất, hỗ trợ tích hợp nhiều phương thức thanh toán.

### Các phương thức thanh toán được hỗ trợ
```php
// app/services/pay/PayServices.php
const WEIXIN_PAY = 'weixin';      // WeChat Pay
const YUE_PAY = 'yue';            // Thanh toán bằng số dư
const OFFLINE_PAY = 'offline';    // Thanh toán ngoại tuyến
const ALIAPY_PAY = 'alipay';      // Alipay
const ALLIN_PAY = 'allinpay';     // Allinpay
const FRIEND = 'friend';          // Bạn bè thanh toán hộ
const BANK = 'bank';              // Chuyển khoản ngân hàng
```

## 🏗️ Kiến trúc hệ thống thanh toán

### Cấu trúc các lớp service cốt lõi
```
app/services/pay/
├── PayServices.php          # Dịch vụ điểm vào thanh toán thống nhất
├── OrderPayServices.php     # Dịch vụ thanh toán đơn hàng
├── PayNotifyServices.php    # Dịch vụ callback thanh toán
├── YuePayServices.php       # Dịch vụ thanh toán bằng số dư
└── RechargeServices.php     # Dịch vụ nạp tiền

crmeb/services/pay/
├── Pay.php                  # Driver thanh toán cơ sở
├── storage/
│   ├── WechatPay.php        # Driver WeChat Pay
│   ├── AliPay.php           # Driver Alipay
│   ├── AllinPay.php         # Driver Allinpay
│   └── V3WechatPay.php      # Driver WeChat Pay V3
```

### Quy trình thanh toán

#### 1. Quy trình yêu cầu thanh toán
```mermaid
graph TD
    A[Client] -> B[Controller thanh toán]
    B --> C[PayServices]
    C --> D[Driver thanh toán WechatPay/AliPay]
    D --> E[Nền tảng thanh toán bên thứ ba]
    E --> F[Trả về tham số thanh toán]
    F --> A
```

#### 2. Quy trình callback thanh toán
```mermaid
graph TD
    A[Thanh toán bên thứ ba] -> B[API callback thanh toán]
    B --> C[PayNotifyServices]
    C --> D[Cập nhật trạng thái đơn hàng]
    C --> E[Cập nhật bản ghi thanh toán]
    D --> F[Xử lý nghiệp vụ]
```

## 🔧 Phân tích code cốt lõi

### Điểm vào thanh toán thống nhất (PayServices.php)
```php
public function pay(string $payType, string $orderId, string $price, string $successAction, string $body, array $options = [])
{
    try {
        // Xử lý ánh xạ loại thanh toán
        if (in_array($payType, ['routine', 'weixinh5', 'weixin', 'pc', 'store'])) {
            $payType = 'wechat_pay';
            // Kiểm tra API V3
            if (sys_config('pay_wechat_type') == 1) {
                $payType = 'v3_wechat_pay';
            }
        } elseif ($payType == 'alipay') {
            $payType = 'ali_pay';
        } elseif ($payType == 'allinpay') {
            $payType = 'allin_pay';
        }

        // Tạo instance thanh toán
        $pay = app()->make(Pay::class, [$payType]);

        return $pay->create($orderId, $price, $successAction, $body, '', $options);

    } catch (\Exception $e) {
        throw new ApiException($e->getMessage());
    }
}
```

### Driver WeChat Pay (WechatPay.php)
```php
public function create(string $orderId, string $totalFee, string $attach, string $body, string $detail, array $options = [])
{
    $this->authSetPayType();

    switch ($this->payType) {
        case Order::NATIVE:   // Thanh toán quét mã
            return WechatService::nativePay(null, $orderId, $totalFee, $attach, $body, $detail);
        case Order::APP:      // Thanh toán trên APP
            return WechatService::appPay($options['openid'], $orderId, $totalFee, $attach, $body, $detail);
        case Order::JSAPI:    // Thanh toán Mini Program/OA WeChat
            if (request()->isRoutine()) {
                // Thanh toán Mini Program
                if ($options['pay_new_weixin_open']) {
                    return MiniProgramService::newJsPay($options['openid'], $orderId, $totalFee, $attach, $body, $detail, $options);
                }
                return MiniProgramService::jsPay($options['openid'], $orderId, $totalFee, $attach, $body, $detail);
            }
            return WechatService::jsPay($options['openid'], $orderId, $totalFee, $attach, $body, $detail);
        case 'h5':            // Thanh toán H5
            return WechatService::paymentPrepare(null, $orderId, $totalFee, $attach, $body, $detail, 'MWEB');
        default:
            throw new PayException('WeChat Pay: loại thanh toán không hợp lệ');
    }
}
```

### Xử lý callback thanh toán (PayNotifyServices.php)
```php
public function wechatProduct(string $order_id = null, string $trade_no = null, string $payType = PayServices::WEIXIN_PAY)
{
    try {
        $services = app()->make(StoreOrderSuccessServices::class);
        $orderInfo = $services->getOne(['order_id' => $order_id]);

        if (!$orderInfo) return true;
        if ($orderInfo->paid) return true;

        return $services->paySuccess($orderInfo->toArray(), $payType, ['trade_no' => $trade_no]);
    } catch (\Exception $e) {
        return false;
    }
}
```

## 🛠️ Hướng dẫn phát triển

### 1. Thiết lập cấu hình thanh toán
Cấu hình trong hệ thống quản trị:
- Tham số WeChat Pay (APPID, mã merchant, khóa API, chứng chỉ)
- Cấu hình Alipay
- Cấu hình Allinpay
- Bật/tắt phương thức thanh toán

### 2. Khởi tạo lệnh gọi thanh toán
```php
// Gọi trong controller
public function createPayment()
{
    $params = $this->request->post();

    // Xác thực tham số
    validate(PaymentValidate::class)->scene('create')->check($params);

    $payServices = app()->make(PayServices::class);
    $result = $payServices->pay(
        $params['pay_type'],
        $params['order_id'], 
        $params['amount'],
        url('api/v1/payment/notify/wechat', [], true, true),
        'Mua sản phẩm'
    );

    return $this->success($result);
}
```

### 3. Cấu hình callback thanh toán
```php
// Cấu hình route
Route::post('api/v1/payment/notify/:type', 'api/v1/payment/notify');

// Controller callback
public function notify($type)
{
    $notifyService = app()->make(PayNotifyServices::class);

    switch ($type) {
        case 'wechat':
            return $notifyService->wechatProduct(
                $this->request->post('out_trade_no'),
                $this->request->post('transaction_id')
            );
        case 'alipay':
            // Xử lý callback Alipay
            break;
    }

    return response('SUCCESS');
}
```

## 🧪 Hướng dẫn kiểm thử

### Ví dụ kiểm thử đơn vị
```php
class PaymentTest extends TestCase
{
    public function testWechatPaymentCreation()
    {
        $payService = new PayServices();
        $result = $payService->pay(
            'weixin', 
            'TEST20240117001', 
            '100.00', 
            'https://domain.com/notify',
            'Sản phẩm thử nghiệm'
        );

        $this->assertArrayHasKey('timeStamp', $result);
        $this->assertArrayHasKey('nonceStr', $result);
        $this->assertArrayHasKey('package', $result);
    }
}
```

### Cấu hình môi trường sandbox
```bash
# Sandbox WeChat Pay
WECHAT_SANDBOX=true
WECHAT_APPID=sandbox_appid
WECHAT_MCHID=sandbox_mchid

# Sandbox Alipay
ALIPAY_SANDBOX=true
ALIPAY_GATEWAY=https://openapi.alipaydev.com/gateway.do
```

## 📊 Thiết kế cơ sở dữ liệu

### Bảng bản ghi thanh toán (eb_store_pay)
```sql
CREATE TABLE `eb_store_pay` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'id người dùng',
  `oid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID đơn hàng',
  `order_id` varchar(32) NOT NULL DEFAULT '' COMMENT 'Mã đơn hàng',
  `out_trade_no` varchar(32) NOT NULL DEFAULT '' COMMENT 'Mã giao dịch thanh toán',
  `pay_type` varchar(20) NOT NULL DEFAULT '' COMMENT 'Phương thức thanh toán',
  `pay_price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT 'Số tiền thanh toán',
  `trade_no` varchar(64) DEFAULT NULL COMMENT 'Mã giao dịch của bên thanh toán thứ ba',
  `paid` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Trạng thái thanh toán',
  `pay_time` int(10) UNSIGNED DEFAULT NULL COMMENT 'Thời gian thanh toán',
  `add_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Thời gian tạo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `out_trade_no` (`out_trade_no`),
  KEY `order_id` (`order_id`),
  KEY `paid` (`paid`)
) ENGINE=InnoDB COMMENT='Bảng lịch sử thanh toán';
```

## 🚀 Triển khai và vận hành

### Cấu hình môi trường production
```bash
# Cấu hình WeChat Pay
WECHAT_APPID=your_appid
WECHAT_MCHID=your_mchid
WECHAT_KEY=your_key
WECHAT_CERT_PATH=/path/to/cert.pem
WECHAT_KEY_PATH=/path/to/key.pem

# Cấu hình chứng chỉ SSL
ssl_certificate /path/to/ssl/cert.pem;
ssl_certificate_key /path/to/ssl/key.pem;
```

### Giám sát và cảnh báo
```php
// Chỉ số giám sát thanh toán
$metrics = [
    'payment_success_rate' => ($successCount / $totalCount) * 100,
    'payment_response_time' => microtime(true) - $startTime,
    'payment_error_count' => $errorCount
];

// Ghi log
Log::info('Giám sát thanh toán', $metrics);
```

## 🔧 Xử lý sự cố

### Sự cố thường gặp
1. **Callback thanh toán thất bại**: Kiểm tra cấu hình nginx, chứng chỉ SSL, tường lửa
2. **Lỗi chữ ký**: Kiểm tra cấu hình khóa thanh toán
3. **Số tiền không khớp**: Kiểm tra đơn vị tiền và cách tính khuyến mãi
4. **Thanh toán trùng lặp**: Thêm cơ chế chống trùng lặp và kiểm tra trạng thái

### Xử lý khẩn cấp
```bash
# Xem log thanh toán
tail -f runtime/log/payment.log

# Vô hiệu hóa phương thức thanh toán gặp sự cố
php think pay:disable weixin

# Bật phương thức thanh toán dự phòng
php think pay:enable alipay
```

---
**Phiên bản tài liệu**: v2.0  
**Ngày cập nhật**: 2024-01-17  
**Phiên bản áp dụng**: CRMEB 5.6.4+  

💡 Lưu ý: Chức năng thanh toán liên quan đến an toàn tài chính, vui lòng tuân thủ nghiêm ngặt quy chuẩn bảo mật và định kỳ thực hiện kiểm tra bảo mật (security audit).

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
