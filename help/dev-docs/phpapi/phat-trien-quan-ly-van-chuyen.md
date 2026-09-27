# Tài liệu phát triển quản lý vận chuyển

## 1. Tổng quan tài liệu

### 1.1 Mục đích tài liệu
Tài liệu này nhằm mô tả chi tiết kiến trúc thiết kế, chức năng cốt lõi, cách triển khai code và quy chuẩn phát triển của module quản lý vận chuyển trong hệ thống CRMEB, cung cấp tài liệu tham khảo phát triển đầy đủ cho lập trình viên hệ thống.

### 1.2 Phạm vi áp dụng
Tài liệu này dành cho lập trình viên, nhân viên bảo trì và nhà phát triển thứ cấp của hệ thống CRMEB, dùng để hướng dẫn công việc phát triển, mở rộng và bảo trì module quản lý vận chuyển.

### 1.3 Định nghĩa thuật ngữ
| Thuật ngữ | Giải thích |
|------|------|
| Đơn vị vận chuyển | Công ty cung cấp dịch vụ vận chuyển và giao hàng, như SF Express, ZTO Express, YTO Express, v.v. |
| Mẫu vận chuyển | Mẫu định nghĩa quy tắc tính phí vận chuyển của sản phẩm, bao gồm cách tính phí, quy tắc giao hàng theo khu vực, v.v. |
| Vận đơn điện tử | Phiếu gửi hàng được tạo trực tuyến, có thể in ra và dùng ngay |
| Nhân viên giao hàng | Người phụ trách giao đơn hàng |
| Mã vận đơn | Mã định danh duy nhất của kiện hàng chuyển phát, dùng để tra cứu thông tin vận chuyển |
| Giao hàng hàng loạt | Thao tác giao hàng cho nhiều đơn hàng trong một lần xử lý |

## 2. Các mô-đun chức năng cốt lõi

### 2.1 Quản lý đơn vị vận chuyển
- **Danh sách đơn vị vận chuyển**: Hiển thị tất cả đơn vị vận chuyển, hỗ trợ tìm kiếm, phân trang
- **Thêm đơn vị vận chuyển**: Thêm đơn vị vận chuyển mới
- **Sửa đơn vị vận chuyển**: Chỉnh sửa thông tin đơn vị vận chuyển, bao gồm tên, mã, trạng thái, v.v.
- **Xóa đơn vị vận chuyển**: Xóa đơn vị vận chuyển
- **Quản lý trạng thái đơn vị vận chuyển**: Bật/tắt đơn vị vận chuyển
- **Đồng bộ đơn vị vận chuyển từ nền tảng**: Đồng bộ thông tin đơn vị vận chuyển mới nhất từ nền tảng vận chuyển
- **API tra cứu thông tin vận chuyển**: Cung cấp API tra cứu thông tin vận chuyển

### 2.2 Quản lý mẫu vận chuyển
- **Danh sách mẫu vận chuyển**: Hiển thị tất cả mẫu vận chuyển, hỗ trợ tìm kiếm, phân trang
- **Thêm mẫu vận chuyển**: Thêm mẫu vận chuyển mới
- **Sửa mẫu vận chuyển**: Chỉnh sửa thông tin mẫu vận chuyển
- **Xóa mẫu vận chuyển**: Xóa mẫu vận chuyển
- **Thiết lập cách tính phí**: Hỗ trợ tính phí theo số lượng, theo khối lượng, theo thể tích
- **Thiết lập giao hàng theo khu vực**: Thiết lập quy tắc giao hàng cho từng khu vực
- **Thiết lập quy tắc miễn phí vận chuyển**: Thiết lập điều kiện miễn phí vận chuyển cho khu vực chỉ định
- **Thiết lập khu vực không giao hàng**: Thiết lập các khu vực không giao hàng tới
- **Quản lý thứ tự sắp xếp và trạng thái mẫu**: Điều chỉnh thứ tự sắp xếp và trạng thái của mẫu

### 2.3 Quản lý giao hàng đơn hàng
- **Giao hàng đơn hàng**: Xử lý giao hàng cho đơn hàng, hỗ trợ giao hàng qua chuyển phát, nhận tại cửa hàng
- **Tra cứu thông tin vận chuyển**: Tra cứu thông tin vận chuyển của đơn hàng
- **Giao hàng hàng loạt**: Hỗ trợ xử lý giao hàng cho nhiều đơn hàng cùng lúc
- **In vận đơn điện tử**: Tạo và in vận đơn điện tử trực tuyến
- **Quản lý nhân viên giao hàng**: Thêm, sửa, xóa nhân viên giao hàng
- **Quản lý mã vận đơn**: Quản lý mã vận đơn của đơn hàng
- **Tách đơn giao hàng**: Tách một đơn hàng thành nhiều kiện hàng để giao
- **Shop gửi hàng**: Hỗ trợ shop đặt đơn gửi hàng qua hệ thống

## 3. Cấu trúc và thiết kế code

### 3.1 Thiết kế kiến trúc
Hệ thống sử dụng thiết kế kiến trúc phân tầng, tuân thủ nghiêm ngặt mẫu thiết kế MVC:
```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    Controllers  │     │    Services     │     │     Daos        │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                         ┌─────────────────┐
                         │    Models       │
                         └─────────────────┘
                                 │
                         ┌─────────────────┐
                         │   Cơ sở dữ liệu          │
                         └─────────────────┘
```

### 3.2 Cấu trúc tệp cốt lõi

#### 3.2.1 Quản lý đơn vị vận chuyển
- **Controller**: `crmeb/app/adminapi/controller/v1/freight/Express.php`
- **Tầng dịch vụ**: `crmeb/app/services/shipping/ExpressServices.php`
- **Tầng truy cập dữ liệu**: `crmeb/app/dao/shipping/ExpressDao.php`
- **Cấu hình route**: `crmeb/app/adminapi/route/freight.php`

#### 3.2.2 Quản lý mẫu vận chuyển
- **Controller**: `crmeb/app/adminapi/controller/v1/setting/ShippingTemplates.php`
- **Tầng dịch vụ**: `crmeb/app/services/shipping/ShippingTemplatesServices.php`
- **Tầng truy cập dữ liệu**: `crmeb/app/dao/shipping/ShippingTemplatesDao.php`
- **Model**: `crmeb/app/model/shipping/ShippingTemplates.php`

#### 3.2.3 Quản lý giao hàng đơn hàng
- **Controller đơn hàng**: `crmeb/app/adminapi/controller/v1/order/StoreOrder.php`
- **Controller quản lý nhân viên giao hàng**: `crmeb/app/adminapi/controller/v1/order/DeliveryService.php`
- **Tầng dịch vụ giao hàng**: `crmeb/app/services/order/DeliveryServiceServices.php`
- **Dịch vụ giao hàng đơn hàng**: `crmeb/app/services/order/StoreOrderDeliveryServices.php`

## 4. Thiết kế cơ sở dữ liệu

### 4.1 Bảng đơn vị vận chuyển (express)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID đơn vị vận chuyển |
| name | varchar(50) | Tên đơn vị vận chuyển |
| code | varchar(20) | Mã đơn vị vận chuyển |
| logo | varchar(255) | Logo đơn vị vận chuyển |
| sort | int(10) | Thứ tự sắp xếp |
| status | tinyint(1) | Trạng thái (0-vô hiệu hóa, 1-kích hoạt) |
| is_show | tinyint(1) | Có hiển thị không (0-không hiển thị, 1-hiển thị) |
| add_time | int(10) | Thời gian thêm |

### 4.2 Bảng mẫu vận chuyển (shipping_templates)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID mẫu |
| name | varchar(50) | Tên mẫu |
| type | tinyint(1) | Cách tính phí (0-theo số lượng, 1-theo khối lượng, 2-theo thể tích) |
| sregion | text | Khu vực giao hàng (định dạng JSON) |
| srules | text | Quy tắc giao hàng (định dạng JSON) |
| sort | int(10) | Thứ tự sắp xếp |
| status | tinyint(1) | Trạng thái (0-vô hiệu hóa, 1-kích hoạt) |
| add_time | int(10) | Thời gian thêm |

### 4.3 Bảng khu vực của mẫu vận chuyển (shipping_templates_region)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID khu vực |
| temp_id | int(10) | ID mẫu |
| region | text | Khu vực (định dạng JSON) |
| first | decimal(10,2) | Số lượng đầu/khối lượng đầu/thể tích đầu |
| first_price | decimal(10,2) | Giá cho số lượng đầu/khối lượng đầu/thể tích đầu |
| continue | decimal(10,2) | Số lượng tiếp theo/khối lượng tiếp theo/thể tích tiếp theo |
| continue_price | decimal(10,2) | Giá cho số lượng tiếp theo/khối lượng tiếp theo/thể tích tiếp theo |
| add_time | int(10) | Thời gian thêm |

### 4.4 Bảng đơn hàng (store_order)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID đơn hàng |
| express_code | varchar(20) | Mã đơn vị vận chuyển |
| express_no | varchar(50) | Mã vận đơn |
| shipping_type | tinyint(1) | Phương thức giao hàng (0-chuyển phát, 1-nhận tại cửa hàng) |
| delivery_type | tinyint(1) | Hình thức giao hàng (0-chưa giao hàng, 1-đã giao hàng, 2-đã hoàn thành) |
| delivery_time | int(10) | Thời gian giao hàng |
| // Các trường khác của đơn hàng | // Kiểu dữ liệu | // Mô tả |

### 4.5 Bảng nhân viên giao hàng (delivery_service)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID nhân viên giao hàng |
| name | varchar(20) | Họ tên nhân viên giao hàng |
| phone | varchar(20) | Số điện thoại nhân viên giao hàng |
| avatar | varchar(255) | Ảnh đại diện nhân viên giao hàng |
| status | tinyint(1) | Trạng thái (0-vô hiệu hóa, 1-kích hoạt) |
| add_time | int(10) | Thời gian thêm |

## 5. Mô tả API

### 5.1 Quản lý đơn vị vận chuyển

#### 5.1.1 Lấy danh sách đơn vị vận chuyển
- **URL yêu cầu**: `/adminapi/v1/freight/express/list`
- **Phương thức yêu cầu**: GET
- **Yêu cầu quyền**: freight/express/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - keyword: từ khóa tìm kiếm
  - status: trạng thái
- **Kết quả trả về**: Dữ liệu danh sách đơn vị vận chuyển

#### 5.1.2 Thêm đơn vị vận chuyển
- **URL yêu cầu**: `/adminapi/v1/freight/express/create`
- **Phương thức yêu cầu**: POST
- **Yêu cầu quyền**: freight/express/create
- **Tham số yêu cầu**:
  - name: tên đơn vị vận chuyển
  - code: mã đơn vị vận chuyển
  - logo: logo đơn vị vận chuyển
  - sort: thứ tự sắp xếp
- **Kết quả trả về**: Kết quả thao tác

#### 5.1.3 Đồng bộ đơn vị vận chuyển từ nền tảng
- **URL yêu cầu**: `/adminapi/v1/freight/express/sync`
- **Phương thức yêu cầu**: POST
- **Yêu cầu quyền**: freight/express/sync
- **Kết quả trả về**: Kết quả thao tác

### 5.2 Quản lý mẫu vận chuyển

#### 5.2.1 Lấy danh sách mẫu vận chuyển
- **URL yêu cầu**: `/adminapi/v1/setting/shipping/template/list`
- **Phương thức yêu cầu**: GET
- **Yêu cầu quyền**: setting/shipping/template/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - keyword: từ khóa tìm kiếm
- **Kết quả trả về**: Dữ liệu danh sách mẫu vận chuyển

#### 5.2.2 Thêm mẫu vận chuyển
- **URL yêu cầu**: `/adminapi/v1/setting/shipping/template/create`
- **Phương thức yêu cầu**: POST
- **Yêu cầu quyền**: setting/shipping/template/create
- **Tham số yêu cầu**:
  - name: tên mẫu
  - type: cách tính phí
  - sregion: khu vực giao hàng
  - srules: quy tắc giao hàng
- **Kết quả trả về**: Kết quả thao tác

### 5.3 Quản lý giao hàng đơn hàng

#### 5.3.1 Giao hàng đơn hàng
- **URL yêu cầu**: `/adminapi/v1/order/delivery/{id}`
- **Phương thức yêu cầu**: PUT
- **Yêu cầu quyền**: order/delivery
- **Tham số yêu cầu**:
  - delivery_type: loại giao hàng (0-giao hàng qua chuyển phát, 1-vận đơn điện tử, 2-shop gửi hàng)
  - express_code: mã đơn vị vận chuyển
  - express_no: mã vận đơn
  - delivery_name: họ tên người gửi hàng
  - delivery_phone: số điện thoại người gửi hàng
- **Kết quả trả về**: Kết quả thao tác

#### 5.3.2 Giao hàng hàng loạt
- **URL yêu cầu**: `/adminapi/v1/order/delivery/batch`
- **Phương thức yêu cầu**: POST
- **Yêu cầu quyền**: order/delivery/batch
- **Tham số yêu cầu**:
  - orders: mảng ID đơn hàng
  - delivery_type: loại giao hàng
  - express_code: mã đơn vị vận chuyển
  - express_no: mã vận đơn
- **Kết quả trả về**: Kết quả thao tác

#### 5.3.3 Tra cứu thông tin vận chuyển
- **URL yêu cầu**: `/adminapi/v1/order/express/{id}`
- **Phương thức yêu cầu**: GET
- **Yêu cầu quyền**: order/express
- **Kết quả trả về**: Dữ liệu thông tin vận chuyển

#### 5.3.4 Lấy danh sách nhân viên giao hàng
- **URL yêu cầu**: `/adminapi/v1/order/delivery/service/list`
- **Phương thức yêu cầu**: GET
- **Yêu cầu quyền**: order/delivery/service/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - keyword: từ khóa tìm kiếm
- **Kết quả trả về**: Dữ liệu danh sách nhân viên giao hàng

## 5. Quy chuẩn và quy trình phát triển

### 5.1 Quy chuẩn code
1. **Quy chuẩn đặt tên**:
   - Tên lớp dùng kiểu PascalCase (UpperCamelCase), ví dụ `ExpressServices`
   - Tên phương thức dùng kiểu camelCase, ví dụ `getExpressList`
   - Tên biến dùng kiểu camelCase, ví dụ `shippingTemplate`
   - Tên hằng số viết hoa toàn bộ, phân tách bằng dấu gạch dưới, ví dụ `SHIPPING_TYPE_EXPRESS`

2. **Cấu trúc code**:
   - Tuân thủ nghiêm ngặt kiến trúc phân tầng, cấm gọi vượt tầng
   - Mỗi phương thức chỉ đảm nhận một trách nhiệm, số dòng code không vượt quá 50 dòng
   - Thêm chú thích hợp lý, mô tả chức năng của phương thức, ý nghĩa tham số và giá trị trả về
   - Mọi thao tác vận chuyển đều phải ghi log để thuận tiện truy vết

3. **Tối ưu hiệu năng**:
   - Kết quả tra cứu vận chuyển phải được lưu vào bộ nhớ đệm để giảm số lần gọi API
   - Thao tác hàng loạt phải dùng transaction để đảm bảo tính nhất quán dữ liệu
   - Thao tác với khối lượng dữ liệu lớn phải dùng hàng đợi để tránh chặn luồng chính

### 5.2 Quy trình phát triển
1. **Phân tích yêu cầu**: Xác định rõ yêu cầu chức năng và logic nghiệp vụ của quản lý vận chuyển
2. **Giai đoạn thiết kế**: Thiết kế cấu trúc dữ liệu, API và quy trình nghiệp vụ
3. **Triển khai mã nguồn**:
   - Triển khai logic tầng dịch vụ vận chuyển
   - Triển khai API controller vận chuyển
   - Triển khai mẫu vận chuyển và quy tắc tính phí
4. **Kiểm thử đơn vị**: Viết unit test, đảm bảo chức năng hoạt động chính xác
5. **Kiểm thử tích hợp**: Tiến hành kiểm thử tích hợp module, kiểm thử tương tác giữa module vận chuyển với các module như đơn hàng
6. **Kiểm thử hiệu năng**: Kiểm thử hiệu năng cho các thao tác như tra cứu vận chuyển, giao hàng hàng loạt
7. **Rà soát code**: Gửi code để rà soát (code review), đảm bảo chất lượng code
8. **Triển khai vận hành**: Triển khai lên môi trường production

## 6. Các vấn đề thường gặp và giải pháp

### 6.1 Tra cứu thông tin vận chuyển thất bại
**Vấn đề**: Gọi API tra cứu vận chuyển thất bại
**Cách khắc phục**:
1. Kiểm tra mã đơn vị vận chuyển có đúng không
2. Kiểm tra mã vận đơn có đúng không
3. Kiểm tra khóa API của nền tảng vận chuyển có hợp lệ không
4. Triển khai cơ chế thử lại (retry) để xử lý lỗi mạng
5. Thêm ghi log để thuận tiện khắc phục sự cố

### 6.2 Giao hàng hàng loạt bị timeout
**Vấn đề**: Bị timeout khi xử lý giao hàng hàng loạt cho số lượng lớn đơn hàng
**Cách khắc phục**:
1. Dùng hàng đợi để xử lý bất đồng bộ việc giao hàng hàng loạt
2. Tối ưu thao tác cơ sở dữ liệu, giảm số lần truy vấn
3. Triển khai xử lý theo từng lô, giảm số lượng xử lý mỗi lần
4. Tăng tài nguyên máy chủ, nâng cao khả năng xử lý đồng thời

### 6.3 Mẫu vận chuyển tính sai
**Vấn đề**: Phí vận chuyển tính theo mẫu vận chuyển không khớp với dự kiến
**Cách khắc phục**:
1. Kiểm tra cấu hình mẫu vận chuyển có đúng không
2. Kiểm tra việc phân chia khu vực có hợp lý không
3. Kiểm tra quy tắc tính phí có đúng không
4. Thêm log tính phí vận chuyển để thuận tiện khắc phục sự cố

### 6.4 In vận đơn điện tử thất bại
**Vấn đề**: Tạo hoặc in vận đơn điện tử thất bại
**Cách khắc phục**:
1. Kiểm tra cấu hình vận đơn điện tử có đúng không
2. Kiểm tra kết nối máy in có bình thường không
3. Kiểm tra định dạng mẫu có đúng không
4. Triển khai cơ chế thử lại (retry) để xử lý lỗi in

## 7. Mở rộng và tùy biến

### 7.1 Thêm đơn vị vận chuyển mới
1. **Thao tác cơ sở dữ liệu**: Thêm thông tin đơn vị vận chuyển mới vào bảng express
2. **Sửa code**:
   - Cập nhật danh sách đơn vị vận chuyển
   - Triển khai API tra cứu vận chuyển cho đơn vị vận chuyển đó
3. **Sửa frontend**: Thêm tùy chọn đơn vị vận chuyển mới ở frontend

### 7.2 Mở rộng cách tính phí của mẫu phí vận chuyển
1. **Sửa đổi cơ sở dữ liệu**: Thêm cách tính phí mới vào bảng shipping_templates
2. **Sửa code**:
   - Cập nhật chức năng cấu hình mẫu phí vận chuyển
   - Triển khai logic tính toán cho cách tính phí mới
3. **Sửa frontend**: Thêm tùy chọn cách tính phí mới ở frontend

### 7.3 Tích hợp nền tảng vận chuyển bên thứ ba
1. **Thiết kế phương án tích hợp**: Xác định cách thức tích hợp với nền tảng vận chuyển bên thứ ba
2. **Triển khai kết nối API**: Triển khai kết nối API với nền tảng vận chuyển bên thứ ba
3. **Cập nhật logic tra cứu vận chuyển**: Sử dụng API tra cứu của nền tảng vận chuyển bên thứ ba
4. **Sửa frontend**: Thêm các chức năng liên quan đến nền tảng vận chuyển bên thứ ba ở frontend

## 8. Lịch sử cập nhật tài liệu

| Phiên bản | Ngày cập nhật | Nội dung cập nhật | Người cập nhật |
|------|----------|----------|--------|
| 1.0 | 2026-01-19 | Phiên bản đầu tiên, bao gồm các chức năng cốt lõi như quản lý đơn vị vận chuyển, quản lý mẫu phí vận chuyển, quản lý giao hàng cho đơn hàng, v.v. | AI Assistant |

## 9. Phụ lục

### 9.1 Sơ đồ quan hệ các lớp cốt lõi

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    Controllers  │     │    Services     │     │     Daos        │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                         ┌─────────────────┐
                         │    Models       │
                         └─────────────────┘
                                 │
                         ┌─────────────────┐
                         │   Cơ sở dữ liệu          │
                         └─────────────────┘
```

### 9.2 Sơ đồ quy trình giao hàng cho đơn hàng

```
Quy trình giao hàng cho đơn hàng:
1. Quản trị viên vào trang chi tiết đơn hàng
2. Nhấn"Giao hàng"Nút
3. Chọn phương thức giao hàng (giao hàng qua vận chuyển, vận đơn điện tử, người bán gửi hàng)
4. Điền thông tin vận chuyển (đơn vị vận chuyển, mã vận đơn, v.v.)
5. Gửi yêu cầu giao hàng
6. Hệ thống cập nhật trạng thái đơn hàng thành đã giao hàng
7. Hệ thống ghi nhận thông tin vận chuyển
8. Kích hoạt sự kiện thông báo vận chuyển
9. Gửi thông báo vận chuyển cho người dùng
```

### 9.3 Sơ đồ quy trình tra cứu vận chuyển

```
Quy trình tra cứu vận chuyển:
1. Người dùng hoặc quản trị viên yêu cầu tra cứu thông tin vận chuyển
2. Hệ thống xác thực tính hợp lệ của yêu cầu
3. Kiểm tra trong bộ nhớ đệm có thông tin vận chuyển không
4. Cache hit, trả về dữ liệu từ bộ nhớ đệm
5. Cache miss, gọi API tra cứu vận chuyển
6. Phân tích kết quả API trả về
7. Lưu thông tin vận chuyển vào bộ nhớ đệm
8. Trả thông tin vận chuyển cho bên yêu cầu
```

### 9.4 Cách tính phí của mẫu phí vận chuyển

| Cách tính phí | Mô tả |
|----------|------|
| Theo số lượng | Tính phí vận chuyển theo số lượng sản phẩm |
| Theo trọng lượng | Tính phí vận chuyển theo trọng lượng sản phẩm |
| Theo thể tích | Tính phí vận chuyển theo thể tích sản phẩm |

### 9.5 Trạng thái giao hàng của đơn hàng

| Giá trị trạng thái | Mô tả |
|--------|------|
| 0 | Chưa giao hàng |
| 1 | Đã giao hàng |
| 2 | Đã hoàn thành |

### 9.6 Phương thức giao hàng

| Giá trị phương thức | Mô tả |
|--------|------|
| 0 | Giao qua đơn vị vận chuyển |
| 1 | Nhận tại cửa hàng |

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
