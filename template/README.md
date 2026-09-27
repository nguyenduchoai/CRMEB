# Tài liệu mô tả thư mục Template

## Cấu trúc thư mục

`template` là thư mục chứa mã nguồn các dự án frontend của hệ thống CRMEB, gồm hai dự án con chính:

```
template/
├── admin/         # Dự án frontend trang quản trị（Vue.js + Element UI）
└── uni-app/       # Dự án frontend cho di động（UniApp）
```

## Giới thiệu dự án

### 1. Dự án frontend trang quản trị (admin/)

Dự án frontend trang quản trị được phát triển trên nền Vue.js + Element UI, dùng làm giao diện thao tác của hệ thống quản trị.

#### Cấu trúc thư mục chính

```
admin/
├── public/        # Thư mục tài nguyên tĩnh
├── src/           # Thư mục mã nguồn
│   ├── api/       # API định nghĩa
│   ├── assets/    # File tài nguyên tĩnh
│   ├── components/ # Thành phần dùng chung
│   ├── config/    # Tệp cấu hình
│   ├── directive/ # Directive tùy chỉnh
│   ├── filters/   # Filter
│   ├── layout/    # Thành phần layout
│   ├── libs/      # Thư viện công cụ
│   ├── pages/     # Thành phần trang
│   ├── router/    # Cấu hình route
│   ├── store/     # Quản lý trạng thái
│   ├── styles/    # File style
│   ├── utils/     # Hàm tiện ích (utility)
│   ├── App.vue    # Thành phần gốc
│   └── main.js    # File điểm vào
├── .env.dev       # Cấu hình môi trường phát triển
├── .env.production # Cấu hình môi trường production
├── package.json   # Phụ thuộc của dự án
├── vue.config.js  # Vue Cấu hình
└── README.md      # Giới thiệu dự án
```

#### Công nghệ sử dụng
- Vue.js 2.x
- Element UI
- Vue Router
- Vuex
- Axios
- ECharts

#### Lệnh phát triển
- Cài đặt phụ thuộc: `npm install`
- Chạy ở môi trường phát triển: `npm run dev`
- Build cho môi trường production: `npm run build`
- Kiểm tra code: `npm run lint`

### 2. Dự án frontend di động (uni-app/)

Dự án frontend di động được phát triển trên nền UniApp, hỗ trợ phát hành đa nền tảng (WeChat Mini Program, H5, App, v.v.).

#### Cấu trúc thư mục chính

```
uni-app/
├── api/           # API định nghĩa
├── components/    # Thành phần dùng chung
├── config/        # Tệp cấu hình
├── libs/          # Thư viện công cụ
├── mixins/        # Mixin
├── pages/         # Thành phần trang
├── static/        # Tài nguyên tĩnh
├── App.vue        # Thành phần gốc
├── main.js        # File điểm vào
├── manifest.json  # Cấu hình ứng dụng
├── pages.json     # Cấu hình trang
└── package.json   # Phụ thuộc của dự án
```

#### Công nghệ sử dụng
- UniApp
- Vue.js 2.x
- uView UI (thư viện component cho UniApp)
- Vuex
- Axios

#### Lệnh phát triển
- Cài đặt phụ thuộc: `npm install`
- Chạy ở môi trường phát triển: dùng HBuilderX để chạy trên nền tảng tương ứng
- Build cho môi trường production: dùng HBuilderX để phát hành lên nền tảng tương ứng

## Tương tác giữa frontend và backend

Các dự án frontend tương tác với backend thông qua API, cấu hình chính như sau:

### Cấu hình API cho trang quản trị
- Môi trường phát triển: `VUE_APP_API_URL` trong tệp `admin/.env.dev`
- Môi trường production: `VUE_APP_API_URL` trong tệp `admin/.env.production`

### Cấu hình API cho di động
- Tệp cấu hình: `uni-app/config/api.js`
- Đường dẫn gốc của API: biến `baseURL`

## Hướng dẫn triển khai

### Triển khai trang quản trị
1. Chạy lệnh build: `npm run build`
2. Triển khai các tệp trong thư mục `admin/dist` lên máy chủ Web
3. Cấu hình máy chủ Nginx hoặc Apache trỏ tới các tệp tĩnh sau khi build

### Triển khai ứng dụng di động
1. Dùng HBuilderX mở thư mục `uni-app`
2. Phát hành lên nền tảng tương ứng theo nhu cầu:
   - WeChat Mini Program: Phát hành -> Mini Program - WeChat
   - H5: Phát hành -> H5
   - App: Phát hành -> App native - Đóng gói trên đám mây

## Quy chuẩn phát triển

### Phong cách code
- Tuân thủ hướng dẫn phong cách (style guide) chính thức của Vue
- Dùng ESLint để kiểm tra code
- Tên component dùng PascalCase
- Tên phương thức và tên biến dùng camelCase

### Quy tắc đặt tên
- Tên tệp và tên thư mục dùng kebab-case
- Tên component dùng PascalCase
- Hằng số viết hoa toàn bộ, các từ phân tách bằng dấu gạch dưới

### Cách dùng thư mục
- `api/`: Tổ chức API theo module
- `components/`: Chứa các component có thể tái sử dụng
- `pages/`: Chứa các component trang
- `utils/`: Chứa các hàm tiện ích
- `styles/`: Chứa style toàn cục

## Lưu ý

1. **API**: Dự án frontend cần giữ nhất quán với API backend, nếu API có thay đổi thì cần sửa code frontend đồng bộ theo.

2. **Cấu hình môi trường**: Địa chỉ API ở từng môi trường cần được sửa trong tệp cấu hình tương ứng.

3. **Quản lý phụ thuộc**: Dùng npm để quản lý các phụ thuộc của dự án, đảm bảo tính nhất quán của phiên bản phụ thuộc.

4. **Tối ưu build**: Khi build cho môi trường production, code sẽ được tự động nén và tối ưu.

5. **Xử lý cross-domain**: Ở môi trường phát triển, dùng cấu hình proxy của Vue CLI để xử lý vấn đề cross-domain, còn ở môi trường production cần cấu hình CORS phía máy chủ.

6. **Tối ưu hiệu năng**:
   - Sử dụng hợp lý lazy load component
   - Tối ưu tài nguyên hình ảnh
   - Giảm các API request không cần thiết
   - Dùng cache để giảm việc lấy dữ liệu lặp lại

## Sự cố thường gặp

### 1. Gọi API ở môi trường phát triển thất bại
- Kiểm tra địa chỉ API trong tệp `.env.dev` có đúng không
- Kiểm tra dịch vụ backend có đang chạy bình thường không
- Kiểm tra kết nối mạng có bình thường không

### 2. Trang bị trắng sau khi build
- Kiểm tra cấu hình route có đúng không
- Kiểm tra xem có lỗi nào chưa được bắt (uncaught) không
- Kiểm tra đường dẫn tài nguyên tĩnh có đúng không

### 3. Vấn đề tương thích trên di động
- Dùng bố cục Flex để thiết kế responsive
- Tương thích theo từng kích thước thiết bị
- Kiểm thử hiển thị trên các thiết bị khác nhau

## Liên hệ và hỗ trợ

- Tài liệu chính thức: https://doc.crmeb.com
- Cộng đồng kỹ thuật: https://www.crmeb.com/ask
- Nhóm QQ chính thức: vui lòng tham khảo website chính thức

## Thông tin phiên bản

- Phiên bản CRMEB: 5.6.4
- Frontend trang quản trị: Vue 2.x + Element UI
- Frontend di động: UniApp

---

**Ghi chú**: Thư mục này chứa mã nguồn các dự án frontend, mã nguồn backend nằm trong thư mục `crmeb/` ở thư mục gốc của dự án.