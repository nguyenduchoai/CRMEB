Thư mục crmeb/app/outapi dùng để định nghĩa các API mà dự án mở ra bên ngoài.

Cụ thể:

- Là thư mục định nghĩa các API mà dự án mở cho các bên thứ ba hợp tác

- Các API ở đây có thể được bên thứ ba gọi trực tiếp để lấy dữ liệu hoặc hoàn thành các nghiệp vụ liên quan

- Loại API này khác với API dùng nội bộ:

  - Mở ra bên ngoài, không cần đăng nhập/ủy quyền
  - Giới hạn bảo mật khá nghiêm ngặt, chỉ cung cấp các API cần thiết
  - Quy chuẩn API tuân theo nguyên tắc RESTful

- Các tình huống phổ biến:

  - Mini Program/APP của bên thứ ba lấy trực tiếp dữ liệu sản phẩm
  - Hệ thống quản trị của người bán bên thứ ba đồng bộ thông tin đơn hàng
  - API nhận thông báo callback thanh toán của Mini Program

Sử dụng thư mục này để định nghĩa API bên ngoài có thể:

- Tích hợp sâu với các hệ thống khác

- Cho phép nhiều tình huống hơn sử dụng được các năng lực do CRMEB cung cấp

- Giảm tính xâm lấn đối với bên thứ ba, chỉ mở các API cần thiết

Tóm lại, outapi dùng cho các API công khai mà dự án mở ra bên ngoài, mở rộng khả năng kết nối của bên thứ ba.