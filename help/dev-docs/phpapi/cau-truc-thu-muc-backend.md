# Mô tả cấu trúc thư mục code backend CRMEB

## Tổng quan dự án

CRMEB là hệ thống thương mại điện tử được phát triển trên framework ThinkPHP 6.x, áp dụng mô hình kiến trúc đa ứng dụng, hỗ trợ nhiều module ứng dụng như trang quản trị, API phía người dùng, hệ thống CSKH, v.v.

## Kiến trúc cốt lõi

### Mô hình kiến trúc
- **Framework**: ThinkPHP 6.x
- **Mô hình kiến trúc**: Kiến trúc đa ứng dụng (Multi-App)
- **Mẫu thiết kế**: Kiến trúc phân tầng MVC + Service + DAO
- **Tiêm phụ thuộc (DI)**: Quản lý bằng container
- **Cơ sở dữ liệu**: MySQL (hỗ trợ tách master-slave)

### Tổng quan cấu trúc thư mục

```
crmeb/
├── app/                          # Thư mục ứng dụng
│   ├── adminapi/                 # Ứng dụng API trang quản trị
│   ├── api/                      # Ứng dụng API phía người dùng
│   ├── kefuapi/                  # Ứng dụng API hệ thống CSKH
│   ├── outapi/                   # Ứng dụng API bên ngoài
│   ├── dao/                      # Tầng truy cập dữ liệu (DAO)
│   ├── http/                     # Các lớp liên quan đến HTTP
│   ├── jobs/                     # Tác vụ hàng đợi
│   ├── lang/                     # Gói đa ngôn ngữ
│   ├── listener/                 # Event listener
│   ├── model/                    # Tầng mô hình dữ liệu
│   └── services/                 # Tầng service logic nghiệp vụ
├── config/                       # Thư mục file cấu hình
├── crmeb/                        # Phần mở rộng framework lõi
├── public/                       # Thư mục tài nguyên công khai
├── route/                        # Định nghĩa route
├── runtime/                      # File runtime
└── vendor/                       # Phụ thuộc bên thứ ba
```

## Mô tả chi tiết thư mục

### 1. Thư mục ứng dụng (app/)

#### 1.1 API trang quản trị (app/adminapi/)
```
adminapi/
├── config/route.php              # Cấu hình route trang quản trị
├── controller/                   # Tầng controller
│   ├── v1/                      # Controller API phiên bản 1
│   │   ├── agent/               # Quản lý đại lý
│   │   ├── application/         # Cấu hình ứng dụng
│   │   ├── cms/                 # Quản lý nội dung
│   │   ├── diy/                 # Thiết kế giao diện DIY
│   │   ├── export/              # Chức năng xuất
│   │   ├── file/                # Quản lý tệp
│   │   ├── finance/             # Quản lý tài chính
│   │   ├── freight/             # Quản lý vận chuyển
│   │   ├── kefu/                # Quản lý CSKH
│   │   ├── marketing/           # Hoạt động marketing
│   │   ├── merchant/            # Quản lý cửa hàng (merchant)
│   │   ├── notification/        # Quản lý thông báo
│   │   ├── order/               # Quản lý đơn hàng
│   │   ├── product/             # Quản lý sản phẩm
│   │   ├── serve/               # Service hệ thống
│   │   ├── setting/             # Cài đặt hệ thống
│   │   ├── statistic/           # Thống kê phân tích
│   │   ├── system/              # Quản lý hệ thống
│   │   └── user/                # Quản lý người dùng
│   ├── AuthController.php       # Lớp cơ sở của controller phân quyền
│   ├── Common.php               # Controller dùng chung
│   ├── Login.php               # Controller đăng nhập
│   └── PublicController.php    # Controller dùng chung
├── middleware/                   # Middleware
│   ├── AdminAuthTokenMiddleware.php    # Xác thực Token quản trị viên
│   ├── AdminCheckRoleMiddleware.php   # Kiểm tra quyền
│   ├── AdminEditorTokenMiddleware.php # Xác thực Token trình soạn thảo
│   └── AdminLogMiddleware.php          # Middleware ghi log thao tác
├── route/                       # Định nghĩa route
│   ├── agent.php               # Route đại lý
│   ├── app.php                 # Route ứng dụng
│   ├── cms.php                 # Route quản lý nội dung
│   ├── common.php              # Route dùng chung
│   ├── crud.php                # Route CRUD
│   ├── diy.php                 # Route DIY
│   ├── export.php              # Route xuất dữ liệu
│   ├── file.php                # Route quản lý file
│   ├── finance.php             # Route tài chính
│   ├── freight.php             # Route vận chuyển
│   ├── live.php                # Route livestream
│   ├── marketing.php           # Route marketing
│   ├── merchant.php            # Route cửa hàng
│   ├── notify.php              # Route thông báo
│   ├── order.php               # Route đơn hàng
│   ├── product.php             # Route sản phẩm
│   ├── serve.php               # Route dịch vụ
│   ├── setting.php             # Route cài đặt
│   ├── statistic.php           # Route thống kê
│   ├── system.php              # Route hệ thống
│   ├── user.php                # Route người dùng
│   └── widget.php              # Route thành phần
└── validate/                    # Validator
    ├── marketing/              # Validator marketing
    ├── merchant/               # Validator cửa hàng
    ├── notification/           # Validator thông báo
    ├── order/                  # Validator đơn hàng
    ├── product/                # Validator sản phẩm
    ├── serve/                  # Validator dịch vụ
    ├── service/                # Validator dịch vụ
    ├── setting/                # Validator cài đặt
    └── user/                   # Validator người dùng
```

#### 1.2 API phía người dùng (app/api/)
```
api/
├── config/route.php             # Cấu hình route phía người dùng
├── controller/                  # Tầng controller
│   ├── pc/                     # Controller cho PC
│   ├── v1/                     # API phiên bản 1
│   │   ├── activity/           # Liên quan đến chương trình khuyến mãi
│   │   ├── admin/              # Liên quan đến quản lý
│   │   ├── order/              # Liên quan đến đơn hàng
│   │   ├── publics/            # API chung
│   │   ├── store/              # Liên quan đến cửa hàng
│   │   ├── user/               # Liên quan đến người dùng
│   │   └── wechat/             # Liên quan đến WeChat
│   ├── v2/                     # API phiên bản 2
│   ├── CrontabController.php   # Controller tác vụ định kỳ
│   ├── LoginController.php    # Controller đăng nhập
│   ├── PayController.php       # Bộ điều khiển thanh toán
│   └── PublicController.php   # Controller dùng chung
├── middleware/                  # Middleware
│   ├── AuthTokenMiddleware.php # Middleware xác thực Token
│   ├── BlockerMiddleware.php   # Middleware giới hạn truy cập
│   ├── CustomerMiddleware.php  # Middleware khách hàng
│   └── StationOpenMiddleware.php # Middleware trạng thái mở website
├── route/                      # Định nghĩa route
│   ├── pc.php                  # Route cho PC
│   ├── v1.php                  # API route v1
│   └── v2.php                  # API route v2
└── validate/                   # Validator
    └── user/                   # Validator người dùng
```

#### 1.3 Tầng truy cập dữ liệu (app/dao/)
```
dao/
├── activity/                   # DAO liên quan đến hoạt động
│   ├── advance/               # Hoạt động đặt trước
│   ├── bargain/               # Hoạt động săn giảm giá
│   ├── combination/           # Hoạt động mua chung
│   ├── integral/              # Cửa hàng đổi điểm
│   ├── live/                  # Hoạt động livestream
│   ├── lottery/               # Hoạt động quay thưởng
│   └── seckill/               # Hoạt động flash sale
├── card/                       # DAO thẻ/phiếu
├── community/                  # DAO cộng đồng
├── configure/                  # DAO cấu hình
├── diy/                        # DIY DAO
├── export/                     # DAO xuất dữ liệu
├── log/                        # DAO log
├── message/                    # DAO tin nhắn
├── ota/                        # DAO liên quan đến OTA
├── other/                      # DAO khác
├── pay/                        # DAO thanh toán
├── product/                    # DAO sản phẩm
│   ├── category/              # Danh mục sản phẩm
│   ├── label/                 # Nhãn sản phẩm
│   ├── product/               # Thực thể sản phẩm
│   ├── reply/                 # Đánh giá sản phẩm
│   ├── rule/                  # Mẫu quy tắc
│   ├── sku/                   # Quy cách sản phẩm
│   └── protection/            # Đảm bảo sản phẩm
├── queue/                      # DAO hàng đợi
├── shipping/                   # DAO vận chuyển
├── store/                      # DAO cửa hàng
├── system/                     # DAO hệ thống
├── user/                       # DAO người dùng
│   ├── address/               # Địa chỉ người dùng
│   ├── bill/                  # Giao dịch người dùng
│   ├── feedback/              # Phản hồi người dùng
│   ├── group/                 # Nhóm người dùng
│   ├── label/                 # Nhãn người dùng
│   ├── level/                 # Hạng người dùng
│   ├── member/                # Thẻ thành viên
│   ├── recharge/              # Lịch sử nạp tiền
│   ├── search/                # Lịch sử tìm kiếm
│   ├── spread/                # Quan hệ giới thiệu
│   └── withdraw/              # Lịch sử rút tiền
└── work/                       # DAO liên quan đến công việc
```

#### 1.4 Tầng service (app/services/)
```
services/
├── activity/                   # Service hoạt động
├── admin/                      # Service quản lý
├── agent/                      # Service đại lý
├── app/                        # Service ứng dụng
├── clone/                      # Service clone
├ ├── community/                 # Service cộng đồng
├── crud/                       # Service tạo code CRUD
├ ├── diy/                       # Service DIY
├ ├── export/                    # Service xuất dữ liệu
├ ├── file/                      # Service file
├ ├── goods/                     # Dịch vụ sản phẩm
├ ├── import/                    # Service nhập dữ liệu
├ ├── invoice/                   # Service hóa đơn
├ ├── ka/                        # Service CSKH
├ ├── lang/                      # Service đa ngôn ngữ
├ ├── live/                      # Service livestream
├ ├── message/                   # Service tin nhắn
├ ├── ota/                       # Service OTA
├ ├── other/                     # Service khác
├ ├── pay/                       # Service thanh toán
├ ├── phonestore/                # Service cửa hàng di động
├ ├── product/                   # Dịch vụ sản phẩm
├ ├── protect/                   # Service bảo vệ
├ ├── queue/                     # Service hàng đợi
├ ├── scan/                      # Service quét mã
├ ├── serve/                     # Service dịch vụ
├ ├── setting/                   # Service cài đặt
├ ├── shipping/                  # Dịch vụ vận chuyển
├ ├── stat/                      # Dịch vụ thống kê
├ ├── store/                     # Dịch vụ cửa hàng
├ ├── system/                    # Service hệ thống
├ ├── template/                  # Dịch vụ mẫu (template)
├ ├── user/                      # Service người dùng
├ ├── utils/                     # Dịch vụ tiện ích
└── work/                       # Dịch vụ công việc
```

#### 1.5 Tầng model (app/model/)
```
model/
├── activity/                   # Model hoạt động
├── card/                       # Model thẻ và phiếu
├── community/                  # Model cộng đồng
├── configure/                  # Model cấu hình
├── diy/                        # Model DIY
├── log/                        # Model log
├── message/                    # Model tin nhắn
├── ota/                        # Model OTA
├── other/                      # Model khác
├── pay/                        # Model thanh toán
├── product/                    # Model sản phẩm
├── queue/                      # Model hàng đợi
├── shipping/                   # Model vận chuyển
├── store/                      # Model cửa hàng
├── system/                     # Model hệ thống
├── user/                       # Model người dùng
└── work/                       # Model công việc
```

### 2. Mở rộng framework cốt lõi (crmeb/)

#### 2.1 Lớp cơ sở (crmeb/basic/)
```
basic/
├── BaseController.php          # Lớp cơ sở của controller
├── BaseJobs.php               # Lớp cơ sở của tác vụ
├── BaseManager.php            # Lớp cơ sở trình quản lý
├── BaseModel.php              # Lớp cơ sở model
└── BaseStorage.php            # Lớp cơ sở lưu trữ
```

#### 2.2 Dòng lệnh (crmeb/command/)
```
command/
├── Npm.php                    # Lệnh NPM
├── Timer.php                  # Lệnh timer
├── Util.php                   # Lệnh tiện ích
└── Workerman.php              # Lệnh Workerman
```

#### 2.3 Xử lý ngoại lệ (crmeb/exceptions/)
```
exceptions/
├── AdminException.php         # Ngoại lệ quản trị
├── ApiException.php          # Ngoại lệ API
├── ApiStatusException.php    # Ngoại lệ trạng thái API
├── AuthException.php         # Ngoại lệ xác thực
├── CrudException.php         # Ngoại lệ CRUD
├── PayException.php          # Ngoại lệ thanh toán
└── UploadException.php      # Ngoại lệ tải lên
```

#### 2.4 Định nghĩa interface (crmeb/interfaces/)
```
interfaces/
├── JobInterface.php          # Interface tác vụ
├── ListenerInterface.php     # Interface listener
├── MiddlewareInterface.php   # Interface middleware
└── ProviderInterface.php     # Interface provider
```

#### 2.5 Tầng service (crmeb/services/)
```
services/
├── app/                       # Service ứng dụng
│   ├── MiniProgramService.php  # Dịch vụ Mini Program
│   ├── WechatOpenService.php   # Dịch vụ nền tảng mở WeChat
│   └── WechatService.php        # Dịch vụ WeChat
├── copyproduct/              # Dịch vụ sao chép sản phẩm
├── crud/                     # Service tạo code CRUD
├── easywechat/               # Dịch vụ SDK WeChat
├── express/                  # Dịch vụ chuyển phát
├── invoice/                  # Service hóa đơn
├── oauth/                    # Dịch vụ xác thực OAuth
├── pay/                      # Service thanh toán
├── printer/                  # Dịch vụ in
├── serve/                    # Service hệ thống
├── sms/                      # Dịch vụ SMS
├── template/                 # Dịch vụ tin nhắn mẫu
├── upload/                   # Service tải lên
├── workerman/                # Dịch vụ Workerman
└── Các tệp dịch vụ cốt lõi...
```

#### 2.6 Trait (crmeb/traits/)
```
traits/
├── JwtAuthModelTrait.php     # Trait xác thực JWT
├── ModelTrait.php           # Trait model
└── QueueTrait.php           # Trait hàng đợi
```

#### 2.7 Lớp tiện ích (crmeb/utils/)
```
utils/
├── Arr.php                   # Công cụ mảng
├── Canvas.php               # Công cụ canvas
├── Captcha.php              # Công cụ mã xác thực
├── DownloadImage.php        # Công cụ tải ảnh
├── fileVerification.php     # Công cụ xác thực tệp
├── Hook.php                 # Công cụ hook
├── Json.php                 # Công cụ JSON
├── JwtAuth.php              # Công cụ xác thực JWT
├── Queue.php                # Công cụ hàng đợi
├── Rsa.php                  # Công cụ mã hóa RSA
├── Str.php                  # Công cụ chuỗi
├── Terminal.php             # Công cụ terminal
└── Translate.php            # Công cụ dịch
```

### 3. Tệp cấu hình (config/)

```
config/
├── ajcaptcha.php             # Cấu hình captcha
├── app.php                   # Cấu hình ứng dụng
├── cache.php                 # Cấu hình bộ nhớ đệm
├── captcha.php               # Cấu hình mã xác thực hình ảnh
├── console.php               # Cấu hình console
├── cookie.php                # Cấu hình Cookie
├── database.php              # Cấu hình cơ sở dữ liệu
├── filesystem.php            # Cấu hình hệ thống file
├── lang.php                  # Cấu hình đa ngôn ngữ
├── log.php                   # Cấu hình log
├── pay.php                   # Cấu hình thanh toán
├── plat.php                  # Cấu hình nền tảng
├── printer.php               # Cấu hình in
├── qrcode.php                # Cấu hình mã QR
├── queue.php                 # Cấu hình hàng đợi
├── route.php                 # Cấu hình route
├── session.php               # Cấu hình session
├── sms.php                   # Cấu hình SMS
├── trace.php                 # Cấu hình debug trace
├── upload.php                # Cấu hình tải lên
├── view.php                  # Cấu hình view
└── workerman.php             # Cấu hình Workerman
```

### 4. Tài nguyên công khai (public/)

```
public/
├── admin/                    # Frontend trang quản trị
├── assets/                   # Tài nguyên tĩnh
├── install/                  # Trình cài đặt
├── pages/                    # Tệp trang
├── static/                   # Tệp tĩnh
├── statics/                  # Tệp tĩnh hệ thống
├── upgrade/                  # Nâng cấp chương trình
├── .htaccess                 # Quy tắc rewrite Apache
├── favicon.ico               # Biểu tượng website
├── index.html                # Trang chủ mặc định
└── File điểm vào...
```

### 5. Định nghĩa route (route/)

```
route/
├── README.md                 # Mô tả route
└── route.php                 # File route chính
```

## Mẫu thiết kế kiến trúc

### 1. Kiến trúc phân tầng
- **Tầng Controller**: Xử lý HTTP request, kiểm tra tham số, gọi Service
- **Tầng Service**: Xử lý logic nghiệp vụ, quản lý transaction, gọi DAO
- **Tầng DAO**: Truy cập dữ liệu, thao tác SQL, ánh xạ model
- **Tầng Model**: Định nghĩa model dữ liệu, quan hệ liên kết

### 2. Tiêm phụ thuộc (Dependency Injection)
- Dùng container của ThinkPHP để quản lý phụ thuộc giữa các đối tượng
- Tiêm phụ thuộc Service thông qua hàm khởi tạo (constructor)
- Hỗ trợ lập trình hướng interface và lập trình hướng khía cạnh (AOP)

### 3. Cơ chế middleware
- Middleware xác thực và phân quyền
- Middleware kiểm tra quyền
- Middleware ghi log thao tác
- Middleware lọc tham số

### 4. Lắng nghe sự kiện
- Sự kiện thay đổi trạng thái đơn hàng
- Sự kiện người dùng đăng ký
- Sự kiện hoàn tất thanh toán
- Sự kiện thay đổi tồn kho sản phẩm

## Các mô-đun chức năng cốt lõi

### 1. Quản lý sản phẩm
- Quản lý danh mục, quy cách, thuộc tính sản phẩm
- Quản lý tồn kho, chiến lược giá
- Duyệt sản phẩm, quản lý lên/xuống kệ
- Chức năng nhập/xuất sản phẩm

### 2. Quản lý đơn hàng
- Quy trình tạo đơn hàng, thanh toán, giao hàng
- Quản lý và theo dõi trạng thái đơn hàng
- Xử lý hoàn tiền, hậu mãi
- Thống kê và phân tích đơn hàng

### 3. Quản lý người dùng
- Đăng ký, đăng nhập, xác thực người dùng
- Cấp độ người dùng, hệ thống thành viên
- Quản lý nhãn, nhóm người dùng
- Phân tích hành vi người dùng

### 4. Hoạt động marketing
- Phiếu giảm giá, chương trình giảm giá theo đơn tối thiểu
- Mua chung, săn giảm giá, flash sale
- Cửa hàng đổi điểm, thưởng điểm danh
- Hệ thống giới thiệu và tiếp thị liên kết

### 5. Hệ thống thanh toán
- WeChat Pay, thanh toán Alipay
- Thanh toán bằng số dư, thanh toán bằng điểm thưởng
- Xử lý callback thanh toán
- Chức năng đối soát hoàn tiền

### 6. Vận chuyển và giao hàng
- Tích hợp đơn vị vận chuyển
- Quản lý mẫu phí vận chuyển
- Tra cứu thông tin vận chuyển
- Theo dõi trạng thái giao hàng

## Đặc điểm kỹ thuật

### 1. Kiến trúc đa ứng dụng
- Trang quản trị, phía người dùng và hệ thống CSKH được triển khai độc lập
- Cơ chế xác thực và phân quyền thống nhất
- Thiết kế module hóa, dễ mở rộng

### 2. Hỗ trợ microservice
- Xử lý bất đồng bộ bằng tác vụ hàng đợi
- Tích hợp dịch vụ bộ nhớ đệm (cache)
- Lưu trữ tệp phân tán
- Hỗ trợ nhiều cơ sở dữ liệu

### 3. Cơ chế bảo mật
- Xác thực JWT Token
- Xác minh chữ ký API
- Phòng chống SQL injection
- Phòng chống tấn công XSS

### 4. Tối ưu hiệu năng
- Tách đọc/ghi cơ sở dữ liệu
- Tối ưu chiến lược bộ nhớ đệm
- CDN cho tài nguyên tĩnh
- Nén phản hồi API

## Quy chuẩn phát triển

### 1. Quy tắc đặt tên
- Tên lớp: PascalCase (ví dụ StoreProductServices)
- Tên phương thức: camelCase (ví dụ getProductList)
- Tên biến: camelCase (ví dụ productId)
- Tên hằng số: UPPER_CASE (ví dụ API_VERSION)

### 2. Tổ chức tệp
- Controller được nhóm theo module nghiệp vụ
- Service được phân chia theo lĩnh vực chức năng
- DAO được tổ chức theo thực thể dữ liệu
- Model tương ứng với cấu trúc bảng cơ sở dữ liệu

### 3. Quy chuẩn chú thích
- Chú thích PHPDoc cho lớp và phương thức
- Giải thích logic nghiệp vụ phức tạp
- Chú thích tài liệu API
- Mô tả các trường cơ sở dữ liệu

### 4. Xử lý lỗi
- Cơ chế xử lý ngoại lệ thống nhất
- Lớp ngoại lệ nghiệp vụ tùy chỉnh
- Ghi nhật ký lỗi
- Thông báo lỗi thân thiện với người dùng

## Yêu cầu triển khai

### 1. Yêu cầu môi trường
- PHP >= 7.4
- MySQL >= 5.7
- Redis >= 4.0
- Nginx/Apache

### 2. Yêu cầu extension
- PDO MySQL
- GD2/ImageMagick
- Curl
- OpenSSL
- Fileinfo

### 3. Cài đặt quyền
- Thư mục runtime có quyền ghi
- public/uploads có quyền ghi
- Thư mục nhật ký (log) có quyền ghi
- Thư mục bộ nhớ đệm (cache) có quyền ghi

## Khuyến nghị bảo trì

### 1. Bảo trì mã nguồn
- Rà soát mã (code review) định kỳ
- Độ bao phủ unit test
- Giám sát và phân tích hiệu năng
- Quét lỗ hổng bảo mật

### 2. Bảo trì cơ sở dữ liệu
- Sao lưu dữ liệu định kỳ
- Tối ưu và điều chỉnh chỉ mục
- Phân tích truy vấn chậm
- Dọn dẹp và lưu trữ dữ liệu cũ

### 3. Giám sát hệ thống
- Giám sát tài nguyên máy chủ
- Giám sát hiệu năng ứng dụng
- Giám sát nhật ký lỗi
- Phân tích hành vi người dùng

---

Tài liệu này mô tả chi tiết cấu trúc thư mục, thiết kế kiến trúc và quy chuẩn phát triển của mã nguồn backend CRMEB, cung cấp hướng dẫn kỹ thuật và tài liệu tham khảo đầy đủ cho đội ngũ phát triển.

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
