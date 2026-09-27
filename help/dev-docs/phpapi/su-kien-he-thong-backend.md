# Tài liệu mô tả sự kiện hệ thống backend CRMEB

## 1. Tổng quan về cơ chế sự kiện

Hệ thống backend CRMEB áp dụng kiến trúc hướng sự kiện (event-driven), dùng cơ chế sự kiện để tách rời (decouple) và giao tiếp giữa các module. Hệ thống sự kiện cho phép các module khác nhau tương tác với nhau và mở rộng logic nghiệp vụ mà không cần phụ thuộc trực tiếp vào nhau.

### 1.1 Khái niệm cốt lõi

- **Sự kiện (Event)**: Hành động cụ thể hoặc thay đổi trạng thái xảy ra trong hệ thống, như tạo đơn hàng, người dùng đăng ký, v.v.
- **Trình lắng nghe (Listener)**: Lớp lắng nghe sự kiện cụ thể và thực thi logic xử lý tương ứng.
- **Kích hoạt sự kiện (Trigger)**: Gọi sự kiện tại một điểm logic nghiệp vụ cụ thể, thông báo cho các listener liên quan thực thi.

## 2. Định nghĩa interface listener

Mọi listener đều phải triển khai interface `crmeb\interfaces\ListenerInterface`, interface này định nghĩa phương thức xử lý sự kiện thống nhất.

```php
<?php
namespace crmeb\interfaces;

interface ListenerInterface
{
    public function handle($event): void;
}
```

### 2.1 Mô tả interface

- **Phương thức handle**: Phương thức cốt lõi để xử lý sự kiện, nhận một tham số `$event` chứa dữ liệu được truyền khi sự kiện được kích hoạt.
- Kiểu trả về: `void`, listener không cần trả về giá trị sau khi xử lý xong.

## 3. Định nghĩa và đăng ký sự kiện

Sự kiện được định nghĩa và đăng ký thông qua tệp `app/event.php`, tệp này trả về một mảng chứa cấu hình sự kiện.

### 3.1 Cấu trúc cấu hình sự kiện

```php
return [
    'bind' => [], // Liên kết sự kiện
    'listen' => [ // Lắng nghe sự kiện
        'EventName' => [
            'Listener1::class',
            'Listener2::class',
        ],
    ],
];
```

### 3.2 Ví dụ đăng ký sự kiện

```php
return [
    'listen' => [
        'OrderCreateAfterListener' => [\app\listener\order\OrderCreateAfterListener::class], // Event sau khi tạo đơn hàng
        'UserRegisterListener' => [\app\listener\user\RegisterListener::class], // Event sau khi người dùng đăng ký
        // Các sự kiện khác...
    ],
];
```

## 4. Danh sách sự kiện tích hợp sẵn của hệ thống

### 4.1 Sự kiện cơ bản

| Tên sự kiện | Thời điểm kích hoạt | Lớp listener | Mô tả |
|---------|---------|---------|------|
| AppInit | Khởi tạo ứng dụng | - | Kích hoạt khi ứng dụng khởi động |
| HttpRun | Chạy yêu cầu HTTP | - | Kích hoạt khi bắt đầu xử lý yêu cầu HTTP |
| HttpEnd | Kết thúc request HTTP | \app\listener\http\HttpEndListener::class | Kích hoạt khi kết thúc xử lý yêu cầu HTTP |
| LogLevel | Cấp độ log | - | Kích hoạt khi thiết lập cấp độ ghi log |
| LogWrite | Ghi log | - | Kích hoạt khi ghi log |
| QueueStartListener | Khởi động hàng đợi | \app\listener\queue\QueueStartListener::class | Kích hoạt khi hàng đợi khởi động |

### 4.2 Sự kiện liên quan đến người dùng

| Tên sự kiện | Thời điểm kích hoạt | Lớp listener | Mô tả |
|---------|---------|---------|------|
| UserLoginListener | Người dùng đăng nhập | \app\listener\user\LoginListener::class | Kích hoạt sau khi người dùng đăng nhập thành công |
| AdminLoginListener | Đăng nhập quản trị viên | \app\listener\admin\AdminLoginListener::class | Kích hoạt sau khi quản trị viên đăng nhập thành công |
| UserRegisterListener | Người dùng đăng ký | \app\listener\user\RegisterListener::class | Kích hoạt sau khi người dùng đăng ký thành công |
| WechatAuthListener | Ủy quyền WeChat | \app\listener\wechat\AuthListener::class | Kích hoạt sau khi người dùng ủy quyền WeChat thành công |
| UserLevelListener | Nâng hạng người dùng | \app\listener\user\UserLevelListener::class | Kích hoạt khi người dùng được nâng hạng |
| UserVisitListener | Truy cập của người dùng | \app\listener\user\UserVisitListener::class | Kích hoạt khi người dùng truy cập trang |

### 4.3 Sự kiện liên quan đến đơn hàng

| Tên sự kiện | Thời điểm kích hoạt | Lớp listener | Mô tả |
|---------|---------|---------|------|
| OrderCreateAfterListener | Tạo đơn hàng | \app\listener\order\OrderCreateAfterListener::class | Kích hoạt sau khi tạo đơn hàng xong |
| OrderPaySuccessListener | Thanh toán đơn hàng thành công | \app\listener\order\OrderPaySuccessListener::class | Kích hoạt sau khi đơn hàng được thanh toán thành công |
| OrderDeliveryListener | Giao đơn hàng | \app\listener\order\OrderDeliveryListener::class | Kích hoạt sau khi giao hàng cho đơn hàng |
| OrderTakeListener | Xác nhận đã nhận hàng | \app\listener\order\OrderTakeListener::class | Kích hoạt sau khi đơn hàng được xác nhận đã nhận hàng |
| OrderRefundCreateAfterListener | Tạo đơn hậu mãi | \app\listener\order\OrderRefundCreateAfterListener::class | Kích hoạt sau khi tạo đơn hậu mãi xong |
| OrderRefundCancelAfterListener | Hủy đơn hậu mãi | \app\listener\order\OrderRefundCancelAfterListener::class | Kích hoạt sau khi đơn hậu mãi bị hủy |
| OrderShippingListener | Quản lý giao hàng Mini Program | \app\listener\order\OrderShippingListener::class | Kích hoạt khi giao hàng trên Mini Program |

### 4.4 Sự kiện khác

| Tên sự kiện | Thời điểm kích hoạt | Lớp listener | Mô tả |
|---------|---------|---------|------|
| OutPushListener | Đẩy dữ liệu ra bên ngoài | \app\listener\out\OutPushListener::class | Kích hoạt khi đẩy dữ liệu ra bên ngoài |
| NoticeListener | Tin nhắn thông báo | \app\listener\notice\NoticeListener::class | Kích hoạt khi gửi tin nhắn thông báo hệ thống |
| CustomNoticeListener | Thông báo tùy chỉnh | \app\listener\notice\CustomNoticeListener::class | Kích hoạt khi gửi tin nhắn tùy chỉnh |
| NotifyListener | Callback thanh toán bất đồng bộ | \app\listener\pay\NotifyListener::class | Kích hoạt khi xử lý callback bất đồng bộ của thanh toán |
| CustomEventListener | Sự kiện tùy chỉnh | \app\listener\CustomEventListener::class | Kích hoạt khi sự kiện nghiệp vụ tùy chỉnh được kích hoạt |

## 5. Cách kích hoạt sự kiện

Sự kiện được kích hoạt thông qua hàm `event()`, đây là hàm toàn cục do framework ThinkPHP cung cấp.

### 5.1 Cách kích hoạt cơ bản

```php
// Kích hoạt sự kiện
 event('EventName', $eventData);

// Ví dụ: Kích hoạt sự kiện tạo đơn hàng
 event('OrderCreateAfterListener', [$order, $group, $uid, $key]);
```

### 5.2 Truyền dữ liệu sự kiện

Khi kích hoạt sự kiện, có thể truyền dữ liệu thuộc bất kỳ kiểu nào, listener nhận dữ liệu qua tham số `$event`. Dữ liệu thường được truyền dưới dạng mảng, chứa mọi thông tin cần thiết để xử lý logic nghiệp vụ.

## 6. Sự kiện tùy chỉnh

Ngoài các sự kiện tích hợp sẵn của hệ thống, CRMEB còn hỗ trợ sự kiện tùy chỉnh, cho phép mở rộng nghiệp vụ một cách linh hoạt thông qua `CustomEventListener`.

### 6.1 Kích hoạt sự kiện tùy chỉnh

```php
// Kích hoạt sự kiện tùy chỉnh
 event('CustomEventListener', ['event_mark', ['data1' => 'value1', 'data2' => 'value2']]);
```

### 6.2 Xử lý sự kiện tùy chỉnh

Sự kiện tùy chỉnh được xử lý bởi `app\listener\CustomEventListener`, listener này lấy code tùy chỉnh đã cấu hình từ cơ sở dữ liệu và thực thi.

```php
<?php
namespace app\listener;

use app\services\system\SystemEventServices;
use think\facade\Log;

class CustomEventListener
{
    public function handle($event)
    {
        [$mark, $data] = $event;
        try {
            $list = app()->make(SystemEventServices::class)->selectList(['mark' => $mark, 'is_del' => 0, 'is_open' => 1])->toArray();
            foreach ($list as $item) {
                eval(json_decode($item['customCode']));
            }
        } catch (\Throwable $e) {
            $listener_log_open = config("log.listener_log", false);
            if ($listener_log_open) {
                $date = date('Y-m-d H:i:s', time());
                Log::write($date . 'Lỗi sự kiện tùy chỉnh:' . $e->getMessage(), 'listener');
            }
        }
    }
}
```

## 7. Quy trình xử lý sự kiện

1. **Kích hoạt sự kiện**: Kích hoạt sự kiện cụ thể trong logic nghiệp vụ thông qua hàm `event()`.
2. **Phân phối sự kiện**: Hệ thống tìm danh sách listener tương ứng dựa trên tên sự kiện.
3. **Thực thi listener**: Lần lượt gọi phương thức `handle()` của từng listener và truyền dữ liệu sự kiện.
4. **Xử lý nghiệp vụ**: Các listener thực thi logic nghiệp vụ riêng của mình, như xử lý dữ liệu, gửi tin nhắn, v.v.
5. **Hoàn tất xử lý**: Sau khi tất cả listener thực thi xong, quy trình xử lý sự kiện kết thúc.

## 8. Ví dụ triển khai listener

### 8.1 Listener sự kiện tạo đơn hàng

```php
<?php
namespace app\listener\order;

use app\jobs\notice\PrintJob;
use app\jobs\OrderCreateAfterJob;
use app\jobs\OrderJob;
use app\jobs\ProductLogJob;
use app\jobs\UnpaidOrderCancelJob;
use app\jobs\UnpaidOrderSend;
use app\services\order\StoreOrderCreateServices;
use app\services\order\StoreOrderStatusServices;
use crmeb\interfaces\ListenerInterface;
use crmeb\services\CacheService;
use crmeb\services\SystemConfigService;
use crmeb\utils\Arr;

/**
 * Event sau khi tạo đơn hàng
 * Class OrderCreateAfterListener
 * @package app\listener\order
 */
class OrderCreateAfterListener implements ListenerInterface
{
    public function handle($event): void
    {
        [$order, $group, $uid, $key, $combinationId, $seckillId, $bargainId] = $event;

        // Sau khi tạo dữ liệu đơn hàng: tính số tiền thực tế sản phẩm, tính hoa hồng, tính chiết khấu ưu đãi, thiết lập địa chỉ mặc định, dọn giỏ hàng
        /** @var StoreOrderCreateServices $orderCreate */
        $orderCreate = app()->make(StoreOrderCreateServices::class);
        $orderCreate->orderCreateAfter($order, $group, $combinationId || $seckillId || $bargainId);

        // Xóa bộ nhớ đệm đơn hàng
        CacheService::delete('user_order_' . $uid . $key);

        // Ghi vào bảng lịch sử đơn hàng
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        $statusService->save([
            'oid' => $order['id'],
            'change_type' => 'cache_key_create_order',
            'change_message' => 'Tạo đơn hàng',
            'change_time' => time()
        ]);

        // Tự động hủy đơn hàng
        $this->pushJob($order['id'], $combinationId, $seckillId, $bargainId);

        // Lịch sử đặt hàng
        ProductLogJob::dispatch(['order', ['uid' => $uid, 'order_id' => $order['id']]]);

        // In biên lai
        PrintJob::dispatch([$order['id'], 2]);
    }

    /**
     * Thêm hủy đơn hàng tự động vào hàng đợi tin nhắn trễ
     * @param int $orderId
     * @param int $combinationId
     * @param int $seckillId
     * @param int $bargainId
     * @return mixed
     */
    public function pushJob(int $orderId, int $combinationId, int $seckillId, int $bargainId)
    {
        // Khoảng thời gian hủy đơn hàng do hệ thống định trước
        $keyValue = ['order_cancel_time', 'order_activity_time', 'order_bargain_time', 'order_seckill_time', 'order_pink_time'];
        // Lấy cấu hình
        $systemValue = SystemConfigService::more($keyValue);
        // Định dạng dữ liệu
        $systemValue = Arr::setValeTime($keyValue, is_array($systemValue) ? $systemValue : []);
        if ($combinationId) {
            $secs = $systemValue['order_pink_time'] ?: $systemValue['order_activity_time'];
        } elseif ($seckillId) {
            $secs = $systemValue['order_seckill_time'] ?: $systemValue['order_activity_time'];
        } elseif ($bargainId) {
            $secs = $systemValue['order_bargain_time'] ?: $systemValue['order_activity_time'];
        } else {
            $secs = $systemValue['order_cancel_time'];
        }
        // Gửi SMS sau 10 phút chưa thanh toán
        UnpaidOrderSend::dispatchSecs(600, [$orderId]);
        // Chưa thanh toán thì hủy đơn hàng theo event cấu hình hệ thống
        UnpaidOrderCancelJob::dispatchSecs((int)($secs * 3600), [$orderId]);
    }
}
```

### 8.2 Listener sự kiện người dùng đăng ký

```php
<?php
namespace app\listener\user;

use app\jobs\AgentJob;
use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\user\UserBillServices;
use app\services\user\UserServices;
use app\services\user\UserSpreadServices;
use crmeb\interfaces\ListenerInterface;

/**
 * Event sau khi hoàn tất đăng ký
 * Class RegisterListener
 * @package app\listener\user
 */
class RegisterListener implements ListenerInterface
{
    /**
     * Event sau khi hoàn tất đăng ký
     * @param $event
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function handle($event): void
    {
        [$spreadUid, $userType, $name, $uid, $isNew] = $event;

        if ($spreadUid) {
            if ($isNew) {
                // Tăng điểm kinh nghiệm khi mời người dùng mới
                /** @var UserBillServices $userBill */
                $userBill = app()->make(UserBillServices::class);
                $userBill->inviteUserIncExp((int)$spreadUid);
                // Tăng hoa hồng giới thiệu
                /** @var UserServices $userServices */
                $userServices = app()->make(UserServices::class);
                $userServices->addBrokeragePrice($uid, $spreadUid);

                // Giới thiệu người mới, xử lý nâng hạng cộng tác viên cho bản thân và cấp trên
                AgentJob::dispatch([$uid]);
            }
            // Ghi lại quan hệ liên kết giới thiệu
            /** @var UserSpreadServices $userSpreadServices */
            $userSpreadServices = app()->make(UserSpreadServices::class);
            $res = $userSpreadServices->setSpread($uid, $spreadUid);

            // Tin nhắn tùy chỉnh - liên kết cấp dưới thành công
            if ($res) {
                $phone = app()->make(UserServices::class)->value($spreadUid, 'phone');
                event('CustomNoticeListener', [$spreadUid, ['nickname' => $name, 'time' => date('Y-m-d H:i:s'), 'phone' => $phone], 'spread_success']);
            }
        }

        if ($isNew) {
            // Gửi phiếu giảm giá cho người mới
            /**@var StoreCouponIssueServices $storeCoupon */
            $storeCoupon = app()->make(StoreCouponIssueServices::class);
            $storeCoupon->userFirstSubGiveCoupon((int)$uid);

            // Mở quyền giới thiệu khi ai cũng có thể làm cộng tác viên
            if (sys_config('brokerage_func_status') && sys_config('store_brokerage_statu') == 2) {
                /** @var UserServices $userServices */
                $userServices = app()->make(UserServices::class);
                $userServices->update($uid, ['is_promoter' => 1]);
            }
        }
    }
}
```

## 9. Thực tiễn tốt nhất

### 9.1 Quy tắc đặt tên sự kiện

- Tên sự kiện cần thể hiện rõ ý nghĩa và thời điểm kích hoạt của sự kiện, ví dụ `OrderCreateAfterListener` biểu thị sự kiện sau khi tạo đơn hàng.
- Đặt tên theo kiểu camelCase, viết hoa chữ cái đầu.

### 9.2 Nguyên tắc thiết kế listener

- **Đơn trách nhiệm**: Mỗi listener chỉ nên chịu trách nhiệm xử lý một logic nghiệp vụ cụ thể.
- **Không có tác dụng phụ**: Listener không nên sửa đổi dữ liệu sự kiện gốc khi xử lý, tránh ảnh hưởng đến các listener khác.
- **Xử lý ngoại lệ**: Bắt ngoại lệ phù hợp trong listener, tránh để một listener thất bại ảnh hưởng đến toàn bộ quy trình xử lý sự kiện.

### 9.3 Tối ưu hiệu năng

- **Xử lý bất đồng bộ**: Với các thao tác tốn thời gian, nên dùng hàng đợi để xử lý bất đồng bộ, như gửi SMS, tạo báo cáo, v.v.
- **Sử dụng bộ nhớ đệm**: Sử dụng hợp lý bộ nhớ đệm (cache) để giảm truy vấn cơ sở dữ liệu, nâng cao hiệu suất xử lý.
- **Tránh phụ thuộc vòng**: Các listener không nên phụ thuộc lẫn nhau, tránh hình thành lời gọi vòng.

## 10. Các vấn đề thường gặp và giải pháp

### 10.1 Sự kiện không được kích hoạt

**Vấn đề**: Sau khi gọi hàm `event()`, listener không được thực thi.

**Cách khắc phục**:
- Kiểm tra tên sự kiện có khớp với tên đã đăng ký trong `event.php` hay không.
- Kiểm tra lớp listener đã triển khai đúng interface `ListenerInterface` hay chưa.
- Kiểm tra lớp listener đã được đăng ký đúng vào sự kiện tương ứng hay chưa.

### 10.2 Thứ tự thực thi listener

**Vấn đề**: Cần kiểm soát thứ tự thực thi của các listener.

**Cách khắc phục**:
- Trong `event.php`, thứ tự đăng ký listener quyết định thứ tự thực thi.
- Nếu cần kiểm soát phức tạp hơn, có thể kích hoạt thủ công các sự kiện khác trong listener.

### 10.3 Truyền dữ liệu sự kiện

**Vấn đề**: Listener không lấy được đầy đủ dữ liệu sự kiện.

**Cách khắc phục**:
- Đảm bảo đã truyền đầy đủ dữ liệu cần thiết khi kích hoạt sự kiện.
- Truyền dữ liệu dưới dạng mảng để dễ mở rộng và bảo trì.
- Kiểm tra và chuyển đổi dữ liệu một cách phù hợp trong listener.

## 11. Tổng kết

Hệ thống sự kiện của CRMEB cung cấp một cơ chế giao tiếp linh hoạt, tách rời (decoupled) giữa các module, thông qua sự phối hợp giữa sự kiện và listener để tách biệt và mở rộng logic nghiệp vụ. Sử dụng hợp lý hệ thống sự kiện có thể nâng cao khả năng bảo trì và khả năng mở rộng của code, đồng thời giảm mức độ phụ thuộc (coupling) giữa các module.

Trong quá trình phát triển, cần tuân thủ quy chuẩn đặt tên sự kiện, nguyên tắc thiết kế listener và các khuyến nghị tối ưu hiệu năng, để đảm bảo hệ thống sự kiện vận hành hiệu quả.
---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
