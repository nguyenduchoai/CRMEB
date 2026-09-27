## Cài đặt
> Môi trường vận hành yêu cầu PHP 7.1~7.4, phiên bản cơ sở dữ liệu là Mysql 5.7.
## Cài đặt một cú nhấp
Tạo trang web, chọn thư mục chạy là /public trong thư mục gốc của dự án, thiết lập quy tắc rewrite URL (giả tĩnh) theo mẫu thinkphp
Nhập tên miền hoặc IP của bạn vào trình duyệt (ví dụ: www.yourdomain.com),
Trình cài đặt sẽ tự động thực hiện cài đặt. Trong quá trình này, hệ thống sẽ nhắc bạn nhập thông tin cơ sở dữ liệu để hoàn tất cài đặt, sau khi cài đặt xong, bạn nên xóa tệp index.php trong thư mục install hoặc đổi tên tệp này.

Địa chỉ truy cập trang quản trị: tên miền/admin 

Địa chỉ truy cập trang chủ OA WeChat và H5: tên miền/

Lưu ý: Nếu không truy cập được, vui lòng kiểm tra xem [Rewrite URL](https://doc.crmeb.com/web/single/crmeb_v4/1139) đã được cấu hình đúng chưa
Vui lòng ghi nhớ tài khoản và mật khẩu của bạn trong quá trình cài đặt!

## Cài đặt lại
1. Xóa cơ sở dữ liệu
2. Xóa tệp /public/install.lock

## Cài đặt thủ công
1.Tạo cơ sở dữ liệu, nhập tệp cơ sở dữ liệu
Tệp cơ sở dữ liệu nằm tại /public/install/crmeb.sql
2.Sửa tệp kết nối cơ sở dữ liệu
Tệp cấu hình nằm tại /.env
~~~
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
~~~
3.Sửa quyền thư mục (hệ thống linux) thành 777

/public 

/runtime

4.Đăng nhập trang quản trị:
http://ten-mien-cua-ban/admin

Tài khoản mặc định: admin Mật khẩu: crmeb.com

## Hàng đợi tin nhắn
Trên hệ thống linux, cài đặt trình quản lý Supervisor, thêm tiến trình nền (daemon)

Chọn người dùng là www

Chọn thư mục chạy là thư mục gốc của dự án

Lệnh khởi động: php think queue:listen --queue

## Tác vụ định kỳ
Được sử dụng trong các chức năng như tự động xác nhận đã nhận hàng, cảnh báo tồn kho, v.v.
```sh
php think timer [ status ] [ --d ]
```
Tham số
- status: trạng thái
    - start: khởi động
    - stop: dừng
    - restart: khởi động lại
- --d : chạy nền
## Dịch vụ kết nối liên tục
Được sử dụng trong các chức năng như chat trên h5, thông báo tin nhắn cho quản trị viên ở trang quản trị, v.v. 

Trước tiên, sửa cấu hình nginx của trang web
~~~
location /notice {
    proxy_pass http://127.0.0.1:20002/;  
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header X-real-ip $remote_addr;
    proxy_set_header X-Forwarded-For $remote_addr;
}
location /msg {
    proxy_pass http://127.0.0.1:20003/;  
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header X-real-ip $remote_addr;
    proxy_set_header X-Forwarded-For $remote_addr;
}
~~~
Các cổng tương ứng phải thống nhất với cấu hình trong /config/workerman.php

Trên hệ thống linux, chạy trực tiếp
```sh
php think workerman [ status ] [ server ] [ --d ]
```
Trong môi trường windows cần thực hiện theo ba bước
```sh
# Dịch vụ giao tiếp nội bộ
php think workerman start channel
# Dịch vụ chat phía h5
php think workerman start chat
# Thông báo cho quản trị viên
php think workerman start admin
```
Hoặc nhấp đúp để chạy trực tiếp /workerman.bat

Tham số
- status: trạng thái
    - start: khởi động
    - stop: dừng
    - restart: khởi động lại
- server: dịch vụ (windows)
    - channel: giao tiếp nội bộ
    - chat: h5
    - admin: trang quản trị

- --d : chạy nền
