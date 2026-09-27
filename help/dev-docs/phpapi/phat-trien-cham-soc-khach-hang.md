# Tài liệu phát triển hệ thống CSKH

## 1. Tổng quan tài liệu

### 1.1 Mục đích tài liệu
Tài liệu này nhằm mô tả chi tiết kiến trúc thiết kế, chức năng cốt lõi, cách triển khai code và quy chuẩn phát triển của module hệ thống CSKH trong CRMEB, cung cấp tài liệu tham khảo phát triển đầy đủ cho lập trình viên hệ thống.

### 1.2 Phạm vi áp dụng
Tài liệu này dành cho lập trình viên, nhân viên bảo trì và nhà phát triển thứ cấp của hệ thống CRMEB, dùng để hướng dẫn công việc phát triển, mở rộng và bảo trì module hệ thống CSKH.

### 1.3 Định nghĩa thuật ngữ
| Thuật ngữ | Giải thích |
|------|------|
| Hệ thống CSKH | Hệ thống giúp người bán và khách hàng trao đổi theo thời gian thực |
| WebSocket | Giao thức truyền thông song công toàn phần (full-duplex) trên một kết nối TCP duy nhất |
| Chuyển tiếp hội thoại | Chuyển phiên chat hiện tại cho nhân viên CSKH khác xử lý |
| Câu trả lời mẫu CSKH | Các mẫu trả lời thường dùng của CSKH |
| Mẫu câu dùng chung | Mẫu câu mà mọi nhân viên CSKH đều có thể sử dụng |
| Mẫu câu cá nhân | Mẫu câu chỉ nhân viên CSKH hiện tại mới được sử dụng |
| Chế độ khách vãng lai | Chế độ cho phép người dùng chưa đăng nhập bắt đầu trò chuyện |

## 2. Các mô-đun chức năng cốt lõi

### 2.1 Quản lý tài khoản CSKH
- **Đăng nhập CSKH**: Hỗ trợ đăng nhập bằng tài khoản/mật khẩu và quét mã WeChat
- **Kiểm soát quyền**: Nhân viên CSKH ở các vai trò khác nhau có quyền khác nhau
- **Quản lý trạng thái online**: Hiển thị trạng thái online/offline của nhân viên CSKH
- **Quản lý hồ sơ CSKH**: Chỉnh sửa thông tin cơ bản của nhân viên CSKH

### 2.2 Chức năng chat CSKH
- **Giao tiếp thời gian thực**: Chat thời gian thực dựa trên WebSocket
- **Loại tin nhắn**: Hỗ trợ nhiều loại tin nhắn như văn bản, hình ảnh, v.v.
- **Lịch sử chat**: Lịch sử chat được lưu trữ bền vững, hỗ trợ tra cứu lịch sử
- **Nhận diện người dùng**: Hỗ trợ hai chế độ chat: khách vãng lai và người dùng đã đăng ký
- **Thông báo tin nhắn**: Thông báo tin nhắn mới theo thời gian thực
- **Trạng thái đã đọc**: Hiển thị trạng thái đã đọc/chưa đọc của tin nhắn

### 2.3 Quản lý người dùng
- **Thông tin người dùng**: Xem thông tin chi tiết của người dùng
- **Nhãn người dùng**: Gắn/gỡ nhãn cho người dùng
- **Nhóm người dùng**: Phân người dùng vào các nhóm khác nhau
- **Lịch sử trò chuyện**: Xem lịch sử chat trước đây của người dùng
- **Lịch sử hành vi**: Xem lịch sử hành vi của người dùng như duyệt xem, mua hàng, v.v.

### 2.4 Xử lý đơn hàng
- **Danh sách đơn hàng**: Xem danh sách đơn hàng của người dùng
- **Giao hàng**: Xử lý giao hàng cho đơn hàng
- **Hoàn tiền đơn hàng**: Xử lý yêu cầu hoàn tiền của đơn hàng
- **Đổi giá đơn hàng**: Chỉnh sửa giá đơn hàng
- **Ghi chú đơn hàng**: Thêm ghi chú cho đơn hàng

### 2.5 Liên quan đến sản phẩm
- **Chi tiết sản phẩm**: Xem thông tin chi tiết sản phẩm
- **Lịch sử mua hàng**: Xem lịch sử mua hàng của người dùng
- **Lịch sử duyệt xem**: Xem lịch sử duyệt xem của người dùng
- **Sản phẩm bán chạy**: Xem danh sách sản phẩm bán chạy

### 2.6 Quản lý mẫu câu CSKH
- **Danh mục mẫu câu**: Quản lý danh mục mẫu câu
- **Thêm mẫu câu**: Thêm mẫu câu mới
- **Sửa mẫu câu**: Chỉnh sửa mẫu câu hiện có
- **Xóa mẫu câu**: Xóa các mẫu câu không còn dùng
- **Mẫu câu dùng chung**: Mẫu câu mà mọi nhân viên CSKH đều có thể sử dụng
- **Mẫu câu cá nhân**: Mẫu câu chỉ nhân viên CSKH hiện tại mới được sử dụng

### 2.7 Chuyển tiếp CSKH
- **Chuyển tiếp hội thoại**: Hỗ trợ chuyển tiếp hội thoại giữa các nhân viên CSKH
- **Lịch sử chuyển tiếp**: Xem lịch sử chuyển tiếp hội thoại
- **Thông báo thời gian thực**: Thông báo ngay cho nhân viên CSKH liên quan khi chuyển tiếp
- **Giữ lịch sử chat**: Giữ nguyên toàn bộ lịch sử chat khi chuyển tiếp

## 3. Cấu trúc và thiết kế code

### 3.1 Thiết kế kiến trúc
Hệ thống CSKH sử dụng kiến trúc tách biệt frontend và backend, backend dùng framework ThinkPHP, frontend dùng Vue.js, giao tiếp thời gian thực được thực hiện dựa trên WebSocket:
```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Ứng dụng frontend       │────▶│   Máy chủ WebSocket  │────▶│   Dịch vụ API backend    │
└─────────────────┘     └─────────────────┘     └─────────────────┘
                                        │
                                        ▼
                                ┌─────────────────┐
                                │   Cơ sở dữ liệu          │
                                └─────────────────┘
```

### 3.2 Cấu trúc tệp cốt lõi

#### 3.2.1 Thư mục backend
**API CSKH**: `/crmeb/app/kefuapi/`
- `controller/` - Controller API CSKH
- `middleware/` - Middleware API CSKH
- `route/` - Route API CSKH
- `validate/` - Validator API CSKH

#### 3.2.2 Tầng mô hình
**Model dịch vụ (service)**: `/crmeb/app/model/service/`
- `StoreService.php` - Model thông tin CSKH
- `StoreServiceRecord.php` - Model bản ghi người dùng chat với CSKH
- `StoreServiceLog.php` - Model lịch sử chat CSKH
- `StoreServiceSpeechcraft.php` - Model mẫu câu CSKH

#### 3.2.3 Tầng dịch vụ
**Service CSKH**: `/crmeb/app/services/kefu/`
- `KefuLoginServices.php` - Service đăng nhập CSKH
- `KefuUserServices.php` - Service người dùng CSKH
- `KefuServiceServices.php` - Quản lý dịch vụ CSKH
- `KefuSpeechcraftServices.php` - Service mẫu câu CSKH

#### 3.2.4 Thư mục frontend
**API frontend CSKH**: `/template/admin/src/api/kefu.js`
- Bao gồm tất cả API mà frontend của hệ thống CSKH gọi

#### 3.2.5 Dịch vụ WebSocket
**Triển khai WebSocket**: Được triển khai dựa trên framework Workerman
- Cung cấp chức năng chat thời gian thực
- Xử lý việc đẩy và nhận tin nhắn

## 4. Thiết kế cơ sở dữ liệu

### 4.1 Bảng thông tin CSKH (store_service)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID nhân viên CSKH |
| uid | int(10) | ID người dùng liên kết |
| nick_name | varchar(20) | Biệt danh CSKH |
| avatar | varchar(255) | Ảnh đại diện CSKH |
| phone | varchar(20) | Số điện thoại CSKH |
| password | varchar(60) | Mật khẩu CSKH |
| salt | varchar(10) | Salt mật khẩu |
| status | tinyint(1) | Trạng thái (0-vô hiệu hóa, 1-kích hoạt) |
| is_online | tinyint(1) | Trạng thái online (0-offline, 1-online) |
| add_time | int(10) | Thời gian thêm |

### 4.2 Bảng bản ghi người dùng chat với CSKH (store_service_record)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID bản ghi |
| uid | int(10) | ID người dùng |
| service_id | int(10) | ID nhân viên CSKH |
| last_msg | varchar(255) | Nội dung tin nhắn cuối cùng |
| last_time | int(10) | Thời gian tin nhắn cuối cùng |
| is_visitor | tinyint(1) | Có phải khách vãng lai không (0-không, 1-có) |
| visitor_id | varchar(50) | ID khách vãng lai |
| status | tinyint(1) | Trạng thái (0-đã kết thúc, 1-đang diễn ra) |
| add_time | int(10) | Thời gian thêm |

### 4.3 Bảng lịch sử chat CSKH (store_service_log)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID bản ghi |
| from_id | int(10) | ID người gửi |
| to_id | int(10) | ID người nhận |
| service_id | int(10) | ID nhân viên CSKH |
| message | text | Nội dung tin nhắn |
| type | tinyint(1) | Loại tin nhắn (0-văn bản, 1-hình ảnh) |
| is_read | tinyint(1) | Đã đọc chưa (0-chưa đọc, 1-đã đọc) |
| is_visitor | tinyint(1) | Có phải khách vãng lai không (0-không, 1-có) |
| visitor_id | varchar(50) | ID khách vãng lai |
| add_time | int(10) | Thời gian thêm |

### 4.4 Bảng mẫu câu CSKH (store_service_speechcraft)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID mẫu câu |
| uid | int(10) | ID nhân viên CSKH (0-mẫu câu dùng chung) |
| cate_id | int(10) | ID danh mục |
| title | varchar(100) | Tiêu đề câu trả lời mẫu |
| content | text | Nội dung câu trả lời mẫu |
| sort | int(10) | Thứ tự sắp xếp |
| status | tinyint(1) | Trạng thái (0-vô hiệu hóa, 1-kích hoạt) |
| add_time | int(10) | Thời gian thêm |

## 5. Mô tả API

### 5.1 Liên quan đến đăng nhập CSKH

#### 5.1.1 Đăng nhập CSKH
- **URL yêu cầu**: `/kefuapi/login`
- **Phương thức yêu cầu**: POST
- **Tham số yêu cầu**:
  - username: tên đăng nhập
  - password: mật khẩu
- **Kết quả trả về**: Trạng thái đăng nhập, token, v.v.

#### 5.1.2 Đăng nhập bằng quét mã WeChat
- **URL yêu cầu**: `/kefuapi/key`
- **Phương thức yêu cầu**: GET
- **Kết quả trả về**: Key đăng nhập quét mã

#### 5.1.3 Kiểm tra tình trạng quét mã
- **URL yêu cầu**: `/kefuapi/scan/:key`
- **Phương thức yêu cầu**: GET
- **Kết quả trả về**: Trạng thái quét mã

### 5.2 Liên quan đến chat

#### 5.2.1 Lấy danh sách người dùng chat
- **URL yêu cầu**: `/kefuapi/user/record`
- **Phương thức yêu cầu**: GET
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - status: trạng thái
- **Kết quả trả về**: Danh sách người dùng chat

#### 5.2.2 Lấy lịch sử chat
- **URL yêu cầu**: `/kefuapi/service/list`
- **Phương thức yêu cầu**: GET
- **Tham số yêu cầu**:
  - uid: ID người dùng
  - visitor_id: ID khách vãng lai
  - page: số trang
  - limit: số lượng mỗi trang
- **Kết quả trả về**: Danh sách lịch sử chat

#### 5.2.3 Chuyển tiếp CSKH
- **URL yêu cầu**: `/kefuapi/service/transfer`
- **Phương thức yêu cầu**: POST
- **Tham số yêu cầu**:
  - uid: ID người dùng
  - visitor_id: ID khách vãng lai
  - to_service_id: ID nhân viên CSKH được chuyển tiếp đến
- **Kết quả trả về**: Kết quả chuyển tiếp

### 5.3 Liên quan đến quản lý người dùng

#### 5.3.1 Lấy thông tin chi tiết người dùng
- **URL yêu cầu**: `/kefuapi/user/info/:uid`
- **Phương thức yêu cầu**: GET
- **Kết quả trả về**: Thông tin chi tiết người dùng

#### 5.3.2 Lấy nhãn người dùng
- **URL yêu cầu**: `/kefuapi/user/label/:uid`
- **Phương thức yêu cầu**: GET
- **Kết quả trả về**: Danh sách nhãn người dùng

#### 5.3.3 Thiết lập nhãn người dùng
- **URL yêu cầu**: `/kefuapi/user/label/:uid`
- **Phương thức yêu cầu**: PUT
- **Tham số yêu cầu**:
  - label_ids: Mảng ID nhãn
- **Kết quả trả về**: Kết quả thao tác

### 5.4 Liên quan đến xử lý đơn hàng

#### 5.4.1 Lấy danh sách đơn hàng của người dùng
- **URL yêu cầu**: `/kefuapi/order/list/:uid`
- **Phương thức yêu cầu**: GET
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - status: Trạng thái đơn hàng
- **Kết quả trả về**: Danh sách đơn hàng của người dùng

#### 5.4.2 Giao hàng cho đơn hàng
- **URL yêu cầu**: `/kefuapi/order/delivery/:id`
- **Phương thức yêu cầu**: POST
- **Tham số yêu cầu**:
  - express_id: ID đơn vị vận chuyển
  - express_no: Mã vận đơn
- **Kết quả trả về**: Kết quả thao tác

#### 5.4.3 Hoàn tiền đơn hàng
- **URL yêu cầu**: `/kefuapi/order/refund`
- **Phương thức yêu cầu**: POST
- **Tham số yêu cầu**:
  - order_id: ID đơn hàng
  - refund_money: Số tiền hoàn
  - refund_reason: Lý do hoàn tiền
- **Kết quả trả về**: Kết quả thao tác

### 5.5 Liên quan đến quản lý câu trả lời mẫu

#### 5.5.1 Lấy danh sách câu trả lời mẫu
- **URL yêu cầu**: `/kefuapi/service/speechcraft`
- **Phương thức yêu cầu**: GET
- **Tham số yêu cầu**:
  - cate_id: ID danh mục
  - type: Loại (0-toàn cục, 1-cá nhân)
- **Kết quả trả về**: Danh sách câu trả lời mẫu

#### 5.5.2 Thêm câu trả lời mẫu
- **URL yêu cầu**: `/kefuapi/service/speechcraft`
- **Phương thức yêu cầu**: POST
- **Tham số yêu cầu**:
  - cate_id: ID danh mục
  - title: Tiêu đề câu trả lời mẫu
  - content: Nội dung câu trả lời mẫu
  - type: Loại (0-toàn cục, 1-cá nhân)
- **Kết quả trả về**: Kết quả thao tác

## 5. Quy chuẩn và quy trình phát triển

### 5.1 Quy chuẩn code
1. **Quy chuẩn đặt tên**:
   - Tên lớp dùng kiểu PascalCase (UpperCamelCase), ví dụ `KefuLoginServices`
   - Tên phương thức dùng kiểu camelCase, ví dụ `getUserList`
   - Tên biến dùng kiểu camelCase, ví dụ `chatMessage`
   - Tên hằng số viết hoa toàn bộ, phân tách bằng dấu gạch dưới, ví dụ `KEFU_STATUS_ONLINE`

2. **Cấu trúc code**:
   - Tuân thủ nghiêm ngặt kiến trúc phân tầng, cấm gọi vượt tầng
   - Mỗi phương thức chỉ đảm nhận một trách nhiệm, số dòng code không vượt quá 50 dòng
   - Thêm chú thích hợp lý, mô tả chức năng của phương thức, ý nghĩa tham số và giá trị trả về
   - Mọi tin nhắn trò chuyện đều phải được ghi log để tiện truy vết

3. **Quy chuẩn WebSocket**:
   - Kết nối WebSocket bắt buộc phải xác thực danh tính
   - Định dạng tin nhắn phải thống nhất để frontend và backend dễ dàng phân tích
   - Phải xử lý các tình huống bất thường như mất kết nối, kết nối lại, v.v.
   - Việc gửi tin nhắn phải có cơ chế thử lại

### 5.2 Quy trình phát triển
1. **Phân tích yêu cầu**: Xác định rõ yêu cầu chức năng và logic nghiệp vụ của hệ thống CSKH
2. **Giai đoạn thiết kế**: Thiết kế cấu trúc dữ liệu, API và quy trình nghiệp vụ
3. **Triển khai mã nguồn**:
   - Triển khai API backend
   - Triển khai dịch vụ WebSocket
   - Triển khai trang frontend và tương tác
4. **Kiểm thử đơn vị**: Viết unit test, đảm bảo chức năng hoạt động chính xác
5. **Kiểm thử tích hợp**: Thực hiện kiểm thử tích hợp mô-đun, kiểm tra tương tác giữa hệ thống CSKH với các mô-đun khác
6. **Kiểm thử hiệu năng**: Kiểm thử hiệu năng cho kết nối WebSocket, đẩy tin nhắn, v.v.
7. **Kiểm thử bảo mật**: Kiểm thử các chức năng bảo mật như xác thực đăng nhập, kiểm soát quyền, v.v.
8. **Rà soát code**: Gửi code để rà soát (code review), đảm bảo chất lượng code
9. **Triển khai vận hành**: Triển khai lên môi trường production

## 6. Các vấn đề thường gặp và giải pháp

### 6.1 Sự cố kết nối WebSocket
**Vấn đề**: Kết nối WebSocket thất bại hoặc thường xuyên bị ngắt
**Cách khắc phục**:
1. Kiểm tra máy chủ WebSocket có đang chạy bình thường không
2. Kiểm tra cấu hình tường lửa, đảm bảo cổng WebSocket đã được mở
3. Triển khai cơ chế heartbeat cho kết nối để duy trì kết nối lâu dài
4. Triển khai cơ chế tự động kết nối lại để xử lý trường hợp mất kết nối

### 6.2 Độ trễ khi đẩy tin nhắn
**Vấn đề**: Việc đẩy tin nhắn bị trễ nghiêm trọng
**Cách khắc phục**:
1. Tối ưu hiệu năng máy chủ WebSocket, tăng khả năng xử lý đồng thời
2. Dùng hàng đợi tin nhắn để xử lý việc đẩy tin nhắn, tránh bị nghẽn (blocking)
3. Tối ưu môi trường mạng, giảm độ trễ mạng
4. Triển khai độ ưu tiên cho tin nhắn, đảm bảo tin nhắn quan trọng được đẩy trước

### 6.3 Mất lịch sử trò chuyện
**Vấn đề**: Lịch sử trò chuyện bị mất hoặc không đầy đủ
**Cách khắc phục**:
1. Đảm bảo lịch sử trò chuyện được lưu trữ bền vững kịp thời
2. Dùng transaction để đảm bảo tính nhất quán của dữ liệu
3. Triển khai cơ chế sao lưu lịch sử trò chuyện
4. Định kỳ kiểm tra tính toàn vẹn của lịch sử trò chuyện

### 6.4 Sự cố chuyển tiếp CSKH
**Vấn đề**: Chuyển tiếp CSKH thất bại hoặc mất lịch sử trò chuyện
**Cách khắc phục**:
1. Đảm bảo logic chuyển tiếp chính xác, giữ lại đầy đủ lịch sử trò chuyện
2. Triển khai thông báo trạng thái chuyển tiếp, đảm bảo các nhân viên CSKH liên quan đều nhận được thông báo
3. Kiểm thử các tình huống chuyển tiếp khác nhau, đảm bảo chức năng chuyển tiếp hoạt động ổn định

## 7. Mở rộng và tùy biến

### 7.1 Thêm loại tin nhắn mới
1. **Sửa đổi cơ sở dữ liệu**: Thêm giá trị enum cho loại tin nhắn mới vào bảng store_service_log
2. **Sửa đổi backend**:
   - Bổ sung xử lý cho loại tin nhắn mới trong logic xử lý tin nhắn
   - Cập nhật API để hỗ trợ loại tin nhắn mới
3. **Sửa đổi frontend**:
   - Thêm component hiển thị cho loại tin nhắn mới
   - Cập nhật component gửi tin nhắn để hỗ trợ loại tin nhắn mới

### 7.2 Mở rộng quyền CSKH
1. **Sửa đổi cơ sở dữ liệu**: Thêm trường quyền vào bảng store_service
2. **Sửa đổi backend**:
   - Bổ sung việc kiểm tra quyền mới vào middleware xác thực quyền
   - Cập nhật kiểm soát quyền của API
3. **Sửa đổi frontend**:
   - Hiển thị menu và nút chức năng một cách động theo quyền
   - Thêm trang quản lý quyền

### 7.3 Tích hợp hệ thống CSKH của bên thứ ba
1. **Thiết kế tầng adapter**: Thiết kế tầng adapter kết nối với hệ thống CSKH của bên thứ ba
2. **Triển khai kết nối API**: Triển khai kết nối API với hệ thống CSKH của bên thứ ba
3. **Đồng bộ dữ liệu**: Triển khai đồng bộ các dữ liệu như dữ liệu CSKH, lịch sử trò chuyện, v.v.
4. **Tích hợp frontend**: Tích hợp các chức năng của hệ thống CSKH bên thứ ba vào trang frontend

## 8. Lịch sử cập nhật tài liệu

| Phiên bản | Ngày cập nhật | Nội dung cập nhật | Người cập nhật |
|------|----------|----------|--------|
| 1.0 | 2026-01-19 | Phiên bản đầu tiên, bao gồm các chức năng cốt lõi như quản lý tài khoản CSKH, chức năng chat CSKH, quản lý người dùng, xử lý đơn hàng, các chức năng liên quan đến sản phẩm, quản lý câu trả lời mẫu CSKH, chuyển tiếp CSKH | AI Assistant |

## 9. Phụ lục

### 9.1 Sơ đồ quan hệ các lớp cốt lõi

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Ứng dụng frontend       │────▶│   Máy chủ WebSocket  │────▶│   Dịch vụ API backend    │
└─────────────────┘     └─────────────────┘     └─────────────────┘
                                        │
                                        ▼
                                ┌─────────────────┐
                                │   Cơ sở dữ liệu          │
                                └─────────────────┘
```

### 9.2 Sơ đồ quy trình trò chuyện

```
Quy trình chat:
1. Người dùng truy cập website, gửi yêu cầu chat
2. Hệ thống phân bổ nhân viên CSKH (hoặc người dùng tự chọn nhân viên CSKH)
3. Thiết lập kết nối WebSocket
4. Người dùng và nhân viên CSKH bắt đầu chat
   a. Gửi tin nhắn
   b. Nhận tin nhắn
   c. Cập nhật trạng thái đã đọc của tin nhắn
5. Kết thúc chat, lưu lịch sử chat
6. Đóng kết nối WebSocket
```

### 9.3 Sơ đồ quy trình chuyển tiếp CSKH

```
Quy trình chuyển tiếp CSKH:
1. Nhân viên CSKH hiện tại gửi yêu cầu chuyển tiếp
2. Hệ thống xác thực quyền chuyển tiếp
3. Chọn nhân viên CSKH đích
4. Gửi yêu cầu chuyển tiếp cho nhân viên CSKH đích
5. Nhân viên CSKH đích chấp nhận chuyển tiếp
6. Hệ thống cập nhật người phụ trách phiên hội thoại
7. Thông báo cho nhân viên CSKH hiện tại và nhân viên CSKH đích
8. Chuyển tiếp hoàn tất, giữ lại lịch sử chat
```

### 9.4 Danh sách quyền CSKH

| Tên quyền | Mô tả |
|----------|------|
| kefu_login | Quyền đăng nhập CSKH |
| kefu_chat | Quyền sử dụng chức năng trò chuyện |
| kefu_user | Quyền quản lý người dùng |
| kefu_order | Quyền xử lý đơn hàng |
| kefu_product | Quyền xem sản phẩm |
| kefu_speechcraft | Quyền quản lý câu trả lời mẫu |
| kefu_transfer | Quyền chuyển tiếp CSKH |

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
