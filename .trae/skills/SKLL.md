# Tài liệu mô tả skill của CRMEB

## Danh sách skill

### 1. Các skill hiện có

| Tên skill | Thư mục | Mô tả |
|---------|------|------|
| Mô tả frontend trang quản trị | admin-element/ | Mô tả skill phát triển frontend trang quản trị |
| Hướng dẫn tạo tài liệu phát triển bằng AI | dev-docs-generate/ | Mô tả skill tạo tài liệu phát triển bằng AI |
| Quy ước commit Git | git-commit/ | Chuẩn hóa định dạng thông điệp commit git, đảm bảo thêm đúng tiền tố khi commit tệp ở các thư mục khác nhau |
| Hướng dẫn phát triển backend PHP | php-api/ | Mô tả skill phát triển backend PHP, bao gồm phát triển API, thiết kế cơ sở dữ liệu, v.v. |
| Hướng dẫn phát triển phía di động | uniapp/ | Mô tả phát triển uniapp cho di động |

## Quy chuẩn tài liệu skill

### 1. Cấu trúc file

```
.trae/skills/
├── Thư mục skill/
│   ├── SKILL.md           # Tài liệu mô tả skill
│   └── references/        # Thư mục tài liệu tham khảo (tùy chọn)
│       └── *.md           # Tài liệu tham khảo liên quan
└── SKLL.md               # Tài liệu danh sách skill
```

### 2. Quy chuẩn định dạng SKILL.md

#### 2.1 Phần đầu tài liệu

```yaml
---
name: Tên skill
description: Mô tả skill
---
```

#### 2.2 Cấu trúc tài liệu

1. **Tiêu đề**: Tên skill
2. **Mô tả cơ chế tự động kích hoạt**:
   - Điều kiện kích hoạt (kích hoạt theo thao tác, kích hoạt theo nội dung, kích hoạt theo lệnh)
   - Tình huống áp dụng (tình huống cốt lõi, tình huống hỗ trợ)
   - Cơ chế kích hoạt (thời điểm gọi, tần suất, mức ưu tiên)
   - Hành vi sau khi kích hoạt (tự động phân tích, hiển thị, đề xuất)
3. **Nội dung cốt lõi**: Tài liệu kỹ thuật liên quan đến skill
4. **Thực tiễn tốt nhất**: Khuyến nghị về phát triển và sử dụng
5. **Vấn đề thường gặp**: Các vấn đề thường gặp và giải pháp
6. **Tài liệu tham khảo**: Tài liệu, công cụ liên quan, v.v.

### 3. Cơ chế kích hoạt skill

#### 3.1 Cách thức kích hoạt

- **Kích hoạt theo thao tác**: Kích hoạt khi thao tác với tệp/thư mục
- **Kích hoạt theo nội dung**: Kích hoạt khi nội dung tệp chứa từ khóa
- **Kích hoạt theo lệnh**: Kích hoạt khi thực thi lệnh cụ thể

#### 3.2 Mức ưu tiên kích hoạt

- **Ưu tiên cao nhất**: Skill cốt lõi của hệ thống
- **Ưu tiên cao**: Skill về cấu trúc mã nguồn
- **Ưu tiên trung bình**: Skill phát triển (frontend, backend, di động)
- **Ưu tiên thấp**: Skill công cụ hỗ trợ

## Hướng dẫn sử dụng skill

### 1. Dành cho lập trình viên

1. **Kích hoạt khi duyệt**: Tự động kích hoạt skill tương ứng khi duyệt thư mục liên quan
2. **Kích hoạt khi chỉnh sửa**: Tự động kích hoạt skill tương ứng khi chỉnh sửa tệp liên quan
3. **Kích hoạt theo lệnh**: Tự động kích hoạt skill tương ứng khi thực thi lệnh liên quan
4. **Gọi thủ công**: Gọi thủ công skill tương ứng thông qua plugin của IDE

### 2. Bảo trì skill

1. **Cập nhật định kỳ**: Cập nhật tài liệu skill theo thay đổi của dự án
2. **Mở rộng skill**: Thêm skill mới theo tính năng mới
3. **Tối ưu skill**: Tối ưu cơ chế kích hoạt và nội dung của skill

## Mô tả thư mục skill

### 1. admin-element/
- **Chức năng**: Skill liên quan đến phát triển frontend trang quản trị
- **Tình huống kích hoạt**: Kích hoạt khi duyệt hoặc chỉnh sửa tệp frontend trang quản trị
- **Nội dung cốt lõi**: Phát triển component frontend, bố cục trang, logic tương tác, v.v.

### 2. dev-docs-generate/
- **Chức năng**: Skill liên quan đến tạo tài liệu phát triển bằng AI
- **Tình huống kích hoạt**: Kích hoạt khi cần tạo tài liệu phát triển
- **Nội dung cốt lõi**: Thiết kế cấu trúc tài liệu, tạo nội dung, quy chuẩn định dạng, v.v.

### 3. git-commit/
- **Chức năng**: Skill liên quan đến quy ước commit Git
- **Tình huống kích hoạt**: Kích hoạt khi thực hiện thao tác commit Git
- **Nội dung cốt lõi**: Định dạng thông điệp commit, quy ước tiền tố thư mục, thực tiễn tốt nhất, v.v.

### 4. php-api/
- **Chức năng**: Skill liên quan đến phát triển backend PHP
- **Tình huống kích hoạt**: Kích hoạt khi duyệt hoặc chỉnh sửa mã nguồn backend
- **Nội dung cốt lõi**: Phát triển API, thiết kế cơ sở dữ liệu, hiện thực logic nghiệp vụ, v.v.
- **Tài liệu tham khảo**: Bao gồm các tài liệu chi tiết về quy trình phát triển API, thiết kế cơ sở dữ liệu, quy chuẩn mã nguồn, v.v.

### 5. uniapp/
- **Chức năng**: Skill liên quan đến phát triển phía di động
- **Tình huống kích hoạt**: Kích hoạt khi duyệt hoặc chỉnh sửa mã nguồn phía di động
- **Nội dung cốt lõi**: Phát triển component UniApp, route trang, tương tác dữ liệu, v.v.

## Tổng kết

Hệ thống skill là công cụ hỗ trợ quan trọng trong phát triển dự án CRMEB. Nhờ tài liệu skill chuẩn hóa và cơ chế kích hoạt, hệ thống giúp lập trình viên nhanh chóng nắm được cấu trúc dự án, quy chuẩn phát triển và các thực tiễn tốt nhất, nâng cao hiệu suất phát triển và chất lượng mã nguồn.

Nên định kỳ bảo trì và cập nhật tài liệu skill, đảm bảo luôn đồng bộ với sự phát triển của dự án, mang lại sự hỗ trợ và hướng dẫn liên tục cho lập trình viên.