# Tài liệu quy trình phát triển frontend trang quản trị

## 1. Tổng quan

Tài liệu này mô tả quy trình phát triển frontend trang quản trị trong dự án CRMEB, bao gồm các khâu phân tích yêu cầu, thiết kế, lập trình, kiểm thử, triển khai, v.v., nhằm chuẩn hóa quy trình phát triển frontend, nâng cao hiệu quả phát triển và chất lượng code.

## 2. Quy trình phát triển

### 2.1 Phân tích yêu cầu

#### 2.1.1 Thu thập yêu cầu
- **Nguồn yêu cầu**: Quản lý sản phẩm, bộ phận nghiệp vụ, lập trình viên backend, nhân viên kiểm thử
- **Hình thức yêu cầu**: Tài liệu PRD, cuộc họp đánh giá yêu cầu, email, công cụ nhắn tin tức thời
- **Nội dung yêu cầu**: Yêu cầu chức năng, yêu cầu phi chức năng, yêu cầu UI/UX

#### 2.1.2 Đánh giá yêu cầu
- **Người đánh giá**: Lập trình viên frontend, lập trình viên backend, quản lý sản phẩm, nhân viên kiểm thử
- **Nội dung đánh giá**: Tính khả thi của yêu cầu, phương án triển khai kỹ thuật, ước lượng thời gian, đánh giá rủi ro
- **Đầu ra đánh giá**: Biên bản đánh giá, phân rã công việc, kế hoạch thời gian

#### 2.1.3 Xác nhận yêu cầu
- **Nội dung xác nhận**: Chi tiết yêu cầu, phương án kỹ thuật, tiêu chuẩn bàn giao
- **Hình thức xác nhận**: Email xác nhận yêu cầu, cuộc họp xác nhận yêu cầu
- **Đầu ra xác nhận**: Tài liệu xác nhận yêu cầu

### 2.2 Giai đoạn thiết kế

#### 2.2.1 Thiết kế UI
- **Công cụ thiết kế**: Figma, Sketch, Adobe XD
- **Nội dung thiết kế**: Bố cục trang, kiểu dáng thành phần, thiết kế tương tác, phối màu
- **Đầu ra thiết kế**: Bản thiết kế UI, tài liệu quy chuẩn thiết kế

#### 2.2.2 Thiết kế kỹ thuật
- **Nội dung thiết kế**: Lựa chọn công nghệ, thiết kế kiến trúc, cấu trúc thư mục, thiết kế thành phần, thiết kế API
- **Đầu ra thiết kế**: Tài liệu thiết kế kỹ thuật, sơ đồ kiến trúc, sơ đồ thiết kế thành phần

#### 2.2.3 Thiết kế nguyên mẫu (prototype)
- **Công cụ tạo nguyên mẫu**: Axure, Figma, Sketch
- **Nội dung nguyên mẫu**: Nguyên mẫu trang, nguyên mẫu tương tác, nguyên mẫu luồng
- **Đầu ra nguyên mẫu**: Nguyên mẫu có thể tương tác, lưu đồ

### 2.3 Lập trình

#### 2.3.1 Thiết lập môi trường
- **Môi trường phát triển**: Node.js, npm/yarn, VS Code, Git
- **Cài đặt phụ thuộc**: `npm install` hoặc `yarn install`
- **Công cụ phát triển**: Vue DevTools, Chrome DevTools

#### 2.3.2 Viết code
- **Thứ tự phát triển**: Thành phần cơ sở → Thành phần trang → Logic nghiệp vụ → Kiểm thử
- **Quy chuẩn phát triển**: Tuân thủ quy chuẩn code, quy tắc đặt tên, quy chuẩn tài liệu
- **Chất lượng code**: Khả năng đọc hiểu, khả năng bảo trì, khả năng mở rộng của code

#### 2.3.3 Rà soát code
- **Người rà soát**: Thành viên đội frontend
- **Nội dung rà soát**: Chất lượng code, hiện thực chức năng, tối ưu hiệu năng, phòng vệ bảo mật
- **Công cụ rà soát**: GitLab/GitHub Code Review, ESLint, Prettier

### 2.4 Kiểm thử và xác minh

#### 2.4.1 Kiểm thử đơn vị (unit test)
- **Công cụ kiểm thử**: Jest, Vue Test Utils
- **Nội dung kiểm thử**: Kiểm thử thành phần, kiểm thử hàm tiện ích, kiểm thử quản lý trạng thái
- **Độ bao phủ kiểm thử**: Độ bao phủ kiểm thử của các chức năng quan trọng ≥ 80%

#### 2.4.2 Kiểm thử chức năng
- **Người kiểm thử**: Lập trình viên frontend, nhân viên kiểm thử
- **Nội dung kiểm thử**: Hiện thực chức năng, trải nghiệm tương tác, bố cục trang, thiết kế responsive
- **Công cụ kiểm thử**: Trình duyệt, công cụ quản lý kiểm thử

#### 2.4.3 Kiểm thử hiệu năng
- **Công cụ kiểm thử**: Chrome DevTools, Lighthouse, WebPageTest
- **Nội dung kiểm thử**: Tốc độ tải màn hình đầu tiên, hiệu năng render trang, mức sử dụng bộ nhớ, request mạng
- **Chỉ số kiểm thử**: FCP, LCP, TTI, CLS, FID

#### 2.4.4 Kiểm thử tương thích
- **Trình duyệt kiểm thử**: Chrome, Firefox, Safari, Edge
- **Độ phân giải kiểm thử**: 1024x768, 1366x768, 1920x1080
- **Nội dung kiểm thử**: Hiển thị trang, hiện thực chức năng, trải nghiệm tương tác

### 2.5 Triển khai lên production

#### 2.5.1 Build và đóng gói
- **Lệnh build**: `npm run build:prod`
- **Sản phẩm build**: Các tệp tài nguyên tĩnh
- **Tối ưu build**: Nén code, nén tài nguyên, Tree Shaking

#### 2.5.2 Cấu hình triển khai
- **Môi trường triển khai**: Môi trường kiểm thử, môi trường staging (tiền phát hành), môi trường production
- **Công cụ triển khai**: Nginx, Apache, CDN
- **Nội dung cấu hình**: Cấu hình máy chủ, cấu hình tên miền, cấu hình HTTPS

#### 2.5.3 Phát hành lên production
- **Quy trình phát hành**: Môi trường kiểm thử → Môi trường staging → Môi trường production
- **Thời điểm phát hành**: Ngoài giờ cao điểm kinh doanh
- **Giám sát phát hành**: Giám sát trạng thái vận hành của hệ thống theo thời gian thực

#### 2.5.4 Kiểm tra sau khi lên production
- **Nội dung kiểm tra**: Kiểm tra chức năng, kiểm tra hiệu năng, kiểm tra tương thích
- **Người kiểm tra**: Lập trình viên frontend, nhân viên kiểm thử, quản lý sản phẩm
- **Đầu ra kiểm tra**: Báo cáo kiểm tra sau khi lên production

## 3. Quy chuẩn phát triển

### 3.1 Quy chuẩn code
- **Quy tắc ESLint**: Tuân theo các quy tắc ESLint do Vue chính thức khuyến nghị
- **Cấu hình Prettier**: Thống nhất quy tắc định dạng code
- **Thụt lề code**: Thụt lề 4 dấu cách
- **Quy tắc đặt tên**: Tên thành phần dùng PascalCase, tên biến/phương thức dùng camelCase, hằng số viết hoa toàn bộ

### 3.2 Quy chuẩn commit
- **Định dạng thông điệp commit**: `[loại] mô tả`
- **Loại**: feat (tính năng mới), fix (sửa lỗi), docs (tài liệu), style (kiểu dáng), refactor (tái cấu trúc), test (kiểm thử), chore (build/phụ thuộc)
- **Tần suất commit**: Commit theo từng bước nhỏ, mỗi tính năng hoặc bản sửa lỗi một commit
- **Nội dung commit**: Chỉ commit code liên quan, không commit các tệp không liên quan

### 3.3 Quy chuẩn nhánh
- **Nhánh chính**: master (môi trường production), develop (môi trường phát triển)
- **Nhánh tính năng**: feature/tên-tính-năng
- **Nhánh sửa lỗi**: fix/nội-dung-sửa
- **Nhánh phát hành**: release/số-phiên-bản
- **Quản lý nhánh**: Định kỳ dọn các nhánh không còn sử dụng

### 3.4 Quy chuẩn tài liệu
- **README.md**: Giới thiệu dự án, hướng dẫn cài đặt, hướng dẫn phát triển
- **Tài liệu thành phần**: Hướng dẫn sử dụng thành phần, Props, Events, Slots
- **Tài liệu API**: Mô tả API, tham số request, định dạng response
- **Tài liệu phát triển**: Quy trình phát triển, quy chuẩn lập trình, quy trình triển khai

## 4. Công cụ phát triển

### 4.1 Môi trường phát triển
- **Node.js**: v12.0.0+
- **npm**: v6.0.0+ hoặc **yarn**: v1.22.0+
- **VS Code**: Phiên bản mới nhất
- **Git**: Phiên bản mới nhất

### 4.2 Plugin trình soạn thảo
- **Vetur**: Plugin phát triển Vue
- **ESLint**: Plugin kiểm tra code
- **Prettier**: Plugin định dạng code
- **GitLens**: Plugin mở rộng tính năng Git
- **Debugger for Chrome**: Plugin gỡ lỗi trên trình duyệt
- **Auto Close Tag**: Tự động đóng thẻ
- **Auto Rename Tag**: Tự động đổi tên thẻ

### 4.3 Công cụ phát triển
- **Vue DevTools**: Công cụ phát triển và gỡ lỗi cho Vue
- **Chrome DevTools**: Công cụ gỡ lỗi trên trình duyệt
- **Postman**: Công cụ kiểm thử API
- **Charles**: Công cụ gỡ lỗi mạng
- **Figma/Sketch**: Công cụ thiết kế UI
- **Zeplin**: Công cụ cộng tác thiết kế

### 4.4 Công cụ build
- **Webpack**: Công cụ đóng gói mô-đun (module bundler)
- **Babel**: Trình biên dịch JavaScript
- **Sass/Less**: Bộ tiền xử lý CSS
- **ESLint**: Công cụ kiểm tra code
- **Prettier**: Công cụ định dạng code

## 5. Vấn đề thường gặp và giải pháp

### 5.1 Thay đổi yêu cầu

- **Vấn đề**: Yêu cầu thay đổi trong quá trình phát triển
- **Giải pháp**:
  1. Ghi lại nội dung thay đổi yêu cầu
  2. Đánh giá phạm vi ảnh hưởng của thay đổi
  3. Xác nhận tính khả thi của thay đổi với quản lý sản phẩm
  4. Điều chỉnh kế hoạch phát triển và ước lượng thời gian
  5. Thông báo cho các thành viên liên quan trong nhóm

### 5.2 Vấn đề kỹ thuật khó

- **Vấn đề**: Gặp vấn đề kỹ thuật khó không thể giải quyết
- **Giải pháp**:
  1. Tra cứu tài liệu và tư liệu liên quan
  2. Nhờ thành viên trong nhóm giúp đỡ
  3. Tham khảo ý kiến chuyên gia bên ngoài
  4. Thử các phương án thay thế
  5. Ghi lại giải pháp, tích lũy thành cơ sở tri thức

### 5.3 Thời gian gấp rút

- **Vấn đề**: Thời gian phát triển gấp rút, công việc không thể hoàn thành đúng hạn
- **Giải pháp**:
  1. Đánh giá mức độ ưu tiên của công việc
  2. Trao đổi với quản lý sản phẩm, điều chỉnh mức độ ưu tiên của yêu cầu
  3. Nhờ thành viên trong nhóm hỗ trợ
  4. Tối ưu quy trình phát triển, nâng cao hiệu quả phát triển
  5. Tăng ca để hoàn thành (biện pháp cuối cùng)

### 5.4 Xung đột code

- **Vấn đề**: Xung đột code trên Git
- **Giải pháp**:
  1. Pull code mới nhất
  2. Giải quyết xung đột thủ công
  3. Kiểm thử code sau khi giải quyết xung đột
  4. Commit code sau khi giải quyết xung đột
  5. Thông báo cho các thành viên liên quan trong nhóm

### 5.5 Sự cố trên production

- **Vấn đề**: Phát sinh sự cố trên production
- **Giải pháp**:
  1. Nhanh chóng xác định nguyên nhân sự cố
  2. Xây dựng giải pháp
  3. Sửa lỗi khẩn cấp và triển khai
  4. Kiểm tra hiệu quả sửa lỗi
  5. Phân tích nguyên nhân sự cố, tránh để sự cố tương tự tái diễn

## 6. Thực tiễn tốt nhất

### 6.1 Chuẩn bị trước khi phát triển
- **Xác nhận yêu cầu**: Đảm bảo hiểu đúng yêu cầu
- **Phương án kỹ thuật**: Xây dựng phương án triển khai kỹ thuật chi tiết
- **Thiết lập môi trường**: Đảm bảo môi trường phát triển được cấu hình đúng
- **Cài đặt phụ thuộc**: Cài đặt các gói phụ thuộc cần thiết

### 6.2 Lập trình
- **Phát triển theo thành phần**: Đóng gói các thành phần có thể tái sử dụng
- **Phát triển theo mô-đun**: Tổ chức code theo mô-đun chức năng
- **Tái sử dụng code**: Tách logic dùng chung, tránh lặp code
- **Tối ưu hiệu năng**: Chú ý hiệu năng của code, tránh nút thắt cổ chai về hiệu năng
- **Tính bảo mật**: Chú ý tính bảo mật của code, tránh lỗ hổng bảo mật

### 6.3 Kiểm thử và xác minh
- **Kiểm thử đơn vị**: Viết unit test cho các chức năng quan trọng
- **Kiểm thử chức năng**: Kiểm thử toàn diện việc hiện thực chức năng
- **Kiểm thử hiệu năng**: Kiểm thử hiệu năng trang
- **Kiểm thử tương thích**: Kiểm thử khả năng tương thích trên các trình duyệt khác nhau
- **Kiểm thử trải nghiệm người dùng**: Kiểm thử trải nghiệm tương tác của trang

### 6.4 Triển khai lên production
- **Tối ưu build**: Tối ưu sản phẩm build
- **Chiến lược triển khai**: Áp dụng chiến lược phát hành dần (gray release)
- **Giám sát và cảnh báo**: Cấu hình giám sát và cảnh báo cho hệ thống
- **Phương án rollback**: Chuẩn bị phương án rollback hệ thống
- **Kiểm tra sau khi lên production**: Kiểm tra trạng thái hệ thống ngay sau khi lên production

### 6.5 Bảo trì và cải tiến
- **Theo dõi sự cố**: Kịp thời theo dõi và giải quyết sự cố trên production
- **Bảo trì code**: Định kỳ bảo trì và tối ưu code
- **Cập nhật tài liệu**: Kịp thời cập nhật tài liệu liên quan
- **Nợ kỹ thuật**: Định kỳ xử lý nợ kỹ thuật
- **Chia sẻ kiến thức**: Chia sẻ kinh nghiệm phát triển và giải pháp

## 7. Cộng tác nhóm

### 7.1 Mô hình cộng tác
- **Phát triển Agile**: Áp dụng mô hình phát triển Agile theo Scrum hoặc Kanban
- **Họp đứng hằng ngày**: Họp đứng 15 phút mỗi ngày, đồng bộ tiến độ phát triển
- **Chu kỳ lặp**: Mỗi chu kỳ lặp kéo dài 2-4 tuần
- **Họp nhìn lại chu kỳ**: Tổ chức họp nhìn lại sau khi kết thúc chu kỳ lặp, rút ra bài học kinh nghiệm

### 7.2 Công cụ giao tiếp
- **Nhắn tin tức thời**: WeCom, DingTalk, Slack
- **Quản lý dự án**: Jira, Trello, GitHub Issues
- **Lưu trữ code**: GitLab, GitHub, Gitee
- **Cộng tác tài liệu**: Yuque, Confluence, Google Docs

### 7.3 Chia sẻ kiến thức
- **Chia sẻ kỹ thuật**: Định kỳ tổ chức buổi chia sẻ kỹ thuật
- **Rà soát code**: Rà soát code chéo cho nhau, chia sẻ kinh nghiệm lập trình
- **Thảo luận vấn đề**: Định kỳ tổ chức buổi thảo luận vấn đề
- **Cơ sở tri thức**: Xây dựng cơ sở tri thức của nhóm, tích lũy kinh nghiệm kỹ thuật

## 8. Tổng kết

Tài liệu này mô tả quy trình phát triển frontend trang quản trị trong dự án CRMEB, bao gồm các khâu phân tích yêu cầu, thiết kế, lập trình, kiểm thử, triển khai, v.v., cùng các quy chuẩn phát triển và phương pháp hay nhất (best practice) liên quan.

Tuân thủ quy trình và quy chuẩn phát triển trong tài liệu này giúp nâng cao hiệu quả phát triển frontend và chất lượng code, giảm thiểu vấn đề và rủi ro trong quá trình phát triển, đảm bảo dự án diễn ra suôn sẻ.

Đồng thời, quy trình phát triển cũng cần được điều chỉnh và tối ưu theo tình hình thực tế của dự án để phù hợp với nhu cầu và đặc điểm của từng dự án.