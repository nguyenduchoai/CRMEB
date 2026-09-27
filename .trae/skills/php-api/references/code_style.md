# Tài liệu quy chuẩn code

## 1. Tổng quan

Tài liệu này mô tả quy chuẩn code của dự án CRMEB, bao gồm tiêu chuẩn viết code cho các ngôn ngữ PHP, Vue, JavaScript, v.v., nhằm thống nhất phong cách code, nâng cao chất lượng và khả năng bảo trì của code.

## 2. Quy chuẩn code PHP

### 2.1 Quy tắc đặt tên

- **Tên lớp**: sử dụng kiểu đặt tên PascalCase, ví dụ `UserController`
- **Tên phương thức**: sử dụng kiểu đặt tên camelCase, ví dụ `getUserList`
- **Tên biến**: sử dụng kiểu đặt tên camelCase, ví dụ `$userName`
- **Hằng số**: viết hoa toàn bộ, các từ phân tách bằng dấu gạch dưới, ví dụ `MAX_PAGE_SIZE`
- **Tên thuộc tính**: sử dụng kiểu đặt tên camelCase, ví dụ `$userId`
- **Tên tệp**: trùng với tên lớp, sử dụng kiểu đặt tên PascalCase, ví dụ `UserController.php`
- **Tên thư mục**: sử dụng chữ thường, các từ phân tách bằng dấu gạch dưới, ví dụ `user_service`

### 2.2 Định dạng code

- **Thụt lề**: thụt lề bằng 4 dấu cách, không được dùng ký tự tab
- **Độ dài dòng**: mỗi dòng code không quá 120 ký tự
- **Dòng trống**: sử dụng dòng trống để phân tách giữa các lớp, phương thức, khối logic
- **Dấu cách**:
  - Đặt dấu cách ở hai bên toán tử, ví dụ `$a = $b + $c`
  - Đặt dấu cách sau dấu phẩy, ví dụ `function($a, $b, $c)`
  - Không đặt dấu cách bên trong dấu ngoặc, ví dụ `if($condition)`

### 2.3 Quy chuẩn chú thích

- **Chú thích lớp**: sử dụng DocBlock để chú thích chức năng, tác giả và phiên bản của lớp
- **Chú thích phương thức**: sử dụng DocBlock để chú thích chức năng, tham số, giá trị trả về và ngoại lệ của phương thức
- **Chú thích code**: thêm chú thích cho các đoạn logic phức tạp
- **Chú thích TODO**: sử dụng `// TODO:` để đánh dấu các công việc cần hoàn thành

### 2.4 Cấu trúc code

- **Cấu trúc lớp**:
  - Khai báo lớp
  - Khai báo thuộc tính
  - Phương thức khởi tạo
  - Các phương thức khác

- **Cấu trúc phương thức**:
  - Khai báo tham số
  - Khai báo biến
  - Logic nghiệp vụ
  - Câu lệnh return

### 2.5 Thực tiễn tốt nhất

- **Đơn trách nhiệm**: mỗi lớp và phương thức chỉ đảm nhận một chức năng
- **Tái sử dụng code**: tách phần code dùng chung thành phương thức hoặc lớp
- **Xử lý ngoại lệ**: sử dụng try-catch để xử lý ngoại lệ
- **Khai báo kiểu (type hint)**: sử dụng type hint để code dễ đọc hơn
- **Magic method**: thận trọng khi sử dụng magic method

## 3. Quy chuẩn code Vue

### 3.1 Quy tắc đặt tên

- **Tên component**: sử dụng kiểu đặt tên PascalCase, ví dụ `UserList`
- **props**: sử dụng kiểu đặt tên camelCase, ví dụ `userName`
- **Thuộc tính data**: sử dụng kiểu đặt tên camelCase, ví dụ `userList`
- **methods**: sử dụng kiểu đặt tên camelCase, ví dụ `getUserList`
- **Tên sự kiện**: sử dụng kiểu đặt tên kebab-case, ví dụ `user-clicked`
- **Tên slot**: sử dụng kiểu đặt tên kebab-case, ví dụ `header-content`

### 3.2 Định dạng code

- **Thụt lề**: thụt lề bằng 2 dấu cách
- **Độ dài dòng**: mỗi dòng code không quá 120 ký tự
- **Dòng trống**: sử dụng dòng trống để phân tách giữa các khối logic khác nhau
- **Dấu cách**:
  - Đặt dấu cách ở hai bên toán tử
  - Đặt dấu cách sau dấu phẩy
  - Không đặt dấu cách bên trong dấu ngoặc

### 3.3 Quy chuẩn template

- **Tên thẻ**: sử dụng kiểu đặt tên kebab-case, ví dụ `<user-list>`
- **Tên thuộc tính**: sử dụng kiểu đặt tên kebab-case, ví dụ `:user-name="userName"`
- **Viết tắt directive**: sử dụng `:` thay cho `v-bind:`, sử dụng `@` thay cho `v-on:`
- **Biểu thức template**: giữ biểu thức template ngắn gọn, logic phức tạp nên được xử lý trong thuộc tính computed hoặc phương thức

### 3.4 Cấu trúc component

- **template**: phần template
- **script**: phần script
- **style**: phần style

### 3.5 Thực tiễn tốt nhất

- **Tách component**: tách component phức tạp thành nhiều component con
- **Thuộc tính computed**: sử dụng thuộc tính computed để xử lý dữ liệu reactive phức tạp
- **Watcher**: sử dụng watcher để theo dõi sự thay đổi của dữ liệu
- **Mixin**: sử dụng mixin để tái sử dụng logic của component
- **Directive**: sử dụng directive tùy chỉnh để đóng gói các thao tác DOM

## 4. Quy chuẩn code JavaScript

### 4.1 Quy tắc đặt tên

- **Tên biến**: sử dụng kiểu đặt tên camelCase, ví dụ `userName`
- **Tên hàm**: sử dụng kiểu đặt tên camelCase, ví dụ `getUserList`
- **Hằng số**: viết hoa toàn bộ, các từ phân tách bằng dấu gạch dưới, ví dụ `MAX_PAGE_SIZE`
- **Thuộc tính đối tượng**: sử dụng kiểu đặt tên camelCase, ví dụ `userName`
- **Tên lớp**: sử dụng kiểu đặt tên PascalCase, ví dụ `UserService`

### 4.2 Định dạng code

- **Thụt lề**: thụt lề bằng 2 dấu cách
- **Độ dài dòng**: mỗi dòng code không quá 120 ký tự
- **Dòng trống**: sử dụng dòng trống để phân tách giữa các khối logic khác nhau
- **Dấu cách**:
  - Đặt dấu cách ở hai bên toán tử
  - Đặt dấu cách sau dấu phẩy
  - Không đặt dấu cách bên trong dấu ngoặc

### 4.3 Cấu trúc code

- **Khai báo hàm**: sử dụng biểu thức hàm (function expression) hoặc arrow function
- **Object literal**: sử dụng cú pháp object literal ngắn gọn
- **Array literal**: sử dụng cú pháp array literal ngắn gọn
- **Câu lệnh điều kiện**: sử dụng cú pháp câu lệnh điều kiện ngắn gọn
- **Câu lệnh lặp**: sử dụng cú pháp câu lệnh lặp ngắn gọn

### 4.4 Thực tiễn tốt nhất

- **Khai báo biến**: sử dụng `let` và `const` để khai báo biến, tránh dùng `var`
- **Arrow function**: sử dụng arrow function trong những trường hợp phù hợp
- **Template string**: sử dụng template string thay cho nối chuỗi
- **Destructuring**: sử dụng phép gán destructuring để đơn giản hóa code
- **Toán tử spread**: sử dụng toán tử spread để đơn giản hóa code
- **Lập trình bất đồng bộ**: sử dụng `async/await` để xử lý các thao tác bất đồng bộ

## 5. Quy chuẩn code HTML/CSS

### 5.1 Quy chuẩn HTML

- **Tên thẻ**: sử dụng chữ thường, ví dụ `<div>`
- **Tên thuộc tính**: sử dụng chữ thường, ví dụ `class="container"`
- **Giá trị thuộc tính**: đặt trong dấu nháy kép, ví dụ `class="container"`
- **Thụt lề**: thụt lề bằng 2 dấu cách
- **Lồng nhau**: giữ cấp lồng nhau của các thẻ rõ ràng
- **Chú thích**: thêm chú thích cho các cấu trúc HTML phức tạp

### 5.2 Quy chuẩn CSS

- **Selector**: sử dụng chữ thường, phân tách bằng dấu gạch ngang, ví dụ `.user-list`
- **Tên thuộc tính**: sử dụng chữ thường, ví dụ `font-size`
- **Giá trị thuộc tính**: sử dụng chữ thường, ví dụ `color: #333`
- **Thụt lề**: thụt lề bằng 2 dấu cách
- **Dấu chấm phẩy**: thêm dấu chấm phẩy sau mỗi khai báo thuộc tính
- **Dòng trống**: sử dụng dòng trống để phân tách giữa các selector khác nhau
- **Chú thích**: thêm chú thích cho các quy tắc CSS phức tạp

### 5.3 Thực tiễn tốt nhất

- **Thẻ ngữ nghĩa**: sử dụng các thẻ HTML có ngữ nghĩa
- **Bộ tiền xử lý CSS**: sử dụng các bộ tiền xử lý CSS như SCSS hoặc Less
- **Đặt tên theo BEM**: sử dụng quy tắc đặt tên BEM để tổ chức tên class CSS
- **Thiết kế responsive**: sử dụng media query để hiện thực thiết kế responsive
- **Biến CSS**: sử dụng biến CSS để quản lý các giá trị style

## 6. Quy chuẩn cơ sở dữ liệu

### 6.1 Quy tắc đặt tên

- **Tên cơ sở dữ liệu**: sử dụng chữ thường, phân tách bằng dấu gạch dưới, ví dụ `crmeb_db`
- **Tên bảng**: sử dụng chữ thường, phân tách bằng dấu gạch dưới, ví dụ `user`
- **Tên trường**: sử dụng chữ thường, phân tách bằng dấu gạch dưới, ví dụ `user_name`
- **Tên chỉ mục**: sử dụng chữ thường, phân tách bằng dấu gạch dưới, ví dụ `idx_user_name`

### 6.2 Quy chuẩn thiết kế

- **Khóa chính**: sử dụng `id` làm khóa chính, kiểu số nguyên tự tăng
- **Khóa ngoại**: sử dụng `{tên_bảng}_id` làm khóa ngoại, ví dụ `user_id`
- **Trường thời gian**: sử dụng `create_time` và `update_time` làm trường thời gian
- **Trường trạng thái**: sử dụng `status` làm trường trạng thái, giá trị mặc định là 0
- **Xóa mềm**: sử dụng `delete_time` làm trường xóa mềm

### 6.3 Thực tiễn tốt nhất

- **Thiết kế theo dạng chuẩn**: tuân theo các dạng chuẩn (normal form) trong thiết kế cơ sở dữ liệu
- **Tối ưu chỉ mục**: thêm chỉ mục cho các trường thường xuyên được truy vấn
- **Bảng phân vùng**: phân vùng (partition) các bảng lớn
- **Xử lý transaction**: sử dụng transaction để đảm bảo tính nhất quán của dữ liệu
- **Chiến lược sao lưu**: sao lưu cơ sở dữ liệu định kỳ

## 7. Review code

### 7.1 Nội dung review

- **Phong cách code**: có tuân thủ quy chuẩn code hay không
- **Chất lượng code**: có tồn tại code smell hay không
- **Bảo mật**: có tồn tại lỗ hổng bảo mật hay không
- **Hiệu năng**: có tồn tại vấn đề hiệu năng hay không
- **Chức năng**: đã hiện thực đúng chức năng theo yêu cầu hay chưa

### 7.2 Quy trình review

1. **Commit code**: lập trình viên commit code lên hệ thống quản lý phiên bản
2. **Review code**: người review tiến hành review code
3. **Phản hồi và chỉnh sửa**: người review đưa ra phản hồi, lập trình viên chỉnh sửa code
4. **Merge code**: merge code sau khi review đạt yêu cầu

### 7.3 Công cụ review

- **ESLint**: công cụ kiểm tra code JavaScript
- **PHP CodeSniffer**: công cụ kiểm tra code PHP
- **Prettier**: công cụ định dạng code
- **SonarQube**: công cụ phân tích chất lượng code

## 8. Quy chuẩn quản lý phiên bản

### 8.1 Quy chuẩn Git

- **Đặt tên nhánh**: sử dụng chữ thường, phân tách bằng dấu gạch ngang, ví dụ `feature/user-auth`
- **Nội dung commit**: mô tả bằng tiếng Việt, định dạng `[tên module] mô tả thao tác`, ví dụ `[Người dùng] Thêm chức năng đăng nhập`
- **Tần suất commit**: mỗi tính năng hoặc bản sửa bug được commit riêng
- **Xung đột code**: giải quyết xung đột code kịp thời

### 8.2 Quy chuẩn số phiên bản

Sử dụng số phiên bản ngữ nghĩa (Semantic Versioning), định dạng `MAJOR.MINOR.PATCH`, ví dụ `5.6.4`:
- **Số phiên bản chính (major)**: thay đổi API không tương thích ngược
- **Số phiên bản phụ (minor)**: bổ sung tính năng có tương thích ngược
- **Số bản vá (patch)**: sửa lỗi có tương thích ngược

## 9. Thực tiễn tốt nhất

1. **Tính dễ đọc của code**: viết code dễ hiểu
2. **Tính dễ bảo trì của code**: viết code dễ bảo trì
3. **Tính dễ kiểm thử của code**: viết code dễ kiểm thử
4. **Tính dễ mở rộng của code**: viết code dễ mở rộng
5. **Tính bảo mật của code**: viết code an toàn
6. **Hiệu năng code**: viết code hiệu năng cao

## 10. Tài liệu tham khảo

- [PSR-1: Quy chuẩn viết code cơ bản](https://www.php-fig.org/psr/psr-1/)
- [PSR-2: Quy chuẩn phong cách viết code](https://www.php-fig.org/psr/psr-2/)
- [Hướng dẫn phong cách chính thức của Vue](https://cn.vuejs.org/v2/style-guide/)
- [Phong cách chuẩn JavaScript](https://standardjs.com/)
- [Hướng dẫn phong cách HTML/CSS của Google](https://google.github.io/styleguide/htmlcssguide.html)