# Tài liệu phát triển tác vụ hàng đợi của hệ thống

## 1. Tổng quan cơ chế hàng đợi

Hệ thống CRMEB sử dụng cơ chế tác vụ hàng đợi dựa trên ThinkPHP Queue để xử lý các thao tác tốn thời gian, nâng cao tốc độ phản hồi và khả năng xử lý đồng thời của hệ thống. Tác vụ hàng đợi hỗ trợ các chức năng như thực thi bất đồng bộ, thực thi trì hoãn, thử lại khi thất bại, phù hợp với các tình huống như xử lý đơn hàng, đẩy tin nhắn, phân tích dữ liệu, v.v.

### 1.1 Thành phần cốt lõi

- **Interface tác vụ**: `JobInterface` - Định nghĩa interface chuẩn cho việc thực thi tác vụ
- **Lớp cơ sở tác vụ**: `BaseJobs` - Cung cấp phần triển khai cơ bản cho việc thực thi tác vụ
- **Công cụ hàng đợi**: `Queue` - Cung cấp chức năng tạo và quản lý tác vụ hàng đợi
- **Trait hàng đợi**: `QueueTrait` - Cung cấp các phương thức điều phối hàng đợi nhanh gọn
- **Tệp cấu hình**: `queue.php` - Cấu hình driver và tham số của hàng đợi

## 2. Cấu hình hàng đợi

### 2.1 Tệp cấu hình

Tệp cấu hình hàng đợi nằm tại `/crmeb/config/queue.php`, các mục cấu hình chính gồm:

```php
return [
    // Driver hàng đợi mặc định
    'default'     => 'redis',
    
    // Cấu hình phương thức kết nối hàng đợi
    'connections' => [
        'sync'     => [
            'type' => 'sync',
        ],
        'database' => [
            'type'       => 'database',
            'queue'      => 'default',
            'table'      => 'jobs',
            'connection' => null,
        ],
        'redis'    => [
            'type'       => 'redis',
            'queue'      => 'default',
            'host'       => env('redis.host', '127.0.0.1'),
            'port'       => env('redis.port', 6379),
            'password'   => env('redis.password', ''),
            'select'     => env('redis.select', 0),
            'timeout'    => 0,
            'persistent' => false,
        ],
    ],
    
    // Cấu hình hàng đợi tác vụ thất bại
    'failed'      => [
        'type'  => 'none',
        'table' => 'failed_jobs',
    ],
];
```

### 2.2 Giải thích cấu hình

- **default**: Driver hàng đợi mặc định, hỗ trợ sync (đồng bộ), database (cơ sở dữ liệu), redis (Redis)
- **connections**: Cấu hình kết nối hàng đợi, có thể cấu hình nhiều driver hàng đợi
- **failed**: Cấu hình hàng đợi thất bại, dùng để lưu các tác vụ thực thi thất bại

## 3. Định nghĩa interface tác vụ

### 3.1 JobInterface

Interface tác vụ định nghĩa phương thức thực thi chuẩn của tác vụ hàng đợi:

```php
namespace crmeb\interfaces;

use think\queue\Job;

interface JobInterface
{
    public function fire(Job $job, $data): void;
}
```

- **fire**: Phương thức đầu vào để thực thi tác vụ, nhận đối tượng Job và dữ liệu tác vụ

## 4. Lớp cơ sở của tác vụ

### 4.1 BaseJobs

Lớp cơ sở tác vụ triển khai interface `JobInterface`, cung cấp logic cơ bản cho việc thực thi tác vụ:

```php
namespace crmeb\basic;

use crmeb\interfaces\JobInterface;
use think\queue\Job;
use think\facade\Log;

abstract class BaseJobs implements JobInterface
{
    /**
     * @param Job $job
     * @param $data
     */
    public function fire(Job $job, $data): void
    {
        try {
            $method = $data['method'] ?? 'doJob';
            if (method_exists($this, $method)) {
                if (!isset($data['data'])) {
                    $res = $this->{$method}();
                } else {
                    $res = $this->{$method}(...$data['data']);
                }
                if ($res) {
                    $job->delete();
                } else {
                    if ($job->attempts() > 3) {
                        $job->delete();
                    } else {
                        $job->release();
                    }
                }
            } else {
                $job->delete();
            }
        } catch (\Throwable $e) {
            Log::error('Thực thi tác vụ hàng đợi thất bại:' . $e->getMessage());
            if ($job->attempts() > 3) {
                $job->delete();
            } else {
                $job->release();
            }
        }
    }
}
```

- **fire**: Phương thức triển khai interface JobInterface, xử lý việc thực thi tác vụ, bắt lỗi và thử lại khi thất bại
- **doJob**: Phương thức thực thi tác vụ mặc định, lớp con có thể ghi đè

## 5. Lớp công cụ hàng đợi

### 5.1 Queue

Lớp công cụ hàng đợi cung cấp cách gọi nối chuỗi (method chaining) để tạo và quản lý tác vụ hàng đợi:

```php
namespace crmeb\utils;

use think\facade\Config;
use think\facade\Queue as QueueThink;
use think\facade\Log;

class Queue
{
    // Phương thức thực thi tác vụ
    protected $do = 'doJob';
    
    // Tên class task
    protected $job;
    
    // Số lần lỗi
    protected $errorCount = 3;
    
    // Dữ liệu
    protected $data;
    
    // Tên hàng đợi
    protected $queueName = null;
    
    // Số giây trì hoãn thực thi
    protected $secs = 0;
    
    // ... Các phương thức khác
}
```

### 5.2 QueueTrait

Trait hàng đợi cung cấp các phương thức điều phối hàng đợi nhanh gọn:

```php
namespace crmeb\traits;

trait QueueTrait
{
    /**
     * Thêm vào hàng đợi
     * @param string $queueName
     * @param array $data
     * @return bool
     */
    public static function dispatch(string $queueName = null, ...$data)
    {
        $class = get_called_class();
        return Queue::instance()->job($class)->data($data)->queue($queueName)->push();
    }
    
    /**
     * Thêm vào hàng đợi có độ trễ
     * @param int $secs
     * @param string|null $queueName
     * @param array $data
     * @return bool
     */
    public static function dispatchSecs(int $secs, string $queueName = null, ...$data)
    {
        $class = get_called_class();
        return Queue::instance()->job($class)->data($data)->secs($secs)->queue($queueName)->push();
    }
}
```

## 6. Tạo tác vụ hàng đợi

### 6.1 Các bước tạo tác vụ

1. Tạo lớp tác vụ, kế thừa `BaseJobs`
2. Triển khai phương thức `doJob` hoặc phương thức tùy chỉnh
3. Dùng các phương thức nhanh do `QueueTrait` cung cấp để điều phối tác vụ

### 6.2 Ví dụ tác vụ

```php
namespace app\jobs;

use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

class OrderCreateAfterJob extends BaseJobs
{
    use QueueTrait;
    
    /**
     * Xử lý sau đơn hàng
     * @param $orderInfo
     * @param $data
     * @param $activity
     * @return bool
     */
    public function doJob($orderInfo, $data, $activity)
    {
        try {
            // Xử lý logic đơn hàng
            // ...
            
            return true;
        } catch (\Throwable $e) {
            Log::error('Hậu xử lý đơn hàng thất bại:' . $e->getMessage());
            return false;
        }
    }
}
```

## 7. Điều phối tác vụ hàng đợi

### 7.1 Điều phối cơ bản

Dùng phương thức `dispatch` do `QueueTrait` cung cấp để điều phối tác vụ:

```php
// Điều phối tác vụ hậu xử lý đơn hàng
OrderCreateAfterJob::dispatch(null, $orderInfo, $data, $activity);
```

### 7.2 Điều phối có trì hoãn

Dùng phương thức `dispatchSecs` do `QueueTrait` cung cấp để điều phối tác vụ có trì hoãn:

```php
// Thực thi tác vụ hậu xử lý đơn hàng sau 10 giây
OrderCreateAfterJob::dispatchSecs(10, null, $orderInfo, $data, $activity);
```

### 7.3 Điều phối bằng công cụ Queue

Dùng lớp công cụ `Queue` để điều phối linh hoạt hơn:

```php
use crmeb\utils\Queue;

// Tạo và điều phối tác vụ
Queue::instance()
    ->job(OrderCreateAfterJob::class)
    ->data($orderInfo, $data, $activity)
    ->secs(10)
    ->queue('order')
    ->push();
```

## 8. Quy trình thực thi tác vụ

1. **Điều phối tác vụ**: Đưa tác vụ vào hàng đợi thông qua `dispatch` hoặc công cụ `Queue`
2. **Tiêu thụ tác vụ**: Tiến trình consumer của hàng đợi lấy tác vụ ra từ hàng đợi
3. **Thực thi tác vụ**: Gọi phương thức `fire` của lớp tác vụ để thực thi tác vụ
4. **Xử lý kết quả**:
   - Thực thi thành công: Xóa tác vụ
   - Thực thi thất bại: Dựa vào số lần thử lại để quyết định có thực thi lại hay không
   - Vượt quá số lần thử lại: Chuyển tác vụ sang hàng đợi thất bại (nếu đã cấu hình)

## 9. Quản lý hàng đợi

### 9.1 Khởi động consumer của hàng đợi

Dùng công cụ dòng lệnh của ThinkPHP để khởi động consumer của hàng đợi:

```bash
# Khởi động consumer của hàng đợi mặc định
php think queue:listen

# Khởi động consumer của hàng đợi chỉ định
php think queue:listen --queue order

# Khởi động consumer ở chế độ daemon
php think queue:work --daemon
```

### 9.2 Xem trạng thái hàng đợi

```bash
# Xem số tác vụ trong hàng đợi
php think queue:status

# Xem tác vụ thất bại
php think queue:failed

# Thử lại tác vụ thất bại
php think queue:retry all

# Xóa các tác vụ thất bại
php think queue:flush
```

## 10. Thực tiễn tốt nhất

### 10.1 Nguyên tắc thiết kế tác vụ

- **Đơn trách nhiệm**: Mỗi tác vụ chỉ đảm nhận một chức năng
- **Tính lũy đẳng (idempotent)**: Thực thi lặp lại tác vụ không gây ra tác dụng phụ
- **Tính toàn vẹn dữ liệu**: Đảm bảo dữ liệu cần thiết để thực thi tác vụ là đầy đủ
- **Xử lý lỗi**: Xử lý thỏa đáng các ngoại lệ trong quá trình thực thi tác vụ

### 10.2 Tối ưu hiệu năng

- Thiết lập số lần thử lại khi thất bại một cách hợp lý, tránh thử lại vô hạn
- Chia nhỏ các tác vụ tốn thời gian để nâng cao khả năng xử lý đồng thời
- Dùng driver hàng đợi phù hợp, Redis thích hợp cho tình huống tải đồng thời cao
- Định kỳ dọn dẹp hàng đợi thất bại, tránh dồn ứ dữ liệu

### 10.3 Giám sát và log

- Ghi log thực thi tác vụ để tiện khắc phục sự cố
- Giám sát độ dài hàng đợi để kịp thời phát hiện điểm nghẽn xử lý
- Thiết lập cơ chế cảnh báo, thông báo kịp thời khi tác vụ hàng đợi bị dồn ứ

## 11. Sự cố thường gặp

### 11.1 Thực thi tác vụ thất bại

- Kiểm tra dữ liệu tác vụ có đầy đủ không
- Kiểm tra các dịch vụ mà tác vụ phụ thuộc có khả dụng không
- Xem tệp log để xác định nguyên nhân lỗi
- Kiểm tra driver hàng đợi có hoạt động bình thường không

### 11.2 Tác vụ hàng đợi bị dồn ứ

- Tăng số lượng tiến trình consumer của hàng đợi
- Tối ưu logic thực thi tác vụ, giảm thời gian thực thi
- Kiểm tra điểm nghẽn hiệu năng của driver hàng đợi
- Cân nhắc dùng driver hàng đợi hiệu quả hơn

### 11.3 Tác vụ trì hoãn không được thực thi

- Kiểm tra consumer của hàng đợi có đang chạy bình thường không
- Kiểm tra thời gian trì hoãn có được thiết lập đúng không
- Kiểm tra driver hàng đợi có hỗ trợ tác vụ trì hoãn không
- Xem log hàng đợi để xác định nguyên nhân sự cố

## 12. Danh sách tác vụ tích hợp sẵn

Hệ thống CRMEB tích hợp sẵn nhiều tác vụ hàng đợi để xử lý các tình huống nghiệp vụ khác nhau:

| Tên class task | Mô tả chức năng |
|---------|---------|
| OrderCreateAfterJob | Xử lý sau khi tạo đơn hàng |
| PinkJob | Xử lý tác vụ mua chung |
| MiniOrderJob | Xử lý đơn hàng Mini Program |
| StoreOrderStatusJob | Xử lý trạng thái đơn hàng |
| UserBrokerageJob | Xử lý hoa hồng của người dùng |
| StoreProductStockJob | Xử lý tồn kho sản phẩm |
| StoreOrderRefundJob | Xử lý hoàn tiền đơn hàng |
| StoreOrderSendJob | Xử lý giao hàng cho đơn hàng |
| StoreOrderTakeJob | Xử lý nhận hàng của đơn hàng |
| StoreOrderPayJob | Xử lý thanh toán đơn hàng |

## 13. Tổng kết

Cơ chế tác vụ hàng đợi của hệ thống CRMEB mang lại khả năng xử lý bất đồng bộ mạnh mẽ. Sử dụng tác vụ hàng đợi một cách hợp lý có thể nâng cao tốc độ phản hồi và khả năng xử lý đồng thời của hệ thống, cải thiện trải nghiệm người dùng. Lập trình viên có thể tạo tác vụ hàng đợi tùy chỉnh theo nhu cầu nghiệp vụ để hiện thực các logic xử lý bất đồng bộ khác nhau.
---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
