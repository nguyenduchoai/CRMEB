# Tài liệu quy trình cài đặt hệ thống CRMEB

## 📋 Tổng quan tài liệu

Tài liệu này cung cấp quy trình cài đặt đầy đủ của hệ thống cửa hàng trực tuyến mã nguồn mở CRMEB, áp dụng cho phiên bản CRMEB 5.6.4+.

## 🖥️ Yêu cầu môi trường máy chủ

### Yêu cầu cấu hình cơ bản

| Mục cấu hình | Cấu hình tối thiểu | Cấu hình đề xuất |
|--------|---------|---------|
| CPU | 1 nhân | 2 nhân trở lên |
| RAM | 1GB | 2GB trở lên |
| Ổ cứng | 20GB SSD | 50GB SSD trở lên |
| Băng thông | 1Mbps | 5Mbps trở lên |

### Yêu cầu hệ điều hành

| Loại hệ thống | Phiên bản hỗ trợ |
|---------|---------|
| Linux | CentOS 7+/Ubuntu 18+/Debian 10+ |
| Windows | Windows Server 2012+ |
| macOS | Chỉ dùng cho môi trường phát triển |

### Yêu cầu môi trường phần mềm

| Phần mềm | Phiên bản tối thiểu | Phiên bản khuyến nghị | Mô tả |
|------|---------|---------|------|
| PHP | 7.1 | 7.4 | Bắt buộc bật extension PDO |
| MySQL | 5.7 | 8.0 | Khuyến nghị dùng engine InnoDB |
| Redis | 3.0 | 7.0 | Tùy chọn, giúp tăng hiệu năng |
| Nginx | 1.18 | 1.22 | Máy chủ reverse proxy |
| Composer | 1.8 | 2.x | Quản lý phụ thuộc (dependency) PHP |
| Git | 2.0 | Phiên bản mới nhất | Quản lý phiên bản code |

### Yêu cầu extension PHP

```
Các extension bắt buộc phải bật:
- fileinfo       (Thông tin tệp)
- pdo            (Thao tác cơ sở dữ liệu)
- pdo_mysql      (Driver MySQL)
- openssl        (Xử lý mã hóa)
- mbstring       (Xử lý chuỗi)
- curl           (Request HTTP)
- json           (Xử lý JSON)
- session        (Quản lý session)

Các extension nên bật:
- redis          (Bộ nhớ đệm Redis)
- gd             (Xử lý ảnh)
- zip            (Xử lý nén)
- bcmath         (Tính toán độ chính xác cao)
```

### Danh sách hàm bị vô hiệu hóa

```php
// php.ini - các hàm cần bỏ vô hiệu hóa
disable_functions = 
; Gỡ bỏ các hàm sau:
; proc_open
; pcntl_signal
; pcntl_signal_dispatch
; pcntl_fork
; pcntl_wait
; pcntl_alarm
```

## 🐳 Cách 1: Triển khai Docker bằng một lệnh (khuyến nghị)

### 1. Chuẩn bị môi trường

```bash
# Kiểm tra Docker đã được cài đặt chưa
docker --version
# Docker version 20.10.x, build xxxxx

# Kiểm tra Docker Compose
docker-compose --version
# Docker Compose version v2.x.x

# Nếu chưa cài đặt, vui lòng cài Docker trước
# Cài đặt trên hệ thống Ubuntu:
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER

# Cài đặt Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### 2. Lấy mã nguồn dự án

```bash
# Clone dự án
git clone https://gitee.com/ZhongBangKeJi/CRMEB.git crmeb
cd crmeb

# Chuyển sang nhánh phiên bản ổn định (chọn theo nhu cầu)
git checkout -b v5.6.4 origin/v5.6.4
```

### 3. Cấu hình biến môi trường

```bash
# Sao chép mẫu biến môi trường
cp .env.example .env

# Chỉnh sửa biến môi trường
vim .env
```

```ini
# Cấu hình cơ sở dữ liệu
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=crmeb
DB_USERNAME=root
DB_PASSWORD=crmeb123456

# Cấu hình Redis
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=

# Cấu hình ứng dụng
APP_DEBUG=true
APP_URL=http://localhost

# Cấu hình trang quản trị Admin
ADMIN_HTTPS=false
```

### 4. Khởi động bằng một lệnh

```bash
# Build và khởi động tất cả dịch vụ
docker-compose up -d --build

# Xem trạng thái dịch vụ
docker-compose ps

# Ví dụ đầu ra:
#   Name                 Command               State           Ports
# ----------------------------------------------------------------------
# crmeb-app     docker-php-entrypoint apach ...   Up              0.0.0.0:80->80/tcp
# crmeb-mysql   docker-entrypoint.sh mysqld ...   Up              3306/tcp
# crmeb-redis   docker-entrypoint.sh redis ...   Up              6379/tcp
```

### 5. Truy cập trình hướng dẫn cài đặt

Mở trình duyệt và truy cập: `http://your-server-ip`

Làm theo các bước của trình hướng dẫn cài đặt để hoàn tất cài đặt hệ thống:
1. Kiểm tra môi trường
2. Kiểm tra quyền thư mục
3. Cấu hình cơ sở dữ liệu
4. Thiết lập tài khoản quản trị viên
5. Cài đặt hoàn tất

### Lệnh quản lý Docker

```bash
# Khởi động dịch vụ
docker-compose start

# Dừng dịch vụ
docker-compose stop

# Khởi động lại dịch vụ
docker-compose restart

# Xem log
docker-compose logs -f

# Xóa dịch vụ (giữ lại dữ liệu)
docker-compose down

# Xóa dịch vụ (bao gồm cả volume dữ liệu)
docker-compose down -v
```

## 🖥️ Cách 2: Triển khai thủ công (BT Panel)

### 1. Cài đặt BT Panel

```bash
# Hệ thống CentOS
yum install -y wget && wget -O install.sh http://download.bt.cn/install/install_6.0.sh && sh install.sh

# Ubuntu/Debian
wget -O install.sh http://download.bt.cn/install/install-ubuntu_6.0.sh && sudo bash install.sh
```

### 2. Đăng nhập BT Panel

Sau khi cài đặt xong, ghi lại địa chỉ đăng nhập, tên người dùng và mật khẩu của panel, rồi đăng nhập BT Panel.

### 3. Cài đặt bộ phần mềm môi trường

Trong BT Panel:
1. Nhấn vào “Cửa hàng phần mềm” (App Store)
2. Cài đặt các phần mềm sau:
   - Nginx 1.22
   - MySQL 5.7/8.0
   - PHP 7.4
   - phpMyAdmin 4.7 (tùy chọn, dùng để quản lý cơ sở dữ liệu)

### 4. Tạo trang web

1. Nhấn “Website” → “Thêm trang web”
2. Điền thông tin trang web:
   - Tên miền: `your-domain.com` (hoặc dùng IP để thử nghiệm)
   - Thư mục gốc: `/www/wwwroot/crmeb`
   - Phiên bản PHP: chọn 7.4
   - Cơ sở dữ liệu: chọn tạo cơ sở dữ liệu
   - Tên người dùng và mật khẩu cơ sở dữ liệu
   - FTP: chọn tạo

### 5. Tải lên mã nguồn dự án

```bash
# Cách 1: Tải lên qua BT Panel
# 1. Tải mã nguồn dự án về máy
# 2. Tải lên và giải nén qua trình quản lý tệp của BT Panel

# Cách 2: Qua dòng lệnh
cd /www/wwwroot
wget https://gitee.com/ZhongBangKeJi/CRMEB/repository/archive/master.zip
unzip master.zip
mv CRMEB-master crmeb
```

### 6. Cấu hình quyền thư mục

```bash
# Thiết lập quyền thư mục
chmod -R 755 /www/wwwroot/crmeb
chmod -R 755 /www/wwwroot/crmeb/runtime
chmod -R 755 /www/wwwroot/crmeb/public/uploads

# Thiết lập chủ sở hữu
chown -R www:www /www/wwwroot/crmeb
```

### 7. Cấu hình rewrite URL (giả tĩnh)

Trong BT Panel:
1. Nhấn “Cài đặt” của trang web tương ứng
2. Chọn “Rewrite URL (giả tĩnh)”
3. Thêm các quy tắc sau:

```nginx
# ThinkPHP (quy tắc rewrite URL giả tĩnh)
location / {
    if (!-e $request_filename) {
        rewrite  ^(.*)$  /index.php?s=$1  last;
        break;
    }
}

# Cấu hình PHP
location ~ \.php$ {
    fastcgi_pass   unix:/tmp/php-cgi-74.sock;
    fastcgi_index  index.php;
    fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
    include        fastcgi_params;
}
```

### 8. Cấu hình giới hạn tải lên của PHP

Trong BT Panel, vào “Cửa hàng phần mềm” → “PHP 7.4” → “Sửa cấu hình”:
- upload_max_filesize：100M
- post_max_size：100M
- max_execution_time：300
- max_input_time：300
- memory_limit：256M

### 9. Truy cập để cài đặt

Mở trình duyệt và truy cập: `http://your-domain.com`

Làm theo trình hướng dẫn cài đặt để hoàn tất cài đặt hệ thống.

## 🖥️ Cách 3: Triển khai thủ công (dòng lệnh)

### 1. Cài đặt môi trường PHP

```bash
# CentOS 7
yum install epel-release -y
yum install -y yum-utils

# Thêm nguồn Remi
yum install http://rpms.remirepo.net/enterprise/remi-release-7.rpm -y

# Cài đặt PHP 7.4
yum-config-manager --enable remi-php74
yum install -y php php-cli php-fpm php-mysql php-xml php-mbstring php-curl php-redis php-gd php-zip php-bcmath

# Khởi động PHP-FPM
systemctl start php-fpm
systemctl enable php-fpm
```

### 2. Cài đặt Nginx

```bash
# CentOS
yum install -y nginx

# Khởi động Nginx
systemctl start nginx
systemctl enable nginx
```

### 3. Cài đặt MySQL

```bash
# CentOS 7 - Cài đặt MySQL 5.7
wget https://dev.mysql.com/get/mysql57-community-release-el7-9.noarch.rpm
rpm -ivh mysql57-community-release-el7-9.noarch.rpm
yum install -y mysql-community-server

# Khởi động MySQL
systemctl start mysqld
systemctl enable mysqld

# Xem mật khẩu ban đầu
grep 'temporary password' /var/log/mysqld.log

# Đăng nhập và đổi mật khẩu
mysql -u root -p
```

```sql
-- MySQL 5.7 Khởi tạo
ALTER USER 'root'@'localhost' IDENTIFIED BY 'YourNewPassword!';

-- Tạo cơ sở dữ liệu
CREATE DATABASE crmeb DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Tạo người dùng và cấp quyền
CREATE USER 'crmeb'@'localhost' IDENTIFIED BY 'crmeb_password!';
GRANT ALL PRIVILEGES ON crmeb.* TO 'crmeb'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Cài đặt Redis

```bash
# CentOS
yum install -y redis

# Khởi động Redis
systemctl start redis
systemctl enable redis

# Cấu hình mật khẩu (sửa /etc/redis.conf）
requirepass your_redis_password
```

### 5. Cài đặt Composer

```bash
# Tải xuống và cài đặt
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Chuyển sang mirror Trung Quốc (tăng tốc tải xuống)
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
```

### 6. Lấy mã nguồn dự án

```bash
# Clone dự án
cd /var/www
git clone https://gitee.com/ZhongBangKeJi/CRMEB.git crmeb
cd crmeb

# Chuyển sang phiên bản ổn định
git checkout -b v5.6.4 origin/v5.6.4
```

### 7. Cấu hình biến môi trường

```bash
# Sao chép mẫu biến môi trường
cp .env.example .env

# Chỉnh sửa cấu hình
vim .env
```

```ini
[APP]
DEBUG = true
DEFAULT_TIMEZONE = Asia/Shanghai

[DATABASE]
TYPE = mysql
HOSTNAME = 127.0.0.1
DATABASE = crmeb
USERNAME = crmeb
PASSWORD = crmeb_password!
HOSTPORT = 3306
CHARSET = utf8mb4
DEBUG = true
PREFIX = eb_

[CACHE]
TYPE = redis
HOST = 127.0.0.1
PORT = 6379
PREFIX = crmeb:

[LANG]
default_lang = zh-cn
```

### 8. Cài đặt các gói phụ thuộc

```bash
# Chuyển đến thư mục dự án
cd /var/www/crmeb

# Cài đặt các dependency của dự án
composer install --no-dev --optimize-autoloader
```

### 9. Cấu hình site Nginx

```bash
# Tạo cấu hình website
vim /etc/nginx/conf.d/crmeb.conf
```

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/crmeb/public;
    index index.php index.html;

    # Cấu hình log
    access_log /var/log/nginx/crmeb_access.log;
    error_log /var/log/nginx/crmeb_error.log;

    # Bộ nhớ đệm cho tệp tĩnh
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Quy tắc route chính
    location / {
        try_files $uri $uri/ /index.php?s=$uri&$args;
    }

    # Xử lý PHP
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;

        # Cấu hình timeout
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
    }

    # Chặn truy cập các tệp nhạy cảm
    location ~ /\.(env|git|htaccess) {
        deny all;
    }
}
```

```bash
# Kiểm tra cấu hình
nginx -t

# Tải lại Nginx
nginx -s reload
```

### 10. Cấu hình quyền thư mục

```bash
# Thiết lập quyền
chmod -R 755 /var/www/crmeb
chmod -R 755 /var/www/crmeb/runtime
chmod -R 755 /var/www/crmeb/public/uploads

# Thiết lập chủ sở hữu
chown -R nginx:nginx /var/www/crmeb
```

### 11. Khởi động hàng đợi tin nhắn (tùy chọn)

```bash
# Dùng Supervisor để quản lý hàng đợi
yum install -y supervisor

# Cấu hình Supervisor
vim /etc/supervisord.d/crmeb.ini
```

```ini
[program:crmeb-queue]
command=php /var/www/crmeb/think queue:listen --queue
directory=/var/www/crmeb
user=nginx
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
```

```bash
# Khởi động Supervisor
systemctl start supervisord
systemctl enable supervisord

# Khởi động tác vụ hàng đợi
supervisorctl update
supervisorctl start crmeb-queue
```

### 12. Khởi động tác vụ định kỳ (tùy chọn)

```bash
# Sửa tác vụ định kỳ
crontab -e

# Thêm tác vụ sau
* * * * * cd /var/www/crmeb && php think timer start >> /dev/null 2>&1
```

### 13. Truy cập để cài đặt

Mở trình duyệt và truy cập: `http://your-domain.com`

Làm theo trình hướng dẫn cài đặt để hoàn tất cài đặt hệ thống.

## 🔧 Cấu hình sau khi cài đặt

### 1. Cấu hình HTTPS (khuyến nghị)

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com;

    ssl_certificate /etc/nginx/ssl/crmeb.crt;
    ssl_certificate_key /etc/nginx/ssl/crmeb.key;
    ssl_session_timeout 5m;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:HIGH:!aNULL:!MD5:!RC4:!DHE;

    # Các cấu hình khác giống như trên...
}

# Chuyển hướng HTTP sang HTTPS
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}
```

### 2. Cấu hình lưu trữ Qiniu Cloud (tùy chọn)

Trong hệ thống quản trị:
1. Vào “Cài đặt hệ thống” → “Cấu hình tệp đính kèm”
2. Chọn “Lưu trữ Qiniu Cloud”
3. Cấu hình các tham số sau:
   - AccessKey
   - SecretKey
   - Bucket (tên không gian lưu trữ)
   - Domain (tên miền truy cập)

### 3. Cấu hình dịch vụ SMS (tùy chọn)

Trong hệ thống quản trị:
1. Vào “Cài đặt hệ thống” → “Cài đặt SMS”
2. Chọn nhà cung cấp dịch vụ SMS (Alibaba Cloud, Tencent Cloud, v.v.)
3. Cấu hình khóa API và chữ ký

## 🧪 Xử lý sự cố thường gặp

### Vấn đề 1: Trang hiển thị lỗi 500

```bash
# Kiểm tra log lỗi PHP
tail -f /var/log/php-fpm/error.log

# Kiểm tra log lỗi Nginx
tail -f /var/log/nginx/crmeb_error.log

# Kiểm tra log runtime của dự án
tail -f /var/www/crmeb/runtime/log/xxx.log
```

### Vấn đề 2: Kết nối cơ sở dữ liệu thất bại

```bash
# Kiểm tra kết nối MySQL
mysql -h localhost -u crmeb -p

# Kiểm tra cấu hình cơ sở dữ liệu
cat /var/www/crmeb/.env | grep DB_
```

### Vấn đề 3: Mã xác thực (captcha) không hiển thị

```bash
# Kiểm tra extension GD của PHP
php -m | grep gd

# Kiểm tra quyền thư mục
ls -la /var/www/crmeb/runtime/
```

### Vấn đề 4: Tải lên tệp thất bại

```bash
# Kiểm tra quyền thư mục upload
chmod -R 755 /var/www/crmeb/public/uploads
chown -R nginx:nginx /var/www/crmeb/public/uploads

# Kiểm tra cấu hình PHP
php -i | grep file_uploads
php -i | grep upload_max_filesize
```

### Vấn đề 5: Callback WeChat Pay thất bại

```bash
# Kiểm tra tường lửa
systemctl status firewalld
iptables -L -n | grep 80

# Kiểm tra cấu hình tên miền
# Đảm bảo tên miền callback đã đăng ký ICP và có thể truy cập
```

## 🔐 Khuyến nghị tăng cường bảo mật

### 1. Đổi đường dẫn trang quản trị mặc định

```bash
# Đổi tên thư mục admin
mv public/admin public/backend

# Sửa cấu hình route
vim config/route.php
```

### 2. Cấu hình khóa bảo mật

```bash
# Cấu hình trong .env
APP_KEY=your_random_string_32_chars
```

### 3. Cập nhật hệ thống định kỳ

```bash
# Cập nhật CRMEB định kỳ
cd /var/www/crmeb
git fetch origin
git pull origin v5.6.4

# Cập nhật các phụ thuộc
composer update
```

## 📞 Nhận hỗ trợ kỹ thuật

- **Tài liệu chính thức**: https://doc.crmeb.com/
- **Cộng đồng kỹ thuật**: https://www.crmeb.com/ask/
- **Gitee Issues**：https://gitee.com/ZhongBangKeJi/CRMEB/issues

---
**Phiên bản tài liệu**: v1.0  
**Ngày cập nhật**: 2024-01-17  
**Phiên bản áp dụng**: CRMEB 5.6.4+  

💡 Lưu ý: Khuyến nghị sử dụng giao thức HTTPS trong môi trường production, định kỳ sao lưu cơ sở dữ liệu và tệp.

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
