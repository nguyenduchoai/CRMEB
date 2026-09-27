# Tài liệu cấu trúc thư mục

## 1. Tổng quan

Tài liệu này mô tả cấu trúc thư mục của dự án CRMEB, bao gồm chức năng của từng thư mục, cách tổ chức tệp, v.v., nhằm giúp lập trình viên hiểu cấu trúc dự án và nâng cao hiệu quả phát triển.

## 2. Cấu trúc thư mục gốc của dự án

```
CRMEB/
├── app/                  # Thư mục ứng dụng
├── config/               # Thư mục cấu hình
├── crmeb/                # Thư mục thư viện lõi
├── database/             # Thư mục cơ sở dữ liệu
├── extend/               # Thư mục mở rộng
├── public/               # Thư mục tài nguyên công khai
├── runtime/              # Thư mục runtime
├── thinkphp/             # ThinkPHP thư mục lõi
├── vendor/               # Thư mục phụ thuộc bên thứ ba
├── .env                  # File biến môi trường
├── .env.example          # File mẫu biến môi trường
├── composer.json         # Composer Tệp cấu hình
├── composer.lock         # Composer file lock
├── LICENSE               # File giấy phép
├── README.md             # Tài liệu giới thiệu dự án
├── think                 # ThinkPHP công cụ dòng lệnh
```

## 3. Cấu trúc thư mục ứng dụng (app/)

```
app/
├── api/                  # API tầng giao tiếp
│   ├── v1/               # API Phiên bản 1
│   ├── v2/               # API Phiên bản 2
│   └── BaseApi.php       # API lớp cơ sở
├── controller/           # Tầng controller
│   ├── admin/            # Controller trang quản trị
│   ├── api/              # API Controller
│   └── BaseController.php # Lớp cơ sở của controller
├── dao/                  # Tầng truy cập dữ liệu
│   └── BaseDao.php       # DAO lớp cơ sở
├── event/                # Tầng sự kiện
├── exception/            # Tầng xử lý ngoại lệ
├── middleware/           # Tầng middleware
├── model/                # Tầng model
│   └── BaseModel.php     # Lớp cơ sở model
├── services/             # Tầng logic nghiệp vụ
│   └── BaseServices.php  # Lớp cơ sở của service
├── subscribe/            # Tầng đăng ký sự kiện
├── validate/             # Tầng xác thực
│   └── BaseValidate.php  # Lớp cơ sở của validator
└── common.php            # File hàm dùng chung
```

### 3.1 Thư mục API (app/api/)

- **Chức năng**: Xử lý các yêu cầu API
- **Cấu trúc**: Chia thư mục theo phiên bản API
- **Lớp cơ sở**: `BaseApi.php`, cung cấp các chức năng API cơ bản
- **Quản lý phiên bản**: Quản lý phiên bản API thông qua cấu trúc thư mục

### 3.2 Thư mục controller (app/controller/)

- **Chức năng**: Xử lý yêu cầu HTTP, điều phối route
- **Cấu trúc**: Chia controller theo module
- **Lớp cơ sở**: `BaseController.php`, cung cấp các chức năng cơ bản cho controller
- **Loại**: Controller phía quản trị, controller API

### 3.3 Thư mục DAO (app/dao/)

- **Chức năng**: Đối tượng truy cập dữ liệu (Data Access Object), đóng gói các thao tác cơ sở dữ liệu
- **Cấu trúc**: Chia các lớp DAO theo module nghiệp vụ
- **Lớp cơ sở**: `BaseDao.php`, cung cấp các chức năng DAO cơ bản
- **Trách nhiệm**: Xử lý các thao tác cơ sở dữ liệu như truy vấn, thêm, cập nhật, xóa

### 3.4 Thư mục model (app/model/)

- **Chức năng**: Mô hình dữ liệu, định nghĩa cấu trúc dữ liệu và quan hệ
- **Cấu trúc**: Chia các lớp model theo module nghiệp vụ
- **Lớp cơ sở**: `BaseModel.php`, cung cấp các chức năng cơ bản cho model
- **Đặc điểm**: Hỗ trợ ORM, truy vấn liên kết, xóa mềm, v.v.

### 3.5 Thư mục service (app/services/)

- **Chức năng**: Tầng logic nghiệp vụ, đóng gói logic nghiệp vụ cốt lõi
- **Cấu trúc**: Chia các lớp service theo module nghiệp vụ
- **Lớp cơ sở**: `BaseServices.php`, cung cấp các chức năng cơ bản cho service
- **Trách nhiệm**: Hiện thực các quy tắc nghiệp vụ, điều phối nhiều DAO và model

### 3.6 Thư mục validate (app/validate/)

- **Chức năng**: Kiểm tra dữ liệu, kiểm tra tính hợp lệ của tham số yêu cầu
- **Cấu trúc**: Chia các lớp validator theo module nghiệp vụ
- **Lớp cơ sở**: `BaseValidate.php`, cung cấp các chức năng cơ bản cho validator
- **Đặc điểm**: Hỗ trợ kiểm tra theo quy tắc (rule), kiểm tra theo tình huống (scene), v.v.

## 4. Cấu trúc thư mục cấu hình (config/)

```
config/
├── app.php               # Cấu hình ứng dụng
├── cache.php             # Cấu hình bộ nhớ đệm
├── captcha.php           # Cấu hình captcha
├── console.php           # Cấu hình console
├── cookie.php            # Cookie Cấu hình
├── database.php          # Cấu hình cơ sở dữ liệu
├── filesystem.php        # Cấu hình hệ thống file
├── lang.php              # Cấu hình ngôn ngữ
├── log.php               # Cấu hình log
├── queue.php             # Cấu hình hàng đợi
├── route.php             # Cấu hình route
├── session.php           # Session Cấu hình
├── template.php          # Cấu hình template
└── trace.php             # Cấu hình debug
```

- **Chức năng**: Lưu trữ tất cả tệp cấu hình của dự án
- **Cấu trúc**: Chia tệp cấu hình theo module chức năng
- **Đặc điểm**: Hỗ trợ cấu hình qua biến môi trường, bộ nhớ đệm cấu hình, v.v.
- **Thứ tự nạp**: Cấu hình framework → Cấu hình ứng dụng → Cấu hình môi trường

## 5. Cấu trúc thư mục thư viện lõi (crmeb/)

```
crmeb/
├── basic/                # Thư viện lớp cơ sở
├── exception/            # Lớp ngoại lệ lõi
├── services/             # Lớp service lõi
├── utils/                # Công cụ tiện ích
└── version.php           # Thông tin phiên bản
```

- **Chức năng**: Lưu trữ mã nguồn thư viện lõi của CRMEB
- **Cấu trúc**: Chia thư mục theo module chức năng
- **Đặc điểm**: Độc lập với mã ứng dụng, thuận tiện cho việc bảo trì và nâng cấp
- **Vai trò**: Cung cấp các chức năng cốt lõi và dịch vụ nền tảng

## 6. Cấu trúc thư mục cơ sở dữ liệu (database/)

```
database/
├── migrations/           # File migration cơ sở dữ liệu
└── seeders/              # File seed cơ sở dữ liệu
```

- **Chức năng**: Lưu trữ các tệp liên quan đến cơ sở dữ liệu
- **Tệp migration**: Dùng để quản lý phiên bản cấu trúc cơ sở dữ liệu
- **Tệp seed**: Dùng để khởi tạo dữ liệu cho cơ sở dữ liệu
- **Công cụ**: Quản lý bằng công cụ migration của ThinkPHP

## 7. Cấu trúc thư mục tài nguyên công khai (public/)

```
public/
├── admin/                # Tài nguyên trang quản trị
├── api/                  # API tài nguyên
├── assets/               # Tài nguyên tĩnh
│   ├── css/              # CSS Tệp
│   ├── images/           # File hình ảnh
│   └── js/               # JavaScript Tệp
├── index.php             # Tệp điểm vào của ứng dụng
├── robots.txt            # File giao thức robot
└── router.php            # URL file rewrite
```

- **Chức năng**: Lưu trữ các tài nguyên công khai có thể truy cập trực tiếp
- **Cấu trúc**: Chia thư mục theo module chức năng
- **Đặc điểm**: Có thể truy cập trực tiếp qua URL
- **Bảo mật**: Không nên đặt tệp nhạy cảm trong thư mục này

## 8. Cấu trúc thư mục runtime (runtime/)

```
runtime/
├── cache/                # Thư mục bộ nhớ đệm (cache)
├── log/                  # Thư mục log
│   └── app/              # Log ứng dụng
├── session/              # Session Thư mục
├── temp/                 # Thư mục file tạm
└── think/                # ThinkPHP Thư mục runtime
```

- **Chức năng**: Lưu trữ các tệp được tạo ra trong lúc chạy
- **Cấu trúc**: Chia thư mục theo module chức năng
- **Đặc điểm**: Tự động tạo, không cần bảo trì thủ công
- **Quyền**: Cần có quyền ghi

## 9. Cấu trúc thư mục lõi (thinkphp/)

```
thinkphp/
├── lang/                 # Thư mục gói ngôn ngữ
├── library/              # Thư mục thư viện lớp lõi
├── tpl/                  # Thư mục template
├── base.php              # File định nghĩa cơ sở
├── composer.json         # Composer Tệp cấu hình
└── helper.php            # File hàm helper
```

- **Chức năng**: Lưu trữ mã nguồn lõi của framework ThinkPHP
- **Cấu trúc**: Cấu trúc mặc định của framework
- **Đặc điểm**: Độc lập với mã ứng dụng
- **Nâng cấp**: Nâng cấp thông qua Composer

## 10. Cấu trúc thư mục thư viện bên thứ ba (vendor/)

```
vendor/
├── autoload.php          # File autoload
├── composer/             # Composer thư mục lõi
├── symfony/              # Symfony Thành phần
├── topthink/             # ThinkPHP Thành phần
└── ...                   # Các phụ thuộc bên thứ ba khác
```

- **Chức năng**: Lưu trữ các thư viện phụ thuộc bên thứ ba
- **Quản lý**: Quản lý thông qua Composer
- **Cấu trúc**: Chia thư mục theo tên gói phụ thuộc
- **Tự động nạp**: Tự động nạp thông qua `autoload.php`

## 11. Thực tiễn tốt nhất cho cấu trúc thư mục

### 11.1 Quy tắc đặt tên

- **Tên thư mục**: Chữ thường, các từ phân cách bằng dấu gạch dưới
- **Tên tệp**: Trùng với tên lớp, dùng kiểu đặt tên PascalCase
- **Tên lớp**: Dùng kiểu đặt tên PascalCase
- **Tên phương thức**: Dùng kiểu đặt tên camelCase
- **Tên biến**: Dùng kiểu đặt tên camelCase

### 11.2 Nguyên tắc tổ chức

- **Module hóa**: Tổ chức cấu trúc thư mục theo module chức năng
- **Kiến trúc phân lớp**: Tuân theo kiến trúc phân lớp MVC + Service + DAO
- **Đơn trách nhiệm**: Mỗi thư mục và tệp chỉ đảm nhận một chức năng
- **Khả năng mở rộng**: Dễ dàng thêm chức năng và module mới
- **Dễ bảo trì**: Dễ hiểu và dễ bảo trì

### 11.3 Khuyến nghị khi phát triển

- **Tuân thủ quy chuẩn framework**: Tuân thủ quy chuẩn cấu trúc thư mục của framework ThinkPHP
- **Chia module hợp lý**: Chia module hợp lý theo chức năng nghiệp vụ
- **Tránh thư mục lồng quá sâu**: Cấp thư mục không nên quá sâu, thường không quá 4 cấp
- **Giữ thư mục gọn gàng**: Kịp thời dọn dẹp các tệp và thư mục không dùng đến
- **Tài liệu hóa**: Bổ sung tài liệu mô tả cho các thư mục quan trọng

## 12. Sự cố thường gặp

### 12.1 Vấn đề quyền thư mục

- **Vấn đề**: Thư mục runtime không có quyền ghi
- **Giải pháp**: Chạy `chmod -R 777 runtime/` để cấp quyền ghi

### 12.2 Vấn đề tự động nạp (autoload)

- **Vấn đề**: Lớp mới thêm không thể tự động nạp
- **Giải pháp**: Chạy `composer dump-autoload` để cập nhật autoload

### 12.3 Tệp cấu hình không có hiệu lực

- **Vấn đề**: Sửa tệp cấu hình nhưng không có hiệu lực
- **Giải pháp**: Xóa bộ nhớ đệm cấu hình, chạy `php think clear`

### 12.4 Cấu trúc thư mục lộn xộn

- **Vấn đề**: Cấu trúc thư mục không rõ ràng, khó bảo trì
- **Giải pháp**: Tổ chức lại cấu trúc thư mục, tuân theo nguyên tắc module hóa

## 13. Tài liệu tham khảo

- [Cấu trúc thư mục ThinkPHP 6](https://www.kancloud.cn/manual/thinkphp6_0/1037487)
- [Thiết kế kiến trúc MVC](https://zh.wikipedia.org/wiki/MVC)
- [Thiết kế kiến trúc phân lớp](https://en.wikipedia.org/wiki/Multitier_architecture)
- [Thiết kế module hóa](https://en.wikipedia.org/wiki/Modular_design)