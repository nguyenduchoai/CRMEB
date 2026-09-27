# Tài liệu phát triển quản lý thành viên

## 1. Tổng quan tài liệu

### 1.1 Mục đích tài liệu
Tài liệu này nhằm mô tả chi tiết kiến trúc thiết kế, chức năng cốt lõi, cách triển khai code và quy chuẩn phát triển của module quản lý thành viên trong hệ thống CRMEB, cung cấp tài liệu tham khảo phát triển đầy đủ cho lập trình viên hệ thống.

### 1.2 Phạm vi áp dụng
Tài liệu này dành cho lập trình viên, nhân viên bảo trì và nhà phát triển thứ cấp của hệ thống CRMEB, dùng để hướng dẫn công việc phát triển, mở rộng và bảo trì module quản lý thành viên.

### 1.3 Định nghĩa thuật ngữ
| Thuật ngữ | Giải thích |
|------|------|
| Hạng thành viên | Hệ thống hạng được phân chia dựa trên điểm kinh nghiệm của người dùng, mỗi hạng được hưởng các quyền lợi khác nhau |
| Điểm kinh nghiệm | Giá trị người dùng nhận được thông qua các hành vi khác nhau, dùng để nâng hạng thành viên |
| Quyền lợi thành viên | Phúc lợi riêng mà mỗi hạng thành viên được hưởng, như chiết khấu, phiếu giảm giá, v.v. |
| Thẻ thành viên | Thẻ trực quan hiển thị các thông tin như hạng thành viên, thời hạn hiệu lực, quyền lợi, v.v. |
| Điểm thưởng | Giá trị người dùng nhận được thông qua các hành vi khác nhau, có thể dùng để thanh toán khi mua sắm |
| Cửa hàng đổi điểm | Nền tảng đổi điểm thưởng lấy sản phẩm hoặc dịch vụ |
| Thời hạn thành viên | Thời hạn hiệu lực của hạng thành viên, sau khi hết hạn có thể bị hạ hạng hoặc mất hiệu lực |

## 2. Các mô-đun chức năng cốt lõi

### 2.1 Quản lý hạng thành viên
- **Danh sách hạng**: Hiển thị các hạng thành viên được cấu hình trong hệ thống, hỗ trợ tìm kiếm, phân trang
- **Thêm hạng**: Thêm hạng thành viên mới, thiết lập tên hạng, chiết khấu, yêu cầu điểm kinh nghiệm, v.v.
- **Sửa hạng**: Chỉnh sửa thông tin hạng thành viên, bao gồm tên, chiết khấu, yêu cầu điểm kinh nghiệm, v.v.
- **Xóa hạng**: Xóa hạng thành viên
- **Quản lý trạng thái hạng**: Bật/tắt hạng thành viên
- **Sắp xếp hạng**: Điều chỉnh thứ tự hiển thị của các hạng thành viên

### 2.2 Quản lý hạng người dùng
- **Danh sách hạng người dùng**: Hiển thị thông tin hạng hiện tại của người dùng, hỗ trợ tìm kiếm, phân trang
- **Tặng hạng**: Tặng hạng thành viên cho người dùng một cách thủ công
- **Xóa bỏ hạng**: Xóa bỏ hạng thành viên của người dùng
- **Quản lý thời hạn hạng**: Thiết lập và chỉnh sửa thời hạn hiệu lực của hạng thành viên
- **Tự động kiểm tra hạng**: Định kỳ kiểm tra người dùng đã đạt điều kiện nâng hạng chưa và tự động nâng hạng

### 2.3 Quản lý điểm kinh nghiệm
- **Cấu hình điểm kinh nghiệm**: Cấu hình số điểm kinh nghiệm nhận được cho từng hành vi
- **Lịch sử điểm kinh nghiệm**: Ghi lại biến động điểm kinh nghiệm của người dùng
- **Tra cứu điểm kinh nghiệm**: Tra cứu lịch sử nhận và sử dụng điểm kinh nghiệm của người dùng
- **Thống kê điểm kinh nghiệm**: Thống kê phân bố nguồn điểm kinh nghiệm của người dùng

### 2.4 Quản lý điểm thưởng
- **Cấu hình điểm thưởng**: Cấu hình số điểm thưởng nhận được cho từng hành vi
- **Cộng điểm thưởng**: Cộng điểm thưởng cho người dùng một cách thủ công
- **Trừ điểm thưởng**: Trừ điểm thưởng của người dùng một cách thủ công
- **Lịch sử điểm thưởng**: Ghi lại biến động điểm thưởng của người dùng
- **Tra cứu điểm thưởng**: Tra cứu lịch sử nhận và sử dụng điểm thưởng của người dùng
- **Thống kê điểm thưởng**: Thống kê phân bố nguồn nhận và mục đích sử dụng điểm thưởng của người dùng

### 2.5 Quản lý quyền lợi thành viên
- **Quyền lợi chiết khấu**: Cấu hình tỷ lệ chiết khấu sản phẩm cho từng hạng
- **Biểu tượng hạng**: Cấu hình biểu tượng và ảnh nền riêng cho từng hạng
- **Thẻ thành viên**: Tạo và quản lý thẻ thành viên
- **Quy tắc quyền lợi**: Cấu hình các quyền lợi khác mà thành viên được hưởng, như CSKH riêng, ưu tiên giao hàng, v.v.

### 2.6 Cơ chế nâng hạng thành viên
- **Tích lũy điểm kinh nghiệm**: Tích lũy điểm kinh nghiệm qua các hành vi như điểm danh, đặt hàng, mời bạn bè, v.v.
- **Kiểm tra điều kiện nâng hạng**: Định kỳ kiểm tra người dùng đã đạt điều kiện nâng hạng chưa
- **Tự động nâng hạng**: Tự động cập nhật hạng thành viên sau khi đạt điều kiện nâng hạng
- **Thông báo nâng hạng**: Gửi thông báo nâng hạng cho người dùng

## 3. Cấu trúc và thiết kế code

### 3.1 Thiết kế kiến trúc
Hệ thống sử dụng thiết kế kiến trúc phân tầng, tuân thủ nghiêm ngặt mẫu thiết kế MVC:
```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    Models       │     │     Daos        │     │    Services     │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                         ┌─────────────────┐
                         │ Controllers     │
                         └─────────────────┘
                                 │
                         ┌─────────────────┐
                         │     Routes      │
                         └─────────────────┘
```

### 3.2 Cấu trúc tệp cốt lõi

#### 3.2.1 Tầng mô hình (Model)
**Các model liên quan đến thành viên**:
- `UserLevel.php` - Model thông tin hạng thành viên thực tế của người dùng
- `SystemUserLevel.php` - Model cấu hình hạng thành viên của hệ thống
- `User.php` - Model thông tin cơ bản của người dùng, chứa các trường như điểm kinh nghiệm, điểm thưởng, v.v.

#### 3.2.2 Tầng dịch vụ (Services)
**Services quản lý thành viên**:
- `UserLevelServices.php` - Thiết lập và quản lý hạng thành viên, tự động kiểm tra điều kiện nâng hạng của người dùng
- `UserBillServices.php` - Cộng và trừ điểm thưởng, quản lý lịch sử điểm thưởng
- `UserServices.php` - Quản lý thông tin cơ bản của người dùng, bao gồm hạng, điểm kinh nghiệm, điểm thưởng, v.v.

#### 3.2.3 Tầng controller (Controller)
**Controller thành viên**:
- `UserLevel.php` - Controller quản lý hạng thành viên
- `User.php` - Controller quản lý người dùng, bao gồm các chức năng quản lý hạng, điểm kinh nghiệm, điểm thưởng, v.v.

#### 3.2.4 Cấu hình route
**Route thành viên**:
- `user.php` - Chứa các route API liên quan đến quản lý thành viên

## 4. Thiết kế cơ sở dữ liệu

### 4.1 Bảng hạng thành viên của hệ thống (system_user_level)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID hạng |
| name | varchar(20) | Tên cấp bậc |
| icon | varchar(255) | Biểu tượng hạng |
| bg | varchar(255) | Ảnh nền hạng |
| grade | int(10) | Cấp bậc |
| experience_min | int(10) | Điểm kinh nghiệm tối thiểu |
| experience_max | int(10) | Điểm kinh nghiệm tối đa |
| discount | decimal(10,2) | Tỷ lệ chiết khấu |
| status | tinyint(1) | Trạng thái (0-vô hiệu hóa, 1-kích hoạt) |
| is_show | tinyint(1) | Có hiển thị không (0-ẩn, 1-hiện) |
| sort | int(10) | Thứ tự sắp xếp |
| add_time | int(10) | Thời gian thêm |

### 4.2 Bảng hạng người dùng (user_level)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID bản ghi |
| uid | int(10) | ID người dùng |
| level_id | int(10) | ID hạng thành viên |
| is_forever | tinyint(1) | Có vĩnh viễn không (0-không, 1-có) |
| start_time | int(10) | Thời gian bắt đầu |
| end_time | int(10) | Thời gian kết thúc |
| status | tinyint(1) | Trạng thái (0-hết hạn, 1-còn hiệu lực) |
| add_time | int(10) | Thời gian thêm |

### 4.3 Bảng người dùng (user)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| uid | int(10) unsigned | ID người dùng |
| user_level | int(10) | ID hạng thành viên hiện tại |
| experience | int(10) | Điểm kinh nghiệm |
| integral | int(10) | Điểm thưởng |
| sign_num | int(10) | Số ngày điểm danh liên tục |
| sign_integral | int(10) | Điểm thưởng điểm danh đã nhận |
| // Các trường thông tin người dùng khác | // Kiểu dữ liệu | // Mô tả |

### 4.4 Bảng giao dịch người dùng (user_bill)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID giao dịch |
| uid | int(10) | ID người dùng |
| category | varchar(20) | Loại tài sản (integral-điểm thưởng, experience-điểm kinh nghiệm) |
| type | varchar(20) | Loại biến động (sign-điểm danh, order-đơn hàng, invite-mời bạn bè) |
| pm | tinyint(1) | Chiều thu/chi (1-thu, 0-chi) |
| number | varchar(50) | Mã giao dịch |
| money | decimal(10,2) | Số tiền biến động |
| balance | decimal(10,2) | Số dư/điểm thưởng/điểm kinh nghiệm sau khi biến động |
| mark | varchar(255) | Ghi chú |
| add_time | int(10) | Thời gian thêm |

## 5. Mô tả API

### 5.1 Quản lý hạng thành viên

#### 5.1.1 Lấy danh sách hạng thành viên của hệ thống
- **URL yêu cầu**: `/adminapi/v1/user/level/list`
- **Phương thức yêu cầu**: GET
- **Yêu cầu quyền**: user/user_level/list
- **Kết quả trả về**: Dữ liệu danh sách hạng thành viên của hệ thống

#### 5.1.2 Thêm hạng thành viên của hệ thống
- **URL yêu cầu**: `/adminapi/v1/user/level/create`
- **Phương thức yêu cầu**: POST
- **Yêu cầu quyền**: user/user_level/create
- **Tham số yêu cầu**:
  - name: tên hạng
  - grade: cấp hạng
  - experience_min: điểm kinh nghiệm tối thiểu
  - experience_max: điểm kinh nghiệm tối đa
  - discount: tỷ lệ chiết khấu
  - icon: biểu tượng hạng
  - bg: ảnh nền hạng
- **Kết quả trả về**: Kết quả thao tác

#### 5.1.3 Lấy danh sách hạng người dùng
- **URL yêu cầu**: `/adminapi/v1/user/user_level/list`
- **Phương thức yêu cầu**: GET
- **Yêu cầu quyền**: user/user/user_level_list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - keyword: từ khóa tìm kiếm
- **Kết quả trả về**: Dữ liệu danh sách hạng người dùng

#### 5.1.4 Tặng hạng thành viên cho người dùng
- **URL yêu cầu**: `/adminapi/v1/user/user_level/give/{uid}`
- **Phương thức yêu cầu**: POST
- **Yêu cầu quyền**: user/user/user_level_give
- **Tham số yêu cầu**:
  - level_id: ID hạng thành viên
  - is_forever: có vĩnh viễn không (0-không, 1-có)
  - days: thời hạn hiệu lực (ngày)
- **Kết quả trả về**: Kết quả thao tác

### 5.2 Quản lý điểm thưởng

#### 5.2.1 Lấy danh sách lịch sử điểm thưởng
- **URL yêu cầu**: `/adminapi/v1/user/integral/list`
- **Phương thức yêu cầu**: GET
- **Yêu cầu quyền**: user/integral/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - uid: ID người dùng
  - type: loại biến động
- **Kết quả trả về**: Dữ liệu danh sách lịch sử điểm thưởng

#### 5.2.2 Cộng điểm thưởng cho người dùng
- **URL yêu cầu**: `/adminapi/v1/user/integral/add/{uid}`
- **Phương thức yêu cầu**: POST
- **Yêu cầu quyền**: user/integral/add
- **Tham số yêu cầu**:
  - integral: số điểm thưởng
  - mark: ghi chú
- **Kết quả trả về**: Kết quả thao tác

#### 5.2.3 Trừ điểm thưởng của người dùng
- **URL yêu cầu**: `/adminapi/v1/user/integral/sub/{uid}`
- **Phương thức yêu cầu**: POST
- **Yêu cầu quyền**: user/integral/sub
- **Tham số yêu cầu**:
  - integral: số điểm thưởng
  - mark: ghi chú
- **Kết quả trả về**: Kết quả thao tác

### 5.3 API phía người dùng

#### 5.3.1 Lấy danh sách hạng thành viên
- **URL yêu cầu**: `/api/v1/user/level/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: Người dùng đã đăng nhập
- **Kết quả trả về**: Dữ liệu danh sách hạng thành viên

#### 5.3.2 Lấy thông tin hạng của người dùng
- **URL yêu cầu**: `/api/v1/user/level/info`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: Người dùng đã đăng nhập
- **Kết quả trả về**: Thông tin hạng của người dùng hiện tại

#### 5.3.3 Lấy lịch sử điểm thưởng
- **URL yêu cầu**: `/api/v1/user/integral/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: Người dùng đã đăng nhập
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
- **Kết quả trả về**: Lịch sử điểm thưởng của người dùng hiện tại

## 5. Quy chuẩn và quy trình phát triển

### 5.1 Quy chuẩn code
1. **Quy chuẩn đặt tên**:
   - Tên lớp dùng kiểu PascalCase (UpperCamelCase), ví dụ `UserLevelServices`
   - Tên phương thức dùng kiểu camelCase, ví dụ `checkUpgradeCondition`
   - Tên biến dùng kiểu camelCase, ví dụ `userExperience`
   - Tên hằng số viết hoa toàn bộ, phân tách bằng dấu gạch dưới, ví dụ `LEVEL_STATUS_ENABLE`

2. **Cấu trúc code**:
   - Tuân thủ nghiêm ngặt kiến trúc phân tầng, cấm gọi vượt tầng
   - Mỗi phương thức chỉ đảm nhận một trách nhiệm, số dòng code không vượt quá 50 dòng
   - Thêm chú thích hợp lý, mô tả chức năng của phương thức, ý nghĩa tham số và giá trị trả về
   - Mọi biến động điểm kinh nghiệm và điểm thưởng đều phải ghi lại lý do biến động và ID nghiệp vụ liên quan

3. **Tính nhất quán dữ liệu**:
   - Mọi biến động điểm kinh nghiệm và điểm thưởng đều phải dùng transaction để đảm bảo tính nhất quán dữ liệu
   - Khi hạng thành viên thay đổi, phải đồng thời cập nhật dữ liệu ở các bảng liên quan
   - Phải ghi log biến động đầy đủ để thuận tiện truy vết

### 5.2 Quy trình phát triển
1. **Phân tích yêu cầu**: Xác định rõ yêu cầu chức năng và logic nghiệp vụ của quản lý thành viên
2. **Giai đoạn thiết kế**: Thiết kế cấu trúc dữ liệu, API và quy trình nghiệp vụ
3. **Triển khai code**: Viết code theo kiến trúc phân tầng
4. **Kiểm thử đơn vị**: Viết unit test, đảm bảo chức năng hoạt động chính xác
5. **Kiểm thử tích hợp**: Tiến hành kiểm thử tích hợp module, kiểm thử tương tác giữa module thành viên và các module khác
6. **Kiểm thử hiệu năng**: Kiểm thử hiệu năng cho các thao tác tần suất cao như biến động điểm kinh nghiệm và điểm thưởng
7. **Rà soát code**: Gửi code để rà soát (code review), đảm bảo chất lượng code
8. **Triển khai vận hành**: Triển khai lên môi trường production

## 6. Các vấn đề thường gặp và giải pháp

### 6.1 Nâng hạng thành viên không kịp thời
**Vấn đề**: Người dùng đã đạt điều kiện nâng hạng nhưng hạng không được tự động nâng
**Cách khắc phục**:
1. Kiểm tra tác vụ tự động kiểm tra có chạy bình thường không
2. Kiểm tra cấu hình điều kiện nâng hạng có đúng không
3. Kiểm tra logic nâng cấp có tồn tại bug không
4. Cân nhắc thêm chức năng kích hoạt nâng hạng thủ công

### 6.2 Tính điểm thưởng không chính xác
**Vấn đề**: Kết quả tính điểm thưởng của người dùng không khớp với dự kiến
**Cách khắc phục**:
1. Kiểm tra cấu hình điểm thưởng có đúng không
2. Kiểm tra lịch sử biến động điểm thưởng để tìm ra nguyên nhân
3. Đảm bảo mọi biến động điểm thưởng đều được xử lý qua một API thống nhất
4. Thêm chức năng đối soát điểm thưởng, định kỳ kiểm tra tính nhất quán của điểm thưởng

### 6.3 Quyền lợi thành viên không có hiệu lực
**Vấn đề**: Người dùng được hưởng quyền lợi của một hạng nào đó nhưng thực tế quyền lợi không có hiệu lực
**Cách khắc phục**:
1. Kiểm tra hạng thành viên có đúng không
2. Kiểm tra cấu hình quyền lợi có đúng không
3. Kiểm tra logic áp dụng quyền lợi có bug không
4. Thêm log áp dụng quyền lợi để thuận tiện khắc phục sự cố

### 6.4 Nhiều người dùng nâng hạng cùng lúc gây ra vấn đề hiệu năng
**Vấn đề**: Có lượng lớn người dùng trong hệ thống cùng lúc đạt điều kiện nâng hạng, khiến hiệu năng hệ thống giảm sút
**Cách khắc phục**:
1. Tối ưu thuật toán kiểm tra nâng hạng, giảm số lần truy vấn cơ sở dữ liệu
2. Dùng phương thức xử lý bất đồng bộ, tránh chặn luồng chính
3. Cân nhắc xử lý theo mức ưu tiên, xử lý người dùng hoạt động thường xuyên trước
4. Tăng tài nguyên hệ thống, nâng cao khả năng xử lý đồng thời

## 7. Mở rộng và tùy biến

### 7.1 Thêm kênh nhận điểm kinh nghiệm mới
1. **Cấu hình điểm kinh nghiệm**: Thêm kênh nhận điểm kinh nghiệm mới và số điểm tương ứng trong cấu hình hệ thống
2. **Sửa code**:
   - Thêm lời gọi cộng điểm kinh nghiệm trong logic nghiệp vụ liên quan
   - Thêm loại bản ghi điểm kinh nghiệm tương ứng trong UserBillServices
3. **Sửa frontend**: Thêm lối vào nhận điểm kinh nghiệm và phần hướng dẫn tương ứng ở frontend

### 7.2 Thêm cách sử dụng điểm thưởng mới
1. **Cấu hình điểm thưởng**: Thêm cách sử dụng điểm thưởng mới và quy tắc tiêu hao trong cấu hình hệ thống
2. **Sửa code**:
   - Thêm lời gọi trừ điểm thưởng trong logic nghiệp vụ liên quan
   - Thêm loại bản ghi điểm thưởng tương ứng trong UserBillServices
3. **Sửa frontend**: Thêm lối vào sử dụng điểm thưởng và phần hướng dẫn tương ứng ở frontend

### 7.3 Mở rộng quyền lợi thành viên
1. **Sửa cơ sở dữ liệu**: Thêm trường quyền lợi mới vào bảng system_user_level
2. **Sửa code**:
   - Sửa chức năng cấu hình và quản lý hạng thành viên
   - Thêm code kiểm tra và áp dụng quyền lợi trong logic nghiệp vụ liên quan
3. **Sửa frontend**: Thêm phần hiển thị quyền lợi và lối vào sử dụng tương ứng ở frontend

## 8. Lịch sử cập nhật tài liệu

| Phiên bản | Ngày cập nhật | Nội dung cập nhật | Người cập nhật |
|------|----------|----------|--------|
| 1.0 | 2026-01-19 | Phiên bản đầu tiên, bao gồm các chức năng cốt lõi như quản lý hạng thành viên, quản lý hạng người dùng, quản lý điểm kinh nghiệm, quản lý điểm thưởng, quản lý quyền lợi thành viên, cơ chế nâng hạng thành viên | AI Assistant |

## 9. Phụ lục

### 9.1 Sơ đồ quan hệ các lớp cốt lõi

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    Models       │     │     Daos        │     │    Services     │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                         ┌─────────────────┐
                         │ Controllers     │
                         └─────────────────┘
                                 │
                         ┌─────────────────┐
                         │     Routes      │
                         └─────────────────┘
```

### 9.2 Sơ đồ quy trình nâng hạng thành viên

```
Quy trình nâng hạng thành viên:
1. Người dùng thực hiện hành vi nhận điểm kinh nghiệm (điểm danh, đặt hàng, mời bạn, v.v.)
2. Hệ thống cộng điểm kinh nghiệm cho người dùng, ghi nhận biến động điểm kinh nghiệm
3. Kiểm tra điểm kinh nghiệm hiện tại của người dùng có đạt yêu cầu của hạng tiếp theo không
4. Đạt yêu cầu, cập nhật hạng người dùng
5. Ghi log thay đổi hạng
6. Gửi thông báo nâng hạng cho người dùng
7. Cập nhật các quyền lợi mà người dùng được hưởng
```

### 9.3 Sơ đồ quy trình nhận và sử dụng điểm thưởng

```
Quy trình nhận và sử dụng điểm thưởng:
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Nhận điểm thưởng       │     │   Quản lý điểm thưởng       │     │   Sử dụng điểm thưởng       │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Điểm danh nhận điểm   │     │   Cộng điểm       │     │   Khấu trừ tiền hàng   │
│   Đặt hàng nhận điểm   │     │   Trừ điểm       │     │   Đổi tại cửa hàng đổi điểm   │
│   Mời bạn nhận điểm   │     │   Lịch sử điểm       │     │   Tiêu điểm khi tham gia chương trình   │
│   Tham gia chương trình nhận điểm   │     │   Thống kê điểm       │     └─────────────────┘
└─────────────────┘     └─────────────────┘
```

### 9.4 Danh sách quyền lợi thành viên

| Tên quyền lợi | Mô tả |
|----------|------|
| Ưu đãi chiết khấu | Mỗi hạng được hưởng mức chiết khấu sản phẩm khác nhau |
| Biểu tượng hạng | Mỗi hạng có biểu tượng và ảnh nền riêng |
| Tăng tốc điểm kinh nghiệm | Thành viên hạng cao nhận điểm kinh nghiệm nhanh hơn |
| CSKH riêng | Thành viên hạng cao được hưởng dịch vụ CSKH riêng |
| Ưu tiên giao hàng | Đơn hàng của thành viên hạng cao được ưu tiên giao hàng |
| Ưu đãi sinh nhật | Thành viên nhận ưu đãi riêng vào dịp sinh nhật |
| Chương trình dành riêng | Thành viên hạng cao được tham gia các chương trình dành riêng |
| Nhân đôi điểm thưởng | Thành viên hạng cao nhận điểm thưởng với hệ số nhân cao hơn |

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
