# Mô tả các tệp trong thư mục crmeb

## Tổng quan cấu trúc thư mục
```
crmeb/
├── app/                 # Mã nguồn lõi của ứng dụng (controller, model, service, v.v.)
├── backup/              # File sao lưu dữ liệu
├── config/              # File cấu hình (cơ sở dữ liệu, cache, API, v.v.)
├── crmeb/               # Module dùng chung nội bộ của dự án hoặc thư viện mở rộng
├── public/              # Web - điểm truy cập công khai (tài nguyên tĩnh, index.php）
├── route/               # Định nghĩa route
├── runtime/             # Cache, log, Session, v.v. sinh ra khi chạy (cần loại khỏi quản lý phiên bản)
│
├── .constant            # File định nghĩa hằng số (cần loại khỏi quản lý phiên bản)
├── .dockerignore        # Docker - quy tắc bỏ qua khi build
├── .env                 # Cấu hình biến môi trường (thông tin nhạy cảm) (cần loại khỏi quản lý phiên bản)
├── .env.example         # File mẫu biến môi trường
├── .htaccess            # Apache - quy tắc viết lại URL (rewrite)
├── .phpstorm.meta.php   # PhpStorm - siêu dữ liệu (metadata)
├── .travis.yml          # Travis CI Cấu hình
├── .version             # Thông tin phiên bản
├── Dockerfile           # Docker - file build image
├── LICENSE.txt          # Giấy phép mã nguồn mở
├── README.md            # Giới thiệu dự án
├── build.example.php    # Script build mẫu
├── composer.json        # Composer cấu hình phụ thuộc
├── composer.lock        # Composer - khóa phiên bản
├── filetree.txt         # Bản chụp cấu trúc cây thư mục
├── index.html           # Trang chủ mặc định (chặn truy cập trực tiếp thư mục)
├── my.cnf               # MySQL - cấu hình tùy chỉnh (cần loại khỏi quản lý phiên bản)
├── nginx.conf           # Nginx - cấu hình (cần loại khỏi quản lý phiên bản)
├── php-fpm.conf         # PHP-FPM - cấu hình (cần loại khỏi quản lý phiên bản)
├── php-ini-overrides.ini# PHP - ghi đè ini tùy chỉnh (cần loại khỏi quản lý phiên bản)
├── redis.conf           # Redis - cấu hình (cần loại khỏi quản lý phiên bản)
├── start.sh             # Script khởi động dự án (cần loại khỏi quản lý phiên bản)
├── supervisord.conf     # Supervisor - cấu hình quản lý tiến trình (cần loại khỏi quản lý phiên bản)
├── think                # ThinkPHP - file entry của framework
├── vhost.conf           # Cấu hình virtual host (cần loại khỏi quản lý phiên bản)
└── workerman.bat        # Windows - script khởi động Workerman
```

## Mô tả các thư mục chính
- **app/**  
  Chứa mã nguồn cốt lõi của logic nghiệp vụ, bao gồm bộ điều khiển (Controller), mô hình (Model), tầng dịch vụ (Service), v.v., tuân theo kiến trúc MVC hoặc kiến trúc phân tầng tương tự.
- **backup/**  
  Dùng để lưu các tệp sao lưu cơ sở dữ liệu hoặc dữ liệu quan trọng, nên định kỳ dọn dẹp các bản sao lưu cũ.
- **config/**  
  Các tệp cấu hình môi trường và ứng dụng, như cơ sở dữ liệu, bộ nhớ đệm (cache), hàng đợi, xác thực API, v.v.
- **crmeb/**  
  Các mô-đun dùng chung nội bộ của dự án hoặc phần tích hợp SDK bên thứ ba, có thể bao gồm một số lớp tiện ích hoặc chức năng mở rộng.
- **public/**  
  Thư mục gốc của Web server, chứa các tài nguyên có thể truy cập trực tiếp qua trình duyệt (như hình ảnh, JS, CSS) và tệp điểm vào `index.php`.
- **route/**  
  Tệp định nghĩa route, dùng để ánh xạ yêu cầu URL tới phương thức controller cụ thể.
- **runtime/**  
  Chứa dữ liệu tạm được sinh ra khi chạy như cache, log, Session, v.v.; thư mục này nên được bỏ qua trong `.gitignore`.

## Mô tả các tệp chính
- **.env / .env.example**  
  Cấu hình biến môi trường và tệp mẫu, `.env` chứa thông tin nhạy cảm, không được commit lên kho mã nguồn.
- **composer.json / composer.lock**  
  Tệp cấu hình quản lý phụ thuộc (dependency) và tệp khóa (lock) của dự án PHP.
- **Liên quan đến Dockerfile / docker-compose**  
  Cấu hình dùng cho triển khai dạng container.
- **my.cnf / redis.conf / nginx.conf**  
  Cấu hình tùy chỉnh cho các loại dịch vụ.
- **start.sh**  
  Script điểm vào để khởi động dự án ở máy cục bộ hoặc trên máy chủ.
- **think**  
  Tệp điểm vào thống nhất của framework ThinkPHP, chịu trách nhiệm khởi tạo framework và phân phối yêu cầu.