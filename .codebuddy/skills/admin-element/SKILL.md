---
name: Mô tả frontend trang quản trị
description: Mô tả skill phát triển frontend trang quản trị
---

# Mô tả frontend trang quản trị

## 0. Mô tả cơ chế tự động kích hoạt

### 0.1 Điều kiện kích hoạt

#### 0.1.1 Kích hoạt theo thao tác
- **Khi duyệt tệp**: Tự động được gọi khi duyệt các thư mục liên quan đến frontend trang quản trị
  - Kích hoạt khi mở thư mục `public/admin/`
  - Kích hoạt khi mở thư mục component frontend
  - Kích hoạt khi duyệt tệp mã frontend
- **Khi thao tác tệp**: Tự động được gọi khi thao tác với các tệp frontend trang quản trị
  - Kích hoạt khi tạo tệp frontend mới
  - Kích hoạt khi chỉnh sửa component frontend
  - Kích hoạt khi xóa tệp frontend
- **Khi thao tác thư mục**: Tự động được gọi khi thao tác với các thư mục frontend trang quản trị
  - Kích hoạt khi tạo thư mục frontend mới
  - Kích hoạt khi đổi tên thư mục frontend
  - Kích hoạt khi xóa thư mục frontend

#### 0.1.2 Kích hoạt theo nội dung
- **Kích hoạt theo từ khóa**: Tự động được gọi khi nội dung tệp chứa các từ khóa sau
  - Từ khóa trang quản trị: `trang quản trị`, `quản trị`, `admin`, `trang quản trị`
  - Từ khóa frontend: `frontend`, `Vue`, `component`, `trang`, `route`
  - Từ khóa chức năng: `đăng nhập`, `quyền`, `menu`, ` dashboard`, `thống kê`
- **Kích hoạt theo mã nguồn**: Tự động được gọi khi xem mã frontend thuộc loại cụ thể
  - Mã component Vue (`*.vue`)
  - Mã route frontend (`router`)
  - Mã quản lý trạng thái frontend (`store`)
  - Mã gọi API frontend (`api`)

#### 0.1.3 Kích hoạt theo lệnh
- **Kích hoạt bằng lệnh terminal**: Tự động được gọi khi thực thi các lệnh sau
  - `npm run dev` (khởi động máy chủ phát triển)
  - `npm run build` (build dự án frontend)
  - `npm run lint` (kiểm tra mã)
  - `vue` và các lệnh liên quan

### 0.2 Tình huống áp dụng

#### 0.2.1 Tình huống cốt lõi
- **Phát triển trang quản trị**: Khi phát triển chức năng frontend của trang quản trị
- **Phát triển component**: Khi tạo hoặc chỉnh sửa component của trang quản trị
- **Phát triển trang**: Khi phát triển các trang quản trị
- **Tích hợp chức năng**: Khi tích hợp chức năng mới vào trang quản trị

#### 0.2.2 Tình huống hỗ trợ
- **Gỡ lỗi frontend**: Khi gỡ lỗi các vấn đề frontend của trang quản trị
- **Tối ưu hiệu năng**: Khi tối ưu hiệu năng frontend của trang quản trị
- **Điều chỉnh giao diện**: Khi điều chỉnh style giao diện của trang quản trị
- **Cấu hình quyền**: Khi cấu hình quyền cho trang quản trị

### 0.3 Cơ chế kích hoạt

#### 0.3.1 Thời điểm gọi
- **Kích hoạt tức thì**: Kích hoạt ngay khi thao tác tệp frontend
- **Kích hoạt trễ**: Kích hoạt trễ 1 giây khi build frontend phức tạp
- **Kích hoạt hàng loạt**: Gộp thành một lần kích hoạt khi thao tác hàng loạt tệp frontend

#### 0.3.2 Tần suất gọi
- Duyệt tệp: kích hoạt tối đa một lần mỗi 10 giây
- Thao tác tệp: kích hoạt tối đa một lần mỗi 5 giây
- Thực thi lệnh: kích hoạt tối đa một lần mỗi 3 giây

#### 0.3.3 Mức ưu tiên gọi
- **Mức ưu tiên**: Ưu tiên trung bình (3/5)
- **Xử lý tranh chấp**: Khi nhiều skill được kích hoạt cùng lúc
  - Ưu tiên cao nhất: skill cốt lõi của hệ thống
  - Ưu tiên cao: skill cấu trúc mã nguồn
  - Ưu tiên trung bình: skill frontend trang quản trị, skill quy chuẩn tài liệu
  - Ưu tiên thấp: skill công cụ hỗ trợ
- **Giới hạn kích hoạt**: Chỉ được kích hoạt khi có thao tác liên quan đến frontend trang quản trị, không ảnh hưởng đến việc sử dụng bình thường của các skill khác

### 0.4 Hành vi sau khi kích hoạt

#### 0.4.1 Tự động phân tích
- **Phân tích cấu trúc**: Phân tích cấu trúc thư mục frontend trang quản trị
- **Phân tích component**: Phân tích quan hệ phụ thuộc giữa các component frontend
- **Phân tích route**: Phân tích cấu hình route frontend
- **Phân tích hiệu năng**: Phân tích điểm nghẽn hiệu năng frontend

#### 0.4.2 Tự động hiển thị
- **Cấu trúc thư mục**: Hiển thị cấu trúc thư mục frontend trang quản trị
- **Mô tả component**: Hiển thị mô tả chức năng của các component cốt lõi
- **Bộ công nghệ**: Hiển thị bộ công nghệ (tech stack) của frontend trang quản trị
- **Quy chuẩn phát triển**: Hiển thị quy chuẩn phát triển frontend

#### 0.4.3 Tự động đề xuất
- **Đề xuất phát triển**: Đưa ra đề xuất phát triển frontend trang quản trị
- **Đề xuất tối ưu**: Đưa ra đề xuất tối ưu hiệu năng frontend
- **Đề xuất quy chuẩn**: Đưa ra đề xuất về việc tuân thủ quy chuẩn mã nguồn
- **Đề xuất bảo mật**: Đưa ra đề xuất bảo mật cho frontend

## 1. Kiến trúc frontend trang quản trị

### 1.1 Kiến trúc tổng thể
- **Ứng dụng một trang (SPA)**: Ứng dụng một trang xây dựng trên Vue 2.x
- **Tách biệt frontend và backend**: Giao tiếp với backend qua RESTful API
- **Thiết kế mô-đun hóa**: Chức năng được mô-đun hóa, dễ mở rộng
- **Bố cục responsive**: Thích ứng với các kích thước màn hình khác nhau

### 1.2 Bộ công nghệ
- **Framework cốt lõi**: Vue 2.x
- **Thư viện UI component**: Element UI
- **Quản lý trạng thái**: Vuex
- **Quản lý route**: Vue Router
- **HTTP client**: Axios
- **Công cụ build**: Webpack
- **Trình quản lý gói**: npm/yarn
- **Quy chuẩn mã nguồn**: ESLint + Prettier

### 1.3 Cấu trúc thư mục

#### 1.3.1 Thư mục cốt lõi
```
public/admin/
├── index.html           # File HTML điểm vào
├── static/              # Tài nguyên tĩnh
│   ├── css/             # File style
│   ├── js/              # JavaScript Tệp
│   └── img/             # Tài nguyên hình ảnh
├── favicon.ico          # Biểu tượng website
└── manifest.json        # PWA Tệp cấu hình
```

#### 1.3.2 Thư mục mã nguồn (src)
```
src/
├── api/                 # API định nghĩa
├── assets/              # Tài nguyên tĩnh
├── components/          # Thành phần (component) dùng chung
├── layout/              # Thành phần layout
├── pages/               # Thành phần trang
├── router/              # Cấu hình route
├── store/               # Quản lý trạng thái
├── utils/               # Hàm tiện ích (utility)
├── directive/           # Directive tùy chỉnh
├── filters/             # Filter tùy chỉnh
├── mixins/              # Mixin
├── App.vue              # Thành phần gốc
└── main.js              # File điểm vào
```

## 2. Các mô-đun cốt lõi

### 2.1 Mô-đun đăng nhập và xác thực
- **Chức năng**: Đăng nhập người dùng, xác thực quyền, quản lý phiên (session)
- **Component chính**: `Login.vue`, `Auth.vue`
- **Chức năng cốt lõi**: 
  - Đăng nhập bằng tên đăng nhập và mật khẩu
  - Kiểm tra mã xác thực (captcha)
  - Ghi nhớ mật khẩu
  - Quản lý trạng thái đăng nhập
  - Quản lý token phân quyền

### 2.2 Mô-đun menu điều hướng
- **Chức năng**: Hiển thị menu trang quản trị, kiểm soát quyền, điều hướng route
- **Component chính**: `Sidebar.vue`, `Topbar.vue`
- **Chức năng cốt lõi**: 
  - Sinh menu động
  - Kiểm soát quyền menu
  - Điều hướng breadcrumb
  - Quản lý tab
  - Thu gọn/mở rộng menu

### 2.3 Module bảng điều khiển
- **Chức năng**: Tổng quan hệ thống, thống kê dữ liệu, hiển thị các chỉ số chính
- **Component chính**: `Dashboard.vue`, `Statistics.vue`
- **Chức năng cốt lõi**: 
  - Thống kê dữ liệu bán hàng
  - Thống kê dữ liệu đơn hàng
  - Thống kê dữ liệu người dùng
  - Thống kê dữ liệu sản phẩm
  - Giám sát trạng thái hệ thống

### 2.4 Module quản lý người dùng
- **Chức năng**: Quản lý tài khoản quản trị viên, phân quyền, quản lý vai trò
- **Component chính**: `UserList.vue`, `RoleList.vue`, `PermissionList.vue`
- **Chức năng cốt lõi**: 
  - Thêm/xóa/sửa/tra cứu người dùng
  - Quản lý vai trò
  - Phân quyền
  - Đặt lại mật khẩu
  - Xem nhật ký đăng nhập

### 2.5 Module cài đặt hệ thống
- **Chức năng**: Cấu hình hệ thống, cài đặt tham số, sao lưu dữ liệu
- **Component chính**: `SystemConfig.vue`, `Backup.vue`
- **Chức năng cốt lõi**: 
  - Cài đặt cơ bản của hệ thống
  - Cấu hình thanh toán
  - Cấu hình email
  - Cấu hình SMS
  - Sao lưu/khôi phục dữ liệu

## 3. Đặc điểm kỹ thuật

### 3.1 Phát triển theo hướng component
- **Component tái sử dụng**: Đóng gói các component UI thường dùng
- **Giao tiếp giữa các component**: Props/Events, Vuex, EventBus
- **Vòng đời component**: Sử dụng hợp lý các lifecycle hook
- **Quy tắc đặt tên component**: Đặt tên theo PascalCase

### 3.2 Quản lý trạng thái
- **Module hóa Vuex**: Chia store theo module chức năng
- **Lưu trạng thái bền vững (persistence)**: Sử dụng localStorage/sessionStorage
- **Thao tác bất đồng bộ**: Dùng Actions để xử lý request bất đồng bộ
- **getters**: Tính toán trạng thái dẫn xuất

### 3.3 Quản lý định tuyến
- **Route động**: Tạo route dựa trên quyền
- **Route guard**: Route guard toàn cục/cục bộ
- **Lazy load route**: Tăng tốc độ tải màn hình đầu tiên
- **Route lồng nhau**: Xây dựng cấu trúc trang phức tạp

### 3.4 Gọi API
- **Đóng gói Axios**: Thống nhất cách gọi API
- **Request interceptor**: Thêm token xác thực
- **Response interceptor**: Xử lý lỗi thống nhất
- **Module hóa API**: Chia API theo module chức năng

## 4. Quy chuẩn phát triển

### 4.1 Quy chuẩn code
- **Hướng dẫn phong cách Vue**: Tuân thủ hướng dẫn phong cách (Style Guide) chính thức của Vue
- **Quy tắc ESLint**: Tuân thủ nghiêm ngặt các quy tắc ESLint
- **Thụt lề code**: Thụt lề 4 dấu cách
- **Quy tắc đặt tên**: 
  - Tên component: PascalCase
  - Phương thức/biến: camelCase
  - Hằng số: Viết hoa toàn bộ
  - Tên file: kebab-case hoặc PascalCase

### 4.2 Quy chuẩn thư mục
- **Thư mục component**: Tổ chức component theo module chức năng
- **Thư mục trang**: Tổ chức trang theo cấu trúc route
- **Thư mục tài nguyên**: Lưu tài nguyên tĩnh theo từng loại
- **Thư mục tiện ích**: Phân loại hàm tiện ích theo chức năng

### 4.3 Quy tắc đặt tên
- **Đặt tên component**: Dùng tên component có ngữ nghĩa rõ ràng
- **Đặt tên route**: Tương ứng với tên component của trang
- **Đặt tên store**: Tương ứng với tên module chức năng
- **Đặt tên API**: Tương ứng với tên API phía backend

### 4.4 Quy chuẩn chú thích
- **Chú thích component**: Mô tả chức năng, props, events của component
- **Chú thích phương thức**: Mô tả chức năng, tham số, giá trị trả về của phương thức
- **Chú thích logic phức tạp**: Giải thích các bước logic quan trọng
- **Chú thích TODO**: Đánh dấu chức năng chưa hoàn thành

## 5. Tối ưu hiệu năng

### 5.1 Tối ưu tốc độ tải
- **Lazy load route**: Tải component trang theo nhu cầu
- **Lazy load component**: Tải các component lớn theo nhu cầu
- **Tối ưu hình ảnh**: Nén ảnh, lazy load
- **Nén tài nguyên**: Nén JS/CSS
- **Tăng tốc bằng CDN**: Dùng CDN cho tài nguyên tĩnh

### 5.2 Tối ưu render
- **Danh sách ảo**: Dùng cuộn ảo (virtual scrolling) cho danh sách dài
- **Cache thuộc tính computed**: Dùng computed để cache kết quả tính toán
- **Tránh cập nhật quá thường xuyên**: Sử dụng v-once, v-memo
- **Sử dụng key hợp lý**: Dùng key duy nhất khi render danh sách
- **Giảm thao tác DOM**: Cập nhật DOM theo lô

### 5.3 Tối ưu mạng
- **Gộp request API**: Gộp các request giống nhau
- **Cache request**: Cache kết quả của các request lặp lại
- **Debounce và throttle**: Tối ưu các sự kiện được kích hoạt thường xuyên
- **WebSocket**: Dùng WebSocket cho dữ liệu thời gian thực
- **HTTP/2**: Bật giao thức HTTP/2

### 5.4 Tối ưu build
- **Tree Shaking**: Loại bỏ code không sử dụng
- **Tách code (code splitting)**: Tách code theo route
- **Tải trước (preload)**: Tải trước các tài nguyên quan trọng
- **Biến môi trường**: Phân biệt môi trường phát triển/production

## 6. Bảo mật

### 6.1 Phòng chống XSS
- **Kiểm tra đầu vào**: Kiểm tra dữ liệu người dùng nhập
- **Mã hóa đầu ra**: Mã hóa HTML (HTML encoding) cho dữ liệu đầu ra
- **CSP**: Cấu hình chính sách bảo mật nội dung (Content Security Policy)
- **Tự động escape của Vue**: Tận dụng tính năng escape HTML của Vue

### 6.2 Phòng chống CSRF
- **Xác thực Token**: Sử dụng CSRF Token
- **Chính sách cùng nguồn gốc**: Tuân thủ chính sách cùng nguồn gốc (same-origin policy) của trình duyệt
- **Kiểm tra header của request**: Xác minh nguồn gốc request

### 6.3 Kiểm soát quyền
- **Kiểm tra quyền ở frontend**: Kiểm soát quyền truy cập route
- **Kiểm tra quyền ở backend**: Kiểm tra quyền truy cập API
- **Phân quyền cấp nút bấm**: Kiểm soát quyền chi tiết
- **Xác minh thao tác nhạy cảm**: Yêu cầu xác nhận lại với các thao tác quan trọng

### 6.4 An toàn dữ liệu
- **Mã hóa dữ liệu nhạy cảm**: Mã hóa thông tin nhạy cảm khi lưu trữ
- **An toàn lưu trữ cục bộ**: Sử dụng localStorage hợp lý
- **An toàn API**: HTTPS, chữ ký API

## 7. Thực tiễn tốt nhất

### 7.1 Quy trình phát triển
1. **Phân tích yêu cầu**: Làm rõ yêu cầu chức năng
2. **Giai đoạn thiết kế**: Thiết kế cấu trúc trang và component
3. **Lập trình**: Viết code theo quy chuẩn phát triển
4. **Kiểm thử**: Kiểm thử chức năng và kiểm thử hiệu năng
5. **Rà soát code (code review)**: Kiểm tra chất lượng code
6. **Triển khai và phát hành**: Build và triển khai

### 7.2 Phát triển component
- **Trách nhiệm đơn nhất**: Mỗi component chỉ đảm nhận một chức năng
- **Khả năng cấu hình**: Tham số của component có thể cấu hình
- **Tài liệu đầy đủ**: Có tài liệu hướng dẫn sử dụng component
- **Độ phủ kiểm thử**: Unit test cho component

### 7.3 Phát triển trang
- **Quy chuẩn bố cục**: Tuân thủ quy chuẩn bố cục thống nhất
- **Thiết kế responsive**: Tương thích với các kích thước màn hình khác nhau
- **Trải nghiệm người dùng**: Tối ưu trải nghiệm tương tác
- **Cân nhắc hiệu năng**: Hiệu năng tải và render trang

### 7.4 Mẹo gỡ lỗi
- **Vue DevTools**: Sử dụng công cụ phát triển của Vue
- **Gỡ lỗi bằng console**: Sử dụng hợp lý các phương thức console
- **Gỡ lỗi mạng**: Phân tích request và response của API
- **Phân tích hiệu năng**: Sử dụng công cụ phân tích hiệu năng của trình duyệt

## 8. Sự cố thường gặp

### 8.1 Vấn đề đăng nhập
- **Token hết hạn**: Xử lý logic khi token hết hạn
- **Không đủ quyền**: Xử lý khi kiểm tra quyền thất bại
- **Duy trì trạng thái đăng nhập**: Lưu bền vững trạng thái đăng nhập

### 8.2 Vấn đề định tuyến
- **Trang 404**: Cấu hình route 404
- **Chuyển hướng route**: Cấu hình chuyển hướng route hợp lý
- **Quyền truy cập route**: Kiểm soát quyền cho route động

### 8.3 Vấn đề hiệu năng
- **Màn hình đầu tiên tải chậm**: Tối ưu tốc độ tải màn hình đầu tiên
- **Trang bị giật lag**: Tối ưu hiệu năng render trang
- **Rò rỉ bộ nhớ**: Tránh rò rỉ bộ nhớ

### 8.4 Vấn đề tương thích
- **Tương thích trình duyệt**: Hỗ trợ các trình duyệt phổ biến
- **Tương thích độ phân giải**: Thích ứng với các độ phân giải khác nhau
- **Tương thích thiết bị**: Thích ứng với các thiết bị khác nhau

## 9. Triển khai và phát hành

### 9.1 Quy trình build
- **Môi trường phát triển**: `npm run dev`
- **Môi trường kiểm thử**: `npm run build:test`
- **Môi trường production**: `npm run build:prod`

### 9.2 Phương thức triển khai
- **Triển khai tĩnh**: Triển khai lên máy chủ file tĩnh
- **Triển khai bằng container**: Triển khai bằng container Docker
- **Tăng tốc bằng CDN**: Dùng CDN cho tài nguyên tĩnh

### 9.3 Quy trình phát hành
1. **Commit code**: Commit code lên hệ thống quản lý phiên bản
2. **Build và kiểm thử**: Build rồi chạy kiểm thử
3. **Triển khai và phát hành**: Triển khai lên môi trường production
4. **Giám sát vận hành**: Giám sát trạng thái vận hành của hệ thống

## 10. Công cụ phát triển khuyên dùng

### 10.1 IDE khuyên dùng
- **VS Code**: Trình soạn thảo nhẹ, kho plugin phong phú
- **WebStorm**: IDE frontend chuyên nghiệp
- **Sublime Text**: Trình soạn thảo code tốc độ cao

### 10.2 Plugin khuyên dùng
- **Vetur**: Plugin phát triển Vue
- **ESLint**: Plugin kiểm tra code
- **Prettier**: Plugin định dạng code
- **GitLens**: Plugin mở rộng tính năng Git
- **Debugger for Chrome**: Plugin gỡ lỗi trên trình duyệt

### 10.3 Công cụ khuyên dùng
- **Postman**: Công cụ kiểm thử API
- **Charles**: Công cụ gỡ lỗi mạng
- **Figma/Sketch**: Công cụ thiết kế UI
- **Zeplin**: Công cụ cộng tác thiết kế
- **Lighthouse**: Công cụ phân tích hiệu năng

## 11. Tài liệu tham khảo

### 11.1 Tài liệu chính thức
- [Tài liệu chính thức Vue 2](https://v2.vuejs.org/)
- [Tài liệu chính thức Element UI](https://element.eleme.io/#/zh-CN)
- [Tài liệu chính thức Vuex](https://vuex.vuejs.org/zh/)
- [Tài liệu chính thức Vue Router](https://router.vuejs.org/zh/)
- [Tài liệu chính thức Axios](https://axios-http.com/zh/docs/intro)

### 11.2 Tài nguyên học tập
- [Vue Mastery](https://www.vuemastery.com/)
- [Vue School](https://vueschool.io/)
- [MDN Web Docs](https://developer.mozilla.org/zh-CN/)
- [CSS-Tricks](https://css-tricks.com/)

### 11.3 Tài nguyên cộng đồng
- [Diễn đàn Vue](https://forum.vuejs.org/)
- [Cộng đồng Element UI](https://github.com/ElemeFE/element)
- [GitHub](https://github.com/)
- [Stack Overflow](https://stackoverflow.com/)

## 12. Quản lý phiên bản

### 12.1 Quản lý phiên bản
- **Git**: Dùng Git để quản lý phiên bản
- **Quản lý nhánh**: Nhánh chính, nhánh phát triển, nhánh tính năng
- **Quy chuẩn commit**: Commit message có ngữ nghĩa rõ ràng

### 12.2 Phát hành phiên bản
- **Đánh số phiên bản theo ngữ nghĩa**: Tuân thủ quy chuẩn SemVer
- **Nhật ký phát hành**: Ghi lại nội dung thay đổi của từng phiên bản
- **Phương án rollback**: Chiến lược rollback phiên bản

## 13. Cộng tác nhóm

### 13.1 Quy trình cộng tác
- **Đánh giá yêu cầu**: Cả nhóm cùng đánh giá yêu cầu
- **Phân công công việc**: Phân công nhiệm vụ phát triển hợp lý
- **Rà soát code (code review)**: Kiểm tra chất lượng code
- **Phối hợp kiểm thử**: Phối hợp giữa bộ phận phát triển và kiểm thử

### 13.2 Quy ước chung
- **Quy chuẩn code**: Thống nhất phong cách code
- **Quy tắc đặt tên**: Thống nhất quy tắc đặt tên
- **Quy chuẩn tài liệu**: Thống nhất định dạng tài liệu
- **Quy chuẩn commit**: Thống nhất định dạng commit message

### 13.3 Công cụ cộng tác
- **Quản lý dự án**: Sử dụng công cụ quản lý dự án
- **Lưu trữ mã nguồn**: Sử dụng nền tảng lưu trữ mã nguồn
- **CI/CD**: Tích hợp liên tục/triển khai liên tục
- **Cộng tác tài liệu**: Công cụ cộng tác tài liệu cho nhóm

### 14 Tài nguyên khác
- Tài liệu quy trình phát triển /references/development_flow.md
- Tài liệu quy chuẩn code /references/code_style.md
- Tài liệu triển khai dự án /references/deploy.md
- Tài liệu cấu trúc thư mục /references/directory_structure.md
- Tài liệu quy trình request API /references/api_flow.md
- Tài liệu cấu hình hệ thống /references/system_config.md
