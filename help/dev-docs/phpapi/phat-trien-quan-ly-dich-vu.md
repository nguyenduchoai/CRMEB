# Tài liệu phát triển quản lý dịch vụ

## 1. Tổng quan tài liệu

### 1.1 Mục đích tài liệu
Tài liệu này nhằm mô tả chi tiết kiến trúc thiết kế, chức năng cốt lõi, cách triển khai code và quy chuẩn phát triển của module quản lý dịch vụ trong hệ thống CRMEB, cung cấp tài liệu tham khảo phát triển đầy đủ cho lập trình viên hệ thống.

### 1.2 Phạm vi áp dụng
Tài liệu này dành cho lập trình viên, nhân viên bảo trì và nhà phát triển thứ cấp của hệ thống CRMEB, dùng để hướng dẫn công việc phát triển, mở rộng và bảo trì module quản lý dịch vụ.

### 1.3 Định nghĩa thuật ngữ
| Thuật ngữ | Giải thích |
|------|------|
| Gói dịch vụ | Các tổ hợp dịch vụ giá trị gia tăng do hệ thống cung cấp |
| Vận đơn điện tử | Mẫu vận đơn chuyển phát được tạo trực tuyến |
| Dịch vụ SMS | Dịch vụ dùng để gửi SMS như mã xác thực, thông báo, v.v. |
| Sao chép sản phẩm | Dịch vụ sao chép thông tin sản phẩm từ các nền tảng khác về hệ thống cục bộ |
| In biên lai | Dịch vụ in biên lai đơn hàng |
| Hóa đơn điện tử | Dịch vụ tạo và quản lý hóa đơn điện tử trực tuyến |
| Access Token | Token xác thực của API dịch vụ |

## 2. Các mô-đun chức năng cốt lõi

### 2.1 Kích hoạt và quản lý dịch vụ
- **Quản lý gói dịch vụ**: Xem và mua các gói dịch vụ
- **Kích hoạt dịch vụ**: Kích hoạt và cấu hình các dịch vụ
- **Quản lý trạng thái dịch vụ**: Xem và quản lý trạng thái của các dịch vụ đã kích hoạt
- **Quản lý cấu hình dịch vụ**: Cấu hình các tham số của dịch vụ

### 2.2 Quản lý bản ghi chi tiêu
- **Tra cứu bản ghi chi tiêu**: Tra cứu bản ghi chi tiêu theo các điều kiện như loại, thời gian, v.v.
- **Thống kê chi tiêu**: Thống kê tình hình chi tiêu của từng dịch vụ
- **Chi tiết chi tiêu**: Xem thông tin chi tiết của bản ghi chi tiêu

### 2.3 Quản lý thông tin người dùng
- **Đăng nhập nền tảng dịch vụ**: Đăng nhập vào nền tảng quản lý dịch vụ
- **Lấy thông tin người dùng**: Lấy thông tin người dùng trên nền tảng dịch vụ
- **Đổi mật khẩu**: Đổi mật khẩu đăng nhập nền tảng dịch vụ
- **Đổi số điện thoại**: Đổi số điện thoại đã liên kết

### 2.4 Quản lý dịch vụ SMS
- **Kích hoạt dịch vụ SMS**: Kích hoạt dịch vụ SMS
- **Quản lý mẫu SMS**: Đăng ký và quản lý mẫu SMS
- **Sửa chữ ký**: Sửa chữ ký SMS
- **Cấu hình gửi SMS**: Cấu hình tham số gửi SMS

### 2.5 Dịch vụ vận đơn điện tử
- **Kích hoạt vận đơn điện tử**: Kích hoạt dịch vụ vận đơn điện tử
- **Cấu hình vận đơn điện tử**: Cấu hình tham số vận đơn điện tử
- **Tạo vận đơn điện tử**: Tạo vận đơn điện tử
- **Quản lý mẫu vận đơn điện tử**: Quản lý mẫu vận đơn điện tử

### 2.6 Dịch vụ sao chép sản phẩm
- **Kích hoạt sao chép sản phẩm**: Kích hoạt dịch vụ sao chép sản phẩm
- **Cấu hình sao chép sản phẩm**: Cấu hình tham số sao chép sản phẩm
- **Thao tác sao chép sản phẩm**: Sao chép thông tin sản phẩm từ các nền tảng khác

### 2.7 Dịch vụ in biên lai
- **Kích hoạt in biên lai**: Kích hoạt dịch vụ in biên lai
- **Cấu hình in biên lai**: Cấu hình tham số in biên lai
- **Quản lý mẫu biên lai**: Quản lý mẫu in biên lai

### 2.8 Dịch vụ hóa đơn điện tử
- **Kích hoạt hóa đơn điện tử**: Kích hoạt dịch vụ hóa đơn điện tử
- **Cấu hình hóa đơn điện tử**: Cấu hình tham số hóa đơn điện tử
- **Tạo hóa đơn điện tử**: Tạo hóa đơn điện tử
- **Quản lý hóa đơn điện tử**: Quản lý bản ghi hóa đơn điện tử

## 3. Cấu trúc và thiết kế code

### 3.1 Thiết kế kiến trúc
Hệ thống áp dụng thiết kế kiến trúc phân tầng, tuân thủ nghiêm ngặt mẫu thiết kế MVC và sử dụng mô hình driver để hỗ trợ nhiều nhà cung cấp dịch vụ:
```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│     Routes      │────▶│   Controllers   │────▶│    Services     │────▶│    Storage      │
└─────────────────┘     └─────────────────┘     └─────────────────┘     └─────────────────┘
        ▲                      ▲                      ▲                      ▲
        │                      │                      │                      │
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│  Request &      │     │   FormBuilder   │     │ BaseManager     │     │ External APIs   │
│   Response      │     └─────────────────┘     └─────────────────┘     └─────────────────┘
└─────────────────┘
```

### 3.2 Cấu trúc tệp cốt lõi

#### 3.2.1 Cấu hình route
**Route dịch vụ**: `crmeb/app/adminapi/route/serve.php`
- Bao gồm tất cả các route API liên quan đến quản lý dịch vụ
- Thiết kế module hóa: đăng nhập nền tảng, vận đơn điện tử, thông tin người dùng, gói thanh toán, dịch vụ SMS, bản ghi chi tiêu, v.v.

#### 3.2.2 Tầng điều khiển (Controller)
**Controller dịch vụ**: `crmeb/app/adminapi/controller/v1/serve/`
- `Serve.php` - Controller dịch vụ cốt lõi, xử lý các chức năng như gói dịch vụ, thanh toán, kích hoạt, tra cứu bản ghi, v.v.
- `Export.php` - Xử lý các chức năng liên quan đến vận đơn điện tử
- `Sms.php` - Xử lý các chức năng liên quan đến dịch vụ SMS
- `Login.php` - Xử lý các chức năng liên quan đến đăng nhập nền tảng dịch vụ

#### 3.2.3 Tầng dịch vụ (Services)
**Services quản lý dịch vụ**: `crmeb/app/services/serve/`
- `ServeServices.php` - Service quản lý dịch vụ cốt lõi, cung cấp các phương thức khởi tạo instance dịch vụ
  - `sms()`: Dịch vụ SMS
  - `copy()`: Dịch vụ sao chép sản phẩm
  - `express()`: Dịch vụ vận đơn điện tử
  - `orderPrint()`: Dịch vụ in biên lai
  - `user()`: Dịch vụ người dùng
  - `invoice()`: Dịch vụ hóa đơn điện tử

#### 3.2.4 Trình quản lý dịch vụ
**Trình quản lý dịch vụ**: `crmeb/crmeb/services/serve/Serve.php`
- Được triển khai dựa trên BaseManager, thiết kế theo mô hình driver
- Hỗ trợ nhiều driver dịch vụ, mặc định dùng dịch vụ CRMEB
- Cung cấp chức năng khởi tạo instance và quản lý dịch vụ

#### 3.2.5 Tầng lưu trữ dịch vụ
**Lưu trữ dịch vụ**: `crmeb/crmeb/services/serve/storage/Crmeb.php`
- Tương tác với API dịch vụ bên ngoài
- Lấy thông tin người dùng
- Tra cứu bản ghi lượng sử dụng
- Xử lý việc kích hoạt và cấu hình dịch vụ

#### 3.2.6 Dịch vụ xác thực
**Dịch vụ AccessToken**: `crmeb/app/services/serve/AccessTokenServeService.php`
- Xử lý xác thực và phân quyền cho API dịch vụ
- Tạo và quản lý Access Token
- Xác minh tính hợp lệ của yêu cầu API

## 4. Thiết kế cơ sở dữ liệu

### 4.1 Bảng gói dịch vụ (serve_meal)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID gói |
| name | varchar(100) | Tên gói |
| type | varchar(20) | Loại dịch vụ |
| price | decimal(10,2) | Giá gói |
| description | text | Mô tả gói |
| days | int(10) | Thời hạn hiệu lực (ngày) |
| status | tinyint(1) | Trạng thái (0-tắt, 1-bật) |
| sort | int(10) | Thứ tự sắp xếp |
| add_time | int(10) | Thời gian thêm |

### 4.2 Bảng bản ghi kích hoạt dịch vụ (serve_open)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID bản ghi |
| uid | int(10) | ID người dùng |
| type | varchar(20) | Loại dịch vụ |
| status | tinyint(1) | Trạng thái (0-chưa kích hoạt, 1-đã kích hoạt, 2-đã hết hạn) |
| start_time | int(10) | Thời gian bắt đầu |
| end_time | int(10) | Thời gian kết thúc |
| add_time | int(10) | Thời gian thêm |

### 4.3 Bảng bản ghi chi tiêu dịch vụ (serve_record)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID bản ghi |
| uid | int(10) | ID người dùng |
| type | varchar(20) | Loại dịch vụ |
| money | decimal(10,2) | Số tiền chi tiêu |
| mark | varchar(255) | Lý do chi tiêu |
| add_time | int(10) | Thời gian chi tiêu |

### 4.4 Bảng mẫu SMS (sms_template)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID mẫu |
| name | varchar(100) | Tên mẫu |
| content | text | Nội dung mẫu |
| status | tinyint(1) | Trạng thái (0-đang chờ duyệt, 1-đã được duyệt, 2-không được duyệt) |
| add_time | int(10) | Thời gian thêm |
| audit_time | int(10) | Thời gian duyệt |
| audit_msg | varchar(255) | Ghi chú duyệt |

### 4.5 Bảng cấu hình vận đơn điện tử (express_config)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID cấu hình |
| uid | int(10) | ID người dùng |
| company | varchar(50) | Đơn vị vận chuyển |
| app_id | varchar(100) | ID ứng dụng |
| app_key | varchar(100) | Khóa ứng dụng |
| config | text | Cấu hình khác (định dạng JSON) |
| status | tinyint(1) | Trạng thái (0-chưa kích hoạt, 1-đã kích hoạt) |
| add_time | int(10) | Thời gian thêm |

## 5. Mô tả API

### 5.1 Quản lý gói dịch vụ

#### 5.1.1 Lấy danh sách gói dịch vụ
- **URL yêu cầu**: `/adminapi/v1/serve/meal/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: serve/meal/list
- **Kết quả trả về**: Dữ liệu danh sách gói dịch vụ

#### 5.1.2 Mua gói dịch vụ
- **URL yêu cầu**: `/adminapi/v1/serve/meal/buy/{id}`
- **Phương thức yêu cầu**: POST
- **Quyền yêu cầu**: serve/meal/buy
- **Tham số yêu cầu**:
  - pay_type: phương thức thanh toán
- **Kết quả trả về**: Thông tin thanh toán

### 5.2 Quản lý kích hoạt dịch vụ

#### 5.2.1 Lấy danh sách dịch vụ đã kích hoạt
- **URL yêu cầu**: `/adminapi/v1/serve/open/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: serve/open/list
- **Kết quả trả về**: Dữ liệu danh sách dịch vụ đã kích hoạt

#### 5.2.2 Kích hoạt dịch vụ
- **URL yêu cầu**: `/adminapi/v1/serve/open/{type}`
- **Phương thức yêu cầu**: POST
- **Quyền yêu cầu**: serve/open
- **Tham số yêu cầu**: Tham số cấu hình dịch vụ
- **Kết quả trả về**: Kết quả thao tác

### 5.3 Quản lý bản ghi chi tiêu

#### 5.3.1 Lấy danh sách bản ghi chi tiêu
- **URL yêu cầu**: `/adminapi/v1/serve/record/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: serve/record/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - type: loại dịch vụ
  - start_time: thời gian bắt đầu
  - end_time: thời gian kết thúc
- **Kết quả trả về**: Dữ liệu danh sách bản ghi chi tiêu

### 5.4 Quản lý dịch vụ SMS

#### 5.4.1 Lấy danh sách mẫu SMS
- **URL yêu cầu**: `/adminapi/v1/serve/sms/template/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: serve/sms/template/list
- **Kết quả trả về**: Dữ liệu danh sách mẫu SMS

#### 5.4.2 Đăng ký mẫu SMS
- **URL yêu cầu**: `/adminapi/v1/serve/sms/template/apply`
- **Phương thức yêu cầu**: POST
- **Quyền yêu cầu**: serve/sms/template/apply
- **Tham số yêu cầu**:
  - name: tên mẫu
  - content: nội dung mẫu
  - remark: ghi chú
- **Kết quả trả về**: Kết quả thao tác

### 5.5 Quản lý vận đơn điện tử

#### 5.5.1 Kích hoạt vận đơn điện tử
- **URL yêu cầu**: `/adminapi/v1/serve/export/open`
- **Phương thức yêu cầu**: POST
- **Quyền yêu cầu**: serve/export/open
- **Tham số yêu cầu**:
  - company: đơn vị vận chuyển
  - app_id: ID ứng dụng
  - app_key: khóa bí mật ứng dụng
- **Kết quả trả về**: Kết quả thao tác

#### 5.5.2 Tạo vận đơn điện tử
- **URL yêu cầu**: `/adminapi/v1/serve/export/generate`
- **Phương thức yêu cầu**: POST
- **Quyền yêu cầu**: serve/export/generate
- **Tham số yêu cầu**:
  - company: đơn vị vận chuyển
  - order_id: ID đơn hàng
  - recipient: thông tin người nhận
  - sender: thông tin người gửi
- **Kết quả trả về**: Dữ liệu vận đơn điện tử

## 5. Quy chuẩn và quy trình phát triển

### 5.1 Quy chuẩn code
1. **Quy chuẩn đặt tên**:
   - Tên lớp dùng kiểu PascalCase (UpperCamelCase), ví dụ `ServeServices`
   - Tên phương thức dùng kiểu camelCase, ví dụ `getMealList`
   - Tên biến dùng kiểu camelCase, ví dụ `serviceConfig`
   - Tên hằng số viết hoa toàn bộ, phân tách bằng dấu gạch dưới, ví dụ `SERVE_TYPE_SMS`

2. **Cấu trúc code**:
   - Tuân thủ nghiêm ngặt kiến trúc phân tầng, cấm gọi vượt tầng
   - Mỗi phương thức chỉ đảm nhận một trách nhiệm, số dòng code không vượt quá 50 dòng
   - Thêm chú thích hợp lý, mô tả chức năng của phương thức, ý nghĩa tham số và giá trị trả về
   - Lớp driver dịch vụ nên kế thừa từ BaseManager

3. **Tương tác API**:
   - Mọi yêu cầu API bên ngoài bắt buộc phải được xác thực qua AccessTokenServeService
   - Yêu cầu và phản hồi API bắt buộc phải dùng định dạng thống nhất
   - Bắt buộc phải xử lý ngoại lệ của yêu cầu API, cung cấp thông báo lỗi thân thiện

### 5.2 Quy trình phát triển
1. **Phân tích yêu cầu**: Làm rõ yêu cầu chức năng và logic nghiệp vụ của quản lý dịch vụ
2. **Giai đoạn thiết kế**: Thiết kế cấu trúc dữ liệu, API và quy trình nghiệp vụ
3. **Triển khai code**: Viết code theo kiến trúc phân tầng
4. **Kiểm thử đơn vị**: Viết unit test, đảm bảo chức năng hoạt động chính xác
5. **Kiểm thử tích hợp**: Tiến hành kiểm thử tích hợp module, kiểm thử tương tác giữa dịch vụ và các module khác
6. **Kiểm thử hiệu năng**: Kiểm thử hiệu năng cho các lệnh gọi API
7. **Rà soát code**: Gửi code để rà soát (code review), đảm bảo chất lượng code
8. **Triển khai vận hành**: Triển khai lên môi trường production

## 6. Các vấn đề thường gặp và giải pháp

### 6.1 Vấn đề xác thực API
**Vấn đề**: Xác thực yêu cầu API thất bại
**Cách khắc phục**:
1. Kiểm tra AccessToken đã hết hạn chưa, làm mới kịp thời
2. Đảm bảo tham số yêu cầu chính xác, đặc biệt là tham số chữ ký
3. Kiểm tra dịch vụ đã được kích hoạt chưa, trạng thái có bình thường không
4. Kiểm tra thiết lập danh sách trắng IP (nếu có)

### 6.2 Kích hoạt dịch vụ thất bại
**Vấn đề**: Kích hoạt hoặc cấu hình dịch vụ thất bại
**Cách khắc phục**:
1. Kiểm tra gói dịch vụ có còn hiệu lực không
2. Đảm bảo trạng thái thanh toán bình thường
3. Kiểm tra tham số cấu hình có chính xác không
4. Xem nhật ký dịch vụ, xác định lỗi cụ thể

### 6.3 Yêu cầu API bị quá thời gian chờ
**Vấn đề**: Yêu cầu API bên ngoài bị quá thời gian chờ
**Cách khắc phục**:
1. Kiểm tra kết nối mạng có bình thường không
2. Tối ưu yêu cầu API, giảm số lần gửi yêu cầu
3. Triển khai cơ chế thử lại yêu cầu, thiết lập thời gian chờ hợp lý
4. Cân nhắc dùng yêu cầu bất đồng bộ để tránh chặn luồng xử lý chính

### 6.4 Vấn đề đồng bộ dữ liệu dịch vụ
**Vấn đề**: Dữ liệu cục bộ và dữ liệu trên nền tảng dịch vụ không đồng bộ
**Cách khắc phục**:
1. Triển khai cơ chế đồng bộ định kỳ, đảm bảo tính nhất quán của dữ liệu
2. Đồng bộ dữ liệu ngay sau các thao tác quan trọng
3. Cung cấp chức năng đồng bộ thủ công, cho phép quản trị viên kích hoạt đồng bộ thủ công

## 7. Mở rộng và tùy biến

### 7.1 Thêm loại dịch vụ mới
1. **Tạo lớp driver dịch vụ**: Kế thừa BaseManager, triển khai các chức năng cốt lõi của dịch vụ
2. **Thêm bảng cấu hình dịch vụ**: Tạo bảng cấu hình cho loại dịch vụ mới
3. **Triển khai controller dịch vụ**: Xử lý các yêu cầu API của dịch vụ mới
4. **Thêm cấu hình route**: Thêm route của dịch vụ mới trong serve.php
5. **Cập nhật trình quản lý dịch vụ**: Thêm phương thức khởi tạo instance của dịch vụ mới trong ServeServices

### 7.2 Thay đổi nhà cung cấp dịch vụ
1. **Tạo lớp lưu trữ dịch vụ mới**: Kế thừa từ lớp cơ sở lưu trữ dịch vụ, triển khai tương tác với nhà cung cấp dịch vụ mới
2. **Cập nhật cấu hình dịch vụ**: Chỉnh sửa cấu hình dịch vụ, chuyển sang nhà cung cấp dịch vụ mới
3. **Kiểm thử chức năng dịch vụ**: Đảm bảo mọi chức năng dịch vụ hoạt động bình thường

### 7.3 Mở rộng chức năng dịch vụ
1. **Thêm API mới**: Thêm phương thức mới trong controller để triển khai chức năng mở rộng
2. **Cập nhật cấu hình route**: Thêm route cho API mới
3. **Triển khai logic nghiệp vụ**: Triển khai logic nghiệp vụ mới ở tầng service
4. **Cập nhật cơ sở dữ liệu**: Nếu cần, thêm hoặc chỉnh sửa bảng cơ sở dữ liệu

## 8. Lịch sử cập nhật tài liệu

| Phiên bản | Ngày cập nhật | Nội dung cập nhật | Người cập nhật |
|------|----------|----------|--------|
| 1.0 | 2026-01-19 | Phiên bản đầu tiên, bao gồm các chức năng cốt lõi như kích hoạt và quản lý dịch vụ, quản lý bản ghi chi tiêu, quản lý thông tin người dùng, quản lý dịch vụ SMS, dịch vụ vận đơn điện tử, dịch vụ sao chép sản phẩm, dịch vụ in biên lai, dịch vụ hóa đơn điện tử, v.v. | AI Assistant |

## 9. Phụ lục

### 9.1 Sơ đồ quan hệ các lớp cốt lõi

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│     Routes      │────▶│   Controllers   │────▶│    Services     │────▶│    Storage      │
└─────────────────┘     └─────────────────┘     └─────────────────┘     └─────────────────┘
        ▲                      ▲                      ▲                      ▲
        │                      │                      │                      │
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│  Request &      │     │   FormBuilder   │     │ BaseManager     │     │ External APIs   │
│   Response      │     └─────────────────┘     └─────────────────┘     └─────────────────┘
└─────────────────┘
```

### 9.2 Lưu đồ kích hoạt dịch vụ

```
Quy trình kích hoạt dịch vụ:
1. Người dùng chọn gói dịch vụ
2. Hệ thống tạo đơn hàng dịch vụ
3. Người dùng thanh toán đơn hàng
4. Sau khi thanh toán thành công, hệ thống gọi API dịch vụ để kích hoạt dịch vụ
5. Hệ thống cập nhật trạng thái dịch vụ
6. Hệ thống ghi nhận lịch sử chi tiêu
7. Gửi thông báo kích hoạt dịch vụ cho người dùng
```

### 9.3 Lưu đồ yêu cầu API

```
Quy trình request API:
1. Client gửi request API
2. Server xác thực tham số request
3. AccessTokenServeService xác thực AccessToken
4. Controller dịch vụ xử lý request
5. Tầng service gọi driver dịch vụ tương ứng
6. Driver dịch vụ gọi API bên ngoài
7. API bên ngoài trả về kết quả
8. Driver dịch vụ xử lý kết quả
9. Controller dịch vụ trả response cho client
```

### 9.4 Hằng số loại dịch vụ

| Hằng số | Mô tả |
|------|------|
| SERVE_TYPE_SMS | Dịch vụ SMS |
| SERVE_TYPE_COPY | Dịch vụ sao chép sản phẩm |
| SERVE_TYPE_EXPRESS | Dịch vụ vận đơn điện tử |
| SERVE_TYPE_ORDER_PRINT | Dịch vụ in biên lai |
| SERVE_TYPE_INVOICE | Dịch vụ hóa đơn điện tử |

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
