# Tài liệu triển khai dự án

## 1. Tổng quan

Tài liệu này mô tả quy trình triển khai dự án CRMEB, bao gồm cấu hình môi trường, các bước triển khai, quản lý dịch vụ, v.v., nhằm chuẩn hóa việc triển khai dự án và đảm bảo dự án vận hành ổn định.

## 2. Kiến trúc triển khai

### 2.1 Kiến trúc hệ thống

#### 2.1.1 Kiến trúc cơ bản

- **Máy chủ Web**: Nginx/Apache
- **Máy chủ ứng dụng**: PHP-FPM
- **Máy chủ cơ sở dữ liệu**: MySQL
- **Máy chủ bộ nhớ đệm (cache)**: Redis
- **Máy chủ hàng đợi**: Hàng đợi tích hợp sẵn của ThinkPHP
- **Máy chủ kết nối liên tục**: Workerman

#### 2.1.2 Mô hình triển khai

##### 2.1.2.1 Triển khai trên một máy chủ

```
┌─────────────────────────────────────────────────────┐
│                    Máy chủ                           │
├──────────────┬──────────────┬──────────────┬─────────┤
│  Nginx/Apache│   PHP-FPM    │    MySQL     │  Redis  │
└──────────────┴──────────────┴──────────────┴─────────┘
```

##### 2.1.2.2 Triển khai phân tán

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  Web server  │────>│ Máy chủ ứng dụng │────>│ Máy chủ CSDL │
├──────────────┤     ├──────────────┤     ├──────────────┤
│   Nginx      │     │   PHP-FPM    │     │    MySQL     │
└──────────────┘     └──────────────┘     └──────────────┘
          │                  │                  │
          └──────────────────┼──────────────────┘
                             ▼
                     ┌──────────────┐
                     │  Máy chủ cache  │
                     ├──────────────┤
                     │    Redis     │
                     └──────────────┘
```

### 2.2 Kiến trúc mạng

#### 2.2.1 Cấu trúc liên kết mạng (topology)

- **Mạng công cộng**: Truy cập từ bên ngoài
- **Mạng nội bộ**: Giao tiếp giữa các dịch vụ nội bộ
- **DMZ**: Vùng biên

#### 2.2.2 Quy hoạch cổng

- **HTTP**: 80
- **HTTPS**: 443
- **SSH**: 22
- **MySQL**: 3306
- **Redis**: 6379
- **PHP-FPM**: 9000
- **Workerman**: 8282

## 3. Cấu hình môi trường

### 3.1 Hệ điều hành

#### 3.1.1 Yêu cầu hệ thống

- **Linux**: CentOS 7+/Ubuntu 18.04+
- **Windows**: Windows Server 2016+
- **macOS**: macOS 10.15+

#### 3.1.2 Tối ưu hệ thống

```bash
# Tắt SELinux
setenforce 0
sed -i 's/SELINUX=enforcing/SELINUX=disabled/g' /etc/selinux/config

# Tắt tường lửa (môi trường production nên cấu hình quy tắc)
systemctl stop firewalld
systemctl disable firewalld

# Điều chỉnh file descriptor
cat >> /etc/security/limits.conf << EOF
* soft nofile 65536
* hard nofile 65536
EOF

# Điều chỉnh tham số kernel
cat >> /etc/sysctl.conf << EOF
net.core.somaxconn = 65535
net.ipv4.tcp_max_syn_backlog = 65535
net.ipv4.tcp_fin_timeout = 30
net.ipv4.tcp_keepalive_time = 1200
net.ipv4.tcp_max_tw_buckets = 5000
EOF
sysctl -p
```

### 3.2 Môi trường PHP

#### 3.2.1 Yêu cầu phiên bản

- **PHP**: 7.1~7.4

#### 3.2.2 Các bước cài đặt

```bash
# CentOS Cài đặt PHP 7.4
rpm -Uvh https://mirror.webtatic.com/yum/el7/epel-release.rpm
rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
yum install -y php74w php74w-fpm php74w-cli php74w-mysql php74w-redis php74w-gd php74w-mbstring php74w-xml php74w-zip php74w-opcache

# Ubuntu Cài đặt PHP 7.4
apt update
apt install -y php7.4 php7.4-fpm php7.4-cli php7.4-mysql php7.4-redis php7.4-gd php7.4-mbstring php7.4-xml php7.4-zip php7.4-opcache
```

#### 3.2.3 Tối ưu cấu hình

```php
// php.ini Cấu hình
memory_limit = 512M
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
date.timezone = Asia/Shanghai
opcache.enable = 1
opcache.enable_cli = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 60

// www.conf Cấu hình
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.process_idle_timeout = 10s
pm.max_requests = 1000
```

### 3.3 Máy chủ Web

#### 3.3.1 Cấu hình Nginx

```nginx
# crmeb.conf
server {
    listen 80;
    server_name example.com;
    root /data/www/crmeb/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }

    access_log /data/logs/nginx/crmeb.access.log;
    error_log /data/logs/nginx/crmeb.error.log;
}
```

### 3.4 Cấu hình cơ sở dữ liệu

#### 3.4.1 Cài đặt MySQL

```bash
# CentOS Cài đặt MySQL 5.7
yum localinstall -y https://dev.mysql.com/get/mysql57-community-release-el7-11.noarch.rpm
yum install -y mysql-community-server

systemctl start mysqld
systemctl enable mysqld

# Lấy mật khẩu ban đầu
grep 'temporary password' /var/log/mysqld.log

# Cấu hình bảo mật
mysql_secure_installation

# Ubuntu Cài đặt MySQL 5.7
apt update
apt install -y mysql-server
mysql_secure_installation
```

#### 3.4.2 Tối ưu cấu hình MySQL

```ini
# my.cnf Cấu hình
[mysqld]
bind-address = 127.0.0.1
port = 3306
datadir = /var/lib/mysql
socket = /var/lib/mysql/mysql.sock
user = mysql

# Tối ưu hiệu năng
max_connections = 1000
wait_timeout = 60
interactive_timeout = 28800
key_buffer_size = 64M
table_open_cache = 256
sort_buffer_size = 1M
read_buffer_size = 1M
read_rnd_buffer_size = 4M
myisam_sort_buffer_size = 64M
thread_cache_size = 8
query_cache_size = 16M

# InnoDB tối ưu hóa
innodb_buffer_pool_size = 1G
innodb_file_per_table = 1
innodb_log_file_size = 256M
innodb_log_buffer_size = 8M
innodb_flush_method = O_DIRECT
```

### 3.5 Cấu hình bộ nhớ đệm (cache)

#### 3.5.1 Cài đặt Redis

```bash
# CentOS Cài đặt Redis
yum install -y epel-release
yum install -y redis

systemctl start redis
systemctl enable redis

# Ubuntu Cài đặt Redis
apt update
apt install -y redis-server
systemctl start redis
systemctl enable redis
```

#### 3.5.2 Tối ưu cấu hình Redis

```conf
# redis.conf Cấu hình
bind 127.0.0.1
port 6379
databases 16
dir /var/lib/redis
requirepass your_password
maxmemory 512mb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

## 4. Quy trình triển khai

### 4.1 Triển khai mã nguồn

#### 4.1.1 Triển khai bằng Git

```bash
# Clone code
git clone https://github.com/crmeb/CRMEB.git /data/www/crmeb
cd /data/www/crmeb

# Chuyển phiên bản
git checkout tags/v5.6.4

# Cài đặt các gói phụ thuộc
composer install --no-dev

# Cấu hình biến môi trường
cp .env.example .env
# Sửa file .env, cấu hình thông tin cơ sở dữ liệu, Redis, v.v.

# Tạo khóa
php think key:generate

# Migration cơ sở dữ liệu
php think migrate:run

# Tạo bảng dữ liệu
php think crmeb:install

# Xóa bộ nhớ đệm
php think clear
```

#### 4.1.2 Triển khai thủ công

1. **Tải mã nguồn**: Tải phiên bản mới nhất từ trang web chính thức
2. **Tải mã nguồn lên**: Tải lên thư mục `/data/www/` trên máy chủ
3. **Giải nén mã nguồn**: `unzip CRMEB_v5.6.4.zip -d /data/www/crmeb`
4. **Cài đặt phụ thuộc**: `composer install --no-dev`
5. **Cấu hình môi trường**: Giống các bước triển khai bằng Git

### 4.2 Khởi động dịch vụ

#### 4.2.1 Dịch vụ Web

```bash
# Nginx Khởi động
systemctl start nginx
systemctl enable nginx

# Apache Khởi động
systemctl start httpd
systemctl enable httpd
```

#### 4.2.2 Dịch vụ PHP-FPM

```bash
systemctl start php-fpm
systemctl enable php-fpm
```

#### 4.2.3 Dịch vụ hàng đợi

```bash
# Khởi động hàng đợi (khuyến nghị dùng Supervisor để quản lý)
supervisorctl start crmeb-queue

# Supervisor Cấu hình
[program:crmeb-queue]
command=php /data/www/crmeb/think queue:listen --queue=default --timeout=60
process_name=%(program_name)s_%(process_num)02d
autostart=true
autorestart=true
user=www
numprocs=2
directory=/data/www/crmeb
stdout_logfile=/data/logs/supervisor/crmeb-queue-stdout.log
stderr_logfile=/data/logs/supervisor/crmeb-queue-stderr.log
```

#### 4.2.4 Dịch vụ kết nối liên tục

```bash
# Khởi động dịch vụ kết nối liên tục
php think workerman start --d

# Dừng dịch vụ kết nối liên tục
php think workerman stop
```

#### 4.2.5 Tác vụ định kỳ

```bash
# Thêm tác vụ định kỳ
crontab -e

# Cấu hình tác vụ định kỳ
* * * * * php /data/www/crmeb/think timer run
0 0 * * * php /data/www/crmeb/think crmeb:backup
```

### 4.3 Kiểm tra sau khi triển khai

#### 4.3.1 Kiểm tra tình trạng hoạt động (health check)

- **Truy cập trang chủ**: `http://example.com`
- **Truy cập trang quản trị**: `http://example.com/admin`
- **Kiểm thử API**: `http://example.com/api/ping`
- **Kết nối cơ sở dữ liệu**: Kiểm tra trạng thái kết nối cơ sở dữ liệu
- **Kết nối Redis**: Kiểm tra trạng thái kết nối bộ nhớ đệm

#### 4.3.2 Kiểm tra log

```bash
# Kiểm tra log Nginx
tail -f /data/logs/nginx/crmeb.error.log

# Kiểm tra log lỗi PHP
tail -f /var/log/php-fpm/error.log

# Kiểm tra log ứng dụng
tail -f /data/www/crmeb/runtime/log/*.log
```

## 5. Quản lý dịch vụ

### 5.1 Vận hành hằng ngày

#### 5.1.1 Kiểm tra giám sát

- **Trạng thái dịch vụ**: Kiểm tra tất cả dịch vụ có hoạt động bình thường không
- **Tải hệ thống**: Giám sát tình trạng sử dụng CPU, bộ nhớ, ổ đĩa
- **Trạng thái mạng**: Giám sát kết nối mạng và mức sử dụng băng thông
- **Trạng thái ứng dụng**: Giám sát thời gian phản hồi và tỷ lệ lỗi của ứng dụng

#### 5.1.2 Quản lý log

- **Thu thập log**: Thu thập tập trung log của tất cả dịch vụ
- **Phân tích log**: Phân tích lỗi và ngoại lệ trong log
- **Dọn dẹp log**: Định kỳ dọn dẹp log đã hết hạn
- **Sao lưu log**: Sao lưu log quan trọng lên kho lưu trữ từ xa

#### 5.1.3 Sao lưu và khôi phục

##### 5.1.3.1 Sao lưu dữ liệu

```bash
# Sao lưu cơ sở dữ liệu
mysqldump -u username -p database_name > /data/backup/data/$(date +%Y%m%d)_backup.sql

# Sao lưu code
tar -czf /data/backup/code/$(date +%Y%m%d)_crmeb.tar.gz /data/www/crmeb

# Sao lưu file cấu hình
tar -czf /data/backup/config/$(date +%Y%m%d)_config.tar.gz /data/www/crmeb/config
```

##### 5.1.3.2 Khôi phục dữ liệu

```bash
# Khôi phục cơ sở dữ liệu
mysql -u username -p database_name < /data/backup/data/20240101_backup.sql

# Khôi phục code
tar -xzf /data/backup/code/20240101_crmeb.tar.gz -C /data/www/

# Khôi phục file cấu hình
tar -xzf /data/backup/config/20240101_config.tar.gz -C /data/www/crmeb/
```

## 6. Sự cố thường gặp

### 6.1 Vấn đề khi triển khai

#### 6.1.1 Cài đặt thư viện phụ thuộc thất bại

- **Vấn đề**: Composer cài đặt thư viện phụ thuộc thất bại
- **Nguyên nhân**: Sự cố mạng, phiên bản PHP không tương thích
- **Giải pháp**: Sử dụng mirror trong nước, kiểm tra phiên bản PHP

#### 6.1.2 Kết nối cơ sở dữ liệu thất bại

- **Vấn đề**: Ứng dụng không thể kết nối cơ sở dữ liệu
- **Nguyên nhân**: Dịch vụ cơ sở dữ liệu chưa khởi động, sai tên đăng nhập hoặc mật khẩu, sự cố kết nối mạng
- **Giải pháp**: Kiểm tra dịch vụ MySQL, xác minh tên đăng nhập và mật khẩu, kiểm tra kết nối mạng

#### 6.1.3 Lỗi phân quyền

- **Vấn đề**: Lỗi quyền truy cập tệp hoặc thư mục
- **Nguyên nhân**: Thiết lập quyền không đúng, nhóm người dùng không khớp
- **Giải pháp**: Thiết lập đúng quyền cho tệp, đảm bảo người dùng PHP-FPM có quyền truy cập

#### 6.1.4 Cổng bị chiếm dụng

- **Vấn đề**: Khởi động dịch vụ thất bại, cổng đã bị chiếm dụng
- **Nguyên nhân**: Dịch vụ khác đang chiếm dụng cùng cổng
- **Giải pháp**: Tìm và dừng dịch vụ đang chiếm dụng cổng, hoặc đổi cổng của dịch vụ

### 6.2 Vấn đề khi vận hành

#### 6.2.1 Ứng dụng phản hồi chậm

- **Vấn đề**: Thời gian phản hồi của ứng dụng lâu
- **Nguyên nhân**: Truy vấn cơ sở dữ liệu chậm, mã PHP kém hiệu quả, tài nguyên máy chủ không đủ
- **Giải pháp**: Tối ưu truy vấn SQL, tối ưu mã PHP, tăng tài nguyên máy chủ

#### 6.2.2 Tràn bộ nhớ

- **Vấn đề**: PHP bị tràn bộ nhớ
- **Nguyên nhân**: Giới hạn bộ nhớ quá nhỏ, mã nguồn bị rò rỉ bộ nhớ
- **Giải pháp**: Tăng giới hạn bộ nhớ của PHP, tối ưu việc sử dụng bộ nhớ trong mã nguồn

#### 6.2.3 Hàng đợi bị tồn đọng

- **Vấn đề**: Tác vụ hàng đợi bị tồn đọng
- **Nguyên nhân**: Tốc độ xử lý hàng đợi chậm, khối lượng tác vụ quá lớn
- **Giải pháp**: Tăng số tiến trình hàng đợi, tối ưu logic xử lý tác vụ hàng đợi

## 7. Tài liệu tham khảo

- [Tài liệu chính thức CRMEB](https://doc.crmeb.com/single_open)
- [Tài liệu chính thức ThinkPHP 6](https://www.kancloud.cn/manual/thinkphp6_0)
- [Tài liệu chính thức Nginx](https://nginx.org/en/docs/)
- [Tài liệu chính thức PHP](https://www.php.net/docs.php)
- [Tài liệu chính thức MySQL](https://dev.mysql.com/doc/)
- [Tài liệu chính thức Redis](https://redis.io/documentation)