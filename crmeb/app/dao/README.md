Thư mục crmeb/app/dao là thư mục mã nguồn của tầng đối tượng truy cập dữ liệu (DAO) trong dự án.

Trách nhiệm và vai trò chính của tầng DAO như sau:

1. Thực hiện truy cập tầng lưu trữ dữ liệu bền vững (persistence), thực hiện các thao tác CURD trên cơ sở dữ liệu.

2. Dựa trên kết nối cơ sở dữ liệu, thực hiện các chức năng cơ bản thêm, xóa, sửa, truy vấn trên bảng.

3. Đóng gói các thao tác nguyên thủy với cơ sở dữ liệu (truy vấn, chèn, cập nhật, v.v.), giảm độ khó khi phát triển.

4. Tách rời (decouple) khỏi cơ sở dữ liệu, cung cấp giao diện thống nhất, thuận tiện cho việc mở rộng và bảo trì.

Cụ thể:

- Mỗi tệp trong thư mục dao tương ứng với một bảng dữ liệu hoặc một mô-đun nghiệp vụ
- Trong tệp đóng gói các phương thức thao tác cơ bản với bảng, như tìm kiếm, chèn, cập nhật, v.v.
- Tham số và kiểu giá trị trả về của phương thức là đối tượng mô hình (Model), giúp tách rời dữ liệu và nghiệp vụ
- Cung cấp nhiều điều kiện truy vấn phong phú để thuận tiện khi gọi
- Tầng dưới sử dụng ActiveRecord của ThinkPHP để thực hiện thao tác dữ liệu

Lợi ích của việc sử dụng tầng DAO:

- Cung cấp giao diện thao tác dữ liệu hướng đối tượng
- Che giấu sự khác biệt giữa các cơ sở dữ liệu, tăng tính khả chuyển
- Thuận tiện cho việc kiểm thử và mở rộng
- Tách biệt nghiệp vụ và tầng dữ liệu

Vì vậy, thư mục này chịu trách nhiệm thao tác dữ liệu ở tầng dưới của dự án, các nghiệp vụ khác cần gọi đến nó để thao tác với cơ sở dữ liệu.