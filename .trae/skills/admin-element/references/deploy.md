# Tài liệu triển khai frontend trang quản trị

## 1. Tổng quan

Tài liệu này mô tả quy trình triển khai frontend trang quản trị trong dự án CRMEB, bao gồm các khâu build, triển khai, cấu hình, v.v., nhằm chuẩn hóa quy trình triển khai frontend, đảm bảo quá trình triển khai diễn ra suôn sẻ và hệ thống vận hành ổn định.

## 2. Môi trường triển khai

### 2.1 Yêu cầu máy chủ
- **Hệ điều hành**: Linux (Ubuntu 18.04+, CentOS 7+)
- **Máy chủ Web**: Nginx 1.14+ hoặc Apache 2.4+
- **Node.js**: v12.0.0+ (chỉ cần khi build)
- **npm/yarn**: v6.0.0+ (chỉ cần khi build)
- **Bộ nhớ**: Tối thiểu 2GB RAM
- **CPU**: Tối thiểu 2 nhân CPU
- **Dung lượng ổ đĩa**: Tối thiểu 20GB dung lượng trống

### 2.2 Chuẩn bị môi trường
- **Cấu hình máy chủ Web**: Cấu hình virtual host, trỏ tới thư mục chứa sản phẩm build của frontend
- **Chứng chỉ SSL**: Cấu hình HTTPS, sử dụng chứng chỉ SSL
- **Tường lửa**: Mở cổng 80/443
- **Tên miền**: Cấu hình phân giải tên miền, trỏ tới IP máy chủ

## 3. Quy trình build

### 3.1 Build cho môi trường phát triển
- **Lệnh**: `npm run dev`
- **Mục đích**: Phát triển cục bộ, khởi động máy chủ phát triển (dev server)
- **Địa chỉ truy cập**: `http://localhost:8080`

### 3.2 Build cho môi trường kiểm thử
- **Lệnh**: `npm run build:test`
- **Mục đích**: Triển khai lên môi trường kiểm thử
- **Sản phẩm build**: Thư mục `dist/`

### 3.3 Build cho môi trường production
- **Lệnh**: `npm run build:prod`
- **Mục đích**: Triển khai lên môi trường production
- **Sản phẩm build**: Thư mục `dist/`

### 3.4 Tối ưu build
- **Nén code**: Nén các tệp JS/CSS/HTML
- **Nén tài nguyên**: Nén hình ảnh và các tài nguyên tĩnh khác
- **Tree Shaking**: Loại bỏ code không sử dụng
- **Tách code (code splitting)**: Tách code theo route
- **Tải trước (preload)**: Tải trước các tài nguyên quan trọng

## 4. Phương thức triển khai

### 4.1 Triển khai tĩnh

#### 4.1.1 Cấu hình Nginx
```nginx
server {
    listen 80;
    server_name admin.example.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name admin.example.com;
    
    # SSL Cấu hình
    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/key.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers on;
    
    # Cấu hình file tĩnh
    root /path/to/admin/dist;
    index index.html;
    
    # Viết lại route, khắc phục lỗi 404 khi làm mới trang ở ứng dụng một trang (SPA)
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    # Cache tài nguyên tĩnh
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 30d;
        add_header Cache-Control "public, max-age=2592000";
    }
    
    # Cấu hình log
    access_log /var/log/nginx/admin_access.log;
    error_log /var/log/nginx/admin_error.log;
}
```

#### 4.1.2 Cấu hình Apache
```apache
<VirtualHost *:80>
    ServerName admin.example.com
    Redirect permanent / https://admin.example.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName admin.example.com
    
    # SSL Cấu hình
    SSLEngine on
    SSLCertificateFile /path/to/ssl/cert.pem
    SSLCertificateKeyFile /path/to/ssl/key.pem
    
    # Cấu hình file tĩnh
    DocumentRoot /path/to/admin/dist
    <Directory /path/to/admin/dist>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Viết lại route, khắc phục lỗi 404 khi làm mới trang ở ứng dụng một trang (SPA)
    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteBase /
        RewriteRule ^index\.html$ - [L]
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . /index.html [L]
    </IfModule>
    
    # Cấu hình log
    ErrorLog ${APACHE_LOG_DIR}/admin_error.log
    CustomLog ${APACHE_LOG_DIR}/admin_access.log combined
</VirtualHost>
```

### 4.2 Triển khai bằng container

#### 4.2.1 Dockerfile
```dockerfile
# Image cơ sở
FROM node:12-alpine as build

# Đặt thư mục làm việc
WORKDIR /app

# Sao chép file phụ thuộc
COPY package*.json ./

# Cài đặt các gói phụ thuộc
RUN npm install

# Sao chép mã nguồn
COPY . .

# Build bản production
RUN npm run build:prod

# Image production
FROM nginx:1.19-alpine

# Sao chép kết quả build
COPY --from=build /app/dist /usr/share/nginx/html

# Sao chép cấu hình Nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Mở cổng
EXPOSE 80

# Khởi động Nginx
CMD ["nginx", "-g", "daemon off;"]
```

#### 4.2.2 Docker Compose
```yaml
version: '3'
services:
  admin:
    build: .
    ports:
      - "80:80"
    restart: always
    volumes:
      - ./ssl:/etc/nginx/ssl
    environment:
      - TZ=Asia/Shanghai
```

#### 4.2.3 Lệnh triển khai
```bash
# Build image
docker build -t crmeb-admin .

# Chạy container
docker run -d --name crmeb-admin -p 80:80 crmeb-admin

# Đã sử dụng Docker Compose
docker-compose up -d
```

### 4.3 Triển khai qua CDN

#### 4.3.1 Cấu hình CDN
- **Thiết lập máy chủ gốc (origin)**: Trỏ tới máy chủ chứa tệp tĩnh
- **Chiến lược cache**: Cấu hình thời gian cache cho tài nguyên tĩnh
- **HTTPS**: Bật HTTPS
- **HTTP/2**: Bật HTTP/2

#### 4.3.2 Quy trình triển khai
1. Build dự án frontend
2. Tải sản phẩm build lên CDN
3. Cấu hình chiến lược cache của CDN
4. Kiểm tra kết quả triển khai

## 5. Quản lý cấu hình

### 5.1 Cấu hình biến môi trường
- **Môi trường phát triển**: `.env.development`
- **Môi trường kiểm thử**: `.env.test`
- **Môi trường production**: `.env.production`

### 5.2 Ví dụ cấu hình
```env
# API địa chỉ gốc
VUE_APP_API_BASE_URL=https://api.example.com

# Địa chỉ CDN cho tài nguyên tĩnh
VUE_APP_CDN_BASE_URL=https://cdn.example.com

# Tên ứng dụng
VUE_APP_TITLE=CRMEB Trang quản trị

# Môi trường build
NODE_ENV=production

# Phiên bản build
VUE_APP_VERSION=1.0.0
```

### 5.3 Cấu hình khi chạy (runtime)
- **Địa chỉ API**: Có thể thay đổi qua biến môi trường hoặc tệp cấu hình
- **Cấu hình giao diện (theme)**: Có thể thay đổi qua tệp cấu hình
- **Cấu hình quyền**: Có thể lấy động qua API backend

## 6. Quy trình triển khai

### 6.1 Triển khai thủ công
1. **Pull code**: `git pull origin master`
2. **Cài đặt phụ thuộc**: `npm install`
3. **Build dự án**: `npm run build:prod`
4. **Triển khai tệp**: Sao chép thư mục `dist/` lên máy chủ
5. **Cấu hình máy chủ Web**: Cấu hình virtual host
6. **Khởi động lại dịch vụ**: Khởi động lại máy chủ Web
7. **Kiểm tra triển khai**: Truy cập địa chỉ trang quản trị

### 6.2 Triển khai tự động

#### 6.2.1 Cấu hình CI/CD
- **Jenkins**: Cấu hình job Jenkins để tự động build và triển khai
- **GitLab CI**: Cấu hình `.gitlab-ci.yml` để tự động build và triển khai
- **GitHub Actions**: Cấu hình `.github/workflows/deploy.yml` để tự động build và triển khai

#### 6.2.2 Ví dụ GitHub Actions
```yaml
name: Deploy Admin Frontend

on:
  push:
    branches:
      - master

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout code
        uses: actions/checkout@v2

      - name: Setup Node.js
        uses: actions/setup-node@v2
        with:
          node-version: '12'

      - name: Install dependencies
        run: npm install

      - name: Build project
        run: npm run build:prod

      - name: Deploy to server
        uses: easingthemes/ssh-deploy@v2.1.5
        env:
          SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
          ARGS: '-rltgoDzvO --delete'
          SOURCE: 'dist/'
          REMOTE_HOST: ${{ secrets.REMOTE_HOST }}
          REMOTE_USER: ${{ secrets.REMOTE_USER }}
          REMOTE_PORT: ${{ secrets.REMOTE_PORT }}
          TARGET: ${{ secrets.REMOTE_TARGET }}

      - name: Restart Nginx
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.REMOTE_HOST }}
          username: ${{ secrets.REMOTE_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          port: ${{ secrets.REMOTE_PORT }}
          script: sudo systemctl restart nginx
```

## 7. Giám sát và bảo trì

### 7.1 Giám sát
- **Log truy cập**: Phân tích log truy cập của máy chủ Web
- **Log lỗi**: Giám sát log lỗi của máy chủ Web
- **Giám sát hiệu năng**: Giám sát tốc độ tải trang, thời gian phản hồi
- **Giám sát tính khả dụng**: Giám sát tính khả dụng của dịch vụ, thiết lập cảnh báo

### 7.2 Bảo trì
- **Cập nhật định kỳ**: Định kỳ cập nhật code frontend, vá lỗ hổng
- **Dọn cache**: Định kỳ dọn cache CDN
- **Dọn log**: Định kỳ dọn các tệp log
- **Sao lưu**: Định kỳ sao lưu sản phẩm build và tệp cấu hình

### 7.3 Vấn đề thường gặp

#### 7.3.1 Lỗi 404
- **Vấn đề**: Tải lại trang thì bị lỗi 404
- **Giải pháp**: Cấu hình máy chủ Web, chuyển hướng mọi request về index.html

#### 7.3.2 Tải tài nguyên tĩnh thất bại
- **Vấn đề**: Tải tài nguyên tĩnh (JS/CSS/hình ảnh) thất bại
- **Giải pháp**: Kiểm tra đường dẫn tài nguyên tĩnh, đảm bảo CDN được cấu hình đúng

#### 7.3.3 Gọi API thất bại
- **Vấn đề**: Frontend không gọi được API backend
- **Giải pháp**: Kiểm tra cấu hình địa chỉ API, đảm bảo dịch vụ backend đang chạy bình thường

#### 7.3.4 Vấn đề hiệu năng
- **Vấn đề**: Trang tải chậm, giật lag
- **Giải pháp**: Tối ưu code frontend, sử dụng CDN, bật HTTP/2

## 8. Chiến lược rollback

### 8.1 Quản lý phiên bản
- **Số phiên bản**: Tuân theo quy chuẩn đánh số phiên bản ngữ nghĩa (Semantic Versioning)
- **Nhật ký phát hành**: Ghi lại phiên bản và nội dung thay đổi của mỗi lần phát hành
- **Sao lưu**: Sao lưu sản phẩm build của mỗi lần phát hành

### 8.2 Quy trình rollback
1. **Dừng dịch vụ**: Dừng dịch vụ của phiên bản hiện tại
2. **Khôi phục bản sao lưu**: Khôi phục về phiên bản ổn định trước đó
3. **Khởi động lại dịch vụ**: Khởi động dịch vụ sau khi khôi phục
4. **Kiểm tra rollback**: Kiểm tra dịch vụ có chạy bình thường không

### 8.3 Phương án rollback
- **Rollback thủ công**: Sao chép thủ công tệp sao lưu vào thư mục triển khai
- **Rollback tự động**: Tự động rollback thông qua công cụ CI/CD
- **Rollback container**: Rollback theo phiên bản Docker image

## 9. Triển khai an toàn

### 9.1 Cấu hình HTTPS
- **Chứng chỉ SSL**: Sử dụng chứng chỉ SSL do CA uy tín cấp
- **Gia hạn chứng chỉ**: Định kỳ gia hạn chứng chỉ SSL
- **Chuyển hướng HTTP**: Chuyển hướng request HTTP sang HTTPS

### 9.2 Header bảo mật
- **Content-Security-Policy**: Cấu hình chính sách bảo mật nội dung
- **X-Content-Type-Options**: Ngăn chặn việc dò đoán kiểu MIME (MIME sniffing)
- **X-Frame-Options**: Ngăn chặn tấn công clickjacking
- **X-XSS-Protection**: Bật bộ lọc XSS

### 9.3 Kiểm soát truy cập
- **Danh sách trắng IP**: Giới hạn các IP được phép truy cập trang quản trị
- **Xác thực đăng nhập**: Bắt buộc xác thực đăng nhập
- **Kiểm soát quyền**: Kiểm soát quyền dựa trên vai trò
- **Quản lý phiên**: Quản lý phiên (session) an toàn

### 9.4 Phòng chống lỗ hổng
- **Quét phụ thuộc**: Định kỳ quét lỗ hổng của các gói phụ thuộc
- **Kiểm định code**: Định kỳ kiểm định (audit) bảo mật code
- **Kiểm thử xâm nhập**: Định kỳ thực hiện kiểm thử xâm nhập (pentest)

## 10. Thực tiễn tốt nhất

### 10.1 Tối ưu build
- **Sử dụng cache**: Cache các gói phụ thuộc và sản phẩm build
- **Build song song**: Dùng đa luồng để build song song
- **Build tăng dần (incremental)**: Chỉ build các tệp có thay đổi
- **Log build**: Lưu log build để thuận tiện xử lý sự cố

### 10.2 Tối ưu triển khai
- **Phát hành dần (gray release)**: Áp dụng chiến lược phát hành dần
- **Triển khai blue-green**: Áp dụng chiến lược triển khai blue-green
- **Triển khai cuốn chiếu (rolling)**: Áp dụng chiến lược triển khai cuốn chiếu
- **Phát hành canary**: Áp dụng chiến lược phát hành canary

### 10.3 Tối ưu giám sát
- **Giám sát thời gian thực**: Giám sát trạng thái vận hành của hệ thống theo thời gian thực
- **Cơ chế cảnh báo**: Thiết lập ngưỡng cảnh báo hợp lý
- **Tổng hợp log**: Tổng hợp log từ nhiều máy chủ
- **Phân tích hiệu năng**: Định kỳ phân tích hiệu năng hệ thống

### 10.4 Tối ưu bảo mật
- **Quyền tối thiểu**: Tuân thủ nguyên tắc quyền tối thiểu
- **Cập nhật định kỳ**: Định kỳ cập nhật các gói phụ thuộc và hệ thống
- **Quét bảo mật**: Định kỳ quét bảo mật
- **Ứng phó khẩn cấp**: Xây dựng cơ chế ứng phó khẩn cấp với sự cố bảo mật

## 11. Tổng kết

Tài liệu này mô tả quy trình triển khai frontend trang quản trị trong dự án CRMEB, bao gồm các khâu build, triển khai, cấu hình, v.v., cùng các phương pháp hay nhất (best practice) liên quan và giải pháp cho các vấn đề thường gặp.

Tuân thủ quy trình và quy chuẩn triển khai trong tài liệu này giúp đảm bảo việc triển khai frontend diễn ra suôn sẻ và hệ thống vận hành ổn định, đồng thời nâng cao hiệu quả triển khai và tính bảo mật của hệ thống.

Cùng với sự phát triển của dự án và sự thay đổi của công nghệ, quy trình triển khai cũng cần liên tục được tối ưu và điều chỉnh để đáp ứng các nhu cầu nghiệp vụ và thách thức kỹ thuật mới.