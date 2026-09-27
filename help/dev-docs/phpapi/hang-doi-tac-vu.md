# Tài liệu về tác vụ hàng đợi của hệ thống CRMEB

## Tổng quan

Hệ thống CRMEB sử dụng cơ chế hàng đợi tin nhắn dựa trên Redis, thực hiện xử lý tác vụ bất đồng bộ thông qua thành phần hàng đợi của framework ThinkPHP. Hệ thống hàng đợi đóng vai trò quan trọng trong việc nâng cao hiệu năng hệ thống, tối ưu trải nghiệm người dùng và đảm bảo tính nhất quán của dữ liệu.

## Kiến trúc hệ thống hàng đợi

### 1. Công nghệ sử dụng
- **Driver hàng đợi**: Redis (chính) + Database (dự phòng) + Sync (đồng bộ)
- **Dịch vụ hàng đợi**: ThinkPHP Queue
- **Tác vụ định kỳ**: Workerman + Crontab
- **Điều phối tác vụ**: Xử lý bất đồng bộ dựa trên sự kiện

### 2. Thành phần cốt lõi

#### 2.1 Cấu hình hàng đợi (config/queue.php)
```php
return [
    'default'     => 'redis',           // Driver mặc định
    'prefix'      => 'crmeb_',          // Tiền tố hàng đợi
    'connections' => [
        'redis' => [
            'driver'     => 'redis',
            'queue'      => 'CRMEB',     // Tên hàng đợi
            'host'       => '127.0.0.1',
            'port'       => 6379,
            'password'   => '',
            'select'     => 0,
        ],
    ],
    'failed' => [
        'type'  => 'database',
        'table' => 'failed_jobs',       // Bảng tác vụ thất bại
    ],
];
```

#### 2.2 Lớp tác vụ cơ sở (BaseJobs)
```php
abstract class BaseJobs implements JobInterface
{
    // Điểm vào thực thi tác vụ
    public function fire(Job $job, $data): void
    
    // Cơ chế thử lại tác vụ
    protected function runJob(string $action, Job $job, array $infoData, int $errorCount = 3)
}
```

#### 2.3 Trait hàng đợi (QueueTrait)
```php
trait QueueTrait
{
    // Đẩy tác vụ ngay lập tức
    public static function dispatch($action, array $data = [], string $queueName = null)
    
    // Đẩy tác vụ có độ trễ
    public static function dispatchSecs(int $secs, $action, array $data = [], string $queueName = null)
}
```

## Phân loại tác vụ hàng đợi

### 1. Tác vụ liên quan đến đơn hàng

#### 1.1 OrderJob - Tác vụ xử lý đơn hàng
**Chức năng**: Xử lý tiếp theo sau khi đơn hàng thanh toán thành công
**Thời điểm kích hoạt**: Khi đơn hàng thanh toán thành công
**Xử lý chính**:
- Tính số tiền tiết kiệm được của sản phẩm
- Cập nhật số đơn hàng đã thanh toán của người dùng
- Kiểm tra và thiết lập tư cách người giới thiệu
- Gắn nhãn mua hàng cho người dùng
- Gửi tin nhắn thông báo cho CSKH
- Kiểm tra nâng hạng thành viên
- Gửi tin nhắn đơn hàng mới đến trang quản trị

```php
// Ví dụ sử dụng
OrderJob::dispatch('doJob', [$orderInfo]);
```

#### 1.2 OrderCreateAfterJob - Xử lý sau khi tạo đơn hàng
**Chức năng**: Xử lý dữ liệu sau khi tạo đơn hàng
**Thời điểm kích hoạt**: Sau khi tạo đơn hàng xong
**Xử lý chính**:
- Tính giá thực tế của đơn hàng
- Xử lý quan hệ giới thiệu
- Tính toán phân bổ hoa hồng
- Cập nhật thông tin bổ sung của đơn hàng

#### 1.3 UnpaidOrderCancelJob - Hủy đơn hàng chưa thanh toán
**Chức năng**: Tự động hủy các đơn hàng chưa thanh toán quá thời hạn
**Thời điểm kích hoạt**: Tác vụ định kỳ hoặc hàng đợi trễ
**Logic xử lý**:
- Kiểm tra trạng thái thanh toán của đơn hàng
- Hoàn lại điểm thưởng và phiếu giảm giá
- Khôi phục tồn kho sản phẩm
- Đánh dấu đơn hàng là đã hủy

```php
// Thực thi sau khi trễ 30 phút
UnpaidOrderCancelJob::dispatchSecs(1800, 'doJob', [$orderId]);
```

### 2. Tác vụ liên quan đến sản phẩm

#### 2.1 ProductLogJob - Ghi nhật ký sản phẩm
**Chức năng**: Ghi nhật ký hành vi liên quan đến sản phẩm
**Thời điểm kích hoạt**: Xem sản phẩm, thêm vào giỏ hàng, đặt hàng, v.v.
**Loại nhật ký**:
- `visit`: Truy cập sản phẩm
- `cart`: Thêm vào giỏ hàng
- `order`: Đặt hàng
- `pay`: Thanh toán
- `collect`: Yêu thích
- `refund`: Hoàn tiền

```php
ProductLogJob::dispatch('doJob', ['visit', $productData]);
```

#### 2.2 ProductStockJob - Xử lý tồn kho sản phẩm
**Chức năng**: Tính toán và cập nhật tồn kho sản phẩm
**Thời điểm kích hoạt**: Sau khi thao tác với sản phẩm
**Chức năng xử lý**:
- Tính toán tồn kho phân tán
- Tổng hợp tồn kho theo quy cách
- Cập nhật đồng bộ tồn kho

#### 2.3 ProductCopyJob - Tác vụ sao chép sản phẩm
**Chức năng**: Sao chép thông tin sản phẩm theo cách bất đồng bộ
**Thời điểm kích hoạt**: Thao tác sao chép sản phẩm
**Nội dung xử lý**:
- Sao chép thông tin cơ bản của sản phẩm
- Sao chép quy cách và thuộc tính
- Sao chép tài nguyên hình ảnh

### 3. Tác vụ liên quan đến thông báo

#### 3.1 SmsJob - Tác vụ gửi SMS
**Chức năng**: Gửi thông báo SMS theo cách bất đồng bộ
**Thời điểm kích hoạt**: Các sự kiện nghiệp vụ
**Tình huống hỗ trợ**:
- Thông báo đơn hàng
- Nhắc nhở thanh toán
- Xác thực đăng ký
- Tiếp thị, quảng bá

```php
SmsJob::dispatch('doJob', [$phone, $data, $template]);
```

#### 3.2 TemplateJob - Tác vụ tin nhắn mẫu
**Chức năng**: Gửi tin nhắn mẫu WeChat
**Thời điểm kích hoạt**: Thay đổi trạng thái nghiệp vụ
**Loại tin nhắn**:
- Thông báo trạng thái đơn hàng
- Thông báo thanh toán thành công
- Thông báo cập nhật vận chuyển

#### 3.3 SyncMessageJob - Tác vụ đồng bộ tin nhắn
**Chức năng**: Đồng bộ tin nhắn trên nhiều nền tảng
**Thời điểm kích hoạt**: Khi gửi tin nhắn
**Phạm vi đồng bộ**:
- Hệ thống CSKH
- Trang quản trị
- Phía người dùng

### 4. Tác vụ liên quan đến marketing

#### 4.1 PinkJob - Tác vụ mua chung
**Chức năng**: Xử lý chương trình mua chung
**Thời điểm kích hoạt**: Các thao tác liên quan đến mua chung
**Nội dung xử lý**:
- Cập nhật trạng thái mua chung
- Xử lý mua chung thất bại
- Thông báo mua chung thành công

#### 4.2 LiveJob - Tác vụ livestream
**Chức năng**: Xử lý liên quan đến livestream
**Thời điểm kích hoạt**: Sự kiện livestream
**Nội dung xử lý**:
- Đồng bộ trạng thái livestream
- Đăng bán/ngừng bán sản phẩm
- Thống kê dữ liệu người xem

#### 4.3 AutoCommentJob - Tác vụ đánh giá tự động
**Chức năng**: Tự động tạo đánh giá sản phẩm
**Thời điểm kích hoạt**: Sau khi đơn hàng hoàn thành
**Logic xử lý**:
- Tạo đánh giá dựa trên thông tin sản phẩm
- Mô phỏng đánh giá của người dùng
- Tăng mức độ tương tác của sản phẩm

### 5. Tác vụ bảo trì hệ thống

#### 5.1 TaskJob - Tác vụ định kỳ
**Chức năng**: Bảo trì hệ thống định kỳ
**Tần suất thực thi**: Định kỳ hằng ngày
**Nội dung bảo trì**:
- Dọn dẹp tệp hết hạn
- Tổng hợp thống kê dữ liệu
- Kiểm tra tình trạng hệ thống

```php
// Dọn poster của ngày hôm qua
TaskJob::dispatch('emptyYesterdayAttachment');
```

#### 5.2 UpgradeJob - Tác vụ nâng cấp hệ thống
**Chức năng**: Xử lý nâng cấp hệ thống
**Thời điểm kích hoạt**: Kích hoạt thủ công hoặc theo lịch
**Nội dung xử lý**:
- Cập nhật cấu trúc cơ sở dữ liệu
- Nâng cấp tệp cấu hình
- Kiểm tra tính tương thích

#### 5.3 CheckQueueJob - Tác vụ kiểm tra hàng đợi
**Chức năng**: Giám sát tình trạng hoạt động của hàng đợi
**Tần suất thực thi**: Kiểm tra định kỳ
**Hạng mục kiểm tra**:
- Tình trạng tồn đọng hàng đợi
- Thống kê tác vụ thất bại
- Mức sử dụng tài nguyên hệ thống

### 6. Tác vụ liên quan đến người dùng

#### 6.1 UserJob - Tác vụ xử lý người dùng
**Chức năng**: Xử lý bất đồng bộ liên quan đến người dùng
**Thời điểm kích hoạt**: Thao tác của người dùng
**Nội dung xử lý**:
- Tính cấp độ người dùng
- Thống kê và phân tích điểm thưởng
- Cập nhật dữ liệu hành vi

#### 6.2 AgentJob - Tác vụ đại lý
**Chức năng**: Xử lý liên quan đến đại lý
**Thời điểm kích hoạt**: Nghiệp vụ đại lý
**Nội dung xử lý**:
- Tính hoa hồng
- Nâng cấp bậc
- Thống kê dữ liệu

### 7. Tác vụ liên quan đến vận chuyển

#### 7.1 OrderExpressJob - Tác vụ vận chuyển đơn hàng
**Chức năng**: Xử lý thông tin vận chuyển
**Thời điểm kích hoạt**: Thay đổi trạng thái vận chuyển
**Nội dung xử lý**:
- Đồng bộ thông tin vận chuyển
- Thông báo cập nhật trạng thái
- Xử lý tình huống bất thường

#### 7.2 TakeOrderJob - Tác vụ tiếp nhận đơn hàng
**Chức năng**: Xử lý tiếp nhận đơn hàng
**Thời điểm kích hoạt**: Tạo đơn hàng
**Nội dung xử lý**:
- Kiểm tra tính hợp lệ của dữ liệu đơn hàng
- Kiểm tra tồn kho
- Tự động phân bổ

### 8. Tác vụ liên quan đến tài chính

#### 8.1 OrderInvoiceJob - Tác vụ hóa đơn của đơn hàng
**Chức năng**: Xử lý hóa đơn
**Thời điểm kích hoạt**: Yêu cầu xuất hóa đơn
**Nội dung xử lý**:
- Tạo hóa đơn
- Đồng bộ trạng thái
- Gửi thông báo

#### 8.2 OutPushJob - Tác vụ đẩy dữ liệu ra bên ngoài
**Chức năng**: Đẩy dữ liệu ra hệ thống bên ngoài
**Thời điểm kích hoạt**: Thay đổi dữ liệu
**Nội dung đẩy**:
- Dữ liệu đơn hàng
- Dữ liệu người dùng
- Dữ liệu sản phẩm

## Hệ thống tác vụ định kỳ

### 1. Lệnh Timer
Bộ lập lịch tác vụ định kỳ được xây dựng dựa trên Workerman

```bash
# Khởi động tác vụ định kỳ
php think timer start

# Khởi động ở chế độ daemon (tiến trình nền)
php think timer start -d

# Dừng tác vụ định kỳ
php think timer stop

# Khởi động lại tác vụ định kỳ
php think timer reload
```

### 2. Tác vụ định kỳ của hệ thống
Bảng cơ sở dữ liệu: `system_crontab`

**Mô tả trường**:
- `name`: Tên tác vụ
- `task_type`: Loại tác vụ
- `mark`: Mã định danh tác vụ
- `content`: Nội dung tác vụ
- `max_execution_time`: Thời gian thực thi tối đa
- `execution_cycle`: Chu kỳ thực thi
- `is_open`: Có bật hay không
- `next_execution_time`: Thời gian thực thi lần tới
- `last_execution_time`: Thời gian thực thi lần trước

### 3. Tác vụ định kỳ tích hợp sẵn

#### 3.1 Liên quan đến đơn hàng
- **Hủy đơn hàng chưa thanh toán**: Kiểm tra mỗi phút một lần
- **Gỡ sản phẩm đặt trước khỏi kệ**: Kiểm tra mỗi giờ một lần
- **Xử lý mua chung thất bại**: Kiểm tra 5 phút một lần

#### 3.2 Thống kê dữ liệu
- **Tổng hợp thống kê theo ngày**: Thực thi vào rạng sáng hằng ngày
- **Phân tích hành vi người dùng**: Thực thi mỗi giờ
- **Thống kê dữ liệu bán hàng**: Thực thi hằng ngày

#### 3.3 Bảo trì hệ thống
- **Dọn dẹp nhật ký**: Thực thi hằng tuần
- **Dọn dẹp tệp tạm**: Thực thi hằng ngày
- **Làm mới bộ nhớ đệm**: Thực thi định kỳ

## Giám sát và quản lý hàng đợi

### 1. Giám sát trạng thái hàng đợi
```php
// Kiểm tra trạng thái hàng đợi
Queue::instance()->getQueueInfo();

// Lấy độ dài hàng đợi
Queue::instance()->getQueueLength();

// Lấy các tác vụ thất bại
Queue::instance()->getFailedJobs();
```

### 2. Quản lý tác vụ
```php
// Thực thi lại các tác vụ thất bại
Queue::instance()->retryFailed($jobId);

// Làm trống hàng đợi
Queue::instance()->clearQueue($queueName);

// Tạm dừng hàng đợi
Queue::instance()->pauseQueue($queueName);
```

### 3. Ghi log
Mọi tác vụ hàng đợi đều được ghi nhật ký chi tiết:
- Nhật ký thực thi thành công
- Nhật ký thử lại khi thất bại
- Thông tin stack trace của ngoại lệ
- Thống kê hiệu năng thực thi

## Thực tiễn tốt nhất khi cấu hình hàng đợi

### 1. Phân nhóm hàng đợi
Phân nhóm theo mức độ quan trọng của nghiệp vụ:
```php
// Hàng đợi ưu tiên cao
'order_pay'     => 'CRMEB_ORDER_PAY'
'sms'           => 'CRMEB_SMS'
'email'         => 'CRMEB_EMAIL'

// Hàng đợi ưu tiên thường
'statistics'    => 'CRMEB_STATISTICS'
'log'           => 'CRMEB_LOG'

// Hàng đợi ưu tiên thấp
'report'        => 'CRMEB_REPORT'
'cleanup'       => 'CRMEB_CLEANUP'
```

### 2. Chiến lược thử lại
```php
// Số lần thử lại cho từng loại tác vụ
'order_pay'     => 3      // Tác vụ thanh toán thử lại 3 lần
'sms'           => 5      // Tác vụ SMS thử lại 5 lần
'email'         => 3      // Tác vụ email thử lại 3 lần
'statistics'    => 1      // Tác vụ thống kê thử lại 1 lần
```

### 3. Cài đặt độ trễ
```php
// Các thời gian trễ thường dùng
UnpaidOrderCancelJob::dispatchSecs(1800, 'doJob', [$orderId]);  // 30 phút
OrderRemindJob::dispatchSecs(86400, 'doJob', [$orderId]);       // 24 giờ
CleanupJob::dispatchSecs(604800, 'doJob', [$data]);             // 7 ngày
```

## Khuyến nghị tối ưu hiệu năng

### 1. Tối ưu hàng đợi
- Dùng Redis cluster để nâng cao tính sẵn sàng
- Thiết lập số lượng hàng đợi hợp lý để tránh điểm nóng (hotspot)
- Định kỳ dọn dẹp dữ liệu hàng đợi hết hạn
- Giám sát độ dài hàng đợi để kịp thời mở rộng

### 2. Tối ưu tác vụ
- Tránh để một tác vụ đơn lẻ quá phức tạp
- Chia nhỏ tác vụ lớn thành các tác vụ nhỏ một cách hợp lý
- Dùng transaction để đảm bảo tính nhất quán của dữ liệu
- Thêm cơ chế kiểm soát thời gian chờ (timeout) để tránh bị chặn

### 3. Tối ưu tài nguyên
- Kiểm soát số lượng tác vụ chạy đồng thời
- Thiết lập giới hạn bộ nhớ hợp lý
- Tối ưu hiệu năng truy vấn cơ sở dữ liệu
- Dùng connection pool để giảm chi phí tài nguyên

## Xử lý sự cố

### 1. Sự cố thường gặp

#### 1.1 Tồn đọng hàng đợi
**Hiện tượng**: Độ dài hàng đợi liên tục tăng
**Nguyên nhân**: Tốc độ tiêu thụ (consume) chậm hơn tốc độ tạo tác vụ (produce)
**Giải pháp**: 
- Tăng số tiến trình consumer
- Tối ưu hiệu suất thực thi tác vụ
- Kiểm tra mức sử dụng tài nguyên hệ thống

#### 1.2 Tác vụ bị thực thi lặp lại
**Hiện tượng**: Cùng một tác vụ bị thực thi nhiều lần
**Nguyên nhân**: Tác vụ bị đưa vào hàng đợi nhiều lần hoặc lỗi về tính lũy đẳng (idempotency)
**Giải pháp**:
- Thêm mã định danh duy nhất cho tác vụ
- Triển khai kiểm tra tính lũy đẳng
- Tối ưu logic thử lại tác vụ

#### 1.3 Rò rỉ bộ nhớ
**Hiện tượng**: Bộ nhớ của tiến trình liên tục tăng
**Nguyên nhân**: Trong tác vụ có tài nguyên chưa được giải phóng
**Giải pháp**:
- Kiểm tra tham chiếu đối tượng
- Giải phóng kịp thời các đối tượng lớn
- Định kỳ khởi động lại tiến trình consumer

### 2. Xử lý khẩn cấp

#### 2.1 Tạm dừng hàng đợi
```bash
# Tạm dừng tất cả hàng đợi
php think queue:pause

# Tạm dừng hàng đợi chỉ định
php think queue:pause --queue=order_pay
```

#### 2.2 Xóa sạch cưỡng bức
```bash
# Làm trống hàng đợi chỉ định
php think queue:clear --queue=statistics

# Làm trống tất cả hàng đợi
php think queue:clear --all
```

#### 2.3 Tạo lại hàng đợi
```bash
# Tạo lại bảng hàng đợi
php think queue:table

# Đẩy lại các tác vụ thất bại
php think queue:retry all
```

## Hướng dẫn phát triển

### 1. Tạo tác vụ mới

#### 1.1 Tạo lớp tác vụ
```php
<?php
namespace app\jobs;

use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;

class CustomJob extends BaseJobs
{
    use QueueTrait;
    
    /**
     * Thực thi tác vụ
     * @param array $data
     * @return bool
     */
    public function doJob(array $data): bool
    {
        try {
            // Xử lý logic nghiệp vụ
            $this->processData($data);
            return true;
        } catch (\Exception $e) {
            // Ghi log lỗi
            Log::error('Thực thi tác vụ thất bại: ' . $e->getMessage());
            return false;
        }
    }
    
    private function processData(array $data): void
    {
        // Logic nghiệp vụ cụ thể
    }
}
```

#### 1.2 Điều phối tác vụ
```php
// Thực thi ngay
CustomJob::dispatch('doJob', [$data]);

// Thực thi có độ trễ
CustomJob::dispatchSecs(300, 'doJob', [$data]);

// Chỉ định hàng đợi
CustomJob::dispatch('doJob', [$data], 'custom_queue');
```

### 2. Kiểm thử tác vụ hàng đợi

#### 2.1 Kiểm thử đơn vị (unit test)
```php
public function testCustomJob()
{
    $job = new CustomJob();
    $result = $job->doJob($testData);
    $this->assertTrue($result);
}
```

#### 2.2 Kiểm thử tích hợp
```php
public function testQueueDispatch()
{
    // Giả lập điều phối hàng đợi
    Queue::fake();
    
    CustomJob::dispatch('doJob', [$testData]);
    
    Queue::assertPushed(CustomJob::class, function ($job) use ($testData) {
        return $job->data[0] === $testData;
    });
}
```

### 3. Giám sát và gỡ lỗi

#### 3.1 Thêm nhật ký
```php
public function doJob(array $data): bool
{
    Log::info('Tác vụ bắt đầu thực thi', ['data' => $data]);
    
    try {
        // Logic nghiệp vụ
        Log::info('Thực thi tác vụ thành công');
        return true;
    } catch (\Exception $e) {
        Log::error('Thực thi tác vụ thất bại', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}
```

#### 3.2 Giám sát hiệu năng
```php
public function doJob(array $data): bool
{
    $startTime = microtime(true);
    
    try {
        // Logic nghiệp vụ
        $endTime = microtime(true);
        $executeTime = ($endTime - $startTime) * 1000; // Mili giây
        
        Log::info('Thực thi tác vụ hoàn tất', [
            'execute_time' => $executeTime . 'ms',
            'memory_usage' => memory_get_usage(true)
        ]);
        
        return true;
    } catch (\Exception $e) {
        Log::error('Thực thi tác vụ thất bại', ['error' => $e->getMessage()]);
        return false;
    }
}
```

## Tổng kết

Hệ thống tác vụ hàng đợi của CRMEB là thành phần then chốt giúp hệ thống vận hành với hiệu năng cao. Nhờ phân loại tác vụ hợp lý, quản lý mức ưu tiên, xử lý lỗi và cơ chế giám sát, hệ thống đảm bảo được tính ổn định và khả năng mở rộng. Khi sử dụng thực tế, cần thiết kế độ chi tiết của tác vụ cho hợp lý theo đặc thù nghiệp vụ, tối ưu hiệu suất thực thi và xây dựng cơ chế giám sát, cảnh báo hoàn chỉnh.

Sử dụng đúng hệ thống hàng đợi có thể cải thiện đáng kể trải nghiệm người dùng, giảm tải cho hệ thống và nâng cao tính nhất quán trong xử lý dữ liệu. Đội ngũ phát triển nên nắm vững cơ chế hàng đợi, áp dụng hợp lý trong các tình huống phù hợp, đồng thời liên tục tối ưu và cải thiện hiệu năng hàng đợi.

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
