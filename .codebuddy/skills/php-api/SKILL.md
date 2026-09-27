---
name: Hướng dẫn phát triển backend PHP
description: Mô tả skill phát triển backend PHP
---

# Hướng dẫn phát triển backend PHP

## 0. Mô tả cơ chế tự động kích hoạt

### 0.1 Điều kiện kích hoạt

#### 0.1.1 Kích hoạt theo thao tác
- **Khi duyệt file**: Tự động được gọi khi duyệt các thư mục liên quan đến backend PHP
  - Kích hoạt khi mở thư mục `app/`
  - Kích hoạt khi mở thư mục thư viện lõi `crmeb/`
  - Kích hoạt khi duyệt thư mục controller, model, service
  - Kích hoạt khi xem thư mục chứa file cấu hình
- **Khi thao tác file**: Tự động được gọi khi thao tác trên các file backend PHP
  - Kích hoạt khi tạo file PHP mới
  - Kích hoạt khi sửa code backend
  - Kích hoạt khi xóa file PHP
- **Khi thao tác thư mục**: Tự động được gọi khi thao tác trên các thư mục backend PHP
  - Kích hoạt khi tạo thư mục backend mới
  - Kích hoạt khi đổi tên thư mục backend
  - Kích hoạt khi xóa thư mục backend

#### 0.1.2 Kích hoạt theo nội dung
- **Kích hoạt theo từ khóa**: Tự động được gọi khi nội dung tệp chứa các từ khóa sau
  - Từ khóa backend: `backend`, `PHP`, `máy chủ`, `API`, `API`
  - Từ khóa framework: `ThinkPHP`, `TP6`, `framework`, `route`, `controller`
  - Từ khóa chức năng: `đăng nhập`, `đăng ký`, `thanh toán`, `đơn hàng`, `người dùng`
- **Kích hoạt theo code**: Tự động được gọi khi xem code PHP thuộc các loại cụ thể
  - Code controller (`Controller`)
  - Code model (`Model`)
  - Code service (`Service`)
  - Code cấu hình (`Config`)
  - Code route (`Route`)

#### 0.1.3 Kích hoạt theo lệnh
- **Kích hoạt bằng lệnh terminal**: Tự động được gọi khi thực thi các lệnh sau
  - `php think` (lệnh ThinkPHP)
  - `composer` (lệnh quản lý dependency)
  - `php` (lệnh thực thi PHP)
  - `artisan` (lệnh Laravel, nếu cần)

### 0.2 Tình huống áp dụng

#### 0.2.1 Tình huống cốt lõi
- **Phát triển backend**: Khi phát triển tính năng backend PHP
- **Phát triển API**: Khi phát triển RESTful API
- **Triển khai logic nghiệp vụ**: Khi xây dựng logic nghiệp vụ cốt lõi
- **Thao tác dữ liệu**: Khi thực hiện các thao tác cơ sở dữ liệu

#### 0.2.2 Tình huống hỗ trợ
- **Gỡ lỗi code**: Khi gỡ lỗi code backend PHP
- **Tối ưu hiệu năng**: Khi tối ưu hiệu năng backend
- **Tăng cường bảo mật**: Khi nâng cao tính bảo mật của backend
- **Thiết kế kiến trúc**: Khi thiết kế kiến trúc backend

### 0.3 Cơ chế kích hoạt

#### 0.3.1 Thời điểm gọi
- **Kích hoạt tức thời**: Kích hoạt ngay khi thao tác với file PHP
- **Kích hoạt trễ**: Kích hoạt sau 1 giây khi thao tác thư mục phức tạp
- **Kích hoạt hàng loạt**: Gộp thành một lần kích hoạt khi thao tác file hàng loạt

#### 0.3.2 Tần suất gọi
- Duyệt tệp: kích hoạt tối đa một lần mỗi 10 giây
- Thao tác tệp: kích hoạt tối đa một lần mỗi 5 giây
- Thực thi lệnh: kích hoạt tối đa một lần mỗi 3 giây

#### 0.3.3 Mức ưu tiên gọi
- **Mức ưu tiên**: Ưu tiên trung bình (3/5)
- **Xử lý tranh chấp**: Khi nhiều skill được kích hoạt cùng lúc
  - Ưu tiên cao nhất: skill cốt lõi của hệ thống
  - Ưu tiên cao: skill cấu trúc mã nguồn
  - Ưu tiên trung bình: Skill backend PHP, skill frontend, skill di động
  - Ưu tiên thấp: skill công cụ hỗ trợ
- **Giới hạn kích hoạt**: Chỉ được kích hoạt khi có thao tác liên quan đến backend PHP, không ảnh hưởng đến việc sử dụng bình thường của các skill khác

### 0.4 Hành vi sau khi kích hoạt

#### 0.4.1 Tự động phân tích
- **Phân tích code**: Phân tích cấu trúc và chất lượng code PHP
- **Phân tích phụ thuộc**: Phân tích quan hệ phụ thuộc của code
- **Phân tích hiệu năng**: Phân tích điểm nghẽn hiệu năng của code
- **Phân tích bảo mật**: Phân tích các nguy cơ bảo mật tiềm ẩn trong code

#### 0.4.2 Tự động hiển thị
- **Cấu trúc thư mục**: Trình bày cấu trúc thư mục backend PHP
- **Giải thích code**: Trình bày mô tả chức năng của code cốt lõi
- **Bộ công nghệ**: Trình bày bộ công nghệ backend
- **Quy chuẩn phát triển**: Trình bày quy chuẩn phát triển PHP

#### 0.4.3 Tự động đề xuất
- **Gợi ý phát triển**: Đưa ra gợi ý phát triển backend PHP
- **Gợi ý tối ưu**: Đưa ra gợi ý tối ưu hiệu năng
- **Đề xuất quy chuẩn**: Đưa ra đề xuất về việc tuân thủ quy chuẩn mã nguồn
- **Gợi ý bảo mật**: Đưa ra gợi ý phòng vệ bảo mật

## 1. Kiến trúc backend PHP

### 1.1 Kiến trúc tổng thể
- **Framework**: ThinkPHP 6.x
- **Mô hình kiến trúc**: Kiến trúc phân tầng MVC + Service + DAO
- **Mẫu thiết kế**: Singleton, Factory, Dependency Injection, v.v.
- **Cơ sở dữ liệu**: MySQL 5.7~8.0
- **Bộ nhớ đệm (cache)**: Redis (khuyến nghị)
- **Hàng đợi**: Hàng đợi tích hợp sẵn của ThinkPHP

### 1.2 Bộ công nghệ
- **PHP**: 7.1~7.4
- **ThinkPHP**: 6.x
- **MySQL**: 5.7+
- **Redis**: 5.0+
- **Composer**: Quản lý phụ thuộc
- **Workerman**: Dịch vụ kết nối liên tục (long connection)

### 1.3 Cấu trúc thư mục

#### 1.3.1 Thư mục cốt lõi
```
app/
├── api/              # API tầng giao tiếp
├── controller/       # Tầng controller
├── dao/              # Tầng truy cập dữ liệu
├── model/            # Tầng model
├── services/         # Tầng logic nghiệp vụ
├── event/            # Tầng xử lý sự kiện
├── middleware/       # Tầng middleware
└── validate/         # Tầng xác thực dữ liệu
```

#### 1.3.2 Thư mục cấu hình
```
config/
├── app.php           # Cấu hình ứng dụng
├── database.php      # Cấu hình cơ sở dữ liệu
├── route.php         # Cấu hình route
├── cache.php         # Cấu hình bộ nhớ đệm
└── queue.php         # Cấu hình hàng đợi
```

#### 1.3.3 Thư mục thư viện lõi
```
crmeb/
├── basic/            # Thư viện lớp cơ sở
├── exception/        # Xử lý ngoại lệ
└── services/         # Service cốt lõi
```

## 2. Các mô-đun cốt lõi

### 2.1 Module controller
- **Chức năng**: Xử lý request HTTP, điều phối route, phản hồi client
- **Đặc điểm**: Phong cách RESTful, phân tầng rõ ràng, kiểm tra tham số
- **File chính**: `BaseController.php` (lớp cơ sở của controller)
- **Code mẫu**:
  ```php
  <?php
  namespace app\controller;
  
  use app\BaseController;
  
  class UserController extends BaseController
  {
      public function index()
      {
          return $this->success('Lấy danh sách người dùng thành công', $data);
      }
  }
  ```

### 2.2 Module service
- **Chức năng**: Hiện thực logic nghiệp vụ cốt lõi, đóng gói các quy tắc nghiệp vụ
- **Đặc điểm**: Logic nghiệp vụ tập trung, khả năng tái sử dụng cao, dễ kiểm thử
- **File chính**: Các lớp service nghiệp vụ
- **Code mẫu**:
  ```php
  <?php
  namespace app\services;
  
  use crmeb\basic\BaseServices;
  
  class UserServices extends BaseServices
  {
      public function createUser($data)
      {
          // Triển khai logic nghiệp vụ
          return $userId;
      }
  }
  ```

### 2.3 Module truy cập dữ liệu
- **Chức năng**: Đóng gói các thao tác cơ sở dữ liệu, cung cấp các phương thức truy cập dữ liệu
- **Đặc điểm**: Quản lý SQL tập trung, chống SQL injection, tăng cường bảo mật
- **File chính**: Các lớp đối tượng truy cập dữ liệu (DAO)
- **Code mẫu**:
  ```php
  <?php
  namespace app\dao;
  
  use crmeb\basic\BaseDao;
  
  class UserDao extends BaseDao
  {
      public function getUserById($id)
      {
          return $this->where('id', $id)->find();
      }
  }
  ```

### 2.4 Module model
- **Chức năng**: Định nghĩa model dữ liệu, xử lý quan hệ dữ liệu
- **Đặc điểm**: Ánh xạ ORM, tự động nhận cấu trúc bảng, truy vấn liên kết
- **File chính**: `BaseModel.php` (lớp cơ sở của model)
- **Code mẫu**:
  ```php
  <?php
  namespace app\model;
  
  use crmeb\basic\BaseModel;
  
  class User extends BaseModel
  {
      protected $table = 'user';
      protected $pk = 'id';
  }
  ```

### 2.5 Module route
- **Chức năng**: Định nghĩa quy tắc route cho URL, điều phối request
- **Đặc điểm**: Phong cách RESTful, nhóm route, hỗ trợ middleware
- **File chính**: `route/api.php`, `route/app.php`
- **Code mẫu**:
  ```php
  <?php
  use think\facade\Route;
  
  Route::get('user/:id', 'User/read');
  Route::post('user', 'User/save');
  Route::put('user/:id', 'User/update');
  Route::delete('user/:id', 'User/delete');
  ```

## 3. Quy chuẩn phát triển

### 3.1 Quy chuẩn code
- **Quy chuẩn PHP**: Tuân theo quy chuẩn đặt tên PSR-2
- **Quy tắc đặt tên**:
  - Tên lớp: PascalCase
  - Phương thức/biến: camelCase
  - Hằng số: Viết hoa toàn bộ, phân tách bằng dấu gạch dưới
  - Tên file: Trùng với tên lớp, PascalCase
- **Thụt lề code**: Thụt lề 4 dấu cách, không được dùng ký tự tab
- **Quy chuẩn chú thích**: Chú thích phương thức, chú thích lớp, chú thích logic quan trọng

### 3.2 Quy chuẩn thư mục
- **Tổ chức theo module chức năng**: Code cùng chức năng đặt trong cùng một thư mục
- **Phân cấp thư mục rõ ràng**: Tránh lồng thư mục quá sâu
- **Đặt tên có ngữ nghĩa**: Tên thư mục cần phản ánh chức năng của nó

### 3.3 Quy chuẩn cơ sở dữ liệu
- **Tên bảng**: Chữ thường, phân tách bằng dấu gạch dưới
- **Khóa chính**: Thống nhất đặt tên là `id`
- **Khóa ngoại**: Định dạng `{tên_bảng}_id`
- **Trường thời gian**: `create_time`/`update_time`
- **Trường trạng thái**: `status`, giá trị mặc định 0

### 3.4 Quy chuẩn API
- **Phong cách RESTful**: Sử dụng các phương thức HTTP chuẩn
- **Định dạng phản hồi**: Định dạng JSON thống nhất
- **Xử lý lỗi**: Sử dụng mã lỗi và thông báo lỗi thống nhất
- **Kiểm tra tham số**: Kiểm tra tham số nghiêm ngặt

## 4. Thực tiễn tốt nhất

### 4.1 Quy trình phát triển
1. **Phân tích yêu cầu**: Làm rõ yêu cầu chức năng và phương án kỹ thuật
2. **Thiết kế kiến trúc**: Thiết kế cấu trúc module và cấu trúc cơ sở dữ liệu
3. **Viết code**: Lập trình tuân theo quy chuẩn phát triển
4. **Kiểm thử và xác minh**: Unit test và kiểm thử chức năng
5. **Rà soát code (code review)**: Kiểm tra chất lượng code
6. **Triển khai và phát hành**: Build và triển khai

### 4.2 Tối ưu hiệu năng
- **Tối ưu cơ sở dữ liệu**: Tối ưu index, tối ưu SQL
- **Chiến lược cache**: Sử dụng cache Redis hợp lý
- **Tối ưu code**: Giảm vòng lặp lồng nhau, tối ưu thuật toán
- **Tối ưu request**: Gộp request, giảm số lần gọi HTTP

### 4.3 Phòng vệ bảo mật
- **Chống SQL injection**: Dùng parameter binding, tránh nối chuỗi SQL trực tiếp
- **Chống XSS**: Kiểm tra dữ liệu đầu vào và mã hóa (encode) dữ liệu đầu ra
- **Chống CSRF**: Sử dụng xác thực bằng Token
- **Kiểm soát quyền**: Cơ chế kiểm tra quyền nghiêm ngặt
- **Bảo vệ thông tin nhạy cảm**: Mã hóa thông tin nhạy cảm khi lưu trữ

### 4.4 Tái sử dụng code
- **Trừu tượng hóa logic dùng chung**: Tách logic dùng chung thành service hoặc lớp tiện ích
- **Sử dụng Traits**: Tái sử dụng các đoạn code
- **Kế thừa lớp cơ sở**: Kế thừa lớp cơ sở để có các chức năng dùng chung
- **Dependency Injection**: Nâng cao khả năng kiểm thử và khả năng bảo trì của code

## 5. Sự cố thường gặp

### 5.1 Vấn đề hiệu năng
- **Truy vấn cơ sở dữ liệu chậm**: Kiểm tra index, tối ưu SQL
- **Chiếm dụng bộ nhớ cao**: Kiểm tra các mảng lớn, tối ưu việc sử dụng bộ nhớ
- **Thời gian phản hồi lâu**: Kiểm tra logic nghiệp vụ, sử dụng cache

### 5.2 Vấn đề bảo mật
- **SQL injection**: Dùng parameter binding, tránh nối chuỗi SQL trực tiếp
- **Tấn công XSS**: Kiểm tra và lọc dữ liệu đầu vào
- **Tấn công CSRF**: Hiện thực xác thực CSRF Token
- **Vượt quyền**: Kiểm tra quyền nghiêm ngặt, tránh lỗ hổng logic

### 5.3 Vấn đề triển khai
- **Cấu hình môi trường**: Đảm bảo cấu hình môi trường production chính xác
- **Quản lý phụ thuộc**: Sử dụng Composer để quản lý phụ thuộc
- **Xóa cache**: Xóa cache sau khi triển khai
- **Quản lý log**: Cấu hình mức log hợp lý

### 5.4 Vấn đề về code
- **Đặt tên không chuẩn**: Tuân theo quy chuẩn đặt tên
- **Thiếu chú thích**: Thêm các chú thích cần thiết
- **Logic rối rắm**: Refactor code, nâng cao tính dễ đọc
- **Code trùng lặp**: Trừu tượng hóa logic dùng chung, giảm trùng lặp

## 6. Công cụ phát triển khuyên dùng

### 6.1 IDE khuyên dùng
- **PHPStorm**: IDE chuyên nghiệp cho PHP, tính năng mạnh mẽ
- **VS Code**: Trình soạn thảo nhẹ, kho plugin phong phú
- **Sublime Text**: Trình soạn thảo code tốc độ cao

### 6.2 Plugin khuyên dùng
- **PHP Inspections**: Plugin kiểm tra code PHP
- **Laravel Idea**: Plugin phát triển Laravel (nếu cần)
- **GitLens**: Plugin mở rộng tính năng Git
- **Debugger for Chrome**: Plugin gỡ lỗi trên trình duyệt

### 6.3 Công cụ khuyên dùng
- **Composer**: Công cụ quản lý phụ thuộc cho PHP
- **PHPUnit**: Framework unit test cho PHP
- **Postman**: Công cụ kiểm thử API
- **MySQL Workbench**: Công cụ thiết kế cơ sở dữ liệu
- **Redis Desktop Manager**: Công cụ quản lý Redis

## 7. Tài liệu tham khảo

### 7.1 Tài liệu chính thức
- [Tài liệu chính thức ThinkPHP 6](https://www.kancloud.cn/manual/thinkphp6_0)
- [Tài liệu chính thức PHP](https://www.php.net/docs.php)
- [Tài liệu chính thức MySQL](https://dev.mysql.com/doc/)
- [Tài liệu chính thức Redis](https://redis.io/documentation)

### 7.2 Tài nguyên học tập
- [Laravel Academy](https://learnku.com/laravel)
- [PHP Chinese Website](https://www.php.cn/)
- [Cộng đồng ThinkPHP](https://www.thinkphp.cn/)
- [Stack Overflow](https://stackoverflow.com/)

### 7.3 Quy chuẩn code
- [Chuẩn PSR](https://www.php-fig.org/psr/)
- [Quy chuẩn code ThinkPHP](https://www.thinkphp.cn/doc)
- [Thực tiễn tốt nhất cho PHP](https://phpbestpractices.org/)

### 7.4 Tài nguyên khác
- Tài liệu quy trình phát triển API ./references/api_create.md
- Tài liệu quy chuẩn code ./references/code_style.md
- Tài liệu thiết kế cơ sở dữ liệu ./references/db_design.md
- Tài liệu triển khai dự án ./references/deploy.md
- Tài liệu cấu trúc thư mục ./references/directory_structure.md
- Tài liệu mã lỗi ./references/error_code.md
- Tài liệu cấu hình hệ thống ./references/system_config.md
- Tài liệu quy trình request API ./references/api_flow.md

