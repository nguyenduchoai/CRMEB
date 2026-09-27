Vai trò chính của thư mục crmeb/public trong dự án CRMEB là:

- Là thư mục điểm vào để truy cập từ front-end website hoặc thiết bị di động

- Lưu trữ các tài nguyên tĩnh được truy cập từ bên ngoài của dự án, như các tệp css, js, hình ảnh, v.v.

- Chứa tệp điểm vào index.php, dùng để định tuyến đến controller cụ thể

- Phân giải quy tắc URL giả tĩnh (rewrite) thông qua .htaccess

Cụ thể:

- Người dùng dù truy cập qua trình duyệt hay APP đều truy cập các tệp trong thư mục public

- Các tệp trong thư mục không chứa bất kỳ mã nguồn lõi nào của dự án

- Sau khi phân tích định tuyến, yêu cầu được chuyển đến controller thực tế để xử lý

- Các tệp tài nguyên có thể được lưu trữ và phân phối qua CDN hoặc các cách khác

Ưu điểm của cách thiết kế thư mục này:

- Ẩn cấu trúc tệp nội bộ thực tế của dự án

- Tăng tính bảo mật, bên ngoài không thể truy cập trực tiếp mã nguồn

- Tối ưu việc phân phối tài nguyên tĩnh

Vì vậy, nó đóng vai trò “lớp vỏ” đối ngoại của dự án, đảm nhận chức năng điểm vào của dự án và lưu trữ tài nguyên.