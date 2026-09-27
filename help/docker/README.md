# Cài đặt docker
## Tải docker từ trang chính thức
https://www.docker.com/products/docker-desktop
## Cài đặt bằng lệnh
```
curl -sSL https://get.daocloud.io/docker | sh
```

# Cách 1: Khởi động hệ thống CRMEB chỉ với một lệnh

```
docker run -d \
  --name crmeb_app \
  -p 8111:80 \
  -v $(pwd)/crmeb/runtime:/var/www/crmeb/runtime \
  -v $(pwd)/crmeb/uploads:/var/www/crmeb/public/uploads \
  -v $(pwd)/crmeb_mysql:/var/lib/mysql \
  -v $(pwd)/crmeb_redis:/var/lib/redis \
  -e TZ=Asia/Shanghai \
  ccr.ccs.tencentyun.com/zbkj/crmebky:latest
 ``` 
 
# Cách 2: Chạy nhanh dự án bằng docker-compose


## 1、Cài đặt docker-compose
https://www.runoob.com/docker/docker-compose.html

## 2、Tải chương trình CRMEB
Nên tải mã nguồn mở mới nhất tại https://gitee.com/ZhongBangKeJi/CRMEB
Đặt chương trình vào thư mục ngang cấp với thư mục docker

## 3、Khởi chạy dự án
```
Vào thư mục docker-compose cd /docker

Chạy lệnh:
```
docker-compose up -d

```
## 4、Truy cập hệ thống CRMEB
Địa chỉ truy cập trên di động: http://localhost:8011/
Địa chỉ truy cập trên PC: http://localhost:8011/admin


## 5、Cài đặt CRMEB
### Thông tin cơ sở dữ liệu Mysql:
```
Host:crmeb_mysql
Post:3306
user:crmeb
pwd:123456
```
### Thông tin Redis:
```
Host:crmeb_redis
Post:6379
db:0
pwd:123456
```

## 6、Các lỗi thường gặp và cách khắc phục

### 6.1 MySQL khởi động thất bại
**Hiện tượng lỗi**: Container MySQL khởi động thất bại, log hiển thị "--initialize specified but the data directory has files in it. Aborting."

**Cách khắc phục**：
1. Dừng tất cả container:`docker-compose down`
2. Làm trống thư mục dữ liệu:`rm -rf mysql/data/*`
3. Khởi động lại dịch vụ:`docker-compose up -d`

**Nguyên nhân**: Thư mục dữ liệu MySQL không trống, dẫn đến khởi tạo thất bại.

### 6.2 Sự cố ánh xạ thư mục dữ liệu
**Hiện tượng lỗi**: Cơ sở dữ liệu không thể khởi động hoặc dữ liệu không thể lưu trữ lâu dài

**Cách khắc phục**：
1. Đảm bảo thư mục `mysql/data` tồn tại:`mkdir -p mysql/data`
2. Đảm bảo thư mục `mysql/data` trống
3. Đảm bảo đã cấu hình đúng ánh xạ volume dữ liệu trong docker-compose.yml:
   ```yaml
   volumes:
     - ./mysql/data:/var/lib/mysql
   ```

**Nguyên nhân**: Thư mục dữ liệu chưa được ánh xạ hoặc ánh xạ không đúng, dẫn đến không thể tạo cơ sở dữ liệu hoặc bị mất dữ liệu.

## 6.3 Mô tả các thư mục thường cần ánh xạ

### 6.3.1 MySQL Thư mục dữ liệu
- **Đường dẫn cục bộ**：`mysql/data`
- **Đường dẫn trong container**：`/var/lib/mysql`
- **Công dụng**: Lưu trữ các tệp dữ liệu của cơ sở dữ liệu MySQL
- **Lưu ý**: Phải là thư mục trống, nếu không MySQL sẽ khởi tạo thất bại

### 6.2 MySQL Thư mục log
- **Đường dẫn cục bộ**：`mysql/log`
- **Đường dẫn trong container**：`/var/log/mysql`
- **Công dụng**: Lưu trữ các tệp log của MySQL
- **Lưu ý**: Đảm bảo thư mục tồn tại và có quyền đọc/ghi

### 6.3.2 PHP Thư mục ứng dụng
- **Đường dẫn cục bộ**：`../../crmeb`
- **Đường dẫn trong container**：`/var/www`
- **Công dụng**: Lưu trữ mã nguồn ứng dụng CRMEB
- **Lưu ý**: Đảm bảo thư mục tồn tại và chứa đầy đủ mã nguồn CRMEB

### 6.3.3 PHP Thư mục runtime
- **Đường dẫn cục bộ**：`../../crmeb/runtime`
- **Đường dẫn trong container**：`/var/www/runtime`
- **Công dụng**: Lưu trữ các tệp runtime của ứng dụng PHP như bộ nhớ đệm, log, v.v.
- **Lưu ý**: Đảm bảo thư mục tồn tại và có quyền đọc/ghi

### 6.3.4 Nginx Thư mục cấu hình
- **Đường dẫn cục bộ**：`./nginx/vhost.conf`
- **Đường dẫn trong container**：`/etc/nginx/conf.d/default.conf`
- **Công dụng**: Tệp cấu hình virtual host của Nginx
- **Lưu ý**: Đảm bảo tệp cấu hình tồn tại và đúng định dạng

### 6.3.5 Nginx Thư mục log
- **Đường dẫn cục bộ**：`./nginx/log`
- **Đường dẫn trong container**：`/etc/nginx/log`
- **Công dụng**: Lưu trữ các tệp log của Nginx
- **Lưu ý**: Đảm bảo thư mục tồn tại và có quyền đọc/ghi

### 6.3.6 Lệnh tạo thư mục
```bash
# Tạo tất cả các thư mục cần thiết
mkdir -p mysql/data mysql/log nginx/log

# Đảm bảo thư mục ứng dụng CRMEB đã tồn tại
mkdir -p ../crmeb ../crmeb/runtime
```


