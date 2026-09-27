# Quy tắc Chat dành riêng cho dự án CRMEB

## 1. Tổng quan dự án
- **Tên**: Hệ thống thương mại điện tử mã nguồn mở CRMEB (phiên bản PHP) 
- **Bộ công nghệ**: ThinkPHP 6 + ElementUI + UniApp
- **Phiên bản**: 5.6.4
- **Giấy phép**: Apache-2.0

## 2. Quy chuẩn phong cách mã nguồn

### 2.1 Quy chuẩn PHP
- Tuân thủ quy chuẩn đặt tên PSR-2
- Thiết kế API theo kiểu Restful
- Mã nguồn phân lớp rõ ràng, chú thích ngắn gọn
- Tên lớp dùng PascalCase, phương thức/biến dùng camelCase
- Hằng số viết hoa toàn bộ, phân tách bằng dấu gạch dưới
- Thụt lề 4 dấu cách, cấm dùng ký tự tab

### 2.2 Quy chuẩn Vue
- Tên component dùng PascalCase
- Phương thức/biến dùng camelCase
- Template dùng kebab-case
- Tuân thủ hướng dẫn phong cách (style guide) chính thức của Vue

### 2.3 Quy chuẩn cơ sở dữ liệu
- Tên bảng viết thường, phân tách bằng dấu gạch dưới
- Khóa chính thống nhất đặt tên là `id`
- Định dạng khóa ngoại `{tên_bảng}_id`
- Trường thời gian `create_time`/`update_time`
- Trường trạng thái `status`, giá trị mặc định 0

## 3. Giới hạn lựa chọn công nghệ
- **Backend**: ThinkPHP 6.x (không được nâng cấp lên 7.x)
- **Frontend**: Vue 2.x + ElementUI (Admin), UniApp (di động), Nuxt (PC)
- **Cơ sở dữ liệu**: MySQL 5.7~8.0 (InnoDB)
- **Bộ nhớ đệm**: Redis (khuyên dùng)
- **Hàng đợi**: Hàng đợi tích hợp sẵn của ThinkPHP
- **Kết nối liên tục**: Workerman

## 4. Quy chuẩn quy trình phát triển

### 4.1 Môi trường phát triển
- PHP 7.1~7.4
- Công cụ phát triển: PHPStorm/VS Code
- Quản lý phiên bản: Git

### 4.2 Commit mã nguồn
- Thông điệp commit rõ ràng, mô tả bằng tiếng Việt
- Định dạng: `[tên module] mô tả thao tác`
- Không được commit nhiều tính năng không liên quan trong cùng một lần

### 4.3 Quy trình phát triển tùy biến
1. Đọc tài liệu dự án và chú thích mã nguồn
2. Dùng công cụ sinh mã để tạo các chức năng cơ bản
3. Tuân theo kiến trúc hệ thống, không phá vỡ cấu trúc sẵn có
4. Dùng sự kiện hệ thống để mở rộng chức năng
5. Commit sau khi kiểm thử đạt yêu cầu

## 5. Quy chuẩn bảo mật
- Mọi thao tác phải được ghi nhật ký hệ thống
- Dữ liệu nhạy cảm phải được mã hóa khi lưu trữ
- Cấm nối chuỗi SQL trực tiếp, hãy dùng ràng buộc tham số (parameter binding)
- Kiểm tra dữ liệu người dùng nhập vào, phòng chống tấn công XSS/CSRF
- Dùng hệ thống quản lý quyền tích hợp sẵn, cấm hardcode quyền
- Sử dụng bộ nhớ đệm hợp lý, giảm truy vấn cơ sở dữ liệu
- Dùng hàng đợi để xử lý các tình huống có tải đồng thời cao

## 6. Quy chuẩn triển khai

### 6.1 Môi trường vận hành
- Hệ điều hành: Linux/Windows
- Máy chủ Web: Nginx/Apache/IIS
- Tiện ích mở rộng PHP: fileinfo (tùy chọn), redis (tùy chọn)
- Vô hiệu hóa các hàm nguy hiểm: `proc_open`, `pcntl_signal`, v.v.

### 6.2 Lệnh khởi động
- Hàng đợi tin nhắn: `php think queue:listen --queue` (quản lý bằng Supervisor)
- Kết nối liên tục: `sudo -u www php think workerman start --d`
- Tác vụ định kỳ: `php think timer start --d`

## 7. Các lệnh phát triển thường dùng
- Sinh mã: `php think crmeb:build`
- Migration cơ sở dữ liệu: `php think migrate:run`
- Xem route: `php think route:list`
- Xóa bộ nhớ đệm: `php think clear`

## 8. Tài liệu và hỗ trợ
- Tài liệu chính thức: https://doc.crmeb.com/single_open
- Cộng đồng kỹ thuật: https://www.crmeb.com/ask/thread/list/147

## 9. Lưu ý
- Đảm bảo mã nguồn tương thích với PHP 7.1~7.4
- Frontend tương thích với các trình duyệt phổ biến và hệ điều hành di động
- Tránh các truy vấn SQL liên kết (JOIN) phức tạp
- Chia mảng lớn thành nhiều lô để xử lý
- Giữ mã nguồn gọn gàng, chú thích vừa phải
- Tuân thủ nguyên tắc đơn trách nhiệm (Single Responsibility)

## 10. Xử lý vi phạm
- Các commit vi phạm quy chuẩn sẽ bị từ chối
- Mã nguồn ảnh hưởng đến độ ổn định của hệ thống sẽ bị hoàn tác (rollback)
- Người vi phạm nhiều lần sẽ bị cấm commit mã nguồn

---

Các quy tắc trên áp dụng cho tất cả lập trình viên của dự án CRMEB, nhằm đảm bảo tính nhất quán, khả năng bảo trì và tính bảo mật của mã nguồn.