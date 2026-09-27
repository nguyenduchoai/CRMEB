# Mô tả chi tiết cấu trúc thư mục frontend trang quản trị CRMEB

## 📋 Tổng quan dự án

**Bộ công nghệ**: Vue.js 2.x + Element UI + Vuex + SCSS  
**Framework UI**: Element UI + VXE-Table + Vue Awesome Swiper  
**Phiên bản dự án**: CRMEB bản tiêu chuẩn 5.6.2  
**Mô hình phát triển**: Component hóa + Module hóa + Bố cục responsive  

## 🏗️ Thiết kế kiến trúc tổng thể

```
template/admin/
├── public/                    # Template HTML tĩnh
│   ├── image/                 # Tài nguyên biểu tượng website
│   ├── favicon.ico            # Biểu tượng website
│   └── index.html             # Template HTML điểm vào
├── src/                       # Thư mục mã nguồn
│   ├── api/                   # Tầng dịch vụ API (30+ module API)
│   ├── assets/                # Tầng tài nguyên tĩnh
│   ├── components/            # Tầng thành phần (80+ thành phần tái sử dụng)
│   ├── config/                # Tầng cấu hình
│   ├── directive/             # Tầng directive
│   ├── filters/               # Tầng filter
│   ├── i18n/                  # Tầng đa ngôn ngữ (i18n)
│   ├── layout/                # Tầng layout
│   ├── libs/                  # Tầng thư viện tiện ích
│   ├── mixins/                # Tầng mixin
│   ├── pages/                 # Tầng trang (17 module nghiệp vụ)
│   ├── plugin/                # Tầng plugin
│   ├── router/                # Tầng router
│   ├── services/              # Tầng service
│   ├── store/                 # Tầng quản lý trạng thái (17+ module Vuex)
│   ├── styles/                # Tầng style
│   ├── theme/                 # Tầng chủ đề
│   ├── utils/                 # Tầng hàm tiện ích
│   ├── App.vue                # Thành phần gốc của ứng dụng
│   ├── main.js                # Điểm vào ứng dụng
│   └── setting.js             # Cài đặt ứng dụng
├── package.json               # cấu hình phụ thuộc
└── .babelrc                   # Cấu hình Babel
```

## 📊 Phân tích chi tiết cấu trúc thư mục

### 1. 📁 api/ - Tầng dịch vụ API

Quản lý API được phân chia theo module nghiệp vụ:

```javascript
api/
├── account.js         # API quản lý tài khoản
├── agent.js           # API đại lý
├── app.js             # API cấu hình ứng dụng
├── cms.js             # API quản lý nội dung
├── common.js          # API chung
├── crud.js            # API thêm/xóa/sửa/tra cứu dùng chung
├── diy.php            # API cấu hình thiết kế giao diện
├── export.php         # API xuất dữ liệu
├── finance.js         # API tài chính
├── index.php          # API trang chủ
├── kefu_mobile.js     # API CSKH trên di động
├── kefu.js            # API hệ thống CSKH
├── live.js            # API livestream
├── lottery.js         # API hoạt động quay thưởng
├── marketing.js       # API hoạt động marketing
├── membershipLevel.js # API hạng thành viên
├── notification.php   # API tin nhắn thông báo
├── order.js           # API quản lý đơn hàng
├── product.js         # API quản lý sản phẩm
├── setting.php        # API cài đặt hệ thống
├── statistic.php      # API dữ liệu thống kê
├── system.php         # API cấu hình hệ thống
├── systemAdmin.php    # API quản trị viên
├── systemBackendRouting.php  # API cấu hình route
├── systemCodeGeneration.php  # API trình tạo code
├── systemMenus.php    # API quản lý menu
├── systemOutAccount.php # API tài khoản bên thứ ba
├── upload.php         # API tải lên
├── uploadPictures.php # API tải lên hình ảnh
└── user.php           # API quản lý người dùng
```

**Đặc điểm thiết kế API**:
- Thiết kế theo phong cách RESTful
- Chặn request (interceptor) và xử lý response thống nhất
- Hỗ trợ tải lên và tải xuống tệp
- Cơ chế xử lý lỗi hoàn chỉnh

### 2. 📁 components/ - Tầng component

Thư viện component phong phú, áp dụng tư duy thiết kế nguyên tử (atomic design):

#### 2.1 Component cơ bản
- `Pagination/` - Component phân trang
- `pagesHeader/` - Component phần đầu trang
- `common-icon/` - Component biểu tượng dùng chung
- `cards/` - Component thẻ (card)
- `copyright/` - Component bản quyền
- `icons/` - Component chọn biểu tượng

#### 2.2 Component biểu mẫu
- `cropperImg/` - Component cắt ảnh
- `from/` - Component biểu mẫu
- `iconFrom/` - Component biểu mẫu biểu tượng
- `dateRadio/` - Component chọn ngày dạng radio
- `labelList/` - Component danh sách nhãn
- `goodsLabel/` - Component nhãn sản phẩm

#### 2.3 Component tải lên
- `uploadPictures/` - Component tải ảnh lên
  - `model.vue` - Hộp thoại tải lên
  - `upload.vue` - Component tải lên
- `uploadVideo/` - Component tải video lên
- `uploadVideo2/` - Component tải video lên v2
- `upload/` - Component tải lên dùng chung

#### 2.4 Component nghiệp vụ
- `couponList/` - Component danh sách phiếu giảm giá
- `customerInfo/` - Component thông tin khách hàng
- `goodsList/` - Component danh sách sản phẩm
- `freightTemplate/` - Component mẫu phí vận chuyển
- `hotpotModal/` - Component hộp thoại vùng nóng (hotspot)
- `link/` - Component chọn liên kết
- `linkaddress/` - Component địa chỉ liên kết

#### 2.5 Component trực quan hóa
- `echarts/` - Component biểu đồ
- `echartsNew/` - Component biểu đồ phiên bản mới
- `tree-table/` - Component bảng dạng cây

#### 2.6 Component cấu hình cho giao diện di động
```
mobileConfig/           # Thư viện thành phần DIY cho di động
├── c_auxiliary_box.vue     # Hộp phụ trợ
├── c_auxiliary_line.vue    # Đường phân cách
├── c_banner.vue            # Ảnh trình chiếu
├── c_home_bargain.vue      # Thành phần săn giảm giá
├── c_home_comb.vue         # Thành phần mua chung
├── c_home_coupon.vue       # Component phiếu giảm giá
├── c_home_goods_list.vue   # Danh sách sản phẩm
├── c_home_hot.vue          # Thành phần phổ biến
├── c_home_menu.vue         # Thành phần menu
├── c_home_pink.vue         #  Mua chung (Pink)
├── c_home_product.vue      # Thành phần sản phẩm
├── c_home_seckill.vue      # Thành phần flash sale
├── c_home_service.vue      # Trung tâm dịch vụ
├── c_home_store_list.vue   # Danh sách cửa hàng
├── c_home_title.vue        # Thành phần tiêu đề
├── c_hotspot.vue           # Thành phần vùng nóng
├── c_nav_bar.vue           # Thanh điều hướng
├── c_new_list.vue          # Danh sách tin tức
├── c_new_vip.vue           # VIP mới
├── c_news_roll.vue         # Tin tức cuộn
├── c_picture_cube.vue      # Khối ảnh
├── c_points_mall.vue       # Cửa hàng đổi điểm
├── c_presale.vue           # Thành phần đặt trước
├── c_ranking.vue           # Bảng xếp hạng
├── c_search_box.vue        # Ô tìm kiếm
├── c_short_video.vue       # Video ngắn
├── c_sign_in.vue           # Thành phần điểm danh
├── c_ueditor_box.vue       # Khung văn bản định dạng
├── c_userInfor.vue         # Thông tin người dùng
├── c_video.vue             # Thành phần video
├── c_wechat_attention.vue  # Theo dõi WeChat
├── c_wechat_live.vue       # Livestream WeChat
├── index.js                # Export thành phần
├── pageFoot.vue            # Chân trang
└── pageTitle.vue           # Tiêu đề trang
```

#### 2.7 Component cấu hình bên phải cho giao diện di động
```
mobileConfigRight/      # Thư viện thành phần cấu hình bên phải cho di động
├── c_bg_color.vue          # Màu nền
├── c_brand.vue             # Thành phần thương hiệu
├── c_button_img.vue        # Ảnh nút
├── c_button_style.vue      # Kiểu nút
├── c_cascader.vue          # Chọn phân cấp
├── c_checkbox.vue          # Hộp kiểm
├── c_classify.vue          # Thành phần danh mục
├── c_comb_data.vue         # Dữ liệu mua chung
├── c_fillet.vue            # Cài đặt bo góc
├── c_foot.vue              # Cấu hình chân trang
├── c_goods_label.vue       # Nhãn sản phẩm
├── c_goods_search.vue      # Tìm kiếm sản phẩm
├── c_goods.vue             # Thành phần sản phẩm
├── c_hot_box.vue           # Hộp vùng nóng
├── c_hot_imgs.vue          # Ảnh phổ biến
├── c_hot_word.vue          # Thành phần từ khóa phổ biến
├── c_input_item.vue        # Mục nhập
└── c_input_number.vue      # Nhập số
```

#### 2.8 Component thiết kế giao diện DIY
```
diyComponents/          # Thư viện thành phần thiết kế giao diện DIY
├── c_bg_color.vue          # Màu nền
├── c_goods.vue             # Thành phần sản phẩm
├── c_hot_word.vue          # Thành phần từ khóa phổ biến
├── c_input_list.vue        # Danh sách nhập
├── c_input_number.vue      # Nhập số
├── c_is_show.vue           # Điều khiển hiển thị
├── c_page_ueditor.vue      # Trình soạn thảo trang
├── c_select.vue            # Bộ chọn
├── c_tab_bar.vue           # Thanh tab
├── c_tab.vue               # Thành phần tab
├── c_txt_list.vue          # Danh sách văn bản
├── c_txt_tab.vue           # Tab văn bản
├── c_upload_img.vue        # Tải lên ảnh
├── c_upload_list.vue       # Tải lên danh sách
├── c_upload_video.vue      # Tải lên video
└── index.js                # Export thành phần
```

### 3. 📁 pages/ - Tầng trang (17 module nghiệp vụ)

#### 3.1 Quản lý tài khoản (account)
```
pages/account/
├── accountList/         # Danh sách tài khoản
├── balanceWithdraw/     # Rút tiền từ số dư
├── expenditure/         # Lịch sử chi
├── recharge/            # Lịch sử nạp tiền
└── revenue/             # Lịch sử thu
```

#### 3.2 Quản lý đại lý (agent)
```
pages/agent/
├── agentList/           # Danh sách đại lý
├── agentOrder/          # Đơn hàng đại lý
└── levelList/           # Hạng đại lý
```

#### 3.3 Cấu hình ứng dụng (app)
```
pages/app/
├── appStyle/            # Phong cách ứng dụng
├── appVersion/          # Phiên bản ứng dụng
├── distribution/        # Cấu hình tiếp thị liên kết
├── diy/                 # Thiết kế giao diện DIY
│   ├── index/           # Thiết kế trang chủ
│   ├── menu/            # Thiết kế menu
│   ├── page/            # Thiết kế giao diện
│   └── user/            # Thiết kế trang người dùng
├── login_record/        # Lịch sử đăng nhập
├── maintenance/         # Bảo trì hệ thống
├── membershipLevel/     # Hạng thành viên
├── notice/              # Thông báo
├── userAgreement/       # Thỏa thuận người dùng
└── wechatMenu/          # Menu WeChat
```

#### 3.4 Quản lý nội dung (cms)
```
pages/cms/
├── article/             # Quản lý bài viết
│   ├── articleCate/     # Danh mục bài viết
│   └── articleList/     # Danh sách bài viết
├── feedback/            # Quản lý phản hồi
├── articleAttr/         # Thuộc tính bài viết
├── articleTag/          # Nhãn bài viết
├── articleCreate/       # Tạo bài viết
├── links/               # Liên kết hữu ích
├── visit/               # Lịch sử truy cập
└── notice/              # Quản lý thông báo
```

#### 3.5 Vận hành tự động hóa (crud)
```
pages/crud/
├── generate/            # Tạo mã nguồn
├── generateEdit/        # Chỉnh sửa bản tạo
├── index/               # Trang chủ CRUD
├── table/               # Cấu hình bảng
└── template/            # Quản lý mẫu
```

#### 3.6 Quản lý tiếp thị liên kết (division)
```
pages/division/
├── setting/             # Cài đặt tiếp thị liên kết
├── level/               # Cấp độ CTV
├── user/                # Người dùng tiếp thị liên kết
├── commission/          # Quản lý hoa hồng
├── extraction/          # Quản lý rút tiền
├── order/               # Đơn hàng tiếp thị liên kết
├── statistics/          # Thống kê tiếp thị liên kết
└── contract/            # Thỏa thuận tiếp thị liên kết
```

#### 3.7 Quản lý tài chính (finance)
```
pages/finance/
├── bill/                # Quản lý giao dịch
├── userbill/            # Giao dịch người dùng
├── extract/             # Quản lý rút tiền
├── recharge/            # Quản lý nạp tiền
├── invoice/             # Quản lý hóa đơn
└── account/             # Quản lý tài khoản
```

#### 3.8 Bảng điều khiển trang chủ (index)
```
pages/index/
├── index/               # Trang chủ quản trị
└── owerManagement/      # Cài đặt quản trị viên
```

#### 3.9 Quản lý CSKH (kefu)
```
pages/kefu/
├── chat/                # Chat CSKH
├── kefuList/            # Danh sách nhân viên CSKH
├── kefuRecord/          # Lịch sử CSKH
├── kefuSetting/         # Cài đặt CSKH
└── userList/            # Danh sách người dùng
```

#### 3.10 Quản lý marketing (marketing)
```
pages/marketing/
├── bargain/             # Hoạt động săn giảm giá
├── coupon/              # Quản lý phiếu giảm giá
├── integral/            # Cửa hàng đổi điểm
├── lottery/             # Hoạt động quay thưởng
├── promotion/           # Hoạt động khuyến mãi
├── seckill/             # Hoạt động flash sale
├── storeCoupon/         # Phiếu giảm giá cửa hàng
└── combination/         # Hoạt động mua chung
```

#### 3.11 Quản lý thông báo (notify)
```
pages/notify/
├── sms/                 # Thông báo SMS
├── template/            # Mẫu thông báo
└── sysMessages/         # Thông báo hệ thống
```

#### 3.12 Quản lý đơn hàng (order)
```
pages/order/
├── orderList/           # Danh sách đơn hàng
├── orderDetail/         # Chi tiết đơn hàng
├── orderCancellation/   # Xác nhận sử dụng đơn hàng
├── deliver/             # Quản lý giao hàng
├── batchDeliver/        # Giao hàng hàng loạt
├── exportOrder/         # Xuất đơn hàng
└── orderStatistics/     # Thống kê đơn hàng
```

#### 3.13 Quản lý sản phẩm (product)
```
pages/product/
├── productList/         # Danh sách sản phẩm
├── productAttr/         # Thuộc tính sản phẩm
├── productCate/         # Danh mục sản phẩm
├── productCreate/       # Tạo sản phẩm
├── productReply/        # Đánh giá sản phẩm
├── productSpec/         # Quy cách sản phẩm
├── stock/               # Quản lý tồn kho
└── storeStaff/          # Nhân viên cửa hàng
```

#### 3.14 Cài đặt hệ thống (setting)
```
pages/setting/
├── system/              # Cài đặt hệ thống
│   ├── clearCache/      # Dọn cache
│   ├── systemLog/       # Nhật ký hệ thống
│   └── systemStore/     # Cài đặt cửa hàng
└── systemMenus/         # Cài đặt menu
```

#### 3.15 Báo cáo thống kê (statistic)
```
pages/statistic/
├── analysis/            # Phân tích dữ liệu
├── userChart/          # Thống kê người dùng
├── orderChart/         # Thống kê đơn hàng
├── productChart/       # Thống kê sản phẩm
├── financeChart/       # Thống kê tài chính
└── marketingChart/     # Thống kê marketing
```

#### 3.16 Quản lý hệ thống (system)
```
pages/system/
├── admin/              # Quản trị viên
├── adminAdd/           # Thêm quản trị viên
├── adminRole/          # Quản lý vai trò
├── systemRole/         # Vai trò hệ thống
├── systemMenus/        # Quản lý menu
├── systemLogs/         # Quản lý log
├── systemStorage/      # Cài đặt lưu trữ
├── systemClear/        # Cài đặt dọn dẹp
├── systemQueue/        # Cấu hình hàng đợi
├── systemNotice/       # Thông báo hệ thống
└── codeGeneration/     # Tạo mã nguồn
```

#### 3.17 Quản lý người dùng (user)
```
pages/user/
├── userList/           # Danh sách người dùng
├── userLevel/          # Hạng người dùng
├── userLabel/          # Nhãn người dùng
├── userMember/         # Quản lý thành viên
├── userFrom/           # Nguồn người dùng
├── userNotice/         # Thông báo người dùng
├── userExport/         # Xuất người dùng
└── userBalance/        # Số dư người dùng
```

### 4. 📁 layout/ - Tầng bố cục

```
layout/
├── aside/                  # Thanh bên
│   └── index.vue
├── component/              # Thành phần layout
│   ├── Breadcrumb/         # Breadcrumb
│   ├── Center/             # Bố cục căn giữa
│   ├── Columns/            # Bố cục nhiều cột
│   ├── FirmOrder/          # Đơn hàng ghim trên cùng
│   ├── Header/             # Thành phần header
│   ├── Iocn/               # Thành phần biểu tượng
│   ├── Logo/               # Thành phần LOGO
│   └── NavBars/            # Thanh điều hướng
│       ├── index.vue
│       ├── breadcrumb.vue
│       ├── setings.vue     # Popup cài đặt
│       └── topic.vue
├── main.vue                # Bố cục chính
├── mixins/                 # Mixin bố cục
│   └── mobileMenu.js
├── navBars/                # Thành phần thanh điều hướng
│   └── breadcrumbs.vue
└── upgrade/                # Thông báo nâng cấp
    └── index.vue
```

### 5. 📁 store/ - Tầng quản lý trạng thái

Quản lý trạng thái Vuex theo module (17+ module):

```javascript
store/
├── module/                # Module trạng thái
│   ├── user.js           # Trạng thái người dùng
│   ├── app.js            # Trạng thái ứng dụng
│   ├── menus.js          # Trạng thái menu
│   ├── menu.js           # Dữ liệu menu
│   ├── userInfo.js       # Thông tin người dùng
│   ├── userLevel.js      # Hạng người dùng
│   ├── order.js          # Trạng thái đơn hàng
│   ├── media.js          # Trạng thái media
│   ├── goodSelect.js     # Chọn sản phẩm
│   ├── moren.js          # Trạng thái mặc định
│   ├── shopping.js       # Trạng thái mua sắm
│   ├── fresh.js          # Làm mới trạng thái
│   ├── kefu.js           # Trạng thái CSKH
│   ├── integralOrder.js  # Đơn đổi điểm
│   ├── mobildConfig.js   # Cấu hình di động
│   ├── upgrade.js        # Trạng thái nâng cấp
│   ├── layout.js         # Trạng thái bố cục
│   ├── themeConfig.js    # Cấu hình chủ đề
│   ├── routesList.js     # Danh sách route
│   ├── tagsViewRoutes.js # Route tab
│   ├── userInfos.js      # Thông tin người dùng
│   └── keepAliveNames.js # Trang được cache
└── index.js              # Điểm vào chính của Store
```

**Đặc điểm quản lý trạng thái**:
- Lưu trữ bền vững cho Vuex
- Phân chia trạng thái theo module
- Kiểm soát chặt chẽ việc thay đổi trạng thái
- Hỗ trợ responsive trên nhiều thiết bị

### 6. 📁 libs/ - Tầng thư viện tiện ích

```javascript
libs/
├── util.js           # Hàm tiện ích dùng chung
├── wechat.js         # Wrapper cho WeChat SDK
├── dialog.js         # Công cụ popup
├── timeOptions.js    # Tùy chọn thời gian
├── loading.js        # Animation loading
├── auth.js           # Công cụ phân quyền
├── authLapse.js      # Xử lý quyền hết hạn
├── public.js         # Công cụ dùng chung
├── formCreate        # Trình tạo form
└── excel.js          # Xử lý Excel
```

### 7. 📁 utils/ - Tầng hàm tiện ích

```javascript
utils/
├── storage.js        # Công cụ lưu trữ cục bộ
├── modalForm.js      # Form popup
├── newToExcel.js     # Xuất Excel
├── videoCloud.js     # Xử lý video trên cloud
├── public.js         # Phương thức dùng chung
├── authLapse.js      # Quyền hết hạn
├── public_fun.js     # Hàm dùng chung
├── loading.js        # Animation loading
└── auth.js           # Xác thực quyền
```

### 8. 📁 router/ - Tầng định tuyến (router)

```javascript
router/
├── index.js          # File route chính
└── routers.js        # Cấu hình route
```

**Đặc điểm định tuyến**:
- Tải route động
- Kiểm soát quyền truy cập route
- Lọc menu theo quyền
- Bộ nhớ đệm Keep-alive

### 9. 📁 static/ - Tầng tài nguyên tĩnh

#### 9.1 Tài nguyên style
```
static/css/ Hoặc assets/
├── fonts/            # File font
│   └── D-DIN-PRO-*.ttf
├── iconfont/         # Font biểu tượng
│   ├── iconfont.css
│   ├── iconfont.js
│   └── iconfont.json
├── iconfontYI/       # Biểu tượng nghiệp vụ
│   └── iconfontYI.css
├── icons/            # Biểu tượng hệ thống
└── js/               # Tài nguyên JS
    ├── canvas-nest.min.js
    ├── jigsaw.css
    └── jigsaw.js
```

#### 9.2 Tài nguyên hình ảnh
```
assets/images/
├── error-page/       # Hình ảnh trang lỗi
│   ├── error-401.svg
│   ├── error-404.svg
│   └── error-500.svg
├── login-bg.jpg      # Nền trang đăng nhập
├── logo.png          # Ảnh LOGO
├── default.jpg       # Ảnh mặc định
└── Các tài nguyên hình ảnh nghiệp vụ
```

### 10. 📁 theme/ - Tầng chủ đề (theme)

```
theme/
├── index.scss        # Điểm vào chủ đề
├── mixin.scss        # Mixin SCSS
├── var.scss          # Định nghĩa biến
└── element/          # Ghi đè theme Element
```

### 11. 📁 directive/ - Tầng directive

```
directive/
├── limit.js          # Directive giới hạn nhập liệu
├── copy.js           # Directive sao chép
├── auth.js           # Directive phân quyền
├── clickOutside.js   # Directive click bên ngoài
└── waves/            # Directive hiệu ứng gợn sóng
```

### 12. 📁 i18n/ - Tầng quốc tế hóa

```
i18n/
├── lang/             # Gói ngôn ngữ
│   ├── zh.js         # Tiếng Trung
│   └── en.js         # Tiếng Anh
└── index.js          # Cấu hình đa ngôn ngữ (i18n)
```

### 13. 📁 mixins/ - Tầng mixin

```javascript
mixins/
├── mobileMenu.js     # Mixin menu di động
├── popuppicker.js    # Mixin bộ chọn popup
└── lists.js          # Mixin trang danh sách
```

### 14. 📁 plugin/ - Tầng plugin

```
plugin/
├── emoji-awesome/    # Plugin emoji
│   └── css/
└── ...
```

## 🚀 Đặc tính kỹ thuật cốt lõi

### 1. Tích hợp UI framework
```javascript
// Element UI
Vue.use(Element, { i18n, size: 'small' })

// VXE-Table (bảng hiệu năng cao)
Vue.use(VxeTable)
Vue.use(VxeUIAll)

// Bảng dạng cây
Vue.use(TreeTable)
Vue.use(VOrgTree)
```

### 2. Thư viện component nâng cao
- **VueAwesomeSwiper** - Component trình chiếu (carousel)
- **VueLazyload** - Tải ảnh trì hoãn (lazy load)
- **Viewer** - Trình xem ảnh
- **VueDND** - Kéo thả để sắp xếp
- **formCreate** - Trình tạo biểu mẫu
- **VueCodeMirror** - Trình soạn thảo code
- **VueTreeList** - Danh sách dạng cây

### 3. Kiểm soát quyền
- Quản lý quyền theo vai trò
- Tải route động
- Lọc menu theo quyền
- Kiểm soát quyền đến cấp nút bấm

### 4. Bố cục responsive
```javascript
// Phát hiện thiết bị
matchMedia('(max-width: 600px)')   // Di động
matchMedia('(max-width: 992px)')   // Máy tính bảng
else                               // Máy tính để bàn
```

### 5. Trực quan hóa dữ liệu
- Tích hợp biểu đồ ECharts
- Component biểu đồ tùy chỉnh
- Cập nhật dữ liệu thời gian thực

## 📋 Quy chuẩn phát triển

### 1. Quy tắc đặt tên
- **Đặt tên thư mục**: Chữ thường, dùng dấu gạch nối
- **Đặt tên tệp**: Component Vue đặt tên theo PascalCase
- **Đặt tên biến**: Theo camelCase
- **Đặt tên style**: Theo BEM hoặc CSS Modules

### 2. Tổ chức code
- Mỗi component chỉ đảm nhận một trách nhiệm
- Kiểm tra kiểu dữ liệu Props
- Xử lý ranh giới lỗi (error boundary)
- Chú thích hợp lý

### 3. Quy ước commit Git
- feat: Tính năng mới
- fix: Sửa bug
- docs: Cập nhật tài liệu
- style: Định dạng code
- refactor: Tái cấu trúc

## 🛠️ Lệnh phát triển

```bash
# Môi trường phát triển
npm run dev
npm run serve

# Build production
npm run build

# Kiểm tra code
npm run lint

# Kiểm thử đơn vị
npm run test
```

## 📝 Lưu ý

1. **Cấu hình môi trường**: Cấu hình địa chỉ API và khóa bí mật
2. **Thiết lập quyền**: Cấu hình đúng quyền cho vai trò
3. **Bảo mật dữ liệu**: Mã hóa dữ liệu nhạy cảm
4. **Tối ưu hiệu năng**: Sử dụng Keep-alive hợp lý
5. **Tính tương thích**: Chú ý khả năng tương thích với trình duyệt

---

*Tài liệu này được tạo dựa trên phân tích phiên bản CRMEB 5.6.2, cập nhật lần cuối: 2026-01-19*  
*📧 Nếu có thắc mắc, vui lòng liên hệ đội ngũ phát triển: admin@crmeb.com*

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
