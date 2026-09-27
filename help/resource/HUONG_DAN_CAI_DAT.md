# Hướng dẫn cài đặt CRMEB

> Yêu cầu môi trường chạy: PHP 7.1~7.4, phiên bản cơ sở dữ liệu là MySQL 5.7~8.0

---

## I. Cài đặt một cú nhấp

1. Tạo website, chọn thư mục chạy trong thư mục gốc của dự án là `/public`
2. Thiết lập rewrite URL (giả tĩnh) theo quy tắc của ThinkPHP
3. Nhập tên miền hoặc IP của bạn vào trình duyệt (ví dụ: www.yourdomain.com)
4. Trình cài đặt sẽ tự động thực hiện cài đặt, trong quá trình đó hệ thống sẽ nhắc bạn nhập thông tin cơ sở dữ liệu để hoàn tất cài đặt
5. Sau khi cài đặt xong, khuyến nghị xóa hoặc đổi tên tệp `index.php` trong thư mục `install`

### Địa chỉ truy cập

| Cổng (port) | Địa chỉ |
|------|------|
| Trang quản trị | tên-miền/admin |
| Trang chủ OA WeChat và H5 | tên-miền/ |

> Lưu ý: Nếu không truy cập được, vui lòng kiểm tra xem [Rewrite URL](https://doc.crmeb.com/web/single/crmeb_v4/1139) đã được cấu hình đúng chưa
> 
> Vui lòng ghi nhớ tài khoản và mật khẩu của bạn trong quá trình cài đặt!

---

## II. Cài đặt lại

1. Xóa cơ sở dữ liệu
2. Xóa tệp `/public/install.lock`

---

## III. Cài đặt thủ công

### 1. Tạo cơ sở dữ liệu

Nhập tệp cơ sở dữ liệu: `/public/install/crmeb.sql`

### 2. Sửa tệp kết nối cơ sở dữ liệu

Đường dẫn tệp cấu hình: `/.env`

```ini
APP_DEBUG = true

[APP]
DEFAULT_TIMEZONE = Asia/Shanghai

[DATABASE]
TYPE = mysql
HOSTNAME = 127.0.0.1
HOSTPORT = 3306
USERNAME = root
PASSWORD = 'root'
DATABASE = crmeb
PREFIX = eb_
CHARSET = utf8
DEBUG = true

[LANG]
default_lang = zh-cn

[REDIS]
REDIS_HOSTNAME = 127.0.0.1
PORT = 6379
REDIS_PASSWORD = 
SELECT = 0

[QUEUE]
QUEUE_NAME = crmeb
```

### 3. Sửa quyền thư mục (hệ thống Linux)

Các thư mục sau cần được đặt quyền 777:

```bash
chmod -R 777 public runtime
```

### 4. Đăng nhập trang quản trị

- Địa chỉ: http://ten-mien-cua-ban/admin
- Tài khoản mặc định: `admin`
- Mật khẩu mặc định: `crmeb.com`

---

## IV. Hàng đợi tin nhắn (message queue)

Trên hệ thống Linux, cài đặt trình quản lý Supervisor và thêm tiến trình nền (daemon):

- Người dùng: `www`
- Thư mục chạy: thư mục gốc của dự án
- Lệnh khởi động:

```bash
php think queue:listen --queue
```

---

## V. Tác vụ định kỳ

Được sử dụng trong các chức năng như tự động xác nhận đã nhận hàng, cảnh báo tồn kho và các chức năng khác:

```bash
php think timer [status] [--d]
```

| Tham số | Mô tả |
|------|------|
| status | Trạng thái: start (khởi động), stop (dừng), restart (khởi động lại) |
| --d | Chạy nền |

---

## VI. Dịch vụ kết nối liên tục

Được sử dụng trong các chức năng như trò chuyện trên H5, thông báo tin nhắn cho quản trị viên ở trang quản trị và các chức năng khác.

### 1. Sửa cấu hình Nginx

```nginx
# Thông báo tin nhắn cho quản trị viên (tương ứng dịch vụ admin, cổng 40001)
location /notice {
    proxy_pass http://127.0.0.1:40001/;  
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header X-real-ip $remote_addr;
    proxy_set_header X-Forwarded-For $remote_addr;
}

# Dịch vụ chat trên H5 (tương ứng dịch vụ chat, cổng 40002)
location /msg {
    proxy_pass http://127.0.0.1:40002/;  
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header X-real-ip $remote_addr;
    proxy_set_header X-Forwarded-For $remote_addr;
}
```

> Cổng tương ứng phải khớp với cấu hình trong `/config/workerman.php`

### 2. Khởi động dịch vụ

**Hệ thống Linux:**

```bash
php think workerman [status] [server] [--d]
```

**Môi trường Windows:**

Cần thực hiện theo ba bước:

```bash
# Dịch vụ giao tiếp nội bộ
php think workerman start channel

# Dịch vụ chat trên H5
php think workerman start chat

# Thông báo cho quản trị viên
php think workerman start admin
```

Hoặc nhấp đúp để chạy trực tiếp `/workerman.bat`

### 3. Giải thích tham số

| Tham số | Mô tả |
|------|------|
| status | Trạng thái: start (khởi động), stop (dừng), restart (khởi động lại) |
| server | Dịch vụ (Windows): channel (giao tiếp nội bộ), chat (H5), admin (trang quản trị) |
| --d | Chạy nền |

---

## VII. Triển khai nhanh bằng Docker

```bash
# Pull và chạy image Docker CRMEB
docker run -d --name crmeb -p 8080:80 ccr.ccs.tencentyun.com/crmebky_php/crmebky:latest
```

### Truy cập dịch vụ

| Dịch vụ | Địa chỉ | Tài khoản/Mật khẩu |
|------|------|----------|
| Website | http://localhost:8080 | - |
| Trang quản trị | http://localhost:8080/admin | admin / crmeb.com |
| MySQL | localhost:3306 | root / 123456 |
| Redis | localhost:6379 | - |

> Xem hướng dẫn chi tiết tại [Tài liệu triển khai Docker](/help/docker/README.md)

---

## Liên kết liên quan

- [Cài đặt và triển khai nhanh bằng một cú nhấp](https://doc.crmeb.com/single_open/open_v54/20366)
- [Cấu hình và cài đặt thủ công](https://doc.crmeb.com/single_open/open_v54/20389)
- [Triển khai bằng một cú nhấp với Docker-Compose](https://doc.crmeb.com/single_open/open_v54/20145)
- [Cài đặt bằng một cú nhấp trên môi trường BT Panel](https://doc.crmeb.com/single_open/open_v54/19892)
- [Tài liệu sử dụng](https://doc.crmeb.com/single_open/open_v54/19849)
- [Tài liệu API](https://doc.crmeb.com/single_open/open_v54/21040)
