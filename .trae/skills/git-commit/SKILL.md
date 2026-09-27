---
name: Quy ước commit Git
description: Chuẩn hóa định dạng thông điệp commit git, đảm bảo thêm đúng tiền tố khi commit tệp ở các thư mục khác nhau
---

# Quy ước commit Git

## 0. Mô tả cơ chế tự động kích hoạt

### 0.1 Điều kiện kích hoạt

#### 0.1.1 Kích hoạt theo thao tác
- **Khi duyệt tệp**: Tự động được gọi khi duyệt các thư mục sau
  - Kích hoạt khi mở thư mục `crmeb/`
  - Kích hoạt khi mở thư mục `docker-compose/`
  - Kích hoạt khi mở thư mục `dev-docs/`
  - Kích hoạt khi mở thư mục `template/`
- **Khi thao tác tệp**: Tự động được gọi khi thao tác với tệp trong các thư mục sau
  - Kích hoạt khi sửa tệp trong thư mục `crmeb/`
  - Kích hoạt khi sửa tệp trong thư mục `docker-compose/`
  - Kích hoạt khi sửa tệp trong thư mục `dev-docs/`
  - Kích hoạt khi sửa tệp trong thư mục `template/`
- **Khi thao tác Git**: Tự động được gọi khi thực thi các lệnh Git sau
  - `git add` (thêm tệp vào vùng staging)
  - `git commit` (commit thay đổi)
  - `git push` (push thay đổi)

#### 0.1.2 Kích hoạt theo nội dung
- **Kích hoạt theo từ khóa**: Tự động được gọi khi nội dung tệp chứa các từ khóa sau
  - Từ khóa Git: `git`, `commit`, `push`, `pull`, `branch`
  - Từ khóa commit: `commit`, `cập nhật`, `sửa lỗi`, `thêm`, `xóa`
  - Từ khóa thư mục: `crmeb`, `docker-compose`, `docs`, `template`

#### 0.1.3 Kích hoạt theo lệnh
- **Kích hoạt bằng lệnh terminal**: Tự động được gọi khi thực thi các lệnh sau
  - `git` (các lệnh liên quan đến Git)
  - `git commit` (lệnh commit)
  - `git add` (lệnh add)

### 0.2 Tình huống áp dụng

#### 0.2.1 Tình huống cốt lõi
- **Commit mã nguồn**: Khi commit thay đổi mã nguồn
- **Sửa tệp**: Khi sửa tệp của dự án
- **Thao tác thư mục**: Khi thao tác với thư mục dự án

#### 0.2.2 Tình huống hỗ trợ
- **Rà soát mã nguồn**: Khi rà soát các commit mã nguồn
- **Quản lý phiên bản**: Khi quản lý phiên bản dự án
- **Cộng tác nhóm**: Khi các thành viên trong nhóm cùng phối hợp phát triển

### 0.3 Cơ chế kích hoạt

#### 0.3.1 Thời điểm gọi
- **Kích hoạt tức thì**: Kích hoạt ngay khi lệnh Git được thực thi
- **Kích hoạt trễ**: Kích hoạt sau khi thao tác tệp 1 giây
- **Kích hoạt hàng loạt**: Gộp thành một lần kích hoạt khi thao tác file hàng loạt

#### 0.3.2 Tần suất gọi
- Duyệt tệp: kích hoạt tối đa một lần mỗi 10 giây
- Thao tác tệp: kích hoạt tối đa một lần mỗi 5 giây
- Lệnh Git: Tối đa mỗi 3 giây kích hoạt một lần

#### 0.3.3 Mức ưu tiên gọi
- **Mức ưu tiên**: Ưu tiên trung bình (3/5)
- **Xử lý tranh chấp**: Khi nhiều skill được kích hoạt cùng lúc
  - Ưu tiên cao nhất: skill cốt lõi của hệ thống
  - Ưu tiên cao: skill cấu trúc mã nguồn
  - Ưu tiên trung bình: Skill commit Git, skill backend PHP, skill frontend
  - Ưu tiên thấp: skill công cụ hỗ trợ

### 0.4 Hành vi sau khi kích hoạt

#### 0.4.1 Tự động phân tích
- **Phân tích thư mục**: Phân tích thư mục chứa tệp đang được thao tác
- **Phân tích thông điệp commit**: Phân tích xem định dạng thông điệp commit có đúng quy ước không
- **Kiểm tra tiền tố**: Kiểm tra thông điệp commit có chứa đúng tiền tố thư mục không

#### 0.4.2 Tự động hiển thị
- **Quy ước commit**: Hiển thị quy ước commit Git
- **Tiền tố thư mục**: Hiển thị tiền tố commit tương ứng với thư mục hiện tại
- **Định dạng mẫu**: Hiển thị ví dụ về định dạng thông điệp commit đúng

#### 0.4.3 Tự động đề xuất
- **Gợi ý tiền tố**: Đưa ra gợi ý tiền tố commit tương ứng với thư mục hiện tại
- **Gợi ý định dạng**: Đưa ra gợi ý về định dạng thông điệp commit
- **Thực tiễn tốt nhất**: Đưa ra khuyến nghị về thực tiễn tốt nhất khi commit Git

## 1. Quy ước định dạng thông điệp commit

### 1.1 Định dạng cơ bản
```
[Tiền tố thư mục] Mô tả commit

Giải thích chi tiết (tùy chọn)
```

### 1.2 Quy tắc tiền tố thư mục

| Thư mục | Tiền tố | Ví dụ |
|------|------|------|
| crmeb/ | [Thư mục chương trình] | [Thư mục chương trình] Sửa bug chức năng đăng nhập |
| docker-compose/ | 【DOCKER】 | [DOCKER] Cập nhật tệp cấu hình Docker |
| dev-docs/ | [Tài liệu phát triển] | [Tài liệu phát triển] Hoàn thiện tài liệu API |
| template/ | [Tệp frontend] | [Tệp frontend] Tối ưu style giao diện frontend |

### 1.3 Quy ước mô tả commit
- **Giới hạn độ dài**: Không quá 50 ký tự
- **Yêu cầu nội dung**: Ngắn gọn, rõ ràng, nêu nội dung chính của lần commit này
- **Cách dùng động từ**: Dùng động từ ở thì hiện tại, như “Thêm”, “Sửa”, “Cập nhật”, v.v.
- **Quy ước định dạng**: Viết hoa chữ cái đầu, không thêm dấu câu ở cuối

### 1.4 Quy ước phần mô tả chi tiết
- **Giới hạn độ dài**: Không quá 200 ký tự
- **Yêu cầu nội dung**: Trình bày chi tiết lý do của lần commit này, vấn đề được giải quyết, v.v.
- **Quy ước định dạng**: Mỗi dòng không quá 72 ký tự, dùng dòng trống để phân tách các đoạn

## 2. Ví dụ thông điệp commit

### 2.1 Ví dụ cho thư mục crmeb
```
[Thư mục chương trình] Thêm chức năng đăng ký người dùng

- Triển khai API đăng ký người dùng
- Thêm kiểm tra tham số đăng ký
- Tích hợp chức năng mã xác thực (OTP) qua SMS
```

### 2.2 Ví dụ cho thư mục docker-compose
```
【DOCKER】Tối ưu build image Docker

- Giảm dung lượng image
- Tăng tốc độ build
- Sửa lỗi khởi động container
```

### 2.3 Ví dụ cho thư mục docs
```
[Tài liệu phát triển] Cập nhật tài liệu API

- Bổ sung mô tả API mới
- Sửa lỗi mô tả tham số
- Thêm ví dụ response
```

### 2.4 Ví dụ cho thư mục template
```
[File frontend] Tối ưu giao diện trang đăng nhập

- Điều chỉnh bố cục form
- Làm đẹp kiểu nút
- Tối ưu thiết kế responsive
```

## 3. Thực tiễn tốt nhất

### 3.1 Tần suất commit
- **Chia nhỏ hợp lý**: Mỗi commit chỉ chứa một tính năng hoặc một bản sửa lỗi
- **Commit kịp thời**: Commit ngay sau khi hoàn thành một tính năng hoặc bản sửa lỗi
- **Tránh commit dồn**: Tránh commit một lượng lớn thay đổi không liên quan trong một lần

### 3.2 Chất lượng thông điệp commit
- **Rõ ràng, dễ hiểu**: Thông điệp commit cần nêu rõ nội dung thay đổi lần này
- **Mô tả chính xác**: Thông điệp commit cần phản ánh chính xác thay đổi của mã nguồn
- **Quy ước định dạng**: Tuân thủ nghiêm ngặt quy ước định dạng thông điệp commit

### 3.3 Quản lý nhánh
- **Nhánh chính**: Giữ nhánh chính ổn định, chỉ dùng để phát hành
- **Nhánh phát triển**: Phát triển tính năng trên nhánh phát triển
- **Nhánh tính năng**: Tạo nhánh tính năng (feature branch) riêng cho các tính năng lớn
- **Nhánh sửa lỗi**: Tạo nhánh sửa lỗi (hotfix) riêng cho các bug khẩn cấp

### 3.4 Rà soát mã nguồn
- **Tự rà soát**: Tự rà soát mã nguồn trước khi commit
- **Rà soát theo nhóm**: Các thay đổi quan trọng cần được cả nhóm rà soát mã nguồn
- **Tiêu chí rà soát**: Rà soát chất lượng mã nguồn, tính bảo mật, hiệu năng, v.v.

## 4. Sự cố thường gặp

### 4.1 Vấn đề về thông điệp commit
- **Thiếu tiền tố**: Quên thêm tiền tố thư mục
  - Cách giải quyết: Tham khảo quy tắc tiền tố thư mục, thêm đúng tiền tố
- **Mô tả quá dài**: Mô tả commit vượt quá 50 ký tự
  - Cách giải quyết: Rút gọn mô tả, làm nổi bật trọng tâm
- **Định dạng không đúng quy ước**: Định dạng thông điệp commit không tuân thủ quy ước
  - Cách giải quyết: Tham khảo quy ước định dạng thông điệp commit để sửa lại định dạng

### 4.2 Vấn đề về quản lý nhánh
- **Nhánh lộn xộn**: Quá nhiều nhánh hoặc đặt tên không đúng quy ước
  - Cách giải quyết: Định kỳ dọn dẹp các nhánh không dùng, đặt tên nhánh theo quy ước
- **Xung đột khi merge**: Phát sinh xung đột khi merge nhánh
  - Cách giải quyết: Kịp thời đồng bộ với nhánh chính để giảm khả năng xung đột

### 4.3 Vấn đề về tần suất commit
- **Commit quá dày**: Commit quá thường xuyên
  - Cách giải quyết: Sắp xếp các commit hợp lý, tránh commit vụn vặt
- **Commit quá thưa**: Thời gian dài không commit
  - Cách giải quyết: Commit thay đổi kịp thời, tránh mất mã nguồn

## 5. Các lệnh Git thường dùng

### 5.1 Lệnh cơ bản
- **git status**: Xem trạng thái hiện tại
- **git add <file>**: Thêm tệp vào vùng staging
- **git commit -m "[Tiền tố] Mô tả"**: Commit thay đổi
- **git push**: Đẩy thay đổi lên kho từ xa (remote)
- **git pull**: Kéo thay đổi từ kho từ xa (remote) về

### 5.2 Lệnh về nhánh
- **git branch**: Xem các nhánh
- **git checkout <branch>**: Chuyển nhánh
- **git checkout -b <branch>**: Tạo và chuyển sang nhánh mới
- **git merge <branch>**: Gộp (merge) nhánh

### 5.3 Lệnh xem lịch sử
- **git log**: Xem lịch sử commit
- **git show <commit>**: Xem chi tiết commit
- **git diff**: Xem nội dung thay đổi

## 6. Tài liệu tham khảo

### 6.1 Tài liệu chính thức
- [Tài liệu chính thức của Git](https://git-scm.com/doc)
- [Hướng dẫn Git của GitHub](https://guides.github.com/introduction/git-handbook/)

### 6.2 Tài nguyên học tập
- [Pro Git](https://git-scm.com/book/zh/v2)
- [Chiến lược quản lý nhánh Git](https://nvie.com/posts/a-successful-git-branching-model/)
- [Quy ước thông điệp commit](https://chris.beams.io/posts/git-commit/)

### 6.3 Công cụ khuyên dùng
- **Git GUI**: GitHub Desktop、SourceTree
- **Git client**: GitKraken, Tower
- **Lưu trữ mã nguồn**: GitHub, GitLab, Gitee

### 6.4 Tài nguyên khác
- Thực tiễn tốt nhất cho quy trình làm việc (workflow) với Git
- Quy ước sử dụng Git trong nhóm
- Hướng dẫn quy trình rà soát mã nguồn