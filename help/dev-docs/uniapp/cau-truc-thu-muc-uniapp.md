# Tài liệu cấu trúc thư mục uniapp của dự án CRMEB

## 1. Lời mở đầu
- **Mục đích tài liệu**: Mô tả chi tiết cấu trúc thư mục uniapp trong dự án CRMEB và chức năng của từng thư mục
- **Phạm vi áp dụng**: Lập trình viên di động, lập trình viên frontend, nhân sự bảo trì dự án
- **Định nghĩa thuật ngữ**: uniapp - Framework phát triển ứng dụng đa nền tảng dựa trên Vue.js

## 2. Cấu trúc thư mục

### 2.1 Cây thư mục

```
uni-app/
├── androidPrivacy.json  # Android (cấu hình chính sách bảo mật)
├── api/                 # API thư mục
├── App.vue              # Thành phần điểm vào ứng dụng
├── components/          # Thư mục thành phần dùng chung
├── config/              # Thư mục file cấu hình
├── libs/                # Thư mục thư viện bên thứ ba
├── main.js              # Tệp điểm vào của ứng dụng
├── manifest.json        # File cấu hình ứng dụng
├── mixins/              # Thư mục tệp mixin
├── package-lock.json    # npm (tệp khóa phiên bản dependency)
├── package.json         # npm (tệp cấu hình dependency)
├── pages/               # Thư mục trang
├── pages.json           # File cấu hình route trang
├── plugin/              # Thư mục plugin
├── static/              # Thư mục tài nguyên tĩnh
├── store/               # Thư mục quản lý trạng thái
├── uni.scss             # File style toàn cục
├── utils/               # Thư mục hàm tiện ích
└── vue.config.js        # Vue Tệp cấu hình
```

### 2.2 Mô tả chức năng các thư mục

#### 2.2.1 Thư mục cốt lõi
- **api/**: Chứa định nghĩa API, bao gồm tất cả các lệnh gọi API backend
- **components/**: Chứa các component dùng chung, có thể tái sử dụng ở nhiều trang
- **pages/**: Chứa các trang của ứng dụng, mỗi trang tương ứng với một thư mục con
- **static/**: Chứa tài nguyên tĩnh như hình ảnh, font chữ, biểu tượng, v.v.
- **utils/**: Chứa các hàm tiện ích như xử lý ngày tháng, đóng gói request, v.v.

#### 2.2.2 Thư mục cấu hình
- **config/**: Chứa cấu hình ứng dụng như địa chỉ API, định nghĩa hằng số, v.v.
- **mixins/**: Chứa các tệp mixin, dùng để tái sử dụng logic của component
- **store/**: Chứa các tệp quản lý trạng thái Vuex
- **libs/**: Chứa thư viện bên thứ ba như SDK, thư viện tiện ích, v.v.

#### 2.2.3 Thư mục plugin
- **plugin/**: Chứa các plugin của ứng dụng như plugin thanh toán, plugin chia sẻ, v.v.

## 3. Mô tả các tệp cốt lõi

### 3.1 Tệp điểm vào
- **App.vue**: Component gốc của ứng dụng, cấu hình toàn cục và quản lý vòng đời
- **main.js**: Tệp điểm vào của ứng dụng, khởi tạo instance Vue và cấu hình toàn cục

### 3.2 Tệp cấu hình
- **manifest.json**: Tệp cấu hình ứng dụng, bao gồm tên ứng dụng, phiên bản, quyền, v.v.
- **pages.json**: Tệp cấu hình route trang, định nghĩa đường dẫn trang, thanh điều hướng, v.v.
- **vue.config.js**: Tệp cấu hình dự án Vue, như cấu hình proxy, cấu hình build, v.v.

### 3.3 Tệp style
- **uni.scss**: Tệp style toàn cục, định nghĩa biến chủ đề (theme) và style dùng chung

### 3.4 Tệp phụ thuộc
- **package.json**: Tệp cấu hình phụ thuộc npm, quản lý các phụ thuộc của dự án
- **package-lock.json**: Tệp khóa phụ thuộc npm, đảm bảo phiên bản các phụ thuộc nhất quán

## 4. Cấu trúc thư mục trang

### 4.1 Cách tổ chức trang
```
pages/
├── index/              # Trang chủ
│   ├── index.vue       # Thành phần trang
│   └── main.js         # Điểm vào của trang (tùy chọn)
├── goods/              # Các trang liên quan đến sản phẩm
│   ├── list.vue        # Danh sách sản phẩm
│   └── detail.vue      # Chi tiết sản phẩm
└── user/               # Các trang liên quan đến người dùng
    ├── index.vue       # Trung tâm người dùng
    └── login.vue       # Trang đăng nhập
```

### 4.2 Mô tả tệp trang
- **Component trang (.vue)**: Gồm template, script và style
- **Điểm vào của trang (main.js)**: Cấu hình khởi tạo ở cấp trang (tùy chọn)

## 5. Quy chuẩn phát triển

### 5.1 Quy tắc đặt tên
- **Tên thư mục**: Từ viết thường, nhiều từ thì phân tách bằng dấu gạch nối (-)
- **Tên tệp**: Từ viết thường, nhiều từ thì phân tách bằng dấu gạch nối (-)
- **Tên component**: Quy tắc đặt tên PascalCase
- **Tên biến**: Quy tắc đặt tên camelCase
- **Tên hằng số**: Viết hoa toàn bộ, nhiều từ thì phân tách bằng dấu gạch dưới (_)

### 5.2 Quy chuẩn code
- Tuân thủ hướng dẫn phong cách (style guide) chính thức của Vue
- Sử dụng cú pháp ES6+
- Phát triển theo hướng component, tăng khả năng tái sử dụng code
- Sử dụng Vuex hợp lý để quản lý trạng thái toàn cục
- Logic trang rõ ràng, tránh lồng nhau quá sâu

### 5.3 Tối ưu hiệu năng
- Tối ưu tài nguyên hình ảnh, dùng kích thước và định dạng phù hợp
- Giảm số lượng HTTP request, sử dụng cache hợp lý
- Lazy load component, giảm kích thước gói ban đầu
- Tránh dùng các phép tính phức tạp trong template
- Sử dụng hợp lý các API tối ưu hiệu năng do uni-app cung cấp

## 6. Lưu ý

### 6.1 Tương thích đa nền tảng
- Chú ý sự khác biệt về API giữa các nền tảng
- Tránh dùng các tính năng đặc thù của một nền tảng
- Khi kiểm thử cần bao quát các nền tảng chính (iOS, Android, WeChat Mini Program, v.v.)

### 6.2 Cấu hình đóng gói
- Cấu hình tham số đóng gói tương ứng cho từng nền tảng
- Chú ý cấu hình quyền của ứng dụng
- Cấu hình hợp lý biểu tượng ứng dụng và màn hình khởi động

### 6.3 Mẹo gỡ lỗi
- Dùng công cụ dành cho nhà phát triển của uni-app để gỡ lỗi
- Dùng console.log để xuất thông tin gỡ lỗi
- Theo dõi các cảnh báo và thông báo lỗi trên console

## 7. Tổng kết

### 7.1 Đặc điểm cấu trúc thư mục
- **Cấu trúc rõ ràng**: Tuân theo cấu trúc thư mục được uni-app chính thức khuyến nghị
- **Tính module hóa cao**: Tổ chức code thành các module theo chức năng
- **Dễ bảo trì**: Trách nhiệm của từng thư mục rõ ràng, cấu trúc code hợp lý
- **Khả năng mở rộng tốt**: Dễ dàng thêm chức năng và trang mới

### 7.2 Kế hoạch tiếp theo
- Hoàn thiện thư viện component, tăng khả năng tái sử dụng code
- Tối ưu hiệu năng trang, nâng cao trải nghiệm người dùng
- Bổ sung kiểm thử tự động, đảm bảo chất lượng code
- Liên tục cập nhật các thư viện phụ thuộc, giữ cho framework luôn hiện đại

---

Phiên bản: 1.0
Tác giả: Hệ thống tự động tạo
Ngày cập nhật: 2026-01-23
---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
