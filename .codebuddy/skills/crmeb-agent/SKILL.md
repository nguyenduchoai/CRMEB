---
name: Agent hệ thống thương mại điện tử CRMEB
description: Trợ lý phát triển thông minh được thiết kế riêng cho hệ thống thương mại điện tử CRMEB, giúp lập trình viên hiểu kiến trúc, phát triển tính năng nhanh chóng và giải quyết vấn đề
---

# Agent hệ thống thương mại điện tử CRMEB

## 0. Mô tả cơ chế tự động kích hoạt

### 0.1 Điều kiện kích hoạt

#### 0.1.1 Kích hoạt theo thao tác
- **Khi duyệt file**: Tự động được gọi khi duyệt các thư mục cốt lõi của dự án CRMEB
  - Kích hoạt khi mở thư mục ứng dụng `crmeb/app/`
  - Kích hoạt khi mở thư mục thư viện lõi `crmeb/crmeb/`
  - Kích hoạt khi mở thư mục frontend `template/`
  - Kích hoạt khi duyệt thư mục chứa file cấu hình
- **Khi thao tác file**: Tự động được gọi khi thao tác trên các file của dự án CRMEB
  - Kích hoạt khi tạo controller, service, model
  - Kích hoạt khi sửa code nghiệp vụ cốt lõi
  - Kích hoạt khi sửa file cấu hình
- **Khi thao tác thư mục**: Tự động được gọi khi thao tác trên các thư mục của dự án
  - Kích hoạt khi tạo thư mục module mới
  - Kích hoạt khi đổi tên thư mục nghiệp vụ

#### 0.1.2 Kích hoạt theo nội dung
- **Kích hoạt theo từ khóa**: Tự động được gọi khi nội dung tệp chứa các từ khóa sau
  - Từ khóa thương mại điện tử: `đơn hàng`, `sản phẩm`, `người dùng`, `thanh toán`, `giỏ hàng`
  - Từ khóa marketing: `phiếu giảm giá`, `mua chung`, `săn giảm giá`, `flash sale`, `điểm thưởng`
  - Từ khóa hệ thống: `CRMEB`, `ThinkPHP`, `trang quản trị`, `di động`
- **Kích hoạt theo code**: Tự động được gọi khi xem code thuộc các loại cụ thể
  - Code controller (`Controller`)
  - Code tầng service (`Services`)
  - Code model (`Model`)
  - Component Vue phía frontend (`*.vue`)

#### 0.1.3 Kích hoạt theo lệnh
- **Kích hoạt bằng lệnh terminal**: Tự động được gọi khi thực thi các lệnh sau
  - `php think` (lệnh ThinkPHP)
  - `composer install/update` (quản lý dependency)
  - `npm run dev/build` (build frontend)
  - `php think queue:listen` (khởi động hàng đợi)
  - `php think workerman` (khởi động WebSocket)

### 0.2 Tình huống áp dụng

#### 0.2.1 Tình huống cốt lõi
- **Phát triển tính năng**: Khi phát triển module tính năng thương mại điện tử mới
- **Phát triển API**: Khi phát triển API giữa frontend và backend
- **Thiết kế cơ sở dữ liệu**: Khi thiết kế cấu trúc bảng dữ liệu
- **Khắc phục sự cố**: Khi xử lý các sự cố trong quá trình vận hành hệ thống

#### 0.2.2 Tình huống hỗ trợ
- **Rà soát code**: Khi rà soát chất lượng code
- **Tối ưu hiệu năng**: Khi tối ưu hiệu năng hệ thống
- **Tăng cường bảo mật**: Khi nâng cao tính bảo mật của hệ thống
- **Triển khai và vận hành**: Khi triển khai và bảo trì hệ thống

## 1. Kiến trúc hệ thống CRMEB

### 1.1 Kiến trúc tổng thể
- **Framework**: ThinkPHP 6.x (backend PHP) + Vue 2.x (frontend)
- **Mô hình kiến trúc**: Tách biệt frontend và backend + MVC + kiến trúc phân tầng Service + DAO
- **Cơ sở dữ liệu**: MySQL 5.7-8.0 (dùng tiền tố eb_)
- **Bộ nhớ đệm (cache)**: Redis (tùy chọn, dùng làm bộ nhớ đệm và hàng đợi)
- **Hàng đợi tin nhắn**: ThinkPHP Queue + Workerman
- **Giao tiếp thời gian thực**: Workerman WebSocket

### 1.2 Bộ công nghệ

#### Bộ công nghệ backend
```
- PHP: 7.1-7.4
- ThinkPHP: 6.x
- Composer: Quản lý phụ thuộc
- Workerman: Dịch vụ kết nối liên tục
- PHPUnit: Kiểm thử đơn vị (tùy chọn)
```

#### Bộ công nghệ frontend
```
Trang quản trị:
- Vue.js 2.x
- Element UI 2.15.6
- Vuex 3.0
- Vue Router 3.0
- Axios
- ECharts 4.8.0

Di động:
- UniApp
- uView UI
```

#### Dịch vụ bên thứ ba
```
- Thanh toán: WeChat Pay, Alipay
- Lưu trữ: Alibaba Cloud OSS, Tencent Cloud COS, Qiniu Cloud
- SMS: Alibaba Cloud SMS
- WeChat: OA WeChat, Mini Program
```

### 1.3 Cấu trúc thư mục

#### 1.3.1 Cấu trúc thư mục backend
```
crmeb/
├── app/                          # Thư mục ứng dụng
│   ├── adminapi/                 # API trang quản trị (chế độ đa ứng dụng)
│   │   ├── controller/           # Tầng controller
│   │   │   ├── system/           # Quản lý hệ thống
│   │   │   ├── product/          # Quản lý sản phẩm
│   │   │   ├── order/            # Quản lý đơn hàng
│   │   │   ├── user/             # Quản lý người dùng
│   │   │   └── marketing/        # Quản lý marketing
│   │   ├── middleware/           # Middleware
│   │   ├── route/                # Định nghĩa route
│   │   └── validate/             # Validator
│   ├── api/                      # API di động
│   ├── kefuapi/                  # API phía CSKH
│   ├── outapi/                   # API bên ngoài
│   ├── dao/                      # Tầng truy cập dữ liệu
│   │   ├── UserDao.php
│   │   ├── StoreOrderDao.php
│   │   └── ...
│   ├── model/                    # Tầng mô hình dữ liệu
│   │   ├── User.php
│   │   ├── StoreOrder.php
│   │   └── ...
│   ├── services/                 # Tầng service nghiệp vụ
│   │   ├── user/                 # Service người dùng
│   │   ├── product/              # Dịch vụ sản phẩm
│   │   ├── order/                # Service đơn hàng
│   │   ├── activity/             # Service marketing
│   │   ├── agent/                # Service tiếp thị liên kết
│   │   └── system/               # Service hệ thống
│   ├── jobs/                     # Tác vụ hàng đợi
│   ├── listener/                 # Event listener
│   ├── http/                     # Middleware HTTP
│   └── common.php                # Phương thức dùng chung
├── crmeb/                        # Thư mục framework lõi
│   ├── basic/                    # Thư viện lớp cơ sở
│   │   ├── BaseServices.php      # Lớp cơ sở của service
│   │   ├── BaseDao.php           # Lớp cơ sở DAO
│   │   ├── BaseModel.php         # Lớp cơ sở model
│   │   └── BaseController.php    # Lớp cơ sở của controller
│   ├── command/                  # Lệnh CLI
│   │   ├── Timer.php             # Tác vụ định kỳ
│   │   └── Swoole.php            # Service Swoole
│   ├── services/                 # Service cốt lõi
│   ├── traits/                   # Tập hợp Trait
│   ├── utils/                    # Công cụ tiện ích
│   └── exceptions/               # Lớp ngoại lệ
├── config/                       # Tệp cấu hình
│   ├── app.php                   # Cấu hình ứng dụng
│   ├── database.php              # Cấu hình cơ sở dữ liệu
│   ├── cache.php                 # Cấu hình bộ nhớ đệm
│   ├── queue.php                 # Cấu hình hàng đợi
│   ├── workerman.php             # Cấu hình Workerman
│   └── ...
├── route/                        # File route chính
├── public/                       # Điểm vào Web
│   ├── index.php                 # Điểm vào frontend
│   └── admin/                    # File frontend trang quản trị
├── runtime/                      # File runtime
├── composer.json                 # Phụ thuộc Composer
├── .env                          # Cấu hình môi trường
└── think                         # Công cụ dòng lệnh ThinkPHP
```

#### 1.3.2 Cấu trúc thư mục frontend
```
template/
├── admin/                        # Trang quản trị (Vue + ElementUI)
│   ├── src/
│   │   ├── api/                  # Định nghĩa API
│   │   ├── components/           # Thành phần dùng chung
│   │   ├── pages/                # Thành phần trang
│   │   ├── router/               # Cấu hình route
│   │   ├── store/                # Quản lý trạng thái Vuex
│   │   └── utils/                # Hàm tiện ích (utility)
│   ├── package.json
│   └── vue.config.js
└── uni-app/                      # Di động (UniApp)
    ├── api/                      # Các API
    ├── components/               # Thành phần
    ├── pages/                    # Trang
    ├── manifest.json             # Cấu hình ứng dụng
    └── pages.json                # Cấu hình route trang
```

## 2. Các module nghiệp vụ cốt lõi

### 2.1 Module người dùng (`app/services/user/`)

#### Service cốt lõi
- **UserServices**: Service chính của người dùng, quản lý thông tin cơ bản của người dùng
- **LoginServices**: Service đăng nhập, xử lý nhiều phương thức đăng nhập
- **UserLevelServices**: Quản lý hạng người dùng
- **UserMoneyServices**: Quản lý số dư người dùng
- **UserBillServices**: Quản lý sao kê giao dịch
- **UserExtractServices**: Quản lý rút tiền
- **UserRechargeServices**: Quản lý nạp tiền
- **UserGroupServices**: Nhóm người dùng
- **UserLabelServices**: Nhãn người dùng

#### Bảng dữ liệu
```sql
eb_user                  # Bảng người dùng
eb_user_bill             # Bảng giao dịch
eb_user_extract          # Bảng rút tiền
eb_user_recharge         # Bảng nạp tiền
eb_user_level            # Bảng hạng người dùng
eb_user_group            # Bảng nhóm người dùng
eb_user_label            # Bảng nhãn người dùng
```

#### Lưu ý khi phát triển
- Đăng nhập người dùng hỗ trợ nhiều phương thức: tài khoản và mật khẩu, mã xác thực qua số điện thoại, ủy quyền WeChat
- Mọi thay đổi số dư của người dùng đều phải được ghi vào bảng sao kê
- Hạng người dùng có thể thiết lập điều kiện nâng hạng
- Nhóm và nhãn người dùng được dùng để marketing đúng đối tượng

### 2.2 Module sản phẩm (`app/services/product/`)

#### Service cốt lõi
- **StoreProductServices**: Service chính của sản phẩm
- **StoreCategoryServices**: Danh mục sản phẩm
- **StoreProductAttrServices**: Thuộc tính sản phẩm
- **StoreProductReplyServices**: Đánh giá sản phẩm
- **CopyTaobaoServices**: Thu thập sản phẩm từ Taobao

#### Bảng dữ liệu
```sql
eb_store_product        # Bảng sản phẩm
eb_store_category       # Bảng danh mục sản phẩm
eb_store_product_attr   # Bảng thuộc tính sản phẩm
eb_store_product_reply  # Bảng đánh giá sản phẩm
eb_store_product_description  # Bảng chi tiết sản phẩm
```

#### Lưu ý khi phát triển
- Sản phẩm hỗ trợ nhiều quy cách (SKU), cần xử lý tồn kho và giá
- Sản phẩm có thể được thiết lập là sản phẩm thường, sản phẩm đổi điểm, sản phẩm đặt trước, v.v.
- Danh mục sản phẩm hỗ trợ nhiều cấp
- Thuộc tính sản phẩm hỗ trợ quy cách tùy chỉnh

### 2.3 Module đơn hàng (`app/services/order/`)

#### Service cốt lõi
- **StoreOrderServices**: Service chính của đơn hàng
- **StoreOrderCreateServices**: Tạo đơn hàng
- **StoreOrderDeliveryServices**: Giao hàng cho đơn hàng
- **StoreOrderRefundServices**: Hoàn tiền đơn hàng
- **StoreCartServices**: Giỏ hàng
- **OtherOrderServices**: Đơn hàng khác (đơn đổi điểm, v.v.)
- **OutStoreOrderServices**: Đơn hàng bên ngoài

#### Bảng dữ liệu
```sql
eb_store_order          # Bảng đơn hàng
eb_store_order_cart     # Bảng sản phẩm trong đơn hàng
eb_store_order_status   # Lịch sử thay đổi trạng thái đơn hàng
eb_store_refund         # Bảng hoàn tiền
eb_store_cart           # Bảng giỏ hàng
```

#### Lưu ý khi phát triển
- Luồng chuyển trạng thái đơn hàng: Chưa thanh toán → Chờ giao hàng → Chờ nhận hàng → Đã hoàn thành (hoặc hủy/hoàn tiền)
- Khi tạo đơn hàng cần trừ tồn kho
- Sau khi thanh toán thành công sẽ kích hoạt các quy trình tiếp theo (thông báo giao hàng, cộng điểm thưởng, v.v.)
- Khi hoàn tiền đơn hàng cần hoàn lại tồn kho
- Nên dùng hàng đợi để xử lý bất đồng bộ các thao tác liên quan đến đơn hàng

#### Sơ đồ chuyển trạng thái đơn hàng
```
Chưa thanh toán (status=0)
   ↓ Thanh toán thành công
Chờ giao hàng (status=1)
   ↓ Giao hàng
Chờ nhận hàng (status=2)
   ↓ Xác nhận đã nhận hàng
Đã hoàn thành (status=3)

Luồng rẽ nhánh:
- Chưa thanh toán → Đã hủy (status=-1)
- Chờ giao hàng → Yêu cầu hoàn tiền → Đang hoàn tiền (status=-2) → Đã hoàn tiền (status=-3)
- Chờ nhận hàng → Yêu cầu hoàn tiền → Đang hoàn tiền (status=-2) → Đã hoàn tiền (status=-3)
```

### 2.4 Module thanh toán (`app/services/pay/`)

#### Service cốt lõi
- **PayServices**: Service thanh toán
- **WechatPayServices**: WeChat Pay
- **AlipayServices**: Thanh toán Alipay

#### Bảng dữ liệu
```sql
eb_pay                  # Bảng lịch sử thanh toán
```

#### Lưu ý khi phát triển
- Phương thức thanh toán: WeChat Pay (OA WeChat/Mini Program/H5), thanh toán Alipay, thanh toán bằng số dư
- Quy trình thanh toán: Tạo đơn hàng → Gọi thanh toán → Callback thanh toán → Cập nhật trạng thái đơn hàng
- Callback thanh toán cần xác minh chữ ký để chống giả mạo
- Sau khi thanh toán thành công sẽ kích hoạt sự kiện, có thể mở rộng logic nghiệp vụ tiếp theo

### 2.5 Module marketing (`app/services/activity/`)

#### Service cốt lõi
- **Mua chung**: StoreCombinationServices, StorePinkServices
- **Săn giảm giá (bargain)**: StoreBargainServices
- **Flash sale**: StoreSeckillServices
- **Phiếu giảm giá (coupon)**: StoreCouponService, StoreCouponUserServices
- **Điểm thưởng**: StoreIntegralServices
- **Livestream**: LiveRoomServices, LiveGoodsServices
- **Vòng quay may mắn**: LuckLotteryServices

#### Bảng dữ liệu
```sql
eb_store_combination    # Bảng sản phẩm mua chung
eb_store_pink           # Bảng lịch sử mua chung
eb_store_bargain        # Bảng sản phẩm săn giảm giá
eb_store_bargain_user   # Bảng lịch sử săn giảm giá
eb_store_seckill        # Bảng sản phẩm flash sale
eb_store_coupon         # Bảng phiếu giảm giá
eb_store_coupon_user    # Bảng phiếu giảm giá của người dùng
eb_integral_product     # Bảng sản phẩm đổi điểm
```

#### Lưu ý khi phát triển
- Mọi hoạt động marketing đều cần thiết lập khoảng thời gian
- Phiếu giảm giá có thể thiết lập điều kiện sử dụng và sản phẩm áp dụng
- Mua chung cần xử lý logic mua chung thành công/thất bại
- Săn giảm giá cần xử lý tiến độ săn giảm giá và thời gian hoàn thành
- Sản phẩm flash sale cần giới hạn tồn kho và số lượng mua

### 2.6 Module tiếp thị liên kết (`app/services/agent/`)

#### Service cốt lõi
- **AgentLevelServices**: Quản lý cấp độ CTV
- **AgentLevelTaskServices**: Hệ thống nhiệm vụ CTV
- **DivisionServices**: Quản lý đại lý khu vực/đại lý
- **SpreadApplyServices**: Đăng ký làm CTV

#### Bảng dữ liệu
```sql
eb_agent_level          # Bảng hạng CTV
eb_agent_level_task     # Bảng nhiệm vụ CTV
eb_division             # Bảng đại lý khu vực
eb_spread_apply         # Bảng đăng ký CTV
```

#### Lưu ý khi phát triển
- Hệ thống tiếp thị liên kết hỗ trợ nhiều cấp
- CTV được tự động thăng cấp khi hoàn thành nhiệm vụ
- Khi thanh toán hoa hồng CTV cần tính hoa hồng cho từng cấp
- Đơn đăng ký làm CTV cần được quản trị viên duyệt

### 2.7 Module quản lý hệ thống (`app/services/system/`)

#### Service cốt lõi
- **SystemCrudServices**: Trình sinh code CRUD
- **SystemConfigServices**: Cấu hình hệ thống
- **SystemEventServices**: Quản lý sự kiện hệ thống
- **SystemCrontabServices**: Quản lý tác vụ định kỳ
- **SystemMenusServices**: Quản lý menu
- **SystemAdminServices**: Quản lý quản trị viên
- **SystemLogServices**: Nhật ký thao tác
- **SystemFileServices**: Quản lý file
- **SystemUpgradeServices**: Nâng cấp hệ thống

#### Lưu ý khi phát triển
- **Trình sinh code**: Có thể nhanh chóng tạo Controller, Service, DAO, Model, Validate
- **Cấu hình hệ thống**: Hỗ trợ cấu hình động từ trang quản trị, lưu trong cơ sở dữ liệu
- **Hệ thống sự kiện**: Đã định nghĩa 30+ điểm neo sự kiện hệ thống, có thể mở rộng logic nghiệp vụ
- **Tác vụ định kỳ**: Dựa trên Workerman, hỗ trợ biểu thức Cron
- **Quản lý quyền**: Dựa trên mô hình RBAC, có thể kiểm soát tới cấp menu và nút bấm

## 3. Quy chuẩn phát triển

### 3.1 Quy tắc đặt tên

#### Đặt tên lớp
- **Controller**: Tên module + Controller, ví dụ `UserController`
- **Service**: Tên module + Services, ví dụ `UserServices`
- **DAO**: Tên module + Dao, ví dụ `UserDao`
- **Model**: Tên module, ví dụ `User`
- **Validator**: Tên module + Validate, ví dụ `UserValidate`

#### Đặt tên phương thức
- **Phương thức controller**: Chữ thường + dấu gạch dưới, ví dụ `get_list`, `save_data`
- **Phương thức service**: Kiểu camelCase, ví dụ `getUserList`, `saveData`
- **Phương thức DAO**: Liên quan đến thao tác cơ sở dữ liệu, ví dụ `selectList`, `insert`, `update`, `delete`

#### Đặt tên biến
- **Biến thông thường**: Kiểu camelCase, ví dụ `$userName`, `$orderId`
- **Biến mảng**: Dạng số nhiều, ví dụ `$users`, `$products`
- **Biến boolean**: Bắt đầu bằng is/has/can, ví dụ `$isPaid`, `$hasStock`

### 3.2 Quy chuẩn code

#### Tầng controller
```php
<?php
namespace app\adminapi\controller\system;

use think\facade\App;
use app\services\system\SystemAdminServices;

/**
 * Controller quản trị viên
 */
class SystemAdminController
{
    protected $services;

    public function __construct(App $app, SystemAdminServices $services)
    {
        $this->services = $services;
    }

    /**
     * Lấy danh sách quản trị viên
     * @return mixed
     */
    public function get_list()
    {
        $where = $this->request->getMore([
            ['keywords', ''],
            ['status', '']
        ]);
        $list = $this->services->getAdminList($where);
        return app('json')->success($list);
    }

    /**
     * Lưu quản trị viên
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['account', ''],
            ['real_name', ''],
            ['pwd', ''],
            ['roles', []],
            ['status', 1]
        ]);
        $this->services->saveAdmin($data);
        return app('json')->success('Lưu thành công');
    }
}
```

#### Tầng service
```php
<?php
namespace app\services\system;

use crmeb\basic\BaseServices;
use crmeb\exceptions\AdminException;

class SystemAdminServices extends BaseServices
{
    /**
     * Lấy danh sách quản trị viên
     * @param array $where
     * @return array
     */
    public function getAdminList(array $where): array
    {
        // Xây dựng điều kiện truy vấn
        $query = $this->dao->search($where);

        // Lấy danh sách
        $list = $query->select()->toArray();

        // Xử lý dữ liệu
        foreach ($list as &$item) {
            $item['role_names'] = $this->getRoleNames($item['roles']);
        }

        return $list;
    }

    /**
     * Lưu quản trị viên
     * @param array $data
     * @return int
     */
    public function saveAdmin(array $data): int
    {
        // Xác thực dữ liệu
        if (empty($data['account'])) {
            throw new AdminException('Tài khoản không được để trống');
        }

        // Mã hóa mật khẩu
        if (!empty($data['pwd'])) {
            $data['pwd'] = password_hash($data['pwd'], PASSWORD_DEFAULT);
        } else {
            unset($data['pwd']);
        }

        // Lưu dữ liệu
        if (isset($data['id']) && $data['id']) {
            // Cập nhật
            $id = $data['id'];
            unset($data['id']);
            $this->dao->update($id, $data);
        } else {
            // Thêm mới
            $id = $this->dao->save($data);
        }

        return $id;
    }
}
```

#### Tầng DAO
```php
<?php
namespace app\dao\system;

use crmeb\basic\BaseDao;
use app\model\system\SystemAdmin;

class SystemAdminDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return SystemAdmin::class;
    }

    /**
     * Điều kiện tìm kiếm
     * @param array $where
     * @return \think\Model
     */
    public function search(array $where = [])
    {
        $query = $this->getModel()
            ->where('is_del', 0);

        // Tìm kiếm theo tài khoản
        if (!empty($where['keywords'])) {
            $query = $query->whereLike('account|real_name', "%{$where['keywords']}%");
        }

        // Lọc theo trạng thái
        if ($where['status'] !== '') {
            $query = $query->where('status', $where['status']);
        }

        return $query;
    }
}
```

#### Tầng model
```php
<?php
namespace app\model\system;

use crmeb\basic\BaseModel;

class SystemAdmin extends BaseModel
{
    protected $name = 'system_admin';

    protected $pk = 'id';

    /**
     * Liên kết vai trò
     * @return \think\model\relation\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(SystemRole::class, 'system_admin_role', 'role_id', 'admin_id');
    }

    /**
     * Setter mật khẩu
     * @param $value
     * @return string
     */
    public function setPwdAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * Getter trạng thái
     * @param $value
     * @return string
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = [
            0 => 'Vô hiệu hóa',
            1 => 'Kích hoạt'
        ];
        return $status[$data['status']] ?? '';
    }
}
```

### 3.3 Quy chuẩn cơ sở dữ liệu

#### Đặt tên bảng
- Dùng chữ thường và dấu gạch dưới
- Thống nhất dùng tiền tố `eb_`
- Tên bảng dùng dạng số nhiều hoặc có ý nghĩa rõ ràng

#### Đặt tên trường
- Dùng chữ thường và dấu gạch dưới
- Tên trường không bắt đầu bằng dấu gạch dưới
- Khóa chính thống nhất đặt tên là `id`
- Khóa ngoại đặt tên là `{tên_bảng}_id`, ví dụ `user_id`
- Trường thời gian đặt tên là `create_time`, `update_time`
- Trường trạng thái đặt tên là `status`, giá trị mặc định là 0
- Cờ đánh dấu xóa đặt tên là `is_del`, 0 là chưa xóa, 1 là đã xóa

#### Loại trường
- Số nguyên: Dùng `int`, ví dụ `tinyint`, `smallint`, `int`, `bigint`
- Chuỗi: Dùng `varchar`, ví dụ `varchar(255)`
- Văn bản: Dùng `text`
- Số tiền: Dùng `decimal(10,2)`
- Thời gian: Dùng `int` (timestamp) hoặc `datetime`

### 3.4 Quy chuẩn API

#### Phương thức yêu cầu
- **GET**: Truy vấn dữ liệu
- **POST**: Tạo dữ liệu
- **PUT**: Cập nhật dữ liệu
- **DELETE**: Xóa dữ liệu

#### Định dạng phản hồi
```json
{
    "code": 200,
    "msg": "Thao tác thành công",
    "data": {}
}
```

#### Phản hồi lỗi
```json
{
    "code": 400,
    "msg": "Thông tin lỗi",
    "data": null
}
```

#### Phản hồi phân trang
```json
{
    "code": 200,
    "msg": "Thao tác thành công",
    "data": {
        "list": [],
        "count": 100,
        "page": 1,
        "limit": 10
    }
}
```

## 4. Tính năng tự động hóa

### 4.1 Trình sinh code

#### Cách sử dụng
1. Trong trang quản trị, vào “Quản lý hệ thống” > “Tạo mã nguồn”
2. Chọn bảng dữ liệu
3. Cấu hình tham số sinh code (kiểu trường, kiểu tìm kiếm, kiểu form, v.v.)
4. Sinh code

#### Các kiểu form được hỗ trợ
- **input**: Ô nhập liệu thông thường
- **textarea**: Vùng nhập văn bản nhiều dòng
- **select**: Danh sách thả xuống
- **radio**: Ô chọn một
- **checkbox**: Ô chọn nhiều
- **date**: Chọn ngày
- **datetime**: Chọn ngày giờ
- **image**: Tải lên ảnh
- **file**: Tải lên tệp
- **editor**: Trình soạn thảo văn bản định dạng
- **number**: Nhập số
- **switch**: Công tắc
- v.v.

#### Các kiểu tìm kiếm được hỗ trợ
- **Tìm kiếm thường**: Tìm kiếm văn bản thông thường
- **Khoảng ngày**: Tìm kiếm theo khoảng ngày
- **Khoảng thời gian**: Tìm kiếm theo khoảng thời gian
- **Danh sách thả xuống**: Lọc bằng danh sách thả xuống

### 4.2 Tác vụ định kỳ

#### Lệnh khởi động
```bash
# Khởi động tác vụ định kỳ (tiến trình daemon)
php think timer start --d

# Dừng tác vụ định kỳ
php think timer stop

# Khởi động lại tác vụ định kỳ
php think timer restart

# Xem trạng thái tác vụ định kỳ
php think timer status
```

#### Các tác vụ định kỳ có sẵn của hệ thống
1. Tự động hủy đơn hàng chưa thanh toán
2. Tự động xác nhận đã nhận hàng
3. Tự động đánh giá
4. Tự động đóng nhóm mua chung
5. Tự động đóng lượt săn giảm giá
6. Xử lý điểm thưởng hết hạn
7. Xử lý phiếu giảm giá hết hạn
8. Thanh toán hoa hồng CTV
9. Tổng hợp dữ liệu thống kê
10. Dọn dẹp nhật ký hệ thống

### 4.3 Tác vụ hàng đợi

#### Lệnh khởi động
```bash
# Khởi động consumer của hàng đợi
php think queue:listen --queue

# Hoặc dùng Workerman
php think queue:work --queue
```

#### Các tác vụ hàng đợi chính
- **OrderJob**: Tác vụ liên quan đến đơn hàng (tạo, thanh toán, giao hàng, v.v.)
- **PinkJob**: Tác vụ mua chung
- **BargainJob**: Tác vụ săn giảm giá
- **SeckillJob**: Tác vụ flash sale
- **AutoCommentJob**: Tự động đánh giá
- **PosterJob**: Tạo poster
- **AgentJob**: Kiểm tra thăng cấp CTV
- **UnpaidOrderCancelJob**: Hủy đơn hàng chưa thanh toán
- v.v.

### 4.4 Hệ thống sự kiện

#### Điểm neo sự kiện hệ thống

**Sự kiện người dùng**:
- Người dùng đăng ký (user.register)
- Người dùng đăng nhập (user.login)
- Người dùng đăng xuất (user.logout)
- Người dùng sửa thông tin (user.update)
- Người dùng liên kết người giới thiệu (user.bind_spread)
- Người dùng điểm danh (user.sign)
- Người dùng nạp tiền (user.recharge)

**Sự kiện đơn hàng**:
- Tạo đơn hàng (order.create)
- Đơn hàng thanh toán thành công (order.pay_success)
- Giao hàng cho đơn hàng (order.delivery)
- Xác nhận đã nhận hàng (order.confirm)
- Hủy đơn hàng (order.cancel)
- Hoàn tiền đơn hàng (order.refund)

**Sự kiện sản phẩm**:
- Đăng bán sản phẩm (product.on_shelf)
- Ngừng bán sản phẩm (product.off_shelf)
- Đánh giá sản phẩm (product.comment)

**Sự kiện thanh toán**:
- Thanh toán thành công (pay.success)
- Thanh toán thất bại (pay.fail)

**Sự kiện marketing**:
- Nhận phiếu giảm giá (coupon.receive)
- Tham gia mua chung (combination.join)
- Tham gia săn giảm giá (bargain.join)

#### Phát triển trình lắng nghe sự kiện (listener)
```php
<?php
namespace app\listener\order;

use think\Container;

/**
 * Listener thanh toán đơn hàng thành công
 */
class OrderPaySuccessListener
{
    /**
     * Xử lý sự kiện thanh toán đơn hàng thành công
     * @param $event
     * @return void
     */
    public function handle($event)
    {
        // $event chứa thông tin đơn hàng
        $order = $event['order'];

        // Logic nghiệp vụ
        // 1. Gửi thông báo
        // 2. Tăng điểm thưởng
        // 3. Cập nhật tồn kho
        // 4. Kích hoạt tính hoa hồng tiếp thị liên kết
        // ...
    }
}
```

### 4.5 Giao tiếp thời gian thực qua WebSocket

#### Lệnh khởi động
```bash
# Khởi động dịch vụ WebSocket
php think workerman start --d
```

#### Cấu hình dịch vụ
```php
// config/workerman.php
return [
    // Thông báo trang quản trị
    'admin' => [
        'protocol' => 'websocket',
        'port' => 40001,
        'ip' => '0.0.0.0',
    ],
    // Tin nhắn CSKH
    'chat' => [
        'protocol' => 'websocket',
        'port' => 40002,
        'ip' => '0.0.0.0',
    ],
    // Giao tiếp nội bộ
    'channel' => [
        'port' => 40003,
        'ip' => '127.0.0.1',
    ],
];
```

#### Tình huống sử dụng
- Thông báo đơn hàng mới theo thời gian thực
- Chat trực tuyến với CSKH
- Đẩy thông báo hệ thống
- Thống kê dữ liệu theo thời gian thực

## 5. Các tình huống phát triển thường gặp

### 5.1 Tạo module tính năng mới

#### Bước 1: Tạo bảng dữ liệu
```sql
CREATE TABLE `eb_example` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Tên',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Trạng thái 0 tắt 1 bật',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT 'Thời gian tạo',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT 'Thời gian cập nhật',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Đã xóa',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Bảng mẫu';
```

#### Bước 2: Tạo model
```bash
# Dùng dòng lệnh để tạo model
php think make:model Example
```

#### Bước 3: Tạo DAO
```php
<?php
namespace app\dao;

use crmeb\basic\BaseDao;
use app\model\Example;

class ExampleDao extends BaseDao
{
    protected function setModel(): string
    {
        return Example::class;
    }
}
```

#### Bước 4: Tạo service
```php
<?php
namespace app\services;

use crmeb\basic\BaseServices;
use app\dao\ExampleDao;

class ExampleServices extends BaseServices
{
    public function __construct(ExampleDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList(array $where): array
    {
        return $this->dao->getList($where);
    }

    public function save(array $data): int
    {
        return $this->dao->save($data);
    }
}
```

#### Bước 5: Tạo controller
```php
<?php
namespace app\adminapi\controller;

use app\services\ExampleServices;

class ExampleController
{
    protected $services;

    public function __construct(ExampleServices $services)
    {
        $this->services = $services;
    }

    public function get_list()
    {
        $where = $this->request->getMore([
            ['name', ''],
            ['status', '']
        ]);
        $list = $this->services->getList($where);
        return app('json')->success($list);
    }

    public function save()
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['status', 1]
        ]);
        $this->services->save($data);
        return app('json')->success('Lưu thành công');
    }
}
```

#### Bước 6: Tạo route
```php
// route/app.php Hoặc route/adminapi.php
use think\facade\Route;

Route::group('example', function () {
    Route::get('list', 'Example/get_list');
    Route::post('save', 'Example/save');
});
```

#### Bước 7: Dùng trình sinh code (tùy chọn)
Cấu hình sinh code ngay trong trang quản trị, tự động tạo code frontend và backend

### 5.2 Phát triển tính năng đơn hàng

#### Quy trình tạo đơn hàng
1. Kiểm tra tồn kho và trạng thái sản phẩm
2. Tính số tiền đơn hàng
3. Tạo bản ghi đơn hàng
4. Tạo bản ghi sản phẩm của đơn hàng
5. Trừ tồn kho sản phẩm
6. Làm trống giỏ hàng
7. Kích hoạt sự kiện tạo đơn hàng

#### Quy trình thanh toán đơn hàng
1. Gọi thanh toán (WeChat/Alipay/số dư)
2. Nhận callback thanh toán
3. Xác minh chữ ký
4. Cập nhật trạng thái đơn hàng thành “Chờ giao hàng”
5. Khấu trừ phiếu giảm giá
6. Tăng điểm thưởng người dùng
7. Kích hoạt sự kiện thanh toán thành công (tiếp thị liên kết, thông báo, v.v.)

#### Quy trình giao hàng
1. Lấy thông tin đơn hàng
2. Điền thông tin vận chuyển
3. Cập nhật trạng thái đơn hàng thành “Chờ nhận hàng”
4. Gửi thông báo giao hàng
5. Kích hoạt sự kiện giao hàng

#### Quy trình nhận hàng
1. Người dùng xác nhận đã nhận hàng hoặc hệ thống tự động xác nhận nhận hàng (sau 7 ngày chưa xác nhận)
2. Cập nhật trạng thái đơn hàng thành “Đã hoàn thành”
3. Thanh toán hoa hồng CTV
4. Tăng điểm thưởng người dùng
5. Kích hoạt sự kiện nhận hàng

### 5.3 Phát triển hoạt động marketing

#### Lưu ý khi phát triển tính năng phiếu giảm giá
1. Tạo mẫu phiếu giảm giá (mệnh giá, giá trị đơn tối thiểu, điều kiện sử dụng, v.v.)
2. Người dùng nhận phiếu giảm giá
3. Chọn phiếu giảm giá khi đặt hàng
4. Tính số tiền được giảm
5. Xác nhận sử dụng phiếu giảm giá sau khi thanh toán
6. Tự động mất hiệu lực khi hết hạn

#### Lưu ý khi phát triển tính năng mua chung
1. Tạo sản phẩm mua chung (giá mua chung, số người để thành nhóm, thời lượng mua chung)
2. Người dùng tạo nhóm mua chung
3. Người khác tham gia mua chung
4. Xác định mua chung thành công/thất bại
5. Sau khi thành nhóm, tính đơn hàng theo giá mua chung
6. Hoàn tiền khi thất bại

#### Lưu ý khi phát triển tính năng flash sale
1. Tạo chương trình flash sale (thời gian, sản phẩm, tồn kho, giới hạn mua)
2. Người dùng tham gia flash sale
3. Kiểm tra tồn kho và giới hạn mua
4. Tạo đơn hàng flash sale
5. Tự động hủy đơn hàng chưa thanh toán
6. Cập nhật tồn kho sau khi chương trình kết thúc

### 5.4 Phát triển tính năng tiếp thị liên kết

#### Quy trình tiếp thị liên kết
1. Người dùng đăng ký làm CTV
2. Quản trị viên duyệt đơn đăng ký
3. CTV chia sẻ liên kết giới thiệu
4. Người dùng mới đăng ký qua liên kết và trở thành cấp dưới
5. Người dùng cấp dưới đặt hàng
6. Hệ thống tính hoa hồng CTV
7. Hoa hồng được thanh toán vào số dư của CTV

#### Thăng cấp CTV
1. Tạo cấp độ CTV (tên cấp độ, tỷ lệ hoa hồng)
2. Thiết lập nhiệm vụ thăng cấp (số đơn hàng, số tiền, v.v.)
3. Tác vụ định kỳ kiểm tra tình trạng hoàn thành nhiệm vụ của CTV
4. Tự động thăng cấp khi đạt điều kiện

## 6. Hướng dẫn khắc phục sự cố

### 6.1 Lỗi thường gặp

#### Lỗi kết nối cơ sở dữ liệu
```php
// Thông tin lỗi
SQLSTATE[HY000] [2002] Connection refused

// Các bước xử lý sự cố
1. Kiểm tra dịch vụ cơ sở dữ liệu đã khởi động chưa
2. Kiểm tra cấu hình cơ sở dữ liệu trong file cấu hình .env
3. Kiểm tra quyền của người dùng cơ sở dữ liệu
4. Kiểm tra cài đặt tường lửa
```

#### Tác vụ hàng đợi không chạy
```php
// Các bước xử lý sự cố
1. Kiểm tra consumer của hàng đợi đã khởi động chưa: php think queue:work
2. Kiểm tra cấu hình hàng đợi: config/queue.php
3. Kiểm tra kết nối Redis: redis-cli ping
4. Xem log hàng đợi: runtime/log/
```

#### Tác vụ định kỳ không chạy
```bash
# Các bước xử lý sự cố
1. Kiểm tra tác vụ định kỳ đã khởi động chưa: php think timer status
2. Kiểm tra cấu hình tác vụ định kỳ
3. Kiểm tra biểu thức Cron có đúng không
4. Xem log tác vụ định kỳ
```

#### Kết nối WebSocket thất bại
```bash
# Các bước xử lý sự cố
1. Kiểm tra dịch vụ WebSocket đã khởi động chưa: php think workerman status
2. Kiểm tra cổng có bị chiếm dụng không: netstat -tlnp | grep 40001
3. Kiểm tra cài đặt tường lửa
4. Kiểm tra địa chỉ kết nối của client có đúng không
```

### 6.2 Tối ưu hiệu năng

#### Tối ưu cơ sở dữ liệu
- Thêm chỉ mục (index) cho các trường thường dùng để truy vấn
- Tránh dùng `SELECT *`, chỉ truy vấn các trường cần thiết
- Dùng `EXPLAIN` để phân tích kế hoạch thực thi SQL
- Sử dụng cache hợp lý để giảm truy vấn cơ sở dữ liệu

#### Tối ưu cache
- Dùng Redis để cache dữ liệu nóng (hot data)
- Đặt thời gian hết hạn cache hợp lý
- Dùng tiền tố cache để tránh xung đột

#### Tối ưu mã nguồn
- Giảm vòng lặp lồng nhau
- Tối ưu độ phức tạp thuật toán
- Dùng hàng đợi để xử lý các thao tác tốn thời gian
- Xử lý bất đồng bộ các nghiệp vụ không then chốt

### 6.3 Tăng cường bảo mật

#### Phòng chống SQL injection
- Dùng tham số ràng buộc (parameter binding), không nối chuỗi SQL trực tiếp
- Dùng Query Builder của ThinkPHP
- Kiểm tra tính hợp lệ của dữ liệu người dùng nhập vào

#### Phòng chống XSS
- Lọc dữ liệu người dùng nhập vào
- Escape HTML khi xuất dữ liệu
- Dùng CSP (chính sách bảo mật nội dung)

#### Phòng chống CSRF
- Dùng CSRF Token
- Xác minh nguồn gốc request
- Xác nhận hai lần với các thao tác quan trọng

#### Kiểm soát quyền
- Kiểm tra quyền nghiêm ngặt
- Mô hình phân quyền dựa trên RBAC
- Ghi log các thao tác nhạy cảm

## 7. Triển khai và vận hành

### 7.1 Yêu cầu môi trường
- PHP >= 7.1
- MySQL >= 5.7
- Redis >= 5.0 (tùy chọn)
- Nginx/Apache
- Composer

### 7.2 Các bước triển khai

#### 1. Cài đặt các gói phụ thuộc
```bash
composer install
```

#### 2. Cấu hình môi trường
```bash
cp .env.example .env
# Sửa cấu hình trong file .env
```

#### 3. Khởi tạo cơ sở dữ liệu
```bash
# Nhập cơ sở dữ liệu
mysql -u root -p crmeb < database.sql
```

#### 4. Thiết lập quyền thư mục
```bash
chmod -R 755 runtime
chmod -R 755 public/uploads
```

#### 5. Khởi động dịch vụ
```bash
# Khởi động hàng đợi
php think queue:listen --queue

# Khởi động tác vụ định kỳ
php think timer start --d

# Khởi động WebSocket
php think workerman start --d
```

#### 6. Build frontend
```bash
cd template/admin
npm install
npm run build
```

### 7.3 Triển khai bằng Docker

#### Dùng docker-compose
```bash
cd docker-compose
docker-compose up -d
```

### 7.4 Giám sát và log

#### Vị trí log
- Log ứng dụng: `runtime/log/`
- Log lỗi: `runtime/log/error/`
- Log SQL: Bật ghi log SQL của cơ sở dữ liệu

#### Chỉ số giám sát
- Tài nguyên máy chủ: CPU, bộ nhớ, ổ đĩa
- Hiệu năng ứng dụng: thời gian phản hồi, thông lượng
- Cơ sở dữ liệu: truy vấn chậm, số kết nối
- Hàng đợi: độ dài hàng đợi, tốc độ xử lý

## 8. Tài liệu tham khảo

### 8.1 Tài liệu chính thức
- [Tài liệu chính thức ThinkPHP 6](https://www.kancloud.cn/manual/thinkphp6_0)
- [Tài liệu chính thức Vue 2](https://v2.vuejs.org/)
- [Tài liệu chính thức Element UI](https://element.eleme.io/)
- [Tài liệu chính thức UniApp](https://uniapp.dcloud.net.cn/)

### 8.2 Tài liệu dự án
- `/dev-docs/huong-dan-ai-doc-hieu-ma-nguon.md` - Hướng dẫn đọc hiểu mã nguồn
- `/dev-docs/ma-loi.md` - Giải thích mã lỗi
- `.codebuddy/skills/php-api/SKILL.md` - Quy chuẩn phát triển backend
- `.codebuddy/skills/admin-element/SKILL.md` - Quy chuẩn phát triển frontend

### 8.3 Công cụ khuyên dùng
- **IDE**: PhpStorm / VS Code
- **Kiểm thử API**: Postman / Apifox
- **Cơ sở dữ liệu**: Navicat / phpMyAdmin
- **Redis**: Redis Desktop Manager

## 9. Tra cứu nhanh các lệnh thường dùng

### 9.1 Lệnh ThinkPHP
```bash
php think                     # Xem tất cả lệnh
php think make:controller      # Tạo controller
php think make:model           # Tạo model
php think make:middleware      # Tạo middleware
php think make:validate        # Tạo validator
php think clear                # Xóa bộ nhớ đệm
php think run                  # Khởi động máy chủ tích hợp sẵn
```

### 9.2 Lệnh hàng đợi
```bash
php think queue:listen        # Lắng nghe hàng đợi
php think queue:work           # Xử lý tác vụ trong hàng đợi
php think queue:restart        # Khởi động lại hàng đợi
php think queue:fail           # Xem tác vụ thất bại
php think queue:retry          # Thử lại tác vụ thất bại
```

### 9.3 Lệnh tác vụ định kỳ
```bash
php think timer start          # Khởi động tác vụ định kỳ
php think timer stop           # Dừng tác vụ định kỳ
php think timer restart        # Khởi động lại tác vụ định kỳ
php think timer status         # Xem trạng thái
```

### 9.4 Lệnh Workerman
```bash
php think workerman start      # Khởi động
php think workerman stop       # Dừng
php think workerman restart    # Khởi động lại
php think workerman reload     # Khởi động lại mượt (graceful)
php think workerman status     # Xem trạng thái
```

---

> **Gợi ý**: Agent này được thiết kế riêng cho hệ thống thương mại điện tử CRMEB, giúp bạn nhanh chóng phát triển và hiểu rõ hệ thống. Nếu có thắc mắc, vui lòng tham khảo tài liệu chính thức hoặc xem mã nguồn dự án.
