# Mô tả kiến trúc dự án CRMEB

## 🏗️ Tổng quan kiến trúc

### Công nghệ sử dụng
```
Frontend: Vue.js 2 + ElementUI + UniApp
Backend: ThinkPHP 6.0 + chế độ đa ứng dụng
Cơ sở dữ liệu: MySQL 5.7+
Bộ nhớ đệm: Redis
Hàng đợi tin nhắn: Workerman
```

### Mô hình kiến trúc
- **Mô hình MVC**: Kiến trúc phân lớp Model-View-Controller
- **Kiến trúc ba lớp**: Controller → Service → DAO/Model
- **Tách biệt front-end và back-end**: API và các trang front-end được triển khai độc lập
- **Hỗ trợ đa nền tảng**: Hỗ trợ Mini Program, H5, APP, PC

## 📂 Cấu trúc thư mục dự án

```
CRMEB/
├── app/                          # Thư mục lõi của ứng dụng
│   ├── adminapi/                 # Trang quản trị API
│   │   ├── config/               # Tệp cấu hình (route, middleware, v.v.)
│   │   ├── controller/           # Tầng controller
│   │   │   ├── v1/               # API Phiên bản 1
│   │   │   │   ├── agent/        # Liên quan đến đại lý
│   │   │   │   ├── marketing/    # Mô-đun marketing
│   │   │   │   ├── order/        # Mô-đun đơn hàng
│   │   │   │   ├── product/      # Mô-đun sản phẩm
│   │   │   │   ├── user/         # Mô-đun người dùng
│   │   │   │   ├── ...           # Các module chức năng khác
│   │   │   └── AuthController.php # Controller xác thực
│   │   ├── middleware/           # Middleware
│   │   │   ├── AdminAuthTokenMiddleware.php  # Xác thực Token trang quản trị
│   │   │   ├── AdminCheckRoleMiddleware.php  # Kiểm tra quyền theo vai trò
│   │   │   └── AdminLogMiddleware.php        # Log thao tác
│   │   ├── validate/             # Tầng xác thực dữ liệu
│   │   │   ├── marketing/        # Validator marketing
│   │   │   ├── order/            # Validator đơn hàng
│   │   │   ├── product/          # Validator sản phẩm
│   │   │   └── user/             # Validator người dùng
│   │   └── route/                # Cấu hình route
│   │       ├── admin.php         # Route trang quản trị
│   │       ├── app.php           # Route frontend
│   │       └── common.php        # Route công khai
│   │
│   ├── api/                      # Người dùng frontend gọi API
│   │   ├── config/               # Tệp cấu hình
│   │   ├── controller/           # Controller
│   │   │   ├── pc/               # PC API
│   │   │   └── v1/               # API cho thiết bị di động
│   │   └── middleware/           # Middleware
│   │
│   ├── services/                 # Tầng service nghiệp vụ
│   │   ├── user/                 # Service người dùng
│   │   ├── order/                # Service đơn hàng
│   │   ├── product/              # Dịch vụ sản phẩm
│   │   ├── payment/              # Service thanh toán
│   │   └── ...
│   │
│   ├── dao/                      # Tầng truy cập dữ liệu
│   │   ├── UserDao.php
│   │   ├── OrderDao.php
│   │   └── ...
│   │
│   └── model/                    # Tầng mô hình dữ liệu
│       ├── User.php
│       ├── Order.php
│       └── ...
│
├── crmeb/                        # Thư mục thư viện lõi
│   ├── basic/                    # Thư viện lớp cơ sở
│   │   ├── BaseController.php
│   │   ├── BaseServices.php
│   │   └── BaseDao.php
│   │
│   ├── services/                 # Thành phần dịch vụ cốt lõi
│   │   ├── WechatServices.php    # Dịch vụ WeChat
│   │   ├── SmsServices.php       # Dịch vụ SMS
│   │   ├── UploadServices.php    # Dịch vụ tải lên tệp
│   │   ├── CacheServices.php     # Dịch vụ bộ nhớ đệm
│   │   └── ...
│   │
│   ├── utils/                    # Công cụ tiện ích
│   │   ├── Http.php              # HTTP (công cụ request)
│   │   ├── Time.php              # Công cụ xử lý thời gian
│   │   ├── Arr.php               # Công cụ xử lý mảng
│   │   └── ...
│   │
│   └── jobs/                     # Tác vụ hàng đợi
│
├── config/                       # Cấu hình hệ thống
│   ├── app.php                   # Cấu hình ứng dụng
│   ├── cache.php                 # Cấu hình bộ nhớ đệm
│   ├── database.php              # Cấu hình cơ sở dữ liệu
│   ├── filesystem.php            # Cấu hình lưu trữ tệp
│   └── route.php                 # Cấu hình route
│
├── database/                     # Liên quan đến cơ sở dữ liệu
│   ├── migrations/               # Migration cơ sở dữ liệu
│   └── seeds/                    # Seed dữ liệu
│
├── public/                       # WEB (thư mục điểm vào)
│   ├── index.php                 # File điểm vào
│   ├── router.php                # Tệp route
│   ├── .htaccess                 # Apache (rewrite URL giả tĩnh)
│   └── uploads/                  # Thư mục tệp tải lên
│
├── extend/                       # Thư viện lớp mở rộng
│
├── runtime/                      # Thư mục runtime
│   ├── cache/                    # Tệp bộ nhớ đệm
│   ├── log/                      # Tệp log
│   └── temp/                     # Tệp tạm
│
├── docs/                         # Tài liệu dự án
├── tests/                        # File kiểm thử
└── composer.json                 # cấu hình phụ thuộc
```

## 🔄 Quy trình xử lý yêu cầu

```
┌─────────────────────────────────────────────────────────────────┐
│                        Request từ client                                │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                     Nginx / Apache                              │
│                         Chuyển tiếp route                                 │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                      public/index.php                           │
│                      Tệp điểm vào của ứng dụng                                │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                     Middleware ứng dụng (Middleware)                      │
│  1. Middleware toàn cục                                                 │
│  2. Middleware route                                                 │
│  3. Middleware controller                                               │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                     Điều phối route (Route)                             │
│  Ánh xạ tới phương thức controller tương ứng dựa theo URL                                   │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                   Tầng controller (Controller)                          │
│  1. Nhận tham số và xác thực sơ bộ                                          │
│  2. Gọi tầng service để xử lý nghiệp vụ                                          │
│  3. Xử lý kết quả phản hồi                                               │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                   Tầng service (Services)                              │
│  1. Xử lý logic nghiệp vụ                                               │
│  2. Lắp ghép và chuyển đổi dữ liệu                                             │
│  3. Gọi tầng DAO để thao tác dữ liệu                                        │
│  4. Gọi dịch vụ bên ngoài                                               │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                   Tầng truy cập dữ liệu (DAO)                               │
│  1. Thao tác CRUD cơ bản                                             │
│  2. Đóng gói truy vấn phức tạp                                              │
│  3. Xử lý truy vấn liên kết                                               │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                   Tầng mô hình dữ liệu (Model)                             │
│  1. Ánh xạ bảng dữ liệu                                                 │
│  2. Định nghĩa liên kết trường                                               │
│  3. Xử lý sự kiện model                                               │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                       MySQL Cơ sở dữ liệu                              │
└─────────────────────────────────────────────────────────────────┘
```

## 🔐 Cơ chế xác thực và phân quyền

### Xác thực JWT Token
```
┌──────────────┐    Request kèm theo Token     ┌──────────────┐
│   Ứng dụng frontend    │ ──────────────────→  │   Backend API    │
└──────────────┘                       └──────────────┘
                                            ↓
                                    ┌──────────────┐
                                    │ Phân tích Token   │
                                    │ Kiểm tra hiệu lực   │
                                    └──────────────┘
                                            ↓
                                    ┌──────────────┐
                                    │ Lấy user ID  │
                                    │ Gắn vào request     │
                                    └──────────────┘
```

### Kiểm soát quyền
- **Mô hình phân quyền RBAC**: Người dùng → Vai trò → Quyền
- **Quyền menu**: Kiểm soát việc hiển thị menu trang quản trị
- **Quyền nút bấm**: Kiểm soát việc hiển thị các nút thao tác
- **Quyền dữ liệu**: Kiểm soát phạm vi truy cập dữ liệu

## 📊 Luồng dữ liệu

### Luồng dữ liệu đơn hàng
```
Người dùng đặt hàng → Tạo đơn hàng → Tạm trừ tồn kho → Xử lý thanh toán → Thanh toán thành công
                                              ↓
              Trừ tồn kho thực tế ← Xử lý giao hàng ← Giao đơn hàng
                                              ↓
              Hoàn thành đơn hàng ← Xác nhận đã nhận hàng ← Cập nhật vận chuyển
```

### Luồng dữ liệu người dùng
```
Đăng ký → Hoàn thiện thông tin → Nâng hạng → Nhận điểm thưởng → Khấu trừ khi mua hàng
                                      ↓
                               Ghi nhận biến động tài khoản
```

## 🔧 Các thành phần dịch vụ cốt lõi

### Dịch vụ WeChat (WechatServices)
- Phát triển OA WeChat
- Phát triển Mini Program
- WeChat Pay
- Đẩy tin nhắn (push)

### Dịch vụ SMS (SmsServices)
- Gửi mã xác thực
- Tin nhắn thông báo
- SMS marketing

### Dịch vụ tải lên tệp (UploadServices)
- Lưu trữ cục bộ
- Lưu trữ đám mây (Alibaba Cloud, Qiniu Cloud, Tencent Cloud)
- Xử lý ảnh

### Dịch vụ bộ nhớ đệm (CacheServices)
- Bộ nhớ đệm phân tán
- Bộ nhớ đệm trang
- Bộ nhớ đệm dữ liệu

## ?? Hàng đợi tin nhắn và tác vụ định kỳ

### Hàng đợi tin nhắn (Workerman)
- Xử lý đơn hàng quá hạn
- Gửi thông báo bất đồng bộ
- Xử lý thống kê dữ liệu
- Thao tác dữ liệu hàng loạt

### Tác vụ định kỳ (Timer)
- Tự động hủy đơn hàng
- Tổng hợp dữ liệu thống kê
- Tự động dọn dẹp bộ nhớ đệm
- Xử lý dữ liệu hết hạn

## 📈 Tối ưu hiệu năng

### Chiến lược bộ nhớ đệm
- **Bộ nhớ đệm API**: Lưu đệm kết quả của các API được gọi thường xuyên
- **Bộ nhớ đệm trang**: Giảm chi phí render trang
- **Bộ nhớ đệm dữ liệu**: Giảm số lần truy vấn cơ sở dữ liệu

### Tối ưu cơ sở dữ liệu
- **Tách đọc/ghi**: Sao chép master-slave, đọc từ slave, ghi vào master
- **Tối ưu chỉ mục**: Thiết kế chỉ mục hợp lý
- **Phân tích truy vấn chậm**: Xác định điểm nghẽn hiệu năng

### Tối ưu mã nguồn
- **Lazy load**: Tải dữ liệu khi cần
- **Tải trước (preload)**: Tải sẵn dữ liệu sẽ dùng tiếp theo
- **Xử lý hàng loạt**: Giảm các thao tác lặp

## 🛡️ Cơ chế bảo mật

### Bảo mật API
- **Lọc tham số**: Chống SQL injection
- **Giới hạn tần suất**: Chống các yêu cầu độc hại
- **Xác minh chữ ký**: Chống giả mạo dữ liệu

### Bảo mật dữ liệu
- **Mã hóa dữ liệu nhạy cảm**: Mật khẩu, thông tin thanh toán được mã hóa khi lưu trữ
- **Ghi nhật ký**: Ghi lại toàn bộ quá trình thao tác
- **Sao lưu và khôi phục**: Sao lưu dữ liệu định kỳ

## 📱 Hỗ trợ đa nền tảng

```
┌─────────────┐
│  WeChat Mini Program   │ ← UniApp biên dịch
├─────────────┤
│    H5 (trang web)   │ ← UniApp biên dịch
├─────────────┤
│   APP      │ ← UniApp biên dịch（iOS/Android）
├─────────────┤
│   PC client     │ ← Nuxt.js + Element
└─────────────┘
        ↓
   Dùng chung backend API
```

## 🔗 Quan hệ phụ thuộc giữa các module

```
Mô-đun người dùng (user)
    ├── Quản lý thông tin người dùng
    ├── Quản lý địa chỉ người dùng
    ├── Quản lý nhãn người dùng
    └── Quản lý hạng người dùng
         ↓
Mô-đun sản phẩm (product)
    ├── Quản lý sản phẩm
    ├── Quản lý danh mục
    ├── Quản lý thuộc tính
    └── Quản lý đánh giá
         ↓
Mô-đun đơn hàng (order)
    ├── Tạo đơn hàng
    ├── Thanh toán đơn hàng
    ├── Giao đơn hàng
    └── Hậu mãi đơn hàng
         ↓
Mô-đun marketing (marketing)
    ├── Quản lý phiếu giảm giá
    ├── Quản lý chương trình
    ├── Quản lý tiếp thị liên kết
    └── Quản lý điểm thưởng
```

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
