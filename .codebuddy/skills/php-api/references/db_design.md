# Tài liệu thiết kế cơ sở dữ liệu

## 1. Tổng quan

Tài liệu này mô tả thiết kế cơ sở dữ liệu của dự án CRMEB, bao gồm kiến trúc cơ sở dữ liệu, cấu trúc bảng, thiết kế chỉ mục, thiết kế quan hệ, v.v., nhằm chuẩn hóa việc thiết kế cơ sở dữ liệu, nâng cao hiệu năng và khả năng bảo trì của cơ sở dữ liệu.

## 2. Kiến trúc cơ sở dữ liệu

### 2.1 Kiến trúc tổng thể

- **Hệ quản trị cơ sở dữ liệu**: MySQL 5.7~8.0
- **Engine lưu trữ**: InnoDB (mặc định)
- **Bộ ký tự**: utf8mb4
- **Quy tắc đối chiếu (collation)**: utf8mb4_general_ci
- **Connection pool**: khuyến nghị sử dụng
- **Vị trí tệp SQL**: `public/install/crmeb.sql`

### 2.2 Bộ công nghệ

- **MySQL**: 5.7+
- **Redis**: dùng làm bộ nhớ đệm (cache)
- **ThinkPHP ORM**: dùng cho các thao tác với model
- **Migration cơ sở dữ liệu**: dùng để quản lý phiên bản
- **Sao lưu cơ sở dữ liệu**: dùng để đảm bảo an toàn dữ liệu

### 2.3 Mô tả cấu hình

#### 2.3.1 Cấu hình cơ sở dữ liệu

```php
// config/database.php
return [
    'default' => env('database.driver', 'mysql'),
    'connections' => [
        'mysql' => [
            'type' => 'mysql',
            'hostname' => env('database.hostname', '127.0.0.1'),
            'database' => env('database.database', ''),
            'username' => env('database.username', ''),
            'password' => env('database.password', ''),
            'hostport' => env('database.hostport', '3306'),
            'charset' => 'utf8mb4',
            'prefix' => env('database.prefix', ''),
            'debug' => env('app_debug', true),
        ],
    ],
];
```

## 3. Quy chuẩn thiết kế cơ sở dữ liệu

### 3.1 Quy tắc đặt tên

- **Tên cơ sở dữ liệu**: chữ thường, phân tách bằng dấu gạch dưới
- **Tên bảng**: chữ thường, phân tách bằng dấu gạch dưới, tiền tố thống nhất
- **Tên trường**: chữ thường, phân tách bằng dấu gạch dưới
- **Tên chỉ mục**: chữ thường, phân tách bằng dấu gạch dưới, có tiền tố theo loại chỉ mục
  - Khóa chính: `PRIMARY`
  - Chỉ mục duy nhất: `uk_{tên_trường}`
  - Chỉ mục thường: `idx_{tên_trường}`

### 3.2 Quy chuẩn cấu trúc bảng

- **Khóa chính**: thống nhất đặt tên là `id`, số nguyên tự tăng
- **Khóa ngoại**: định dạng `{tên_bảng}_id`, ví dụ `user_id`
- **Trường thời gian**: `create_time`/`update_time`
- **Trường trạng thái**: `status`, giá trị mặc định 0
- **Trường xóa mềm**: `delete_time`, giá trị mặc định NULL

### 3.3 Quy chuẩn kiểu trường

- **Kiểu số nguyên**: chọn theo phạm vi giá trị thực tế
  - `TINYINT`: 1 byte, phạm vi -128~127
  - `SMALLINT`: 2 byte, phạm vi -32768~32767
  - `INT`: 4 byte, phạm vi -2147483648~2147483647
  - `BIGINT`: 8 byte, phạm vi lớn hơn

- **Kiểu chuỗi**: 
  - Độ dài cố định: `CHAR`
  - Độ dài thay đổi: `VARCHAR`
  - Văn bản dài: `TEXT`
  - Văn bản lớn: `LONGTEXT`

- **Kiểu ngày giờ**: 
  - Ngày: `DATE`
  - Thời gian: `TIME`
  - Ngày giờ: `DATETIME`
  - Dấu thời gian: `TIMESTAMP`

- **Kiểu số**: 
  - Số thập phân: `DECIMAL`
  - Số thực dấu phẩy động: `FLOAT`, `DOUBLE`

- **Kiểu boolean**: sử dụng `TINYINT(1)`, 0 biểu thị false, 1 biểu thị true

### 3.4 Quy chuẩn chỉ mục

- **Chỉ mục khóa chính**: mỗi bảng bắt buộc phải có khóa chính
- **Chỉ mục duy nhất**: dùng cho các trường định danh duy nhất
- **Chỉ mục thường**: dùng cho các trường thường xuyên được truy vấn
- **Chỉ mục kết hợp**: dùng cho truy vấn trên nhiều trường
- **Chỉ mục khóa ngoại**: dùng cho truy vấn liên kết (JOIN)
- **Số lượng chỉ mục**: mỗi bảng không nên có quá nhiều chỉ mục, thường không quá 5

## 4. Cấu trúc các bảng cốt lõi

### 4.1 Bảng người dùng (`user`)

| Tên trường | Loại dữ liệu | Độ dài | Ràng buộc | Mô tả |
|-------|---------|------|------|------|
| `id` | `INT` | 11 | `PRIMARY KEY AUTO_INCREMENT` | ID người dùng |
| `username` | `VARCHAR` | 50 | `NOT NULL` | Tên người dùng |
| `password` | `VARCHAR` | 255 | `NOT NULL` | Mật khẩu |
| `nickname` | `VARCHAR` | 50 | `NOT NULL` | Biệt danh |
| `avatar` | `VARCHAR` | 255 | | Ảnh đại diện |
| `mobile` | `VARCHAR` | 20 | | Số điện thoại |
| `email` | `VARCHAR` | 100 | | Email |
| `status` | `TINYINT` | 1 | `DEFAULT 1` | Trạng thái |
| `create_time` | `DATETIME` | | `DEFAULT CURRENT_TIMESTAMP` | Thời gian tạo |
| `update_time` | `DATETIME` | | `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` | Thời gian cập nhật |
| `delete_time` | `DATETIME` | | | Thời gian xóa |

### 4.2 Bảng sản phẩm (`product`)

| Tên trường | Loại dữ liệu | Độ dài | Ràng buộc | Mô tả |
|-------|---------|------|------|------|
| `id` | `INT` | 11 | `PRIMARY KEY AUTO_INCREMENT` | ID sản phẩm |
| `name` | `VARCHAR` | 255 | `NOT NULL` | Tên sản phẩm |
| `category_id` | `INT` | 11 | `NOT NULL` | ID danh mục |
| `price` | `DECIMAL` | 10,2 | `NOT NULL` | Giá |
| `stock` | `INT` | 11 | `NOT NULL` | Tồn kho |
| `status` | `TINYINT` | 1 | `DEFAULT 1` | Trạng thái |
| `create_time` | `DATETIME` | | `DEFAULT CURRENT_TIMESTAMP` | Thời gian tạo |
| `update_time` | `DATETIME` | | `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` | Thời gian cập nhật |
| `delete_time` | `DATETIME` | | | Thời gian xóa |

### 4.3 Bảng đơn hàng (`order`)

| Tên trường | Loại dữ liệu | Độ dài | Ràng buộc | Mô tả |
|-------|---------|------|------|------|
| `id` | `INT` | 11 | `PRIMARY KEY AUTO_INCREMENT` | ID đơn hàng |
| `order_sn` | `VARCHAR` | 32 | `NOT NULL UNIQUE` | Mã đơn hàng |
| `user_id` | `INT` | 11 | `NOT NULL` | ID người dùng |
| `total_price` | `DECIMAL` | 10,2 | `NOT NULL` | Tổng giá |
| `status` | `TINYINT` | 1 | `DEFAULT 0` | Trạng thái |
| `create_time` | `DATETIME` | | `DEFAULT CURRENT_TIMESTAMP` | Thời gian tạo |
| `update_time` | `DATETIME` | | `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` | Thời gian cập nhật |
| `delete_time` | `DATETIME` | | | Thời gian xóa |

### 4.4 Bảng danh mục (`category`)

| Tên trường | Loại dữ liệu | Độ dài | Ràng buộc | Mô tả |
|-------|---------|------|------|------|
| `id` | `INT` | 11 | `PRIMARY KEY AUTO_INCREMENT` | ID danh mục |
| `name` | `VARCHAR` | 50 | `NOT NULL` | Tên danh mục |
| `parent_id` | `INT` | 11 | `DEFAULT 0` | ID danh mục cha |
| `sort` | `INT` | 11 | `DEFAULT 0` | Thứ tự sắp xếp |
| `status` | `TINYINT` | 1 | `DEFAULT 1` | Trạng thái |
| `create_time` | `DATETIME` | | `DEFAULT CURRENT_TIMESTAMP` | Thời gian tạo |
| `update_time` | `DATETIME` | | `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` | Thời gian cập nhật |
| `delete_time` | `DATETIME` | | | Thời gian xóa |

### 4.5 Bảng địa chỉ (`address`)

| Tên trường | Loại dữ liệu | Độ dài | Ràng buộc | Mô tả |
|-------|---------|------|------|------|
| `id` | `INT` | 11 | `PRIMARY KEY AUTO_INCREMENT` | ID địa chỉ |
| `user_id` | `INT` | 11 | `NOT NULL` | ID người dùng |
| `name` | `VARCHAR` | 50 | `NOT NULL` | Họ tên người nhận |
| `mobile` | `VARCHAR` | 20 | `NOT NULL` | Số điện thoại |
| `province` | `VARCHAR` | 50 | `NOT NULL` | Tỉnh |
| `city` | `VARCHAR` | 50 | `NOT NULL` | Thành phố |
| `district` | `VARCHAR` | 50 | `NOT NULL` | Quận/huyện |
| `detail` | `VARCHAR` | 255 | `NOT NULL` | Địa chỉ chi tiết |
| `is_default` | `TINYINT` | 1 | `DEFAULT 0` | Đặt làm mặc định |
| `create_time` | `DATETIME` | | `DEFAULT CURRENT_TIMESTAMP` | Thời gian tạo |
| `update_time` | `DATETIME` | | `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` | Thời gian cập nhật |
| `delete_time` | `DATETIME` | | | Thời gian xóa |

## 5. Thiết kế chỉ mục

### 5.1 Chỉ mục bảng người dùng

| Tên chỉ mục | Loại | Trường | Mô tả |
|-------|------|------|------|
| `PRIMARY` | Khóa chính | `id` | Chỉ mục khóa chính |
| `uk_username` | Duy nhất | `username` | Chỉ mục duy nhất cho tên người dùng |
| `idx_mobile` | Thường | `mobile` | Chỉ mục số điện thoại |
| `idx_email` | Thường | `email` | Chỉ mục email |
| `idx_status` | Thường | `status` | Chỉ mục trạng thái |

### 5.2 Chỉ mục bảng sản phẩm

| Tên chỉ mục | Loại | Trường | Mô tả |
|-------|------|------|------|
| `PRIMARY` | Khóa chính | `id` | Chỉ mục khóa chính |
| `idx_category_id` | Thường | `category_id` | Chỉ mục ID danh mục |
| `idx_price` | Thường | `price` | Chỉ mục giá |
| `idx_status` | Thường | `status` | Chỉ mục trạng thái |

### 5.3 Chỉ mục bảng đơn hàng

| Tên chỉ mục | Loại | Trường | Mô tả |
|-------|------|------|------|
| `PRIMARY` | Khóa chính | `id` | Chỉ mục khóa chính |
| `uk_order_sn` | Duy nhất | `order_sn` | Chỉ mục duy nhất cho mã đơn hàng |
| `idx_user_id` | Thường | `user_id` | Chỉ mục ID người dùng |
| `idx_status` | Thường | `status` | Chỉ mục trạng thái |
| `idx_create_time` | Thường | `create_time` | Chỉ mục thời gian tạo |

### 5.4 Chỉ mục bảng danh mục

| Tên chỉ mục | Loại | Trường | Mô tả |
|-------|------|------|------|
| `PRIMARY` | Khóa chính | `id` | Chỉ mục khóa chính |
| `idx_parent_id` | Thường | `parent_id` | Chỉ mục ID danh mục cha |
| `idx_sort` | Thường | `sort` | Chỉ mục thứ tự sắp xếp |
| `idx_status` | Thường | `status` | Chỉ mục trạng thái |

### 5.5 Chỉ mục bảng địa chỉ

| Tên chỉ mục | Loại | Trường | Mô tả |
|-------|------|------|------|
| `PRIMARY` | Khóa chính | `id` | Chỉ mục khóa chính |
| `idx_user_id` | Thường | `user_id` | Chỉ mục ID người dùng |
| `idx_is_default` | Thường | `is_default` | Chỉ mục cờ mặc định |

## 6. Thiết kế quan hệ

### 6.1 Sơ đồ quan hệ giữa các bảng

```
user ----------------- order
  |                      |
  |                      |
  |                      |
address                product
                          |
                          |
                          |
                      category
```

### 6.2 Mô tả quan hệ

- **Người dùng và đơn hàng**: quan hệ một-nhiều, một người dùng có thể có nhiều đơn hàng
- **Người dùng và địa chỉ**: quan hệ một-nhiều, một người dùng có thể có nhiều địa chỉ
- **Sản phẩm và danh mục**: quan hệ nhiều-một, nhiều sản phẩm thuộc về một danh mục
- **Đơn hàng và sản phẩm**: quan hệ nhiều-nhiều, một đơn hàng có thể chứa nhiều sản phẩm, một sản phẩm có thể xuất hiện trong nhiều đơn hàng

### 6.3 Quan hệ khóa ngoại

| Bảng cha | Khóa chính | Bảng con | Khóa ngoại | Mô tả |
|------|------|------|------|------|
| `user` | `id` | `order` | `user_id` | Người dùng sở hữu đơn hàng |
| `user` | `id` | `address` | `user_id` | Người dùng sở hữu địa chỉ |
| `category` | `id` | `product` | `category_id` | Danh mục chứa sản phẩm |

## 7. Tối ưu hiệu năng

### 7.1 Tối ưu chỉ mục

- **Chọn loại chỉ mục phù hợp**: chọn loại chỉ mục phù hợp theo tình huống truy vấn
- **Tránh lạm dụng chỉ mục**: chỉ tạo chỉ mục trên những trường cần thiết
- **Sử dụng chỉ mục kết hợp**: với truy vấn trên nhiều trường, hãy sử dụng chỉ mục kết hợp
- **Bảo trì chỉ mục định kỳ**: định kỳ tạo lại các chỉ mục bị phân mảnh

### 7.2 Tối ưu truy vấn

- **Tránh quét toàn bảng**: sử dụng truy vấn có chỉ mục bao phủ (covering index)
- **Giảm số trường truy vấn**: chỉ truy vấn những trường cần thiết
- **Sử dụng truy vấn JOIN**: sử dụng JOIN hợp lý, tránh truy vấn con (subquery)
- **Giới hạn kết quả truy vấn**: sử dụng LIMIT để giới hạn số lượng kết quả truy vấn

### 7.3 Tối ưu lưu trữ

- **Chọn kiểu trường phù hợp**: chọn kiểu trường phù hợp theo nhu cầu thực tế
- **Sử dụng bảng phân vùng**: với bảng lớn, sử dụng bảng phân vùng để cải thiện hiệu năng truy vấn
- **Dọn dẹp dữ liệu định kỳ**: định kỳ xóa dữ liệu không còn sử dụng để giảm kích thước bảng
- **Sử dụng bộ nhớ đệm**: với dữ liệu được truy vấn thường xuyên, sử dụng Redis để cache

### 7.4 Tối ưu cấu hình

- **Điều chỉnh innodb_buffer_pool_size**: điều chỉnh theo dung lượng bộ nhớ của máy chủ
- **Điều chỉnh max_connections**: điều chỉnh theo lượng truy cập đồng thời
- **Bật bộ nhớ đệm truy vấn**: dành cho tình huống đọc nhiều, ghi ít
- **Tối ưu cấu hình log**: cấu hình hợp lý binary log và log truy vấn chậm

## 8. Thiết kế bảo mật

### 8.1 An toàn dữ liệu

- **Lưu trữ mã hóa**: dữ liệu nhạy cảm (như mật khẩu) phải được mã hóa khi lưu trữ
- **Sao lưu dữ liệu**: sao lưu cơ sở dữ liệu định kỳ
- **Khôi phục dữ liệu**: xây dựng cơ chế khôi phục dữ liệu
- **Kiểm soát truy cập**: kiểm soát chặt chẽ quyền truy cập cơ sở dữ liệu

### 8.2 Chống SQL injection

- **Sử dụng truy vấn tham số hóa**: tránh nối chuỗi SQL trực tiếp
- **Sử dụng ORM**: sử dụng framework ThinkPHP ORM
- **Kiểm tra đầu vào**: kiểm tra tính hợp lệ của dữ liệu người dùng nhập vào
- **Escape ký tự đặc biệt**: thực hiện escape cho các ký tự đặc biệt

### 8.3 Quản lý quyền

- **Nguyên tắc đặc quyền tối thiểu**: chỉ cấp những quyền cần thiết
- **Phân tách vai trò**: mỗi vai trò có quyền khác nhau
- **Rà soát định kỳ**: định kỳ rà soát (audit) log truy cập cơ sở dữ liệu

## 9. Sao lưu và khôi phục

### 9.1 Chiến lược sao lưu

- **Sao lưu toàn bộ**: định kỳ sao lưu toàn bộ
- **Sao lưu gia tăng**: sao lưu gia tăng hằng ngày
- **Sao lưu log**: sao lưu binary log

### 9.2 Chiến lược khôi phục

- **Khôi phục toàn bộ**: khôi phục từ bản sao lưu toàn bộ
- **Khôi phục gia tăng**: khôi phục từ bản sao lưu gia tăng
- **Khôi phục theo thời điểm**: sử dụng binary log để khôi phục về một thời điểm cụ thể

### 9.3 Công cụ sao lưu

- **mysqldump**: công cụ sao lưu có sẵn của MySQL
- **xtrabackup**: công cụ sao lưu do Percona cung cấp
- **Công cụ bên thứ ba**: như Navicat, v.v.

## 10. Quản lý phiên bản

### 10.1 Migration cơ sở dữ liệu

- **Sử dụng công cụ migration**: sử dụng công cụ migration cơ sở dữ liệu của ThinkPHP
- **Quản lý phiên bản**: quản lý phiên bản cho các thay đổi cấu trúc cơ sở dữ liệu
- **Cơ chế rollback**: hỗ trợ rollback cấu trúc cơ sở dữ liệu

### 10.2 Quy tắc đặt tên tệp migration

- **Định dạng**: `YYYYMMDDHHMMSS_mo_ta.php`
- **Ví dụ**: `20230101000000_create_user_table.php`

### 10.3 Cấu trúc tệp migration

```php
<?php
use think\migration\Migrator;
use think\migration\db\Column;

class CreateUserTable extends Migrator
{
    public function change()
    {
        $table = $this->table('user');
        $table->addColumn('username', 'string', ['limit' => 50, 'comment' => 'Tên người dùng'])
              ->addColumn('password', 'string', ['limit' => 255, 'comment' => 'Mật khẩu'])
              ->addColumn('nickname', 'string', ['limit' => 50, 'comment' => 'Biệt danh'])
              ->addColumn('avatar', 'string', ['limit' => 255, 'comment' => 'Ảnh đại diện'])
              ->addColumn('mobile', 'string', ['limit' => 20, 'comment' => 'Số điện thoại'])
              ->addColumn('email', 'string', ['limit' => 100, 'comment' => 'Email'])
              ->addColumn('status', 'tinyint', ['default' => 1, 'comment' => 'Trạng thái'])
              ->addColumn('create_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'comment' => 'Thời gian tạo'])
              ->addColumn('update_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => 'Thời gian cập nhật'])
              ->addColumn('delete_time', 'datetime', ['comment' => 'Thời gian xóa'])
              ->addIndex('username', ['unique' => true])
              ->addIndex('mobile')
              ->addIndex('email')
              ->addIndex('status')
              ->create();
    }
}
```

## 11. Bảo trì và giám sát

### 11.1 Bảo trì thường xuyên

- **Tối ưu bảng định kỳ**: định kỳ chạy lệnh OPTIMIZE TABLE
- **Giám sát kích thước bảng**: theo dõi sự thay đổi kích thước bảng
- **Kiểm tra truy vấn chậm**: định kỳ phân tích log truy vấn chậm
- **Cập nhật thông tin thống kê**: định kỳ cập nhật thông tin thống kê của bảng

### 11.2 Chỉ số giám sát

- **Hiệu năng truy vấn**: giám sát thời gian phản hồi của truy vấn
- **Số kết nối**: giám sát số lượng kết nối cơ sở dữ liệu
- **Tỷ lệ cache hit**: Giám sát tỷ lệ cache hit
- **Tỷ lệ sử dụng ổ đĩa**: Giám sát tình trạng sử dụng dung lượng ổ đĩa
- **Tỷ lệ sử dụng CPU**: Giám sát tỷ lệ sử dụng CPU của máy chủ cơ sở dữ liệu

### 11.3 Công cụ giám sát

- **MySQL Enterprise Monitor**: Công cụ giám sát của MySQL bản Enterprise
- **Percona Monitoring and Management**: Công cụ giám sát mã nguồn mở
- **Zabbix**: Công cụ giám sát đa năng
- **Prometheus + Grafana**: Giải pháp giám sát hiện đại

## 12. Tổng kết

Tài liệu này mô tả quy chuẩn thiết kế cơ sở dữ liệu và các thực tiễn tốt nhất (best practice) của dự án CRMEB, bao gồm các khía cạnh như kiến trúc cơ sở dữ liệu, cấu trúc bảng, thiết kế chỉ mục, thiết kế quan hệ, tối ưu hiệu năng, thiết kế bảo mật, sao lưu và khôi phục, quản lý phiên bản, bảo trì và giám sát.

Tuân thủ quy chuẩn thiết kế trong tài liệu này giúp nâng cao hiệu năng và khả năng bảo trì của cơ sở dữ liệu, đảm bảo hệ thống vận hành ổn định. Đồng thời, việc bảo trì và giám sát cơ sở dữ liệu định kỳ giúp kịp thời phát hiện và xử lý các vấn đề tiềm ẩn, bảo đảm tính an toàn và độ tin cậy của hệ thống.

Cùng với sự phát triển của nghiệp vụ và quá trình hoàn thiện của hệ thống, thiết kế cơ sở dữ liệu cũng cần được liên tục tối ưu và điều chỉnh để thích ứng với các yêu cầu nghiệp vụ và thách thức kỹ thuật mới.