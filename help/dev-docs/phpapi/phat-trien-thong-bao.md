# Tài liệu phát triển thông báo tin nhắn

## 1. Tổng quan tài liệu

### 1.1 Mục đích tài liệu
Tài liệu này nhằm mô tả chi tiết kiến trúc thiết kế, chức năng cốt lõi, cách triển khai code và quy chuẩn phát triển của module thông báo tin nhắn trong hệ thống CRMEB, cung cấp tài liệu tham khảo phát triển đầy đủ cho lập trình viên hệ thống.

### 1.2 Phạm vi áp dụng
Tài liệu này dành cho lập trình viên, nhân viên bảo trì và nhà phát triển thứ cấp của hệ thống CRMEB, dùng để hướng dẫn công việc phát triển, mở rộng và bảo trì module thông báo tin nhắn.

### 1.3 Định nghĩa thuật ngữ
| Thuật ngữ | Giải thích |
|------|------|
| Thông báo hệ thống | Thông báo nội bộ được lưu trong cơ sở dữ liệu, được đẩy qua cơ chế nội bộ của hệ thống |
| Tin nhắn mẫu | Bao gồm tin nhắn mẫu OA WeChat và tin nhắn đăng ký Mini Program |
| Thông báo SMS | Tin nhắn gửi đến điện thoại của người dùng thông qua nhà cung cấp dịch vụ SMS bên thứ ba |
| Tin nhắn WeCom | Tin nhắn WeCom gửi cho quản trị viên và nhân viên CSKH |
| Hướng sự kiện (event-driven) | Xử lý các tình huống thông báo khác nhau bằng cách kích hoạt sự kiện |
| Kênh thông báo | Cách thức gửi tin nhắn, như thông báo nội bộ, SMS, tin nhắn mẫu, v.v. |
| Tình huống thông báo | Tình huống nghiệp vụ kích hoạt thông báo, như thanh toán đơn hàng, giao hàng, v.v. |

## 2. Các mô-đun chức năng cốt lõi

### 2.1 Quản lý tin nhắn hệ thống
- **Danh sách tin nhắn**: Hiển thị tin nhắn hệ thống, hỗ trợ tìm kiếm, phân trang
- **Thêm tin nhắn**: Thêm tin nhắn hệ thống mới
- **Sửa tin nhắn**: Chỉnh sửa nội dung tin nhắn hệ thống
- **Xóa tin nhắn**: Xóa tin nhắn hệ thống
- **Gửi tin nhắn**: Gửi tin nhắn hệ thống đến người dùng hoặc nhóm người dùng được chỉ định
- **Đọc tin nhắn**: Ghi nhận trạng thái đọc tin nhắn của người dùng
- **Thống kê tin nhắn**: Thống kê tình hình gửi và đọc tin nhắn

### 2.2 Quản lý thông báo SMS
- **Quản lý mẫu SMS**: Thêm, sửa, xóa mẫu SMS
- **Lịch sử gửi SMS**: Xem lịch sử và trạng thái gửi SMS
- **Cấu hình SMS**: Cấu hình nhà cung cấp dịch vụ SMS, khóa API, v.v.
- **Quản lý chữ ký SMS**: Quản lý chữ ký SMS
- **Cấu hình tình huống SMS**: Cấu hình mẫu SMS cho từng tình huống khác nhau

### 2.3 Quản lý tin nhắn mẫu
- **Quản lý tin nhắn mẫu OA WeChat**: Thêm, sửa, xóa tin nhắn mẫu OA WeChat
- **Quản lý tin nhắn đăng ký Mini Program**: Thêm, sửa, xóa tin nhắn đăng ký Mini Program
- **Lịch sử gửi tin nhắn mẫu**: Xem lịch sử và trạng thái gửi tin nhắn mẫu
- **Cấu hình tin nhắn mẫu**: Cấu hình tin nhắn mẫu cho từng tình huống khác nhau

### 2.4 Quản lý tin nhắn WeCom
- **Cấu hình WeCom**: Cấu hình khóa API WeCom, AgentId, v.v.
- **Mẫu tin nhắn WeCom**: Thêm, sửa, xóa mẫu tin nhắn WeCom
- **Lịch sử gửi tin nhắn WeCom**: Xem lịch sử và trạng thái gửi tin nhắn WeCom

### 2.5 Cấu hình tình huống thông báo
- **Danh sách tình huống thông báo**: Hiển thị các tình huống thông báo mà hệ thống hỗ trợ
- **Cấu hình thông báo theo tình huống**: Cấu hình các kênh thông báo cần gửi trong từng tình huống
- **Cấu hình mẫu thông báo**: Cấu hình mẫu thông báo tương ứng cho từng tình huống
- **Quản lý trạng thái thông báo**: Bật/tắt thông báo của tình huống cụ thể

## 3. Cấu trúc và thiết kế code

### 3.1 Thiết kế kiến trúc
Hệ thống áp dụng thiết kế kiến trúc hướng sự kiện, xử lý thống nhất các sự kiện thông báo thông qua listener:
```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Kích hoạt sự kiện nghiệp vụ  │────▶│  Trình lắng nghe sự kiện     │────▶│  Dịch vụ thông báo xử lý  │
└─────────────────┘     └─────────────────┘     └─────────────────┘
                                                          │
                                                          ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Gửi tin nhắn hệ thống  │◀────│  Thông báo đa kênh     │────▶│  Gửi thông báo SMS  │
└─────────────────┘     └─────────────────┘     └─────────────────┘
                                                          │
                                                          ▼
                                        ┌─────────────────────────┐
                                        │  Gửi tin nhắn mẫu           │
                                        ├─────────────────────────┤
                                        │  - Tin nhắn mẫu OA WeChat       │
                                        │  - Tin nhắn đăng ký Mini Program       │
                                        └─────────────────────────┘
                                                          │
                                                          ▼
                                        ┌─────────────────────────┐
                                        │  Gửi tin nhắn WeCom       │
                                        └─────────────────────────┘
```

### 3.2 Cấu trúc tệp cốt lõi

#### 3.2.1 Tầng dịch vụ (Services)
**Dịch vụ thông báo tin nhắn**:
- `NoticeService.php` - Lớp cơ sở của dịch vụ thông báo, dùng để thiết lập và quản lý sự kiện thông báo
- `SystemNotificationServices.php` - Lớp quản lý cấu hình thông báo hệ thống, chứa định nghĩa các tình huống thông báo
- `MessageSystemServices.php` - Lớp dịch vụ thông báo nội bộ, xử lý việc gửi, tra cứu và quản lý tin nhắn nội bộ của người dùng
- `SmsService.php` - Lớp dịch vụ SMS, hỗ trợ nhiều nhà cung cấp dịch vụ SMS
- `TemplateMessageServices.php` - Lớp quản lý tin nhắn mẫu, quản lý tin nhắn mẫu OA WeChat và tin nhắn đăng ký Mini Program

#### 3.2.2 Trình lắng nghe (Listener)
**Listener sự kiện thông báo**:
- `NoticeListener.php` - Listener sự kiện cốt lõi, xử lý các sự kiện thông báo, ánh xạ 30+ tình huống thông báo tới phương thức xử lý tương ứng
- `CustomNoticeListener.php` - Listener thông báo tùy chỉnh, dùng để xử lý các tình huống thông báo đặc biệt

#### 3.2.3 Tầng mô hình (Model)
**Các model liên quan đến tin nhắn**:
- `MessageSystem.php` - Model tin nhắn hệ thống
- `SmsRecord.php` - Model lịch sử gửi SMS
- `SystemNotification.php` - Model cấu hình thông báo hệ thống
- `TemplateMessage.php` - Model tin nhắn mẫu

#### 3.2.4 Tầng điều khiển (Controller)
**Controller thông báo tin nhắn**:
- `SystemNotification.php` - Controller cấu hình thông báo hệ thống
- `MessageSystem.php` - Controller tin nhắn hệ thống
- `Sms.php` - Controller quản lý SMS
- `TemplateMessage.php` - Controller tin nhắn mẫu

#### 3.2.5 Cấu hình route
**Route thông báo tin nhắn**:
- Bao gồm các route API liên quan đến thông báo tin nhắn, như quản lý tin nhắn hệ thống, cấu hình SMS, quản lý tin nhắn mẫu, v.v.

## 4. Thiết kế cơ sở dữ liệu

### 4.1 Bảng tin nhắn hệ thống (message_system)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID tin nhắn |
| uid | int(10) | ID người dùng nhận |
| title | varchar(100) | Tiêu đề tin nhắn |
| content | text | Nội dung tin nhắn |
| type | varchar(20) | Loại thông báo |
| status | tinyint(1) | Trạng thái đọc (0-chưa đọc, 1-đã đọc) |
| add_time | int(10) | Thời gian thêm |
| read_time | int(10) | Thời gian đọc |

### 4.2 Bảng lịch sử gửi SMS (sms_record)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID bản ghi |
| phone | varchar(20) | Số điện thoại nhận |
| template_id | varchar(50) | ID mẫu SMS |
| template_param | text | Tham số mẫu (định dạng JSON) |
| content | text | Nội dung SMS |
| status | tinyint(1) | Trạng thái gửi (0-thất bại, 1-thành công) |
| error_msg | varchar(255) | Thông tin lỗi |
| add_time | int(10) | Thời gian gửi |

### 4.3 Bảng cấu hình thông báo hệ thống (system_notification)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID cấu hình |
| mark | varchar(50) | Mã định danh thông báo |
| title | varchar(100) | Tiêu đề thông báo |
| scene | varchar(50) | Tình huống thông báo |
| type | varchar(20) | Loại thông báo (system-tin nhắn hệ thống, sms-SMS, wechat-tin nhắn mẫu WeChat) |
| template | text | Mẫu thông báo |
| is_open | tinyint(1) | Có bật hay không (0-tắt, 1-bật) |
| sort | int(10) | Thứ tự sắp xếp |
| add_time | int(10) | Thời gian thêm |

### 4.4 Bảng tin nhắn mẫu (template_message)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID mẫu |
| type | varchar(20) | Loại mẫu (wechat-OA WeChat, routine-Mini Program) |
| name | varchar(50) | Tên mẫu |
| template_id | varchar(100) | ID mẫu WeChat |
| short_key | varchar(50) | Khóa ngắn của mẫu |
| content | text | Nội dung mẫu |
| example | text | Ví dụ của mẫu |
| status | tinyint(1) | Trạng thái (0-vô hiệu hóa, 1-kích hoạt) |
| add_time | int(10) | Thời gian thêm |

## 5. Mô tả API

### 5.1 Quản lý tin nhắn hệ thống

#### 5.1.1 Lấy danh sách tin nhắn hệ thống
- **URL yêu cầu**: `/adminapi/v1/message/system/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: message/system/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - keyword: từ khóa tìm kiếm
  - status: Trạng thái đọc
- **Kết quả trả về**: Dữ liệu danh sách tin nhắn hệ thống

#### 5.1.2 Gửi tin nhắn hệ thống
- **URL yêu cầu**: `/adminapi/v1/message/system/send`
- **Phương thức yêu cầu**: POST
- **Quyền yêu cầu**: message/system/send
- **Tham số yêu cầu**:
  - uids: Mảng ID người dùng
  - title: Tiêu đề tin nhắn
  - content: Nội dung tin nhắn
  - type: Loại tin nhắn
- **Kết quả trả về**: Kết quả thao tác

### 5.2 Quản lý SMS

#### 5.2.1 Lấy danh sách mẫu SMS
- **URL yêu cầu**: `/adminapi/v1/message/sms/template/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: message/sms/template/list
- **Kết quả trả về**: Dữ liệu danh sách mẫu SMS

#### 5.2.2 Gửi SMS
- **URL yêu cầu**: `/adminapi/v1/message/sms/send`
- **Phương thức yêu cầu**: POST
- **Quyền yêu cầu**: message/sms/send
- **Tham số yêu cầu**:
  - phone: Số điện thoại nhận
  - template_id: ID mẫu SMS
  - params: Tham số mẫu (định dạng JSON)
- **Kết quả trả về**: Kết quả thao tác

### 5.3 Quản lý tin nhắn mẫu

#### 5.3.1 Lấy danh sách tin nhắn mẫu
- **URL yêu cầu**: `/adminapi/v1/message/template/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: message/template/list
- **Tham số yêu cầu**:
  - type: Loại mẫu (wechat-OA WeChat, routine-Mini Program)
- **Kết quả trả về**: Dữ liệu danh sách tin nhắn mẫu

#### 5.3.2 Gửi tin nhắn mẫu
- **URL yêu cầu**: `/adminapi/v1/message/template/send`
- **Phương thức yêu cầu**: POST
- **Quyền yêu cầu**: message/template/send
- **Tham số yêu cầu**:
  - openid: openid của người dùng
  - template_id: ID mẫu
  - data: Dữ liệu mẫu (định dạng JSON)
  - page: Trang chuyển hướng
- **Kết quả trả về**: Kết quả thao tác

### 5.4 Cấu hình tình huống thông báo

#### 5.4.1 Lấy danh sách tình huống thông báo
- **URL yêu cầu**: `/adminapi/v1/message/notification/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: message/notification/list
- **Kết quả trả về**: Dữ liệu danh sách tình huống thông báo

#### 5.4.2 Cấu hình tình huống thông báo
- **URL yêu cầu**: `/adminapi/v1/message/notification/config`
- **Phương thức yêu cầu**: POST
- **Quyền yêu cầu**: message/notification/config
- **Tham số yêu cầu**:
  - scene: Tình huống thông báo
  - config: Cấu hình thông báo (định dạng JSON, gồm trạng thái bật và mẫu của từng kênh thông báo)
- **Kết quả trả về**: Kết quả thao tác

### 5.5 API phía người dùng

#### 5.5.1 Lấy danh sách tin nhắn của người dùng
- **URL yêu cầu**: `/api/v1/user/message/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: Người dùng đã đăng nhập
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - type: Loại tin nhắn
- **Kết quả trả về**: Dữ liệu danh sách tin nhắn của người dùng

#### 5.5.2 Đánh dấu tin nhắn là đã đọc
- **URL yêu cầu**: `/api/v1/user/message/read/{id}`
- **Phương thức yêu cầu**: PUT
- **Quyền yêu cầu**: Người dùng đã đăng nhập
- **Kết quả trả về**: Kết quả thao tác

## 5. Quy chuẩn và quy trình phát triển

### 5.1 Quy chuẩn code
1. **Quy chuẩn đặt tên**:
   - Tên lớp dùng kiểu PascalCase (UpperCamelCase), ví dụ `NoticeService`
   - Tên phương thức dùng kiểu camelCase, ví dụ `sendSystemMessage`
   - Tên biến dùng kiểu camelCase, ví dụ `messageContent`
   - Tên hằng số viết hoa toàn bộ, phân tách bằng dấu gạch dưới, ví dụ `NOTICE_SCENE_ORDER_PAY`

2. **Cấu trúc code**:
   - Tuân thủ nghiêm ngặt kiến trúc phân tầng, cấm gọi vượt tầng
   - Mỗi phương thức chỉ đảm nhận một trách nhiệm, số dòng code không vượt quá 50 dòng
   - Thêm chú thích hợp lý, mô tả chức năng của phương thức, ý nghĩa tham số và giá trị trả về
   - Mọi lần gửi thông báo đều phải ghi log để tiện truy vết

3. **Hướng sự kiện**:
   - Mọi tình huống thông báo đều phải được kích hoạt thông qua sự kiện
   - Tên sự kiện phải rõ ràng, dễ hiểu, phản ánh đúng tình huống kích hoạt
   - Listener sự kiện phải được xử lý thống nhất để tiện quản lý

### 5.2 Quy trình phát triển
1. **Phân tích yêu cầu**: Làm rõ yêu cầu chức năng và tình huống nghiệp vụ của thông báo tin nhắn
2. **Giai đoạn thiết kế**: Thiết kế cấu trúc sự kiện, API và quy trình nghiệp vụ
3. **Triển khai mã nguồn**:
   - Định nghĩa lớp sự kiện (nếu cần)
   - Thêm phương thức xử lý sự kiện trong NoticeListener
   - Triển khai logic gửi thông báo
   - Thêm API
4. **Kiểm thử đơn vị**: Viết unit test, đảm bảo chức năng hoạt động chính xác
5. **Kiểm thử tích hợp**: Tiến hành kiểm thử tích hợp module, kiểm thử tương tác giữa thông báo và các module khác
6. **Kiểm thử hiệu năng**: Kiểm thử hiệu năng cho các tình huống thông báo có tần suất cao
7. **Rà soát code**: Gửi code để rà soát (code review), đảm bảo chất lượng code
8. **Triển khai vận hành**: Triển khai lên môi trường production

## 6. Các vấn đề thường gặp và giải pháp

### 6.1 Gửi thông báo thất bại
**Vấn đề**: Gửi thông báo thất bại, như gửi SMS thất bại, gửi tin nhắn mẫu thất bại
**Cách khắc phục**:
1. Kiểm tra cấu hình có đúng không, như khóa API, ID mẫu, v.v.
2. Kiểm tra dịch vụ bên thứ ba có hoạt động bình thường không, như nhà cung cấp dịch vụ SMS, API WeChat, v.v.
3. Xem log gửi, xác định nguyên nhân lỗi cụ thể
4. Triển khai cơ chế thử lại, gửi lại các thông báo bị gửi thất bại

### 6.2 Thông báo bị trễ
**Vấn đề**: Thông báo gửi chậm, ví dụ thanh toán đơn hàng xong rất lâu mới nhận được thông báo
**Cách khắc phục**:
1. Kiểm tra cấu hình hàng đợi, đảm bảo dịch vụ hàng đợi chạy bình thường
2. Tối ưu logic gửi thông báo, giảm thời gian xử lý
3. Tăng số lượng consumer của hàng đợi, nâng cao năng lực xử lý
4. Triển khai gửi bất đồng bộ, tránh chặn luồng xử lý chính

### 6.3 Thông báo bị gửi trùng lặp
**Vấn đề**: Cùng một thông báo bị gửi lặp lại cho người dùng
**Cách khắc phục**:
1. Triển khai xử lý lũy đẳng (idempotent), đảm bảo cùng một sự kiện không kích hoạt thông báo nhiều lần
2. Kiểm tra logic kích hoạt sự kiện, tránh kích hoạt lặp lại
3. Kiểm tra xem thông báo đã được gửi hay chưa trước khi gửi
4. Triển khai cơ chế loại bỏ trùng lặp, lọc các thông báo trùng

### 6.4 Quản lý mẫu thông báo lộn xộn
**Vấn đề**: Quá nhiều mẫu thông báo, quản lý lộn xộn
**Cách khắc phục**:
1. Quản lý mẫu theo phân loại, như theo kênh thông báo, theo tình huống nghiệp vụ
2. Xây dựng quy tắc đặt tên mẫu, phản ánh rõ mục đích sử dụng của mẫu
3. Định kỳ dọn dẹp các mẫu không dùng đến
4. Triển khai quản lý phiên bản mẫu để dễ dàng khôi phục (rollback)

## 7. Mở rộng và tùy biến

### 7.1 Thêm tình huống thông báo mới
1. **Định nghĩa hằng số tình huống**: Thêm hằng số tình huống thông báo mới vào lớp hằng số liên quan
2. **Thêm xử lý sự kiện**: Thêm phương thức xử lý sự kiện tương ứng trong NoticeListener
3. **Cấu hình mẫu**: Thêm cấu hình mẫu thông báo tương ứng vào bảng system_notification
4. **Thêm điểm kích hoạt**: Thêm code kích hoạt sự kiện vào logic nghiệp vụ
5. **Cấu hình frontend**: Thêm mục cấu hình tương ứng vào giao diện trang quản trị

### 7.2 Thêm kênh thông báo mới
1. **Triển khai lớp dịch vụ**: Tạo lớp dịch vụ thông báo mới, kế thừa NoticeService
2. **Thêm mục cấu hình**: Thêm cấu hình kênh thông báo mới vào cấu hình hệ thống
3. **Cập nhật listener sự kiện**: Thêm logic gửi thông báo qua kênh mới trong NoticeListener
4. **Cấu hình frontend**: Thêm mục cấu hình tương ứng vào giao diện trang quản trị

### 7.3 Tùy chỉnh mẫu thông báo
1. **Thêm trường mẫu**: Thêm trường mẫu vào bảng system_notification
2. **Cập nhật quản lý mẫu**: Sửa chức năng quản lý mẫu để hỗ trợ mẫu tùy chỉnh
3. **Triển khai render mẫu**: Thêm logic render mẫu vào dịch vụ thông báo
4. **Cấu hình frontend**: Thêm chức năng sửa mẫu vào giao diện trang quản trị

## 8. Lịch sử cập nhật tài liệu

| Phiên bản | Ngày cập nhật | Nội dung cập nhật | Người cập nhật |
|------|----------|----------|--------|
| 1.0 | 2026-01-19 | Phiên bản đầu tiên, bao gồm các chức năng cốt lõi như quản lý tin nhắn hệ thống, quản lý thông báo SMS, quản lý tin nhắn mẫu, quản lý tin nhắn WeCom, cấu hình tình huống thông báo, v.v. | AI Assistant |

## 9. Phụ lục

### 9.1 Sơ đồ quan hệ các lớp cốt lõi

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   NoticeService │────▶│  NoticeListener │────▶│  Các dịch vụ thông báo  │
└─────────────────┘     └─────────────────┘     └─────────────────┘
                                                          │
                                                          ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   SystemMessage │◀────│  MessageSystem  │     │   SmsService    │
└─────────────────┘     └─────────────────┘     └─────────────────┘
                                                          │
                                                          ▼
                                        ┌─────────────────────────┐
                                        │  TemplateMessageServices │
                                        ├─────────────────────────┤
                                        │  - Tin nhắn mẫu OA WeChat       │
                                        │  - Tin nhắn đăng ký Mini Program       │
                                        └─────────────────────────┘
```

### 9.2 Sơ đồ minh họa quy trình thông báo

```
Quy trình thông báo:
1. Hệ thống nghiệp vụ kích hoạt sự kiện (ví dụ: đơn hàng thanh toán thành công)
2. Trình lắng nghe sự kiện bắt sự kiện
3. Tìm cấu hình thông báo tương ứng theo loại sự kiện
4. Duyệt qua các kênh thông báo đã cấu hình
5. Gọi dịch vụ tương ứng để gửi thông báo
   a. Gửi tin nhắn hệ thống
   b. Gửi thông báo SMS
   c. Gửi tin nhắn mẫu
   d. Gửi tin nhắn WeCom
6. Ghi log gửi thông báo
7. Trả về kết quả gửi
```

### 9.3 Các tình huống thông báo được hỗ trợ

| Tên tình huống | Mô tả |
|----------|------|
| order_pay | Thanh toán đơn hàng thành công |
| order_delivery | Giao đơn hàng |
| order_received | Xác nhận đã nhận hàng |
| order_refund | Hoàn tiền đơn hàng |
| order_price_edit | Sửa giá đơn hàng |
| recharge_success | Nạp tiền thành công |
| extract_success | Rút tiền thành công |
| extract_fail | Rút tiền thất bại |
| brokerage_arrive | Hoa hồng đã về tài khoản |
| bargain_success | Săn giảm giá thành công |
| pink_success | Mua chung thành công |
| pink_fail | Mua chung thất bại |
| user_register | Người dùng đăng ký |
| user_login | Người dùng đăng nhập |
| spread_bind | Liên kết quan hệ giới thiệu |

### 9.4 Các kênh thông báo được hỗ trợ

| Tên kênh | Mô tả |
|----------|------|
| system | Tin nhắn hệ thống (thông báo nội bộ) |
| sms | Thông báo SMS |
| wechat | Tin nhắn mẫu OA WeChat |
| routine | Tin nhắn đăng ký Mini Program |
| enterprise_wechat | Tin nhắn WeCom |

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
