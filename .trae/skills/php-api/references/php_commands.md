# Tài liệu các lệnh PHP thường dùng

## 1. Tổng quan

Tài liệu này mô tả các lệnh PHP thường dùng trong dự án CRMEB, bao gồm lệnh PHP cơ bản, lệnh Composer, lệnh ThinkPHP, lệnh riêng của CRMEB, v.v., nhằm giúp lập trình viên nhanh chóng nắm bắt và sử dụng các lệnh này, nâng cao hiệu quả phát triển.

## 2. Lệnh PHP cơ bản

### 2.1 Xem phiên bản PHP

```bash
# Xem phiên bản PHP
php -v

# Xem thông tin chi tiết PHP
php -i

# Xem vị trí file cấu hình PHP
php --ini
```

### 2.2 Lệnh chạy PHP

```bash
# Chạy file PHP
php filename.php

# Chạy tương tác PHP
php -a

# Thực thi code PHP
php -r "echo 'Hello, CRMEB!';"

# Kiểm tra lỗi cú pháp
php -l filename.php
```

### 2.3 Quản lý extension PHP

```bash
# Xem các extension đã cài đặt
php -m

# Xem thông tin của một extension cụ thể
php -i | grep extension_name

# Xem thư mục extension
php -i | grep extension_dir
```

## 3. Lệnh Composer

### 3.1 Lệnh Composer cơ bản

```bash
# Xem phiên bản Composer
composer -V

# Khởi tạo dự án Composer
composer init

# Cài đặt các gói phụ thuộc
composer install

# Cập nhật các phụ thuộc
composer update

# Thêm phụ thuộc mới
composer require package_name

# Gỡ bỏ phụ thuộc
composer remove package_name

# Xem các phụ thuộc đã cài đặt
composer show

# Tối ưu autoload
composer dump-autoload

# Tối ưu autoload (môi trường production)
composer dump-autoload --optimize
```

### 3.2 Lệnh cấu hình Composer

```bash
# Xem cấu hình Composer
composer config --list

# Đặt mirror cho Composer
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# Hủy cài đặt mirror của Composer
composer config -g --unset repo.packagist
```

## 4. Lệnh ThinkPHP

### 4.1 Lệnh ThinkPHP cơ bản

```bash
# Xem danh sách lệnh ThinkPHP
php think

# Xem trợ giúp của lệnh
php think help command_name

# Xóa bộ nhớ đệm
php think clear

# Xem danh sách route
php think route:list

# Tạo khóa ứng dụng
php think generate:key
```

### 4.2 Lệnh liên quan đến cơ sở dữ liệu

```bash
# Chạy migration cơ sở dữ liệu
php think migrate:run

# Rollback migration cơ sở dữ liệu
php think migrate:rollback

# Tạo file migration cơ sở dữ liệu
php think migrate:create migration_name

# Chạy seed cơ sở dữ liệu
php think seed:run

# Tạo file seed cơ sở dữ liệu
php think seed:create seed_name
```

### 4.3 Lệnh sinh mã

```bash
# Tạo model
php think make:model ModelName

# Tạo controller
php think make:controller ControllerName

# Tạo middleware
php think make:middleware MiddlewareName

# Tạo validator
php think make:validate ValidateName

# Tạo event
php think make:event EventName

# Tạo listener
php think make:listener ListenerName
```

## 5. Lệnh riêng của CRMEB

### 5.1 Lệnh sinh mã của CRMEB

```bash
# Tạo code CRUD
php think crmeb:build

# Tạo API
php think crmeb:api

# Tạo trang quản trị
php think crmeb:admin
```

### 5.2 Lệnh hệ thống của CRMEB

```bash
# Xem phiên bản hệ thống
php think crmeb:version

# Khởi tạo hệ thống
php think crmeb:init

# Dọn cache hệ thống
php think crmeb:clear

# Tạo cấu hình hệ thống
php think crmeb:config
```

### 5.3 Lệnh liên quan đến hàng đợi

```bash
# Khởi động listener của hàng đợi
php think queue:listen

# Khởi động tiến trình worker của hàng đợi
php think queue:work

# Xem trạng thái hàng đợi
php think queue:status

# Khởi động lại hàng đợi
php think queue:restart
```

### 5.4 Lệnh tác vụ định kỳ

```bash
# Khởi động tác vụ định kỳ
php think timer start --d

# Dừng tác vụ định kỳ
php think timer stop

# Xem trạng thái tác vụ định kỳ
php think timer status
```

### 5.5 Lệnh kết nối liên tục

```bash
# Khởi động dịch vụ kết nối liên tục
php think workerman start --d

# Dừng dịch vụ kết nối liên tục
php think workerman stop

# Khởi động lại dịch vụ kết nối liên tục
php think workerman restart

# Xem trạng thái dịch vụ kết nối liên tục
php think workerman status
```

## 6. Lệnh công cụ phát triển

### 6.1 Lệnh kiểm tra mã

```bash
# Dùng PHP_CodeSniffer để kiểm tra chuẩn code
./vendor/bin/phpcs

# Dùng PHPStan để phân tích tĩnh
./vendor/bin/phpstan analyze

# Dùng Psalm để phân tích tĩnh
./vendor/bin/psalm
```

### 6.2 Lệnh kiểm thử

```bash
# Chạy kiểm thử PHPUnit
./vendor/bin/phpunit

# Chạy một kiểm thử cụ thể
./vendor/bin/phpunit tests/TestCase.php

# Tạo báo cáo độ bao phủ kiểm thử
./vendor/bin/phpunit --coverage-html coverage
```

### 6.3 Lệnh định dạng mã

```bash
# Dùng PHP-CS-Fixer để định dạng code
./vendor/bin/php-cs-fixer fix

# Dùng Pretty PHP để định dạng code
./vendor/bin/pretty-php
```

## 7. Lệnh triển khai

### 7.1 Lệnh build dự án

```bash
# Cài đặt phụ thuộc (môi trường production)
composer install --no-dev --optimize-autoloader

# Biên dịch tài nguyên frontend
npm install
npm run build

# Dọn cache
php think clear
```

### 7.2 Lệnh triển khai trên máy chủ

```bash
# Tải code lên máy chủ
scp -r local_directory user@server:/remote_directory

# Thực thi lệnh từ xa
ssh user@server "cd /project/directory && php think clear"

# Dùng rsync để đồng bộ code
rsync -avz --exclude='.git' --exclude='vendor' local_directory/ user@server:/remote_directory/
```

### 7.3 Lệnh triển khai bằng Docker

```bash
# Build image Docker
docker build -t crmeb .

# Chạy container Docker
docker run -d --name crmeb -p 80:80 crmeb

# Xem trạng thái container Docker
docker ps

# Vào container Docker
docker exec -it crmeb bash
```

## 8. Lệnh cơ sở dữ liệu

### 8.1 Lệnh MySQL

```bash
# Kết nối cơ sở dữ liệu MySQL
mysql -u username -p database_name

# Nhập file SQL
mysql -u username -p database_name < crmeb.sql

# Xuất file SQL
mysqldump -u username -p database_name > backup.sql

# Xuất một bảng cụ thể
mysqldump -u username -p database_name table1 table2 > backup.sql
```

### 8.2 Lệnh migration cơ sở dữ liệu

```bash
# Tạo file migration
php think migrate:create CreateUsersTable

# Chạy migration
php think migrate:run

# Rollback migration
php think migrate:rollback

# Xem trạng thái migration
php think migrate:status
```

### 8.3 Lệnh seed cơ sở dữ liệu

```bash
# Tạo file seed
php think seed:create UserSeeder

# Chạy seed
php think seed:run

# Chạy một seed cụ thể
php think seed:run --seed=UserSeeder
```

## 9. Lệnh tối ưu hiệu năng

### 9.1 Lệnh tối ưu mã

```bash
# Tối ưu autoload của Composer
composer dump-autoload --optimize --classmap-authoritative

# Tạo script làm nóng (warm-up) OPcache
php -r '$files = glob(__DIR__ . "/vendor/**/*.php", GLOB_BRACE); foreach ($files as $file) { require_once $file; }'
```

### 9.2 Lệnh tối ưu bộ nhớ đệm

```bash
# Xóa toàn bộ cache
php think clear

# Xóa cache template
php think clear --template

# Xóa cache cấu hình
php think clear --config

# Xóa cache route
php think clear --route
```

### 9.3 Lệnh tối ưu cơ sở dữ liệu

```bash
# Tối ưu bảng MySQL
mysql -u username -p -e "OPTIMIZE TABLE table1, table2;" database_name

# Sửa lỗi bảng MySQL
mysql -u username -p -e "REPAIR TABLE table1, table2;" database_name

# Phân tích bảng MySQL
mysql -u username -p -e "ANALYZE TABLE table1, table2;" database_name
```

## 10. Lệnh xử lý sự cố

### 10.1 Lệnh xem log

```bash
# Xem log lỗi Nginx
tail -f /var/log/nginx/error.log

# Xem log lỗi PHP-FPM
tail -f /var/log/php-fpm/error.log

# Xem log ứng dụng CRMEB
tail -f runtime/log/$(date +%Y%m%d).log

# Xem log truy vấn chậm
tail -f /var/log/mysql/mysql-slow.log
```

### 10.2 Lệnh xem tiến trình

```bash
# Xem tiến trình PHP-FPM
ps aux | grep php-fpm

# Xem tiến trình Nginx
ps aux | grep nginx

# Xem tiến trình MySQL
ps aux | grep mysql

# Xem tiến trình hàng đợi CRMEB
ps aux | grep queue:work
```

### 10.3 Lệnh xem thông tin mạng

```bash
# Xem các cổng đang bị chiếm dụng
netstat -tuln

# Xem một cổng cụ thể có bị chiếm dụng không
lsof -i :80

# Xem kết nối mạng
netstat -an | grep ESTABLISHED

# Kiểm tra kết nối mạng
ping example.com

# Kiểm tra kết nối cổng
telnet example.com 80
```

## 11. Thực tiễn tốt nhất

### 11.1 Khuyến nghị khi sử dụng lệnh

- **Dùng đường dẫn tuyệt đối**: Khi chạy lệnh nên dùng đường dẫn tuyệt đối để tránh lỗi đường dẫn
- **Cấp quyền thực thi**: Với tệp script, nhớ cấp quyền thực thi
- **Dùng alias**: Với các lệnh thường dùng, có thể thêm alias trong `.bashrc` hoặc `.zshrc`
- **Xem trợ giúp**: Khi gặp lệnh chưa quen, dùng `--help` để xem thông tin trợ giúp
- **Ghi lại các lệnh thường dùng**: Ghi các lệnh thường dùng vào tài liệu để tiện tra cứu

### 11.2 Khuyến nghị bảo mật

- **Tránh dùng người dùng root**: Khi chạy lệnh PHP, tránh dùng người dùng root
- **Bảo vệ thông tin nhạy cảm**: Tránh nhập trực tiếp mật khẩu và các thông tin nhạy cảm khác trên dòng lệnh
- **Giới hạn quyền thực thi lệnh**: Với môi trường production, hãy giới hạn quyền thực thi lệnh
- **Cập nhật thư viện phụ thuộc định kỳ**: Định kỳ dùng `composer update` để cập nhật thư viện phụ thuộc, vá lỗ hổng bảo mật

### 11.3 Khuyến nghị về hiệu năng

- **Dùng bộ nhớ đệm**: Với các lệnh chạy thường xuyên, cân nhắc sử dụng bộ nhớ đệm (cache)
- **Thực thi song song**: Với các tác vụ độc lập, có thể cân nhắc thực thi song song
- **Giới hạn đầu ra**: Với các lệnh tạo nhiều đầu ra, dùng pipe hoặc chuyển hướng (redirect) để giới hạn đầu ra
- **Chạy nền**: Với các lệnh tốn nhiều thời gian, hãy chạy ở chế độ nền

## 12. Sự cố thường gặp

### 12.1 Chạy lệnh PHP thất bại

- **Vấn đề**: Khi chạy lệnh PHP nhận được thông báo "Command not found"
- **Giải pháp**: Kiểm tra PHP đã được cài đặt chưa và đã có trong biến môi trường PATH chưa

### 12.2 Chạy lệnh Composer thất bại

- **Vấn đề**: Khi chạy lệnh Composer nhận được thông báo "Composer could not find a composer.json file"
- **Giải pháp**: Đảm bảo chạy lệnh tại thư mục gốc của dự án và tệp composer.json có tồn tại

### 12.3 Chạy lệnh ThinkPHP thất bại

- **Vấn đề**: Khi chạy lệnh ThinkPHP nhận được thông báo "Class not found"
- **Giải pháp**: Chạy `composer dump-autoload` để cập nhật autoload

### 12.4 Chạy lệnh cơ sở dữ liệu thất bại

- **Vấn đề**: Khi chạy lệnh cơ sở dữ liệu nhận được thông báo "Access denied for user"
- **Giải pháp**: Kiểm tra tên đăng nhập và mật khẩu cơ sở dữ liệu có đúng không, và có đủ quyền tương ứng không

### 12.5 Chạy lệnh hàng đợi thất bại

- **Vấn đề**: Khi chạy lệnh hàng đợi nhận được thông báo "Queue not found"
- **Giải pháp**: Kiểm tra cấu hình hàng đợi có đúng không, và dịch vụ hàng đợi đã được khởi động chưa

## 13. Tài liệu tham khảo

- [Tài liệu chính thức PHP](https://www.php.net/docs.php)
- [Tài liệu chính thức Composer](https://getcomposer.org/doc/)
- [Tài liệu chính thức ThinkPHP](https://www.kancloud.cn/manual/thinkphp6_0)
- [Tài liệu chính thức MySQL](https://dev.mysql.com/doc/)
- [Tài liệu chính thức Docker](https://docs.docker.com/)
- [Tổng hợp lệnh Linux](https://www.runoob.com/linux/linux-command-manual.html)

## 14. Tổng kết

Tài liệu này giới thiệu các lệnh PHP thường dùng trong dự án CRMEB, bao gồm lệnh PHP cơ bản, lệnh Composer, lệnh ThinkPHP, lệnh riêng của CRMEB, lệnh cơ sở dữ liệu, lệnh tối ưu hiệu năng, lệnh xử lý sự cố, v.v.

Khi nắm vững các lệnh này, lập trình viên có thể phát triển, triển khai và bảo trì dự án hiệu quả hơn. Đồng thời, tài liệu này cũng cung cấp một số thực tiễn tốt nhất và giải pháp cho các vấn đề thường gặp, hy vọng giúp lập trình viên tránh được một số lỗi phổ biến.

Cùng với sự phát triển của dự án và sự thay đổi của công nghệ, các lệnh này cũng có thể thay đổi, lập trình viên nên định kỳ tra cứu tài liệu liên quan để nắm được các lệnh và cách dùng mới nhất.