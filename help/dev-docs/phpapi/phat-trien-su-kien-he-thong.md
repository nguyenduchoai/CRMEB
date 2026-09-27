# Tài liệu phát triển sự kiện hệ thống CRMEB

## 1. Tổng quan về cơ chế sự kiện

Hệ thống backend CRMEB áp dụng **kiến trúc hướng sự kiện (event-driven)**, dùng cơ chế sự kiện để tách rời (decouple) và giao tiếp giữa các module. Hệ thống sự kiện cho phép các module khác nhau tương tác với nhau và mở rộng logic nghiệp vụ mà không cần phụ thuộc trực tiếp vào nhau.

### 1.1 Khái niệm cốt lõi

- **Sự kiện (Event)**: Hành động hoặc thay đổi trạng thái cụ thể xảy ra trong hệ thống, như tạo đơn hàng, người dùng đăng ký, v.v.
- **Trình lắng nghe (Listener)**: Lớp lắng nghe một sự kiện cụ thể và thực thi logic xử lý tương ứng
- **Kích hoạt sự kiện (Trigger)**: Gọi sự kiện tại một điểm cụ thể trong logic nghiệp vụ để thông báo cho các listener liên quan thực thi

## 2. Kiến trúc hệ thống sự kiện

### 2.1 Định nghĩa interface của listener

Mọi listener đều phải triển khai interface `crmeb\interfaces\ListenerInterface`, interface này định nghĩa phương thức xử lý sự kiện thống nhất:

```php
<?php
namespace crmeb\interfaces;

interface ListenerInterface
{
    public function handle($event): void;
}
```

### 2.2 Cấu hình và đăng ký sự kiện

Sự kiện được định nghĩa và đăng ký thông qua tệp `app/event.php`, tệp này trả về một mảng chứa cấu hình sự kiện:

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

**Cấu hình mẫu**:
```php
return [
    'listen' => [
        'HttpEnd' => [\app\listener\http\HttpEndListener::class], //Event callback khi kết thúc request HTTP
        'UserRegisterListener' => [\app\listener\user\RegisterListener::class], //Event sau khi người dùng đăng ký
        'OrderCreateAfterListener' => [\app\listener\order\OrderCreateAfterListener::class], //Event sau khi tạo đơn hàng
        // Các sự kiện khác...
    ],
];
```

## 3. Sự kiện tích hợp sẵn của hệ thống

Hệ thống CRMEB tích hợp sẵn nhiều sự kiện, bao quát mọi khía cạnh trong quá trình vận hành hệ thống:

| Loại sự kiện | Tên sự kiện | Thời điểm kích hoạt | Lớp listener |
|---------|---------|---------|---------|
| **Sự kiện cơ bản** | HttpEnd | Kết thúc request HTTP | \app\listener\http\HttpEndListener::class |
| | QueueStartListener | Khởi động hàng đợi | \app\listener\queue\QueueStartListener::class |
| **Liên quan đến người dùng** | UserLoginListener | Người dùng đăng nhập | \app\listener\user\LoginListener::class |
| | AdminLoginListener | Đăng nhập quản trị viên | \app\listener\admin\AdminLoginListener::class |
| | UserRegisterListener | Người dùng đăng ký | \app\listener\user\RegisterListener::class |
| | WechatAuthListener | Ủy quyền WeChat | \app\listener\wechat\AuthListener::class |
| | UserLevelListener | Nâng hạng người dùng | \app\listener\user\UserLevelListener::class |
| | UserVisitListener | Truy cập của người dùng | \app\listener\user\UserVisitListener::class |
| **Liên quan đến đơn hàng** | OrderCreateAfterListener | Tạo đơn hàng | \app\listener\order\OrderCreateAfterListener::class |
| | OrderPaySuccessListener | Thanh toán đơn hàng thành công | \app\listener\order\OrderPaySuccessListener::class |
| | OrderDeliveryListener | Giao đơn hàng | \app\listener\order\OrderDeliveryListener::class |
| | OrderTakeListener | Xác nhận đã nhận hàng | \app\listener\order\OrderTakeListener::class |
| | OrderRefundCreateAfterListener | Tạo đơn hậu mãi | \app\listener\order\OrderRefundCreateAfterListener::class |
| | OrderRefundCancelAfterListener | Hủy đơn hậu mãi | \app\listener\order\OrderRefundCancelAfterListener::class |
| **Sự kiện khác** | OutPushListener | Đẩy dữ liệu ra bên ngoài | \app\listener\out\OutPushListener::class |
| | NoticeListener | Tin nhắn thông báo | \app\listener\notice\NoticeListener::class |
| | CustomEventListener | Sự kiện tùy chỉnh | \app\listener\CustomEventListener::class |

## 4. Kích hoạt và xử lý sự kiện

### 4.1 Cách kích hoạt sự kiện

Sự kiện được kích hoạt thông qua hàm `event()`, đây là hàm toàn cục do framework ThinkPHP cung cấp:

```php
// Kích hoạt sự kiện
event('EventName', $eventData);

// Ví dụ: Kích hoạt sự kiện tạo đơn hàng
event('OrderCreateAfterListener', [$order, $group, $uid, $key, $combinationId, $seckillId, $bargainId]);
```

### 4.2 Quy trình xử lý sự kiện

1. **Kích hoạt sự kiện**: Kích hoạt một sự kiện cụ thể trong logic nghiệp vụ thông qua hàm `event()`
2. **Phân phối sự kiện**: Hệ thống tìm danh sách listener tương ứng dựa trên tên sự kiện
3. **Thực thi listener**: Lần lượt gọi phương thức `handle()` của từng listener và truyền dữ liệu sự kiện
4. **Xử lý nghiệp vụ**: Các listener thực thi logic nghiệp vụ riêng của mình, như xử lý dữ liệu, gửi tin nhắn, v.v.
5. **Hoàn tất xử lý**: Sau khi tất cả listener thực thi xong, quy trình xử lý sự kiện kết thúc

## 5. Triển khai listener

### 5.1 Các bước tạo listener

1. **Tạo lớp listener**: Tạo lớp listener tương ứng trong thư mục `app/listener/`
2. **Triển khai interface**: Triển khai interface `crmeb\interfaces\ListenerInterface`
3. **Viết logic xử lý**: Viết logic xử lý sự kiện trong phương thức `handle()`
4. **Đăng ký listener**: Đăng ký listener trong tệp `app/event.php`

### 5.2 Ví dụ triển khai listener

#### 5.2.1 Listener sự kiện tạo đơn hàng

```php
<?php
namespace app\listener\order;

use app\jobs\notice\PrintJob;
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

#### 5.2.2 Listener sự kiện người dùng đăng ký

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

## 6. Sự kiện tùy chỉnh

CRMEB hỗ trợ sự kiện tùy chỉnh, cho phép mở rộng nghiệp vụ linh hoạt thông qua `CustomEventListener`:

### 6.1 Kích hoạt sự kiện tùy chỉnh

```php
// Kích hoạt sự kiện tùy chỉnh
event('CustomEventListener', ['event_mark', ['data1' => 'value1', 'data2' => 'value2']]);
```

### 6.2 Xử lý sự kiện tùy chỉnh

Sự kiện tùy chỉnh được xử lý bởi `app\listener\CustomEventListener`, listener này lấy code tùy chỉnh đã cấu hình từ cơ sở dữ liệu và thực thi:

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

### 6.3 Cấu hình sự kiện tùy chỉnh

Sự kiện tùy chỉnh có thể được cấu hình qua hệ thống quản trị, thêm và quản lý sự kiện tùy chỉnh tại “Cài đặt hệ thống” -> “Quản lý sự kiện”.

## 7. Thực tiễn tốt nhất

### 7.1 Quy tắc đặt tên sự kiện

- Tên sự kiện cần thể hiện rõ ý nghĩa và thời điểm kích hoạt của sự kiện, ví dụ `OrderCreateAfterListener` biểu thị sự kiện sau khi tạo đơn hàng
- Dùng cách đặt tên kiểu camelCase, viết hoa chữ cái đầu

### 7.2 Nguyên tắc thiết kế listener

- **Đơn trách nhiệm**: Mỗi listener chỉ nên chịu trách nhiệm xử lý một logic nghiệp vụ cụ thể
- **Không gây tác dụng phụ**: Quá trình xử lý của listener không được sửa đổi dữ liệu sự kiện gốc, tránh ảnh hưởng đến các listener khác
- **Xử lý ngoại lệ**: Bắt ngoại lệ hợp lý trong listener, tránh để một listener bị lỗi ảnh hưởng đến toàn bộ quy trình xử lý sự kiện

### 7.3 Tối ưu hiệu năng

- **Xử lý bất đồng bộ**: Với các thao tác tốn thời gian, nên dùng hàng đợi để xử lý bất đồng bộ, như gửi SMS, tạo báo cáo, v.v.
- **Sử dụng bộ nhớ đệm**: Sử dụng bộ nhớ đệm (cache) hợp lý để giảm truy vấn cơ sở dữ liệu, nâng cao hiệu quả xử lý
- **Tránh phụ thuộc vòng**: Các listener không nên phụ thuộc lẫn nhau, tránh hình thành lời gọi vòng

## 8. Các vấn đề thường gặp và giải pháp

### 8.1 Sự kiện không được kích hoạt

**Vấn đề**: Sau khi gọi hàm `event()`, listener không được thực thi.

**Cách khắc phục**:
- Kiểm tra tên sự kiện có khớp với tên đã đăng ký trong `event.php` hay không
- Kiểm tra lớp listener đã triển khai đúng interface `ListenerInterface` hay chưa
- Kiểm tra lớp listener đã được đăng ký đúng vào sự kiện tương ứng hay chưa

### 8.2 Thứ tự thực thi listener

**Vấn đề**: Cần kiểm soát thứ tự thực thi của các listener.

**Cách khắc phục**:
- Trong `event.php`, thứ tự đăng ký listener quyết định thứ tự thực thi
- Nếu cần kiểm soát phức tạp hơn, có thể kích hoạt thủ công các sự kiện khác trong listener

### 8.3 Truyền dữ liệu sự kiện

**Vấn đề**: Listener không lấy được đầy đủ dữ liệu sự kiện.

**Cách khắc phục**:
- Đảm bảo đã truyền tất cả dữ liệu cần thiết khi kích hoạt sự kiện
- Truyền dữ liệu dưới dạng mảng để dễ mở rộng và bảo trì
- Xác thực và chuyển đổi dữ liệu phù hợp trong listener

## 9. Đề xuất tối ưu code

### 9.1 Tối ưu xử lý ngoại lệ trong listener

**Vấn đề**: Một số listener có thể thiếu cơ chế xử lý ngoại lệ hoàn chỉnh, khiến một listener bị lỗi ảnh hưởng đến toàn bộ quy trình xử lý sự kiện.

**Đề xuất**: Thêm khối try-catch trong phương thức `handle()` của listener để bắt và ghi log ngoại lệ, đảm bảo dù một listener nào đó bị lỗi cũng không ảnh hưởng đến việc thực thi các listener khác.

```php
public function handle($event): void
{
    try {
        // Logic của listener
    } catch (\Exception $e) {
        // Ghi log ngoại lệ
        Log::error('Listener error: ' . $e->getMessage(), [
            'listener' => __CLASS__,
            'event' => $event,
            'trace' => $e->getTraceAsString()
        ]);
        // Có thể thêm logic xử lý khác tùy theo nhu cầu
    }
}
```

### 9.2 Tối ưu xác thực dữ liệu sự kiện

**Vấn đề**: Listener phụ thuộc nhiều vào dữ liệu sự kiện nhưng thiếu bước xác thực tính hợp lệ của dữ liệu, có thể gây lỗi khi chạy (runtime).

**Đề xuất**: Thêm logic xác thực dữ liệu trong listener, đảm bảo dữ liệu nhận được đúng định dạng và yêu cầu mong đợi.

```php
public function handle($event): void
{
    // Xác thực dữ liệu sự kiện
    if (!is_array($event) || count($event) < 4) {
        Log::warning('Invalid event data', [
            'listener' => __CLASS__,
            'event' => $event
        ]);
        return;
    }
    
    [$order, $group, $uid, $key] = $event;
    
    // Xác thực các tham số bắt buộc
    if (empty($order['id']) || empty($uid)) {
        Log::warning('Missing required parameters', [
            'listener' => __CLASS__,
            'order_id' => $order['id'] ?? null,
            'uid' => $uid
        ]);
        return;
    }
    
    // Logic của listener
}
```

### 9.3 Tối ưu điểm kích hoạt sự kiện

**Vấn đề**: Các điểm kích hoạt sự kiện có thể quá phân tán, gây khó khăn cho việc truy vết vị trí và thời điểm kích hoạt sự kiện.

**Đề xuất**: Xây dựng tài liệu hoặc quy chuẩn chú thích cho các điểm kích hoạt sự kiện, ghi rõ vị trí, thời điểm kích hoạt và cấu trúc dữ liệu được truyền của từng sự kiện, thuận tiện cho việc bảo trì và mở rộng về sau.

```php
/**
 * Tạo đơn hàng
 * @param array $data Dữ liệu đơn hàng
 * @return array Thông tin đơn hàng
 * @event OrderCreateAfterListener Kích hoạt sau khi tạo đơn hàng, tham số truyền vào:[$order, $group, $uid, $key, $combinationId, $seckillId, $bargainId]
 */
public function createOrder(array $data)
{
    // Logic tạo đơn hàng
    
    // Kích hoạt sự kiện
    event('OrderCreateAfterListener', [$order, $group, $uid, $key, $combinationId, $seckillId, $bargainId]);
    
    return $order;
}
```

## 10. Ví dụ đầu vào và đầu ra

### 10.1 Kích hoạt sự kiện tạo đơn hàng

**Đầu vào**:
```php
// Kích hoạt sự kiện sau khi tạo đơn hàng xong
$order = [
    'id' => 1001,
    'order_sn' => '2023120100001',
    'uid' => 101,
    'total_price' => 99.99,
    // Thông tin đơn hàng khác
];
$group = [
    // Thông tin nhóm sản phẩm của đơn hàng
];
$uid = 101;
$key = 'order_cache_key';
$combinationId = 0;
$seckillId = 0;
$bargainId = 0;

// Kích hoạt sự kiện
event('OrderCreateAfterListener', [$order, $group, $uid, $key, $combinationId, $seckillId, $bargainId]);
```

**Đầu ra**:
```
// Sau khi sự kiện được kích hoạt, listener thực hiện các thao tác sau:
1. Tính số tiền thực tế của sản phẩm, hoa hồng, chiết khấu ưu đãi
2. Đặt địa chỉ mặc định
3. Dọn giỏ hàng
4. Xóa bộ nhớ đệm đơn hàng
5. Ghi vào bảng lịch sử đơn hàng
6. Thêm vào hàng đợi trễ để tự động hủy đơn hàng
7. Ghi nhận hành vi đặt hàng
8. Kích hoạt in biên lai
```

### 10.2 Kích hoạt sự kiện người dùng đăng ký

**Đầu vào**:
```php
// Kích hoạt sự kiện sau khi người dùng đăng ký xong
$spreadUid = 201; // ID người giới thiệu
$userType = 1; // Loại người dùng
$name = 'Nguyễn Văn A'; // Tên người dùng
$uid = 301; // ID người dùng mới
$isNew = true; // Có phải người dùng mới không

// Kích hoạt sự kiện
event('UserRegisterListener', [$spreadUid, $userType, $name, $uid, $isNew]);
```

**Đầu ra**:
```
// Sau khi sự kiện được kích hoạt, listener thực hiện các thao tác sau:
1. Mời người dùng mới thì cộng điểm kinh nghiệm cho người giới thiệu
2. Cộng hoa hồng giới thiệu cho người giới thiệu
3. Xử lý nâng hạng CTV
4. Ghi lại quan hệ liên kết giới thiệu
5. Gửi thông báo liên kết cấp dưới thành công
6. Gửi phiếu giảm giá cho người mới
7. Mở quyền giới thiệu (nếu đã bật chế độ ai cũng có thể làm CTV)
```

## 11. Tổng kết

Hệ thống sự kiện của CRMEB cung cấp một cơ chế giao tiếp giữa các module linh hoạt và tách rời (decoupled), thông qua sự phối hợp giữa sự kiện và listener để tách biệt và mở rộng logic nghiệp vụ. Sử dụng hệ thống sự kiện hợp lý có thể:

1. **Nâng cao khả năng bảo trì của code**: Tập trung logic nghiệp vụ liên quan vào listener, thuận tiện cho việc quản lý và bảo trì
2. **Tăng khả năng mở rộng của hệ thống**: Nhờ cơ chế sự kiện, có thể dễ dàng thêm logic nghiệp vụ mới mà không cần sửa code hiện có
3. **Giảm mức độ phụ thuộc (coupling) giữa các module**: Các module giao tiếp với nhau thông qua sự kiện thay vì phụ thuộc trực tiếp, giảm sự ràng buộc giữa các module
4. **Tăng tốc độ phản hồi của hệ thống**: Với các thao tác tốn thời gian, có thể xử lý bất đồng bộ qua hàng đợi để tăng tốc độ phản hồi của hệ thống

Trong quá trình phát triển, cần tuân thủ quy tắc đặt tên sự kiện, nguyên tắc thiết kế listener và các đề xuất tối ưu hiệu năng để đảm bảo hệ thống sự kiện vận hành hiệu quả. Tận dụng hợp lý cơ chế sự kiện có thể nâng cao đáng kể chất lượng code và hiệu quả phát triển, tạo nền tảng vững chắc cho việc bảo trì và mở rộng hệ thống lâu dài.