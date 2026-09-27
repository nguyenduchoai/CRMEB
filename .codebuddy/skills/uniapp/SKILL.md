---
name: Hướng dẫn uniapp
description: Hướng dẫn phát triển uniapp cho di động
---

# Hướng dẫn uniapp

## 0. Mô tả cơ chế tự động kích hoạt

### 0.1 Điều kiện kích hoạt

#### 0.1.1 Kích hoạt theo thao tác
- **Khi duyệt file**: Tự động gọi khi duyệt các thư mục liên quan đến uniapp
  - Kích hoạt khi mở thư mục gốc của dự án uniapp
  - Kích hoạt khi mở thư mục pages
  - Kích hoạt khi duyệt thư mục components
  - Kích hoạt khi xem file cấu hình uniapp
- **Khi thao tác file**: Tự động gọi khi thao tác trên file uniapp
  - Kích hoạt khi tạo file trang mới
  - Kích hoạt khi sửa file component
  - Kích hoạt khi xóa file uniapp
  - Kích hoạt khi sửa file cấu hình
- **Khi thao tác thư mục**: Tự động gọi khi thao tác trên thư mục uniapp
  - Kích hoạt khi tạo thư mục trang mới
  - Kích hoạt khi đổi tên thư mục component
  - Kích hoạt khi xóa thư mục uniapp

#### 0.1.2 Kích hoạt theo nội dung
- **Kích hoạt theo từ khóa**: Tự động được gọi khi nội dung tệp chứa các từ khóa sau
  - Từ khóa về di động: `di động`, `app`, `điện thoại`, `Mini Program`, `H5`
  - Từ khóa về uniapp: `uniapp`, `uni-app`, `uView`, `uniCloud`
  - Từ khóa về chức năng: `đăng nhập`, `đăng ký`, `trang chủ`, `sản phẩm`, `giỏ hàng`, `đơn hàng`
- **Kích hoạt theo code**: Tự động gọi khi xem code uniapp thuộc các loại cụ thể
  - Code trang (`*.vue`)
  - Code component (`components`)
  - Code cấu hình (`pages.json`, `manifest.json`)
  - Code gọi API (các API bắt đầu bằng `uni.`)

#### 0.1.3 Kích hoạt theo lệnh
- **Kích hoạt bằng lệnh terminal**: Tự động được gọi khi thực thi các lệnh sau
  - `npm run dev:h5` (khởi động chế độ phát triển H5)
  - `npm run dev:mp-weixin` (khởi động chế độ phát triển WeChat Mini Program)
  - `npm run build:h5` (build H5)
  - `npm run build:mp-weixin` (build WeChat Mini Program)
  - `uni` và các lệnh liên quan

### 0.2 Tình huống áp dụng

#### 0.2.1 Tình huống cốt lõi
- **Phát triển cho di động**: Khi phát triển ứng dụng di động bằng uniapp
- **Phát triển trang**: Khi tạo hoặc sửa trang uniapp
- **Phát triển component**: Khi phát triển component uniapp có thể tái sử dụng
- **Tích hợp chức năng**: Khi tích hợp chức năng mới vào ứng dụng di động

#### 0.2.2 Tình huống hỗ trợ
- **Gỡ lỗi đa nền tảng**: Khi gỡ lỗi các vấn đề tương thích trên các nền tảng khác nhau
- **Tối ưu hiệu năng**: Khi tối ưu hiệu năng ứng dụng di động
- **Điều chỉnh style**: Khi điều chỉnh style giao diện trên di động
- **Gọi API**: Khi sử dụng API gốc (native) của uniapp

### 0.3 Cơ chế kích hoạt

#### 0.3.1 Thời điểm gọi
- **Kích hoạt tức thời**: Kích hoạt ngay khi thao tác trên file trang
- **Kích hoạt trễ**: Khi sửa cấu hình phức tạp, trì hoãn 1 giây rồi mới kích hoạt
- **Kích hoạt hàng loạt**: Gộp thành một lần kích hoạt khi thao tác file hàng loạt

#### 0.3.2 Tần suất gọi
- Duyệt tệp: kích hoạt tối đa một lần mỗi 10 giây
- Thao tác tệp: kích hoạt tối đa một lần mỗi 5 giây
- Thực thi lệnh: kích hoạt tối đa một lần mỗi 3 giây

#### 0.3.3 Mức ưu tiên gọi
- **Mức ưu tiên**: Ưu tiên trung bình (3/5)
- **Xử lý tranh chấp**: Khi nhiều skill được kích hoạt cùng lúc
  - Ưu tiên cao nhất: skill cốt lõi của hệ thống
  - Ưu tiên cao: skill cấu trúc mã nguồn
  - Ưu tiên trung bình: skill uniapp, skill front-end trang quản trị
  - Ưu tiên thấp: skill công cụ hỗ trợ
- **Giới hạn kích hoạt**: Chỉ được kích hoạt khi có thao tác liên quan đến uniapp, không ảnh hưởng đến việc sử dụng bình thường của các skill khác

### 0.4 Hành vi sau khi kích hoạt

#### 0.4.1 Tự động phân tích
- **Phân tích cấu trúc**: Phân tích cấu trúc thư mục dự án uniapp
- **Phân tích component**: Phân tích quan hệ phụ thuộc giữa các component của trang
- **Phân tích cấu hình**: Phân tích file cấu hình uniapp
- **Phân tích hiệu năng**: Phân tích điểm nghẽn hiệu năng trên di động

#### 0.4.2 Tự động hiển thị
- **Cấu trúc thư mục**: Trình bày cấu trúc thư mục dự án uniapp
- **Mô tả trang**: Trình bày mô tả chức năng của các trang cốt lõi
- **Bộ công nghệ**: Trình bày bộ công nghệ của uniapp
- **Quy chuẩn phát triển**: Trình bày quy chuẩn phát triển cho di động

#### 0.4.3 Tự động đề xuất
- **Gợi ý phát triển**: Đưa ra gợi ý phát triển uniapp
- **Gợi ý tối ưu**: Đưa ra gợi ý tối ưu hiệu năng cho di động
- **Đề xuất quy chuẩn**: Đưa ra đề xuất về việc tuân thủ quy chuẩn mã nguồn
- **Gợi ý đa nền tảng**: Đưa ra gợi ý về khả năng tương thích đa nền tảng

## 1. Giới thiệu kiến trúc uniapp

### 1.1 Kiến trúc tổng thể
- **Framework đa nền tảng**: Framework ứng dụng đa nền tảng dựa trên Vue 2.x
- **Phát hành đa nền tảng**: Hỗ trợ phát hành lên nhiều nền tảng như H5, Mini Program, App
- **Phát triển theo component**: Dựa trên mô hình phát triển theo component của Vue
- **Khả năng native**: Gọi khả năng API gốc (native) của từng nền tảng
- **Quản lý trạng thái**: Quản lý trạng thái bằng Vuex hoặc Pinia

### 1.2 Bộ công nghệ
- **Framework cốt lõi**: Vue 2.x / Vue 3.x
- **Thư viện UI component**: uView UI, ColorUI, uni-ui
- **Quản lý trạng thái**: Vuex 3.x / Pinia
- **HTTP client**: uni.request, axios
- **Công cụ build**: Webpack, Vite
- **Trình quản lý gói**: npm/yarn
- **Quy chuẩn mã nguồn**: ESLint + Prettier

### 1.3 Cấu trúc thư mục

#### 1.3.1 Thư mục cốt lõi
```
uniapp/
├── pages/               # Thư mục trang
├── components/          # Thư mục thành phần
├── static/              # Thư mục tài nguyên tĩnh
├── utils/               # Thư mục hàm tiện ích
├── api/                 # API thư mục
├── store/               # Thư mục quản lý trạng thái
├── common/              # Thư mục dùng chung
├── mixins/              # Thư mục mixin
├── pages.json           # Cấu hình route trang
├── manifest.json        # Cấu hình ứng dụng
├── main.js              # File điểm vào
├── App.vue              # Thành phần gốc
└── uni.scss             # Style toàn cục
```

#### 1.3.2 Thư mục trang
```
pages/
├── index/               # Trang chủ
├── goods/               # Sản phẩm
├── cart/                # Giỏ hàng
├── order/               # Đơn hàng
├── user/                # Trung tâm người dùng
├── login/               # Đăng nhập
└── detail/              # Trang chi tiết
```

## 2. Các mô-đun chức năng cốt lõi

### 2.1 Mô-đun đăng nhập và xác thực
- **Chức năng**: Đăng nhập, đăng ký, khôi phục mật khẩu, đăng nhập qua bên thứ ba
- **Trang chủ chốt**: `login.vue`, `register.vue`
- **Chức năng cốt lõi**: 
  - Đăng nhập bằng số điện thoại và mật khẩu
  - Đăng nhập bằng mã xác thực (OTP)
  - Đăng nhập bằng WeChat
  - Chức năng đăng ký
  - Khôi phục mật khẩu
  - Quản lý trạng thái đăng nhập

### 2.2 Mô-đun trang chủ
- **Chức năng**: Lối vào ứng dụng, ảnh trình chiếu (banner), sản phẩm đề xuất, lối vào danh mục
- **Trang chủ chốt**: `index.vue`
- **Chức năng cốt lõi**: 
  - Hiển thị ảnh trình chiếu (banner)
  - Điều hướng danh mục
  - Sản phẩm đề xuất
  - Lối vào chương trình khuyến mãi
  - Chức năng tìm kiếm
  - Lối tắt

### 2.3 Mô-đun sản phẩm
- **Chức năng**: Danh sách sản phẩm, tìm kiếm sản phẩm, lọc sản phẩm, chi tiết sản phẩm
- **Trang chủ chốt**: `goods/list.vue`, `goods/detail.vue`
- **Chức năng cốt lõi**: 
  - Hiển thị danh sách sản phẩm
  - Tìm kiếm sản phẩm
  - Lọc sản phẩm
  - Chi tiết sản phẩm
  - Đánh giá sản phẩm
  - Chia sẻ sản phẩm

### 2.4 Mô-đun giỏ hàng
- **Chức năng**: Quản lý sản phẩm, điều chỉnh số lượng, tính giá, thanh toán
- **Trang chủ chốt**: `cart/index.vue`
- **Chức năng cốt lõi**: 
  - Thêm/xóa sản phẩm
  - Điều chỉnh số lượng
  - Tính giá
  - Thanh toán sản phẩm
  - Xóa toàn bộ giỏ hàng
  - Chọn sản phẩm

### 2.5 Mô-đun đơn hàng
- **Chức năng**: Tạo đơn hàng, danh sách đơn hàng, chi tiết đơn hàng, thanh toán đơn hàng
- **Trang chủ chốt**: `order/create.vue`, `order/list.vue`, `order/detail.vue`
- **Chức năng cốt lõi**: 
  - Tạo đơn hàng
  - Danh sách đơn hàng
  - Chi tiết đơn hàng
  - Thanh toán đơn hàng
  - Hủy đơn hàng
  - Hoàn tiền đơn hàng

### 2.6 Mô-đun trang cá nhân
- **Chức năng**: Thông tin người dùng, địa chỉ nhận hàng, phiếu giảm giá (coupon), yêu thích, lịch sử
- **Trang chủ chốt**: `user/index.vue`, `user/info.vue`
- **Chức năng cốt lõi**: 
  - Quản lý thông tin người dùng
  - Quản lý địa chỉ nhận hàng
  - Quản lý phiếu giảm giá
  - Sản phẩm yêu thích
  - Lịch sử xem
  - Cài đặt cá nhân

## 3. Đặc điểm kỹ thuật

### 3.1 Phát triển đa nền tảng
- **Phát triển một lần, phát hành đa nền tảng**: Hỗ trợ nhiều nền tảng như H5, Mini Program, App
- **Biên dịch có điều kiện**: Viết code riêng theo từng nền tảng
- **Xử lý khác biệt nền tảng**: Xử lý khác biệt API giữa các nền tảng
- **Gọi khả năng native**: Gọi các khả năng gốc (native) của từng nền tảng

### 3.2 Phát triển theo component
- **Component trang**: Phát triển component cấp trang
- **Component dùng chung**: Các component dùng chung có thể tái sử dụng
- **Component nghiệp vụ**: Component cho logic nghiệp vụ cụ thể
- **Giao tiếp giữa các component**: Props/Events, Vuex, EventBus

### 3.3 Quản lý trạng thái
- **Module hóa Vuex**: Chia store theo module chức năng
- **Lưu trạng thái bền vững (persistence)**: Sử dụng localStorage/sessionStorage
- **Thao tác bất đồng bộ**: Dùng Actions để xử lý request bất đồng bộ
- **getters**: Tính toán trạng thái dẫn xuất

### 3.4 Gọi API
- **uni.request**: Phương thức request tích hợp sẵn của uniapp
- **Đóng gói request**: Thống nhất cách gọi API
- **Interceptor**: Interceptor cho request/response
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
  - Tên file: kebab-case

### 4.2 Quy chuẩn thư mục
- **Thư mục trang**: Tổ chức trang theo mô-đun chức năng
- **Thư mục component**: Tổ chức component theo loại
- **Thư mục tài nguyên**: Lưu tài nguyên tĩnh theo từng loại
- **Thư mục tiện ích**: Phân loại hàm tiện ích theo chức năng

### 4.3 Quy tắc đặt tên
- **Đặt tên trang**: Dùng tên trang có ngữ nghĩa rõ ràng
- **Đặt tên component**: Tương ứng với tên mô-đun chức năng
- **Đặt tên store**: Tương ứng với tên module chức năng
- **Đặt tên API**: Tương ứng với tên API phía backend

### 4.4 Quy chuẩn chú thích
- **Chú thích trang**: Mô tả chức năng trang, tham số
- **Chú thích component**: Mô tả chức năng, props, events của component
- **Chú thích phương thức**: Mô tả chức năng, tham số, giá trị trả về của phương thức
- **Chú thích logic phức tạp**: Giải thích các bước logic quan trọng

## 5. Tối ưu hiệu năng

### 5.1 Tối ưu tốc độ tải
- **Lazy load route**: Tải component trang theo nhu cầu
- **Lazy load component**: Tải các component lớn theo nhu cầu
- **Tối ưu hình ảnh**: Nén ảnh, lazy load, định dạng WebP
- **Nén tài nguyên**: Nén JS/CSS
- **Tải trước (preload)**: Tải trước các tài nguyên quan trọng

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
- **Biến môi trường**: Phân biệt môi trường phát triển/production
- **Tải theo gói con**: Tải gói con (subpackage) cho Mini Program

## 6. Tương thích đa nền tảng

### 6.1 Khác biệt giữa các nền tảng
- **Phía H5**: Khả năng tương thích trình duyệt
- **Phía Mini Program**: Các giới hạn Mini Program của từng nền tảng
- **Phía App**: Khác biệt giữa iOS/Android

### 6.2 Chiến lược tương thích
- **Biên dịch có điều kiện**: Dùng `#ifdef`/`#endif` để xử lý khác biệt giữa các nền tảng
- **Tương thích API**: Đóng gói cách gọi API thống nhất
- **Tương thích style**: Xử lý khác biệt style giữa các nền tảng
- **Giảm cấp chức năng**: Cung cấp phương án giảm cấp (fallback) cho các chức năng không được hỗ trợ

### 6.3 Chiến lược kiểm thử
- **Kiểm thử đa nền tảng**: Tiến hành kiểm thử trên từng nền tảng
- **Kiểm thử tương thích**: Kiểm thử khả năng tương thích với các phiên bản khác nhau
- **Kiểm thử hiệu năng**: Kiểm thử hiệu năng trên từng nền tảng
- **Kiểm thử trải nghiệm người dùng**: Kiểm thử trải nghiệm người dùng trên từng nền tảng

## 7. Bảo mật

### 7.1 An toàn dữ liệu
- **Mã hóa dữ liệu nhạy cảm**: Mã hóa thông tin nhạy cảm khi lưu trữ
- **An toàn lưu trữ cục bộ**: Sử dụng localStorage hợp lý
- **An toàn API**: HTTPS, chữ ký API
- **Kiểm tra dữ liệu**: Kiểm tra dữ liệu ở front-end

### 7.2 Quản lý quyền
- **Xin quyền**: Xin quyền thiết bị một cách hợp lý
- **Thông báo quyền**: Giải thích cho người dùng mục đích sử dụng quyền
- **Giảm cấp theo quyền**: Cung cấp phương án giảm cấp (fallback) khi không có quyền

### 7.3 Biện pháp chống gian lận
- **Giới hạn tần suất request**: Ngăn chặn request độc hại
- **Kiểm tra tham số**: Kiểm tra tham số của request
- **Xác minh chữ ký**: Xác minh chữ ký của request
- **Định danh thiết bị**: Sử dụng định danh thiết bị một cách hợp lý

## 8. Thực tiễn tốt nhất

### 8.1 Quy trình phát triển
1. **Phân tích yêu cầu**: Làm rõ yêu cầu chức năng
2. **Giai đoạn thiết kế**: Thiết kế cấu trúc trang và component
3. **Lập trình**: Viết code theo quy chuẩn phát triển
4. **Kiểm thử đa nền tảng**: Tiến hành kiểm thử trên từng nền tảng
5. **Tối ưu hiệu năng**: Tối ưu hiệu năng ứng dụng
6. **Phát hành chính thức**: Build và phát hành

### 8.2 Phát triển trang
- **Quy chuẩn bố cục**: Tuân theo quy chuẩn bố cục cho di động
- **Thiết kế responsive**: Tương thích với các kích thước màn hình khác nhau
- **Trải nghiệm người dùng**: Tối ưu trải nghiệm tương tác trên di động
- **Cân nhắc hiệu năng**: Hiệu năng tải và render trang

### 8.3 Phát triển component
- **Trách nhiệm đơn nhất**: Mỗi component chỉ đảm nhận một chức năng
- **Khả năng cấu hình**: Tham số của component có thể cấu hình
- **Tương thích đa nền tảng**: Cân nhắc khả năng tương thích trên từng nền tảng
- **Tối ưu hiệu năng**: Tối ưu hiệu năng component

### 8.4 Mẹo gỡ lỗi
- **Công cụ phát triển uni-app**: Dùng công cụ phát triển chính thức
- **Gỡ lỗi trên thiết bị thật**: Dùng thiết bị thật để gỡ lỗi
- **Gỡ lỗi bằng console**: Sử dụng hợp lý các phương thức console
- **Gỡ lỗi mạng**: Phân tích request và response của API

## 9. Sự cố thường gặp

### 9.1 Vấn đề tương thích
- **Tương thích API**: Xử lý khác biệt API giữa các nền tảng
- **Tương thích style**: Xử lý khác biệt style giữa các nền tảng
- **Tương thích chức năng**: Xử lý khác biệt chức năng giữa các nền tảng

### 9.2 Vấn đề hiệu năng
- **Tốc độ khởi động**: Tối ưu tốc độ khởi động ứng dụng
- **Tải trang**: Tối ưu tốc độ tải trang
- **Hiệu năng cuộn**: Tối ưu hiệu năng cuộn trang
- **Mức chiếm dụng bộ nhớ**: Tối ưu mức chiếm dụng bộ nhớ của ứng dụng

### 9.3 Vấn đề đóng gói
- **Dung lượng gói**: Tối ưu dung lượng gói ứng dụng
- **Tải theo gói con**: Sử dụng hợp lý cơ chế tải theo gói con (subpackage)
- **File tài nguyên**: Tối ưu kích thước file tài nguyên

### 9.4 Vấn đề phát hành lên cửa hàng ứng dụng
- **Kiểm duyệt của cửa hàng ứng dụng**: Tuân thủ quy tắc kiểm duyệt của từng cửa hàng ứng dụng
- **Kiểm duyệt Mini Program**: Tuân thủ quy tắc kiểm duyệt của từng nền tảng Mini Program
- **Tuân thủ nội dung**: Đảm bảo nội dung ứng dụng tuân thủ quy định

## 10. Triển khai và phát hành

### 10.1 Quy trình build
- **Build H5**: `npm run build:h5`
- **Build WeChat Mini Program**: `npm run build:mp-weixin`
- **Build App**: Dùng tính năng đóng gói trên đám mây (cloud build) của HBuilderX

### 10.2 Quy trình phát hành
- **Phát hành H5**: Triển khai lên máy chủ file tĩnh
- **Phát hành Mini Program**: Gửi lên từng nền tảng Mini Program
- **Phát hành App**: Gửi lên cửa hàng ứng dụng

### 10.3 Quản lý phiên bản
- **Quy chuẩn số phiên bản**: Tuân theo quy chuẩn phiên bản ngữ nghĩa (Semantic Versioning)
- **Nhật ký phát hành**: Ghi lại nội dung thay đổi của từng phiên bản
- **Phát hành dần (gray release)**: Áp dụng chiến lược phát hành dần

## 11. Công cụ phát triển khuyên dùng

### 11.1 IDE khuyên dùng
- **HBuilderX**: IDE được uniapp chính thức khuyến nghị
- **VS Code**: Dùng kèm plugin uniapp
- **WebStorm**: IDE frontend chuyên nghiệp

### 11.2 Plugin khuyên dùng
- **Chợ plugin uni-app**: Chợ plugin chính thức
- **uView UI**: Thư viện UI component cho di động
- **ColorUI**: Thư viện UI component cho di động
- **uni-ui**: Thư viện UI component chính thức

### 11.3 Công cụ khuyên dùng
- **Charles**: Công cụ gỡ lỗi mạng
- **Postman**: Công cụ kiểm thử API
- **Figma/Sketch**: Công cụ thiết kế UI
- **Lighthouse**: Công cụ phân tích hiệu năng

## 12. Tài liệu tham khảo

### 12.1 Tài liệu chính thức
- [Tài liệu chính thức của uni-app](https://uniapp.dcloud.io/)
- [Tài liệu uView UI](https://uviewui.com/)
- [Tài liệu chính thức của Vue](https://v2.vuejs.org/)
- [Tài liệu WeChat Mini Program](https://developers.weixin.qq.com/miniprogram/dev/framework/)

### 12.2 Tài nguyên học tập
- [Hướng dẫn uni-app](https://uniapp.dcloud.io/tutorial/)
- [Vue Mastery](https://www.vuemastery.com/)
- [MDN Web Docs](https://developer.mozilla.org/zh-CN/)
- [CSS-Tricks](https://css-tricks.com/)

### 12.3 Tài nguyên cộng đồng
- [Cộng đồng uni-app](https://ask.dcloud.net.cn/forum-79-1.html)
- [GitHub](https://github.com/)
- [Stack Overflow](https://stackoverflow.com/)
- [Juejin](https://juejin.cn/)

### 12.4 Tài liệu tham khảo cục bộ
- [Tài liệu cấu trúc thư mục UniApp](./references/directory_structure.md)
- [Tài liệu quy trình phát triển API UniApp](./references/api_flow.md)
- [Tài liệu quy chuẩn code UniApp](./references/code_style.md)

## 13. Cộng tác nhóm

### 13.1 Quy trình cộng tác
- **Đánh giá yêu cầu**: Cả nhóm cùng đánh giá yêu cầu
- **Phân công công việc**: Phân công nhiệm vụ phát triển hợp lý
- **Rà soát code (code review)**: Kiểm tra chất lượng code
- **Kiểm thử đa nền tảng**: Các thành viên trong nhóm phụ trách kiểm thử trên các nền tảng khác nhau

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