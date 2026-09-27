# Mô tả quy trình nghiệp vụ

## 📋 Quy trình nghiệp vụ cốt lõi

### 🛒 Quy trình tạo đơn hàng
1. Người dùng chọn sản phẩm → Thêm vào giỏ hàng → Tạo đơn hàng
2. Xác thực đơn hàng (tồn kho, giá, phiếu giảm giá)
3. Tạo dữ liệu đơn hàng
4. Trừ tồn kho
5. Gửi thông báo đơn hàng

### 💳 Quy trình xử lý thanh toán  
1. Người dùng chọn phương thức thanh toán
2. Gọi API thanh toán
3. Callback kết quả thanh toán
4. Cập nhật trạng thái đơn hàng
5. Ghi nhận giao dịch thanh toán

### 📦 Quy trình giao hàng
1. Kiểm tra trạng thái đơn hàng (đã thanh toán)
2. Tạo phiếu giao hàng
3. Gọi API vận chuyển
4. Cập nhật trạng thái giao hàng
5. Thông báo thông tin vận chuyển cho người dùng

### 🔄 Quy trình hậu mãi
1. Người dùng gửi yêu cầu hậu mãi
2. Duyệt yêu cầu hậu mãi
3. Xử lý hoàn tiền/đổi hàng
4. Cập nhật tồn kho và tài chính
5. Hoàn tất quy trình hậu mãi

## ⏱️ Quy trình tác vụ bất đồng bộ
- Hàng đợi tin nhắn xử lý các tác vụ liên quan đến đơn hàng
- Tác vụ định kỳ xử lý thống kê dữ liệu
- Tác vụ nền xử lý các thao tác hàng loạt

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
