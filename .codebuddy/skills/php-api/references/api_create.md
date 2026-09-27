# Tài liệu quy trình phát triển API

## 1. Tổng quan

Tài liệu này mô tả quy trình phát triển API đầy đủ trong dự án CRMEB, bao gồm các giai đoạn phân tích yêu cầu, thiết kế, phát triển, kiểm thử, viết tài liệu, phát hành, giám sát và bảo trì. Quy trình này nhằm chuẩn hóa quá trình phát triển API, nâng cao chất lượng và khả năng bảo trì của API.

## 2. Phân tích yêu cầu và thiết kế

### 2.1 Tìm hiểu yêu cầu

1. **Thu thập yêu cầu**: Thu thập các yêu cầu API do bộ phận nghiệp vụ hoặc quản lý sản phẩm (PM) đưa ra
2. **Phân tích yêu cầu**: Phân tích tính khả thi, mức độ ưu tiên và phạm vi ảnh hưởng của yêu cầu
3. **Xác nhận yêu cầu**: Xác nhận chi tiết yêu cầu với bên đưa ra yêu cầu, đảm bảo hiểu thống nhất
4. **Đánh giá rủi ro**: Đánh giá các rủi ro có thể gặp phải trong quá trình hiện thực yêu cầu

### 2.2 Thiết kế tài nguyên

1. **Thiết kế model dữ liệu**: Thiết kế model dữ liệu và cấu trúc bảng cơ sở dữ liệu liên quan đến API
2. **Định nghĩa tài nguyên**: Định nghĩa các tài nguyên mà API thao tác và quan hệ giữa các tài nguyên
3. **Trường dữ liệu**: Xác định các trường và kiểu dữ liệu của tài nguyên
4. **Quan hệ liên kết**: Xác định quan hệ liên kết giữa các tài nguyên

### 2.3 Thiết kế API

1. **Thiết kế URL**: Thiết kế URL tuân theo quy chuẩn RESTful
   - Dùng dạng số nhiều, ví dụ `/api/v1/users` thay vì `/api/v1/user`
   - Dùng tên có ngữ nghĩa, ví dụ `/api/v1/orders` thay vì `/api/v1/getOrders`
   - Phân cấp URL hợp lý, thường không quá 3 cấp

2. **Thiết kế phương thức request**: Chọn phương thức request HTTP phù hợp
   - `GET`: Lấy tài nguyên
   - `POST`: Tạo tài nguyên
   - `PUT`: Cập nhật tài nguyên
   - `DELETE`: Xóa tài nguyên
   - `PATCH`: Cập nhật một phần tài nguyên

3. **Thiết kế tham số**: Thiết kế tham số request
   - **Tham số đường dẫn**: Dùng để định danh tài nguyên, ví dụ `/api/v1/users/:id`
   - **Tham số truy vấn**: Dùng để lọc, sắp xếp, phân trang, ví dụ `?page=1&limit=10`
   - **Tham số request body**: Dùng để tạo hoặc cập nhật tài nguyên, dùng định dạng JSON
   - **Tham số request header**: Dùng cho xác thực, phân quyền, v.v.

4. **Thiết kế phản hồi**: Thiết kế định dạng phản hồi thống nhất
   - Dùng `app('json')->success()` để trả về phản hồi thành công
   - Dùng `app('json')->fail()` để trả về phản hồi lỗi
   - Các trường phản hồi thống nhất: `status`, `msg`, `data`, phản hồi lỗi có thêm trường `code`
   - Phản hồi phân trang gồm các trường `total`, `page`, `limit`, `list`

### 2.4 Thiết kế phân quyền

1. **Phương thức xác thực**: Chọn phương thức xác thực phù hợp
   - Xác thực JWT
   - Xác thực OAuth2
   - Xác thực API Key

2. **Thiết kế phân quyền**: Thiết kế quyền truy cập API
   - Kiểm soát truy cập dựa trên vai trò (RBAC)
   - Kiểm soát truy cập dựa trên tài nguyên (RBAC)
   - Kiểm soát quyền ở cấp API

3. **Kiểm soát truy cập**: Hiện thực kiểm soát truy cập cho API
   - Dùng middleware để kiểm tra quyền
   - Cơ chế danh sách trắng (whitelist) cho API
   - Giới hạn tần suất gọi API (rate limit)

### 2.5 Thiết kế lỗi

1. **Tình huống lỗi**: Định nghĩa các tình huống lỗi mà API có thể gặp
2. **Mã lỗi**: Chọn hoặc đăng ký mã lỗi phù hợp từ tài liệu `error_code.md`
3. **Thông báo lỗi**: Thiết kế thông báo lỗi rõ ràng, chính xác
4. **Xử lý lỗi**: Thiết kế cơ chế xử lý lỗi thống nhất

## 3. Phát triển và hiện thực

### 3.1 Tạo route

1. **Định nghĩa route**: Định nghĩa route API trong file route
2. **Nhóm route**: Dùng nhóm route để tổ chức các API liên quan
3. **Middleware**: Thêm các middleware cần thiết cho route
4. **Đặt tên route**: Đặt tên cho route để thuận tiện tạo URL

**Code mẫu**:

```php
// api/v1/route.php
use think\facade\Route;

// API nhóm theo phiên bản
Route::group('v1', function () {
    // Route liên quan đến người dùng
    Route::group('users', function () {
        Route::get('', 'User/index'); // Lấy danh sách người dùng
        Route::post('', 'User/save'); // Tạo người dùng
        Route::get(':id', 'User/read'); // Lấy chi tiết người dùng
        Route::put(':id', 'User/update'); // Cập nhật người dùng
        Route::delete(':id', 'User/delete'); // Xóa người dùng
    })->middleware(['auth', 'permission']);
})->middleware(['cors', 'jwt']);
```

### 3.2 Hiện thực controller

1. **Tạo controller**: Tạo lớp controller cho API
2. **Kế thừa lớp cơ sở**: Kế thừa lớp controller cơ sở của dự án
3. **Hiện thực phương thức**: Hiện thực các phương thức API
4. **Kiểm tra tham số**: Dùng validator để kiểm tra tham số request
5. **Logic nghiệp vụ**: Hiện thực logic nghiệp vụ của API
6. **Trả về phản hồi**: Dùng phương thức phản hồi thống nhất để trả về phản hồi

**Code mẫu**:

```php
// app/api/controller/v1/User.php
namespace app\api\controller\v1;

use app\BaseController;
use app\validate\User as UserValidate;
use app\services\UserServices;

class User extends BaseController
{
    protected $userServices;
    
    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
    }
    
    /**
     * Lấy danh sách người dùng
     */
    public function index()
    {
        $params = $this->request->param();
        $list = $this->userServices->getUserList($params);
        return app('json')->success('Lấy danh sách người dùng thành công', $list);
    }
    
    /**
     * Tạo người dùng
     */
    public function save()
    {
        $data = $this->request->post();
        // Xác thực tham số
        $this->validate($data, UserValidate::class);
        // Logic nghiệp vụ
        $result = $this->userServices->createUser($data);
        // Trả về phản hồi
        return app('json')->success('Tạo người dùng thành công', $result);
    }
    
    /**
     * Lấy chi tiết người dùng
     */
    public function read($id)
    {
        $user = $this->userServices->getUserById($id);
        if (!$user) {
            return app('json')->fail(400005); // Người dùng không tồn tại
        }
        return app('json')->success('Lấy chi tiết người dùng thành công', $user);
    }
}
```

### 3.3 Kiểm tra tham số

1. **Tạo validator**: Tạo validator cho tham số API
2. **Định nghĩa quy tắc**: Định nghĩa các quy tắc kiểm tra tham số
3. **Thông báo lỗi**: Định nghĩa thông báo lỗi khi kiểm tra, dùng mã lỗi
4. **Gọi validator**: Gọi validator trong controller

**Code mẫu**:

```php
// app/validate/User.php
namespace app\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'username' => 'require|length:3,20|unique:user',
        'password' => 'require|length:6,20',
        'email' => 'email|unique:user',
        'mobile' => 'mobile|unique:user',
        'status' => 'in:0,1',
    ];
    
    protected $message = [
        'username.require' => 400033, // Vui lòng điền tài khoản quản trị viên
        'username.length' => 400762, // Tài khoản và mật khẩu phải dài từ 6 đến 32 ký tự
        'username.unique' => 400001, // Tên người dùng đã tồn tại
        'password.require' => 400020, // Mật khẩu là bắt buộc
        'password.length' => 400762, // Tài khoản và mật khẩu phải dài từ 6 đến 32 ký tự
        'email.email' => 400003, // Email đã được đăng ký
        'email.unique' => 400003, // Email đã được đăng ký
        'mobile.mobile' => 400319, // Vui lòng nhập đúng số CCCD/CMND
        'mobile.unique' => 400002, // Số điện thoại đã được đăng ký
        'status.in' => 400751, // Trạng thái phải là số nguyên trong khoảng 0-1
    ];
}
```

### 3.4 Hiện thực logic nghiệp vụ

1. **Tầng service**: Đóng gói logic nghiệp vụ vào tầng service
2. **Tầng model**: Đóng gói truy cập dữ liệu vào tầng model
3. **Xử lý transaction**: Nghiệp vụ phức tạp cần dùng transaction
4. **Xử lý lỗi**: Xử lý thống nhất các lỗi logic nghiệp vụ

**Code mẫu**:

```php
// app/services/UserServices.php
namespace app\services;

use app\model\User;
use crmeb\basic\BaseServices;

class UserServices extends BaseServices
{
    protected $userModel;
    
    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }
    
    /**
     * Lấy danh sách người dùng
     */
    public function getUserList(array $params)
    {
        $page = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 10;
        $keyword = $params['keyword'] ?? '';
        
        $query = $this->userModel->where('is_deleted', 0);
        
        if ($keyword) {
            $query->where('username|nickname|email|mobile', 'like', "%$keyword%");
        }
        
        $list = $query->page($page, $limit)->select();
        $total = $query->count();
        
        return [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'list' => $list
        ];
    }
    
    /**
     * Tạo người dùng
     */
    public function createUser(array $data)
    {
        // Mã hóa mật khẩu
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['create_time'] = time();
        $data['update_time'] = time();
        
        return $this->userModel->save($data);
    }
}
```

### 3.5 Xử lý lỗi

1. **Dùng mã lỗi**: Ưu tiên dùng mã lỗi được định nghĩa trong `error_code.md`
2. **Trả lỗi thống nhất**: Dùng `app('json')->fail()` để trả về lỗi
3. **Xử lý ngoại lệ**: Dùng try-catch để xử lý ngoại lệ
4. **Ghi log**: Ghi log lỗi

**Code mẫu**:

```php
// Cách dùng đúng
return app('json')->fail(410025); // Tài khoản hoặc mật khẩu không đúng

// Cách dùng không khuyến nghị
return app('json')->fail('Tài khoản hoặc mật khẩu không đúng');

// Dùng mã lỗi và truyền thêm dữ liệu
return app('json')->fail(400086, [], ['field' => 'username']);

// Xử lý ngoại lệ
try {
    // Logic nghiệp vụ
} catch (\Exception $e) {
    // Ghi log
    app('log')->error($e->getMessage());
    // Trả về lỗi
    return app('json')->fail(500); // Lỗi hệ thống
}
```

## 4. Kiểm thử và xác minh

### 4.1 Kiểm thử đơn vị (unit test)

1. **Viết unit test**: Viết unit test cho API
2. **Framework kiểm thử**: Dùng framework kiểm thử PHPUnit
3. **Độ bao phủ test**: Nâng cao độ bao phủ test (test coverage)
4. **CI/CD**: Tích hợp vào quy trình CI/CD

**Code mẫu**:

```php
// tests/api/UserTest.php
namespace tests\api;

use think\testing\TestCase;

class UserTest extends TestCase
{
    public function testIndex()
    {
        $response = $this->get('/api/v1/users');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'msg',
            'data' => [
                'total',
                'page',
                'limit',
                'list' => [
                    '*' => [
                        'id',
                        'username',
                        'nickname',
                        'email',
                        'mobile',
                        'status'
                    ]
                ]
            ]
        ]);
    }
    
    public function testSave()
    {
        $data = [
            'username' => 'testuser',
            'password' => '123456',
            'email' => 'test@example.com',
            'mobile' => '13800138000',
            'status' => 1
        ];
        
        $response = $this->post('/api/v1/users', $data);
        $response->assertStatus(200);
        $response->assertJson(['status' => 200, 'msg' => 'Tạo người dùng thành công']);
    }
}
```

### 4.2 Kiểm thử tích hợp

1. **Kiểm thử tích hợp**: Kiểm thử việc tích hợp giữa API và các module khác
2. **Kiểm thử cơ sở dữ liệu**: Kiểm thử các thao tác của API trên cơ sở dữ liệu
3. **Kiểm thử cache**: Kiểm thử cơ chế cache của API
4. **Kiểm thử hàng đợi**: Kiểm thử việc tích hợp giữa API và hàng đợi

### 4.3 Kiểm thử API

1. **Kiểm thử bằng công cụ**: Dùng các công cụ như Postman, Apifox để kiểm thử API
2. **Test case**: Viết đầy đủ các test case
3. **Kiểm thử biên**: Kiểm thử các trường hợp biên
4. **Kiểm thử hiệu năng**: Kiểm thử hiệu năng của API

### 4.4 Kiểm thử bảo mật

1. **Kiểm thử xác thực**: Kiểm thử cơ chế xác thực của API
2. **Kiểm thử phân quyền**: Kiểm thử cơ chế phân quyền của API
3. **Kiểm thử injection**: Kiểm thử các vấn đề bảo mật như SQL injection, XSS
4. **Kiểm thử giới hạn tần suất**: Kiểm thử giới hạn tần suất (rate limit) của API

## 5. Viết tài liệu

### 5.1 Tài liệu API

1. **Viết tài liệu API**: Viết tài liệu API chi tiết
2. **Công cụ tài liệu**: Dùng các công cụ như Swagger, Apifox để tạo tài liệu
3. **Nội dung tài liệu**: Bao gồm URL, phương thức request, tham số, phản hồi, mã lỗi, v.v.
4. **Ví dụ**: Cung cấp ví dụ request và phản hồi

### 5.2 Tài liệu mã lỗi

1. **Ghi nhận mã lỗi**: Ghi lại các mã lỗi mà API sử dụng trong tài liệu `error_code.md`
2. **Quy chuẩn mã lỗi**: Tuân theo quy chuẩn mã lỗi của dự án
3. **Mô tả mã lỗi**: Mô tả rõ ý nghĩa và tình huống sử dụng của mã lỗi

### 5.3 Lịch sử thay đổi

1. **Ghi nhận thay đổi**: Ghi lại lịch sử thay đổi của API
2. **Loại thay đổi**: Bao gồm thêm mới, chỉnh sửa, xóa, v.v.
3. **Phạm vi ảnh hưởng**: Ghi lại phạm vi ảnh hưởng của thay đổi
4. **Quản lý phiên bản**: Quản lý phiên bản của API

## 6. Phát hành

### 6.1 Rà soát code

1. **Rà soát code**: Tiến hành rà soát code (code review), đảm bảo chất lượng code
2. **Tiêu chuẩn rà soát**: Tuân theo quy chuẩn code của dự án
3. **Rà soát bảo mật**: Rà soát tính bảo mật của code
4. **Rà soát hiệu năng**: Rà soát hiệu năng của code

### 6.2 Xác minh trên môi trường test

1. **Triển khai thử nghiệm**: Triển khai API lên môi trường test
2. **Xác minh chức năng**: Xác minh chức năng của API
3. **Xác minh hiệu năng**: Xác minh hiệu năng của API
4. **Xác minh bảo mật**: Xác minh tính bảo mật của API

### 6.3 Phát hành canary (gray release)

1. **Chiến lược canary**: Xây dựng chiến lược phát hành canary
2. **Triển khai canary**: Triển khai API theo hình thức canary
3. **Giám sát, theo dõi**: Giám sát tình hình hoạt động của API
4. **Tăng dần lưu lượng**: Tăng dần lưu lượng cho bản canary

### 6.4 Chính thức đưa vào vận hành

1. **Chuẩn bị phát hành**: Chuẩn bị các tài nguyên cần thiết cho việc phát hành
2. **Triển khai chính thức**: Triển khai chính thức API
3. **Xác minh sau phát hành**: Xác minh API hoạt động bình thường
4. **Đăng thông báo**: Đăng thông báo phát hành API

## 7. Giám sát và bảo trì

### 7.1 Giám sát

1. **Giám sát hiệu năng**: Giám sát thời gian phản hồi, QPS, v.v. của API
2. **Giám sát lỗi**: Giám sát tỷ lệ lỗi, loại lỗi, v.v. của API
3. **Giám sát tính khả dụng**: Giám sát tính khả dụng của API
4. **Giám sát log**: Giám sát log truy cập và log lỗi của API

### 7.2 Log

1. **Log truy cập**: Ghi log truy cập của API
2. **Log lỗi**: Ghi log lỗi của API
3. **Log truy vấn chậm**: Ghi log truy vấn chậm của API
4. **Phân tích log**: Phân tích log định kỳ

### 7.3 Tối ưu

1. **Tối ưu hiệu năng**: Tối ưu hiệu năng API dựa trên dữ liệu giám sát
2. **Tối ưu bảo mật**: Tăng cường bảo mật cho API
3. **Tối ưu chức năng**: Tối ưu các chức năng của API
4. **Tối ưu code**: Tối ưu code của API

### 7.4 Bảo trì

1. **Bảo trì định kỳ**: Bảo trì API định kỳ
2. **Cập nhật phiên bản**: Cập nhật phiên bản của API
3. **Vá lỗ hổng**: Vá các lỗ hổng của API
4. **Cập nhật tài liệu**: Cập nhật tài liệu của API

## 8. Thực tiễn tốt nhất

### 8.1 Thực tiễn tốt nhất khi thiết kế

1. **Tuân theo quy chuẩn RESTful**: Áp dụng quy chuẩn thiết kế RESTful API
2. **Quy chuẩn đặt tên thống nhất**: Sử dụng quy chuẩn đặt tên thống nhất
3. **Thiết kế URL hợp lý**: Thiết kế URL ngắn gọn, có ngữ nghĩa
4. **Định dạng phản hồi thống nhất**: Sử dụng định dạng phản hồi thống nhất
5. **Mã lỗi rõ ràng**: Sử dụng mã lỗi rõ ràng

### 8.2 Thực tiễn tốt nhất khi phát triển

1. **Kiến trúc phân tầng**: Tuân theo thiết kế kiến trúc phân tầng
2. **Dependency Injection**: Sử dụng Dependency Injection
3. **Kiểm tra tham số**: Kiểm tra nghiêm ngặt tham số request
4. **Xử lý transaction**: Sử dụng transaction hợp lý
5. **Chiến lược cache**: Sử dụng cache hợp lý

### 8.3 Thực tiễn tốt nhất khi kiểm thử

1. **Kiểm thử tự động**: Viết kiểm thử tự động
2. **Độ bao phủ test**: Nâng cao độ bao phủ test (test coverage)
3. **Kiểm thử biên**: Kiểm thử các trường hợp biên
4. **Kiểm thử hiệu năng**: Kiểm thử hiệu năng của API
5. **Kiểm thử bảo mật**: Kiểm thử tính bảo mật của API

### 8.4 Thực tiễn tốt nhất khi phát hành

1. **Phát hành canary**: Áp dụng chiến lược phát hành canary
2. **Giám sát, theo dõi**: Giám sát chặt chẽ sau khi phát hành
3. **Cơ chế rollback**: Chuẩn bị sẵn cơ chế rollback
4. **Đăng thông báo**: Kịp thời đăng thông báo

### 8.5 Thực tiễn tốt nhất khi bảo trì

1. **Cảnh báo giám sát**: Thiết lập cảnh báo giám sát
2. **Kiểm tra định kỳ**: Kiểm tra API định kỳ
3. **Phân tích log**: Phân tích log định kỳ
4. **Tối ưu hiệu năng**: Liên tục tối ưu hiệu năng
5. **Cập nhật tài liệu**: Kịp thời cập nhật tài liệu

## 9. Sự cố thường gặp

### 9.1 Vấn đề truy cập chéo miền (CORS)

- **Vấn đề**: Chính sách cùng nguồn gốc (same-origin policy) của trình duyệt khiến request chéo miền bị thất bại
- **Giải pháp**: Áp dụng CORS (chia sẻ tài nguyên chéo miền), thiết lập header phản hồi phù hợp

### 9.2 Vấn đề xác thực và phân quyền

- **Vấn đề**: Xác thực, phân quyền API thất bại
- **Giải pháp**: Kiểm tra thông tin xác thực, đảm bảo token còn hiệu lực, quyền hạn chính xác

### 9.3 Vấn đề kiểm tra tham số

- **Vấn đề**: Kiểm tra tham số request thất bại
- **Giải pháp**: Kiểm tra tham số request, đảm bảo đúng định dạng, có đầy đủ các tham số bắt buộc

### 9.4 Vấn đề hiệu năng

- **Vấn đề**: Thời gian phản hồi của API quá lâu
- **Giải pháp**: Tối ưu phần hiện thực API, sử dụng cache, giảm số lần truy vấn cơ sở dữ liệu

### 9.5 Vấn đề bảo mật

- **Vấn đề**: API có lỗ hổng bảo mật
- **Giải pháp**: Tăng cường bảo mật cho API, ví dụ dùng HTTPS, kiểm tra tham số, chống SQL injection, v.v.

## 10. Tài liệu tham khảo

- [Hướng dẫn thiết kế RESTful API](https://restfulapi.net/)
- [Mã trạng thái HTTP](https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Status)
- [Thực tiễn tốt nhất khi thiết kế API](https://cloud.google.com/apis/design)
- [Đặc tả JSON API](https://jsonapi.org/)
- [Đặc tả OpenAPI](https://swagger.io/specification/)
- [Thực tiễn tốt nhất về bảo mật API](https://owasp.org/www-project-api-security/)
- [Tài liệu chính thức ThinkPHP 6](https://www.kancloud.cn/manual/thinkphp6_0)
- [Tài liệu chính thức PHPUnit](https://phpunit.de/)
- [Tài liệu chính thức Postman](https://learning.postman.com/)
