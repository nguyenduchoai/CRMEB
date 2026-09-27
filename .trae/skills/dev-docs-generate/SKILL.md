---
name: dev-docs-generate
description: Quy chuẩn tạo tài liệu phát triển, giúp tạo nhanh tài liệu phát triển và tự động đặt vào thư mục docs, giúp đội ngũ kỹ thuật nhanh chóng hiểu dự án và bắt tay vào phát triển
---

# Quy chuẩn viết tài liệu dự án CRMEB

## 0. Tình huống tự động gọi

### 0.1 Điều kiện kích hoạt

- **Khi duyệt thư mục**: Tự động được gọi khi duyệt các thư mục liên quan đến tài liệu
  - Kích hoạt khi mở thư mục `dev-docs/`
  - Kích hoạt khi mở thư mục `dev-docs/phpapi/`
  - Kích hoạt khi mở thư mục `dev-docs/admin/`
  - Kích hoạt khi mở thư mục `dev-docs/uniapp/`
  - Kích hoạt khi mở thư mục `dev-docs/nuxt/`
- **Khi tạo tài liệu**: Tự động được gọi khi tạo tệp tài liệu Markdown mới
- **Khi chỉnh sửa tài liệu**: Tự động được gọi khi chỉnh sửa tệp tài liệu hiện có
- **Kích hoạt theo từ khóa**: Tự động được gọi khi nội dung tài liệu chứa các từ khóa sau
  - `tài liệu`、`giải thích`、`hướng dẫn`、`sổ tay`、`quy chuẩn`
  - `API`、`API`、`triển khai`、`phát triển`、`yêu cầu`

### 0.2 Loại tệp áp dụng

- `.md` (tệp Markdown)
- `.txt` (tệp văn bản)
- `.doc`/`.docx` (tài liệu Word)
- `.pdf` (tài liệu PDF)

### 0.3 Mức ưu tiên gọi

- Khi nhiều skill được kích hoạt cùng lúc, skill quy chuẩn tài liệu có mức ưu tiên trung bình
- Chỉ được kích hoạt khi thao tác liên quan đến tài liệu
- Không ảnh hưởng đến việc sử dụng bình thường của các skill khác

## 1. Loại tài liệu

### 1.1 Tài liệu kỹ thuật

- **Tài liệu API**: Thiết kế API, mô tả tham số, định dạng trả về
- **Tài liệu phát triển**: Thiết kế kiến trúc, mô tả module, quy trình phát triển
- **Tài liệu triển khai**: Yêu cầu môi trường, các bước cài đặt, hướng dẫn cấu hình
- **Tài liệu endpoint**: Danh sách endpoint, tham số request, ví dụ kết quả trả về

### 1.2 Tài liệu nghiệp vụ

- **Tài liệu yêu cầu**: Mô tả chức năng, quy trình nghiệp vụ, cấu trúc dữ liệu
- **Tài liệu kiểm thử**: Test case, kết quả kiểm thử, báo cáo lỗi
- **Sổ tay người dùng**: Giới thiệu chức năng, hướng dẫn thao tác, câu hỏi thường gặp

## 2. Quy chuẩn định dạng

### 2.1 Quy chuẩn tên tệp

- Dùng chữ thường, phân tách bằng dấu gạch dưới
- Mô tả rõ nội dung tài liệu
- Ví dụ: `tai-lieu-api.md`, `huong-dan-trien-khai.md`

### 2.2 Cấp tiêu đề

- Dùng `#` để biểu thị cấp tiêu đề
- Tiêu đề cấp 1: Chủ đề tài liệu
- Tiêu đề cấp 2: Các chương chính
- Tiêu đề cấp 3: Nội dung chi tiết
- Dùng tối đa tiêu đề cấp 4

### 2.3 Định dạng văn bản

- Nội dung chính dùng phông SimSun/phông không chân (sans-serif), 14px
- Khối mã được bao bằng ```, chỉ rõ ngôn ngữ
- Danh sách được biểu thị bằng `-` hoặc `1.`
- Nội dung nhấn mạnh dùng `**in đậm**` hoặc `*in nghiêng*`

### 2.4 Quy chuẩn mã nguồn

- Khối mã bắt buộc phải chỉ rõ ngôn ngữ
- Thụt lề nhất quán, định dạng rõ ràng
- Thêm chú thích cho các đoạn mã quan trọng
- Ví dụ:
  ```php
  // Lấy thông tin người dùng
  public function getUserInfo($id) {
      return $this->where('id', $id)->find();
  }
  ```

### 2.5 Thư mục lưu trữ tài liệu

- Tài liệu được lưu vào thư mục `dev-docs`
- Tài liệu API backend lưu trong thư mục `dev-docs/phpapi`
- Tài liệu frontend trang quản trị ElementUI (Admin) lưu trong thư mục `dev-docs/admin`
- Tài liệu frontend di động UniApp (di động) lưu trong thư mục `dev-docs/uniapp`
- Tài liệu phía PC Nuxt (PC) lưu trong thư mục `dev-docs/nuxt`

## 3. Yêu cầu về nội dung

### 3.1 Cấu trúc rõ ràng

- Lời mở đầu: Mục đích tài liệu, phạm vi áp dụng
- Phần chính: Nội dung chi tiết, logic mạch lạc
- Kết luận: Tổng kết, kế hoạch tiếp theo
- Phụ lục: Tài liệu tham khảo, bảng thuật ngữ

### 3.2 Yêu cầu về ngôn ngữ

- Dùng ngôn ngữ ngắn gọn, chính xác
- Tránh gây hiểu nhầm, thống nhất thuật ngữ
- Tài liệu tiếng Việt dùng chính tả chuẩn
- Tài liệu tiếng Anh phải đúng ngữ pháp

### 3.3 Tính đầy đủ của nội dung

- Có đủ thông tin bối cảnh cần thiết
- Các bước rõ ràng, có thể thực hiện được
- Cung cấp ví dụ và ảnh chụp màn hình
- Ghi rõ phiên bản và ngày cập nhật

### 3.4 Khả năng bảo trì

- Cập nhật định kỳ, đảm bảo tính thời sự
- Dùng hệ thống quản lý phiên bản để quản lý tài liệu
- Ghi rõ tác giả và thông tin liên hệ
- Thuận tiện cho việc tìm kiếm và điều hướng

## 4. Công cụ tài liệu

### 4.1 Công cụ soạn thảo

- Khuyến nghị dùng định dạng Markdown
- Công cụ hỗ trợ: VS Code, Typora, Yuque
- Lưu trữ hình ảnh: Trong nội bộ dự án hoặc dịch vụ lưu trữ ảnh (image hosting)

### 4.2 Quản lý phiên bản

- Quản lý bằng Git cùng với mã nguồn
- Thông điệp commit rõ ràng, nêu rõ thay đổi của tài liệu
- Sao lưu định kỳ để tránh mất dữ liệu

## 5. Duyệt và phát hành

### 5.1 Quy trình duyệt

1. Tự kiểm tra sau khi soạn xong
2. Gửi cho người liên quan duyệt
3. Chỉnh sửa, hoàn thiện theo phản hồi
4. Xác nhận cuối cùng và phát hành

### 5.2 Quy chuẩn phát hành

- Kiểm tra định dạng và nội dung trước khi phát hành
- Ghi rõ số phiên bản tài liệu
- Thông báo cho người liên quan về việc cập nhật tài liệu
- Đảm bảo tài liệu có thể truy cập được

## 6. Mẫu tài liệu Markdown

```markdown
# Tiêu đề tài liệu

## 1. Lời mở đầu

### 1.1 Mục đích tài liệu
- Nêu mục đích biên soạn tài liệu

### 1.2 Phạm vi áp dụng
- Nêu phạm vi áp dụng của tài liệu

### 1.3 Định nghĩa thuật ngữ
- Giải thích các thuật ngữ chuyên môn dùng trong tài liệu

## 2. Nội dung chính

### 2.1 Mô tả chức năng
- Mô tả chi tiết các đặc tính chức năng

### 2.2 Phương án triển khai
- Nêu phương án triển khai kỹ thuật

### 2.3 Ví dụ code

```php
// Ví dụ mã
function example() {
    return true;
}
```

### 2.4 Các bước thực hiện

1. Thao tác bước 1
2. Thao tác bước 2
3. Thao tác bước 3

## 3. Kết luận

### 3.1 Tổng kết
- Tóm tắt nội dung chính của tài liệu

### 3.2 Kế hoạch tiếp theo
- Nêu kế hoạch công việc tiếp theo

## 4. Phụ lục

### 4.1 Tài liệu tham khảo
- Liệt kê các tài liệu và nguồn tham khảo

### 4.2 Thông tin liên hệ
- Cung cấp thông tin người liên hệ

---

**Phiên bản**: 1.0
**Tác giả**: Tác giả tài liệu
**Ngày cập nhật**: YYYY-MM-DD
```

## 7. Hướng dẫn cú pháp Markdown

### 7.1 Tiêu đề

```markdown
# Tiêu đề cấp 1
## Tiêu đề cấp 2
### Tiêu đề cấp 3
#### Tiêu đề cấp 4
```

### 7.2 Danh sách

- Mục danh sách không thứ tự 1
- Mục danh sách không thứ tự 2
  - Mục danh sách lồng nhau

1. Mục danh sách có thứ tự 1
2. Mục danh sách có thứ tự 2

### 7.3 Liên kết và hình ảnh

- [Văn bản liên kết](https://example.com)
- ![Mô tả hình ảnh](https://example.com/image.jpg)

### 7.4 Khối mã

```javascript
// JavaScript Code
console.log('Hello World');
```

### 7.5 Bảng

| Tiêu đề cột 1 | Tiêu đề cột 2 |
| ------ | ------ |
| Ô 1 | Ô 2 |
| Ô 3 | Ô 4 |

### 7.6 Trích dẫn

> Đây là một đoạn văn bản trích dẫn

## 8. Lưu ý

- Tránh dài dòng, làm nổi bật trọng tâm
- Giữ định dạng thống nhất
- Cập nhật tài liệu định kỳ
- Đảm bảo nội dung chính xác, không sai sót
- Giúp người khác dễ hiểu và dễ sử dụng
- Cuối tài liệu được tạo phải ghi rõ là do AI tạo

---

Các quy chuẩn trên áp dụng cho mọi hoạt động viết tài liệu của dự án CRMEB, nhằm đảm bảo chất lượng và tính nhất quán của tài liệu.

