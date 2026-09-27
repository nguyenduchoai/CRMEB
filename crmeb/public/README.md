# Mô tả cấu trúc thư mục public

## Cấu trúc thư mục

```
:.
├── .htaccess               # Cấu hình rewrite URL (giả tĩnh) cho Apache
├── admin/                  # Tài nguyên tĩnh của trang quản trị
├── assets/                 # Tài nguyên tĩnh dùng chung
├── favicon.ico             # Biểu tượng website
├── index.html              # Điểm vào HTML tĩnh
├── index.php              # Tệp điểm vào PHP
├── install/                # Thư mục trình hướng dẫn cài đặt
├── mobile.html             # HTML điểm vào cho thiết bị di động
├── nginx.htaccess          # Cấu hình rewrite URL (giả tĩnh) cho Nginx
├── pages/                  # Thư mục mẫu trang
├── product_migration.xlsx  # Mẫu Excel di chuyển sản phẩm
├── robots.txt              # Tệp robots cho công cụ tìm kiếm
├── router.php              # Tệp điểm vào định tuyến
├── service_pay_result.html # Trang kết quả thanh toán
├── static/                 # Thư mục tệp tĩnh
├── statics/                # Thư mục tài nguyên tĩnh (style, script)
├── upgrade/                # Thư mục trình hướng dẫn nâng cấp
├── uploads/                # Thư mục tệp tải lên
└── README.md              # Tệp mô tả thư mục
```

## Mô tả thư mục

- **admin/** - Tài nguyên tĩnh của trang quản trị (CSS, JS, hình ảnh)
- **assets/** - Tài nguyên tĩnh dùng chung của dự án
- **index.php** - Tệp điểm vào chính của dự án
- **install/** - Trình hướng dẫn cài đặt hệ thống
- **static/statics** - Thư mục tài nguyên tĩnh của frontend
- **uploads/** - Thư mục tệp do người dùng tải lên
- **pages/** - Mẫu trang cho thiết bị di động

## Mô tả chức năng

Thư mục public là thư mục điểm vào của website:

- **Vai trò điểm vào** - Điểm truy cập duy nhất từ bên ngoài vào dự án
- **Tài nguyên tĩnh** - Chứa các tệp tĩnh như CSS, JS, hình ảnh, v.v.
- **Cách ly bảo mật** - Định tuyến qua tệp điểm vào, ẩn cấu trúc bên trong của dự án
- **Giả tĩnh** - Thực hiện rewrite URL thông qua .htaccess

## Lưu ý bảo mật

- Không nên lưu tệp nhạy cảm trong thư mục này
- Thư mục tải lên cần giới hạn loại tệp
- Định kỳ dọn dẹp các tệp tải lên tạm thời
