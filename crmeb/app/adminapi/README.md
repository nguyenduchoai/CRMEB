Thư mục crmeb/app/adminapi chủ yếu chứa các tệp API của hệ thống quản trị.

Cụ thể:

- Các tệp trong thư mục adminapi đều là tệp bộ điều khiển (Controller) của hệ thống quản trị, các controller này được dùng để xử lý mọi loại yêu cầu của hệ thống quản trị.

- Mỗi tệp controller tương ứng với một mô-đun chức năng của hệ thống quản trị, ví dụ AuthController xử lý các yêu cầu của mô-đun xác thực, StoreProduct xử lý các yêu cầu của mô-đun sản phẩm, v.v.

- Trong controller có nhiều phương thức, các phương thức này tương đương với các API, có thể xử lý yêu cầu GET, POST và trả về dữ liệu JSON.

- Khi trình duyệt hoặc APP gọi các API này, yêu cầu sẽ được gửi đến phương thức controller tương ứng, ví dụ yêu cầu của API đăng nhập được gửi đến phương thức login trong tệp Login.

- Sau khi xử lý xong yêu cầu, controller trả kết quả xử lý về cho trình duyệt hoặc APP thông qua việc trả về đối tượng Response.

Nói một cách đơn giản, thư mục adminapi phụ trách toàn bộ API của hệ thống quản trị, các API này được APP hoặc frontend gọi để thực hiện các thao tác quản lý như truy vấn dữ liệu, thêm, sửa, xóa, v.v. Khi thêm chức năng mới cho trang quản trị, lập trình viên cũng cần thêm controller và API tương ứng trong thư mục này.

Thực chất, thư mục này phụ trách tầng giao tiếp tương tác của hệ thống quản trị, tách rời (decouple) logic backend với phần hiển thị frontend, và được thiết kế theo chuẩn RESTful.

# Mô tả cấu trúc thư mục adminapi

## Cấu trúc thư mục

```
.
├── config/                  # Thư mục cấu hình
├── controller/              # Thư mục controller
├── lang/                    # Thư mục gói ngôn ngữ
├── middleware/              # Thư mục middleware
├── route/                   # Thư mục cấu hình route
├── validate/                # Thư mục validator
├── AdminApiExceptionHandle.php # Trình xử lý ngoại lệ
├── common.php               # Phương thức dùng chung
├── event.php                # Cấu hình sự kiện
└── provider.php             # Service provider
```

## Mô tả thư mục

- **config/** - Cấu hình dành riêng cho trang quản trị
- **controller/** - Controller của trang quản trị, xử lý logic nghiệp vụ phía quản trị
- **lang/** - Tệp đa ngôn ngữ của trang quản trị
- **middleware/** - Middleware của trang quản trị, như xác thực quyền, ghi log, v.v.
- **route/** - Cấu hình route của trang quản trị
- **validate/** - Validator dữ liệu của trang quản trị

## Mô tả chức năng

Mô-đun adminapi chuyên xử lý các API của hệ thống quản trị, bao gồm:
- Quản lý quyền người dùng
- Quản lý sản phẩm
- Xử lý đơn hàng
- Thống kê dữ liệu
- Cài đặt hệ thống và các chức năng quản trị khác