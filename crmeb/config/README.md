Thư mục crmeb/config dùng để lưu trữ các tệp cấu hình của dự án.

Trong phát triển dự án PHP, tệp cấu hình đóng vai trò vô cùng quan trọng:

- Chứa các tham số và thiết lập cấp hệ thống, như thông tin kết nối cơ sở dữ liệu, v.v.
- Tách biệt mã nguồn phần mềm và thiết lập môi trường chạy, thuận tiện cho việc triển khai
- Khi chạy, tải các dịch vụ hệ thống và thành phần (component) theo cấu hình
- Có thể thay đổi tham số mà không cần sửa mã nguồn

Trong dự án CRMEB, thư mục config chịu trách nhiệm:

- Chứa các cấu hình hệ thống như cơ sở dữ liệu, bộ nhớ đệm (cache) cục bộ, API mở của bên thứ ba, v.v.
- Định nghĩa cơ chế tự động tải các thành phần (component) của dự án
- Quy tắc định tuyến và viết lại URL
- Mức độ xuất lỗi và nhật ký (log)
- Tách biệt cấu hình cho các tham số khác nhau giữa các môi trường

Khi chạy, dự án sẽ tải và phân tích các cấu hình này:

- Khởi tạo các dịch vụ hệ thống như kết nối cơ sở dữ liệu
- Đăng ký thành phần (component) vào container
- Tải môi trường chạy theo cấu hình
- Cung cấp tham số và biến cho các mô-đun khác

Vì vậy, thư mục này định nghĩa kiến trúc hệ thống và môi trường chạy của dự án, có ảnh hưởng quan trọng đến dự án.

Thông qua cấu hình, nó mang lại cho dự án khả năng tùy chỉnh cấu hình và mở rộng.