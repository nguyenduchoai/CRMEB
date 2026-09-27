# Tài liệu hướng dẫn cài đặt CRMEB bằng Docker

## 1. Chuẩn bị môi trường

### 1.1 Cài đặt Docker

Vui lòng chọn cách cài đặt Docker phù hợp với hệ điều hành của bạn:

#### Windows / macOS
Truy cập trang chủ Docker để tải xuống và cài đặt Docker Desktop:
[https://www.docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop)

#### Linux
Dùng lệnh sau để cài đặt Docker:
```bash
curl -sSL https://get.daocloud.io/docker | sh
```

### 1.2 Cài đặt Docker Compose

#### Windows / macOS
Docker Desktop đã bao gồm Docker Compose, không cần cài đặt thêm.

#### Linux
Vui lòng tham khảo tài liệu chính thức để cài đặt Docker Compose:
[https://docs.docker.com/compose/install/](https://docs.docker.com/compose/install/)

## 2. Tải xuống chương trình CRMEB

1. Tải xuống mã nguồn mở mới nhất:
[https://gitee.com/ZhongBangKeJi/CRMEB](https://gitee.com/ZhongBangKeJi/CRMEB)

2. Giải nén chương trình và đặt ở vị trí cùng cấp với thư mục `docker-compose`.

## 3. Cấu hình cài đặt cho từng hệ điều hành

### 3.1 Cấu hình chung (mặc định)

Áp dụng cho hầu hết các hệ thống Linux và macOS dùng chip Intel.

Đường dẫn tệp cấu hình: `docker-compose/docker-compose.yml`

### 3.2 Hệ thống Linux

Cấu hình dành riêng cho Linux, bao gồm các thiết lập tương thích nền tảng.

Đường dẫn tệp cấu hình: `docker-compose/linux/docker-compose.yml`

### 3.3 macOS (chip Intel)

Cấu hình dành riêng cho macOS dùng chip Intel.

Đường dẫn tệp cấu hình: `docker-compose/MacIntel/docker-compose.yml`

### 3.4 macOS (chip Apple Silicon)

Cấu hình dành riêng cho các chip Apple Silicon như MacBook M1/M2/M3, đã khắc phục vấn đề tương thích của MySQL.

Đường dẫn tệp cấu hình: `docker-compose/MacArm/docker-compose.yml`

### 3.5 Hệ thống Windows

Cấu hình dành riêng cho Windows.

Đường dẫn tệp cấu hình: `docker-compose/window/docker-compose.yml`

## 4. Mô tả cấu hình dịch vụ

### 4.1 Cơ sở dữ liệu MySQL

| Mục cấu hình | Giá trị mặc định | Mô tả |
|--------|--------|------|
| Tên container | crmeb_mysql | Tên container Docker |
| Image | mysql:5.7 | Image cơ sở dữ liệu (MacArm dùng mysql/mysql-server) |
| Cổng (port) | 3336:3306 | Cổng máy host:Cổng container |
| Tên người dùng | root | Tên đăng nhập cơ sở dữ liệu |
| Mật khẩu | 123456 | Mật khẩu cơ sở dữ liệu |
| Tên cơ sở dữ liệu | crmeb | Tên cơ sở dữ liệu được tạo mặc định |
| IP container | 192.168.10.11 | IP cố định trong mạng nội bộ |

### 4.2 Bộ nhớ đệm Redis

| Mục cấu hình | Giá trị mặc định | Mô tả |
|--------|--------|------|
| Tên container | crmeb_redis | Tên container Docker |
| Image | redis:alpine | Image Redis |
| Cổng (port) | 6379:6379 | Cổng máy host:Cổng container |
| IP container | 192.168.10.10 | IP cố định trong mạng nội bộ |

### 4.3 Ứng dụng PHP

| Mục cấu hình | Giá trị mặc định | Mô tả |
|--------|--------|------|
| Tên container | crmeb_php | Tên container Docker |
| Image | crmeb_php | Image PHP (build từ Dockerfile) |
| Cổng (port) | 9000:9000 | Cổng PHP-FPM |
|  | 20002:20002 | Cổng kết nối liên tục 1 |
|  | 20003:20003 | Cổng kết nối liên tục 2 |
| IP container | 192.168.10.90 | IP cố định trong mạng nội bộ |
| Thư mục chương trình | /var/www | Đường dẫn của dự án trong container |

### 4.4 Máy chủ Nginx

| Mục cấu hình | Giá trị mặc định | Mô tả |
|--------|--------|------|
| Tên container | crmeb_nginx | Tên container Docker |
| Image | nginx:alpine | Image Nginx |
| Cổng (port) | 8011:80 | Cổng máy host:Cổng container |
| IP container | 192.168.10.80 | IP cố định trong mạng nội bộ |

## 5. Khởi chạy dự án

### 5.1 Các bước khởi động cơ bản

1. Vào thư mục docker-compose:
```bash
cd docker-compose
```

2. Khởi động tất cả dịch vụ:
```bash
docker-compose up -d
```

3. Xem trạng thái container:
```bash
docker-compose ps
```

### 5.2 Khởi động các dịch vụ bổ sung (bắt buộc)

Vào container PHP và khởi động các dịch vụ hàng đợi, tác vụ định kỳ và kết nối liên tục:

1. Vào container PHP:
```bash
docker exec -it crmeb_php /bin/bash
```

2. Vào thư mục dự án:
```bash
cd /var/www
```

3. Khởi động tác vụ định kỳ:
```bash
php think timer start --d
```

4. Khởi động dịch vụ kết nối liên tục:
```bash
php think workerman start --d
```

5. Khởi động dịch vụ hàng đợi:
```bash
php think queue:listen --queue
```

## 6. Truy cập hệ thống CRMEB

### 6.1 Địa chỉ truy cập

Nhập địa chỉ sau vào trình duyệt để truy cập hệ thống CRMEB:
```
http://localhost:8011/
```

### 6.2 Cài đặt hệ thống

Lần truy cập đầu tiên sẽ vào trình hướng dẫn cài đặt CRMEB, hãy làm theo hướng dẫn để hoàn tất cài đặt hệ thống.

#### Cấu hình cơ sở dữ liệu

| Mục cấu hình | Giá trị |
|--------|-----|
| Địa chỉ cơ sở dữ liệu | 192.168.10.11 |
| Cổng (port) | 3306 |
| Tên người dùng | root |
| Mật khẩu | 123456 |
| Tên cơ sở dữ liệu | crmeb |

#### Cấu hình Redis

| Mục cấu hình | Giá trị |
|--------|-----|
| Địa chỉ Redis | 192.168.10.10 |
| Cổng (port) | 6379 |
| Cơ sở dữ liệu | 0 |
| Mật khẩu | 123456 |

## 7. Quản lý container

### 7.1 Dừng dịch vụ

```bash
# Dừng tất cả dịch vụ
docker-compose down

# Dừng dịch vụ chỉ định
docker-compose stop <service-name>
```

### 7.2 Khởi động lại dịch vụ

```bash
# Khởi động lại tất cả dịch vụ
docker-compose restart

# Khởi động lại dịch vụ chỉ định
docker-compose restart <service-name>
```

### 7.3 Xem log

```bash
# Xem log của tất cả dịch vụ
docker-compose logs

# Xem log của dịch vụ chỉ định
docker-compose logs <service-name>

# Xem log theo thời gian thực
docker-compose logs -f <service-name>
```

## 8. Vấn đề thường gặp và giải pháp

### 8.1 Cổng bị chiếm dụng

**Vấn đề**: Khi khởi động xuất hiện lỗi cổng bị chiếm dụng

**Cách khắc phục**:
1. Sửa ánh xạ cổng trong `docker-compose.yml`, ví dụ đổi `8011:80` thành `8080:80`
2. Khởi động lại dịch vụ

### 8.2 Xung đột địa chỉ IP

**Vấn đề**: `Error response from daemon: Address already in use`

**Cách khắc phục**:
1. Sửa `ipv4_address` của container bị xung đột trong `docker-compose.yml`
2. Đảm bảo địa chỉ IP nằm trong dải mạng `192.168.*.*` và không xung đột với thiết bị khác

### 8.3 Container MySQL khởi động thất bại (chip Mac ARM)

**Vấn đề**: Container MySQL không khởi động được, không có log đầu ra

**Cách khắc phục**:
1. Dùng cấu hình chuyên dụng trong thư mục MacArm
2. Đảm bảo đã dùng đúng image MySQL (`mysql/mysql-server`)
3. Kiểm tra thiết lập quyền tệp

### 8.4 Thiếu extension PHP

**Vấn đề**: Hệ thống báo thiếu một số extension PHP

**Cách khắc phục**:
1. Vào container PHP
2. Cài đặt các extension cần thiết
3. Hoặc sửa `docker-compose/php/Dockerfile` để thêm extension rồi build lại image

### 8.5 Vấn đề quyền tệp

**Vấn đề**: Chương trình không thể ghi tệp hoặc tạo thư mục

**Cách khắc phục**:
1. Kiểm tra quyền của thư mục `crmeb` trên máy host
2. Đảm bảo người dùng `www-data` trong container có đủ quyền
3. Có thể thử thay đổi quyền thư mục:
   ```bash
   chmod -R 777 crmeb/runtime
   chmod -R 777 crmeb/public/upload
   ```

## 9. Lưu ý

### 9.1 Lưu trữ dữ liệu bền vững

- Dữ liệu MySQL mặc định được mount vào thư mục `docker-compose/mysql/data`
- Dữ liệu Redis mặc định không được mount, nếu cần lưu trữ bền vững vui lòng sửa tệp cấu hình
- Code dự án được mount vào thư mục `crmeb`, sửa code trên máy host sẽ ảnh hưởng trực tiếp đến chương trình trong container

### 9.2 Cấu hình mạng

- Tất cả dịch vụ chạy trong mạng `app_net`, dùng địa chỉ IP cố định
- Máy host và container giao tiếp với nhau qua ánh xạ cổng
- Các container có thể giao tiếp trực tiếp với nhau qua IP nội bộ

### 9.3 Tối ưu hiệu năng

- Điều chỉnh giới hạn tài nguyên của container theo cấu hình máy chủ
- Ở môi trường production nên đổi mật khẩu và cổng mặc định
- Cấu hình chính sách dọn dẹp log phù hợp

### 9.4 Hướng dẫn nâng cấp

1. Dừng tất cả dịch vụ
2. Sao lưu dữ liệu và tệp cấu hình
3. Cập nhật code
4. Khởi động lại dịch vụ
5. Chạy migration cơ sở dữ liệu (nếu cần)

## 10. Cấu hình nâng cao

### 10.1 Đổi mật khẩu mặc định

Chỉnh sửa tệp `docker-compose.yml`, thay đổi các biến môi trường sau:

- MySQL: `MYSQL_ROOT_PASSWORD`, `MYSQL_PASS`
- Redis: Cấu hình mật khẩu trong `redis.conf`

### 10.2 Cấu hình HTTPS

1. Bật ánh xạ cổng 443 trong `docker-compose.yml`
2. Chuẩn bị chứng chỉ SSL
3. Sửa `nginx/vhost.conf` để cấu hình HTTPS

### 10.3 Tùy chỉnh cấu hình PHP

Sửa tệp `docker-compose/php/php-ini-overrides.ini` để tùy chỉnh cấu hình PHP.

## 11. Hỗ trợ kỹ thuật

Nếu bạn gặp vấn đề trong quá trình cài đặt, có thể nhận trợ giúp qua các kênh sau:

- Cộng đồng chính thức của CRMEB: [https://gitee.com/ZhongBangKeJi/CRMEB/issues](https://gitee.com/ZhongBangKeJi/CRMEB/issues)
- Tài liệu chính thức của Docker: [https://docs.docker.com/](https://docs.docker.com/)

---

**Phiên bản tài liệu**: v1.0
**Ngày cập nhật**: 2023-12-04
**Phiên bản áp dụng**: CRMEB v5.6+
---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
