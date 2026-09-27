# Hướng dẫn chạy CRMEB Docker chỉ với một lệnh

## Thông tin image

- **Địa chỉ image**: `ccr.ccs.tencentyun.com/crmebky_php/crmebky:latest`
- **Kiến trúc hỗ trợ**: `linux/amd64`, `linux/arm64` (tự động thích ứng)

## Khởi động nhanh

```bash
# Tải image về
docker pull ccr.ccs.tencentyun.com/crmebky_php/crmebky:latest

# Chạy container
docker run -d --name crmeb \
  -p 80:80 \
  -p 3306:3306 \
  -p 6379:6379 \
  ccr.ccs.tencentyun.com/crmebky_php/crmebky:latest
```

## Truy cập dịch vụ

- **Website**: http://localhost
- **MySQL**: localhost:3306 (tài khoản: root, mật khẩu: 123456)
- **Redis**: localhost:6379

## Các dịch vụ có trong container

- Nginx (cổng 80)
- PHP-FPM 7.4
- MySQL 8.0 (tài khoản: root/crmeb, mật khẩu: 123456)
- Redis
- Hàng đợi tin nhắn
- Tác vụ định kỳ
- Workerman

## Lưu trữ dữ liệu bền vững (tùy chọn)

### Các thư mục nên mount

| Đường dẫn trong container | Mô tả | Cách mount khuyến nghị |
|---------|------|-------------|
| `/var/lib/mysql` | Thư mục dữ liệu MySQL | Bắt buộc mount, tránh mất dữ liệu |
| `/var/www/crmeb/public/uploads` | Thư mục tệp tải lên | Nên mount, để lưu các tệp do người dùng tải lên |
| `/var/www/crmeb/runtime` | Thư mục cache/log | Mount tùy chọn |
| `/var/lib/redis` | Thư mục dữ liệu Redis | Mount tùy chọn |

### Sử dụng Docker Volume

```bash
docker run -d --name crmeb \
  -p 80:80 \
  -p 3306:3306 \
  -p 6379:6379 \
  -v crmeb_mysql:/var/lib/mysql \
  -v crmeb_uploads:/var/www/crmeb/public/uploads \
  ccr.ccs.tencentyun.com/crmebky_php/crmebky:latest
```

### Mount bằng thư mục trên máy local

```bash
# Tạo thư mục cục bộ
mkdir -p ~/crmeb-data/mysql ~/crmeb-data/uploads

# Chạy container
docker run -d --name crmeb \
  -p 80:80 \
  -p 3306:3306 \
  -p 6379:6379 \
  -v ~/crmeb-data/mysql:/var/lib/mysql \
  -v ~/crmeb-data/uploads:/var/www/crmeb/public/uploads \
  ccr.ccs.tencentyun.com/crmebky_php/crmebky:latest
```

### Ví dụ đầy đủ (khuyến nghị cho môi trường production)

```bash
# Tạo thư mục cục bộ
mkdir -p ~/crmeb-data/{mysql,uploads,runtime,redis}

# Chạy container
docker run -d --name crmeb \
  -p 80:80 \
  -p 3306:3306 \
  -p 6379:6379 \
  -v ~/crmeb-data/mysql:/var/lib/mysql \
  -v ~/crmeb-data/uploads:/var/www/crmeb/public/uploads \
  -v ~/crmeb-data/runtime:/var/www/crmeb/runtime \
  -v ~/crmeb-data/redis:/var/lib/redis \
  --restart unless-stopped \
  ccr.ccs.tencentyun.com/crmebky_php/crmebky:latest
```

## Các lệnh thường dùng

```bash
# Xem log container
docker logs -f crmeb

# Vào container
docker exec -it crmeb /bin/bash

# Dừng container
docker stop crmeb

# Khởi động container
docker start crmeb

# Xóa container (giữ lại volume dữ liệu)
docker rm crmeb

# Xóa hoàn toàn (bao gồm cả volume dữ liệu)
docker rm -v crmeb
```
