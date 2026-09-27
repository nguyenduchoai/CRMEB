# Tạo tệp skill quy ước commit Git

## Mục tiêu
Tạo một tệp skill mới dùng để chuẩn hóa định dạng thông điệp commit git, đảm bảo khi commit tệp ở các thư mục khác nhau đều thêm đúng tiền tố mô tả.

## Bước

### 1. Tạo thư mục skill
- Tạo thư mục con `git-commit/` trong thư mục `.trae/skills/`

### 2. Tạo tệp SKILL.md
Tạo tệp SKILL.md đáp ứng yêu cầu định dạng, gồm các nội dung sau:

#### 2.1 Thông tin cơ bản
- Tên skill: Quy ước commit Git
- Mô tả: Chuẩn hóa định dạng thông điệp commit git, đảm bảo thêm đúng tiền tố khi commit tệp ở các thư mục khác nhau

#### 2.2 Mô tả cơ chế tự động kích hoạt
- Điều kiện kích hoạt: Tự động được gọi khi duyệt hoặc thao tác với các tệp liên quan đến git
- Tình huống áp dụng: Nhắc nhở và kiểm tra quy ước trước khi thực hiện thao tác commit git

#### 2.3 Nội dung cốt lõi
- Quy ước định dạng thông điệp commit
- Mô tả tiền tố commit cho từng thư mục
- Ví dụ thông điệp commit
- Thực tiễn tốt nhất

#### 2.4 Quy tắc tiền tố commit
- **Thư mục crmeb**: Khi commit, thêm tiền tố `[Thư mục chương trình]`
- **Thư mục docker-compose**: Khi commit, thêm tiền tố `【DOCKER】`
- **Thư mục dev-docs**: Khi commit, thêm tiền tố `[Tài liệu phát triển]`
- **Thư mục template**: Khi commit, thêm tiền tố `[Tệp frontend]`

#### 2.5 Tài liệu tham khảo
- Tài liệu chính thức của Git
- Các lệnh git thường dùng
- Hướng dẫn quy ước thông điệp commit

### 3. Kiểm tra cấu trúc tệp
Đảm bảo tệp được tạo ở đúng vị trí, định dạng tuân thủ chuẩn quy định cho tệp skill.