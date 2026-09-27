# Tài liệu mã lỗi

## 1. Tổng quan

Tài liệu này mô tả quy chuẩn mã lỗi của dự án CRMEB, bao gồm cách phân loại, định nghĩa, cách sử dụng mã lỗi, v.v., nhằm thống nhất định dạng mã lỗi, nâng cao tính nhất quán và khả năng bảo trì của việc xử lý lỗi.

## 2. Phân loại mã lỗi

### 2.1 Mã trạng thái HTTP

- **1xx**: Mã trạng thái thông tin, cho biết yêu cầu đã được tiếp nhận và đang tiếp tục xử lý
- **2xx**: Mã trạng thái thành công, cho biết yêu cầu đã được xử lý thành công
- **3xx**: Mã trạng thái chuyển hướng, cho biết cần thao tác thêm để hoàn tất yêu cầu
- **4xx**: Mã trạng thái lỗi phía client, cho biết yêu cầu có lỗi cú pháp hoặc không thể hoàn tất yêu cầu
- **5xx**: Mã trạng thái lỗi máy chủ, cho biết máy chủ gặp lỗi khi xử lý yêu cầu

### 2.2 Mã lỗi nghiệp vụ

Mã lỗi nghiệp vụ gồm 5 chữ số, có định dạng `XXXXX`, trong đó:

- **Chữ số đầu tiên**: Ký hiệu loại lỗi
  - `1`: Lỗi hệ thống
  - `2`: Lỗi nghiệp vụ
  - `3`: Lỗi tham số
  - `4`: Lỗi phân quyền
  - `5`: Lỗi tài nguyên
  - `6`: Lỗi cơ sở dữ liệu
  - `7`: Lỗi dịch vụ bên thứ ba
  - `8`: Lỗi khác

- **Bốn chữ số cuối**: Mã lỗi cụ thể, tăng dần bắt đầu từ 0000

## 3. Mã lỗi hệ thống

### 3.1 Lỗi hệ thống (1xxxxx)

| Mã lỗi | Mô tả | Mã trạng thái HTTP |
|-------|------|-------------|
| 10001 | Lỗi nội bộ hệ thống | 500 |
| 10002 | Hệ thống đang bảo trì | 503 |
| 10003 | Hệ thống đang bận | 503 |
| 10004 | Dịch vụ không khả dụng | 503 |
| 10005 | Lỗi gateway | 502 |

### 3.2 Lỗi nghiệp vụ (2xxxxx)

| Mã lỗi | Mô tả | Mã trạng thái HTTP |
|-------|------|-------------|
| 20001 | Thao tác thất bại | 400 |
| 20002 | Lỗi logic nghiệp vụ | 400 |
| 20003 | Dữ liệu đã tồn tại | 400 |
| 20004 | Dữ liệu không tồn tại | 404 |
| 20005 | Thao tác không được phép | 403 |

### 3.3 Lỗi tham số (3xxxxx)

| Mã lỗi | Mô tả | Mã trạng thái HTTP |
|-------|------|-------------|
| 30001 | Tham số không được để trống | 400 |
| 30002 | Tham số sai định dạng | 400 |
| 30003 | Tham số sai kiểu dữ liệu | 400 |
| 30004 | Tham số vượt quá phạm vi | 400 |
| 30005 | Xác thực tham số thất bại | 422 |

### 3.4 Lỗi phân quyền (4xxxxx)

| Mã lỗi | Mô tả | Mã trạng thái HTTP |
|-------|------|-------------|
| 40001 | Chưa đăng nhập | 401 |
| 40002 | Phiên đăng nhập đã hết hạn | 401 |
| 40003 | Không có quyền truy cập | 403 |
| 40004 | Không đủ quyền | 403 |
| 40005 | Token không hợp lệ | 401 |

### 3.5 Lỗi tài nguyên (5xxxxx)

| Mã lỗi | Mô tả | Mã trạng thái HTTP |
|-------|------|-------------|
| 50001 | Tài nguyên không tồn tại | 404 |
| 50002 | Tài nguyên đã bị xóa | 404 |
| 50003 | Tài nguyên đã bị khóa | 400 |
| 50004 | Không đủ tài nguyên | 400 |
| 50005 | Tài nguyên đã hết hạn | 400 |

### 3.6 Lỗi cơ sở dữ liệu (6xxxxx)

| Mã lỗi | Mô tả | Mã trạng thái HTTP |
|-------|------|-------------|
| 60001 | Kết nối cơ sở dữ liệu thất bại | 500 |
| 60002 | Truy vấn cơ sở dữ liệu thất bại | 500 |
| 60003 | Cập nhật cơ sở dữ liệu thất bại | 500 |
| 60004 | Chèn dữ liệu vào cơ sở dữ liệu thất bại | 500 |
| 60005 | Xóa dữ liệu trong cơ sở dữ liệu thất bại | 500 |
| 60006 | Giao dịch cơ sở dữ liệu (transaction) thất bại | 500 |

### 3.7 Lỗi dịch vụ bên thứ ba (7xxxxx)

| Mã lỗi | Mô tả | Mã trạng thái HTTP |
|-------|------|-------------|
| 70001 | Kết nối dịch vụ bên thứ ba thất bại | 500 |
| 70002 | Hết thời gian chờ dịch vụ bên thứ ba | 504 |
| 70003 | Dịch vụ bên thứ ba trả về lỗi | 500 |
| 70004 | Xác thực dịch vụ bên thứ ba thất bại | 401 |
| 70005 | Dịch vụ bên thứ ba giới hạn tần suất truy cập (rate limit) | 429 |

## 4. Mã lỗi theo module

### 4.1 Module người dùng (801xx)

| Mã lỗi | Mô tả | Mã trạng thái HTTP |
|-------|------|-------------|
| 80101 | Tên đăng nhập hoặc mật khẩu không đúng | 401 |
| 80102 | Người dùng không tồn tại | 404 |
| 80103 | Người dùng đã tồn tại | 400 |
| 80104 | Trạng thái người dùng bất thường | 400 |
| 80105 | Mã xác thực không đúng | 400 |
| 80106 | Mã xác thực đã hết hạn | 400 |
| 80107 | Số điện thoại sai định dạng | 400 |
| 80108 | Email sai định dạng | 400 |

### 4.2 Module sản phẩm (802xx)

| Mã lỗi | Mô tả | Mã trạng thái HTTP |
|-------|------|-------------|
| 80201 | Sản phẩm không tồn tại | 404 |
| 80202 | Sản phẩm đã ngừng bán | 400 |
| 80203 | Sản phẩm không đủ tồn kho | 400 |
| 80204 | Giá sản phẩm bất thường | 400 |
| 80205 | Danh mục sản phẩm không tồn tại | 404 |

### 4.3 Module đơn hàng (803xx)

| Mã lỗi | Mô tả | Mã trạng thái HTTP |
|-------|------|-------------|
| 80301 | Đơn hàng không tồn tại | 404 |
| 80302 | Trạng thái đơn hàng bất thường | 400 |
| 80303 | Đơn hàng đã bị hủy | 400 |
| 80304 | Đơn hàng đã hoàn thành | 400 |
| 80305 | Thanh toán đơn hàng thất bại | 400 |
| 80306 | Đơn hàng quá hạn thanh toán | 400 |

### 4.4 Module thanh toán (804xx)

| Mã lỗi | Mô tả | Mã trạng thái HTTP |
|-------|------|-------------|
| 80401 | Phương thức thanh toán không được hỗ trợ | 400 |
| 80402 | Số tiền thanh toán bất thường | 400 |
| 80403 | Tham số thanh toán không hợp lệ | 400 |
| 80404 | Thanh toán thất bại | 400 |
| 80405 | Hết thời gian thanh toán | 400 |
| 80406 | Thanh toán đã hoàn tất | 400 |

## 5. Quy chuẩn sử dụng mã lỗi

### 5.1 Định dạng phản hồi lỗi

```json
{
  "code": 20001,
  "msg": "Thao tác thất bại",
  "data": []
}
```

### 5.2 Định dạng phản hồi thành công

```json
{
  "code": 200,
  "msg": "Thao tác thành công",
  "data": {...}
}
```

### 5.3 Quy trình xử lý lỗi

1. **Bắt ngoại lệ**: Bắt ngoại lệ trong controller hoặc middleware
2. **Xác định mã lỗi**: Xác định mã lỗi tương ứng dựa trên loại ngoại lệ
3. **Tạo phản hồi**: Tạo phản hồi lỗi theo định dạng thống nhất
4. **Trả về phản hồi**: Trả phản hồi lỗi về cho client

### 5.4 Ghi log lỗi

- **Nội dung ghi log**: Mã lỗi, thông báo lỗi, tham số yêu cầu, đường dẫn yêu cầu, thông tin người dùng, timestamp, v.v.
- **Mức ghi log**: Chọn mức log phù hợp theo mức độ nghiêm trọng của lỗi
- **Vị trí ghi log**: Tệp log hệ thống
- **Giám sát và cảnh báo**: Đối với lỗi nghiêm trọng, kích hoạt cơ chế cảnh báo

## 6. Quản lý mã lỗi

### 6.1 Định nghĩa mã lỗi

Mã lỗi được định nghĩa trong tệp cấu hình để thuận tiện cho việc quản lý và bảo trì tập trung:

```php
// config/error_code.php
return [
    // Lỗi hệ thống
    10001 => 'Lỗi nội bộ hệ thống',
    10002 => 'Hệ thống đang bảo trì',
    // Lỗi nghiệp vụ
    20001 => 'Thao tác thất bại',
    20002 => 'Lỗi logic nghiệp vụ',
    // Tham số không hợp lệ
    30001 => 'Tham số không được để trống',
    30002 => 'Tham số sai định dạng',
    // ...
];
```

### 6.2 Sử dụng mã lỗi

Khi sử dụng mã lỗi trong code, nên tham chiếu trực tiếp đến định nghĩa trong tệp cấu hình:

```php
// Dùng trong controller
return $this->fail(config('error_code.20001'));

// Hoặc dùng trực tiếp mã lỗi
return $this->fail('Thao tác thất bại', [], 20001);
```

### 6.3 Cập nhật mã lỗi

Khi cần thêm mã lỗi mới, cần tuân theo quy trình sau:

1. **Xác định loại lỗi**: Xác định loại lỗi dựa trên bản chất của lỗi
2. **Cấp mã lỗi**: Cấp một mã lỗi chưa được sử dụng trong dải mã lỗi của loại tương ứng
3. **Cập nhật cấu hình**: Thêm định nghĩa mã lỗi vào tệp cấu hình `error_code.php`
4. **Cập nhật tài liệu**: Cập nhật tài liệu mã lỗi
5. **Thông báo cho nhóm**: Thông báo cho các thành viên trong nhóm về mã lỗi mới được thêm

## 7. Thực tiễn tốt nhất về mã lỗi

### 7.1 Nguyên tắc thiết kế

- **Tính duy nhất**: Mỗi mã lỗi chỉ tương ứng với duy nhất một trường hợp lỗi
- **Tính dễ đọc**: Mã lỗi cần dễ hiểu và dễ nhớ
- **Khả năng mở rộng**: Mã lỗi cần có khả năng mở rộng tốt, thuận tiện cho việc thêm mã lỗi mới
- **Tính nhất quán**: Định dạng và cách sử dụng mã lỗi cần được giữ nhất quán
- **Tính chi tiết**: Thông báo lỗi cần rõ ràng, chính xác, thuận tiện cho việc debug và xác định vấn đề

### 7.2 Khuyến nghị sử dụng

- **Tránh hardcode**: Mã lỗi cần được định nghĩa trong tệp cấu hình, tránh hardcode trực tiếp trong code
- **Xử lý thống nhất**: Dùng middleware xử lý lỗi thống nhất để xử lý lỗi
- **Log chi tiết**: Ghi log lỗi chi tiết, thuận tiện cho việc debug và phân tích
- **Thông báo thân thiện**: Trả về cho client thông báo lỗi thân thiện, tránh để lộ chi tiết nội bộ của hệ thống
- **Dọn dẹp định kỳ**: Định kỳ loại bỏ các mã lỗi không còn sử dụng, giữ cho bộ mã lỗi gọn gàng

### 7.3 Vấn đề thường gặp

#### 7.3.1 Xung đột mã lỗi

- **Vấn đề**: Các module khác nhau dùng cùng một mã lỗi
- **Giải pháp**: Phân chia dải mã lỗi nghiêm ngặt theo module để tránh xung đột

#### 7.3.2 Thông báo lỗi không rõ ràng

- **Vấn đề**: Thông báo lỗi quá sơ sài, không thể xác định được vấn đề
- **Giải pháp**: Cung cấp thông báo lỗi chi tiết, kèm theo ngữ cảnh cần thiết

#### 7.3.3 Mã lỗi không được cập nhật kịp thời

- **Vấn đề**: Khi thêm chức năng mới, không bổ sung kịp thời mã lỗi tương ứng
- **Giải pháp**: Khi phát triển chức năng mới, đồng thời cập nhật định nghĩa mã lỗi và tài liệu

## 8. Tài liệu tham khảo

- [Mã trạng thái HTTP](https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Status)
- [Xử lý lỗi RESTful API](https://restfulapi.net/http-status-codes/)
- [Thực tiễn tốt nhất khi thiết kế mã lỗi](https://www.thoughtworks.com/insights/blog/error-handling-microservices)
- [Quy chuẩn mã lỗi API](https://cloud.google.com/apis/design/errors)