# Tài liệu cấu trúc thư mục Admin-Element

## 1 Cấu trúc thư mục gốc của dự án

```
template/       # Thư mục dự án frontend
├── admin-element/    # Dự án frontend trang quản trị
│   ├── public/        # Thư mục tài nguyên tĩnh
│   │   ├── favicon.ico    # Biểu tượng website
│   │   ├── index.html     # File HTML điểm vào
│   │   └── static/        # File tài nguyên tĩnh
│   ├── src/           # Thư mục mã nguồn
│   │   ├── api/        # Định nghĩa API
│   │   ├── assets/     # Tài nguyên tĩnh
│   │   ├── components/ # Thành phần dùng chung
│   │   ├── config/     # Tệp cấu hình
│   │   ├── directive/  # Directive tùy chỉnh
│   │   ├── filters/    # Filter
│   │   ├── layout/     # Thành phần layout
│   │   ├── router/     # Cấu hình route
│   │   ├── store/      # Quản lý trạng thái
│   │   ├── styles/     # File style
│   │   ├── utils/      # Hàm tiện ích (utility)
│   │   ├── views/      # Thành phần trang
│   │   ├── App.vue     # Thành phần gốc
│   │   └── main.js     # File điểm vào
│   ├── tests/          # File kiểm thử
│   ├── .env.*          # Cấu hình biến môi trường
│   ├── babel.config.js # Cấu hình Babel
│   ├── package.json    # Phụ thuộc của dự án
│   ├── vue.config.js   # Cấu hình Vue
│   └── README.md       # Giới thiệu dự án
```

## 2 Mô tả các thư mục chính

### 2.1 Thư mục public/
- **favicon.ico**: Tệp biểu tượng website
- **index.html**: Tệp HTML điểm vào của ứng dụng, ứng dụng Vue sẽ được mount vào tệp này
- **static/**: Thư mục tài nguyên tĩnh, chứa các tệp tĩnh không cần qua xử lý của webpack

### 2.2 Thư mục src/

#### 2.2.1 Thư mục api/
- Định nghĩa tất cả request API
- Tổ chức các tệp API theo mô-đun
- Bao gồm các phương thức gọi API và cấu hình tham số

#### 2.2.2 Thư mục assets/
- **images/**: Tài nguyên hình ảnh
- **icons/**: Tài nguyên biểu tượng (icon)
- **styles/**: Tệp style toàn cục
- Các tệp tài nguyên tĩnh khác

#### 2.2.3 Thư mục components/
- **base/**: Thành phần cơ sở
- **business/**: Thành phần nghiệp vụ
- **common/**: Thành phần dùng chung
- Các thành phần Vue có thể tái sử dụng

#### 2.2.4 Thư mục config/
- **index.js**: Tệp cấu hình chính
- **router.config.js**: Cấu hình route
- **menu.config.js**: Cấu hình menu
- **theme.config.js**: Cấu hình giao diện (theme)
- Các tệp cấu hình hệ thống khác

#### 2.2.5 Thư mục directive/
- Directive Vue tùy chỉnh
- Chẳng hạn directive kiểm soát quyền, xác thực biểu mẫu, v.v.

#### 2.2.6 Thư mục filters/
- Filter Vue tùy chỉnh
- Chẳng hạn định dạng ngày tháng, định dạng số, v.v.

#### 2.2.7 Thư mục layout/
- **components/**: Thành phần bố cục (layout)
- **index.vue**: Tệp bố cục chính
- **AppMain.vue**: Thành phần vùng nội dung
- **Navbar.vue**: Thành phần thanh điều hướng
- **Sidebar.vue**: Thành phần thanh bên (sidebar)

#### 2.2.8 Thư mục router/
- **index.js**: Tệp cấu hình route chính
- **modules/**: Cấu hình route được tổ chức theo mô-đun
- Cấu hình route guard

#### 2.2.9 Thư mục store/
- **index.js**: Tệp quản lý trạng thái chính
- **modules/**: Quản lý trạng thái được tổ chức theo mô-đun
- **getters.js**: Thuộc tính tính toán (computed) toàn cục

#### 2.2.10 Thư mục styles/
- **index.scss**: Điểm vào style toàn cục
- **variables.scss**: Biến toàn cục
- **mixins.scss**: Mixin
- **reset.scss**: Đặt lại style mặc định (reset)

#### 2.2.11 Thư mục utils/
- **request.js**: Đóng gói request mạng
- **auth.js**: Tiện ích liên quan đến xác thực
- **tools.js**: Hàm tiện ích dùng chung
- **storage.js**: Tiện ích lưu trữ

#### 2.2.12 Thư mục views/
- Tổ chức các thành phần trang theo mô-đun nghiệp vụ
- Mỗi mô-đun một thư mục
- Bao gồm thành phần trang và các thành phần con liên quan

#### 2.2.13 App.vue
- Thành phần gốc của ứng dụng
- Chứa cấu trúc bố cục toàn cục

#### 2.2.14 main.js
- Tệp điểm vào của ứng dụng
- Khởi tạo instance Vue
- Tải plugin và cấu hình toàn cục

### 2.3 Tệp cấu hình

#### 2.3.1 package.json
- Cấu hình phụ thuộc của dự án
- Cấu hình lệnh script
- Cấu hình thông tin dự án

#### 2.3.2 vue.config.js
- Cấu hình Vue CLI
- Cấu hình build
- Cấu hình proxy

#### 2.3.3 babel.config.js
- Cấu hình chuyển mã (transpile) của Babel

#### 2.3.4 .env.*
- Tệp cấu hình biến môi trường
- **.env.development**: Môi trường phát triển
- **.env.production**: Môi trường production
- **.env.staging**: Môi trường kiểm thử

## 3 Quy chuẩn thư mục

### 3.1 Quy tắc đặt tên
- Tên thư mục dùng chữ thường, nhiều từ thì phân tách bằng dấu gạch nối (-)
- Tên tệp dùng chữ thường, nhiều từ thì phân tách bằng dấu gạch nối (-)
- Tên thành phần dùng kiểu đặt tên PascalCase

### 3.2 Nguyên tắc tổ chức thư mục
1. **Tổ chức theo mô-đun chức năng**: Đặt các tệp có chức năng liên quan trong cùng một thư mục
2. **Mô-đun hóa**: Mỗi mô-đun giữ tính độc lập tương đối
3. **Khả năng mở rộng**: Cấu trúc thư mục cần dễ mở rộng và bảo trì
4. **Tính nhất quán**: Giữ cấu trúc thư mục nhất quán

### 3.3 Xử lý thư mục đặc biệt
- **components/**: Chỉ chứa các thành phần có thể tái sử dụng
- **views/**: Chứa các thành phần cấp trang
- **api/**: Tổ chức API theo module
- **store/modules/**: Tổ chức quản lý trạng thái (state) theo module

## 4 Thực tiễn tốt nhất

### 4.1 Gợi ý sử dụng thư mục
- Khi thêm module nghiệp vụ mới, tạo thư mục tương ứng trong views/
- Khi thêm component có thể tái sử dụng, đặt vào thư mục con tương ứng trong components/
- Khi thêm API mới, tổ chức theo module trong api/
- Khi thêm quản lý trạng thái mới, tạo module tương ứng trong store/modules/

### 4.2 Duy trì cấu trúc thư mục
- Định kỳ dọn dẹp các file và thư mục không còn dùng
- Giữ cấu trúc thư mục rõ ràng và gọn gàng
- Tuân thủ quy tắc đặt tên thống nhất
- Cập nhật tài liệu kịp thời để phản ánh các thay đổi của cấu trúc thư mục

## 5 Vấn đề thường gặp

### 5.1 Vấn đề quyền thư mục
- Đảm bảo quyền thư mục được thiết lập đúng, tránh lỗi quyền truy cập khi build

### 5.2 Vấn đề tham chiếu đường dẫn
- Dùng đường dẫn tương đối hoặc đường dẫn alias để tham chiếu file
- Tránh dùng đường dẫn tuyệt đối

### 5.3 Tối ưu cấu trúc thư mục
- Khi quy mô dự án tăng lên, kịp thời điều chỉnh cấu trúc thư mục
- Giữ phân cấp thư mục hợp lý, tránh lồng nhau quá sâu

## 6 Tổng kết

Dự án Admin-Element áp dụng cấu trúc dự án Vue + ElementUI tiêu chuẩn, nhờ cách tổ chức thư mục rõ ràng và quy tắc đặt tên thống nhất mà nâng cao được khả năng bảo trì và khả năng mở rộng của code. Lập trình viên nên tuân thủ quy chuẩn cấu trúc thư mục, tổ chức code hợp lý để đảm bảo khả năng bảo trì lâu dài của dự án.