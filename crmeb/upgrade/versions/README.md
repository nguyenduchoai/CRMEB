Thư mục crmeb/upgrade/versions trong dự án CRMEB có vai trò chính là lưu trữ các tệp phiên bản và script nâng cấp liên quan đến việc nâng cấp hệ thống.

Trong quá trình cập nhật qua các phiên bản, hệ thống CRMEB cần một cơ chế nâng cấp hoàn chỉnh để đảm bảo việc di chuyển dữ liệu và tương thích giữa các phiên bản. Thư mục này đảm nhận việc quản lý tập trung tài nguyên nâng cấp của từng phiên bản.

Cụ thể:

- Lưu tài liệu hướng dẫn nâng cấp của từng phiên bản, ghi lại nội dung thay đổi của phiên bản
- Chứa các script migration cơ sở dữ liệu, xử lý thay đổi cấu trúc bảng và di chuyển dữ liệu
- Lưu trữ các tệp tài nguyên tĩnh cần cho việc nâng cấp
- Ghi lại quan hệ phụ thuộc giữa các phiên bản và thứ tự nâng cấp
- Lưu tệp đánh dấu phát hiện phiên bản và trạng thái nâng cấp

Việc sử dụng thư mục này có các ưu điểm sau:

- Quản lý nâng cấp phiên bản theo mô-đun, thuận tiện cho việc truy vết và rollback
- Tách biệt với mã nghiệp vụ cốt lõi, giảm rủi ro khi nâng cấp
- Hỗ trợ nâng cấp tăng dần qua nhiều phiên bản, linh hoạt thích ứng với các phiên bản xuất phát khác nhau
- Giúp công cụ triển khai tự động dễ dàng nhận diện và thực hiện quy trình nâng cấp

Nhìn chung, thư mục này đảm nhận việc điều phối quá trình cập nhật phiên bản hệ thống và di chuyển cơ sở dữ liệu.

Bằng cách sử dụng thư mục này đúng quy chuẩn, có thể đảm bảo quá trình nâng cấp an toàn, kiểm soát được và truy vết được.
