Thư mục chương trình backend CRMEB-KY v6.0.0
===============

## Chạy bằng một cú nhấp với docker

### Khởi động nhanh

```bash
# Tải image về
docker pull ccr.ccs.tencentyun.com/crmebky_php/crmebky:latest

# Chạy container
docker run -d --name crmeb \
  -p 8080:80 \
  -p 3306:3306 \
  -p 6379:6379 \
  ccr.ccs.tencentyun.com/crmebky_php/crmebky:latest
```

### Truy cập dịch vụ
- **Website**: http://localhost:8080 
- **Trang quản trị**: http://localhost:8080/admin (tài khoản: admin, mật khẩu: crmeb.com)
- **MySQL**: localhost:3306 (tài khoản: root, mật khẩu: 123456)
- **Redis**: localhost:6379
> Xem hướng dẫn chi tiết tại [tài liệu trợ giúp](https://gitee.com/ZhongBangKeJi/CRMEB/blob/master/help/docker/README.md).


> Môi trường chạy yêu cầu PHP7.1-7.4. 

## Cài đặt

## Cài đặt một cú nhấp
Tải mã nguồn của bạn lên, thiết lập thư mục gốc (document root) của website là /public
Nhập tên miền hoặc IP của bạn vào trình duyệt (ví dụ: www.yourdomain.com),
Trình cài đặt sẽ tự động tiến hành cài đặt. Trong quá trình này, hệ thống sẽ nhắc bạn nhập thông tin cơ sở dữ liệu để hoàn tất cài đặt, sau khi cài đặt xong nên xóa thư mục install.

Địa chỉ truy cập trang quản trị:
1.tên-miền/admin
Địa chỉ truy cập trang chủ OA WeChat và H5:
1.tên-miền/

Vui lòng ghi nhớ tài khoản và mật khẩu của bạn trong quá trình cài đặt!

## Cài đặt lại
1. Xóa cơ sở dữ liệu
2. Xóa tệp /public/install.lock

## Cài đặt thủ công
1.Tạo cơ sở dữ liệu, nhập (import) tệp cơ sở dữ liệu
Tệp cơ sở dữ liệu nằm tại /public/install/crmeb.sql
2.Sửa tệp kết nối cơ sở dữ liệu
Tệp cấu hình nằm tại /.env
~~~
APP_DEBUG = true

[APP]
DEFAULT_TIMEZONE = Asia/Shanghai

[DATABASE]
TYPE = mysql
HOSTNAME = 127.0.0.1 #Địa chỉ kết nối cơ sở dữ liệu
HOSTPORT = 3306 #Cổng cơ sở dữ liệu
DATABASE = test #Tên cơ sở dữ liệu
USERNAME = username #Tài khoản đăng nhập cơ sở dữ liệu
PASSWORD = password #Mật khẩu đăng nhập cơ sở dữ liệu
PREFIX = eb_
CHARSET = utf8mb4
DEBUG = true

[LANG]
default_lang = zh-cn

[CACHE]
DRIVER = file #Loại bộ nhớ đệm, redis/file
CACHE_PREFIX = cache_xxxx: #Tiền tố cache
CACHE_TAG_PREFIX = cache_tag_xxxx: #Tiền tố loại bộ nhớ đệm

[REDIS]
REDIS_HOSTNAME = 127.0.0.1 #Địa chỉ kết nối redis
PORT = 6379 #Số cổng
REDIS_PASSWORD = 123456 #Mật khẩu
SELECT = 0 #Cơ sở dữ liệu

[QUEUE]
QUEUE_NAME = xxxx #Tiền tố hàng đợi
~~~
3.Sửa quyền thư mục (hệ thống linux) thành 777
/crmeb
/template

4.Đăng nhập trang quản trị:
http://ten-mien-cua-ban/admin
Tài khoản mặc định: admin Mật khẩu: crmeb.com


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
```sh
php think workerman [ status ]  [ --d ]
```
Trong môi trường windows cần thực hiện theo ba bước
```sh
# Dịch vụ giao tiếp nội bộ
php think workerman start --d
```
Tham số
- status: trạng thái
  - start: khởi động
  - stop: dừng
  - restart: khởi động lại
- --d : chạy nền

## Quy chuẩn phát triển
#### Quy tắc đặt tên
ThinkPHP6.0 tuân theo chuẩn đặt tên PSR-2 và chuẩn tự động nạp (autoload) PSR-4, đồng thời cần lưu ý các quy tắc sau:

1. Thư mục và tệp
2. Tên thư mục dùng chữ thường + dấu gạch dưới;
3. Các tệp thư viện lớp và tệp hàm thống nhất dùng hậu tố .php;
4. Tên tệp của lớp đều được định nghĩa theo namespace, và đường dẫn namespace phải trùng với đường dẫn chứa tệp thư viện lớp;
5. Tệp lớp (bao gồm interface và Trait) được đặt tên theo kiểu camelCase (viết hoa chữ cái đầu), các tệp khác được đặt tên bằng chữ thường + dấu gạch dưới;
6. Tên lớp (bao gồm interface và Trait) phải trùng với tên tệp, thống nhất đặt tên theo kiểu camelCase (viết hoa chữ cái đầu);

#### Đặt tên hàm, lớp và thuộc tính

1. Tên lớp đặt theo kiểu camelCase (viết hoa chữ cái đầu), ví dụ User, UserType;
2. Tên hàm common dùng chữ thường và dấu gạch dưới (bắt đầu bằng chữ thường), ví dụ get_client_ip;
3. Các phương thức trong controller dùng chữ thường và dấu gạch dưới (bắt đầu bằng chữ thường), ví dụ get_client_ip
4. Tên phương thức đặt theo kiểu camelCase (chữ cái đầu viết thường), ví dụ getUserName;
5. Tên thuộc tính đặt theo kiểu camelCase (chữ cái đầu viết thường), ví dụ tableName, instance;
6. Ngoại lệ: các hàm hoặc phương thức bắt đầu bằng hai dấu gạch dưới __ được dùng làm phương thức magic, ví dụ __call và __autoload;

#### Hằng số và cấu hình
1. Hằng số được đặt tên bằng chữ in hoa và dấu gạch dưới, ví dụ APP_PATH;
2. Tham số cấu hình được đặt tên bằng chữ thường và dấu gạch dưới, ví dụ url_route_on và url_convert;
3. Biến môi trường được đặt tên bằng chữ in hoa và dấu gạch dưới, ví dụ APP_DEBUG;

#### Bảng dữ liệu và trường
1. Bảng dữ liệu và trường được đặt tên bằng chữ thường kèm dấu gạch dưới, lưu ý tên trường không được bắt đầu bằng dấu gạch dưới, ví dụ bảng think_user và trường user_name, không khuyến khích dùng kiểu camelCase và tiếng Trung để đặt tên bảng dữ liệu và trường

Lưu ý: vui lòng hiểu rõ và cố gắng tuân thủ các quy tắc đặt tên trên, điều này giúp giảm thiểu các lỗi không cần thiết trong quá trình phát triển

#### Quy chuẩn cú pháp
1. Ưu tiên sử dụng cú pháp mới của php7
2. Sau mỗi câu lệnh khai báo namespace và khối câu lệnh khai báo use, PHẢI chèn một dòng trống
3. Dấu ngoặc nhọn mở ({) của lớp PHẢI được đặt trên một dòng riêng sau phần khai báo lớp, dấu ngoặc nhọn đóng (}) cũng PHẢI được đặt trên một dòng riêng sau phần thân lớp
4. Dấu ngoặc nhọn mở ({) của phương thức PHẢI được đặt trên một dòng riêng sau phần khai báo hàm, dấu ngoặc nhọn đóng (}) cũng PHẢI được đặt trên một dòng riêng sau phần thân hàm.
5. Thuộc tính và phương thức của lớp PHẢI có từ khóa phạm vi truy cập (private, protected và public), abstract và final PHẢI được khai báo trước từ khóa phạm vi truy cập, còn static PHẢI được khai báo sau từ khóa phạm vi truy cập
6. Sau từ khóa của cấu trúc điều khiển PHẢI có một dấu cách, còn khi gọi phương thức hoặc hàm thì KHÔNG ĐƯỢC có
7. Dấu ngoặc nhọn mở ({) của cấu trúc điều khiển PHẢI được đặt trên cùng một dòng với phần khai báo, còn dấu ngoặc nhọn đóng (}) PHẢI được đặt trên một dòng riêng sau phần thân
8. Tệp mã PHP thuần PHẢI lược bỏ thẻ đóng ?> ở cuối
9. Tất cả phương thức, lớp và lớp controller đều PHẢI có từ khóa phạm vi truy cập
    ~~~

    /**
     * Chú thích tiếng Việt
     * @param string $str Khai báo kiểu
     * @param array $arr
     * @return bool
     */
    public function action(string $str, array $arr)
    {
         return true;
    }
    ~~~
10. Trong danh sách tham số, sau mỗi dấu phẩy PHẢI có một dấu cách, còn trước dấu phẩy KHÔNG ĐƯỢC có dấu cách
    ~~~
     function foo($arg1, &$arg2, $arg3 = [])
     {
            // method body
     }
    ~~~
11. Tham số CÓ THỂ được tách thành nhiều dòng, khi đó mỗi tham số, kể cả tham số đầu tiên, NÊN nằm trên một dòng riêng.
    ~~~
    <?php
    $foo->bar(
        $longArgument,
        $longerArgument,
        $muchLongerArgument
    );
    ~~~
12. Cấu trúc if chuẩn như đoạn mã dưới đây, hãy chú ý vị trí của “dấu ngoặc đơn”, “dấu cách” và “dấu ngoặc nhọn”,
    lưu ý else và elseif đều nằm trên cùng một dòng với dấu ngoặc nhọn đóng phía trước
    ~~~
    <?php
    if ($expr1) {
        // if body
    } elseif ($expr2) {
        // elseif body
    } else {
        // else body;
    }
    ~~~
13. Trước và sau dấu bằng của phép gán phải có dấu cách
    ~~~
    <?php
    $arr = [];
    ~~~


#### Các cú pháp mới thường dùng của PHP 7.1+

1. Toán tử ba ngôi
   ~~~
   <?php

   $arr = ['crmeb'=>true];
   Trước đây
   echo isset($arr['crmeb']) ? $arr['crmeb'] : '';
   Hiện nay
   echo $arr['crmeb'] ?? '';
   ~~~
2.  define() định nghĩa mảng hằng số
   ~~~
   <?php
    define('ARR',['a','b']);
   ~~~
3.  Tối ưu namespace
   ~~~
    <?php
    //Cú pháp trước PHP7
    use FooLibrary\Bar\Baz\ClassA;
    use FooLibrary\Bar\Baz\ClassB;
    // Cách viết theo cú pháp mới của PHP7
    use FooLibrary\Bar\Baz\{ ClassA, ClassB};

   ~~~
#### Quy chuẩn CRMEB PRO
 1. Toàn bộ phần kiểm tra dữ liệu (validate) đặt trong thư mục validates của mô-đun
 2. Trả về JSON bằng các phương thức success và fail trong lớp cha AuthController
 3. Khi phát hiện lỗi thì ném ngoại lệ (exception), do một lớp lỗi kiểm soát đầu ra một cách thống nhất
    ~~~
    <?php

        throw new AuthException('Thông tin lỗi',400);
    ~~~
 4. Mã lỗi và thông báo lỗi nên được quản lý tập trung để thuận tiện chuyển đổi đa ngôn ngữ
 5. Thao tác cơ sở dữ liệu dùng lớp model, không được dùng Db::table()
 6. Lấy dữ liệu biểu mẫu bằng app\Request
    ~~~
    <?php
    use app\Request;


    public function index(Request $request) {

        //Lấy dữ liệu đã gửi và trả về dưới dạng mảng hai chiều
        $arr = $request->getMore([
            'name',
            'nickname'
        ]);
        //Lấy dữ liệu đã gửi, trả về dưới dạng mảng hai chiều kèm giá trị mặc định
        $arr = $request->getMore([
           ['name','123'],
           ['nickname','0']
        ]);
        //Lấy dữ liệu đã gửi, trả về dưới dạng mảng một chiều kèm giá trị mặc định
        [$name, $nickname] = $request->getMore([
           ['name','123'],
           ['nickname','0']
        ],true);

    }
    ~~~
 7. Tất cả các lớp controller được đặt tên tương ứng với tên bảng, theo quy tắc đặt tên PascalCase
 8. Tất cả thư mục được đặt tên bằng chữ thường kèm dấu gạch dưới
 9. Tất cả tên thuộc tính, tên biến nên tuân theo quy tắc đặt tên camelCase
 10. Với logic phức tạp, nhiều trạng thái, nên thêm chú thích trong dòng (inline comment) một cách phù hợp
 11. Trong model chỉ được viết các câu lệnh điều kiện tìm kiếm, việc kết hợp dữ liệu truy vấn được phải viết ở tầng services để xử lý, lệnh tạo services: php make:services api@user/User


## Tài liệu

[Hướng dẫn sử dụng](https://doc.crmeb.com)
[Tài liệu phát triển TP6](https://www.kancloud.cn/manual/thinkphp6_0/content)


## Tham gia phát triển

Vui lòng tham khảo [CRMEB](https://github.com/crmeb/CRMEB).

## Thông tin bản quyền


Thông tin bản quyền của mã nguồn và tệp nhị phân của bên thứ ba có trong dự án này được ghi chú riêng.

Bản quyền Copyright © 2017-2026 by CRMEB (http://www.crmeb.com)

All rights reserved。

Chủ sở hữu nhãn hiệu và quyền tác giả CRMEB® là Xi'an Zhongbang Network Technology Co., Ltd.
