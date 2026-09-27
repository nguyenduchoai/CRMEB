# Mô tả mã lỗi hệ thống

## 📋 Quy chuẩn mã lỗi

### Quy chuẩn chung
- **Định dạng**: Mã lỗi 3 chữ số + thông báo lỗi
- **Phân loại**: Phân loại theo module và loại lỗi
- **Phản hồi thống nhất**: Mọi lỗi đều được trả về thông qua cơ chế xử lý ngoại lệ thống nhất

### Định dạng phản hồi
```json
{
    "status": 400,
    "msg": "Tham số không hợp lệ",
    "data": null,
    "time": "2024-01-17 15:30:00"
}
```

## 🔢 Phân loại mã lỗi

| Phạm vi mã lỗi | Loại lỗi | Mô tả |
|-----------|---------|------|
| 200       | Thành công    | Yêu cầu thành công |
| 400-499   | Lỗi phía client | Tham số không hợp lệ, không đủ quyền, v.v. |
| 500-599   | Lỗi máy chủ | Lỗi hệ thống, lỗi cơ sở dữ liệu, v.v. |
| 1000-1999 | Lỗi liên quan đến người dùng | Đăng nhập, đăng ký, thông tin người dùng, v.v. |
| 2000-2999 | Lỗi liên quan đến đơn hàng | Tạo đơn hàng, thanh toán, hậu mãi, v.v. |
| 3000-3999 | Lỗi liên quan đến sản phẩm | Thông tin sản phẩm, tồn kho, danh mục, v.v. |
| 4000-4999 | Lỗi liên quan đến marketing | Phiếu giảm giá, chương trình khuyến mãi, điểm thưởng, v.v. |
| 5000-5999 | Lỗi liên quan đến thanh toán | Xử lý thanh toán, hoàn tiền, v.v. |
| 6000-6999 | Lỗi dịch vụ bên thứ ba | WeChat, SMS, vận chuyển, v.v. |

## 📊 Mã lỗi chung

### Trạng thái thành công
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 200 | success | Yêu cầu thành công |

### Lỗi cấp hệ thống
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 400 | Tham số không hợp lệ | Tham số yêu cầu không đúng quy định |
| 401 | Chưa được ủy quyền | Người dùng chưa đăng nhập hoặc Token không hợp lệ |
| 403 | Không đủ quyền | Người dùng không có quyền thao tác |
| 404 | Tài nguyên không tồn tại | Tài nguyên được yêu cầu không tồn tại |
| 405 | Phương thức không được phép | Phương thức yêu cầu không đúng |
| 408 | Request quá thời gian chờ | Xử lý yêu cầu quá thời gian chờ |
| 429 | Yêu cầu quá thường xuyên | Tần suất yêu cầu vượt quá giới hạn |
| 500 | Lỗi máy chủ nội bộ | Máy chủ xử lý gặp sự cố |
| 501 | Chức năng chưa được triển khai | Chức năng được yêu cầu hiện chưa được hỗ trợ |
| 502 | Lỗi gateway | Dịch vụ upstream không khả dụng |
| 503 | Dịch vụ không khả dụng | Dịch vụ tạm thời không khả dụng |
| 504 | Hết thời gian chờ gateway | Dịch vụ upstream phản hồi quá thời gian chờ |

## 👥 Mã lỗi liên quan đến người dùng (1000-1999)

### Liên quan đến xác thực
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 1001 | Tên đăng nhập hoặc mật khẩu không đúng | Xác thực đăng nhập thất bại |
| 1002 | Mã xác thực không đúng | Mã captcha hình ảnh hoặc mã xác thực SMS không đúng |
| 1003 | Mã xác thực đã hết hạn | Mã xác thực đã quá thời gian hiệu lực |
| 1004 | Tài khoản đã bị vô hiệu hóa | Tài khoản người dùng bị quản trị viên vô hiệu hóa |
| 1005 | Tài khoản chưa được kích hoạt | Tài khoản người dùng chưa hoàn tất quy trình kích hoạt |
| 1006 | Token đã hết hạn | JWT Token đã quá thời hạn hiệu lực |
| 1007 | Token không hợp lệ | JWT Token sai định dạng hoặc đã mất hiệu lực |
| 1008 | Trạng thái đăng nhập bất thường | Kiểm tra trạng thái đăng nhập thất bại |

### Liên quan đến đăng ký
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 1101 | Số điện thoại đã được đăng ký | Số điện thoại đã được người dùng khác đăng ký |
| 1102 | Email đã được đăng ký | Email đã được người dùng khác đăng ký |
| 1103 | Tên người dùng đã tồn tại | Tên người dùng đã được người dùng khác sử dụng |
| 1104 | Mã mời không hợp lệ | Mã mời dùng khi đăng ký không hợp lệ |
| 1105 | Thông tin đăng ký chưa đầy đủ | Thiếu thông tin bắt buộc khi đăng ký |

### Liên quan đến thông tin người dùng
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 1201 | Người dùng không tồn tại | ID người dùng được chỉ định không tồn tại |
| 1202 | Cập nhật thông tin người dùng thất bại | Đã xảy ra lỗi khi cập nhật thông tin người dùng |
| 1203 | Mật khẩu cũ không đúng | Xác minh mật khẩu cũ thất bại khi đổi mật khẩu |
| 1204 | Số điện thoại sai định dạng | Số điện thoại không đúng định dạng |
| 1205 | Email sai định dạng | Email không đúng định dạng |
| 1206 | Xác thực danh tính thất bại | Xác minh thông tin xác thực danh tính thất bại |

## 🛒 Mã lỗi liên quan đến đơn hàng (2000-2999)

### Tạo đơn hàng
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 2001 | Sản phẩm không đủ tồn kho | Sản phẩm không đủ tồn kho khi đặt hàng |
| 2002 | Sản phẩm đã ngừng bán | Sản phẩm đã bị gỡ khỏi kệ khi đặt hàng |
| 2003 | Địa chỉ nhận hàng không hợp lệ | Địa chỉ nhận hàng đã chọn không khả dụng |
| 2004 | Phiếu giảm giá không khả dụng | Phiếu giảm giá đã chọn không đủ điều kiện sử dụng |
| 2005 | Không đủ điểm thưởng | Số điểm thưởng không đủ khi thanh toán bằng điểm |
| 2006 | Số dư không đủ | Số dư không đủ khi thanh toán bằng số dư |
| 2007 | Lỗi tính toán số tiền đơn hàng | Lỗi khi tính số tiền đơn hàng |

### Thanh toán đơn hàng
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 2101 | Phương thức thanh toán không được hỗ trợ | Phương thức thanh toán đã chọn không khả dụng |
| 2102 | Mật khẩu thanh toán không đúng | Mật khẩu thanh toán không đúng khi thanh toán bằng số dư |
| 2103 | Hết thời gian thanh toán | Thao tác thanh toán quá thời gian chờ |
| 2104 | Số tiền thanh toán không khớp | Số tiền thanh toán không khớp với số tiền đơn hàng |
| 2105 | Trạng thái thanh toán bất thường | Trạng thái thanh toán không như mong đợi |

### Thao tác đơn hàng
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 2201 | Trạng thái đơn hàng không cho phép thao tác | Trạng thái đơn hàng hiện tại không hỗ trợ thao tác này |
| 2202 | Đơn hàng không tồn tại | ID đơn hàng được chỉ định không tồn tại |
| 2203 | Hủy đơn hàng thất bại | Đã xảy ra lỗi khi hủy đơn hàng |
| 2204 | Xóa đơn hàng thất bại | Đã xảy ra lỗi khi xóa đơn hàng |
| 2205 | Xác nhận đã nhận hàng cho đơn hàng thất bại | Thao tác xác nhận đã nhận hàng thất bại |

### Liên quan đến hậu mãi
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 2301 | Đã hết thời hạn yêu cầu hậu mãi | Đã vượt quá thời hạn cho phép yêu cầu hậu mãi |
| 2302 | Yêu cầu hậu mãi bị gửi trùng lặp | Cùng một đơn hàng gửi yêu cầu hậu mãi nhiều lần |
| 2303 | Số tiền hoàn vượt quá giới hạn | Số tiền hoàn vượt quá số tiền có thể hoàn |
| 2304 | Trạng thái hậu mãi không cho phép thao tác | Trạng thái hậu mãi hiện tại không hỗ trợ thao tác này |
| 2305 | Thông tin vận chuyển trả hàng không đúng | Mã vận đơn hoặc đơn vị vận chuyển trả hàng không đúng |

## 🏪 Mã lỗi liên quan đến sản phẩm (3000-3999)

### Thông tin sản phẩm
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 3001 | Sản phẩm không tồn tại | ID sản phẩm được chỉ định không tồn tại |
| 3002 | Sản phẩm đã bị xóa | Sản phẩm đã bị xóa logic (soft delete) |
| 3003 | Danh mục sản phẩm không tồn tại | ID danh mục được chỉ định không tồn tại |
| 3004 | Quy cách sản phẩm không hợp lệ | Chọn sai quy cách sản phẩm |
| 3005 | Thuộc tính sản phẩm không đúng | Thông tin thuộc tính sản phẩm không đúng |

### Liên quan đến tồn kho
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 3101 | Trừ tồn kho thất bại | Xảy ra lỗi khi trừ tồn kho sản phẩm |
| 3102 | Rollback tồn kho thất bại | Xảy ra lỗi khi rollback tồn kho sản phẩm |
| 3103 | Khóa tồn kho thất bại | Xảy ra lỗi khi khóa tồn kho sản phẩm |
| 3104 | Tồn kho không đủ | Tồn kho sản phẩm không đủ khi thao tác |

### Liên quan đến giỏ hàng
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 3201 | Sản phẩm trong giỏ hàng không tồn tại | Sản phẩm không tồn tại trong giỏ hàng |
| 3202 | Số lượng sản phẩm trong giỏ hàng vượt giới hạn | Số lượng sản phẩm vượt quá giới hạn mua |
| 3203 | Thao tác giỏ hàng thất bại | Thao tác thêm, xóa sản phẩm trong giỏ hàng thất bại |

## 🎯 Mã lỗi liên quan đến marketing (4000-4999)

### Liên quan đến phiếu giảm giá
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 4001 | Phiếu giảm giá không tồn tại | Phiếu giảm giá được chỉ định không tồn tại |
| 4002 | Phiếu giảm giá đã hết lượt nhận | Phiếu giảm giá đã được nhận hết |
| 4003 | Nhận phiếu giảm giá vượt giới hạn | Vượt quá giới hạn số lượng nhận của mỗi người |
| 4004 | Phiếu giảm giá đã hết hạn | Phiếu giảm giá đã quá thời hạn hiệu lực |
| 4005 | Chưa đáp ứng điều kiện sử dụng phiếu giảm giá | Không đủ điều kiện sử dụng phiếu giảm giá |
| 4006 | Trạng thái phiếu giảm giá bất thường | Trạng thái phiếu giảm giá không khả dụng |

### Liên quan đến chương trình khuyến mãi
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 4101 | Chương trình không tồn tại | Chương trình được chỉ định không tồn tại |
| 4102 | Chương trình chưa bắt đầu | Chương trình chưa bắt đầu |
| 4103 | Chương trình đã kết thúc | Chương trình đã kết thúc |
| 4104 | Số người tham gia chương trình đã đủ | Số người tham gia chương trình đã đạt giới hạn tối đa |
| 4105 | Không đủ tư cách tham gia chương trình | Không đáp ứng điều kiện tham gia chương trình |

### Liên quan đến điểm thưởng
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 4201 | Không đủ điểm thưởng | Số dư điểm thưởng không đủ |
| 4202 | Thao tác điểm thưởng thất bại | Thao tác cộng/trừ điểm thưởng thất bại |
| 4203 | Quy tắc điểm thưởng không tồn tại | Cấu hình quy tắc điểm thưởng không đúng |

## 💳 Mã lỗi liên quan đến thanh toán (5000-5999)

### Xử lý thanh toán
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 5001 | Kênh thanh toán không khả dụng | Kênh thanh toán bị cấu hình sai hoặc không khả dụng |
| 5002 | Chữ ký thanh toán không đúng | Xác minh chữ ký thanh toán thất bại |
| 5003 | Xác minh callback thanh toán thất bại | Xác minh dữ liệu callback thanh toán thất bại |
| 5004 | Đơn thanh toán không tồn tại | Bản ghi đơn thanh toán không tồn tại |
| 5005 | Số tiền thanh toán không đúng | Số tiền thanh toán bất thường |

### Liên quan hoàn tiền
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 5101 | Yêu cầu hoàn tiền thất bại | Thao tác gửi yêu cầu hoàn tiền thất bại |
| 5102 | Số tiền hoàn không đúng | Số tiền hoàn vượt quá số tiền có thể hoàn |
| 5103 | Trạng thái hoàn tiền bất thường | Trạng thái hoàn tiền không như dự kiến |
| 5104 | Chứng từ hoàn tiền không đúng | Thông tin chứng từ hoàn tiền không đúng |

## 🌐 Mã lỗi dịch vụ bên thứ ba (6000-6999)

### Liên quan đến WeChat
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 6001 | Ủy quyền WeChat thất bại | Đăng nhập ủy quyền WeChat thất bại |
| 6002 | Thanh toán WeChat Pay thất bại | Thao tác WeChat Pay thất bại |
| 6003 | Gửi tin nhắn mẫu WeChat thất bại | Gửi tin nhắn mẫu thất bại |
| 6004 | Cấu hình OA WeChat không đúng | Thông tin cấu hình OA WeChat không đúng |

### Liên quan đến SMS
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 6101 | Gửi SMS thất bại | Gọi API gửi SMS thất bại |
| 6102 | Mẫu SMS không tồn tại | Cấu hình mẫu SMS không đúng |
| 6103 | Mã xác thực SMS đã hết hiệu lực | Xác minh mã xác thực SMS thất bại |

### Liên quan đến vận chuyển
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 6201 | Tra cứu vận chuyển thất bại | Tra cứu thông tin vận chuyển thất bại |
| 6202 | Đơn vị vận chuyển không được hỗ trợ | Không hỗ trợ đơn vị vận chuyển này |
| 6203 | Mã vận đơn không đúng | Định dạng mã vận đơn không đúng |

### Liên quan đến lưu trữ tệp
| Mã lỗi | Thông tin lỗi | Mô tả |
|--------|---------|------|
| 6301 | Tải tệp lên thất bại | Tải tệp lên máy chủ thất bại |
| 6302 | Định dạng tệp không được hỗ trợ | Không hỗ trợ định dạng tệp này |
| 6303 | Kích thước tệp vượt giới hạn | Kích thước tệp vượt quá giới hạn cho phép |
| 6304 | Cấu hình lưu trữ đám mây không đúng | Cấu hình dịch vụ lưu trữ đám mây không đúng |

## 🛠️ Thực tiễn tốt nhất khi xử lý lỗi

### Xử lý ở tầng controller
```php
try {
    // Xử lý logic nghiệp vụ
    $result = $service->doSomething($params);
    return $this->success($result);
} catch (ApiException $e) {
    // Bắt ngoại lệ nghiệp vụ
    return $this->fail($e->getMessage(), $e->getCode());
} catch (\Exception $e) {
    // Bắt ngoại lệ hệ thống
    Log::error('Ngoại lệ hệ thống: ' . $e->getMessage());
    return $this->fail('Hệ thống đang bận, vui lòng thử lại sau', 500);
}
```

### Ném ngoại lệ ở tầng service
```php
// Ném ngoại lệ khi xác thực nghiệp vụ thất bại
if (!$user) {
    throw new ApiException('Người dùng không tồn tại', 1201);
}

if ($stock < $quantity) {
    throw new ApiException('Sản phẩm không đủ tồn kho', 2001);
}
```

### Lớp ngoại lệ tùy chỉnh
```php
class ApiException extends \Exception
{
    public function __construct($message = "", $code = 400, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class AdminException extends ApiException
{
    // Ngoại lệ dành riêng cho phía quản trị
}
```

## 📝 Quy chuẩn bảo trì mã lỗi

### Thêm mã lỗi mới
1. Chọn dải mã lỗi phù hợp
2. Ghi lại mã lỗi và mô tả trong tài liệu này
3. Thêm ánh xạ mã lỗi trong phần xử lý ngoại lệ tương ứng

### Nguyên tắc sử dụng mã lỗi
1. **Tính duy nhất**: Mỗi mã lỗi tương ứng với một tình huống lỗi duy nhất
2. **Dễ đọc**: Thông báo lỗi rõ ràng, cụ thể, dễ hiểu
3. **Tính nhất quán**: Các lỗi cùng loại dùng cùng một dải mã lỗi
4. **Khả năng mở rộng**: Dự phòng dải mã lỗi cho việc mở rộng tính năng sau này

## 🔍 Hướng dẫn chẩn đoán và khắc phục lỗi

### Các bước chẩn đoán lỗi thường gặp
1. **Xác nhận mã lỗi**: Dựa vào mã lỗi trả về để xác định loại vấn đề
2. **Xem nhật ký**: Kiểm tra nhật ký (log) của logic nghiệp vụ liên quan
3. **Xác minh tham số**: Kiểm tra tham số yêu cầu có đúng quy định không
4. **Kiểm tra trạng thái**: Xác nhận trạng thái của dữ liệu liên quan có bình thường không
5. **Môi trường kiểm thử**: Tái hiện vấn đề và gỡ lỗi trong môi trường kiểm thử

### Quy chuẩn ghi nhật ký
```php
// Ghi log lỗi
Log::error('Thông tin lỗi', [
    'error_code' => $errorCode,
    'params' => $requestParams,
    'user_id' => $userId,
    'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
]);
```

---
**Cập nhật lần cuối**: 2024-01-17  
**Người bảo trì**: Đội ngũ phát triển  
**Phiên bản tài liệu**: v1.0

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
