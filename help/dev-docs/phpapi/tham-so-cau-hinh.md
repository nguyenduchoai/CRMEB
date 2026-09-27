# Mô tả tham số cấu hình

## ⚙️ Cấu hình cốt lõi của hệ thống

### Cấu hình cơ sở dữ liệu
```php
// config/database.php
'connections' => [
    'mysql' => [
        'hostname' => '127.0.0.1',    // Host cơ sở dữ liệu
        'database' => 'crmeb',         // Tên cơ sở dữ liệu
        'username' => 'root',          // Tên người dùng
        'password' => '',              // Mật khẩu
        'hostport' => '3306',          // Cổng (port)
        'charset'  => 'utf8mb4',       // Bộ ký tự
    ]
]
```

### Cấu hình cache Redis
```php
// config/cache.php
'redis' => [
    'host'     => '127.0.0.1',    // Host Redis
    'port'     => 6379,           // Cổng Redis
    'password' => '',             // Mật khẩu
    'select'   => 0,              // Chỉ số cơ sở dữ liệu
    'timeout'  => 0,              // Thời gian timeout
]
```

### Cấu hình lưu trữ tệp
```php
// config/filesystem.php
'disks' => [
    'local' => [
        'driver' => 'local',      // Lưu trữ cục bộ
        'root'   => app()->getRootPath() . 'public/uploads',
    ],
    'qiniu' => [                  // Lưu trữ Qiniu Cloud
        'driver'     => 'qiniu',
        'access_key' => env('qiniu.access_key'),
        'secret_key' => env('qiniu.secret_key'),
        'bucket'     => env('qiniu.bucket'),
        'domain'     => env('qiniu.domain'),
    ]
]
```

## 🔧 Cấu hình liên quan đến nghiệp vụ

### Cấu hình SMS
```php
// Cấu hình nhà cung cấp SMS
'sms' => [
    'default' => 'aliyun',        // Nhà cung cấp mặc định
    'aliyun' => [                 // SMS Alibaba Cloud
        'access_key_id' => '',
        'access_key_secret' => '',
        'sign_name' => '',        // Chữ ký SMS
    ]
]
```

### Cấu hình thanh toán
```php
// Cấu hình WeChat Pay
'wechat_pay' => [
    'app_id' => '',              // ID ứng dụng
    'mch_id' => '',              // Mã merchant
    'key'    => '',              // Khóa API
    'cert_path' => '',           // Đường dẫn chứng chỉ
    'key_path'  => '',           // Đường dẫn khóa bí mật
]
```

### Cấu hình dịch vụ bên thứ ba
```php
// Cấu hình tra cứu vận chuyển
'express' => [
    'default' => 'kuaidi100',    // Dịch vụ tra cứu vận chuyển mặc định
    'kuaidi100' => [             // Kuaidi100
        'customer' => '',        // Mã khách hàng
        'key'      => '',        // Key ủy quyền
    ]
]
```

## 🌐 Cấu hình biến môi trường (.env)

```ini
# Cấu hình cơ sở dữ liệu
DATABASE_HOST=127.0.0.1
DATABASE_NAME=crmeb
DATABASE_USERNAME=root
DATABASE_PASSWORD=

# Cấu hình Redis  
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=
REDIS_SELECT=0

# Cấu hình ứng dụng
APP_DEBUG=false
APP_URL=http://localhost

# Cấu hình tải lên tệp
UPLOAD_TYPE=local
UPLOAD_MAXSIZE=10485760
```

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
