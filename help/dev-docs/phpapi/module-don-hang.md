# Mô tả module đơn hàng

## 📦 Luồng chuyển đổi trạng thái đơn hàng

### Các trạng thái cốt lõi của đơn hàng
```php
const ORDER_STATUS_UNPAID = 0;       // Chưa thanh toán
const ORDER_STATUS_PAID = 1;         // Đã thanh toán
const ORDER_STATUS_SHIPPED = 2;      // Đã giao hàng
const ORDER_STATUS_COMPLETED = 3;    // Đã hoàn thành
const ORDER_STATUS_CANCELLED = -1;   // Đã hủy
const ORDER_STATUS_REFUNDING = -2;   // Đang hoàn tiền
const ORDER_STATUS_REFUNDED = -3;    // Đã hoàn tiền
```

### Trạng thái thanh toán
```php
const PAY_STATUS_UNPAID = 0;         // Chưa thanh toán
const PAY_STATUS_PAID = 1;           // Đã thanh toán
const PAY_STATUS_REFUNDING = 2;      // Đang hoàn tiền
const PAY_STATUS_REFUNDED = 3;       // Đã hoàn tiền
```

## 🔄 Quy trình tạo đơn hàng

### 1. Xác thực tham số
```php
// Xác thực địa chỉ nhận hàng, thông tin sản phẩm, phiếu giảm giá, v.v.
$validate = new OrderValidate();
if (!$validate->scene('create')->check($params)) {
    throw new ApiException($validate->getError());
}
```

### 2. Kiểm tra sản phẩm
```php
// Kiểm tra tồn kho, giá, trạng thái sản phẩm
foreach ($cartInfo as $item) {
    $product = $productService->getProductInfo($item['product_id']);
    if ($product['stock'] < $item['cart_num']) {
        throw new ApiException('Sản phẩm không đủ tồn kho');
    }
}
```

### 3. Tính toán ưu đãi
```php
// Tính phiếu giảm giá, điểm thưởng, giảm giá theo đơn tối thiểu, v.v.
$couponAmount = $couponService->calculateCoupon($uid, $cartInfo);
$integralAmount = $integralService->calculateIntegral($uid, $totalAmount);
$finalAmount = $totalAmount - $couponAmount - $integralAmount;
```

### 4. Tạo đơn hàng
```php
// Tạo dữ liệu đơn hàng
$orderData = [
    'order_id' => $this->getNewOrderId(),
    'uid' => $uid,
    'total_price' => $totalAmount,
    'pay_price' => $finalAmount,
    'status' => self::ORDER_STATUS_UNPAID,
    'add_time' => time()
];

// Lưu đơn hàng
$orderId = $this->dao->save($orderData);
```

## 💳 Xử lý thanh toán

### Phương thức thanh toán
```php
const PAY_TYPE_WECHAT = 'wechat';    // WeChat Pay
const PAY_TYPE_ALIPAY = 'alipay';    // Alipay
const PAY_TYPE_BALANCE = 'balance';  // Thanh toán bằng số dư
const PAY_TYPE_INTEGRAL = 'integral'; // Thanh toán bằng điểm thưởng
```

### Xử lý callback thanh toán
```php
public function payNotify($params) {
    // Xác thực kết quả thanh toán
    if ($this->verifyPayResult($params)) {
        // Cập nhật trạng thái đơn hàng
        $this->updateOrderStatus($params['order_id'], self::ORDER_STATUS_PAID);

        // Ghi nhận giao dịch thanh toán
        $this->savePayRecord($params);

        // Gửi thông báo thanh toán thành công
        $this->sendPaySuccessNotify($params['order_id']);
    }
}
```

## 📦 Xử lý giao hàng

### Quy trình giao hàng
```php
public function deliverOrder($orderId, $expressData) {
    // Kiểm tra trạng thái đơn hàng
    $order = $this->getOrderInfo($orderId);
    if ($order['status'] != self::ORDER_STATUS_PAID) {
        throw new ApiException('Trạng thái đơn hàng không cho phép giao hàng');
    }

    // Tạo phiếu giao hàng
    $deliverData = [
        'order_id' => $orderId,
        'express_code' => $expressData['code'],
        'express_number' => $expressData['number'],
        'deliver_time' => time()
    ];

    // Cập nhật trạng thái đơn hàng
    $this->updateOrderStatus($orderId, self::ORDER_STATUS_SHIPPED);

    // Gửi thông báo giao hàng
    $this->sendDeliverNotify($orderId);
}
```

## 🔄 Xử lý hậu mãi

### Yêu cầu hoàn tiền
```php
public function applyRefund($orderId, $reason, $amount) {
    // Kiểm tra đơn hàng có thể hoàn tiền không
    if (!$this->canRefund($orderId)) {
        throw new ApiException('Đơn hàng này không thể hoàn tiền');
    }

    // Tạo bản ghi hoàn tiền
    $refundData = [
        'order_id' => $orderId,
        'refund_amount' => $amount,
        'refund_reason' => $reason,
        'status' => self::REFUND_STATUS_APPLY
    ];

    // Cập nhật trạng thái đơn hàng
    $this->updateOrderStatus($orderId, self::ORDER_STATUS_REFUNDING);
}
```

### Duyệt hoàn tiền
```php
public function auditRefund($refundId, $status, $remark = '') {
    $refund = $this->getRefundInfo($refundId);

    if ($status == self::REFUND_STATUS_APPROVED) {
        // Thực hiện hoàn tiền
        $this->doRefund($refund);

        // Cập nhật trạng thái đơn hàng
        $this->updateOrderStatus($refund['order_id'], self::ORDER_STATUS_REFUNDED);
    } else {
        // Từ chối hoàn tiền
        $this->updateRefundStatus($refundId, self::REFUND_STATUS_REJECTED, $remark);

        // Khôi phục trạng thái đơn hàng
        $this->recoverOrderStatus($refund['order_id']);
    }
}
```

## 📊 Thống kê đơn hàng

### Các phương thức thống kê thường dùng
```php
// Thống kê đơn hàng hôm nay
public function getTodayOrderStats() {
    $todayStart = strtotime('today');
    $todayEnd = strtotime('tomorrow') - 1;

    return [
        'total' => $this->dao->where('add_time', 'between', [$todayStart, $todayEnd])->count(),
        'amount' => $this->dao->where('add_time', 'between', [$todayStart, $todayEnd])->sum('pay_price'),
        'paid' => $this->dao->where('add_time', 'between', [$todayStart, $todayEnd])
                          ->where('status', '>', 0)->count()
    ];
}
```

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
