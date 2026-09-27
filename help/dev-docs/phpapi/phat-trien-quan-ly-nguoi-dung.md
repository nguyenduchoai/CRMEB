# Tài liệu phát triển quản lý người dùng hệ thống

## 1. Tổng quan tài liệu

### 1.1 Mục đích tài liệu
Tài liệu này nhằm mô tả chi tiết kiến trúc thiết kế, chức năng cốt lõi, cách triển khai code và quy chuẩn phát triển của module quản lý người dùng trong hệ thống CRMEB, cung cấp tài liệu tham khảo phát triển đầy đủ cho lập trình viên hệ thống.

### 1.2 Phạm vi áp dụng
Tài liệu này dành cho lập trình viên, nhân viên bảo trì và nhà phát triển thứ cấp của hệ thống CRMEB, dùng để hướng dẫn công việc phát triển, mở rộng và bảo trì module quản lý người dùng.

### 1.3 Định nghĩa thuật ngữ
| Thuật ngữ | Giải thích |
|------|------|
| Người dùng | Người dùng cuối đã đăng ký trên hệ thống, bao gồm người dùng thông thường, người dùng thành viên, v.v. |
| Hạng thành viên | Hạng của người dùng được phân chia dựa trên các điều kiện như số tiền chi tiêu, điểm thưởng, v.v. |
| Nhóm người dùng | Nhóm tùy chỉnh dùng để quản lý người dùng |
| Nhãn người dùng | Nhãn dùng để đánh dấu đặc điểm của người dùng |
| Quan hệ giới thiệu | Quan hệ liên kết giới thiệu giữa các người dùng |
| Tiếp thị liên kết | Mô hình chia sẻ doanh thu bán hàng dựa trên quan hệ giới thiệu |

## 2. Các mô-đun chức năng cốt lõi

### 2.1 Quản lý cơ bản người dùng
- **Danh sách người dùng**: Hiển thị thông tin cơ bản của người dùng, hỗ trợ tìm kiếm đa điều kiện, phân trang
- **Chi tiết người dùng**: Xem đầy đủ thông tin người dùng, bao gồm thông tin cơ bản, thông tin tài chính, thông tin cấp độ, v.v.
- **Thêm người dùng**: Thêm người dùng thủ công từ trang quản trị
- **Sửa người dùng**: Chỉnh sửa thông tin người dùng
- **Xóa người dùng**: Xóa người dùng (xóa vật lý/xóa logic)
- **Quản lý trạng thái người dùng**: Kích hoạt/vô hiệu hóa tài khoản người dùng
- **Đặt lại mật khẩu**: Đặt lại mật khẩu đăng nhập của người dùng

### 2.2 Quản lý tài chính và điểm thưởng của người dùng
- **Quản lý số dư**: Tăng/giảm số dư của người dùng, tạo bản ghi biến động số dư
- **Quản lý điểm thưởng**: Tăng/giảm điểm thưởng của người dùng, tạo bản ghi biến động điểm thưởng
- **Quản lý hoa hồng**: Quản lý hoa hồng tiếp thị liên kết của người dùng, tạo bản ghi hoa hồng

### 2.3 Quản lý hạng thành viên
- **Tặng hạng**: Tặng hạng thành viên thủ công từ trang quản trị
- **Xóa hạng**: Xóa hạng thành viên của người dùng
- **Quản lý thời hạn**: Quản lý thời hạn hiệu lực của thành viên trả phí
- **Nhiệm vụ lên hạng**: Cấu hình nhiệm vụ thăng hạng thành viên

### 2.4 Quản lý phân loại người dùng
- **Nhóm người dùng**: Tạo, sửa, xóa nhóm người dùng, phân bổ người dùng vào các nhóm khác nhau
- **Nhãn người dùng**: Tạo, sửa, xóa nhãn người dùng, thêm/gỡ nhãn cho người dùng
- **Danh mục nhãn**: Quản lý danh mục nhãn

### 2.5 Quản lý quan hệ giới thiệu
- **Quản lý người giới thiệu**: Quản lý quan hệ giới thiệu của người dùng
- **Liên kết giới thiệu**: Liên kết/hủy liên kết quan hệ giới thiệu của người dùng
- **Thống kê giới thiệu**: Thống kê dữ liệu giới thiệu của người dùng

### 2.6 Chức năng khác
- **Đồng bộ người dùng WeChat**: Đồng bộ thông tin người dùng WeChat
- **Quản lý hủy tài khoản người dùng**: Xử lý yêu cầu hủy tài khoản của người dùng
- **Cấu hình quà tặng người mới**: Cấu hình phần thưởng đăng ký cho người dùng mới

## 3. Cấu trúc và thiết kế code

### 3.1 Thiết kế kiến trúc
Hệ thống sử dụng thiết kế kiến trúc phân tầng, tuân thủ nghiêm ngặt mẫu thiết kế MVC:
```
├── Model：Tầng model, định nghĩa cấu trúc dữ liệu và quan hệ liên kết
├── Dao：Tầng truy cập dữ liệu, xử lý các thao tác cơ sở dữ liệu
├── Services：Tầng logic nghiệp vụ, triển khai logic nghiệp vụ cốt lõi
├── Controller：Tầng điều khiển, xử lý request và response HTTP
└── Route：Tầng route, định nghĩa route API
```

### 3.2 Cấu trúc tệp cốt lõi

#### 3.2.1 Tầng mô hình (Model)
**Đường dẫn tệp**: `crmeb/app/model/user/User.php`

**Chức năng cốt lõi**:
- Định nghĩa cấu trúc bảng người dùng và khóa chính
- Bao gồm các trường tự động điền (add_time, add_ip, last_time, last_ip)
- Định nghĩa quan hệ liên kết với các bảng khác
- Cung cấp đa dạng các phương thức searcher (bộ tìm kiếm)

**Các quan hệ liên kết chính**:
| Tên liên kết | Model liên kết | Loại liên kết |
|----------|----------|----------|
| systemUserLevel | SystemUserLevel | Một-một |
| userGroup | UserGroup | Một-một |
| spreadUser | User | Một-một |
| spreadCount | User | Một-nhiều |
| label | UserLabel | Nhiều-nhiều |
| address | UserAddress | Một-nhiều |
| extract | UserExtract | Một-nhiều |
| order | StoreOrder | Một-nhiều |
| agentLevel | AgentLevel | Một-một |
| bill | UserBill | Một-nhiều |

#### 3.2.2 Tầng truy cập dữ liệu (DAO)
**Đường dẫn tệp**: `crmeb/app/dao/user/UserDao.php`

**Chức năng cốt lõi**:
- Kế thừa từ BaseDao, triển khai các thao tác cơ sở dữ liệu cơ bản
- Cung cấp các phương thức như truy vấn danh sách người dùng, đếm số lượng thống kê, cập nhật trường, v.v.
- Hỗ trợ phân trang, tìm kiếm theo điều kiện
- Bao gồm các truy vấn đặc thù như người dùng CTV, danh sách người được giới thiệu, v.v.

#### 3.2.3 Tầng dịch vụ nghiệp vụ (Services)
**Đường dẫn tệp**: `crmeb/app/services/user/UserServices.php`

**Chức năng cốt lõi**:
- Quản lý thông tin cơ bản của người dùng (thêm, xóa, sửa, truy vấn)
- Quản lý điểm thưởng và số dư (tăng, giảm, ghi nhận)
- Quản lý hạng thành viên (tặng, xóa, quản lý thời hạn)
- Quản lý nhãn và nhóm người dùng
- Quản lý quan hệ giới thiệu
- Đồng bộ người dùng WeChat
- Phần thưởng quà tặng người mới
- Tạo các loại dữ liệu thống kê

#### 3.2.4 Tầng điều khiển (Controller)
**Đường dẫn tệp**: `crmeb/app/adminapi/controller/v1/user/User.php`

**Chức năng cốt lõi**:
- Xử lý các yêu cầu API quản lý người dùng ở trang quản trị
- Cung cấp các API như danh sách, thêm, sửa, xóa người dùng, v.v.
- Hỗ trợ tặng hạng thành viên, quản lý thời hạn thành viên trả phí
- Cung cấp chức năng điều chỉnh điểm thưởng và số dư
- Hỗ trợ quản lý nhãn và nhóm người dùng

#### 3.2.5 Cấu hình route
**Đường dẫn tệp**: `crmeb/app/adminapi/route/user.php`

**Chức năng cốt lõi**:
- Định nghĩa hệ thống route quản lý người dùng hoàn chỉnh
- Bao gồm các module như quản lý người dùng, quản lý cấp độ, quản lý nhóm, quản lý nhãn, v.v.
- Cấu hình middleware phân quyền

## 4. Thiết kế cơ sở dữ liệu

### 4.1 Bảng người dùng (user)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| uid | int(10) unsigned | ID người dùng (khóa chính) |
| user_type | tinyint(1) | Loại người dùng |
| user_level | int(10) | ID hạng thành viên |
| agent_level | int(10) | ID hạng phân phối |
| group_id | int(10) | ID nhóm người dùng |
| spread_uid | int(10) | ID người giới thiệu |
| nickname | varchar(50) | Biệt danh |
| avatar | varchar(255) | Ảnh đại diện |
| phone | varchar(20) | Số điện thoại |
| email | varchar(50) | Email |
| password | varchar(60) | Mật khẩu |
| salt | varchar(10) | Salt mật khẩu |
| sex | tinyint(1) | Giới tính |
| birthday | date | Ngày sinh |
| add_time | int(10) | Thời gian thêm |
| add_ip | varchar(15) | IP khi thêm |
| last_time | int(10) | Thời gian đăng nhập cuối |
| last_ip | varchar(15) | IP đăng nhập lần cuối |
| user_money | decimal(10,2) | Số dư người dùng |
| frozen_money | decimal(10,2) | Số tiền bị đóng băng |
| pay_money | decimal(10,2) | Số tiền chi tiêu |
|Trường điểm thưởng | int(10) | Điểm thưởng |
| sign_num | int(10) | Số lần điểm danh liên tiếp |
| status | tinyint(1) | Trạng thái người dùng |
| del | tinyint(1) | Đã xóa |

### 4.2 Bảng mở rộng người dùng

- **Bảng nhãn người dùng (user_label)**: Lưu trữ nhãn người dùng
- **Bảng nhóm người dùng (user_group)**: Lưu trữ nhóm người dùng
- **Bảng địa chỉ người dùng (user_address)**: Lưu trữ địa chỉ nhận hàng của người dùng
- **Bảng biến động số dư người dùng (user_bill)**: Ghi nhận biến động số dư, điểm thưởng, hoa hồng của người dùng
- **Bảng quan hệ giới thiệu người dùng (user_spread)**: Ghi nhận quan hệ giới thiệu của người dùng
- **Bảng hạng thành viên người dùng (user_member)**: Ghi nhận thông tin hạng thành viên của người dùng

## 5. Mô tả API

### 5.1 API cơ bản

#### 5.1.1 Danh sách người dùng
- **URL yêu cầu**: `/adminapi/v1/user/list`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: user/user/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - keyword: từ khóa tìm kiếm
  - date: khoảng thời gian
  - user_level: hạng thành viên
  - group_id: nhóm người dùng
  - status: trạng thái người dùng
- **Kết quả trả về**: Dữ liệu danh sách người dùng

#### 5.1.2 Chi tiết người dùng
- **URL yêu cầu**: `/adminapi/v1/user/detail/{uid}`
- **Phương thức yêu cầu**: GET
- **Quyền yêu cầu**: user/user/detail
- **Kết quả trả về**: Thông tin chi tiết người dùng

#### 5.1.3 Thêm người dùng
- **URL yêu cầu**: `/adminapi/v1/user/create`
- **Phương thức yêu cầu**: POST
- **Quyền yêu cầu**: user/user/create
- **Tham số yêu cầu**: Thông tin cơ bản của người dùng
- **Kết quả trả về**: Kết quả thao tác

#### 5.1.4 Sửa người dùng
- **URL yêu cầu**: `/adminapi/v1/user/update/{uid}`
- **Phương thức yêu cầu**: PUT
- **Quyền yêu cầu**: user/user/update
- **Tham số yêu cầu**: Thông tin cơ bản của người dùng
- **Kết quả trả về**: Kết quả thao tác

#### 5.1.5 Xóa người dùng
- **URL yêu cầu**: `/adminapi/v1/user/delete/{uid}`
- **Phương thức yêu cầu**: DELETE
- **Quyền yêu cầu**: user/user/delete
- **Kết quả trả về**: Kết quả thao tác

### 5.2 API liên quan đến tài chính

#### 5.2.1 Điều chỉnh số dư người dùng
- **URL yêu cầu**: `/adminapi/v1/user/money/{uid}`
- **Phương thức yêu cầu**: PUT
- **Quyền yêu cầu**: user/user/money
- **Tham số yêu cầu**:
  - money: số tiền biến động
  - mark: ghi chú biến động
- **Kết quả trả về**: Kết quả thao tác

#### 5.2.2 Điều chỉnh điểm thưởng người dùng
- **URL yêu cầu**: `/adminapi/v1/user/integral/{uid}`
- **Phương thức yêu cầu**: PUT
- **Quyền yêu cầu**: user/user/integral
- **Tham số yêu cầu**:
  - integral: số điểm thưởng biến động
  - mark: ghi chú biến động
- **Kết quả trả về**: Kết quả thao tác

### 5.3 API liên quan đến hạng thành viên

#### 5.3.1 Tặng hạng thành viên
- **URL yêu cầu**: `/adminapi/v1/user/level/{uid}`
- **Phương thức yêu cầu**: PUT
- **Quyền yêu cầu**: user/user/level
- **Tham số yêu cầu**:
  - level: ID hạng
  - time: thời hạn hiệu lực (ngày)
- **Kết quả trả về**: Kết quả thao tác

#### 5.3.2 Xóa hạng thành viên
- **URL yêu cầu**: `/adminapi/v1/user/level/clear/{uid}`
- **Phương thức yêu cầu**: PUT
- **Quyền yêu cầu**: user/user/level/clear
- **Kết quả trả về**: Kết quả thao tác

### 5.4 API nhóm và nhãn người dùng

#### 5.4.1 Thiết lập nhóm người dùng
- **URL yêu cầu**: `/adminapi/v1/user/group/{uid}`
- **Phương thức yêu cầu**: PUT
- **Quyền yêu cầu**: user/user/group
- **Tham số yêu cầu**:
  - group_id: ID nhóm
- **Kết quả trả về**: Kết quả thao tác

#### 5.4.2 Thiết lập nhãn người dùng
- **URL yêu cầu**: `/adminapi/v1/user/label/{uid}`
- **Phương thức yêu cầu**: PUT
- **Quyền yêu cầu**: user/user/label
- **Tham số yêu cầu**:
  - label_ids: Mảng ID nhãn
- **Kết quả trả về**: Kết quả thao tác

## 5. Quy chuẩn và quy trình phát triển

### 5.1 Quy chuẩn code
1. **Quy chuẩn đặt tên**:
   - Tên lớp dùng kiểu PascalCase (UpperCamelCase)
   - Tên phương thức dùng kiểu camelCase
   - Tên biến dùng kiểu camelCase
   - Tên hằng số viết hoa toàn bộ, phân tách bằng dấu gạch dưới

2. **Cấu trúc code**:
   - Tuân thủ nghiêm ngặt kiến trúc phân tầng, cấm gọi vượt tầng
   - Mỗi phương thức chỉ đảm nhận một trách nhiệm, số dòng code không vượt quá 50 dòng
   - Thêm chú thích hợp lý, mô tả chức năng của phương thức, ý nghĩa tham số và giá trị trả về

3. **Thao tác cơ sở dữ liệu**:
   - Mọi thao tác cơ sở dữ liệu phải được thực hiện thông qua tầng Dao
   - Dùng query builder do ORM cung cấp, tránh viết SQL trực tiếp
   - Thao tác hàng loạt dùng transaction để xử lý

### 5.2 Quy trình phát triển
1. **Phân tích yêu cầu**: Làm rõ yêu cầu chức năng và logic nghiệp vụ
2. **Giai đoạn thiết kế**: Thiết kế cấu trúc dữ liệu, API và quy trình nghiệp vụ
3. **Triển khai code**: Viết code theo kiến trúc phân tầng
4. **Kiểm thử đơn vị**: Viết unit test, đảm bảo chức năng hoạt động chính xác
5. **Kiểm thử tích hợp**: Tiến hành kiểm thử tích hợp module
6. **Rà soát code**: Gửi code để rà soát (code review), đảm bảo chất lượng code
7. **Triển khai vận hành**: Triển khai lên môi trường production

## 6. Các vấn đề thường gặp và giải pháp

### 6.1 Mã hóa mật khẩu người dùng
**Vấn đề**: Mật khẩu người dùng được mã hóa và lưu trữ như thế nào?
**Giải pháp**: Hệ thống dùng thuật toán bcrypt để mã hóa và lưu trữ mật khẩu người dùng, đảm bảo an toàn cho mật khẩu.

### 6.2 Quản lý trạng thái đăng nhập của người dùng
**Vấn đề**: Làm thế nào để quản lý trạng thái đăng nhập của người dùng?
**Giải pháp**: Hệ thống dùng cơ chế token để quản lý trạng thái đăng nhập của người dùng, sau khi đăng nhập thành công sẽ tạo token, các yêu cầu tiếp theo mang theo token để xác thực danh tính.

### 6.3 Liên kết quan hệ giới thiệu
**Vấn đề**: Làm thế nào để xử lý việc liên kết quan hệ giới thiệu của người dùng?
**Giải pháp**: Khi người dùng đăng ký, hệ thống lấy ID người giới thiệu thông qua tham số URL hoặc mã mời, rồi tự động liên kết quan hệ giới thiệu.

### 6.4 Tính thời hạn hiệu lực của hạng thành viên
**Vấn đề**: Làm thế nào để tính thời hạn hiệu lực của hạng thành viên?
**Giải pháp**: Hệ thống tính thời điểm hết hạn của hạng thành viên bằng thời gian hiện tại cộng với số ngày hiệu lực, sau khi hết hạn sẽ tự động hạ hạng.

## 7. Mở rộng và tùy biến

### 7.1 Mở rộng chức năng
Hệ thống được thiết kế có khả năng mở rộng tốt, có thể mở rộng chức năng theo các cách sau:

1. **Thêm model mới**: Kế thừa BaseModel, định nghĩa cấu trúc dữ liệu mới
2. **Thêm service mới**: Kế thừa BaseServices, triển khai logic nghiệp vụ mới
3. **Thêm controller mới**: Kế thừa BaseController, xử lý các yêu cầu API mới
4. **Thêm route mới**: Định nghĩa quy tắc route mới trong tệp route

### 7.2 Mở rộng bằng sự kiện
Hệ thống kích hoạt sự kiện tại các điểm nghiệp vụ quan trọng, có thể mở rộng chức năng bằng cách lắng nghe sự kiện:

1. **Sự kiện người dùng đăng ký**: UserRegisterEvent
2. **Sự kiện người dùng đăng nhập**: UserLoginEvent
3. **Sự kiện biến động điểm thưởng của người dùng**: UserIntegralChangeEvent
4. **Sự kiện biến động số dư của người dùng**: UserMoneyChangeEvent
5. **Sự kiện thay đổi hạng thành viên**: UserLevelChangeEvent

### 7.3 Mở rộng bằng hook
Hệ thống cung cấp nhiều điểm hook, có thể mở rộng chức năng thông qua hàm hook:

1. **Hook trước khi người dùng đăng ký**: user_register_before
2. **Hook sau khi người dùng đăng ký**: user_register_after
3. **Hook trước khi người dùng đăng nhập**: user_login_before
4. **Hook sau khi người dùng đăng nhập**: user_login_after
5. **Hook trước khi cập nhật thông tin người dùng**: user_update_before
6. **Hook sau khi cập nhật thông tin người dùng**: user_update_after

## 8. Lịch sử cập nhật tài liệu

| Phiên bản | Ngày cập nhật | Nội dung cập nhật | Người cập nhật |
|------|----------|----------|--------|
| 1.0 | 2026-01-19 | Phiên bản đầu tiên, bao gồm các chức năng cốt lõi của quản lý người dùng | AI Assistant |

## 9. Phụ lục

### 9.1 Sơ đồ quan hệ các lớp cốt lõi

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    UserModel    │     │     UserDao     │     │   UserServices  │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                         ┌─────────────────┐
                         │   UserController│
                         └─────────────────┘
                                 │
                         ┌─────────────────┐
                         │     Routes      │
                         └─────────────────┘
```

### 9.2 Lưu đồ nghiệp vụ cốt lõi

```
Quy trình đăng ký người dùng:
1. Người dùng gửi thông tin đăng ký
2. Xác thực số điện thoại/email
3. Tạo bản ghi người dùng
4. Liên kết quan hệ giới thiệu
5. Kích hoạt phần thưởng đăng ký
6. Trả về kết quả đăng ký

Quy trình thăng hạng thành viên:
1. Người dùng đáp ứng điều kiện hạng
2. Hệ thống tự động kiểm tra điều kiện hạng
3. Cập nhật hạng người dùng
4. Ghi log thay đổi hạng
5. Kích hoạt sự kiện thay đổi hạng

Quy trình biến động điểm thưởng của người dùng:
1. Kích hoạt sự kiện biến động điểm thưởng
2. Xác thực tính hợp lệ của biến động
3. Cập nhật điểm thưởng của người dùng
4. Tạo bản ghi biến động điểm thưởng
5. Trả về kết quả biến động
```
---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
