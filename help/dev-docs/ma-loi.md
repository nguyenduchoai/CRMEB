# Tài liệu mô tả mã lỗi CRMEB

## 1. Lời mở đầu

### 1.1 Mục đích tài liệu
- Chuẩn hóa việc sử dụng mã lỗi trong hệ thống CRMEB
- Cung cấp tiêu chuẩn phân loại và định nghĩa mã lỗi
- Cung cấp hướng dẫn tham khảo về xử lý lỗi cho lập trình viên
- Đảm bảo tính nhất quán và khả năng bảo trì của thông tin lỗi hệ thống

### 1.2 Phạm vi áp dụng
- Phát triển và bảo trì tất cả module của hệ thống CRMEB
- Xử lý lỗi API
- Xử lý lỗi trên hệ thống quản trị
- Xử lý lỗi trên ứng dụng di động

### 1.3 Định nghĩa thuật ngữ
- **Mã lỗi**: Mã số dùng trong hệ thống để định danh một loại lỗi cụ thể
- **Lớp ngoại lệ**: Lớp dùng để xử lý và ném (throw) lỗi
- **Đa ngôn ngữ**: Thông tin lỗi bằng các ngôn ngữ khác nhau mà hệ thống hỗ trợ

## 2. Quy chuẩn thiết kế mã lỗi

### 2.1 Định dạng mã lỗi
- **Định dạng**: Chỉ gồm chữ số, độ dài 3-6 chữ số
- **Ví dụ**: 400, 401, 400001, 400086 

### 2.2 Phân loại mã lỗi

| Phạm vi | Danh mục | Mô tả | Ví dụ |
|------|------|------|------|
| 100-199 | Thông tin | Thông báo thông tin chung | 100 |
| 200-299 | Phản hồi thành công | Xử lý yêu cầu thành công | 200 |
| 300-399 | Chuyển hướng | Cần chuyển hướng | 301, 302 |
| 400-499 | Lỗi phía client | Lỗi yêu cầu từ client | 400, 401, 403, 404 |
| 500-599 | Lỗi máy chủ | Lỗi máy chủ nội bộ | 500, 501, 503 |
| 400000-499999 | Lỗi nghiệp vụ | Lỗi logic nghiệp vụ cụ thể | 400086, 400087 |
| 600000-699999 | Lỗi hệ thống | Lỗi cấp hệ thống | 600001 |
| 700000-799999 | Lỗi bên thứ ba | Lỗi dịch vụ bên thứ ba | 700001 |

### 2.3 Quy tắc đặt tên mã lỗi
- Mã lỗi nên mang ngữ nghĩa, dễ hiểu và dễ nhớ
- Mã lỗi của cùng một module nên tập trung trong một dải
- Mã lỗi phải là duy nhất, tránh trùng lặp

## 3. Cơ chế xử lý lỗi

### 3.1 Hệ thống lớp ngoại lệ

Hệ thống CRMEB dùng các lớp ngoại lệ sau để xử lý lỗi:

| Lớp ngoại lệ | Namespace | Công dụng |
|--------|----------|------|
| ApiException | crmeb\exceptions | Lỗi API |
| AuthException | crmeb\exceptions | Lỗi xác thực và phân quyền |
| AdminException | crmeb\exceptions | Lỗi trang quản trị |
| PayException | crmeb\exceptions | Lỗi liên quan đến thanh toán |
| CrudException | crmeb\exceptions | Lỗi thao tác CRUD |
| ApiStatusException | crmeb\exceptions | Lỗi API kèm mã trạng thái |
| OAuthException | crmeb\services\oauth | Lỗi xác thực OAuth |

### 3.2 Cách ném lỗi

#### 3.2.1 Ném ngoại lệ trực tiếp
```php
// Dùng thông báo lỗi
throw new ApiException('Tham số không hợp lệ');

// Dùng mã lỗi
throw new ApiException(400086);

// Dùng mã lỗi và tham số thay thế
throw new ApiException(400086, ['name' => 'Tên người dùng']);

// Dùng dạng mảng
throw new ApiException([400086, 'Tham số không hợp lệ']);
```

#### 3.2.2 Sử dụng trong validator
```php
protected $message = [
    'name.require' => '400086',
    'sort.require' => '400087',
    'sort.number' => '400088'
];
```

### 3.3 Lấy thông tin lỗi

Hệ thống dùng hàm `getLang()` để lấy thông tin lỗi, hỗ trợ đa ngôn ngữ:

```php
// Lấy thông tin lỗi
$message = getLang($code, $replace);
```

## 4. Danh sách mã lỗi thường gặp

### 4.1 Mã lỗi cơ bản

| Mã lỗi | Thông tin lỗi | Mô tả | Danh mục |
|--------|----------|------|------|
| 200 | Thao tác thành công | Xử lý yêu cầu thành công | Phản hồi thành công |
| 400 | Tham số không hợp lệ | Định dạng tham số yêu cầu không hợp lệ | Lỗi phía client |
| 401 | Chưa được ủy quyền | Chưa đăng nhập hoặc phiên đăng nhập đã hết hạn | Lỗi phía client |
| 403 | Cấm truy cập | Không có quyền thực hiện thao tác này | Lỗi phía client |
| 404 | Tài nguyên không tồn tại | Tài nguyên được yêu cầu không tồn tại | Lỗi phía client |
| 500 | Lỗi máy chủ nội bộ | Máy chủ gặp lỗi khi xử lý yêu cầu | Lỗi máy chủ |
| 501 | Chức năng chưa được triển khai | Chức năng được yêu cầu chưa được triển khai | Lỗi máy chủ |
| 503 | Dịch vụ không khả dụng | Máy chủ tạm thời không thể xử lý yêu cầu | Lỗi máy chủ |

### 4.2 Mã lỗi nghiệp vụ

#### 4.2.1 Liên quan đến người dùng

| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|----------|------|
| 400001 | Tên người dùng đã tồn tại | Khi đăng ký, tên người dùng đã được sử dụng |
| 400002 | Số điện thoại đã được đăng ký | Khi đăng ký, số điện thoại đã được sử dụng |
| 400003 | Email đã được đăng ký | Khi đăng ký, email đã được sử dụng |
| 400004 | Tên đăng nhập hoặc mật khẩu không đúng | Khi đăng nhập, tên người dùng hoặc mật khẩu không đúng |
| 400005 | Người dùng không tồn tại | Người dùng được yêu cầu không tồn tại |
| 400006 | Tài khoản đã bị vô hiệu hóa | Tài khoản người dùng bị quản trị viên vô hiệu hóa |
| 400007 | Mã xác thực không đúng | Mã xác thực không đúng |
| 400008 | Mã xác thực đã hết hạn | Mã xác thực đã quá thời hạn hiệu lực |

#### 4.2.2 Liên quan đến sản phẩm

| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|----------|------|
| 400101 | Sản phẩm không tồn tại | Sản phẩm được yêu cầu không tồn tại |
| 400102 | Sản phẩm đã ngừng bán | Sản phẩm đã bị ngừng bán |
| 400103 | Sản phẩm không đủ tồn kho | Sản phẩm không đủ tồn kho |
| 400104 | Giá sản phẩm bất thường | Giá sản phẩm không đáp ứng yêu cầu |
| 400105 | Danh mục sản phẩm không tồn tại | Danh mục sản phẩm được yêu cầu không tồn tại |

#### 4.2.3 Liên quan đến đơn hàng

| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|----------|------|
| 400201 | Đơn hàng không tồn tại | Đơn hàng được yêu cầu không tồn tại |
| 400202 | Trạng thái đơn hàng không hợp lệ | Trạng thái đơn hàng không phù hợp với yêu cầu của thao tác |
| 400203 | Số tiền đơn hàng không đúng | Lỗi tính toán số tiền đơn hàng |
| 400204 | Thanh toán thất bại | Xảy ra lỗi trong quá trình thanh toán |
| 400205 | Hoàn tiền thất bại | Xảy ra lỗi trong quá trình hoàn tiền |
| 400206 | Thông tin địa chỉ không đúng | Thông tin địa chỉ nhận hàng không chính xác |

#### 4.2.4 Liên quan đến quyền

| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|----------|------|
| 400301 | Không có quyền thao tác | Người dùng không có quyền thực hiện thao tác này |
| 400302 | Vai trò không tồn tại | Vai trò được yêu cầu không tồn tại |
| 400303 | Menu không tồn tại | Menu được yêu cầu không tồn tại |
| 400304 | Không đủ quyền | Người dùng không đủ quyền |

#### 4.2.5 Liên quan đến hệ thống

| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|----------|------|
| 400401 | Lỗi cấu hình | Lỗi cấu hình hệ thống |
| 400402 | Lỗi mạng | Kết nối mạng thất bại |
| 400403 | Tải tệp lên thất bại | Xảy ra lỗi trong quá trình tải tệp lên |
| 400404 | Lỗi cơ sở dữ liệu | Thao tác cơ sở dữ liệu thất bại |
| 400405 | Lỗi bộ nhớ đệm | Thao tác bộ nhớ đệm thất bại |

### 4.3 Mã lỗi của validator

| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|----------|------|
| 400086 | Tên không được để trống | Kiểm tra trường tên là bắt buộc |
| 400087 | Thứ tự sắp xếp không được để trống | Kiểm tra trường thứ tự sắp xếp là bắt buộc |
| 400088 | Thứ tự sắp xếp phải là số | Kiểm tra trường thứ tự sắp xếp phải là số |

## 5. Quản lý mã lỗi

### 5.1 Định nghĩa mã lỗi

Mã lỗi nên được định nghĩa tập trung trong tệp ngôn ngữ, thuận tiện cho việc quản lý và hỗ trợ đa ngôn ngữ:

```php
// Ví dụ file ngôn ngữ
return [
    400001 => 'Tên người dùng đã tồn tại',
    400002 => 'Số điện thoại đã được đăng ký',
    // Các mã lỗi khác...
];
```

### 5.2 Phân bổ mã lỗi

- Mã lỗi nên được phân bổ theo các dải khác nhau cho từng module
- Khi thêm mã lỗi mới cần tránh xung đột với các mã lỗi hiện có
- Việc phân bổ mã lỗi nên được ghi lại trong tài liệu để tiện tra cứu

### 5.3 Bảo trì mã lỗi

- Định kỳ kiểm tra tình hình sử dụng mã lỗi
- Dọn dẹp các mã lỗi không còn sử dụng
- Thống nhất quy chuẩn đặt tên và mô tả mã lỗi

## 6. Thực hành tốt nhất về xử lý lỗi

### 6.1 Xử lý lỗi phía frontend

1. **Thống nhất thông báo lỗi**: Dùng component thông báo lỗi thống nhất
2. **Ánh xạ mã lỗi**: Hiển thị thông tin lỗi tương ứng dựa trên mã lỗi
3. **Thân thiện với người dùng**: Thông tin lỗi nên ngắn gọn, rõ ràng, dễ hiểu đối với người dùng
4. **Log lỗi**: Ghi log lỗi phía frontend để thuận tiện điều tra sự cố

### 6.2 Xử lý lỗi phía backend

1. **Xử lý theo tầng**: Các tầng khác nhau dùng các lớp ngoại lệ khác nhau
2. **Log chi tiết**: Ghi log lỗi chi tiết, bao gồm mã lỗi, thông tin lỗi, tham số yêu cầu, v.v.
3. **Quy chuẩn mã lỗi**: Sử dụng mã lỗi tuân thủ nghiêm ngặt quy chuẩn mã lỗi
4. **Bắt ngoại lệ**: Bắt ngoại lệ hợp lý, tránh làm sập hệ thống
5. **Quốc tế hóa thông tin lỗi**: Hỗ trợ thông tin lỗi đa ngôn ngữ

### 6.3 Khuyến nghị sử dụng mã lỗi

1. **Có ngữ nghĩa**: Mã lỗi nên mang ngữ nghĩa, dễ hiểu
2. **Tính nhất quán**: Các lỗi cùng loại nên dùng mã lỗi trong cùng một dải
3. **Khả năng mở rộng**: Dành sẵn đủ không gian mã lỗi để thuận tiện mở rộng về sau
4. **Tài liệu hóa**: Cập nhật tài liệu mã lỗi kịp thời, đảm bảo tài liệu và code nhất quán

## 7. Mở rộng mã lỗi

### 7.1 Mã lỗi tùy chỉnh

Lập trình viên có thể tùy chỉnh mã lỗi theo nhu cầu nghiệp vụ, theo các bước sau:

1. Xác định dải và phân loại của mã lỗi
2. Định nghĩa thông tin lỗi trong tệp ngôn ngữ
3. Sử dụng mã lỗi trong code
4. Cập nhật tài liệu mã lỗi

### 7.2 Quản lý phiên bản mã lỗi

Khi nâng cấp phiên bản hệ thống, việc quản lý mã lỗi cần lưu ý:

1. Duy trì tương thích ngược: Tránh thay đổi ý nghĩa của các mã lỗi hiện có
2. Thêm mã lỗi mới: Thêm mã lỗi mới trong dải đã dành sẵn
3. Ngừng sử dụng mã lỗi: Đánh dấu các mã lỗi không còn sử dụng

## 8. Sự cố thường gặp

### 8.1 Trùng lặp mã lỗi

**Vấn đề**: Các module khác nhau dùng cùng một mã lỗi

**Cách khắc phục**:
- Phân bổ dải mã lỗi theo module
- Xây dựng cơ chế quản lý mã lỗi
- Định kỳ kiểm tra tình hình sử dụng mã lỗi

### 8.2 Thông báo lỗi không rõ ràng

**Vấn đề**: Thông tin lỗi không đủ rõ ràng, khó hiểu

**Cách khắc phục**:
- Thông tin lỗi nên ngắn gọn, rõ ràng
- Thông tin lỗi nên nêu nguyên nhân lỗi cụ thể
- Thông tin lỗi nên dễ hiểu đối với người dùng

### 8.3 Xử lý lỗi không nhất quán

**Vấn đề**: Cách xử lý lỗi giữa các module không nhất quán

**Cách khắc phục**:
- Thống nhất cơ chế xử lý lỗi
- Dùng các lớp ngoại lệ thống nhất
- Tuân theo các thực hành tốt nhất về xử lý lỗi

## 9. Tổng kết

Mã lỗi là một phần quan trọng trong hệ thống, việc thiết kế và sử dụng mã lỗi hợp lý có thể nâng cao khả năng bảo trì của hệ thống và trải nghiệm người dùng. Tài liệu này cung cấp quy chuẩn thiết kế, cách sử dụng và các thực hành tốt nhất về mã lỗi của hệ thống CRMEB, hy vọng có thể giúp lập trình viên hiểu và sử dụng mã lỗi tốt hơn.

Trong quá trình phát triển thực tế, lập trình viên cần tuân thủ nghiêm ngặt các quy chuẩn trong tài liệu này, đảm bảo tính nhất quán và khả năng bảo trì của mã lỗi. Đồng thời, cần không ngừng hoàn thiện và mở rộng hệ thống mã lỗi theo nhu cầu nghiệp vụ để thích ứng với sự phát triển và thay đổi của hệ thống.

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.