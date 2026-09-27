# Tài liệu API CRMEB

## 📍 Phân chia điểm vào API
- **Trang quản trị**: `/adminapi/controller/v1/` (cần quyền quản trị viên)
- **Người dùng frontend**: `/api/controller/v1/` (cần người dùng đăng nhập)
- **API công khai**: `/api/controller/publics/` (không cần đăng nhập)

## 🔐 Cơ chế xác thực
```php
// JWT Token xác thực
'middleware' => [
    AuthTokenMiddleware::class,  // Xác thực token người dùng
    AdminAuthTokenMiddleware::class  // Xác thực token quản trị viên
]
```

## 📋 Ví dụ API cốt lõi

### API quản lý người dùng
```
GET  /adminapi/v1/user/list      # Danh sách người dùng
POST /adminapi/v1/user/edit      # Sửa người dùng
GET  /api/v1/user/info          # Lấy thông tin người dùng
```

### API quản lý đơn hàng  
```
GET  /adminapi/v1/order/list     # Danh sách đơn hàng
POST /api/v1/order/create       # Tạo đơn hàng
GET  /api/v1/order/detail       # Chi tiết đơn hàng
```

## 🏷️ Định dạng phản hồi thống nhất
```json
{
    "status": 200,
    "msg": "success", 
    "data": {...},
    "time": "2024-01-01 10:00:00"
}
```

## ⚠️ Quy chuẩn mã lỗi
- 200: Thành công
- 400: Tham số không hợp lệ
- 401: Chưa được ủy quyền
- 403: Không đủ quyền  
- 500: Lỗi máy chủ

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
