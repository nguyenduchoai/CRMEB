Vai trò chính của thư mục crmeb/runtime trong dự án CRMEB là lưu các tệp tạm được tạo ra trong quá trình dự án chạy.

Framework ThinkPHP sẽ tự động tạo ra một số tệp tạm trong quá trình chạy, chẳng hạn như tệp bộ nhớ đệm (cache), tệp nhật ký (log), v.v. Vị trí của các tệp này được thiết kế nằm trong thư mục runtime.

Cụ thể:

- Chứa kết quả compile được lưu đệm, tránh việc mỗi lần đều phải biên dịch lại định tuyến, v.v.
- Lưu đệm các tệp view của template, tăng tốc kết xuất (render) template
- Lưu các tệp nhật ký (log), thuận tiện cho việc tìm và khắc phục sự cố
- Các tệp Session được lưu trữ theo dạng key-value
- Vị trí lưu tạm các tệp tải lên

Việc sử dụng thư mục này có các ưu điểm sau:

- Tạo tài nguyên động và tự động dọn dẹp
- Tách biệt hoàn toàn với mã nguồn, an toàn và dễ kiểm soát khi chạy
- Dễ dàng thay thế toàn bộ thư mục khi triển khai
- Giảm bớt phần nào nhu cầu quản lý và tối ưu mã nguồn

Nhìn chung, nó đảm nhận việc đọc/ghi các tài nguyên tạm khi dự án chạy.

Bằng cách sử dụng hợp lý thư mục này, có thể cải thiện hiệu năng dự án và đáp ứng nhu cầu triển khai, vận hành.