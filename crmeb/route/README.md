Vai trò chính của thư mục crmeb/route trong dự án CRMEB là định nghĩa các quy tắc định tuyến của dự án.

1. Định nghĩa một phương thức Route::miss để xử lý trường hợp không khớp định tuyến

2. Lấy tên ứng dụng dựa trên đường dẫn yêu cầu (như admin, app, v.v.)

3. Trả về các tệp view khác nhau tùy theo tên ứng dụng

   - admin trả về các điểm vào riêng cho front-end và back-end

   - app/kefu định nghĩa view tương ứng

   - home bao gồm điểm vào cho di động và PC

   - Các trường hợp khác kiểm tra có phải thiết bị di động hay không để trả về view khác nhau

4. Định nghĩa đầy đủ mọi điểm vào định tuyến có thể có của dự án

5. Khớp thông minh các tệp tài nguyên view dựa trên thông tin yêu cầu

Vai trò chính:

- Xử lý thống nhất mọi việc khớp định tuyến
- Ẩn điểm vào thực tế của controller
- Phân phối trang theo tên ứng dụng
- Tự động chuyển đổi giữa PC và di động

Thiết kế này có thể:

- Bao quát đầy đủ mọi trường hợp định tuyến
- Ẩn cấu trúc phân cấp định tuyến thực tế
- Phân phối trang một cách thông minh

Đây là một ví dụ rất tốt về thiết kế định tuyến động.