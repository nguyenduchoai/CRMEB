Thư mục crmeb/app/adminapi chủ yếu chứa các tệp API của hệ thống quản trị.

Cụ thể:

- Các tệp trong thư mục adminapi đều là tệp bộ điều khiển (Controller) của hệ thống quản trị, các controller này được dùng để xử lý mọi loại yêu cầu của hệ thống quản trị.

- Mỗi tệp controller tương ứng với một mô-đun chức năng của hệ thống quản trị, ví dụ AuthController xử lý các yêu cầu của mô-đun xác thực, StoreProduct xử lý các yêu cầu của mô-đun sản phẩm, v.v.

- Trong controller có nhiều phương thức, các phương thức này tương đương với các API, có thể xử lý yêu cầu GET, POST và trả về dữ liệu JSON.

- Khi trình duyệt hoặc APP gọi các API này, yêu cầu sẽ được gửi đến phương thức controller tương ứng, ví dụ yêu cầu của API đăng nhập được gửi đến phương thức login trong tệp Login.

- Sau khi xử lý xong yêu cầu, controller trả kết quả xử lý về cho trình duyệt hoặc APP thông qua việc trả về đối tượng Response.

Nói một cách đơn giản, thư mục adminapi phụ trách toàn bộ API của hệ thống quản trị, các API này được APP hoặc frontend gọi để thực hiện các thao tác quản lý như truy vấn dữ liệu, thêm, sửa, xóa, v.v. Khi thêm chức năng mới cho trang quản trị, lập trình viên cũng cần thêm controller và API tương ứng trong thư mục này.

Thực chất, thư mục này phụ trách tầng giao tiếp tương tác của hệ thống quản trị, tách rời (decouple) logic backend với phần hiển thị frontend, và được thiết kế theo chuẩn RESTful.