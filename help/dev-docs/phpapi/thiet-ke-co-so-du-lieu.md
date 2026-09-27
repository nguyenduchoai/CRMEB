# Thiết kế cấu trúc cơ sở dữ liệu

## 📊 Các bảng dữ liệu cốt lõi

### Bảng liên quan đến người dùng
```
user                    # Bảng chính người dùng
user_level              # Bảng hạng người dùng
user_label              # Bảng nhãn người dùng
user_address            # Bảng địa chỉ người dùng
user_collect            # Bảng yêu thích của người dùng
user_cart               # Bảng giỏ hàng
user_balance            # Bảng số dư người dùng
user_integral           # Bảng điểm thưởng người dùng
```

### Bảng liên quan đến sản phẩm
```
store_product           # Bảng chính sản phẩm
store_product_attr      # Bảng thuộc tính sản phẩm
store_product_attr_value # Bảng giá trị thuộc tính sản phẩm
store_category          # Bảng danh mục sản phẩm
store_product_reply     # Bảng đánh giá sản phẩm
store_product_visit     # Bảng lịch sử xem sản phẩm
store_product_cate      # Bảng liên kết danh mục sản phẩm
```

### Bảng liên quan đến đơn hàng
```
store_order             # Bảng chính đơn hàng
store_order_cart_info   # Bảng thông tin giỏ hàng của đơn hàng
store_order_status      # Bảng lịch sử trạng thái đơn hàng
store_order_refund      # Bảng lịch sử hoàn tiền
store_order_invoice     # Bảng thông tin hóa đơn
store_pay               # Bảng lịch sử thanh toán
```

### Bảng liên quan đến marketing
```
store_coupon            # Bảng phiếu giảm giá
store_coupon_user       # Bảng phiếu giảm giá của người dùng
store_combination       # Bảng chương trình mua chung
store_bargain           # Bảng chương trình săn giảm giá
store_seckill           # Bảng chương trình flash sale
store_integral_goods    # Bảng sản phẩm đổi điểm
store_gift              # Bảng quà tặng
```

### Bảng liên quan đến OA WeChat/Mini Program
```
wechat_user             # Bảng người dùng WeChat
wechat_message          # Bảng tin nhắn WeChat
wechat_qrcode           # Bảng mã QR
wechat_template         # Bảng tin nhắn mẫu
```

### Bảng cấu hình hệ thống
```
system_config           # Bảng cấu hình hệ thống
system_admin            # Bảng quản trị viên
system_role             # Bảng vai trò
system_menus            # Bảng menu
system_log              # Bảng log thao tác
```

## 🔗 Mô tả quan hệ giữa các bảng

### Quan hệ cốt lõi của đơn hàng
```
store_order (1) ──→ (N) store_order_cart_info
store_order (1) ──→ (N) store_order_status
store_order (1) ──→ (N) store_pay
store_order (1) ──→ (N) store_order_refund
```

### Quan hệ giữa người dùng và đơn hàng
```
user (1) ──→ (N) store_order
user (1) ──→ (N) user_address
user (1) ──→ (N) user_cart
user (1) ──→ (N) store_coupon_user
```

### Quan hệ cốt lõi của sản phẩm
```
store_product (1) ──→ (N) store_product_attr
store_product (1) ──→ (N) store_product_attr_value
store_product (1) ──→ (N) store_category
store_product (1) ──→ (N) store_product_reply
```

## 📝 Thiết kế chỉ mục

### Chỉ mục khóa chính
Tất cả các bảng đều dùng trường `id` làm khóa chính với chỉ mục tự tăng

### Chỉ mục truy vấn thường dùng
```
Bảng user: phone, openid, unionid
Bảng store_order: order_id, uid, status, add_time
Bảng store_product: cate_id, is_hot, is_new, is_benefit
Bảng wechat_user: openid, uid
```

### Chỉ mục kết hợp (composite index)
```sql
-- Tra cứu danh sách đơn hàng
INDEX idx_order_list (uid, add_time, status)

-- Truy vấn danh sách sản phẩm  
INDEX idx_product_list (cate_id, is_hot, is_new)
```

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
