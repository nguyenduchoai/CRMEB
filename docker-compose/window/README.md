# Chạy nhanh dự án bằng docker-compose
## 1、Cài đặt docker
Tải docker từ trang chính thức
https://www.docker.com/products/docker-desktop

Hoặc cài đặt bằng lệnh 
```
curl -sSL https://get.daocloud.io/docker | sh
```
## 2、Cài đặt docker-compose
https://www.runoob.com/docker/docker-compose.html
## 3、Tải chương trình CRMEB
Nên tải mã nguồn mở mới nhất tại https://gitee.com/ZhongBangKeJi/CRMEB
Đặt chương trình vào thư mục cùng cấp với docker-compose
## 4、Khởi chạy dự án
```
Vào thư mục docker-compose bằng lệnh cd /docker-compose

Lệnh chạy: docker-compose up -d

```
Các lệnh vào container PHP để khởi động hàng đợi, tác vụ định kỳ, kết nối liên tục
```
Vào container: docker exec -it crmeb_php /bin/bash
Vào thư mục dự án: cd /var/www
Lệnh tác vụ định kỳ: php think timer start --d
Lệnh kết nối liên tục: php think workerman start --d
Lệnh hàng đợi: php think queue:listen --queue
```
## 5、Truy cập hệ thống CRMEB
http://localhost:8011/
## 6、Cài đặt CRMEB
### Thông tin cơ sở dữ liệu Mysql:
```
Host:192.168.10.11
Post:3306 
user:root 
pwd:123456 
```
### Thông tin Redis:
```
Host:192.168.10.10
Post:6379
db:0
pwd:123456
```
## 7、Sự cố thường gặp
1. Cổng bị chiếm dụng: vào docker-compose.yml để sửa cổng

2. Nếu chạy docker-compose up -d mà khởi động thất bại, vui lòng kiểm tra docker-compose.yml và sửa địa chỉ image hoặc các cấu hình khác bên trong

3. Gặp lỗi Error response from daemon: Address already in use
  Thông thường là do IP đã thiết lập bị chiếm dụng, hãy sửa địa chỉ ipv4_address của một container nào đó

4. Container MYSQL không khởi động được, không có bất kỳ log nào
  Lưu ý: với chip m1 cần dùng image mysql daocloud.io/library/mysql:5.7.5-m15; trong mọi trường hợp khác đều
   dùng image mysql:5.7
