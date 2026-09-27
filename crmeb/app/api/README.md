Thư mục crmeb/app/api là thư mục API dành cho frontend của website (không phải trang quản trị).

Điểm khác biệt giữa thư mục này và thư mục adminapi là:

- Thư mục adminapi chứa các API của hệ thống quản trị
- Thư mục api chứa các API của hệ thống frontend website (di động/Mini Program WeChat/H5, v.v.)

Cụ thể:

- Thư mục api cũng tổ chức mã API theo mô hình bộ điều khiển (Controller)
- Mỗi controller tương ứng với một mô-đun chức năng, ví dụ OrderController phụ trách các API liên quan đến đơn hàng, v.v.
- API được dùng cho các yêu cầu ajax của trang frontend, lấy dữ liệu để hiển thị (render)
- API cũng được thiết kế theo phong cách RESTful

Ví dụ:

- API đăng ký người dùng nằm ở phương thức register của UserController
- Lấy danh sách đơn hàng nằm ở phương thức lists của OrderController
- Thông báo kết quả thanh toán nằm ở phương thức notify của PayController

Giống như thư mục adminapi, thư mục api cũng tách rời frontend và backend thông qua việc định nghĩa các API rõ ràng, giúp frontend tập trung hơn vào việc hiển thị nghiệp vụ.

Điểm khác biệt nằm ở đối tượng người dùng mục tiêu:

- adminapi dành cho quản trị viên ở trang quản trị
- Các API trong thư mục api cung cấp dịch vụ dữ liệu cho người dùng phía front-end (thiết bị di động, Mini Program, v.v.)

Vì vậy, cả hai đều đóng vai trò then chốt trong việc tách biệt front-end và back-end.