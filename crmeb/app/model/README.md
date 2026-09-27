Thư mục crmeb/app/model dùng để định nghĩa các lớp mô hình dữ liệu (model) của dự án.

Vai trò và đặc điểm chính của lớp mô hình dữ liệu gồm:

1. Mỗi lớp mô hình tương ứng với một bảng trong cơ sở dữ liệu.

2. Thuộc tính của lớp định nghĩa cấu trúc bảng, tương ứng một-một với cấu trúc bảng.

3. Chứa các phương thức liên quan đến đọc/ghi dữ liệu, được triển khai thông qua ActiveRecord.

4. Tách rời dữ liệu khỏi tầng cơ sở dữ liệu, cung cấp giao diện truy cập dữ liệu thống nhất.

5. Cơ chế xác thực dữ liệu, đảm bảo tính toàn vẹn và nhất quán của dữ liệu.

Cụ thể bao gồm:

- Định nghĩa thuộc tính của model, tên trường tương ứng với cấu trúc bảng.

- Tự động trả về và gán giá trị thuộc tính.

- Triển khai các phương thức CRUD cơ bản để thao tác với cơ sở dữ liệu.

- Có thể mở rộng logic dữ liệu và quy tắc xác thực tùy chỉnh.

Sử dụng lớp mô hình có thể:

- Giảm độ phức tạp do thao tác trực tiếp với cơ sở dữ liệu.

- Tái sử dụng logic tầng dữ liệu giữa các dự án.

- Nâng cao khả năng mở rộng và tái sử dụng của dự án.

Vì vậy, tầng mô hình dữ liệu được định nghĩa trong thư mục này đóng gói thống nhất các mô hình bảng dữ liệu và cách thao tác mà dự án sử dụng.