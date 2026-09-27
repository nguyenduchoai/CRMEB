Thư mục crmeb/app/services dùng để định nghĩa các lớp dịch vụ nghiệp vụ của dự án.

Đặc điểm và vai trò chính của lớp dịch vụ bao gồm:

1. Lớp dịch vụ đóng gói logic và quy tắc nghiệp vụ cụ thể.

2. Trừu tượng hóa các mô-đun chức năng, cung cấp giao diện nghiệp vụ thống nhất.

3. Tách rời các phần của dự án, giảm mức độ phụ thuộc (coupling) giữa chúng.

4. Được cung cấp để toàn bộ môi trường ngữ cảnh sử dụng.

Cụ thể:

- Mỗi lớp dịch vụ tương ứng với một chức năng nghiệp vụ hoặc một tập quy tắc độc lập.

- Bên trong lớp có thể gọi các mô-đun khác để đáp ứng yêu cầu nghiệp vụ.

- Cung cấp giao diện nghiệp vụ đơn giản ra bên ngoài, ẩn chi tiết triển khai bên trong.

- Các lớp dịch vụ có quan hệ phụ thuộc, có thể gọi lẫn nhau để tạo thành dịch vụ tổng hợp.

Sử dụng thiết kế tầng dịch vụ có thể:

- Giảm sự phụ thuộc giữa các mô-đun (loose coupling), nâng cao khả năng mở rộng và tái sử dụng.

- Tái sử dụng cùng một quy tắc nghiệp vụ trong nhiều tình huống.

- Tăng cường khả năng kiểm thử và bảo trì của dự án.

Vì vậy, thư mục này định nghĩa các mô-đun dịch vụ nghiệp vụ cốt lõi của dự án, cung cấp ra bên ngoài các năng lực cốt lõi có thể tái sử dụng.