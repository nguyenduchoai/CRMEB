# Tài liệu phát triển thống kê và phân tích

## 1. Tổng quan tài liệu

### 1.1 Mục đích tài liệu
Tài liệu này nhằm mô tả chi tiết kiến trúc thiết kế, chức năng cốt lõi, cách triển khai code và quy chuẩn phát triển của module thống kê và phân tích trong hệ thống CRMEB, cung cấp tài liệu tham khảo phát triển đầy đủ cho lập trình viên hệ thống.

### 1.2 Phạm vi áp dụng
Tài liệu này dành cho lập trình viên, nhân viên bảo trì và nhà phát triển thứ cấp của hệ thống CRMEB, dùng để hướng dẫn công việc phát triển, mở rộng và bảo trì module thống kê và phân tích.

### 1.3 Định nghĩa thuật ngữ
| Thuật ngữ | Giải thích |
|------|------|
| PV | Lượt xem trang (Page View), tức số lần trang được xem |
| UV | Số khách truy cập duy nhất (Unique Visitor), tức số người dùng truy cập website từ các địa chỉ IP khác nhau |
| Tỷ lệ chuyển đổi | Tỷ lệ giữa số lần chuyển đổi và số lượt truy cập, như tỷ lệ chuyển đổi đăng ký, tỷ lệ chuyển đổi đặt hàng |
| Tỷ lệ mua lại | Tỷ lệ giữa số người dùng mua lại và tổng số người dùng đã mua |
| So với kỳ trước | So sánh dữ liệu thống kê kỳ này với dữ liệu thống kê kỳ trước |
| So với cùng kỳ năm trước | So sánh dữ liệu thống kê kỳ này với dữ liệu thống kê cùng kỳ năm trước |
| Dòng tiền | Bản ghi toàn bộ dòng tiền vào và ra trong hệ thống |

## 2. Các mô-đun chức năng cốt lõi

### 2.1 Thống kê lưu lượng
- **Thống kê PV/UV**: Thống kê lượt xem trang và số khách truy cập duy nhất của website
- **Phân tích xu hướng truy cập**: Hỗ trợ xem xu hướng truy cập theo giờ, ngày, tuần, tháng
- **Phân tích nguồn truy cập**: Phân tích các kênh nguồn truy cập của người dùng
- **Phân tích độ phổ biến của trang**: Thống kê mức độ được truy cập của từng trang
- **Phân tích thiết bị truy cập**: Phân tích loại thiết bị người dùng sử dụng (PC, di động, máy tính bảng, v.v.)

### 2.2 Thống kê người dùng
- **Thống kê thông tin cơ bản về người dùng**: Số người dùng mới, số khách hàng đã mua, số người nạp tiền, số người dùng trả phí
- **Xu hướng tăng trưởng người dùng**: Xem tình hình tăng trưởng người dùng theo thời gian
- **Phân bố khu vực người dùng**: Thống kê phân bố khu vực của người dùng (theo tỉnh, thành phố)
- **Phân bố giới tính người dùng**: Thống kê phân bố giới tính của người dùng
- **Phân bố độ tuổi người dùng**: Thống kê phân bố độ tuổi của người dùng
- **Phân tích người dùng WeChat**: Thống kê lượt theo dõi/hủy theo dõi của người dùng WeChat
- **Phân tích hành vi người dùng**: Phân tích các hành vi của người dùng như xem, tìm kiếm, mua hàng, v.v.

### 2.3 Thống kê bán hàng
- **Thống kê giá trị đơn hàng**: Thống kê tổng giá trị đơn hàng, số tiền thực trả, số tiền hoàn trả, v.v.
- **Thống kê số lượng đơn hàng**: Thống kê tổng số đơn hàng, số đơn hàng thành công, số đơn hàng hoàn tiền, v.v.
- **Phân tích xu hướng đơn hàng**: Xem xu hướng đơn hàng theo thời gian
- **Phân tích nguồn đơn hàng**: Phân tích các kênh nguồn của đơn hàng
- **Phân bố loại đơn hàng**: Thống kê tình hình phân bố của các loại đơn hàng khác nhau
- **Dữ liệu giao dịch thời gian thực**: Hiển thị dữ liệu giao dịch theo thời gian thực
- **Phân tích phương thức thanh toán**: Phân tích tình hình sử dụng các phương thức thanh toán khác nhau

### 2.4 Thống kê tài chính
- **Thống kê dòng tiền**: Thống kê các bản ghi tiền vào và ra trong hệ thống
- **Lịch sử sao kê**: Tạo sao kê theo ngày/tuần/tháng
- **Thống kê số dư**: Thống kê tình hình số dư của người dùng
- **Phân tích xu hướng số dư**: Xem xu hướng số dư theo thời gian
- **Phân tích nguồn số dư**: Phân tích các kênh nguồn của số dư
- **Phân tích loại số dư**: Phân tích phân bố của các loại số dư khác nhau

### 2.5 Thống kê sản phẩm
- **Dữ liệu bán hàng cơ bản của sản phẩm**: Doanh số sản phẩm, lượt bán, số khách truy cập, tỷ lệ chuyển đổi, v.v.
- **Xu hướng bán hàng của sản phẩm**: Xem xu hướng bán hàng của sản phẩm theo thời gian
- **Xếp hạng sản phẩm**: Xếp hạng sản phẩm theo doanh số, theo lượt bán, theo số khách truy cập, v.v.
- **Thống kê theo danh mục sản phẩm**: Thống kê tình hình bán hàng theo danh mục sản phẩm
- **Phân tích thuộc tính sản phẩm**: Phân tích tình hình bán hàng theo các thuộc tính khác nhau của sản phẩm

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

#### 3.2.1 Tầng điều khiển (Controller)
**Controller thống kê và phân tích**: `/crmeb/app/adminapi/controller/v1/statistic/`
- `FlowStatistic.php` - Controller thống kê dòng tiền
- `UserStatistic.php` - Controller thống kê người dùng
- `OrderStatistic.php` - Controller thống kê đơn hàng
- `TradeStatistic.php` - Controller thống kê giao dịch
- `ProductStatistic.php` - Controller thống kê sản phẩm
- `BalanceStatistic.php` - Controller thống kê số dư

#### 3.2.2 Tầng dịch vụ (Services)
**Dịch vụ thống kê và phân tích**: `/crmeb/app/services/statistic/`
- `CapitalFlowServices.php` - Dịch vụ thống kê dòng tiền
- `UserStatisticServices.php` - Dịch vụ thống kê người dùng
- `OrderStatisticServices.php` - Dịch vụ thống kê đơn hàng
- `TradeStatisticServices.php` - Dịch vụ thống kê giao dịch
- `ProductStatisticServices.php` - Dịch vụ thống kê sản phẩm
- `BalanceStatisticServices.php` - Dịch vụ thống kê số dư

#### 3.2.3 Tầng truy cập dữ liệu (Dao)
**Dao thống kê và phân tích**: Dùng chung Dao của các module nghiệp vụ, như Dao người dùng, Dao đơn hàng, Dao sản phẩm, v.v.

#### 3.2.4 Tầng mô hình (Model)
**Model thống kê và phân tích**: Dùng chung model của các module nghiệp vụ, như model người dùng, model đơn hàng, model sản phẩm, v.v.

## 4. Thiết kế cơ sở dữ liệu

### 4.1 Bảng dòng tiền (capital_flow)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID dòng tiền |
| uid | int(10) | ID người dùng |
| link_id | int(10) | ID liên kết (như ID đơn hàng, ID nạp tiền, v.v.) |
| type | varchar(20) | Loại giao dịch (pay-thanh toán đơn hàng, refund-hoàn tiền, recharge-nạp tiền, extract-rút tiền, v.v.) |
| money | decimal(10,2) | Giá trị giao dịch |
| pm | tinyint(1) | Chiều thu/chi (1-thu, 0-chi) |
| balance | decimal(10,2) | Số dư sau giao dịch |
| mark | varchar(255) | Ghi chú |
| add_time | int(10) | Thời gian thêm |

### 4.2 Bảng nhật ký truy cập (visit_log)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID bản ghi |
| uid | int(10) | ID người dùng (0-khách vãng lai) |
| ip | varchar(15) | IP truy cập |
| url | varchar(255) | URL truy cập |
| referer | varchar(255) | URL nguồn |
| user_agent | text | Thông tin user agent |
| device | varchar(20) | Loại thiết bị (pc, mobile, tablet) |
| browser | varchar(20) | Trình duyệt |
| os | varchar(20) | Hệ điều hành |
| add_time | int(10) | Thời gian truy cập |

### 4.3 Bảng thống kê bán hàng (sale_statistics)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID thống kê |
| date | date | Ngày thống kê |
| order_count | int(10) | Số lượng đơn hàng |
| order_amount | decimal(10,2) | Tổng số tiền đơn hàng |
| pay_amount | decimal(10,2) | Số tiền thực trả |
| refund_count | int(10) | Số lượng đơn hoàn tiền |
| refund_amount | decimal(10,2) | Số tiền hoàn |
| user_count | int(10) | Số khách hàng đã mua |
| add_time | int(10) | Thời gian thêm |

## 5. Mô tả API

### 5.1 API thống kê lưu lượng

#### 5.1.1 Lấy thống kê PV/UV
- **URL yêu cầu**: `/adminapi/v1/statistic/flow/pvuv`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: statistic/flow/pvuv
- **Tham số yêu cầu**:
  - type: Đơn vị thời gian (hour, day, week, month)
  - start_time: thời gian bắt đầu
  - end_time: thời gian kết thúc
- **Kết quả trả về**: Dữ liệu thống kê PV/UV

#### 5.1.2 Lấy phân tích nguồn truy cập
- **URL yêu cầu**: `/adminapi/v1/statistic/flow/source`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: statistic/flow/source
- **Tham số yêu cầu**:
  - start_time: thời gian bắt đầu
  - end_time: thời gian kết thúc
- **Kết quả trả về**: Dữ liệu phân tích nguồn truy cập

### 5.2 API thống kê người dùng

#### 5.2.1 Lấy thống kê cơ bản về người dùng
- **URL yêu cầu**: `/adminapi/v1/statistic/user/basic`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: statistic/user/basic
- **Tham số yêu cầu**:
  - start_time: thời gian bắt đầu
  - end_time: thời gian kết thúc
- **Kết quả trả về**: Dữ liệu thống kê cơ bản về người dùng

#### 5.2.2 Lấy xu hướng tăng trưởng người dùng
- **URL yêu cầu**: `/adminapi/v1/statistic/user/trend`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: statistic/user/trend
- **Tham số yêu cầu**:
  - type: Đơn vị thời gian (hour, day, week, month)
  - start_time: thời gian bắt đầu
  - end_time: thời gian kết thúc
- **Kết quả trả về**: Dữ liệu xu hướng tăng trưởng người dùng

#### 5.2.3 Lấy phân bố khu vực người dùng
- **URL yêu cầu**: `/adminapi/v1/statistic/user/region`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: statistic/user/region
- **Tham số yêu cầu**:
  - start_time: thời gian bắt đầu
  - end_time: thời gian kết thúc
- **Kết quả trả về**: Dữ liệu phân bố khu vực người dùng

### 5.3 API thống kê bán hàng

#### 5.3.1 Lấy thống kê đơn hàng
- **URL yêu cầu**: `/adminapi/v1/statistic/order/basic`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: statistic/order/basic
- **Tham số yêu cầu**:
  - start_time: thời gian bắt đầu
  - end_time: thời gian kết thúc
- **Kết quả trả về**: Dữ liệu thống kê đơn hàng

#### 5.3.2 Lấy xu hướng đơn hàng
- **URL yêu cầu**: `/adminapi/v1/statistic/order/trend`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: statistic/order/trend
- **Tham số yêu cầu**:
  - type: Đơn vị thời gian (hour, day, week, month)
  - start_time: thời gian bắt đầu
  - end_time: thời gian kết thúc
- **Kết quả trả về**: Dữ liệu xu hướng đơn hàng

### 5.4 API thống kê tài chính

#### 5.4.1 Lấy danh sách dòng tiền
- **URL yêu cầu**: `/adminapi/v1/statistic/flow/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: statistic/flow/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - type: Loại giao dịch
  - start_time: thời gian bắt đầu
  - end_time: thời gian kết thúc
- **Kết quả trả về**: Dữ liệu danh sách dòng tiền

#### 5.4.2 Lấy lịch sử sao kê
- **URL yêu cầu**: `/adminapi/v1/statistic/flow/bill`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: statistic/flow/bill
- **Tham số yêu cầu**:
  - type: Loại sao kê (day, week, month)
  - date: Ngày thống kê
- **Kết quả trả về**: Dữ liệu lịch sử sao kê

### 5.5 API thống kê sản phẩm

#### 5.5.1 Lấy dữ liệu bán hàng cơ bản của sản phẩm
- **URL yêu cầu**: `/adminapi/v1/statistic/product/basic`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: statistic/product/basic
- **Tham số yêu cầu**:
  - product_id: ID sản phẩm
  - start_time: thời gian bắt đầu
  - end_time: thời gian kết thúc
- **Kết quả trả về**: Dữ liệu bán hàng cơ bản của sản phẩm

#### 5.5.2 Lấy bảng xếp hạng bán hàng của sản phẩm
- **URL yêu cầu**: `/adminapi/v1/statistic/product/rank`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: statistic/product/rank
- **Tham số yêu cầu**:
  - type: Loại xếp hạng (sales, amount, visitor)
  - start_time: thời gian bắt đầu
  - end_time: thời gian kết thúc
  - limit: Số lượng trả về
- **Kết quả trả về**: Dữ liệu xếp hạng bán hàng của sản phẩm

## 5. Quy chuẩn và quy trình phát triển

### 5.1 Quy chuẩn code
1. **Quy chuẩn đặt tên**:
   - Tên lớp dùng kiểu PascalCase (UpperCamelCase), ví dụ `UserStatisticServices`
   - Tên phương thức dùng kiểu camelCase, ví dụ `getUserBasicInfo`
   - Tên biến dùng kiểu camelCase, ví dụ `visitorCount`
   - Tên hằng số viết hoa toàn bộ, phân tách bằng dấu gạch dưới, ví dụ `STATISTIC_TYPE_HOUR`

2. **Cấu trúc code**:
   - Tuân thủ nghiêm ngặt kiến trúc phân tầng, cấm gọi vượt tầng
   - Mỗi phương thức chỉ đảm nhận một trách nhiệm, số dòng code không vượt quá 50 dòng
   - Thêm chú thích hợp lý, mô tả chức năng của phương thức, ý nghĩa tham số và giá trị trả về
   - Mọi dữ liệu thống kê đều phải có khoảng thời gian đầy đủ

3. **Tối ưu hiệu năng**:
   - Truy vấn thống kê bắt buộc phải sử dụng chỉ mục, tránh quét toàn bộ bảng
   - Truy vấn thống kê phức tạp bắt buộc phải sử dụng bộ nhớ đệm (cache), giảm tải cho cơ sở dữ liệu
   - Dữ liệu thống kê bắt buộc phải được tổng hợp định kỳ, tránh tính toán lượng lớn dữ liệu theo thời gian thực
   - Sử dụng chu kỳ thống kê phù hợp, như giờ, ngày, tuần, tháng

### 5.2 Quy trình phát triển
1. **Phân tích yêu cầu**: Xác định rõ yêu cầu chức năng và logic nghiệp vụ của thống kê phân tích
2. **Giai đoạn thiết kế**: Thiết kế cấu trúc dữ liệu, API và quy trình nghiệp vụ
3. **Triển khai mã nguồn**:
   - Triển khai logic tầng service thống kê
   - Triển khai API controller thống kê
   - Triển khai cơ chế tổng hợp dữ liệu và bộ nhớ đệm (cache)
4. **Kiểm thử đơn vị**: Viết unit test, đảm bảo tính chính xác của kết quả thống kê
5. **Kiểm thử hiệu năng**: Kiểm thử hiệu năng các truy vấn thống kê, tối ưu hiệu suất truy vấn
6. **Rà soát code**: Gửi code để rà soát (code review), đảm bảo chất lượng code
7. **Triển khai vận hành**: Triển khai lên môi trường production

## 6. Các vấn đề thường gặp và giải pháp

### 6.1 Dữ liệu thống kê không chính xác
**Vấn đề**: Dữ liệu thống kê không khớp với dữ liệu thực tế
**Cách khắc phục**:
1. Kiểm tra logic thống kê, đảm bảo phạm vi thống kê chính xác
2. Kiểm tra nguồn dữ liệu, đảm bảo dữ liệu đầy đủ
3. Kiểm tra khoảng thời gian, đảm bảo định dạng thời gian chính xác
4. Kiểm tra chu kỳ thống kê, đảm bảo việc phân chia chu kỳ chính xác
5. Triển khai cơ chế kiểm tra dữ liệu, định kỳ đối chiếu dữ liệu thống kê

### 6.2 Vấn đề hiệu năng truy vấn thống kê
**Vấn đề**: Truy vấn thống kê mất quá nhiều thời gian, ảnh hưởng đến hiệu năng hệ thống
**Cách khắc phục**:
1. Sử dụng chỉ mục để tối ưu truy vấn
2. Triển khai bộ nhớ đệm (cache) cho dữ liệu thống kê
3. Định kỳ tính toán trước dữ liệu thống kê
4. Tối ưu cấu trúc cơ sở dữ liệu, như chia bảng (sharding), phân vùng (partition)
5. Sử dụng kho dữ liệu (data warehouse) hoặc cơ sở dữ liệu OLAP để xử lý thống kê phức tạp

### 6.3 Thiếu chiều thống kê
**Vấn đề**: Không đáp ứng được nhu cầu thống kê đa chiều
**Cách khắc phục**:
1. Thiết kế kiến trúc thống kê linh hoạt, hỗ trợ chiều thống kê tùy chỉnh
2. Triển khai cơ chế mở rộng chiều thống kê
3. Sử dụng công nghệ khối dữ liệu (data cube), hỗ trợ phân tích dữ liệu đa chiều

### 6.4 Vấn đề thống kê thời gian thực
**Vấn đề**: Dữ liệu thống kê thời gian thực bị trễ hoặc không chính xác
**Cách khắc phục**:
1. Sử dụng công nghệ xử lý luồng (stream processing), như Kafka, Flink, v.v.
2. Triển khai thống kê gần thời gian thực, cân bằng giữa tính tức thời và độ chính xác
3. Tối ưu quy trình xử lý dữ liệu thời gian thực, giảm độ trễ

## 7. Mở rộng và tùy biến

### 7.1 Thêm chiều thống kê mới
1. **Thiết kế logic thống kê**: Xác định logic tính toán cho chiều thống kê mới
2. **Sửa tầng service**: Thêm phương thức thống kê mới vào service thống kê tương ứng
3. **Thêm API**: Thêm API mới vào controller tương ứng
4. **Sửa frontend**: Thêm biểu đồ thống kê và phần hiển thị mới vào trang frontend

### 7.2 Triển khai báo cáo thống kê tùy chỉnh
1. **Thiết kế cấu trúc báo cáo**: Xác định các trường, chiều và chỉ số của báo cáo
2. **Triển khai logic tạo báo cáo**: Thêm phương thức tạo báo cáo vào service thống kê
3. **Thêm chức năng quản lý báo cáo**: Triển khai chức năng tạo, sửa, xóa báo cáo
4. **Sửa frontend**: Thêm chức năng hiển thị và xuất báo cáo vào trang frontend

### 7.3 Tích hợp công cụ thống kê bên thứ ba
1. **Thiết kế phương án tích hợp**: Xác định cách thức tích hợp với công cụ thống kê bên thứ ba
2. **Triển khai đồng bộ dữ liệu**: Triển khai đồng bộ dữ liệu với công cụ thống kê bên thứ ba
3. **Thêm API**: Thêm API lấy dữ liệu thống kê từ bên thứ ba
4. **Sửa frontend**: Tích hợp biểu đồ thống kê của bên thứ ba vào trang frontend

## 8. Lịch sử cập nhật tài liệu

| Phiên bản | Ngày cập nhật | Nội dung cập nhật | Người cập nhật |
|------|----------|----------|--------|
| 1.0 | 2026-01-19 | Phiên bản đầu tiên, bao gồm các chức năng cốt lõi như thống kê lưu lượng, thống kê người dùng, thống kê bán hàng, thống kê tài chính, thống kê sản phẩm, v.v. | AI Assistant |

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

### 9.2 Sơ đồ minh họa quy trình thống kê

```
Quy trình thống kê:
1. Hệ thống thu thập dữ liệu gốc (log truy cập, dữ liệu đơn hàng, dữ liệu người dùng, v.v.)
2. Định kỳ tổng hợp dữ liệu gốc, tạo dữ liệu thống kê
3. Lưu dữ liệu thống kê vào bảng thống kê
4. Cung cấp API để frontend truy vấn dữ liệu thống kê
5. Frontend hiển thị biểu đồ và dữ liệu thống kê
6. Hỗ trợ xuất dữ liệu thống kê
```

### 9.3 Các chiều thống kê được hỗ trợ

| Tên chiều | Mô tả |
|----------|------|
| Chiều thời gian | Giờ, ngày, tuần, tháng, năm |
| Chiều khu vực | Tỉnh, thành phố |
| Chiều thiết bị | PC, thiết bị di động, máy tính bảng |
| Chiều kênh | WeChat, Alipay, APP, Mini Program, v.v. |
| Chiều sản phẩm | Danh mục sản phẩm, thuộc tính sản phẩm, thương hiệu, v.v. |
| Chiều người dùng | Giới tính, độ tuổi, hạng thành viên, v.v. |
| Chiều đơn hàng | Loại đơn hàng, phương thức thanh toán, phương thức giao hàng, v.v. |

### 9.4 Danh sách chỉ số thống kê

| Tên chỉ số | Mô tả |
|----------|------|
| PV | Lượt xem trang |
| UV | Số khách truy cập duy nhất |
| Số người dùng mới | Số người dùng đăng ký mới |
| Số khách hàng đã mua | Số người dùng đã hoàn tất giao dịch |
| Tỷ lệ mua lại | Tỷ lệ giữa số người dùng mua lại và tổng số người dùng đã mua |
| Tỷ lệ chuyển đổi | Tỷ lệ giữa số lượt chuyển đổi và số lượt truy cập |
| Tổng số tiền đơn hàng | Tổng số tiền của đơn hàng |
| Số tiền thực trả | Số tiền thực tế đã thanh toán |
| Số lượng đơn hàng | Tổng số đơn hàng |
| Giá trị đơn trung bình | Số tiền trung bình của mỗi đơn hàng |
| Doanh số sản phẩm | Tổng doanh số bán của sản phẩm |
| Lượt bán sản phẩm | Số lượng bán ra của sản phẩm |
| Dòng tiền vào | Tổng dòng tiền vào trong hệ thống |
| Dòng tiền ra | Tổng dòng tiền ra trong hệ thống |

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
