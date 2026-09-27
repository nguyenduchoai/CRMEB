# Tài liệu thư mục dự án CRMEB

## 1. Lời mở đầu
- **Mục đích tài liệu**: Mô tả chi tiết cấu trúc thư mục của dự án CRMEB và chức năng của từng thư mục
- **Phạm vi áp dụng**: Lập trình viên dự án, nhân viên bảo trì, thành viên mới
- **Định nghĩa thuật ngữ**: Không có thuật ngữ đặc biệt

## 2. Cấu trúc thư mục gốc của dự án

### 2.1 Cây thư mục

```
CRMEB/
├── .comate/            # Comate (cấu hình công cụ)
├── .env                # Tệp cấu hình môi trường
├── .git/               # Git Quản lý phiên bản
├── .gitignore          # Git File bỏ qua
├── .idea/              # IDEA (cấu hình trình soạn thảo)
├── .trae/              # Trae (cấu hình công cụ)
│   ├── rules/          # Cấu hình quy tắc
│   └── skills/         # Cấu hình skill
├── crmeb/              # Thư mục ứng dụng cốt lõi
├── docker-compose/     # Docker Cấu hình
├── docs/               # Thư mục tài liệu
├── docs.zip            # Gói nén tài liệu
├── LICENSE             # File giấy phép
├── readme/             # Tài liệu hướng dẫn
├── README.md           # Giới thiệu dự án
├── template/           # Tệp mẫu
└── HUONG_DAN_CAI_DAT.docx       # Hướng dẫn cài đặt
```

### 2.2 Mô tả thư mục gốc
- **.trae/**: Thư mục cấu hình công cụ Trae, chứa cấu hình rule và skill
- **crmeb/**: Thư mục ứng dụng cốt lõi của dự án, chứa toàn bộ logic nghiệp vụ
- **docker-compose/**: Cấu hình container Docker, dùng để triển khai nhanh
- **docs/**: Thư mục tài liệu dự án, chứa các loại tài liệu hướng dẫn
- **readme/**: Tài liệu giới thiệu dự án, bao gồm phần giới thiệu tính năng và ảnh chụp màn hình
- **template/**: Thư mục tệp mẫu (template), dùng để sinh code và cấu hình

## 3. Cấu trúc thư mục ứng dụng cốt lõi (crmeb/)

### 3.1 Cây thư mục

```
crmeb/
├── .constant           # Định nghĩa hằng số
├── .env                # Cấu hình môi trường ứng dụng
├── .htaccess           # Apache Cấu hình
├── .phpstorm.meta.php  # PHPStorm - siêu dữ liệu (metadata)
├── .travis.yml         # Travis CI Cấu hình
├── .version            # Thông tin phiên bản
├── app/                # Thư mục lõi của ứng dụng
├── backup/             # Thư mục sao lưu
├── build.example.php   # Ví dụ cấu hình build
├── composer.json       # Composer cấu hình phụ thuộc
├── composer.lock       # Composer (khóa phiên bản dependency)
├── config/             # Thư mục file cấu hình
├── crmeb/              # Thư mục thư viện lõi
├── filetree.txt        # Cấu trúc cây tệp
├── index.html          # Tệp điểm vào HTML
├── install.lock        # Khóa cài đặt
├── LICENSE.txt         # File giấy phép
├── public/             # Thư mục tài nguyên tĩnh
├── README.md           # Mô tả ứng dụng
├── route/              # Thư mục cấu hình route
├── runtime/            # Thư mục runtime
├── think               # ThinkPHP công cụ dòng lệnh
├── vendor/             # Thư mục thư viện phụ thuộc
└── workerman.bat       # Workerman (script khởi động)
```

### 3.2 Mô tả các thư mục cốt lõi
- **app/**: Thư mục cốt lõi của ứng dụng, chứa toàn bộ logic nghiệp vụ
- **config/**: Thư mục tệp cấu hình, chứa cấu hình ứng dụng
- **crmeb/**: Thư mục thư viện lõi, cung cấp các chức năng cơ bản
- **public/**: Thư mục tài nguyên tĩnh, chứa các tệp frontend
- **route/**: Thư mục cấu hình route, định nghĩa route URL
- **runtime/**: Thư mục runtime, chứa cache và log
- **vendor/**: Thư mục thư viện phụ thuộc, chứa các dependency do Composer cài đặt

## 4. Cấu trúc thư mục cốt lõi của ứng dụng (app/)

### 4.1 Cây thư mục

```
app/
├── adminapi/           # API trang quản trị
├── api/                # API frontend
├── AppService.php      # Service ứng dụng
├── build.php           # Cấu hình build
├── common.php          # Hàm dùng chung
├── dao/                # Tầng truy cập dữ liệu
├── event.php           # Cấu hình sự kiện
├── ExceptionHandle.php # Xử lý ngoại lệ
├── filetree.txt        # Cấu trúc cây tệp
├── http/               # HTTP (các thành phần liên quan)
├── jobs/               # Tác vụ hàng đợi
├── kefuapi/            # API CSKH
├── lang/               # Gói ngôn ngữ
├── listener/           # Event listener
├── middleware.php      # Cấu hình middleware
├── model/              # Model dữ liệu
├── outapi/             # API bên ngoài
├── provider.php        # Service provider
├── README.md           # Mô tả thư mục
├── Request.php         # Lớp cơ sở request
├── service.php         # Cấu hình dịch vụ
└── services/           # Tầng logic nghiệp vụ
```

### 4.2 Mô tả các thư mục cốt lõi
- **adminapi/**: API của hệ thống quản trị, xử lý các yêu cầu từ trang quản trị
- **api/**: API của ứng dụng frontend, xử lý các yêu cầu từ client
- **dao/**: Tầng truy cập dữ liệu, đóng gói các thao tác cơ sở dữ liệu
- **jobs/**: Tác vụ hàng đợi, xử lý các thao tác bất đồng bộ
- **kefuapi/**: API của hệ thống chăm sóc khách hàng (CSKH)
- **model/**: Model dữ liệu, định nghĩa cấu trúc dữ liệu
- **outapi/**: API dành cho hệ thống bên ngoài
- **services/**: Tầng logic nghiệp vụ, hiện thực các chức năng nghiệp vụ cốt lõi

## 5. Mô tả các module cốt lõi

### 5.1 Tầng controller (api/, adminapi/, kefuapi/)
- Xử lý yêu cầu HTTP
- Điều phối route
- Xác thực tham số
- Trả về phản hồi

### 5.2 Tầng logic nghiệp vụ (services/)
- Triển khai logic nghiệp vụ cốt lõi
- Gọi tầng truy cập dữ liệu
- Xử lý quy tắc nghiệp vụ

### 5.3 Tầng truy cập dữ liệu (dao/)
- Đóng gói các thao tác cơ sở dữ liệu
- Cung cấp các phương thức truy vấn dữ liệu
- Xử lý lưu trữ dữ liệu bền vững (persistence)

### 5.4 Tầng model dữ liệu (model/)
- Định nghĩa cấu trúc dữ liệu
- Quan hệ liên kết
- Xác thực dữ liệu

### 5.5 Tầng cấu hình (config/)
- Cấu hình ứng dụng
- Cấu hình cơ sở dữ liệu
- Cấu hình bộ nhớ đệm
- Cấu hình log

## 6. Thư mục tài nguyên tĩnh (public/)
- **css/**: Tệp style
- **js/**: Tệp JavaScript
- **images/**: Tệp hình ảnh
- **fonts/**: Tệp phông chữ
- **uploads/**: Tệp tải lên

## 7. Mô tả tệp cấu hình

### 7.1 Tệp cấu hình cốt lõi
- **.env**: Cấu hình biến môi trường
- **config/app.php**: Cấu hình ứng dụng
- **config/database.php**: Cấu hình cơ sở dữ liệu
- **config/cache.php**: Cấu hình bộ nhớ đệm (cache)

### 7.2 Cấu hình route
- **route/api.php**: Route API
- **route/adminapi.php**: Route API trang quản trị
- **route/kefuapi.php**: Route API CSKH

## 8. Mô tả các tệp cốt lõi

### 8.1 Tệp điểm vào
- **public/index.php**: Tệp điểm vào của ứng dụng
- **think**: Công cụ dòng lệnh

### 8.2 Lớp cơ sở
- **app/Request.php**: Lớp cơ sở của request
- **app/ExceptionHandle.php**: Lớp xử lý ngoại lệ
- **app/AppService.php**: Lớp service ứng dụng

### 8.3 Tệp cấu hình
- **composer.json**: Quản lý phụ thuộc
- **.htaccess**: Quy tắc rewrite của Apache

## 9. Khuyến nghị sử dụng thư mục

### 9.1 Quy chuẩn phát triển
- **Thêm tệp mới**: Đặt theo cấu trúc thư mục của module tương ứng
- **Sửa tệp**: Giữ nguyên cấu trúc thư mục ban đầu
- **Xóa tệp**: Xóa sau khi xác nhận không còn phụ thuộc

### 9.2 Quy tắc đặt tên
- **Tên thư mục**: Từ viết thường, không có ký tự đặc biệt
- **Tên tệp**: Đặt tên kiểu camelCase hoặc chữ thường kèm dấu gạch dưới
- **Tên lớp**: PascalCase
- **Tên phương thức**: camelCase

## 10. Tổng kết

### 10.1 Đặc điểm cấu trúc thư mục
- **Phân tầng rõ ràng**: Tuân theo kiến trúc MVC, phân tầng rõ ràng
- **Tách biệt module**: Chia thư mục theo module chức năng
- **Cấu hình tập trung**: Quản lý tệp cấu hình thống nhất
- **Tách biệt tài nguyên**: Tài nguyên tĩnh được lưu riêng

### 10.2 Kế hoạch tiếp theo
- Định kỳ cập nhật tài liệu cấu trúc thư mục
- Điều chỉnh cấu trúc thư mục theo sự phát triển của dự án
- Duy trì tính hợp lý và khả năng bảo trì của cấu trúc thư mục

---

Phiên bản: 1.0
Tác giả: Hệ thống tự động tạo
Ngày cập nhật: 2026-01-23
---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
