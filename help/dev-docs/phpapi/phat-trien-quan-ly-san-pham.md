# Tài liệu phát triển quản lý sản phẩm

## 1. Tổng quan tài liệu

### 1.1 Mục đích tài liệu
Tài liệu này nhằm mô tả chi tiết kiến trúc thiết kế, chức năng cốt lõi, cách triển khai code và quy chuẩn phát triển của module quản lý sản phẩm trong hệ thống CRMEB, cung cấp tài liệu tham khảo phát triển đầy đủ cho lập trình viên hệ thống.

### 1.2 Phạm vi áp dụng
Tài liệu này dành cho lập trình viên, nhân viên bảo trì và nhà phát triển thứ cấp của hệ thống CRMEB, dùng để hướng dẫn công việc phát triển, mở rộng và bảo trì module quản lý sản phẩm.

### 1.3 Định nghĩa thuật ngữ
| Thuật ngữ | Giải thích |
|------|------|
| SPU | Đơn vị sản phẩm tiêu chuẩn hóa, ví dụ "iPhone 13" |
| SKU | Đơn vị lưu kho, ví dụ "iPhone 13 128G màu trắng" |
| Thuộc tính sản phẩm | Đặc điểm của sản phẩm, như màu sắc, kích thước, v.v. |
| Quy cách sản phẩm | Giá trị cụ thể của thuộc tính sản phẩm, như màu đỏ, XL, v.v. |
| Danh mục sản phẩm | Cấu trúc phân loại dạng cây dùng để tổ chức sản phẩm |
| Nhãn sản phẩm | Từ khóa dùng để đánh dấu đặc điểm của sản phẩm |

## 2. Các mô-đun chức năng cốt lõi

### 2.1 Quản lý sản phẩm cơ bản
- **Danh sách sản phẩm**: Hiển thị thông tin cơ bản của sản phẩm, hỗ trợ tìm kiếm đa điều kiện, phân trang
- **Chi tiết sản phẩm**: Xem đầy đủ thông tin sản phẩm, bao gồm thông tin cơ bản, thông tin quy cách, thông tin chương trình khuyến mãi, v.v.
- **Thêm sản phẩm**: Thêm sản phẩm mới, hỗ trợ tạo SPU, chọn mẫu quy cách
- **Sửa sản phẩm**: Chỉnh sửa thông tin sản phẩm, hỗ trợ điều chỉnh quy cách, liên kết chương trình khuyến mãi, v.v.
- **Xóa sản phẩm**: Xóa sản phẩm, hỗ trợ xóa vật lý và xóa logic
- **Đăng bán/ngừng bán sản phẩm**: Kiểm soát trạng thái bán của sản phẩm
- **Thao tác hàng loạt với sản phẩm**: Hỗ trợ đăng bán/ngừng bán hàng loạt, xóa hàng loạt, sửa hàng loạt, v.v.

### 2.2 Quản lý quy cách sản phẩm
- **Quản lý thuộc tính quy cách**: Định nghĩa thuộc tính của sản phẩm, như màu sắc, kích thước, v.v.
- **Quản lý giá trị quy cách**: Định nghĩa giá trị cụ thể cho từng thuộc tính
- **Tạo SKU**: Tự động tạo SKU dựa trên tổ hợp thuộc tính
- **Mẫu quy cách**: Hỗ trợ lưu và sử dụng mẫu quy cách, nâng cao hiệu quả thêm sản phẩm

### 2.3 Quản lý danh mục sản phẩm
- **Danh sách danh mục**: Hiển thị danh mục sản phẩm, hỗ trợ cấu trúc dạng cây
- **Thêm danh mục**: Thêm danh mục mới, hỗ trợ danh mục nhiều cấp
- **Sửa danh mục**: Chỉnh sửa thông tin danh mục, bao gồm tên, biểu tượng, thứ tự sắp xếp, v.v.
- **Xóa danh mục**: Xóa danh mục, hỗ trợ xóa hàng loạt
- **Sắp xếp danh mục**: Điều chỉnh thứ tự hiển thị của danh mục

### 2.4 Quản lý đánh giá sản phẩm
- **Danh sách đánh giá**: Hiển thị đánh giá sản phẩm, hỗ trợ lọc theo nhiều điều kiện
- **Duyệt đánh giá**: Duyệt các đánh giá do người dùng gửi
- **Trả lời đánh giá**: Trả lời đánh giá của người dùng
- **Đánh giá ảo**: Hỗ trợ thêm đánh giá ảo
- **Xóa đánh giá**: Xóa các đánh giá không phù hợp

### 2.5 Quản lý nhãn sản phẩm
- **Danh sách nhãn**: Hiển thị nhãn sản phẩm
- **Thêm nhãn**: Thêm nhãn mới, hỗ trợ phân loại nhãn
- **Sửa nhãn**: Chỉnh sửa thông tin nhãn
- **Xóa nhãn**: Xóa nhãn
- **Liên kết nhãn**: Liên kết nhãn với sản phẩm

### 2.6 Quản lý tham số sản phẩm
- **Danh sách tham số**: Hiển thị các tham số sản phẩm
- **Thêm tham số**: Thêm tham số tùy chỉnh
- **Sửa tham số**: Chỉnh sửa thông tin tham số
- **Xóa tham số**: Xóa tham số
- **Quản lý giá trị tham số**: Thiết lập giá trị tham số cụ thể cho sản phẩm

### 2.7 Quản lý bảo đảm sản phẩm
- **Cấu hình dịch vụ bảo đảm**: Cấu hình dịch vụ bảo đảm sản phẩm, như chính sách đổi trả hàng, thời hạn bảo hành, v.v.
- **Liên kết dịch vụ bảo đảm**: Liên kết dịch vụ bảo đảm với sản phẩm

### 2.8 Chức năng thu thập sản phẩm
- **Thu thập sản phẩm Taobao/Tmall**: Hỗ trợ sao chép thông tin sản phẩm từ Taobao/Tmall
- **Nhập hàng loạt**: Hỗ trợ nhập dữ liệu sản phẩm hàng loạt
- **Di chuyển dữ liệu**: Hỗ trợ di chuyển dữ liệu sản phẩm từ hệ thống khác

### 2.9 Liên kết sản phẩm với chương trình khuyến mãi
- **Liên kết chương trình flash sale**: Liên kết sản phẩm với chương trình flash sale
- **Liên kết chương trình săn giảm giá**: Liên kết sản phẩm với chương trình săn giảm giá
- **Liên kết chương trình mua chung**: Liên kết sản phẩm với chương trình mua chung
- **Liên kết phiếu giảm giá**: Cấu hình phiếu giảm giá cho sản phẩm

## 3. Cấu trúc và thiết kế code

### 3.1 Thiết kế kiến trúc
Hệ thống sử dụng thiết kế kiến trúc phân tầng, tuân thủ nghiêm ngặt mẫu thiết kế MVC:
```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   ProductModel  │     │    ProductDao   │     │ ProductServices │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                         ┌─────────────────┐
                         │ ProductController│
                         └─────────────────┘
                                 │
                         ┌─────────────────┐
                         │     Routes      │
                         └─────────────────┘
```

### 3.2 Cấu trúc tệp cốt lõi

#### 3.2.1 Tầng mô hình (Model)
**Model chính của sản phẩm**: `crmeb/app/model/product/product/StoreProduct.php`
- Kế thừa từ BaseModel, sử dụng ModelTrait
- Chứa các trường thông tin cơ bản của sản phẩm
- Định nghĩa quan hệ liên kết với các model khác:
  - Danh mục sản phẩm (storeCategory)
  - Quy cách sản phẩm (attr, attrValue, attrResult)
  - Mô tả sản phẩm (storeDescription)
  - Đánh giá sản phẩm (reply)
  - Nhãn sản phẩm (label)
  - Tham số sản phẩm (param)
  - Bảo đảm sản phẩm (protection)
  - Phiếu giảm giá của sản phẩm (coupon)
  - Chương trình khuyến mãi của sản phẩm (activity)
- Cung cấp nhiều phương thức searcher (bộ lọc tìm kiếm) phong phú

**Các model liên quan đến quy cách sản phẩm**:
- `StoreProductAttr.php` - Model thuộc tính sản phẩm
- `StoreProductAttrValue.php` - Model giá trị thuộc tính sản phẩm
- `StoreProductAttrResult.php` - Model kết quả thuộc tính sản phẩm

**Model danh mục sản phẩm**:
- `StoreCategory.php` - Model danh mục sản phẩm

#### 3.2.2 Tầng truy cập dữ liệu (DAO)
**DAO sản phẩm**: `crmeb/app/dao/product/product/StoreProductDao.php`
- Kế thừa từ BaseDao
- Cung cấp các phương thức truy vấn danh sách sản phẩm, đếm thống kê, cập nhật trường, v.v.
- Hỗ trợ phân trang, tìm kiếm theo điều kiện
- Bao gồm quản lý các dữ liệu cốt lõi như tồn kho, lượt bán của sản phẩm

#### 3.2.3 Tầng dịch vụ nghiệp vụ (Services)
**Services sản phẩm**: `crmeb/app/services/product/product/StoreProductServices.php`
- Triển khai logic nghiệp vụ cốt lõi của quản lý sản phẩm
- Các chức năng cốt lõi như danh sách sản phẩm, chi tiết, lưu, xóa, quản lý quy cách
- Xử lý quan hệ liên kết giữa sản phẩm với danh mục, quy cách, phiếu giảm giá, chương trình khuyến mãi, v.v.
- Hỗ trợ tạo SPU sản phẩm, quản lý mẫu quy cách
- Triển khai xác thực thuộc tính sản phẩm và tạo SKU
- Xử lý quản lý các dữ liệu cốt lõi như tồn kho, lượt bán của sản phẩm

#### 3.2.4 Tầng điều khiển (Controller)
**Controller sản phẩm**: `crmeb/app/adminapi/controller/v1/product/StoreProduct.php`
- Xử lý các request HTTP của quản lý sản phẩm
- Hỗ trợ thêm, xóa, sửa, tra cứu sản phẩm, lên/xuống kệ, thao tác hàng loạt
- Cung cấp chức năng tạo quy cách sản phẩm, kiểm tra chương trình khuyến mãi, nhập mã thẻ
- Hỗ trợ bộ nhớ đệm (cache) dữ liệu sản phẩm
- Triển khai chức năng thiết lập hàng loạt, di chuyển, nhập/xuất sản phẩm

#### 3.2.5 Cấu hình route
**Route sản phẩm**: `crmeb/app/adminapi/route/product.php`
- Chứa tất cả route API liên quan đến quản lý sản phẩm
- Thiết kế module hóa: danh mục sản phẩm, quản lý sản phẩm, đánh giá sản phẩm, thu thập sản phẩm, nhãn sản phẩm, tham số sản phẩm, bảo đảm sản phẩm
- Mỗi module có đầy đủ route cho các thao tác CRUD
- Hỗ trợ kiểm soát quyền và ghi log

## 4. Thiết kế cơ sở dữ liệu

### 4.1 Bảng chính sản phẩm (store_product)

| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID sản phẩm (khóa chính) |
| cid | int(10) | ID danh mục |
| merchant_id | int(10) | ID người bán |
| store_name | varchar(100) | Tên sản phẩm |
| store_info | text | Mô tả ngắn sản phẩm |
| keywords | varchar(255) | Từ khóa |
| image | varchar(255) | Ảnh chính sản phẩm |
| slider_image | text | Ảnh trình chiếu |
| video_link | varchar(255) | Liên kết video |
| price | decimal(10,2) | Giá sản phẩm |
| ot_price | decimal(10,2) | Giá gốc |
| unit_name | varchar(10) | Tên đơn vị |
| stock | int(10) | Tồn kho |
| sales | int(10) | Lượt bán |
| sales_actual | int(10) | Lượt bán thực tế |
| give_integral | int(10) | Tặng điểm thưởng |
| cost | decimal(10,2) | Giá vốn |
| is_show | tinyint(1) | Hiển thị |
| is_hot | tinyint(1) | Có nổi bật không |
| is_new | tinyint(1) | Là sản phẩm mới |
| is_recommend | tinyint(1) | Có đề xuất không |
| is_postage | tinyint(1) | Có miễn phí vận chuyển |
| postage | decimal(10,2) | Phí vận chuyển |
| unit_id | int(10) | ID đơn vị tính |
| sort | int(10) | Thứ tự sắp xếp |
| add_time | int(10) | Thời gian thêm |
| is_deleted | tinyint(1) | Đã xóa |
| store_content | longtext | Chi tiết sản phẩm |
| virtual_sales | int(10) | Lượt bán ảo |
| browse | int(10) | Lượt xem |
| code_path | varchar(255) | Đường dẫn mã sản phẩm |
| temp_id | int(10) | ID mẫu sản phẩm |
| goods_id | int(10) | ID SPU sản phẩm |
| specs_type | tinyint(1) | Loại quy cách |

### 4.2 Các bảng liên quan đến quy cách sản phẩm

#### Bảng thuộc tính sản phẩm (store_product_attr)
| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID thuộc tính |
| product_id | int(10) | ID sản phẩm |
| attr_name | varchar(50) | Tên thuộc tính |
| attr_values | text | Giá trị thuộc tính |
| sort | int(10) | Thứ tự sắp xếp |
| add_time | int(10) | Thời gian thêm |

#### Bảng giá trị thuộc tính sản phẩm (store_product_attr_value)
| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID giá trị thuộc tính |
| product_id | int(10) | ID sản phẩm |
| attr_id | int(10) | ID thuộc tính |
| attr_value | varchar(50) | Giá trị thuộc tính |
| image | varchar(255) | Hình ảnh thuộc tính |
| sort | int(10) | Thứ tự sắp xếp |
| add_time | int(10) | Thời gian thêm |

#### Bảng kết quả thuộc tính sản phẩm (store_product_attr_result)
| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID kết quả |
| product_id | int(10) | ID sản phẩm |
| attr_result | varchar(255) | Kết quả thuộc tính (định dạng JSON) |
| stock | int(10) | Tồn kho |
| price | decimal(10,2) | Giá |
| cost | decimal(10,2) | Giá vốn |
| code | varchar(30) | Mã sản phẩm |
| bar_code | varchar(30) | Mã vạch |
| weight | decimal(10,2) | Trọng lượng |
| volume | decimal(10,2) | Thể tích |
| sales | int(10) | Lượt bán |
| sales_actual | int(10) | Lượt bán thực tế |
| image | varchar(255) | Hình ảnh sản phẩm |
| is_show | tinyint(1) | Hiển thị |
| sort | int(10) | Thứ tự sắp xếp |
| unique | varchar(255) | Giá trị duy nhất |

### 4.3 Bảng danh mục sản phẩm (store_category)
| Tên trường | Loại dữ liệu | Mô tả |
|--------|----------|------|
| id | int(10) unsigned | ID danh mục |
| pid | int(10) | ID danh mục cha |
| cate_name | varchar(50) | Tên danh mục |
| icon | varchar(255) | Biểu tượng danh mục |
| sort | int(10) | Thứ tự sắp xếp |
| is_show | tinyint(1) | Hiển thị |
| add_time | int(10) | Thời gian thêm |

## 5. Mô tả API

### 5.1 API quản lý cơ bản sản phẩm

#### 5.1.1 Danh sách sản phẩm
- **URL yêu cầu**: `/adminapi/v1/product/list`
- **Phương thức yêu cầu**: GET
- **Yêu cầu quyền**: product/product/list
- **Tham số yêu cầu**:
  - page: số trang
  - limit: số lượng mỗi trang
  - keyword: từ khóa tìm kiếm
  - cid: ID danh mục
  - price_min: giá thấp nhất
  - price_max: giá cao nhất
  - is_show: có hiển thị hay không
  - is_hot: có phải sản phẩm hot không
  - is_new: có phải sản phẩm mới không
  - is_recommend: có đề xuất hay không
- **Kết quả trả về**: Dữ liệu danh sách sản phẩm

#### 5.1.2 Chi tiết sản phẩm
- **URL yêu cầu**: `/adminapi/v1/product/detail/{id}`
- **Phương thức yêu cầu**: GET
- **Yêu cầu quyền**: product/product/detail
- **Kết quả trả về**: Thông tin chi tiết sản phẩm

#### 5.1.3 Thêm sản phẩm
- **URL yêu cầu**: `/adminapi/v1/product/create`
- **Phương thức yêu cầu**: POST
- **Yêu cầu quyền**: product/product/create
- **Tham số request**: Thông tin cơ bản, thông tin quy cách của sản phẩm, v.v.
- **Kết quả trả về**: Kết quả thao tác

#### 5.1.4 Sửa sản phẩm
- **URL yêu cầu**: `/adminapi/v1/product/update/{id}`
- **Phương thức yêu cầu**: PUT
- **Yêu cầu quyền**: product/product/update
- **Tham số request**: Thông tin cơ bản, thông tin quy cách của sản phẩm, v.v.
- **Kết quả trả về**: Kết quả thao tác

#### 5.1.5 Xóa sản phẩm
- **URL yêu cầu**: `/adminapi/v1/product/delete/{id}`
- **Phương thức yêu cầu**: DELETE
- **Yêu cầu quyền**: product/product/delete
- **Kết quả trả về**: Kết quả thao tác

#### 5.1.6 Lên/xuống kệ sản phẩm
- **URL yêu cầu**: `/adminapi/v1/product/status/{id}`
- **Phương thức yêu cầu**: PUT
- **Yêu cầu quyền**: product/product/status
- **Tham số yêu cầu**:
  - is_show: có hiển thị hay không (1-hiện, 0-ẩn)
- **Kết quả trả về**: Kết quả thao tác

### 5.2 API quản lý danh mục sản phẩm

#### 5.2.1 Danh sách danh mục
- **URL yêu cầu**: `/adminapi/v1/product/category/list`
- **Phương thức yêu cầu**: GET
- **Yêu cầu quyền**: product/category/list
- **Kết quả trả về**: Dữ liệu danh sách danh mục

#### 5.2.2 Thêm danh mục
- **URL yêu cầu**: `/adminapi/v1/product/category/create`
- **Phương thức yêu cầu**: POST
- **Yêu cầu quyền**: product/category/create
- **Tham số request**: Thông tin danh mục
- **Kết quả trả về**: Kết quả thao tác

#### 5.2.3 Sửa danh mục
- **URL yêu cầu**: `/adminapi/v1/product/category/update/{id}`
- **Phương thức yêu cầu**: PUT
- **Yêu cầu quyền**: product/category/update
- **Tham số request**: Thông tin danh mục
- **Kết quả trả về**: Kết quả thao tác

#### 5.2.4 Xóa danh mục
- **URL yêu cầu**: `/adminapi/v1/product/category/delete/{id}`
- **Phương thức yêu cầu**: DELETE
- **Yêu cầu quyền**: product/category/delete
- **Kết quả trả về**: Kết quả thao tác

## 6. Quy chuẩn và quy trình phát triển

### 6.1 Quy chuẩn code
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

### 6.2 Quy trình phát triển
1. **Phân tích yêu cầu**: Làm rõ yêu cầu chức năng và logic nghiệp vụ
2. **Giai đoạn thiết kế**: Thiết kế cấu trúc dữ liệu, API và quy trình nghiệp vụ
3. **Triển khai code**: Viết code theo kiến trúc phân tầng
4. **Kiểm thử đơn vị**: Viết unit test, đảm bảo chức năng hoạt động chính xác
5. **Kiểm thử tích hợp**: Tiến hành kiểm thử tích hợp module
6. **Rà soát code**: Gửi code để rà soát (code review), đảm bảo chất lượng code
7. **Triển khai vận hành**: Triển khai lên môi trường production

## 7. Các vấn đề thường gặp và giải pháp

### 7.1 Vấn đề tạo SKU
**Vấn đề**: Khi sản phẩm có quá nhiều thuộc tính quy cách, số lượng SKU được tạo ra quá lớn, khiến trang bị giật lag
**Cách khắc phục**:
1. Giới hạn số lượng thuộc tính của mỗi sản phẩm và số lượng giá trị của mỗi thuộc tính
2. Tạo SKU theo phương thức tải bất đồng bộ
3. Tối ưu thuật toán tạo SKU, nâng cao hiệu suất tạo

### 7.2 Quản lý tồn kho sản phẩm
**Vấn đề**: Trong tình huống truy cập đồng thời cao, tồn kho sản phẩm xảy ra tình trạng bán vượt (overselling)
**Cách khắc phục**:
1. Dùng transaction cơ sở dữ liệu và cơ chế khóa lạc quan (optimistic lock)
2. Triển khai cơ chế tạm trừ và hoàn trả tồn kho
3. Cân nhắc dùng công nghệ bộ nhớ đệm như Redis để nâng cao hiệu suất thao tác tồn kho

### 7.3 Đồng bộ dữ liệu sản phẩm
**Vấn đề**: Dữ liệu sản phẩm đồng bộ không kịp thời giữa nhiều thiết bị đầu cuối
**Cách khắc phục**:
1. Dùng hàng đợi tin nhắn (message queue) để đồng bộ dữ liệu
2. Triển khai cơ chế bộ nhớ đệm dữ liệu, giảm tải truy vấn cơ sở dữ liệu
3. Áp dụng kiến trúc hướng sự kiện (event-driven), đảm bảo thay đổi dữ liệu được thông báo kịp thời

### 7.4 Hiệu năng tìm kiếm sản phẩm
**Vấn đề**: Khi số lượng sản phẩm quá nhiều, tốc độ tìm kiếm bị chậm
**Cách khắc phục**:
1. Tối ưu thiết kế chỉ mục (index) cơ sở dữ liệu
2. Cân nhắc dùng công cụ tìm kiếm (search engine), như Elasticsearch
3. Triển khai bộ nhớ đệm cho kết quả tìm kiếm

## 8. Mở rộng và tùy biến

### 8.1 Mở rộng chức năng
Hệ thống được thiết kế có khả năng mở rộng tốt, có thể mở rộng chức năng theo các cách sau:

1. **Thêm model mới**: Kế thừa BaseModel, định nghĩa cấu trúc dữ liệu mới
2. **Thêm service mới**: Kế thừa BaseServices, triển khai logic nghiệp vụ mới
3. **Thêm controller mới**: Kế thừa BaseController, xử lý các yêu cầu API mới
4. **Thêm route mới**: Định nghĩa quy tắc route mới trong tệp route

### 8.2 Mở rộng bằng sự kiện
Hệ thống kích hoạt sự kiện tại các điểm nghiệp vụ quan trọng, có thể mở rộng chức năng bằng cách lắng nghe sự kiện:

1. **Sự kiện thêm sản phẩm**: ProductAddEvent
2. **Sự kiện sửa sản phẩm**: ProductUpdateEvent
3. **Sự kiện xóa sản phẩm**: ProductDeleteEvent
4. **Sự kiện lên/xuống kệ sản phẩm**: ProductStatusEvent
5. **Sự kiện thay đổi tồn kho sản phẩm**: ProductStockChangeEvent

### 8.3 Mở rộng bằng hook
Hệ thống cung cấp nhiều điểm hook, có thể mở rộng chức năng thông qua hàm hook:

1. **Hook trước khi thêm sản phẩm**: product_add_before
2. **Hook sau khi thêm sản phẩm**: product_add_after
3. **Hook trước khi sửa sản phẩm**: product_edit_before
4. **Hook sau khi sửa sản phẩm**: product_edit_after
5. **Hook trước khi xóa sản phẩm**: product_delete_before
6. **Hook sau khi xóa sản phẩm**: product_delete_after

## 9. Lịch sử cập nhật tài liệu

| Phiên bản | Ngày cập nhật | Nội dung cập nhật | Người cập nhật |
|------|----------|----------|--------|
| 1.0 | 2026-01-19 | Phiên bản đầu tiên, bao gồm các chức năng cốt lõi của quản lý sản phẩm | AI Assistant |

## 10. Phụ lục

### 10.1 Sơ đồ quan hệ các lớp cốt lõi

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   ProductModel  │     │    ProductDao   │     │ ProductServices │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                         ┌─────────────────┐
                         │ ProductController│
                         └─────────────────┘
                                 │
                         ┌─────────────────┐
                         │     Routes      │
                         └─────────────────┘
```

### 10.2 Sơ đồ quy trình thêm sản phẩm

```
Quy trình thêm sản phẩm:
1. Nhận thông tin cơ bản của sản phẩm
2. Xác thực tính hợp lệ của thông tin sản phẩm
3. Xử lý liên kết danh mục sản phẩm
4. Tạo SPU sản phẩm (nếu cần)
5. Xử lý quy cách và thuộc tính sản phẩm
6. Tạo danh sách SKU
7. Lưu thông tin chính của sản phẩm
8. Lưu thông tin quy cách sản phẩm
9. Lưu mô tả sản phẩm
10. Liên kết nhãn, thông số, dịch vụ bảo đảm của sản phẩm, v.v.
11. Trả về kết quả thao tác
```

### 10.3 Sơ đồ quy trình lên/xuống kệ sản phẩm

```
Quy trình lên/xuống kệ sản phẩm:
1. Nhận ID và trạng thái sản phẩm
2. Kiểm tra sản phẩm có tồn tại không
3. Cập nhật trạng thái sản phẩm
4. Xóa bộ nhớ đệm sản phẩm (nếu cần)
5. Kích hoạt sự kiện thay đổi trạng thái sản phẩm
6. Trả về kết quả thao tác
```

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
