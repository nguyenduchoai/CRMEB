# Mô tả chi tiết cấu trúc thư mục frontend di động CRMEB

## 📋 Tổng quan dự án

**Bộ công nghệ**: Vue.js 2.x + uni-app + Vuex + SCSS  
**Hỗ trợ đa nền tảng**: H5, Mini Program WeChat, Mini Program Alipay, Mini Program Baidu, Mini Program Toutiao, App  
**Phiên bản dự án**: CRMEB bản tiêu chuẩn 5.6.2  
**Mô hình phát triển**: Component hóa + Module hóa + Tải theo gói con (subpackage)

## 🏗️ Thiết kế kiến trúc tổng thể 3

```
template/uni-app/
├── 📁 api/                    # Tầng dịch vụ API (120+ API)
├── 📁 components/             # Tầng thành phần (60+ thành phần tái sử dụng)
├── 📁 config/                 # Tầng cấu hình
├── 📁 libs/                   # Tầng thư viện tiện ích
├── 📁 mixins/                 # Tầng mixin
├── 📁 pages/                  # Tầng trang (kiến trúc subpackage)
├── 📁 plugin/                 # Tầng plugin
├── 📁 static/                 # Tầng tài nguyên tĩnh
├── 📁 store/                  # Tầng quản lý trạng thái
├── 📁 utils/                  # Tầng hàm tiện ích
├── 🚀 App.vue                 # Thành phần gốc của ứng dụng
├── 🎯 main.js                 # Điểm vào ứng dụng
├── ⚙️ manifest.json           # File cấu hình ứng dụng
├── 📄 pages.json              # Cấu hình route trang
└── 📦 package.json            # cấu hình phụ thuộc
```

## 📊 Phân tích chi tiết cấu trúc thư mục

### 1. 📁 api/ - Tầng dịch vụ API

Quản lý API được chia theo module nghiệp vụ, đóng gói bằng Promise:

```javascript
// Cấu trúc file
api/
├── activity.js      # API chương trình marketing (săn giảm giá, mua chung, flash sale, v.v.)
├── admin.js         # API trang quản trị
├── api.js           # API cơ bản
├── kefu.js          # API hệ thống CSKH
├── lottery.js       # API hoạt động quay thưởng  
├── order.js         # API quản lý đơn hàng
├── points_mall.js   # API cửa hàng đổi điểm
├── public.js        # API chung (lấy cấu hình, v.v.)
├── store.js         # API liên quan đến cửa hàng
├── user.js          # API trung tâm người dùng
```

**Đặc điểm thiết kế API**:
- Cơ chế xử lý lỗi thống nhất
- Kiểm tra tham số request
- Định dạng dữ liệu phản hồi
- Hỗ trợ tương thích môi trường đa nền tảng

### 2. 📁 components/ - Tầng component

Thư viện component phong phú, áp dụng tư duy thiết kế nguyên tử (atomic design):

#### 2.1 Component cơ bản
- `Authorize.vue` - Component ủy quyền người dùng
- `BaseMoney.vue` - Component hiển thị số tiền (hỗ trợ định dạng)
- `BaseTag.vue` - Component nhãn (nhiều kiểu dáng)
- `emptyPage.vue` - Component trang trạng thái trống
- `menuIcon.vue` - Component biểu tượng menu

#### 2.2 Component nghiệp vụ
- `addressWindow/` - Component chọn địa chỉ (liên kết 3 cấp)
- `cartList/` - Danh sách sản phẩm trong giỏ hàng
- `countDown/` - Component đếm ngược (nhiều kiểu dáng)
- `couponWindow/` - Component chọn phiếu giảm giá
- `goodList/` - Component danh sách sản phẩm (bố cục lưới/danh sách)
- `home/` - Bộ component nghiệp vụ trang chủ
- `orderGoods/` - Component sản phẩm trong đơn hàng
- `payment/` - Component thanh toán (nhiều phương thức thanh toán)

#### 2.3 Component chức năng
- `easy-loadimage/` - Component lazy load hình ảnh
- `jyf-parser/` - Component phân tích văn bản định dạng (hỗ trợ HTML/Markdown)
- `Loading/` - Component trạng thái đang tải
- `skeleton/` - Component skeleton screen (khung xương)
- `swipers/` - Component banner trình chiếu (có thể cấu hình chỉ báo)
- `update/` - Component cập nhật ứng dụng
- `WaterfallsFlow/` - Component bố cục dạng thác nước (waterfall)

#### 2.4 Component đặc biệt
- `parabolaBall/` - Component hiệu ứng parabol (hiệu ứng thêm vào giỏ hàng)
- `shareRedPackets/` - Component chia sẻ lì xì
- `tuiDrawer/` - Component ngăn kéo (drawer)
- `uniNoticeBar/` - Component thanh thông báo

### 3. ?? config/ - Tầng cấu hình

```javascript
config/
├── app.js       # Cấu hình cơ bản của ứng dụng
│   - HTTP_REQUEST_URL    # Địa chỉ gốc của API
│   - IMG_URL             # Địa chỉ tài nguyên hình ảnh  
│   - VERSION             # Phiên bản ứng dụng
│   - WS_URL              # Địa chỉ WebSocket
├── cache.js     # Cấu hình bộ nhớ đệm
│   - Định nghĩa tên key bộ nhớ đệm
│   - Cấu hình chiến lược bộ nhớ đệm
└── socket.js    # Cấu hình Socket
```

### 4. 📁 libs/ - Tầng thư viện tiện ích

```javascript
libs/
├── chat.js          # Thư viện lõi cho chức năng chat
├── login.js         # Công cụ xác thực đăng nhập
├── new_chat.js      # Dịch vụ chat phiên bản mới
├── permission.js    # Công cụ kiểm tra quyền
├── routine.js       # Công cụ liên quan đến Mini Program
├── uniApi.js        # uni-app API mở rộng
└── wechat.js        # Wrapper cho WeChat SDK
```

### 5. 📁 mixins/ - Tầng mixin

```javascript
mixins/
├── color.js             # Mixin chủ đề màu sắc
├── debounce.js          # Mixin hàm debounce
├── SendVerifyCode.js    # Mixin gửi mã xác thực
├── sharePoster.js       # Mixin tạo poster chia sẻ
└── skuSelect.js         # Mixin chọn SKU sản phẩm
```

### 6. 📁 pages/ - Tầng trang (kiến trúc chia gói con)

#### 6.1 Trang trong gói chính (trang cốt lõi)
- `guide/index` - Trang hướng dẫn của ứng dụng
- `index/index` - Trang chủ (TabBar)
- `order_addcart/order_addcart` - Trang giỏ hàng (TabBar)
- `user/index` - Trang cá nhân (TabBar)
- `goods_cate/goods_cate` - Trang danh mục sản phẩm (TabBar)

#### 6.2 Gói con module sản phẩm (goods)
```javascript
pages/goods/
├── goods_list/index             # Danh sách sản phẩm
├── goods_search/index           # Tìm kiếm sản phẩm
├── order_confirm/index          # Xác nhận đơn hàng
├── order_details/index          # Chi tiết đơn hàng
├── goods_details_store/index    # Danh sách cửa hàng
├── goods_comment_con/index      # Đánh giá sản phẩm
├── cashier/index                # Thu ngân
└── lottery/grids/index          # Hoạt động quay thưởng
```

#### 6.3 Gói con module người dùng (users)
```javascript
pages/users/
├── user_info/index              # Thông tin người dùng
├── user_address/index           # Quản lý địa chỉ
├── user_coupon/index            # Phiếu giảm giá
├── user_spread_user/index       # Quản lý giới thiệu
├── user_money/index             # Tài khoản của tôi
├── login/index                  # Trang đăng nhập
└── message_center/index         # Trung tâm tin nhắn
```

#### 6.4 Gói con module khuyến mãi (activity)
```javascript
pages/activity/
├── goods_bargain/index          # Hoạt động săn giảm giá
├── goods_combination/index      # Hoạt động mua chung  
├── goods_seckill/index          # Hoạt động flash sale
├── presell/index                # Hoạt động đặt trước
└── bargain/index                # Lịch sử săn giảm giá
```

#### 6.5 Gói con module quản lý (admin)
```javascript
pages/admin/
├── order/index                  # Quản lý đơn hàng
├── orderList/index              # Danh sách đơn hàng
├── statistics/index             # Thống kê dữ liệu
├── delivery/index               # Giao đơn hàng
└── order_cancellation/index     # Xác nhận sử dụng đơn hàng
```

#### 6.6 Các gói con khác
- `annex/` - Trang phụ trợ (Webview, thành viên, v.v.)
- `points_mall/` - Cửa hàng đổi điểm
- `extension/` - Chức năng mở rộng (CSKH, tin tức, v.v.)
- `columnGoods/` - Sản phẩm chuyên mục
- `goods_details/` - Chi tiết sản phẩm (gói con độc lập)

### 7. 📁 static/ - Tầng tài nguyên tĩnh

#### 7.1 Tài nguyên style
```css
static/
├── css/
│   ├── base.css         # Reset style cơ bản
│   ├── style.scss       # Tệp style chính
│   ├── unocss.css       # Style tiện ích UnoCSS
│   └── guildford.css    # Style riêng
├── fonts/               # File font
│   └── D-DIN-PRO-*.ttf
└── iconfont/            # Font biểu tượng
    └── iconfont.css
```

#### 7.2 Tài nguyên hình ảnh
```bash
static/images/
├── 1-001.png ~ 4-002.png    # Icon TabBar
├── def_avatar.png           # Ảnh đại diện mặc định
├── vip.png                  # Biểu tượng VIP
├── svip.png                 # Biểu tượng SVIP  
└── Các icon chức năng và ảnh nền
```

#### 7.3 Tài nguyên khác
- `app-plus/` - Tài nguyên riêng cho App
- `easy-loadimage/` - Tài nguyên liên quan đến tải ảnh

### 8. 📁 store/ - Tầng quản lý trạng thái

Quản lý trạng thái theo module với Vuex:

```javascript
store/
├── modules/            # Module trạng thái
│   ├── app.js         # Trạng thái ứng dụng
│   ├── hotWords.js    # Trạng thái từ khóa tìm kiếm phổ biến
│   └── indexData.js   # Trạng thái dữ liệu trang chủ
├── getters.js         # Hàm getter
└── index.js           # Điểm vào chính của Store
```

**Đặc điểm quản lý trạng thái**:
- Phân chia trạng thái theo module
- Thay đổi trạng thái nghiêm ngặt thông qua mutations
- Hỗ trợ lưu trạng thái bền vững (persistence)
- Đồng bộ trạng thái trên nhiều nền tảng

### 9. 📁 utils/ - Tầng hàm tiện ích

```javascript
utils/
├── cache.js          # Công cụ quản lý bộ nhớ đệm
├── emoji.js          # Công cụ xử lý biểu tượng cảm xúc
├── index.js          # Điểm vào chính của bộ công cụ
├── lang.js           # Công cụ đa ngôn ngữ
├── permission.js     # Công cụ kiểm tra quyền
├── request.js        # Công cụ đóng gói request
├── util.js           # Hàm tiện ích dùng chung
├── validate.js       # Công cụ xác thực dữ liệu
└── theme.js          # Công cụ quản lý theme
```

## 🚀 Đặc tính kỹ thuật cốt lõi

### 1. Cơ chế tương thích đa nền tảng
```javascript
// Ví dụ biên dịch có điều kiện
// #ifdef H5
// Code riêng cho H5
// #endif

// #ifdef MP-WEIXIN  
// Code riêng cho WeChat Mini Program
// #endif

// #ifdef APP-PLUS
// Code riêng cho App
// #endif
```

### 2. Chiến lược tối ưu hiệu năng
- **Tải theo gói con**: Chia gói con theo module nghiệp vụ, giảm dung lượng gói chính
- **Tải ảnh trì hoãn (lazy load)**: Chỉ tải khi cuộn tới vùng hiển thị
- **Lazy load component**: Tách code (code splitting) ở cấp route
- **Bộ nhớ đệm dữ liệu**: Chiến lược cache hợp lý
- **Debounce request**: Tránh gửi request trùng lặp

### 3. Hệ thống chủ đề và đổi giao diện
Hỗ trợ chuyển đổi màu chủ đề động, được thực hiện thông qua biến CSS:
```css
:root {
  --view-theme: #e93323; /* Màu chủ đề chính */
  --view-assist: #ff9900; /* Màu phụ */
}
```

### 4. Hỗ trợ đa ngôn ngữ (i18n)
Hỗ trợ đa ngôn ngữ dựa trên Vue I18n, cho phép tải gói ngôn ngữ động.

## 📋 Quy chuẩn phát triển

### 1. Quy tắc đặt tên
- **Đặt tên thư mục**: Chữ thường, dùng dấu gạch nối `my-component`
- **Đặt tên tệp**: Component Vue đặt tên theo PascalCase `MyComponent.vue`
- **Đặt tên biến**: Kiểu camelCase `userInfo`
- **Đặt tên hằng số**: Viết hoa toàn bộ, kèm dấu gạch dưới `API_BASE_URL`

### 2. Tổ chức code
- Nguyên tắc đơn trách nhiệm (Single Responsibility)
- Kiểm tra hợp lệ props của component
- Xử lý ranh giới lỗi (error boundary)
- Quy chuẩn chú thích hợp lý

### 3. Quy chuẩn commit
- feat: Tính năng mới
- fix: Sửa bug
- docs: Cập nhật tài liệu
- style: Định dạng code
- refactor: Tái cấu trúc code

## 🛠️ Lệnh phát triển

```bash
# Môi trường phát triển
npm run dev:[platform]

# Build production  
npm run build:[platform]

# Kiểm tra code
npm run lint

# Ví dụ
npm run dev:mp-weixin    # Phát triển WeChat Mini Program
npm run build:h5         # Build production cho H5
npm run build:app-plus   # Build production cho App
```

## 📝 Lưu ý

1. **Cấu hình môi trường**: Đảm bảo cấu hình đúng địa chỉ API và khóa (key) của các dịch vụ bên thứ ba
2. **Khác biệt nền tảng**: Chú ý khả năng tương thích API giữa các nền tảng
3. **Tài nguyên hình ảnh**: Dùng đường dẫn tương đối, chú ý tương thích đa nền tảng
4. **Xin cấp quyền**: Chỉ xin quyền của nền tảng khi cần
5. **Tương thích phiên bản**: Chú ý khả năng tương thích giữa các phiên bản uni-app

---

*Tài liệu này được tạo dựa trên phân tích phiên bản CRMEB 5.6.2, cập nhật lần cuối: 2026-01-19*  
*📧 Nếu có thắc mắc, vui lòng liên hệ đội ngũ phát triển: admin@crmeb.com*

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
