# Tài liệu phát triển cài đặt hệ thống

## 1. Tổng quan tài liệu

### 1.1 Mục đích tài liệu
Tài liệu này nhằm mô tả chi tiết kiến trúc thiết kế, chức năng cốt lõi, cách triển khai code và quy chuẩn phát triển của module cài đặt hệ thống trong CRMEB, cung cấp tài liệu tham khảo phát triển đầy đủ cho lập trình viên hệ thống.

### 1.2 Phạm vi áp dụng
Tài liệu này dành cho lập trình viên, nhân viên bảo trì và nhà phát triển thứ cấp của hệ thống CRMEB, dùng để hướng dẫn công việc phát triển, mở rộng và bảo trì module cài đặt hệ thống.

### 1.3 Định nghĩa thuật ngữ
| Thuật ngữ | Giải thích |
|------|------|
| RBAC | Kiểm soát quyền truy cập dựa trên vai trò (Role-Based Access Control) |
| Cấp bậc quản trị viên | Quan hệ phân cấp giữa các quản trị viên, cấp trên có thể quản lý cấp dưới |
| Vai trò | Tập hợp các quyền, dùng để gán cho quản trị viên |
| Quyền menu | Kiểm soát các menu mà quản trị viên được phép truy cập |
| Quyền thao tác | Kiểm soát các thao tác mà quản trị viên được phép thực hiện |
| Mục cấu hình | Các tham số cài đặt của hệ thống |
| Lưu trữ cloud | Dịch vụ đám mây dùng để lưu trữ hình ảnh, tệp và các tài nguyên khác |
| Tác vụ định kỳ | Tác vụ do hệ thống tự động thực thi |

## 2. Các mô-đun chức năng cốt lõi

### 2.1 Quản lý quản trị viên
- **Danh sách quản trị viên**: Hiển thị thông tin cơ bản của quản trị viên, hỗ trợ tìm kiếm, phân trang
- **Thêm quản trị viên**: Thêm quản trị viên mới, thiết lập thông tin cơ bản, vai trò, cấp bậc, v.v.
- **Sửa quản trị viên**: Chỉnh sửa thông tin quản trị viên, bao gồm vai trò, cấp bậc, trạng thái, v.v.
- **Xóa quản trị viên**: Xóa tài khoản quản trị viên
- **Quản lý trạng thái**: Kích hoạt/vô hiệu hóa tài khoản quản trị viên
- **Đặt lại mật khẩu**: Đặt lại mật khẩu đăng nhập của quản trị viên
- **Kế thừa quyền**: Kiểm soát quyền dựa trên cấp bậc, cấp trên có thể quản lý cấp dưới

### 2.2 Quản lý vai trò
- **Danh sách vai trò**: Hiển thị thông tin cơ bản của vai trò, hỗ trợ tìm kiếm, phân trang
- **Thêm vai trò**: Thêm vai trò mới, thiết lập tên vai trò, cấp bậc, quyền, v.v.
- **Sửa vai trò**: Chỉnh sửa thông tin vai trò, bao gồm tên, cấp bậc, quyền, v.v.
- **Xóa vai trò**: Xóa vai trò
- **Quản lý trạng thái**: Kích hoạt/vô hiệu hóa vai trò
- **Phân quyền**: Phân quyền menu và quyền thao tác cho vai trò
- **Kế thừa quyền**: Kiểm soát vai trò dựa trên cấp bậc

### 2.3 Quản lý menu
- **Danh sách menu**: Hiển thị menu dạng cấu trúc cây, hỗ trợ tìm kiếm, phân trang
- **Thêm menu**: Thêm menu mới, thiết lập tên menu, đường dẫn, biểu tượng, quyền, v.v.
- **Sửa menu**: Chỉnh sửa thông tin menu, bao gồm tên, đường dẫn, biểu tượng, quyền, v.v.
- **Xóa menu**: Xóa menu
- **Quản lý trạng thái**: Hiện/ẩn menu
- **Quản lý mã định danh quyền**: Thiết lập mã định danh quyền của menu
- **Lưu hàng loạt**: Hỗ trợ lưu hàng loạt thông tin menu
- **Lấy menu bên trái**: Cung cấp API lấy các menu mà quản trị viên hiện tại được phép truy cập

### 2.4 Cấu hình hệ thống
- **Quản lý danh mục cấu hình**: Quản lý các danh mục cấu hình hệ thống
- **Quản lý mục cấu hình**: Thêm, sửa, xóa các mục cấu hình hệ thống
- **Sửa cấu hình**: Chỉnh sửa cấu hình cơ bản của hệ thống, như tên website, LOGO, thông tin liên hệ, v.v.
- **Quản lý trạng thái cấu hình**: Bật/tắt mục cấu hình
- **Bộ nhớ đệm cấu hình**: Quản lý cache cấu hình, nâng cao hiệu năng hệ thống

### 2.5 Cấu hình lưu trữ
- **Quản lý phương thức lưu trữ**: Cấu hình phương thức lưu trữ mà hệ thống sử dụng, như lưu trữ cục bộ, Alibaba Cloud OSS, Tencent Cloud COS, v.v.
- **Cấu hình lưu trữ đám mây**: Cấu hình tham số của các dịch vụ lưu trữ đám mây
- **Quản lý tên miền lưu trữ**: Thiết lập tên miền truy cập tài nguyên
- **Quản lý trạng thái lưu trữ**: Bật/tắt phương thức lưu trữ

### 2.6 Nhật ký hệ thống
- **Ghi nhật ký**: Ghi nhật ký thao tác hệ thống, nhật ký đăng nhập, v.v.
- **Tra cứu nhật ký**: Hỗ trợ tra cứu nhật ký theo điều kiện
- **Phân trang nhật ký**: Hiển thị thông tin nhật ký theo trang
- **Dọn dẹp nhật ký**: Xóa các nhật ký đã hết hạn

### 2.7 Sao lưu dữ liệu
- **Sao lưu cơ sở dữ liệu**: Sao lưu cơ sở dữ liệu hệ thống
- **Quản lý bản sao lưu**: Quản lý tệp sao lưu, hỗ trợ tải xuống, xóa
- **Khôi phục cơ sở dữ liệu**: Khôi phục cơ sở dữ liệu từ tệp sao lưu
- **Tối ưu cơ sở dữ liệu**: Tối ưu hiệu năng cơ sở dữ liệu

### 2.8 Tác vụ định kỳ
- **Danh sách tác vụ**: Hiển thị các tác vụ định kỳ của hệ thống
- **Thêm tác vụ**: Thêm tác vụ định kỳ mới
- **Sửa tác vụ**: Chỉnh sửa thông tin tác vụ định kỳ
- **Xóa tác vụ**: Xóa tác vụ định kỳ
- **Quản lý trạng thái tác vụ**: Bật/tắt tác vụ định kỳ
- **Thực thi tác vụ**: Thực thi thủ công tác vụ định kỳ

### 2.9 Quản lý route hệ thống
- **Danh sách route**: Hiển thị các route của hệ thống
- **Đồng bộ route**: Đồng bộ route hệ thống vào cơ sở dữ liệu
- **Quản lý quyền route**: Phân quyền cho route

### 2.10 Sinh code
- **Cấu hình sinh code**: Cấu hình quy tắc sinh code
- **Sinh code**: Sinh code model, controller, view, v.v. dựa trên bảng cơ sở dữ liệu
- **Quản lý lịch sử sinh code**: Quản lý các bản ghi sinh code

## 3. Cấu trúc và thiết kế code

### 3.1 Thiết kế kiến trúc
Hệ thống sử dụng thiết kế kiến trúc phân tầng, tuân thủ nghiêm ngặt mẫu thiết kế MVC:
```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    Routes       │     │   Controllers   │     │    Services     │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                         ┌─────────────────┐
                         │     Models      │
                         └─────────────────┘
                                 │
                         ┌─────────────────┐
                         │     Daos        │
                         └─────────────────┘
```

### 3.2 Cấu trúc tệp cốt lõi

#### 3.2.1 Cấu hình route
- **Route cài đặt hệ thống**: `crmeb/app/adminapi/route/setting.php` - Bao gồm các route quản trị viên, vai trò, menu, cấu hình hệ thống, v.v.
- **Route bảo trì hệ thống**: `crmeb/app/adminapi/route/system.php` - Bao gồm các route cấu hình lưu trữ, nhật ký hệ thống, sao lưu dữ liệu, tác vụ định kỳ, v.v.
- **Route chung**: `crmeb/app/adminapi/route/common.php` - Bao gồm các API chung như lấy menu bên trái, v.v.

#### 3.2.2 Tầng điều khiển (Controller)
**Controller cài đặt hệ thống**: `crmeb/app/adminapi/controller/v1/setting/`
- `SystemAdmin.php` - Controller quản lý quản trị viên
- `SystemRole.php` - Controller quản lý vai trò
- `SystemMenus.php` - Controller quản lý menu
- `SystemConfig.php` - Controller cấu hình hệ thống
- `SystemStorage.php` - Controller cấu hình lưu trữ
- `SystemRoute.php` - Controller quản lý route
- `SystemCrud.php` - Controller sinh code

**Controller bảo trì hệ thống**: `crmeb/app/adminapi/controller/v1/system/`
- `SystemLog.php` - Controller nhật ký hệ thống
- `SystemDatabackup.php` - Controller sao lưu dữ liệu
- `SystemCrontab.php` - Controller tác vụ định kỳ

#### 3.2.3 Tầng dịch vụ (Services)
**Services cài đặt hệ thống**: `crmeb/app/services/system/`
- `SystemAdminServices.php` - Service quản lý quản trị viên
- `SystemRoleServices.php` - Service quản lý vai trò
- `SystemMenusServices.php` - Service quản lý menu
- `SystemConfigServices.php` - Service cấu hình hệ thống
- `SystemStorageServices.php` - Service cấu hình lưu trữ
- `SystemRouteServices.php` - Service quản lý route
- `SystemCrudServices.php` - Service sinh code

**Services bảo trì hệ thống**:
- `SystemLogServices.php` - Service nhật ký hệ thống
- `SystemDatabackupServices.php` - Service sao lưu dữ liệu
- `SystemCrontabServices.php` - Service tác vụ định kỳ

#### 3.2.4 Tầng mô hình (Model)
**Model cài đặt hệ thống**: `crmeb/app/model/system/`
- `SystemAdmin.php` - Model quản trị viên
- `SystemRole.php` - Model vai trò
- `SystemMenus.php` - Model menu
- `SystemConfig.php` - Model cấu hình hệ thống
- `SystemStorage.php` - Model cấu hình lưu trữ
- `SystemRoute.php` - Model route

**Model bảo trì hệ thống**:
- `SystemLog.php` - Model nhật ký hệ thống
- `SystemDatabackup.php` - Model sao lưu dữ liệu
- `SystemCrontab.php` - Model tác vụ định kỳ

## 4. Thiết kế cơ sở dữ liệu

### 4.1 Bảng quản trị viên (system_admin)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID quản trị viên |
| role_id | int(10) | ID vai trò |
| level | tinyint(1) | Cấp bậc quản trị viên |
| username | varchar(20) | Tên người dùng |
| password | varchar(60) | Mật khẩu |
| salt | varchar(10) | Salt mật khẩu |
| real_name | varchar(20) | Họ tên |
| avatar | varchar(255) | Ảnh đại diện |
| phone | varchar(20) | Số điện thoại |
| email | varchar(50) | Email |
| last_login_ip | varchar(15) | IP đăng nhập lần cuối |
| last_login_time | int(10) | Thời gian đăng nhập cuối |
| login_num | int(10) | Số lần đăng nhập |
| status | tinyint(1) | Trạng thái (0-vô hiệu hóa, 1-kích hoạt) |
| is_delete | tinyint(1) | Đã xóa |
| add_time | int(10) | Thời gian thêm |

### 4.2 Bảng vai trò (system_role)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID vai trò |
| role_name | varchar(20) | Tên vai trò |
| level | tinyint(1) | Cấp bậc vai trò |
| auth_rules | text | Quy tắc quyền (định dạng JSON) |
| status | tinyint(1) | Trạng thái (0-vô hiệu hóa, 1-kích hoạt) |
| is_delete | tinyint(1) | Đã xóa |
| add_time | int(10) | Thời gian thêm |

### 4.3 Bảng menu (system_menus)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID menu |
| pid | int(10) | ID menu cha |
| menu_name | varchar(20) | Tên menu |
| menu_type | tinyint(1) | Loại menu (0-thư mục, 1-menu, 2-nút) |
| url | varchar(100) | Đường dẫn menu |
| icon | varchar(50) | Biểu tượng menu |
| auth | varchar(50) | Mã định danh quyền |
| open_type | varchar(10) | Cách mở (_self, _blank) |
| sort | int(10) | Thứ tự sắp xếp |
| is_show | tinyint(1) | Có hiển thị không (0-ẩn, 1-hiện) |
| status | tinyint(1) | Trạng thái (0-vô hiệu hóa, 1-kích hoạt) |
| add_time | int(10) | Thời gian thêm |

### 4.4 Bảng cấu hình hệ thống (system_config)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID cấu hình |
| menu_name | varchar(20) | Tên menu |
| type | tinyint(1) | Loại cấu hình |
| title | varchar(50) | Tiêu đề cấu hình |
| name | varchar(50) | Tên cấu hình |
| value | text | Giá trị cấu hình |
| placeholder | varchar(100) | Mô tả cấu hình |
| tips | varchar(255) | Gợi ý cấu hình |
| sort | int(10) | Thứ tự sắp xếp |
| status | tinyint(1) | Trạng thái (0-vô hiệu hóa, 1-kích hoạt) |
| add_time | int(10) | Thời gian thêm |

### 4.5 Bảng cấu hình lưu trữ (system_storage)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID cấu hình |
| name | varchar(20) | Tên lưu trữ |
| type | varchar(20) | Loại lưu trữ (local, oss, cos, qiniu) |
| config | text | Cấu hình lưu trữ (định dạng JSON) |
| is_default | tinyint(1) | Có phải mặc định không (0-không, 1-có) |
| status | tinyint(1) | Trạng thái (0-vô hiệu hóa, 1-kích hoạt) |
| add_time | int(10) | Thời gian thêm |

### 4.6 Bảng nhật ký hệ thống (system_log)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID nhật ký |
| admin_id | int(10) | ID quản trị viên |
| admin_name | varchar(20) | Tên quản trị viên |
| ip | varchar(15) | IP thao tác |
| url | varchar(255) | URL thao tác |
| method | varchar(10) | Phương thức request |
| param | text | Tham số request |
| type | tinyint(1) | Loại nhật ký (0-nhật ký đăng nhập, 1-nhật ký thao tác) |
| content | varchar(255) | Nội dung thao tác |
| add_time | int(10) | Thời gian thêm |

## 5. Mô tả API

### 5.1 API quản lý quản trị viên

#### 5.1.1 Danh sách quản trị viên
- **URL yêu cầu**: `/adminapi/v1/setting/admin/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: setting/admin/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - keyword: từ khóa tìm kiếm
  - status: trạng thái
- **Kết quả trả về**: Dữ liệu danh sách quản trị viên

#### 5.1.2 Thêm quản trị viên
- **URL yêu cầu**: `/adminapi/v1/setting/admin/create`
- **Phương thức yêu cầu**: POST
- **Quyền yêu cầu**: setting/admin/create
- **Tham số yêu cầu**:
  - role_id: ID vai trò
  - username: tên đăng nhập
  - password: mật khẩu
  - real_name: họ tên
  - phone: số điện thoại
  - email: địa chỉ email
- **Kết quả trả về**: Kết quả thao tác

#### 5.1.3 Sửa quản trị viên
- **URL yêu cầu**: `/adminapi/v1/setting/admin/update/{id}`
- **Phương thức yêu cầu**: PUT
- **Quyền yêu cầu**: setting/admin/update
- **Tham số yêu cầu**:
  - role_id: ID vai trò
  - real_name: họ tên
  - phone: số điện thoại
  - email: địa chỉ email
  - status: trạng thái
- **Kết quả trả về**: Kết quả thao tác

### 5.2 API quản lý vai trò

#### 5.2.1 Danh sách vai trò
- **URL yêu cầu**: `/adminapi/v1/setting/role/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: setting/role/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - keyword: từ khóa tìm kiếm
  - status: trạng thái
- **Kết quả trả về**: Dữ liệu danh sách vai trò

#### 5.2.2 Thêm vai trò
- **URL yêu cầu**: `/adminapi/v1/setting/role/create`
- **Phương thức yêu cầu**: POST
- **Quyền yêu cầu**: setting/role/create
- **Tham số yêu cầu**:
  - role_name: tên vai trò
  - level: cấp bậc vai trò
  - auth_rules: quy tắc quyền (định dạng JSON)
- **Kết quả trả về**: Kết quả thao tác

#### 5.2.3 Phân quyền
- **URL yêu cầu**: `/adminapi/v1/setting/role/permission/{id}`
- **Phương thức yêu cầu**: PUT
- **Quyền yêu cầu**: setting/role/permission
- **Tham số yêu cầu**:
  - auth_rules: quy tắc quyền (định dạng JSON)
- **Kết quả trả về**: Kết quả thao tác

### 5.3 API quản lý menu

#### 5.3.1 Danh sách menu
- **URL yêu cầu**: `/adminapi/v1/setting/menu/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: setting/menu/list
- **Kết quả trả về**: Dữ liệu danh sách menu (cấu trúc cây)

#### 5.3.2 Thêm menu
- **URL yêu cầu**: `/adminapi/v1/setting/menu/create`
- **Phương thức yêu cầu**: POST
- **Quyền yêu cầu**: setting/menu/create
- **Tham số yêu cầu**:
  - pid: ID menu cha
  - menu_name: tên menu
  - menu_type: loại menu
  - url: đường dẫn menu
  - icon: biểu tượng menu
  - auth: mã định danh quyền
- **Kết quả trả về**: Kết quả thao tác

#### 5.3.3 Lấy menu bên trái
- **URL yêu cầu**: `/adminapi/v1/common/menu`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: Người dùng đã đăng nhập
- **Kết quả trả về**: Menu bên trái mà người dùng hiện tại được phép truy cập

### 5.4 API cấu hình hệ thống

#### 5.4.1 Danh sách cấu hình
- **URL yêu cầu**: `/adminapi/v1/setting/config/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: setting/config/list
- **Tham số yêu cầu**:
  - menu_name: danh mục cấu hình
- **Kết quả trả về**: Dữ liệu danh sách cấu hình

#### 5.4.2 Sửa cấu hình
- **URL yêu cầu**: `/adminapi/v1/setting/config/update`
- **Phương thức yêu cầu**: PUT
- **Quyền yêu cầu**: setting/config/update
- **Tham số yêu cầu**:
  - config: các mục cấu hình (định dạng JSON, khóa là tên cấu hình, giá trị là giá trị cấu hình)
- **Kết quả trả về**: Kết quả thao tác

## 5. Quy chuẩn và quy trình phát triển

### 5.1 Quy chuẩn code
1. **Quy chuẩn đặt tên**:
   - Tên lớp dùng kiểu PascalCase (UpperCamelCase), ví dụ `SystemAdminServices`
   - Tên phương thức dùng kiểu camelCase, ví dụ `getAdminList`
   - Tên biến dùng kiểu camelCase, ví dụ `rolePermission`
   - Tên hằng số viết hoa toàn bộ, phân tách bằng dấu gạch dưới, ví dụ `MENU_TYPE_CATALOG`

2. **Cấu trúc code**:
   - Tuân thủ nghiêm ngặt kiến trúc phân tầng, cấm gọi vượt tầng
   - Mỗi phương thức chỉ đảm nhận một trách nhiệm, số dòng code không vượt quá 50 dòng
   - Thêm chú thích hợp lý, mô tả chức năng của phương thức, ý nghĩa tham số và giá trị trả về
   - Code liên quan đến quyền bắt buộc phải thêm xác thực quyền

3. **Kiểm soát quyền**:
   - Tất cả API quản lý bắt buộc phải thêm xác thực quyền
   - Xác thực quyền được thực hiện thông qua middleware
   - Mã định danh quyền phải là duy nhất và đúng định dạng quy chuẩn
   - Quyền của vai trò phải bao gồm quyền của tất cả vai trò cấp dưới

### 5.2 Quy trình phát triển
1. **Phân tích yêu cầu**: Làm rõ yêu cầu chức năng và logic nghiệp vụ của cài đặt hệ thống
2. **Giai đoạn thiết kế**: Thiết kế cấu trúc dữ liệu, API và quy trình nghiệp vụ
3. **Triển khai code**: Viết code theo kiến trúc phân tầng
4. **Kiểm thử đơn vị**: Viết unit test, đảm bảo chức năng hoạt động chính xác
5. **Kiểm thử tích hợp**: Tiến hành kiểm thử tích hợp module, kiểm thử các chức năng như kiểm soát quyền, v.v.
6. **Kiểm thử bảo mật**: Kiểm thử bảo mật đối với kiểm soát quyền, mã hóa mật khẩu, v.v.
7. **Rà soát code**: Gửi code để rà soát (code review), đảm bảo chất lượng code
8. **Triển khai vận hành**: Triển khai lên môi trường production

## 6. Các vấn đề thường gặp và giải pháp

### 6.1 Vấn đề kế thừa quyền
**Vấn đề**: Quản trị viên cấp trên không thể quản lý quản trị viên hoặc vai trò cấp dưới
**Cách khắc phục**:
1. Kiểm tra thiết lập cấp bậc quản trị viên, đảm bảo cấp bậc của cấp trên thấp hơn cấp bậc của cấp dưới
2. Kiểm tra thiết lập cấp bậc vai trò, đảm bảo cấp bậc của vai trò cấp trên thấp hơn cấp bậc của vai trò cấp dưới
3. Kiểm tra logic phân quyền, đảm bảo cấp trên có quyền quản lý cấp dưới

### 6.2 Vấn đề hiển thị menu
**Vấn đề**: Quản trị viên không thấy một số menu
**Cách khắc phục**:
1. Kiểm tra trạng thái menu, đảm bảo menu đã được bật
2. Kiểm tra menu đã được thiết lập hiển thị chưa
3. Kiểm tra vai trò của quản trị viên có quyền đối với menu này không
4. Kiểm tra mã định danh quyền của menu có đúng không

### 6.3 Vấn đề cấu hình không có hiệu lực
**Vấn đề**: Sửa cấu hình hệ thống nhưng không có hiệu lực
**Cách khắc phục**:
1. Kiểm tra cấu hình đã được lưu thành công chưa
2. Kiểm tra cấu hình đã được bật chưa
3. Xóa cache cấu hình
4. Kiểm tra logic đọc cấu hình, đảm bảo đọc đúng cấu hình mới nhất

### 6.4 Vấn đề ghi nhật ký
**Vấn đề**: Thao tác hệ thống không được ghi nhật ký
**Cách khắc phục**:
1. Kiểm tra middleware ghi nhật ký đã được bật chưa
2. Kiểm tra logic ghi nhật ký, đảm bảo bao quát mọi thao tác
3. Kiểm tra bảng nhật ký có quyền ghi không

## 7. Mở rộng và tùy biến

### 7.1 Thêm mục cấu hình mới
1. **Thao tác cơ sở dữ liệu**: Thêm mục cấu hình mới vào bảng system_config
2. **Sửa code**:
   - Thêm phương thức đọc mục cấu hình trong SystemConfigServices
   - Thêm chức năng sửa mục cấu hình trong controller cấu hình
3. **Sửa frontend**: Thêm form cấu hình tương ứng vào trang cấu hình ở frontend

### 7.2 Thêm loại menu mới
1. **Thao tác cơ sở dữ liệu**: Thêm hằng số loại menu mới cho bảng system_menus
2. **Sửa code**:
   - Thêm logic xử lý loại menu trong SystemMenusServices
   - Thêm xác thực loại menu trong controller menu
3. **Sửa frontend**: Thêm tùy chọn loại menu tương ứng vào trang quản lý menu ở frontend

### 7.3 Mở rộng kiểm soát quyền
1. **Thiết kế mở rộng**: Mở rộng mô hình phân quyền, bổ sung kiểm soát quyền chi tiết hơn
2. **Sửa code**:
   - Mở rộng bảng quyền vai trò, thêm các trường quyền chi tiết hơn
   - Sửa logic xác thực quyền, hỗ trợ kiểm soát quyền chi tiết hơn
3. **Sửa frontend**: Thêm tùy chọn quyền tương ứng vào trang phân quyền ở frontend

## 8. Lịch sử cập nhật tài liệu

| Phiên bản | Ngày cập nhật | Nội dung cập nhật | Người cập nhật |
|------|----------|----------|--------|
| 1.0 | 2026-01-19 | Phiên bản đầu tiên, bao gồm các chức năng cốt lõi như quản lý quản trị viên, quản lý vai trò, quản lý menu, cấu hình hệ thống, cấu hình lưu trữ, nhật ký hệ thống, sao lưu dữ liệu, tác vụ định kỳ, v.v. | AI Assistant |

## 9. Phụ lục

### 9.1 Sơ đồ quan hệ các lớp cốt lõi

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    Routes       │     │   Controllers   │     │    Services     │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                         ┌─────────────────┐
                         │     Models      │
                         └─────────────────┘
                                 │
                         ┌─────────────────┐
                         │     Daos        │
                         └─────────────────┘
```

### 9.2 Lưu đồ kiểm soát quyền

```
Quy trình kiểm soát quyền:
1. Người dùng đăng nhập hệ thống, nhận token
2. Gửi request API, kèm theo token
3. Middleware phân quyền xác thực tính hợp lệ của token
4. Lấy vai trò của người dùng
5. Lấy các quyền mà vai trò sở hữu
6. Kiểm tra route được yêu cầu có nằm trong danh sách quyền không
7. Xác thực thành công thì thực thi request; xác thực thất bại thì trả về lỗi quyền
```

### 9.3 Sơ đồ kế thừa quyền menu

```
Quan hệ kế thừa quyền menu:
┌─────────────────┐
│   Siêu quản trị viên     │
└─────────┬───────┘
         │
┌─────────▼───────┐
│   Quản trị viên cấp 1     │
└─────────┬───────┘
         │
┌─────────▼───────┐
│   Quản trị viên cấp 2     │
└─────────────────┘

Ghi chú: Quản trị viên cấp trên có thể quản lý tất cả quản trị viên cấp dưới và có toàn bộ quyền của quản trị viên cấp dưới
```

### 9.4 Danh sách danh mục cấu hình

| Tên danh mục | Mô tả |
|----------|------|
| basic | Cấu hình cơ bản |
| shop | Cấu hình cửa hàng |
| payment | Cấu hình thanh toán |
| sms | Cấu hình SMS |
| storage | Cấu hình lưu trữ |
| wechat | Cấu hình WeChat |
| express | Cấu hình vận chuyển |
| marketing | Cấu hình marketing |

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
