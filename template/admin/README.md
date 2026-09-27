# CRMEB Admin

## Quy chuẩn phát triển

Thống nhất sử dụng cú pháp ES6
Chú thích phương thức
/*
* th => tiêu đề bảng
* data => dữ liệu
* fileName => tên tệp
* fileType => loại tệp
* sheetName => tên trang tính (sheet)
  */
  export default function toExcel ({ th, data, fileName, fileType, sheetName })
  Chú thích dòng //

### Đặt tên

Thư mục trang: thư mục được đặt tên theo kiểu lạc đà (camelCase), ví dụ: danh sách người dùng userList
Ví dụ: mô-đun sản phẩm
product Sản phẩm
├─ product Quản lý sản phẩm
├─ productList Thư mục quản lý sản phẩm
├─ index.vue  Trang chủ
├─ components  Thành phần (component)
├─ tableFrom.vue
├─ tableList.vue
├─ handle Thư mục trang chức năng thao tác
├─ delete.vue
├─ productCategory Thư mục danh mục sản phẩm
├─ index.vue Trang chủ danh mục sản phẩm

Tên trang, thành phần (component), thư mục: đặt tên theo kiểu lạc đà chữ đầu viết thường (lowerCamelCase), ví dụ: danh sách người dùng userList

Tên lớp, tên hàm: kiểu lạc đà chữ đầu viết hoa (PascalCase), ví dụ: addUser
Tên biến: kiểu lạc đà chữ đầu viết thường, ví dụ: user hoặc userInfo _userinfo user-info
Hằng số: đặt tên bằng chữ in hoa toàn bộ, phân tách bằng dấu gạch dưới, ví dụ: VUE_APP_API_URl

### Quy chuẩn quản lý tệp
pages: mỗi mô-đun trang bắt buộc phải tạo thư mục riêng để phân biệt
api: API của mỗi mô-đun nằm trong một tệp
Thành phần (component): mỗi thành phần một thư mục
plugins: mỗi plugin một thư mục
vuex: quản lý trạng thái định tuyến, mỗi mô-đun tạo một thư mục trong modules
router: mỗi mô-đun tạo một thư mục trong modules
style: style nên ưu tiên dùng thành phần có sẵn của iView, common.less là style dùng chung của hệ thống, không được tùy tiện chỉnh sửa
Style dùng chung tùy chỉnh đặt trong style.less, mỗi lần thêm bắt buộc phải kèm chú thích, style riêng của trang thì viết ngay trong trang, định dạng đuôi less
Style của thành phần (component): thêm thư mục composents trong styles, tạo tệp style mới tương ứng với thư mục components
utils: các tệp js tiện ích tùy chỉnh được đặt tên riêng, thường không cần tạo thư mục mới

## Đặt tên mô-đun
~~~
├─ product Quản lý sản phẩm
├─ user Quản lý người dùng
├─ order Quản lý đơn hàng hệ thống
├─ setting Bảo trì cài đặt hệ thống, quản lý quyền hệ thống, quản lý menu hệ thống, quản lý CSKH
├─ chat Quản lý CSKH (danh sách, thêm, xóa, sửa)
├─ application Quản lý chức năng các mô-đun ứng dụng: OA WeChat, Mini Program, Alipay, Mini Program Baidu, Mini Program Toutiao
├─ system Nhật ký cập nhật hệ thống, quản lý cơ sở dữ liệu
├─ finance  Quản lý tài chính
├─ agent Quản lý tiếp thị liên kết
├─ marketing Phiếu giảm giá (coupon), điểm thưởng, mua chung, săn giảm giá (bargain), flash sale
├─ echarts Thống kê và phân tích dữ liệu
├─ notification  Quản lý thông báo, tin nhắn mẫu (danh sách, thông báo, thêm, sửa), SMS
├─ file Quản lý tệp đính kèm
├─ freight Quản lý mẫu phí vận chuyển, đơn vị vận chuyển
├─ merchant Quản lý cửa hàng (merchant)
├─ widget Thành phần, tiện ích nhỏ
└─ cms Quản lý bài viết
~~~
## Cấu trúc thư mục
Cấu trúc thư mục chính và mô tả:
~~~
├── public                      # Tài nguyên tĩnh
│   ├── favicon.ico            # Biểu tượng favicon
│   └── index.html             # html Mẫu
├── src                         # Mã nguồn
│   ├── api                    # Tất cả các yêu cầu (request)
│   │    └──account.js        # API liên quan đến đăng nhập
│   │    └──agent.js          # API liên quan đến tiếp thị liên kết (affiliate)
│   │    └──app.js            # API liên quan đến ứng dụng (Mini Program, OA WeChat)
│   │    └──cms.js            # API liên quan đến nội dung (quản lý bài viết, danh mục)
│   │    └──common.js         # API xóa dữ liệu bảng, lấy thông báo nhắc nhở
│   │    └──finance.js        # API liên quan đến tài chính
│   │    └──index.js          # API liên quan đến trang chủ
│   │    └──marketing.js      # API liên quan đến marketing
│   │    └──order.js          # API liên quan đến đơn hàng
│   │    └──product.js        # API liên quan đến sản phẩm
│   │    └──setting.js        # API liên quan đến cài đặt
│   │    └──system.js         # API liên quan đến bảo trì (cấu hình phát triển, bảo trì an ninh)
│   │    └──systemAdmin.js    # API liên quan đến quản trị viên (Cài đặt--Quản lý quyền--Danh sách quản trị viên)
│   │    └──systemMenus.js    # API liên quan đến quy tắc phân quyền (Cài đặt--Quản lý quyền--Quy tắc phân quyền)
│   │    └──uploadPictures.js # API liên quan đến tải lên hình ảnh đính kèm
│   │    └──user.js           # API liên quan đến thành viên
│   ├── assets                 # Hình ảnh, svg và các tài nguyên tĩnh khác
│   ├── components             # Thành phần (component) dùng chung
│   │    └──cards             # Thống kê
│   │    └──copyright         # Phần tuyên bố ở cuối footer của trang
│   │    └──customerInfo      # Chọn người dùng
│   │    └──echarts           # Biểu đồ thống kê
│   │    └──freightTemplate   # Mẫu phí vận chuyển
│   │    └──from              # Tạo biểu mẫu
│   │    └──goodsList         # Danh sách sản phẩm
│   │    └──iconFrom          # Thêm biểu tượng cho điều hướng
│   │    └──link              # Liên kết thẻ a
│   │    └──mde               # Ô nhập văn bản nhiều dòng
│   │    └──modelSure         # Hộp thoại xác nhận (modal)
│   │    └──newsCategory      # Trang quản lý tin bài (hình ảnh và văn bản)
│   │    └──publicSearchFrom  # Tìm kiếm ở đầu trang (không sử dụng)
│   │    └──quill             # Trình soạn thảo (không sử dụng)
│   │    └──referrerInfo      # Thông tin người giới thiệu
│   │    └──searchFrom        # Tìm kiếm trên trang đơn hàng
│   │    └──sendCoupons       # Gửi phiếu giảm giá
│   │    └──systemStore       # Thêm điểm nhận hàng
│   │    └──uploadPictures    # Tải lên ảnh
│   │    └──uploadVideo       # Tải lên video (dùng trong trình soạn thảo sản phẩm)
│   ├── i18n                   # Đa ngôn ngữ
│   ├── layouts                # Bố cục (layout)
│   │    └──header-breadcrumb # Kiểu breadcrumb ở đầu trang
│   │    └──header-collapse   # Biểu tượng ở đầu trang để thu gọn/mở rộng panel
│   │    └──header-fullscreen # Biểu tượng ở đầu trang để bật/tắt toàn màn hình
│   │    └──header-i18n       # Điều khiển đa ngôn ngữ ở đầu trang
│   │    └──header-log        # Biểu tượng nhật ký lỗi ở đầu trang
│   │    └──header-logo       # Logo ở đầu trang
│   │    └──header-notice     # Thông báo nhắc nhở ở đầu trang
│   │    └──header-reload     # Biểu tượng làm mới ở đầu trang
│   │    └──header-search     # Tìm kiếm ở đầu trang
│   │    └──header-setting    # Cài đặt phong cách trang
│   │    └──header-user       # Của tôi (trang cá nhân, đăng xuất)
│   │    └──menu-head         # 
│   │    └──menu-side         # Thanh điều hướng bên cạnh
│   │    └──tabs              # Các tab điều hướng ngang ở đầu trang
│   │    └──mixins            # Một tệp js dùng để lấy title khi cuộn ngang
│   ├── libs                   # Phương thức dùng chung
│   ├── menu                   # Cấu hình menu
│   ├── mixins                 # Mixin dùng chung
│   ├── mock                   # Dữ liệu giả lập (mock)
│   ├── pages                  # Tất cả các trang
│   │    └──account           # Liên quan đến trang đăng nhập
│   │         └──login        # Đăng nhập
│   │         └──register     # Đăng ký
│   │    └──agent             # Tiếp thị liên kết
│   │         └──agentManage  # Quản lý cộng tác viên
│   │    └──app               # Ứng dụng
│   │         └──routine      # Tin nhắn mẫu Mini Program
│   │         └──wechat       # OA WeChat
│   │              └──menus   # Menu WeChat
│   │              └──newsCategory   # Quản lý tin bài
│   │                   └──save      # Thêm tin bài
│   │              └──reply          # Trả lời tự động
│   │                   └──follow    # Trả lời khi theo dõi WeChat/trả lời từ khóa không hợp lệ
│   │                   └──keyword   # Trả lời theo từ khóa
│   │              └──user           # Người dùng
│   │                   └──tag       # Nhãn người dùng
│   │                   └──user      # Người dùng WeChat
│   │                   └──message   # Nhật ký hành vi người dùng
│   │    └──cms                      # Nội dung
│   │         └──addArticle          # Thêm bài viết/Sửa bài viết
│   │         └──article             # Quản lý bài viết
│   │         └──articleCategory     # Danh mục bài viết
│   │    └──finance                  # Tài chính
│   │         └──commission          # Lịch sử hoa hồng
│   │         └──financialRecords    # Lịch sử tài chính
│   │              └──bill           # Lịch sử dòng tiền
│   │              └──recharge       # Lịch sử nạp tiền
│   │         └──userExtract         # Yêu cầu rút tiền
│   │    └──index                    # Trang chủ
│   │    └──marketing                # Marketing
│   │         └──storeBargain        # Sản phẩm săn giảm giá
│   │         └──storeCombination    # Quản lý mua chung
│   │              └──combinaList    # Danh sách mua chung
│   │              └──create         # Thêm sản phẩm mua chung
│   │              └──index          # Sản phẩm mua chung
│   │         └──storeCoupon         # Tạo phiếu giảm giá
│   │         └──storeCouponIssue    # Danh sách phiếu giảm giá
│   │         └──storeCouponUser     # Lịch sử nhận của thành viên
│   │         └──storeSeckill        # Quản lý flash sale
│   │              └──index          # Sản phẩm flash sale
│   │              └──create         # Thêm sản phẩm flash sale
│   │         └──userPoint           # Nhật ký điểm thưởng
│   │    └──notify                   # Cài đặt SMS
│   │         └──smsConfig           # Tài khoản SMS
│   │         └──smsPay              # Mua gói SMS
│   │         └──smsTemplateApply    # Mẫu SMS
│   │    └──order                    # Quản lý đơn hàng
│   │    └──product                  # Sản phẩm
│   │         └──productAdd          # Thêm sản phẩm
│   │         └──productAttr         # Quy cách sản phẩm
│   │         └──productClassify     # Danh mục sản phẩm
│   │         └──productList         # Quản lý sản phẩm
│   │         └──productReply        # Quản lý đánh giá sản phẩm
│   │    └──setting                  # Cài đặt
│   │         └──cityDada            # Dữ liệu thành phố
│   │         └──clerkList           # Quản lý nhân viên xác nhận sử dụng
│   │         └──freight             # Đơn vị vận chuyển
│   │         └──setSystem           # Cài đặt hệ thống
│   │         └──shippingTemplates   # Mẫu phí vận chuyển
│   │         └──storeList           # Danh sách điểm nhận hàng
│   │         └──storeService        # Quản lý CSKH
│   │         └──systemAdmin         # Danh sách quản trị viên
│   │         └──systemMenus         # Quy tắc quyền
│   │         └──systemRole          # Quản lý vai trò
│   │         └──systemStore         # Cài đặt cửa hàng
│   │         └──user                # Trang cá nhân
│   │         └──verifyOrder         # Đơn hàng xác nhận sử dụng
│   │    └──system                   # Bảo trì
│   │         └──auth                # Giấy phép thương mại
│   │         └──clear               # Làm mới bộ nhớ đệm
│   │         └──configTab           # Cấu hình
│   │              └──index          # Danh mục cấu hình
│   │              └──list           # Danh sách cấu hình
│   │         └──error               # Trang lỗi
│   │              └──403            # 403
│   │              └──404            # 404
│   │              └──500            # 500
│   │         └──group               # Dữ liệu tổ hợp
│   │         └──maintain              
│   │              └──systemCleardata    # Xóa dữ liệu
│   │              └──systemDatabackup   # Sao lưu dữ liệu
│   │              └──systemFile         # Kiểm tra tệp
│   │                   └──opendir       # Quản lý tệp
│   │              └──systemLog          # Nhật ký hệ thống
│   │    └──user                         # Thành viên
│   │         └──group                   # Nhóm thành viên
│   │         └──label                   # Nhãn thành viên
│   │         └──level                   # Hạng thành viên
│   │         └──list                    # Quản lý thành viên
│   ├── plugins                           # Plugin
│   ├── router                            # Cấu hình route
│   │    └──modules                      # Module định tuyến của các trang
│   │         └──agent.js                     # Liên quan đến tiếp thị liên kết (affiliate)
│   │         └──app.js                       # Liên quan đến ứng dụng (Mini Program, OA WeChat)
│   │         └──cms.js                       # Liên quan đến nội dung (quản lý bài viết, danh mục bài viết)
│   │         └──echarts.js                   # Liên quan đến thống kê
│   │         └──finance.js                   # Liên quan đến tài chính
│   │         └──index.js                     # Liên quan đến trang chủ
│   │         └──marketing.js                 # Liên quan đến marketing
│   │         └──order.js                     # Liên quan đến đơn hàng
│   │         └──product.js                   # Liên quan đến sản phẩm
│   │         └──setting.js                   # Liên quan đến cài đặt
│   │         └──system.js                    # Liên quan đến bảo trì
│   │         └──user.js                      # Liên quan đến thành viên
│   │    └──index.js                          # Xuất định tuyến và xử lý chặn (interceptor)
│   │    └──routes.js                         # Tổng hợp định tuyến
│   ├── store                                  # Vuex Quản lý trạng thái
│   ├── utils                                  # Công cụ js
│   │    └──authLapse.js                      # Hộp thoại thông báo ủy quyền
│   │    └──modalForm.js                      # Hộp thoại biểu mẫu (modal)
│   │    └──videoCloud.js                     # Tải video lên lưu trữ đám mây (Qiniu, Tencent, Alibaba)
│   │    └──validate.js                       # Chuyển đổi timestamp thành thời gian;
│   │    └──public.js                         # Hộp thoại hỏi xác nhận (modal);
│   ├── styles            # Quản lý style
│   ├── setting.env.js    # Tệp cấu hình phát triển
│   ├── setting.js        # Tệp cấu hình nghiệp vụ
│   ├── main.js           # Tệp điểm vào, tải thành phần (component), khởi tạo, v.v.
│   └── App.vue           # Trang điểm vào
├── tests                  # Quản lý kiểm thử
├── alias.config.js        # Bí danh (alias), chỉ dùng để cấu hình cho WebStorm nhận diện alias, không có tác dụng thực tế
├── babel.config.js        # babel Cấu hình
├── jest.config.js         # jest Cấu hình
├── package.json           # package.json
└── vue.config.js          # Vue CLI 3 Cấu hình
~~~
## Phát triển và đóng gói dự án
~~~
# Vào thư mục dự án
$ cd admin

# Cài đặt các gói phụ thuộc
$ npm install

# Khởi chạy dự án (môi trường phát triển cục bộ)
$ npm run dev

# Đóng gói (build) dự án
$ npm run build
~~~

## Cấu hình tên miền gọi API


### Cấu hình môi trường phát triển
Đường dẫn tệp cấu hình: /.env.dev

*Cấu hình tên miền gọi API*

`$ VUE_APP_API_URL='http://ten-mien-cua-ban/adminapi'`

### Môi trường production

*Địa chỉ gọi API (http) hoặc (https)://www.crmeb.com(thay bằng tên miền của bạn)/adminapi, nếu không triển khai độc lập thì mặc định để trống*

`$ VUE_APP_API_URL=''`


