Thư mục crmeb/app/jobs là thư mục mã nguồn chứa các lớp tác vụ hàng đợi của dự án CRMEB.

Tác vụ hàng đợi có một số vai trò quan trọng sau trong quá trình phát triển dự án:

1. Xử lý bất đồng bộ. Có thể đưa những tác vụ tốn nhiều thời gian vào hàng đợi để xử lý bất đồng bộ, không làm chặn luồng chính.

2. Xử lý trì hoãn. Có thể chỉ định tác vụ hàng đợi thực thi bất đồng bộ sau một khoảng thời gian nhất định, ví dụ như gửi SMS hoặc email.

3. Xử lý phân tán. Có thể phân phối tác vụ hàng đợi đến các máy chủ khác nhau để xử lý, nâng cao hiệu suất sử dụng máy chủ.

Thư mục này chủ yếu bao gồm các nội dung sau:

- Mỗi lớp tác vụ tương ứng với một tác vụ nghiệp vụ, triển khai interface Job.

- Lớp tác vụ định nghĩa logic nghiệp vụ cụ thể của tác vụ, như gửi SMS/email, v.v.

- Gửi và xử lý bất đồng bộ tác vụ thông qua Broker.

- Hỗ trợ các chức năng như trì hoãn tác vụ, thử lại khi thất bại, v.v.

Sử dụng hàng đợi giúp hiệu năng của dự án tốt hơn:

- Tách các tác vụ gây chặn ra để thực thi bất đồng bộ.

- Trong môi trường phân tán, mỗi tác vụ chạy độc lập, không chặn các tiến trình khác.

- Tái sử dụng cùng một dịch vụ thông qua Broker, đồng thời có khả năng mở rộng tốt.

Vì vậy, thư mục này chịu trách nhiệm viết và điều phối tất cả các tác vụ bất đồng bộ trong dự án, đóng vai trò quan trọng trong việc tối ưu hiệu năng và khả năng mở rộng của hệ thống.