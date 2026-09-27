# Tài liệu request API

## 1. Tổng quan

Tài liệu này mô tả quy trình, quy chuẩn, thiết kế tham số, định dạng phản hồi, v.v. của request API trong dự án CRMEB, nhằm thống nhất định dạng request API, nâng cao tính nhất quán và khả năng bảo trì của API.

## 2. Quy trình request API

### 2.1 Quy trình cơ bản

1. **Client gửi request**: Client gửi request tới máy chủ qua giao thức HTTP/HTTPS
2. **Request đến máy chủ**: Request được truyền qua mạng đến máy chủ
3. **Phân tích request**: Máy chủ phân tích request, bao gồm phương thức request, đường dẫn, tham số, v.v.
4. **Xác thực và phân quyền**: Máy chủ xác thực và phân quyền cho request
5. **Xử lý nghiệp vụ**: Máy chủ thực thi logic nghiệp vụ tương ứng
6. **Tạo phản hồi**: Máy chủ tạo dữ liệu phản hồi
7. **Trả về phản hồi**: Máy chủ trả phản hồi về cho client
8. **Client xử lý phản hồi**: Client xử lý phản hồi do máy chủ trả về

### 2.2 Quy trình chi tiết

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Client        │     │   Middleware    │     │ Controller       │
│ 1. Gửi yêu cầu  │────▶│ 2. Xác thực, phân quyền │────▶│ 3. Xử lý nghiệp vụ     │
└─────────────────┘     └─────────────────┘     └─────────────────┘
                                                      │
                                                      ▼
                                             ┌─────────────────┐
                                             │   Tầng service       │
                                             │ 4. Thực thi logic     │
                                             └─────────────────┘
                                                      │
                                                      ▼
                                             ┌─────────────────┐
                                             │   Tầng dữ liệu       │
                                             │ 5. Thao tác dữ liệu     │
                                             └─────────────────┘
                                                      │
                                                      ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Client        │     │   Middleware    │     │ Controller       │
│ 8. Xử lý phản hồi │◀────│ 7. Xử lý phản hồi │◀────│ 6. Tạo phản hồi     │
└─────────────────┘     └─────────────────┘     └─────────────────┘
```

## 3. Phương thức request

### 3.1 Phương thức HTTP

| Phương thức | Mô tả | Tính lũy đẳng (idempotent) | Tính an toàn (safe) |
|------|------|--------|--------|
| GET | Lấy tài nguyên | Có | Có |
| POST | Tạo tài nguyên | Không | Không |
| PUT | Cập nhật tài nguyên | Có | Không |
| DELETE | Xóa tài nguyên | Có | Không |
| PATCH | Cập nhật một phần tài nguyên | Không | Không |
| OPTIONS | Lấy các thao tác khả dụng trên tài nguyên | Có | Có |
| HEAD | Lấy metadata của tài nguyên | Có | Có |

### 3.2 Quy chuẩn sử dụng phương thức

- **GET**: Dùng để lấy tài nguyên, không được thay đổi trạng thái tài nguyên
- **POST**: Dùng để tạo tài nguyên mới
- **PUT**: Dùng để cập nhật toàn bộ tài nguyên, cần chứa biểu diễn đầy đủ của tài nguyên
- **DELETE**: Dùng để xóa tài nguyên
- **PATCH**: Dùng để cập nhật một phần tài nguyên, chỉ chứa các trường cần cập nhật
- **OPTIONS**: Dùng để lấy các phương thức HTTP mà tài nguyên hỗ trợ
- **HEAD**: Dùng để lấy metadata của tài nguyên, như Content-Length, Last-Modified, v.v.

## 4. Request header

### 4.1 Request header thông dụng

| Request header | Mô tả | Ví dụ |
|-------|------|------|
| Accept | Kiểu nội dung phản hồi mà client chấp nhận | application/json |
| Accept-Encoding | Kiểu mã hóa (encoding) mà client chấp nhận | gzip, deflate |
| Content-Type | Kiểu nội dung của request body | application/json |
| Authorization | Thông tin xác thực | Bearer {token} |
| User-Agent | Định danh client | Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 |
| X-Requested-With | Loại request | XMLHttpRequest |
| X-Token | Token xác thực (tùy chỉnh) | your_token_here |

### 4.2 Request header tùy chỉnh

Request header tùy chỉnh nên có tiền tố `X-`, ví dụ `X-Token`, `X-Request-ID`, v.v.

## 5. Tham số request

### 5.1 Vị trí tham số

| Vị trí | Trường hợp sử dụng | Ví dụ |
|------|----------|------|
| Đường dẫn URL | Định danh tài nguyên | /api/v1/user/1 |
| Chuỗi truy vấn (query string) | Lọc, sắp xếp, phân trang | /api/v1/user?page=1&limit=10&sort=create_time&order=desc |
| Request body | Dữ liệu phức tạp, tạo/cập nhật tài nguyên | {"username": "test", "password": "123456"} |
| Request header | Thông tin xác thực, metadata | Authorization: Bearer {token} |
| Cookie | Thông tin phiên (session) | PHPSESSID=your_session_id |

### 5.2 Quy chuẩn đặt tên tham số

- **Kiểu đặt tên**: Dùng kiểu đặt tên camelCase, ví dụ `userName`
- **Rõ ngữ nghĩa**: Tên tham số cần có ngữ nghĩa rõ ràng, ví dụ `page`, `limit`, `sort`
- **Ngắn gọn**: Tên tham số cần ngắn gọn, dễ hiểu, tránh quá dài
- **Nhất quán**: Các tham số cùng loại cần được giữ nhất quán giữa các API khác nhau

### 5.3 Tham số thường dùng

| Tên tham số | Loại | Mô tả | Ví dụ |
|-------|------|------|------|
| page | int | Số trang, mặc định 1 | page=1 |
| limit | int | Số lượng mỗi trang, mặc định 10 | limit=20 |
| sort | string | Trường sắp xếp | sort=create_time |
| order | string | Kiểu sắp xếp, asc hoặc desc | order=desc |
| keyword | string | Từ khóa tìm kiếm | keyword=test |
| status | int | Lọc theo trạng thái | status=1 |
| start_time | string | Thời gian bắt đầu | start_time=2024-01-01 |
| end_time | string | Thời gian kết thúc | end_time=2024-01-31 |

## 6. Request body

### 6.1 Kiểu nội dung

| Kiểu nội dung | Mô tả | Ví dụ |
|---------|------|------|
| application/json | Định dạng JSON, phổ biến nhất | {"username": "test", "password": "123456"} |
| application/x-www-form-urlencoded | Định dạng form | username=test&password=123456 |
| multipart/form-data | Tải tệp lên | Chứa file và các trường form |
| text/plain | Văn bản thuần | Dữ liệu văn bản đơn giản |
| application/xml | Định dạng XML | <user><username>test</username><password>123456</password></user> |

### 6.2 Quy chuẩn request body JSON

- **Đặt tên theo camelCase**: Tên trường dùng kiểu đặt tên camelCase
- **Kiểu dữ liệu rõ ràng**: Dùng kiểu dữ liệu phù hợp, như chuỗi, số, boolean, mảng, đối tượng
- **Tránh giá trị null**: Các trường không bắt buộc không nên chứa giá trị null
- **Cấu trúc lồng nhau**: Dùng cấu trúc lồng nhau hợp lý, tránh lồng quá sâu
- **Định dạng mảng**: Các phần tử trong mảng cần cùng kiểu

Ví dụ:

```json
{
  "username": "test",
  "password": "123456",
  "nickname": "Người dùng thử nghiệm",
  "age": 18,
  "gender": 1,
  "tags": ["tag1", "tag2"],
  "address": {
    "province": "Beijing",
    "city": "Beijing",
    "district": "Quận Chaoyang"
  }
}
```

## 7. Định dạng phản hồi

### 7.1 Định dạng phản hồi cơ bản

Mọi phản hồi API cần dùng định dạng JSON thống nhất, gồm các trường `status`, `msg` và trường tùy chọn `data`. Cách gọi thực tế trong hệ thống là `app('json')->success()`.

#### 7.1.1 Ví dụ cách gọi

```php
// Response thành công cơ bản
return app('json')->success('Thao tác thành công', ['id' => 1, 'username' => 'test']);

// Chỉ trả về dữ liệu, không chỉ định thông báo
return app('json')->success(['id' => 1, 'username' => 'test']);

// Dùng mã thành công tích hợp sẵn của hệ thống
return app('json')->success(100000); // 100000 Có "Lưu thành công" tương ứng với mã thành công tích hợp sẵn của hệ thống
```

#### 7.1.2 Định dạng phản hồi

```json
{
  "status": 200,
  "msg": "Thao tác thành công",
  "data": {
    "id": 1,
    "username": "test",
    "nickname": "Người dùng thử nghiệm"
  }
}
```

### 7.2 Định dạng phản hồi phân trang

Phản hồi phân trang cần gồm các trường `total`, `page`, `limit` và `list`, cách gọi thực tế trong hệ thống là `app('json')->success()`.

#### 7.2.1 Ví dụ cách gọi

```php
// Response dữ liệu phân trang
$pageData = [
    'total' => 100,
    'page' => 1,
    'limit' => 10,
    'list' => [
        ['id' => 1, 'username' => 'test1', 'nickname' => 'Người dùng thử nghiệm 1'],
        ['id' => 2, 'username' => 'test2', 'nickname' => 'Người dùng thử nghiệm 2']
    ]
];
return app('json')->success('Lấy danh sách thành công', $pageData);
```

#### 7.2.2 Định dạng phản hồi

```json
{
  "status": 200,
  "msg": "Lấy danh sách thành công",
  "data": {
    "total": 100,
    "page": 1,
    "limit": 10,
    "list": [
      {
        "id": 1,
        "username": "test1",
        "nickname": "Người dùng thử nghiệm 1"
      },
      {
        "id": 2,
        "username": "test2",
        "nickname": "Người dùng thử nghiệm 2"
      }
    ]
  }
}
```

### 7.3 Định dạng phản hồi lỗi

Hệ thống dùng định dạng phản hồi lỗi thống nhất, mọi phản hồi lỗi đều được trả về qua phương thức `app('json')->fail()`. Phương thức `fail()` hỗ trợ hai loại tham số:

- **Mã lỗi** (khuyến nghị): Mã lỗi dạng số có sẵn trong hệ thống, ví dụ `410025`
- **Thông báo lỗi**: Thông báo lỗi dạng chuỗi trực tiếp

#### 7.3.1 Định dạng phản hồi thống nhất

Dù dùng loại tham số nào, hệ thống đều trả về định dạng JSON thống nhất, gồm các trường `status`, `msg` và trường tùy chọn `data`. Khi dùng mã lỗi, phản hồi còn có thêm trường `code`.

```json
{
  "status": 400,
  "msg": "Tài khoản hoặc mật khẩu không đúng",
  "code": 410025,
  "data": null
}
```

#### 7.3.2 Ví dụ cách gọi

```php
// Khuyến nghị: dùng mã lỗi tích hợp sẵn của hệ thống
return app('json')->fail(410025);

// Không khuyến nghị: dùng trực tiếp thông báo lỗi
return app('json')->fail('Tài khoản hoặc mật khẩu không đúng');

// Dùng mã lỗi và truyền thêm dữ liệu
return app('json')->fail(410025, ['extra' => 'additional data']);

// Dùng mã lỗi và truyền tham số thay thế
return app('json')->fail(410025, [], ['field' => 'username']);
```

#### 7.3.3 Thực tiễn tốt nhất

- **Ưu tiên dùng mã lỗi**: Mã lỗi có sẵn trong hệ thống đã được quy hoạch thống nhất, thuận tiện cho việc bảo trì và quốc tế hóa
- **Tránh dùng chuỗi trực tiếp**: Dùng trực tiếp chuỗi thông báo lỗi sẽ gây bất lợi cho việc quốc tế hóa và quản lý thống nhất
- **Truyền dữ liệu bổ sung cần thiết**: với các lỗi phức tạp, có thể cung cấp thông tin chi tiết trong trường `data`
- **Sử dụng tham số thay thế**: với thông báo lỗi động, sử dụng tham số thay thế để tăng tính linh hoạt

### 7.4 Quy chuẩn mã phản hồi

| Dải mã phản hồi | Loại | Mô tả | Ví dụ |
|-----------|------|------|------|
| 200 | Thành công | Thao tác thành công | 200 |
| 1000-1999 | Lỗi cấp hệ thống | Lỗi lõi hệ thống | 1001 (tham số không hợp lệ) |
| 4000-4999 | Lỗi cấp nghiệp vụ | Lỗi logic nghiệp vụ cụ thể | 410025 (sai tài khoản hoặc mật khẩu) |
| 400 | Lỗi phía client | Tham số request không hợp lệ | 400 |
| 401 | Lỗi xác thực | Chưa xác thực hoặc xác thực đã hết hạn | 401 |
| 403 | Lỗi phân quyền | Không có quyền truy cập | 403 |
| 404 | Lỗi tài nguyên | Tài nguyên không tồn tại | 404 |
| 500 | Lỗi máy chủ | Lỗi máy chủ nội bộ | 500 |

## 8. Xử lý lỗi

### 8.1 Thiết kế phản hồi lỗi

Phản hồi lỗi cần tuân theo các nguyên tắc thiết kế sau:

1. **Định dạng thống nhất**: mọi phản hồi lỗi đều sử dụng cùng một định dạng JSON
2. **Mã lỗi rõ ràng**: sử dụng mã lỗi duy nhất để phân biệt các loại lỗi khác nhau
3. **Thông báo lỗi rõ ràng**: thông báo lỗi cần ngắn gọn, rõ ràng, dễ hiểu
4. **Thông tin chi tiết tùy chọn**: với các lỗi phức tạp, có thể cung cấp thông tin chi tiết trong trường `data`
5. **Khớp với mã trạng thái HTTP**: mã lỗi cần khớp với mã trạng thái HTTP

### 8.2 AI tự động gợi ý mã lỗi tích hợp sẵn của hệ thống

Hệ thống CRMEB đã tích hợp tính năng gợi ý tự động bằng AI, khi lập trình viên sử dụng phương thức `fail()` để trả về thông báo lỗi trong lúc viết code, AI sẽ tự động:

1. **Nhận diện thông báo lỗi**: tự động nhận diện chuỗi thông báo lỗi do lập trình viên nhập
2. **Đối chiếu mã lỗi tích hợp sẵn**: tìm mã lỗi phù hợp trong kho mã lỗi tích hợp sẵn của hệ thống
3. **Tự động chuyển đổi**: khi chạy (runtime), tự động chuyển thông báo lỗi thành mã lỗi tích hợp sẵn tương ứng của hệ thống
4. **Đưa ra gợi ý**: với thông báo lỗi chưa khớp được, đề xuất các mã lỗi tương tự
5. **Gợi ý theo thời gian thực**: hiển thị gợi ý mã lỗi theo thời gian thực trong IDE
6. **Tài liệu mã lỗi**: tìm phần mô tả mã lỗi tương ứng trong tài liệu mã lỗi error_code.md

#### 8.2.1 Ví dụ tính năng

```php
// Lập trình viên nhập vào
return $this->fail('Đăng nhập thất bại');

// AI tự động gợi ý và thay thế bằng
return $this->fail(410019); // 410019 Có "Đăng nhập thất bại" tương ứng với mã lỗi tích hợp sẵn của hệ thống
```

#### 8.2.2 Nguyên lý hoạt động

1. **Phân tích tệp ngôn ngữ**: khi chạy, hệ thống phân tích tệp ngôn ngữ và xây dựng bảng ánh xạ từ mã lỗi sang thông báo lỗi
2. **Tự động đối chiếu**: khi gọi phương thức `fail()` và truyền vào thông báo lỗi dạng chuỗi, hệ thống tự động tìm mã lỗi phù hợp trong bảng ánh xạ
3. **Chuyển đổi khi chạy**: nếu tìm thấy mã lỗi phù hợp, hệ thống sẽ thay thông báo lỗi bằng mã lỗi và đưa trường `code` vào phản hồi
4. **Gợi ý thông minh**: trong IDE, AI sẽ nhắc lập trình viên theo thời gian thực để sử dụng đúng mã lỗi tích hợp sẵn của hệ thống

#### 8.2.3 Định dạng phản hồi thực tế

Khi sử dụng thông báo lỗi dạng chuỗi, hệ thống sẽ tự động đối chiếu và chuyển thành mã lỗi, định dạng phản hồi cuối cùng là:

```json
{
  "status": 400,
  "msg": "Đăng nhập thất bại",
  "code": 410019
}
```

Khi sử dụng trực tiếp mã lỗi, định dạng phản hồi là:

```json
{
  "status": 400,
  "msg": "Đăng nhập thất bại",
  "code": 410019
}
```

### 8.3 Thực tiễn tốt nhất khi xử lý lỗi

1. **Sử dụng mã lỗi tích hợp sẵn của hệ thống**: ưu tiên sử dụng mã lỗi tích hợp sẵn của hệ thống, tránh tự định nghĩa thông báo lỗi
2. **Bản địa hóa thông báo lỗi**: sử dụng hàm `getLang()` để lấy thông báo lỗi đã được bản địa hóa
3. **Log lỗi chi tiết**: ghi log lỗi chi tiết, bao gồm mã lỗi, thông báo lỗi, tham số request, v.v.
4. **Thông báo lỗi thân thiện**: trả về thông báo lỗi thân thiện cho client, tránh để lộ chi tiết nội bộ của hệ thống
5. **Xử lý lỗi thống nhất**: sử dụng một middleware xử lý lỗi chung để xử lý mọi lỗi
6. **Tài liệu hóa mã lỗi**: định kỳ cập nhật tài liệu mã lỗi, đảm bảo khớp với code thực tế

### 8.4 Mã lỗi tùy chỉnh

Với các tình huống nghiệp vụ mà mã lỗi tích hợp sẵn của hệ thống không bao quát được, có thể tự định nghĩa mã lỗi nhưng cần tuân theo các quy chuẩn sau:

1. **Dải mã lỗi**: sử dụng dải mã lỗi mà hệ thống chưa dùng đến
2. **Quy tắc đặt tên**: mã lỗi cần có ngữ nghĩa rõ ràng
3. **Tài liệu hóa**: mã lỗi tùy chỉnh cần được mô tả rõ trong tài liệu
4. **Bản địa hóa**: mã lỗi tùy chỉnh cần có thông báo lỗi tương ứng được định nghĩa trong tệp ngôn ngữ

## 9. Quy trình phát triển API

### 9.1 Phân tích yêu cầu và thiết kế

1. **Nắm rõ yêu cầu**: xác định rõ yêu cầu nghiệp vụ và yêu cầu chức năng của API
2. **Thiết kế tài nguyên**: xác định các tài nguyên và mô hình dữ liệu mà API liên quan
3. **Thiết kế API**: thiết kế URL, phương thức request, tham số và định dạng phản hồi của API
4. **Thiết kế phân quyền**: xác định quyền truy cập và phương thức xác thực của API
5. **Thiết kế lỗi**: định nghĩa các trường hợp lỗi có thể xảy ra và mã lỗi của API

### 9.2 Phát triển và hiện thực

1. **Tạo route**: định nghĩa route của API trong tệp route
2. **Hiện thực controller**: tạo controller và hiện thực logic của API
3. **Kiểm tra tham số**: sử dụng validator để kiểm tra tham số request
4. **Logic nghiệp vụ**: hiện thực logic nghiệp vụ của API
5. **Xử lý lỗi**: sử dụng `app('json')->fail()` để xử lý lỗi thống nhất
6. **Trả về phản hồi**: sử dụng `app('json')->success()` để trả về phản hồi thống nhất

### 9.3 Kiểm thử và xác minh

1. **Kiểm thử đơn vị**: viết unit test để kiểm tra chức năng của API
2. **Kiểm thử tích hợp**: kiểm thử việc tích hợp giữa API và các module khác
3. **Kiểm thử API**: sử dụng Postman hoặc công cụ khác để kiểm thử API
4. **Kiểm thử hiệu năng**: kiểm thử hiệu năng và thời gian phản hồi của API
5. **Kiểm thử bảo mật**: kiểm thử tính bảo mật của API

### 9.4 Viết tài liệu

1. **Tài liệu API**: viết tài liệu chi tiết cho API
2. **Tài liệu mã lỗi**: ghi lại các mã lỗi mà API sử dụng trong `error_code.md`
3. **Nhật ký thay đổi**: ghi lại lịch sử thay đổi của API

### 9.5 Đưa vào vận hành

1. **Rà soát code**: Tiến hành rà soát code (code review), đảm bảo chất lượng code
2. **Kiểm tra trên môi trường kiểm thử**: kiểm tra chức năng API trên môi trường kiểm thử
3. **Phát hành từng phần (canary)**: phát hành API theo từng phần và theo dõi tình hình vận hành
4. **Vận hành chính thức**: đưa API vào vận hành chính thức

### 9.6 Giám sát và bảo trì

1. **Giám sát**: giám sát trạng thái hoạt động và hiệu năng của API
2. **Log**: ghi log truy cập và log lỗi của API
3. **Tối ưu**: tối ưu hiệu năng API dựa trên dữ liệu giám sát
4. **Bảo trì**: định kỳ bảo trì và cập nhật API

## 10. Thực tiễn tốt nhất

### 10.1 Thiết kế request

- **Sử dụng phong cách RESTful**: tuân theo quy chuẩn thiết kế RESTful API
- **Đặt tên tài nguyên rõ ràng**: sử dụng tên tài nguyên rõ ràng, ví dụ `/api/v1/user` thay vì `/api/v1/getUser`
- **Phân cấp URL hợp lý**: URL không nên phân cấp quá sâu, thường không quá 3 cấp
- **Sử dụng dạng số nhiều**: tên tài nguyên dùng dạng số nhiều, ví dụ `/api/v1/users` thay vì `/api/v1/user`

### 10.2 Thiết kế tham số

- **Tham số bắt buộc**: đánh dấu rõ các tham số bắt buộc
- **Giá trị mặc định**: cung cấp giá trị mặc định hợp lý cho các tham số tùy chọn
- **Kiểm tra tham số**: kiểm tra tính hợp lệ của mọi tham số
- **Kiểu tham số**: xác định rõ kiểu và định dạng của tham số

### 10.3 Thiết kế phản hồi

- **Định dạng thống nhất**: sử dụng định dạng phản hồi thống nhất
- **Kiểu dữ liệu rõ ràng**: kiểu dữ liệu của phản hồi phải rõ ràng
- **Tránh dữ liệu dư thừa**: chỉ trả về dữ liệu cần thiết
- **Phản hồi phân trang**: API danh sách cần hỗ trợ phân trang
- **Thông báo lỗi**: thông báo lỗi phải rõ ràng, chính xác

### 10.4 Thiết kế bảo mật

- **Xác thực và phân quyền**: sử dụng JWT hoặc OAuth2 để xác thực và phân quyền
- **HTTPS**: sử dụng HTTPS để mã hóa đường truyền
- **Kiểm tra tham số**: kiểm tra chặt chẽ mọi tham số đầu vào
- **Mã hóa đầu ra**: mã hóa (encode) dữ liệu đầu ra để chống tấn công XSS
- **Chống SQL injection**: Dùng parameter binding, tránh nối chuỗi SQL trực tiếp
- **Chống CSRF**: triển khai cơ chế xác thực CSRF Token

## 11. Sự cố thường gặp

### 11.1 Vấn đề truy cập chéo miền (CORS)

- **Vấn đề**: Chính sách cùng nguồn gốc (same-origin policy) của trình duyệt khiến request chéo miền bị thất bại
- **Giải pháp**: Áp dụng CORS (chia sẻ tài nguyên chéo miền), thiết lập header phản hồi phù hợp

### 10.2 Xác thực thất bại

- **Vấn đề**: request không kèm thông tin xác thực hoặc thông tin xác thực không hợp lệ
- **Cách khắc phục**: kiểm tra thông tin xác thực trong header của request, đảm bảo token còn hiệu lực

### 10.3 Tham số không hợp lệ

- **Vấn đề**: tham số request sai định dạng hoặc thiếu tham số bắt buộc
- **Cách khắc phục**: kiểm tra tham số request, đảm bảo đúng định dạng và có đủ mọi tham số bắt buộc

### 10.4 Dữ liệu phản hồi không như mong đợi

- **Vấn đề**: định dạng hoặc nội dung dữ liệu phản hồi không như mong đợi
- **Cách khắc phục**: xem lại tài liệu API, đảm bảo định dạng request đúng, hoặc liên hệ bên cung cấp API

### 10.5 Vấn đề hiệu năng

- **Vấn đề**: thời gian phản hồi của request API quá lâu
- **Giải pháp**: Tối ưu phần hiện thực API, sử dụng cache, giảm số lần truy vấn cơ sở dữ liệu

## 12. Tài liệu tham khảo

- [Hướng dẫn thiết kế RESTful API](https://restfulapi.net/)
- [Mã trạng thái HTTP](https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Status)
- [Thực tiễn tốt nhất khi thiết kế API](https://cloud.google.com/apis/design)
- [Đặc tả JSON API](https://jsonapi.org/)
- [Đặc tả OpenAPI](https://swagger.io/specification/)
- [Thực tiễn tốt nhất về bảo mật API](https://owasp.org/www-project-api-security/)