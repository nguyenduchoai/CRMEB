# Mô tả quy chuẩn phát triển

## 🎯 Mở rộng dựa trên quy chuẩn hiện có

### 1. Quy chuẩn đặt tên và sử dụng tầng Service
```php
// ✅ Ví dụ đúng
class UserServices extends BaseServices {
    public function getUserInfo(int $uid) {}
    public function updateUserStatus(int $uid, int $status) {}
}

// ❌ Tránh thao tác trực tiếp với cơ sở dữ liệu trong Controller
// Không viết như thế này: Db::name('user')->where('id', $uid)->update($data)
```

### 2. Phân chia trách nhiệm của tầng DAO
```php
// ✅ DAO chỉ đảm nhận CRUD cơ bản
class UserDao extends BaseDao {
    public function getById(int $id) {}
    public function updateById(int $id, array $data) {}
}

// Service gọi DAO
class UserServices {
    public function complexBusiness() {
        $user = $this->dao->getById($id);
        // Xử lý logic nghiệp vụ
        $this->dao->updateById($id, $data);
    }
}
```

### 3. Tiêu chuẩn xử lý ngoại lệ
```php
// Sử dụng lớp ngoại lệ riêng của dự án
throw new ApiException('Thông tin lỗi', 400);
throw new AdminException('Lỗi phía quản trị', 500);

// Định dạng trả về thống nhất
return $this->success($data);
return $this->fail('Thông tin lỗi');
```

### 4. Vị trí kiểm tra tham số
```php
// Đặt phần xác thực vào tệp tương ứng trong thư mục validate
// adminapi/validate/user/UserValidata.php
class UserValidata extends Validate {
    protected $rule = [
        'nickname' => 'require|max:25',
        'phone' => 'require|mobile'
    ];
}
```

### 5. Bổ sung tiêu chuẩn chú thích
```php
/**
 * Lấy thông tin người dùng (bao gồm thông tin mở rộng)
 * @param int $uid ID người dùng
 * @param array $with Truy vấn liên kết ['order', 'address']
 * @return array
 * @throws ApiException
 */
public function getUserDetail(int $uid, array $with = []) {
    // Logic nghiệp vụ
}
```

## 🔧 Quy chuẩn sử dụng lớp tiện ích
- Sử dụng các lớp tiện ích trong `crmeb\utils\`
- Lấy cấu hình bằng `SystemConfigService`
- Thao tác bộ nhớ đệm (cache) bằng `CacheService`
- Tải lên tệp bằng `UploadService`

## 📝 Quy chuẩn commit code
1. Chạy kiểm thử trước khi commit
2. Định dạng thông điệp commit: `[loại] mô tả`
   - `[feat] Thêm chức năng điểm thưởng cho người dùng`
   - `[fix] Sửa lỗi cập nhật trạng thái đơn hàng`
   - `[docs] Cập nhật tài liệu API`
   - `[style] Điều chỉnh định dạng code`
3. Tránh commit khối lớn, mỗi commit chỉ tập trung vào một chức năng hoặc một bản sửa lỗi

## 🚧 Quản lý nhánh
- `master` - Code môi trường production
- `develop` - Nhánh phát triển
- `feature/xxx` - Nhánh phát triển tính năng
- `hotfix/xxx` - Nhánh sửa lỗi khẩn cấp

## 🧪 Quy chuẩn kiểm thử
1. Kiểm thử đơn vị bao phủ logic nghiệp vụ cốt lõi
2. Kiểm thử tích hợp bao phủ các API
3. Dữ liệu kiểm thử được tạo bằng mẫu Factory
4. Đặt tên test case: `testMethodNameWhenConditionThenResult`

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
