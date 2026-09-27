# Tài liệu cấu hình hệ thống

## 1. Tổng quan

Tài liệu này mô tả cấu hình hệ thống của dự án CRMEB, bao gồm tệp cấu hình, biến môi trường, giải thích các mục cấu hình, v.v., nhằm giúp lập trình viên hiểu và cấu hình dự án, đảm bảo dự án vận hành bình thường.

## 2. Cấu trúc tệp cấu hình

### 2.1 Cấu trúc thư mục cấu hình

```
config/
├── app.php               # Cấu hình ứng dụng
├── cache.php             # Cấu hình bộ nhớ đệm
├── captcha.php           # Cấu hình captcha
├── console.php           # Cấu hình console
├── cookie.php            # Cookie Cấu hình
├── database.php          # Cấu hình cơ sở dữ liệu
├── filesystem.php        # Cấu hình hệ thống file
├── lang.php              # Cấu hình ngôn ngữ
├── log.php               # Cấu hình log
├── queue.php             # Cấu hình hàng đợi
├── route.php             # Cấu hình route
├── session.php           # Session Cấu hình
├── template.php          # Cấu hình template
└── trace.php             # Cấu hình debug
```

### 2.2 Thứ tự nạp cấu hình

1. **Cấu hình mặc định của framework**: Cấu hình mặc định đi kèm framework ThinkPHP
2. **Cấu hình ứng dụng**: Các tệp cấu hình trong thư mục `config/` ở thư mục gốc của dự án
3. **Cấu hình môi trường**: Nạp tệp cấu hình môi trường tương ứng với môi trường hiện tại
4. **Cấu hình động**: Cấu hình được thiết lập động trong lúc chạy
5. **Biến môi trường**: Cấu hình được thiết lập qua tệp `.env` hoặc biến môi trường hệ thống

## 3. Cấu hình biến môi trường

### 3.1 Tệp biến môi trường (.env)

Tệp biến môi trường `.env` dùng để lưu thông tin cấu hình nhạy cảm như mật khẩu cơ sở dữ liệu, khóa API, v.v. Không nên commit tệp này lên hệ thống quản lý phiên bản.

```ini
# Cấu hình ứng dụng
APP_NAME=CRMEB
APP_ENV=local
APP_KEY=base64:your_app_key
APP_DEBUG=true
APP_URL=http://localhost

# Cấu hình cơ sở dữ liệu
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crmeb
DB_USERNAME=root
DB_PASSWORD=your_password

# Redis Cấu hình
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0

# Cấu hình bộ nhớ đệm
CACHE_DRIVER=file

# Cấu hình hàng đợi
QUEUE_CONNECTION=sync

# Cấu hình email
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@example.com
MAIL_FROM_NAME="CRMEB"

# Cấu hình Alibaba Cloud
ALIYUN_ACCESS_KEY_ID=your_access_key_id
ALIYUN_ACCESS_KEY_SECRET=your_access_key_secret

# Cấu hình Tencent Cloud
TENCENTCLOUD_SECRET_ID=your_secret_id
TENCENTCLOUD_SECRET_KEY=your_secret_key
```

### 3.2 Nạp biến môi trường

Biến môi trường có thể được nạp theo các cách sau:

1. **Tệp .env**: Tạo tệp `.env` trong thư mục gốc của dự án, cấu hình các biến liên quan theo nhu cầu của môi trường
2. **Biến môi trường hệ thống**: Thiết lập biến môi trường hệ thống trên máy chủ
3. **Tham số dòng lệnh**: Thiết lập biến môi trường qua tham số khi chạy lệnh

### 3.3 Sử dụng biến môi trường

Trong tệp cấu hình có thể dùng hàm `env()` để lấy biến môi trường:

```php
// config/database.php
return [
    'default' => env('database.driver', 'mysql'),
    'connections' => [
        'mysql' => [
            'type' => 'mysql',
            'hostname' => env('database.hostname', '127.0.0.1'),
            'database' => env('database.database', ''),
            'username' => env('database.username', ''),
            'password' => env('database.password', ''),
            'hostport' => env('database.hostport', '3306'),
            'charset' => 'utf8mb4',
            'prefix' => env('database.prefix', ''),
            'debug' => env('app_debug', true),
        ],
    ],
];
```

## 4. Các mục cấu hình cốt lõi

### 4.1 Cấu hình ứng dụng (app.php)

| Mục cấu hình | Loại | Giá trị mặc định | Mô tả |
|-------|------|--------|------|
| app_debug | bool | true | Chế độ debug của ứng dụng |
| app_trace | bool | false | Chế độ trace của ứng dụng |
| app_status | string | 'dev' | Trạng thái ứng dụng |
| app_namespace | string | 'app' | Namespace của ứng dụng |
| default_return_type | string | 'json' | Kiểu trả về mặc định |
| default_timezone | string | 'Asia/Shanghai' | Múi giờ mặc định |
| lang_switch_on | bool | false | Bật/tắt chuyển đổi ngôn ngữ |
| default_lang | string | 'zh-cn' | Ngôn ngữ mặc định |
| auto_bind_module | bool | true | Tự động gắn (bind) module |
| controller_suffix | bool | false | Hậu tố controller |
| url_route_on | bool | true | Bật/tắt route URL |
| url_route_must | bool | false | Bắt buộc dùng route cho URL |
| var_pathinfo | string | 's' | Tên biến PATH_INFO |
| pathinfo_depr | string | '/' | Ký tự phân tách PATH_INFO |
| url_html_suffix | string | '' | Hậu tố HTML của URL |
| url_common_param | bool | false | Chế độ tham số URL thông thường |
| url_param_type | int | 1 | Kiểu tham số URL |
| request_cache_on | bool | false | Bật/tắt bộ nhớ đệm yêu cầu (request cache) |
| request_cache_expire | int | null | Thời hạn cache request |

### 4.2 Cấu hình cơ sở dữ liệu (database.php)

| Mục cấu hình | Loại | Giá trị mặc định | Mô tả |
|-------|------|--------|------|
| default | string | 'mysql' | Kết nối cơ sở dữ liệu mặc định |
| connections | array | [] | Cấu hình kết nối cơ sở dữ liệu |
| connections.mysql.type | string | 'mysql' | Loại cơ sở dữ liệu |
| connections.mysql.hostname | string | '127.0.0.1' | Tên máy chủ (hostname) cơ sở dữ liệu |
| connections.mysql.database | string | '' | Tên cơ sở dữ liệu |
| connections.mysql.username | string | '' | Tên đăng nhập cơ sở dữ liệu |
| connections.mysql.password | string | '' | Mật khẩu cơ sở dữ liệu |
| connections.mysql.hostport | string | '3306' | Cổng cơ sở dữ liệu |
| connections.mysql.charset | string | 'utf8mb4' | Bộ ký tự (charset) của cơ sở dữ liệu |
| connections.mysql.prefix | string | '' | Tiền tố bảng cơ sở dữ liệu |
| connections.mysql.debug | bool | true | Chế độ debug cơ sở dữ liệu |
| connections.mysql.deploy | array | [] | Cách triển khai cơ sở dữ liệu |
| connections.mysql.rw_separate | bool | false | Tách đọc/ghi cơ sở dữ liệu |
| connections.mysql.master_num | int | 1 | Số lượng cơ sở dữ liệu chính (master) |
| connections.mysql.slave_no | int | '' | Số thứ tự cơ sở dữ liệu phụ (slave) |
| connections.mysql.read_master | bool | false | Có đọc từ máy chủ chính (master) không |
| connections.mysql.deploy_type | int | 0 | Kiểu triển khai cơ sở dữ liệu |
| connections.mysql.failover | array | [] | Chuyển đổi dự phòng (failover) cơ sở dữ liệu |
| connections.mysql.break_reconnect | bool | false | Tự kết nối lại khi mất kết nối |
| connections.mysql.pdo_type | string | '' | Kiểu PDO |
| connections.mysql.max_conn | int | 0 | Số kết nối tối đa |
| connections.mysql.strict_type | bool | false | Chế độ nghiêm ngặt |
| connections.mysql.auto_timestamp | bool | false | Tự động ghi timestamp |
| connections.mysql.datetime_format | string | 'Y-m-d H:i:s' | Định dạng ngày giờ |
| connections.mysql.date_format | string | 'Y-m-d' | Định dạng ngày |
| connections.mysql.time_format | string | 'H:i:s' | Định dạng giờ |
| connections.mysql.sql_build_cache | bool | false | Bộ nhớ đệm tạo câu lệnh SQL |
| connections.mysql.builder | string | '' | Trình tạo truy vấn (query builder) |
| connections.mysql.query | string | '' | Lớp truy vấn |
| connections.mysql.break_match_str | string | '' | Chuỗi so khớp khi mất kết nối |
| connections.mysql.params | array | [] | Tham số kết nối |
| connections.mysql.pk_convert | bool | false | Chuyển đổi khóa chính |
| connections.mysql.resultset_type | string | 'array' | Kiểu tập kết quả (resultset) |
| connections.mysql.return_collection | bool | false | Trả về collection |
| connections.mysql.identifier_quote | string | '' | Dấu bao định danh (identifier quote) |
| connections.mysql.cache | array | [] | Cấu hình bộ nhớ đệm |
| connections.mysql.trace_sql | bool | false | Theo dõi (trace) SQL |

### 4.3 Cấu hình bộ nhớ đệm (cache.php)

| Mục cấu hình | Loại | Giá trị mặc định | Mô tả |
|-------|------|--------|------|
| default | string | 'file' | Driver cache mặc định |
| stores | array | [] | Cấu hình các driver cache |
| stores.file.type | string | 'file' | Driver cache dạng file |
| stores.file.path | string | '' | Đường dẫn cache dạng file |
| stores.file.prefix | string | '' | Tiền tố cache dạng file |
| stores.file.expire | int | 0 | Thời hạn hiệu lực của cache dạng file |
| stores.redis.type | string | 'redis' | Driver cache Redis |
| stores.redis.host | string | '127.0.0.1' | Tên máy chủ Redis |
| stores.redis.port | int | 6379 | Cổng Redis |
| stores.redis.password | string | '' | Mật khẩu Redis |
| stores.redis.select | int | 0 | Cơ sở dữ liệu Redis |
| stores.redis.timeout | int | 0 | Thời gian chờ (timeout) của Redis |
| stores.redis.persistent | bool | false | Kết nối liên tục (persistent) của Redis |
| stores.redis.prefix | string | '' | Tiền tố cache Redis |
| stores.redis.serializer | int | 0 | Phương thức tuần tự hóa (serialize) của Redis |
| stores.memcache.type | string | 'memcache' | Driver cache Memcache |
| stores.memcache.host | string | '127.0.0.1' | Tên máy chủ Memcache |
| stores.memcache.port | int | 11211 | Cổng Memcache |
| stores.memcache.persistent | bool | false | Kết nối liên tục (persistent) của Memcache |
| stores.memcache.timeout | int | 0 | Thời gian chờ (timeout) của Memcache |
| stores.memcache.prefix | string | '' | Tiền tố cache Memcache |
| stores.wincache.type | string | 'wincache' | Driver cache WinCache |
| stores.wincache.prefix | string | '' | Tiền tố cache WinCache |
| stores.xcache.type | string | 'xcache' | Driver cache XCache |
| stores.xcache.prefix | string | '' | Tiền tố cache XCache |
| stores.apc.type | string | 'apc' | Driver cache APC |
| stores.apc.prefix | string | '' | Tiền tố cache APC |
| prefix | string | '' | Tiền tố cache |

### 4.4 Cấu hình hàng đợi (queue.php)

| Mục cấu hình | Loại | Giá trị mặc định | Mô tả |
|-------|------|--------|------|
| default | string | 'sync' | Driver hàng đợi mặc định |
| connections | array | [] | Cấu hình kết nối hàng đợi |
| connections.sync.driver | string | 'sync' | Driver hàng đợi đồng bộ (sync) |
| connections.database.driver | string | 'database' | Driver hàng đợi dùng cơ sở dữ liệu |
| connections.database.table | string | 'jobs' | Tên bảng hàng đợi |
| connections.database.queue | string | 'default' | Tên hàng đợi mặc định |
| connections.database.expire | int | 60 | Thời gian hết hạn của tác vụ |
| connections.redis.driver | string | 'redis' | Driver hàng đợi Redis |
| connections.redis.connection | string | 'default' | Tên kết nối Redis |
| connections.redis.queue | string | 'default' | Tên hàng đợi mặc định |
| connections.redis.expire | int | 60 | Thời gian hết hạn của tác vụ |
| failed | array | [] | Cấu hình hàng đợi tác vụ thất bại |
| failed.driver | string | 'database' | Driver hàng đợi tác vụ thất bại |
| failed.table | string | 'failed_jobs' | Tên bảng hàng đợi tác vụ thất bại |

### 4.5 Cấu hình log (log.php)

| Mục cấu hình | Loại | Giá trị mặc định | Mô tả |
|-------|------|--------|------|
| default | array | ['file'] | Kênh log mặc định |
| channels | array | [] | Cấu hình kênh log |
| channels.file.type | string | 'file' | Driver log dạng file |
| channels.file.path | string | '' | Đường dẫn log dạng file |
| channels.file.level | string | 'debug' | Cấp độ log |
| channels.file.days | int | 15 | Số ngày lưu giữ log |
| channels.file.json | bool | false | Có dùng định dạng JSON hay không |
| channels.syslog.type | string | 'syslog' | Driver log Syslog |
| channels.syslog.ident | string | 'think' | Định danh Syslog |
| channels.syslog.facility | int | 8 | Thiết bị (facility) của Syslog |
| channels.syslog.level | string | 'debug' | Cấp độ log |
| channels.mail.type | string | 'mail' | Driver log qua email |
| channels.mail.to | string | '' | Địa chỉ email người nhận |
| channels.mail.subject | string | 'Log message' | Tiêu đề email |
| channels.mail.level | string | 'error' | Cấp độ log |

## 4. Giải thích các cấu hình cốt lõi

### 4.1 Cấu hình ứng dụng (app.php)

- **app_debug**: Chế độ debug của ứng dụng, môi trường phát triển đặt là `true`, môi trường production đặt là `false`
- **app_trace**: Chế độ theo vết (trace) của ứng dụng, dùng để gỡ lỗi, môi trường phát triển đặt là `true`, môi trường production đặt là `false`
- **app_status**: Trạng thái ứng dụng, dùng để tải các file cấu hình khác nhau
- **default_return_type**: Kiểu trả về mặc định, ứng dụng API thường đặt là `json`
- **default_timezone**: Múi giờ mặc định, khu vực Trung Quốc đặt là `Asia/Shanghai`

### 4.2 Cấu hình cơ sở dữ liệu (database.php)

- **default**: Kết nối cơ sở dữ liệu mặc định, thường dùng `mysql`
- **connections.mysql.hostname**: Tên máy chủ cơ sở dữ liệu, thường là `127.0.0.1` hoặc địa chỉ IP của máy chủ cơ sở dữ liệu
- **connections.mysql.database**: Tên cơ sở dữ liệu, thiết lập theo thực tế
- **connections.mysql.username**: Tên người dùng cơ sở dữ liệu, thiết lập theo thực tế
- **connections.mysql.password**: Mật khẩu cơ sở dữ liệu, thiết lập theo thực tế
- **connections.mysql.charset**: Bộ ký tự (charset) của cơ sở dữ liệu, thường dùng `utf8mb4`
- **connections.mysql.prefix**: Tiền tố bảng cơ sở dữ liệu, thiết lập theo thực tế

### 4.3 Cấu hình bộ nhớ đệm (cache.php)

- **default**: Driver cache mặc định, môi trường phát triển thường dùng `file`, môi trường production thường dùng `redis`
- **stores.redis.host**: Tên máy chủ Redis, thường là `127.0.0.1` hoặc địa chỉ IP của máy chủ Redis
- **stores.redis.password**: Mật khẩu Redis, thiết lập theo thực tế
- **stores.redis.port**: Cổng Redis, mặc định là `6379`
- **stores.redis.select**: Số hiệu cơ sở dữ liệu Redis, mặc định dùng `0`

### 4.4 Cấu hình hàng đợi (queue.php)

- **default**: Driver hàng đợi mặc định, môi trường phát triển thường dùng `sync`, môi trường production thường dùng `redis` hoặc `database`
- **connections.redis.queue**: Tên hàng đợi mặc định, thiết lập theo thực tế
- **failed.table**: Tên bảng hàng đợi tác vụ thất bại, mặc định là `failed_jobs`

### 4.5 Cấu hình log (log.php)

- **default**: Kênh log mặc định, thường dùng `file`
- **channels.file.path**: Đường dẫn log dạng file, mặc định dùng `runtime/log`
- **channels.file.level**: Cấp độ log, môi trường phát triển thường dùng `debug`, môi trường production thường dùng `info` hoặc `error`
- **channels.file.days**: Số ngày lưu giữ log, thiết lập theo thực tế

## 5. Thực tiễn tốt nhất khi cấu hình

### 5.1 Cấu hình môi trường phát triển

- **app_debug**: `true`
- **app_trace**: `true`
- **default_return_type**: `json`
- **database.connections.mysql.hostname**: `127.0.0.1`
- **database.connections.mysql.database**: `crmeb_dev`
- **cache.default**: `file`
- **queue.default**: `sync`
- **log.channels.file.level**: `debug`

### 5.2 Cấu hình môi trường kiểm thử

- **app_debug**: `false`
- **app_trace**: `false`
- **default_return_type**: `json`
- **database.connections.mysql.hostname**: `127.0.0.1`
- **database.connections.mysql.database**: `crmeb_test`
- **cache.default**: `redis`
- **queue.default**: `redis`
- **log.channels.file.level**: `info`

### 5.3 Cấu hình môi trường production

- **app_debug**: `false`
- **app_trace**: `false`
- **default_return_type**: `json`
- **database.connections.mysql.hostname**: `your_db_host`
- **database.connections.mysql.database**: `crmeb_prod`
- **database.connections.mysql.username**: `your_db_user`
- **database.connections.mysql.password**: `your_db_password`
- **cache.default**: `redis`
- **queue.default**: `redis`
- **log.channels.file.level**: `error`
- **log.channels.file.days**: `30`

## 6. Các vấn đề cấu hình thường gặp

### 6.1 Kết nối cơ sở dữ liệu thất bại

- **Vấn đề**: Không thể kết nối tới cơ sở dữ liệu
- **Nguyên nhân**: Cấu hình cơ sở dữ liệu sai, ví dụ sai tên máy chủ, cổng, tên người dùng hoặc mật khẩu
- **Giải pháp**: Kiểm tra cấu hình cơ sở dữ liệu, đảm bảo cấu hình chính xác

### 6.2 Không sử dụng được cache

- **Vấn đề**: Cache không hoạt động bình thường
- **Nguyên nhân**: Cấu hình cache sai, ví dụ sai tên máy chủ, cổng hoặc mật khẩu Redis
- **Giải pháp**: Kiểm tra cấu hình cache, đảm bảo cấu hình chính xác

### 6.3 Hàng đợi không hoạt động bình thường

- **Vấn đề**: Tác vụ trong hàng đợi không thể thực thi
- **Nguyên nhân**: Cấu hình hàng đợi sai, ví dụ lỗi kết nối Redis hoặc cấu hình driver hàng đợi sai
- **Giải pháp**: Kiểm tra cấu hình hàng đợi, đảm bảo cấu hình chính xác

### 6.4 Không ghi được log

- **Vấn đề**: Không thể ghi log vào file
- **Nguyên nhân**: Thư mục log không có quyền ghi
- **Giải pháp**: Cấp quyền ghi cho thư mục log

### 6.5 Biến môi trường không có hiệu lực

- **Vấn đề**: Cấu hình biến môi trường không có hiệu lực
- **Nguyên nhân**: File biến môi trường `.env` không tồn tại hoặc cấu hình sai
- **Giải pháp**: Tạo file `.env`, đảm bảo cấu hình chính xác

## 7. Tài liệu tham khảo

- [Cấu hình ThinkPHP 6](https://www.kancloud.cn/manual/thinkphp6_0/1037478)
- [Cấu hình biến môi trường](https://www.kancloud.cn/manual/thinkphp6_0/1037479)
- [Cấu hình cơ sở dữ liệu](https://www.kancloud.cn/manual/thinkphp6_0/1037480)
- [Cấu hình cache](https://www.kancloud.cn/manual/thinkphp6_0/1037481)
- [Cấu hình hàng đợi](https://www.kancloud.cn/manual/thinkphp6_0/1037482)
- [Cấu hình log](https://www.kancloud.cn/manual/thinkphp6_0/1037483)