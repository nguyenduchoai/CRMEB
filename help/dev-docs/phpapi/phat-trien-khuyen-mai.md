# Tài liệu phát triển hoạt động marketing

## 1. Tổng quan tài liệu

### 1.1 Mục đích tài liệu
Tài liệu này nhằm trình bày chi tiết kiến trúc thiết kế, chức năng cốt lõi, cách triển khai mã nguồn và quy chuẩn phát triển của mô-đun hoạt động marketing trong hệ thống CRMEB, cung cấp tài liệu tham khảo phát triển đầy đủ cho lập trình viên hệ thống.

### 1.2 Phạm vi áp dụng
Tài liệu này dành cho lập trình viên, nhân viên bảo trì và người phát triển thứ cấp hệ thống CRMEB, dùng để hướng dẫn công việc phát triển, mở rộng và bảo trì mô-đun hoạt động marketing.

### 1.3 Định nghĩa thuật ngữ
| Thuật ngữ | Giải thích |
|------|------|
| Flash sale giờ vàng | Trong một khoảng thời gian nhất định, sản phẩm được bán với giá thấp hơn giá gốc |
| Săn giảm giá sản phẩm | Người dùng mời bạn bè giúp giảm giá để mua sản phẩm với giá thấp hơn |
| Mua chung nhiều người | Nhiều người dùng cùng mua sản phẩm để được hưởng giá thấp hơn |
| Phiếu giảm giá | Phiếu điện tử có thể dùng để khấu trừ vào giá sản phẩm |
| SPU | Đơn vị sản phẩm tiêu chuẩn hóa, ví dụ "iPhone 13" |
| SKU | Đơn vị lưu kho, ví dụ "iPhone 13 128G màu trắng" |
| Mở nhóm mua chung | Tạo hoạt động mua chung |
| Tham gia nhóm | Tham gia hoạt động mua chung do người khác tạo |
| Giúp sức | Giúp bạn bè săn giảm giá |

## 2. Các mô-đun chức năng cốt lõi

### 2.1 Flash sale giới hạn thời gian
- **Cấu hình flash sale**: Thiết lập khung giờ flash sale, sản phẩm tham gia, giá flash sale
- **Quản lý sản phẩm flash sale**: Thêm/sửa/xóa sản phẩm tham gia flash sale
- **Quản lý khung giờ flash sale**: Thiết lập khung giờ của hoạt động flash sale
- **Lịch sử flash sale**: Xem lịch sử flash sale của người dùng
- **Quản lý trạng thái flash sale**: Bật/tắt hoạt động flash sale

### 2.2 Săn giảm giá sản phẩm
- **Cấu hình sản phẩm săn giảm giá**: Thiết lập sản phẩm săn giảm giá, giá khởi điểm, giá thấp nhất, số lần săn giảm giá
- **Quản lý hoạt động săn giảm giá**: Thêm/sửa/xóa hoạt động săn giảm giá
- **Quản lý lịch sử săn giảm giá**: Xem lịch sử săn giảm giá của người dùng
- **Lịch sử giúp sức săn giảm giá**: Xem lịch sử giúp sức săn giảm giá của người dùng
- **Quản lý trạng thái săn giảm giá**: Bật/tắt hoạt động săn giảm giá

### 2.3 Mua chung nhiều người
- **Cấu hình sản phẩm mua chung**: Thiết lập sản phẩm mua chung, giá mua chung, số người để thành nhóm, thời hạn hiệu lực của nhóm mua chung
- **Quản lý hoạt động mua chung**: Thêm/sửa/xóa hoạt động mua chung
- **Quản lý lịch sử mua chung**: Xem lịch sử mua chung, trạng thái thành nhóm
- **Thiết lập quy tắc mua chung**: Thiết lập quy tắc mua chung, ví dụ có cho phép tham gia nhóm nhiều lần hay không
- **Quản lý trạng thái mua chung**: Bật/tắt hoạt động mua chung

### 2.4 Quản lý phiếu giảm giá
- **Cấu hình mẫu phiếu giảm giá**: Thiết lập loại phiếu giảm giá (giảm giá theo đơn tối thiểu, chiết khấu), mệnh giá, điều kiện sử dụng, thời hạn hiệu lực
- **Quản lý phát phiếu giảm giá**: Phát phiếu giảm giá thủ công, tự động và hàng loạt
- **Quản lý phiếu giảm giá của người dùng**: Xem phiếu giảm giá người dùng đã nhận và tình hình sử dụng
- **Thống kê sử dụng phiếu giảm giá**: Thống kê số lượng phiếu giảm giá đã được nhận, tỷ lệ sử dụng, số tiền được giảm
- **Quản lý trạng thái phiếu giảm giá**: Bật/tắt phiếu giảm giá

### 2.5 Cửa hàng đổi điểm
- **Cấu hình sản phẩm đổi điểm**: Thiết lập sản phẩm đổi điểm, số điểm thưởng cần đổi, tồn kho
- **Quản lý sản phẩm đổi điểm**: Thêm/sửa/xóa sản phẩm đổi điểm
- **Lịch sử đổi điểm**: Xem lịch sử đổi điểm thưởng của người dùng
- **Thiết lập quy tắc điểm thưởng**: Thiết lập quy tắc nhận và sử dụng điểm thưởng

### 2.6 Hoạt động đặt trước
- **Cấu hình sản phẩm đặt trước**: Thiết lập sản phẩm đặt trước, giá đặt trước, tiền đặt cọc, thời gian thanh toán số tiền còn lại
- **Quản lý hoạt động đặt trước**: Thêm/sửa/xóa hoạt động đặt trước
- **Quản lý đơn hàng đặt trước**: Xem đơn hàng đặt trước, tình hình thanh toán tiền đặt cọc, tình hình thanh toán số tiền còn lại
- **Quản lý trạng thái đặt trước**: Bật/tắt hoạt động đặt trước

## 3. Cấu trúc và thiết kế code

### 3.1 Thiết kế kiến trúc
Hệ thống sử dụng thiết kế kiến trúc phân tầng, tuân thủ nghiêm ngặt mẫu thiết kế MVC:
```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   ActivityModel │     │   ActivityDao   │     │ ActivityServices│
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                         ┌─────────────────┐
                         │ ActivityController│
                         └─────────────────┘
                                 │
                         ┌─────────────────┐
                         │     Routes      │
                         └─────────────────┘
```

### 3.2 Cấu trúc tệp cốt lõi

#### 3.2.1 Tầng mô hình (Model)
Tất cả model của hoạt động marketing đều nằm trong thư mục `crmeb/app/model/activity/`, được phân loại theo loại hoạt động:

- **Hoạt động flash sale**:
  - `seckill/StoreSeckill.php` - Model sản phẩm flash sale

- **Hoạt động săn giảm giá**:
  - `bargain/StoreBargain.php` - Model sản phẩm săn giảm giá
  - `bargain/StoreBargainUser.php` - Model bản ghi người dùng tham gia săn giảm giá
  - `bargain/StoreBargainUserHelp.php` - Model bản ghi giúp sức săn giảm giá

- **Hoạt động mua chung**:
  - `combination/StoreCombination.php` - Model sản phẩm mua chung
  - `combination/StorePink.php` - Model bản ghi mua chung

- **Hoạt động phiếu giảm giá**:
  - `coupon/StoreCoupon.php` - Model mẫu phiếu giảm giá
  - `coupon/StoreCouponIssue.php` - Model bản ghi phát phiếu giảm giá
  - `coupon/StoreCouponProduct.php` - Model sản phẩm áp dụng phiếu giảm giá
  - `coupon/StoreCouponUser.php` - Model phiếu giảm giá của người dùng

#### 3.2.2 Tầng truy cập dữ liệu (DAO)
Tất cả DAO của hoạt động marketing đều nằm trong thư mục `crmeb/app/dao/activity/`, được phân loại theo loại hoạt động:

- **Hoạt động flash sale**:
  - `seckill/StoreSeckillDao.php` - DAO sản phẩm flash sale

- **Hoạt động săn giảm giá**:
  - `bargain/StoreBargainDao.php` - DAO sản phẩm săn giảm giá
  - `bargain/StoreBargainUserDao.php` - DAO bản ghi người dùng tham gia săn giảm giá
  - `bargain/StoreBargainUserHelpDao.php` - DAO bản ghi giúp sức săn giảm giá

- **Hoạt động mua chung**:
  - `combination/StoreCombinationDao.php` - DAO sản phẩm mua chung
  - `combination/StorePinkDao.php` - DAO bản ghi mua chung

- **Hoạt động phiếu giảm giá**:
  - `coupon/StoreCouponDao.php` - DAO mẫu phiếu giảm giá
  - `coupon/StoreCouponIssueDao.php` - DAO bản ghi phát phiếu giảm giá
  - `coupon/StoreCouponProductDao.php` - DAO sản phẩm áp dụng phiếu giảm giá
  - `coupon/StoreCouponUserDao.php` - DAO phiếu giảm giá của người dùng

#### 3.2.3 Tầng dịch vụ nghiệp vụ (Services)
Tất cả Services của hoạt động marketing đều nằm trong thư mục `crmeb/app/services/activity/`, được phân loại theo loại hoạt động:

- **Hoạt động flash sale**:
  - `seckill/StoreSeckillServices.php` - Services sản phẩm flash sale

- **Hoạt động săn giảm giá**:
  - `bargain/StoreBargainServices.php` - Services sản phẩm săn giảm giá
  - `bargain/StoreBargainUserServices.php` - Services bản ghi người dùng tham gia săn giảm giá
  - `bargain/StoreBargainUserHelpServices.php` - Services bản ghi giúp sức săn giảm giá

- **Hoạt động mua chung**:
  - `combination/StoreCombinationServices.php` - Services sản phẩm mua chung
  - `combination/StorePinkServices.php` - Services bản ghi mua chung

- **Hoạt động phiếu giảm giá**:
  - `coupon/StoreCouponServices.php` - Services mẫu phiếu giảm giá
  - `coupon/StoreCouponIssueServices.php` - Services bản ghi phát phiếu giảm giá
  - `coupon/StoreCouponUserServices.php` - Services phiếu giảm giá của người dùng

#### 3.2.4 Tầng điều khiển (Controller)
Controller API dành cho quản trị viên nằm trong thư mục `crmeb/app/adminapi/controller/v1/marketing/`:

- `StoreSeckill.php` - Controller hoạt động flash sale
- `StoreBargain.php` - Controller hoạt động săn giảm giá
- `StoreCombination.php` - Controller hoạt động mua chung
- `StoreCoupon.php` - Controller phiếu giảm giá

Controller API phía di động nằm trong thư mục `crmeb/app/api/controller/v1/activity/`, cung cấp các API hoạt động để phía di động gọi.

#### 3.2.5 Cấu hình route
Cấu hình route của hoạt động marketing nằm trong tệp `crmeb/app/adminapi/route/marketing.php`, bao gồm các route thêm/xóa/sửa/tra cứu, quản lý trạng thái, thống kê, v.v. cho các loại hoạt động.

Cấu hình route phía di động nằm trong thư mục `crmeb/app/api/route/`, được phân loại theo phiên bản API.

## 4. Thiết kế cơ sở dữ liệu

### 4.1 Các bảng liên quan đến hoạt động flash sale

#### Bảng sản phẩm flash sale (store_seckill)
| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID sản phẩm flash sale |
| product_id | int(10) | ID sản phẩm liên kết |
| title | varchar(100) | Tiêu đề hoạt động flash sale |
| image | varchar(255) | Hình ảnh sản phẩm flash sale |
| unit_name | varchar(10) | Tên đơn vị |
| sec_price | decimal(10,2) | Giá flash sale |
| price | decimal(10,2) | Giá gốc |
| cost | decimal(10,2) | Giá vốn |
| description | varchar(255) | Mô tả hoạt động flash sale |
| start_time | int(10) | Thời gian bắt đầu |
| end_time | int(10) | Thời gian kết thúc |
| status | tinyint(1) | Trạng thái (0-tắt, 1-bật) |
| sales | int(10) | Lượt bán |
| stock | int(10) | Tồn kho |
| give_integral | int(10) | Tặng điểm thưởng |
| sort | int(10) | Thứ tự sắp xếp |
| is_delete | tinyint(1) | Đã xóa |
| add_time | int(10) | Thời gian thêm |

### 4.2 Các bảng liên quan đến hoạt động săn giảm giá

#### Bảng sản phẩm săn giảm giá (store_bargain)
| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID sản phẩm săn giảm giá |
| product_id | int(10) | ID sản phẩm liên kết |
| title | varchar(100) | Tiêu đề hoạt động săn giảm giá |
| image | varchar(255) | Hình ảnh sản phẩm săn giảm giá |
| unit_name | varchar(10) | Tên đơn vị |
| min_price | decimal(10,2) | Giá thấp nhất |
| price | decimal(10,2) | Giá gốc |
| cost | decimal(10,2) | Giá vốn |
| description | varchar(255) | Mô tả hoạt động săn giảm giá |
| start_time | int(10) | Thời gian bắt đầu |
| end_time | int(10) | Thời gian kết thúc |
| status | tinyint(1) | Trạng thái (0-tắt, 1-bật) |
| sales | int(10) | Lượt bán |
| stock | int(10) | Tồn kho |
| people_num | int(10) | Số lần mỗi người có thể khởi tạo săn giảm giá |
| bargain_num | int(10) | Số người có thể mời trong mỗi lần săn giảm giá |
| sort | int(10) | Thứ tự sắp xếp |
| is_delete | tinyint(1) | Đã xóa |
| add_time | int(10) | Thời gian thêm |

#### Bảng bản ghi người dùng tham gia săn giảm giá (store_bargain_user)
| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID bản ghi |
| uid | int(10) | ID người dùng |
| bargain_id | int(10) | ID sản phẩm săn giảm giá |
| price | decimal(10,2) | Giá cuối cùng sau khi săn giảm giá |
| status | tinyint(1) | Trạng thái (0-đang diễn ra, 1-thành công, 2-thất bại) |
| add_time | int(10) | Thời gian bắt đầu |
| stop_time | int(10) | Thời gian kết thúc |

#### Bảng bản ghi giúp sức săn giảm giá (store_bargain_user_help)
| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID bản ghi |
| bargain_user_id | int(10) | ID bản ghi người dùng săn giảm giá |
| uid | int(10) | ID người dùng giúp sức |
| price | decimal(10,2) | Số tiền đã giảm được |
| add_time | int(10) | Thời gian giúp sức |

### 4.3 Các bảng liên quan đến hoạt động mua chung

#### Bảng sản phẩm mua chung (store_combination)
| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID sản phẩm mua chung |
| product_id | int(10) | ID sản phẩm liên kết |
| title | varchar(100) | Tiêu đề hoạt động mua chung |
| image | varchar(255) | Hình ảnh sản phẩm mua chung |
| unit_name | varchar(10) | Tên đơn vị |
| pink_price | decimal(10,2) | Giá mua chung |
| price | decimal(10,2) | Giá gốc |
| cost | decimal(10,2) | Giá vốn |
| description | varchar(255) | Mô tả hoạt động mua chung |
| start_time | int(10) | Thời gian bắt đầu |
| end_time | int(10) | Thời gian kết thúc |
| status | tinyint(1) | Trạng thái (0-tắt, 1-bật) |
| sales | int(10) | Lượt bán |
| stock | int(10) | Tồn kho |
| people | int(10) | Số người để thành nhóm |
| sort | int(10) | Thứ tự sắp xếp |
| is_delete | tinyint(1) | Đã xóa |
| add_time | int(10) | Thời gian thêm |

#### Bảng bản ghi mua chung (store_pink)
| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID bản ghi |
| cid | int(10) | ID sản phẩm mua chung |
| uid | int(10) | ID người dùng mở nhóm |
| order_id | int(10) | ID đơn hàng mở nhóm |
| people | int(10) | Số người để thành nhóm |
| price | decimal(10,2) | Giá mua chung |
| status | tinyint(1) | Trạng thái (0-đang ghép nhóm, 1-đã thành nhóm, 2-thất bại) |
| add_time | int(10) | Thời gian mở nhóm |
| stop_time | int(10) | Thời gian kết thúc |

### 4.4 Các bảng liên quan đến phiếu giảm giá

#### Bảng mẫu phiếu giảm giá (store_coupon)
| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID phiếu giảm giá |
| type | tinyint(1) | Loại phiếu giảm giá (0-giảm giá theo đơn tối thiểu, 1-chiết khấu) |
| title | varchar(100) | Tên phiếu giảm giá |
| money | decimal(10,2) | Số tiền phiếu giảm giá |
| min_price | decimal(10,2) | Số tiền sử dụng tối thiểu |
| discount | decimal(10,2) | Chiết khấu (ví dụ 9.5 tức giá còn 95%) |
| use_type | tinyint(1) | Loại áp dụng (0-áp dụng toàn cửa hàng, 1-sản phẩm chỉ định, 2-danh mục chỉ định) |
| start_time | int(10) | Thời gian bắt đầu |
| end_time | int(10) | Thời gian kết thúc |
| status | tinyint(1) | Trạng thái (0-tắt, 1-bật) |
| total_count | int(10) | Tổng số lượng phát hành |
| give_count | int(10) | Số lượng đã phát |
| use_count | int(10) | Số lượng đã sử dụng |
| get_type | tinyint(1) | Cách nhận (0-nhận thủ công, 1-phát tự động) |
| is_perpetual | tinyint(1) | Có hiệu lực vĩnh viễn không |
| effective_days | int(10) | Số ngày hiệu lực |
| sort | int(10) | Thứ tự sắp xếp |
| add_time | int(10) | Thời gian thêm |

#### Bảng phiếu giảm giá của người dùng (store_coupon_user)
| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID bản ghi |
| cid | int(10) | ID phiếu giảm giá |
| uid | int(10) | ID người dùng |
| status | tinyint(1) | Trạng thái (0-chưa sử dụng, 1-đã sử dụng, 2-đã hết hạn) |
| get_time | int(10) | Thời gian nhận |
| use_time | int(10) | Thời gian sử dụng |
| order_id | int(10) | ID đơn hàng sử dụng |
| end_time | int(10) | Thời gian hết hạn |

## 5. Mô tả API

### 5.1 API hoạt động flash sale

#### 5.1.1 Danh sách sản phẩm flash sale
- **URL yêu cầu**: `/adminapi/v1/marketing/seckill/list`
- **Phương thức yêu cầu**: GET
- **Quyền cần có**: marketing/seckill/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - keyword: từ khóa tìm kiếm
  - status: Trạng thái (0-tắt, 1-bật)
- **Kết quả trả về**: Dữ liệu danh sách sản phẩm flash sale

#### 5.1.2 Thêm sản phẩm flash sale
- **URL yêu cầu**: `/adminapi/v1/marketing/seckill/create`
- **Phương thức yêu cầu**: POST
- **Quyền cần có**: marketing/seckill/create
- **Tham số yêu cầu**: Thông tin sản phẩm flash sale
- **Kết quả trả về**: Kết quả thao tác

#### 5.1.3 Sửa sản phẩm flash sale
- **URL yêu cầu**: `/adminapi/v1/marketing/seckill/update/{id}`
- **Phương thức yêu cầu**: PUT
- **Quyền cần có**: marketing/seckill/update
- **Tham số yêu cầu**: Thông tin sản phẩm flash sale
- **Kết quả trả về**: Kết quả thao tác

#### 5.1.4 Xóa sản phẩm flash sale
- **URL yêu cầu**: `/adminapi/v1/marketing/seckill/delete/{id}`
- **Phương thức yêu cầu**: DELETE
- **Quyền cần có**: marketing/seckill/delete
- **Kết quả trả về**: Kết quả thao tác

### 5.2 API hoạt động săn giảm giá

#### 5.2.1 Danh sách sản phẩm săn giảm giá
- **URL yêu cầu**: `/adminapi/v1/marketing/bargain/list`
- **Phương thức yêu cầu**: GET
- **Quyền cần có**: marketing/bargain/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - keyword: từ khóa tìm kiếm
  - status: Trạng thái (0-tắt, 1-bật)
- **Kết quả trả về**: Dữ liệu danh sách sản phẩm săn giảm giá

#### 5.2.2 Thêm sản phẩm săn giảm giá
- **URL yêu cầu**: `/adminapi/v1/marketing/bargain/create`
- **Phương thức yêu cầu**: POST
- **Quyền cần có**: marketing/bargain/create
- **Tham số yêu cầu**: Thông tin sản phẩm săn giảm giá
- **Kết quả trả về**: Kết quả thao tác

### 5.3 API hoạt động mua chung

#### 5.3.1 Danh sách sản phẩm mua chung
- **URL yêu cầu**: `/adminapi/v1/marketing/combination/list`
- **Phương thức yêu cầu**: GET
- **Quyền cần có**: marketing/combination/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - keyword: từ khóa tìm kiếm
  - status: Trạng thái (0-tắt, 1-bật)
- **Kết quả trả về**: Dữ liệu danh sách sản phẩm mua chung

#### 5.3.2 Thêm sản phẩm mua chung
- **URL yêu cầu**: `/adminapi/v1/marketing/combination/create`
- **Phương thức yêu cầu**: POST
- **Quyền cần có**: marketing/combination/create
- **Tham số yêu cầu**: Thông tin sản phẩm mua chung
- **Kết quả trả về**: Kết quả thao tác

### 5.4 API phiếu giảm giá

#### 5.4.1 Danh sách phiếu giảm giá
- **URL yêu cầu**: `/adminapi/v1/marketing/coupon/list`
- **Phương thức yêu cầu**: GET
- **Quyền cần có**: marketing/coupon/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - keyword: từ khóa tìm kiếm
  - status: Trạng thái (0-tắt, 1-bật)
- **Kết quả trả về**: Dữ liệu danh sách phiếu giảm giá

#### 5.4.2 Thêm phiếu giảm giá
- **URL yêu cầu**: `/adminapi/v1/marketing/coupon/create`
- **Phương thức yêu cầu**: POST
- **Quyền cần có**: marketing/coupon/create
- **Tham số yêu cầu**: Thông tin phiếu giảm giá
- **Kết quả trả về**: Kết quả thao tác

## 5. Quy chuẩn và quy trình phát triển

### 5.1 Quy chuẩn code
1. **Quy chuẩn đặt tên**:
   - Tên lớp dùng kiểu PascalCase (UpperCamelCase), ví dụ `StoreSeckillServices`
   - Tên phương thức dùng kiểu camelCase, ví dụ `getSeckillList`
   - Tên biến dùng kiểu camelCase, ví dụ `seckillProduct`
   - Tên hằng số viết hoa toàn bộ, phân tách bằng dấu gạch dưới, ví dụ `COUPON_TYPE_FULL`

2. **Cấu trúc code**:
   - Tuân thủ nghiêm ngặt kiến trúc phân tầng, cấm gọi vượt tầng
   - Mỗi phương thức chỉ đảm nhận một trách nhiệm, số dòng code không vượt quá 50 dòng
   - Thêm chú thích hợp lý, mô tả chức năng của phương thức, ý nghĩa tham số và giá trị trả về

3. **Thao tác cơ sở dữ liệu**:
   - Mọi thao tác cơ sở dữ liệu phải được thực hiện thông qua tầng Dao
   - Dùng query builder do ORM cung cấp, tránh viết SQL trực tiếp
   - Các thao tác trên dữ liệu quan trọng như tiền, tồn kho, v.v. đều bắt buộc phải dùng transaction

### 5.2 Quy trình phát triển
1. **Phân tích yêu cầu**: Xác định rõ yêu cầu chức năng và logic nghiệp vụ của hoạt động marketing
2. **Giai đoạn thiết kế**: Thiết kế cấu trúc dữ liệu, API và quy trình nghiệp vụ
3. **Triển khai code**: Viết code theo kiến trúc phân tầng
4. **Kiểm thử đơn vị**: Viết unit test, đảm bảo chức năng hoạt động chính xác
5. **Kiểm thử tích hợp**: Thực hiện kiểm thử tích hợp mô-đun, kiểm tra tương tác giữa hoạt động với các mô-đun như sản phẩm, đơn hàng, v.v.
6. **Kiểm thử hiệu năng**: Kiểm thử hiệu năng cho các tình huống tải đồng thời cao, ví dụ hoạt động flash sale
7. **Rà soát code**: Gửi code để rà soát (code review), đảm bảo chất lượng code
8. **Triển khai vận hành**: Triển khai lên môi trường production

## 6. Các vấn đề thường gặp và giải pháp

### 6.1 Xử lý tải đồng thời cao
**Vấn đề**: Các hoạt động như flash sale, mua chung có thể gặp các vấn đề như bán vượt tồn kho (overselling), dữ liệu không nhất quán, v.v. trong tình huống tải đồng thời cao
**Cách khắc phục**:
1. Dùng transaction cơ sở dữ liệu và cơ chế khóa lạc quan (optimistic lock)
2. Triển khai cơ chế tạm trừ và hoàn trả tồn kho
3. Dùng các công nghệ bộ nhớ đệm như Redis để nâng cao hiệu quả thao tác tồn kho
4. Áp dụng khóa phân tán để đảm bảo tính nhất quán dữ liệu
5. Triển khai cắt đỉnh lưu lượng (peak shaving), ví dụ dùng các kỹ thuật như hàng đợi, giới hạn lưu lượng (rate limiting)

### 6.2 Quản lý thời gian hoạt động
**Vấn đề**: Kiểm soát chính xác thời gian bắt đầu và kết thúc hoạt động
**Cách khắc phục**:
1. Dùng tác vụ hẹn giờ (timer) để định kỳ kiểm tra trạng thái hoạt động
2. Khi thực hiện thao tác quan trọng (như người dùng tham gia hoạt động), xác thực lại thời gian hoạt động
3. Dùng tính năng thời gian hết hạn của Redis để tự động cập nhật trạng thái hoạt động

### 6.3 Thống kê dữ liệu hoạt động
**Vấn đề**: Thống kê theo thời gian thực các dữ liệu như số người tham gia hoạt động, doanh số, v.v.
**Cách khắc phục**:
1. Dùng Redis để lưu đệm dữ liệu thời gian thực, định kỳ đồng bộ vào cơ sở dữ liệu
2. Áp dụng cách xử lý bất đồng bộ để tránh ảnh hưởng đến luồng nghiệp vụ chính
3. Triển khai chức năng báo cáo dữ liệu, hỗ trợ truy vấn đa chiều

### 6.4 Xử lý xung đột hoạt động
**Vấn đề**: Xung đột khi cùng một sản phẩm tham gia nhiều hoạt động
**Cách khắc phục**:
1. Thiết kế cơ chế độ ưu tiên hoạt động, xác định rõ thứ tự thực hiện các hoạt động
2. Khi người dùng tham gia hoạt động, xác thực xem sản phẩm có thể đồng thời tham gia nhiều hoạt động hay không
3. Cung cấp chức năng phát hiện xung đột hoạt động, cảnh báo xung đột khi tạo hoạt động

## 7. Mở rộng và tùy biến

### 7.1 Mở rộng chức năng
Hệ thống được thiết kế có khả năng mở rộng tốt, có thể mở rộng chức năng theo các cách sau:

1. **Thêm loại hoạt động mới**:
   - Tạo model hoạt động mới trong thư mục `model/activity/`
   - Tạo DAO tương ứng trong thư mục `dao/activity/`
   - Tạo Services tương ứng trong thư mục `services/activity/`
   - Tạo controller tương ứng trong thư mục `controller/`
   - Thêm route mới vào tệp route

2. **Mở rộng chức năng hoạt động hiện có**:
   - Thêm trường mới vào model hiện có
   - Thêm phương thức mới vào Services hiện có
   - Thêm API mới vào controller hiện có

### 7.2 Mở rộng bằng sự kiện
Hệ thống kích hoạt sự kiện tại các điểm nghiệp vụ quan trọng, có thể mở rộng chức năng bằng cách lắng nghe sự kiện:

1. **Sự kiện bắt đầu hoạt động**: ActivityStartEvent
2. **Sự kiện kết thúc hoạt động**: ActivityEndEvent
3. **Sự kiện người dùng tham gia hoạt động**: UserJoinActivityEvent
4. **Sự kiện tạo đơn hàng hoạt động**: ActivityOrderCreateEvent
5. **Sự kiện thanh toán đơn hàng hoạt động**: ActivityOrderPayEvent

### 7.3 Mở rộng bằng hook
Hệ thống cung cấp nhiều điểm hook, có thể mở rộng chức năng thông qua hàm hook:

1. **Hook trước khi tạo hoạt động**: activity_create_before
2. **Hook sau khi tạo hoạt động**: activity_create_after
3. **Hook trước khi sửa hoạt động**: activity_edit_before
4. **Hook sau khi sửa hoạt động**: activity_edit_after
5. **Hook trước khi xóa hoạt động**: activity_delete_before
6. **Hook sau khi xóa hoạt động**: activity_delete_after
7. **Hook trước khi người dùng tham gia hoạt động**: user_join_activity_before
8. **Hook sau khi người dùng tham gia hoạt động**: user_join_activity_after

## 8. Lịch sử cập nhật tài liệu

| Phiên bản | Ngày cập nhật | Nội dung cập nhật | Người cập nhật |
|------|----------|----------|--------|
| 1.0 | 2026-01-19 | Phiên bản đầu tiên, bao gồm các hoạt động marketing cốt lõi như flash sale, săn giảm giá, mua chung, phiếu giảm giá | AI Assistant |

## 9. Phụ lục

### 9.1 Sơ đồ quan hệ các lớp cốt lõi

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   ActivityModel │     │   ActivityDao   │     │ ActivityServices│
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                         ┌─────────────────┐
                         │ ActivityController│
                         └─────────────────┘
                                 │
                         ┌─────────────────┐
                         │     Routes      │
                         └─────────────────┘
```

### 9.2 Sơ đồ quy trình hoạt động flash sale

```
Quy trình chương trình flash sale:
1. Người dùng vào trang chương trình flash sale
2. Hệ thống kiểm tra chương trình đã bắt đầu chưa
3. Người dùng nhấn nút flash sale
4. Hệ thống kiểm tra tồn kho sản phẩm
5. Hệ thống tạm trừ tồn kho sản phẩm
6. Người dùng tạo đơn hàng
7. Người dùng thanh toán đơn hàng
8. Hệ thống xác nhận đơn hàng, trừ tồn kho
9. Chương trình kết thúc, hoàn lại tồn kho của các đơn hàng chưa thanh toán
```

### 9.3 Sơ đồ quy trình hoạt động mua chung

```
Quy trình chương trình mua chung:
1. Người dùng vào trang chương trình mua chung
2. Chọn sản phẩm và quy cách mua chung
3. Chọn mở nhóm hoặc tham gia nhóm
   a. Mở nhóm: Tạo bản ghi mua chung mới
   b. Tham gia nhóm: Gia nhập nhóm mua chung đã có
4. Hệ thống tạm trừ tồn kho sản phẩm
5. Người dùng tạo đơn hàng
6. Người dùng thanh toán đơn hàng
7. Hệ thống kiểm tra đã đủ số người để thành nhóm chưa
   a. Đạt: Mua chung thành công
   b. Chưa đạt: Chờ người dùng khác tham gia nhóm
8. Hết thời gian mua chung
   a. Thành nhóm: Đơn hàng có hiệu lực
   b. Không thành nhóm: Hoàn tiền cho người dùng, hoàn lại tồn kho
```

### 9.4 Sơ đồ quy trình hoạt động săn giảm giá

```
Quy trình chương trình săn giảm giá:
1. Người dùng vào trang chương trình săn giảm giá
2. Chọn sản phẩm, nhấn bắt đầu săn giảm giá
3. Hệ thống tạo bản ghi săn giảm giá
4. Người dùng mời bạn bè giúp săn giảm giá
5. Bạn bè nhấn giúp sức, hệ thống giảm ngẫu nhiên một số tiền nhất định
6. Người dùng đạt mức giá thấp nhất hoặc hết thời gian săn giảm giá
7. Người dùng mua sản phẩm với giá đã săn được
8. Hệ thống tạo đơn hàng, trừ tồn kho
```

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
