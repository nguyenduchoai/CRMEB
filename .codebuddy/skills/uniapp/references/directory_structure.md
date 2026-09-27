# Tài liệu cấu trúc thư mục UniApp

## 1. Tổng quan

Tài liệu này mô tả cấu trúc thư mục phía di động UniApp trong dự án CRMEB, bao gồm chức năng của từng thư mục, cách tổ chức file, v.v., nhằm giúp lập trình viên hiểu cấu trúc dự án và nâng cao hiệu quả phát triển.

## 2. Cấu trúc thư mục gốc của dự án

```
template/uni-app/
├── api/                  # API thư mục
├── config/               # Thư mục cấu hình
├── libs/                 # Thư mục file thư viện
├── mixins/               # Thư mục mixin
├── store/                # Thư mục quản lý trạng thái
├── utils/                # Thư mục lớp tiện ích
├── App.vue               # Thành phần điểm vào ứng dụng
├── main.js               # Tệp điểm vào của ứng dụng
├── manifest.json         # File cấu hình ứng dụng
├── package.json          # File cấu hình dự án
├── pages.json            # File cấu hình route trang
├── uni.scss              # UniApp File style toàn cục
└── vue.config.js         # Vue Tệp cấu hình
```

## 3. Cấu trúc thư mục API (api/)

```
api/
├── activity.js           # API liên quan đến hoạt động
├── admin.js              # API liên quan đến quản lý
├── api.js                # Cấu hình API cơ sở
├── kefu.js               # API liên quan đến CSKH
├── lottery.js            # API liên quan đến quay thưởng
├── order.js              # API liên quan đến đơn hàng
├── public.js             # API chung
├── store.js              # API liên quan đến cửa hàng
└── user.js               # API liên quan đến người dùng
```

- **Chức năng**: Đóng gói các API giao tiếp với backend
- **Cấu trúc**: Chia file API theo mô-đun nghiệp vụ
- **Đặc điểm**: Xử lý thống nhất header request, chặn response (interceptor), xử lý lỗi, v.v.
- **Cách gọi**: Dùng `import` để nhập vào và sử dụng

## 4. Cấu trúc thư mục cấu hình (config/)

```
config/
├── app.js                # Cấu hình ứng dụng
├── cache.js              # Cấu hình bộ nhớ đệm
└── socket.js             # WebSocket Cấu hình
```

- **Chức năng**: Lưu trữ tất cả tệp cấu hình của dự án
- **Cấu trúc**: Chia tệp cấu hình theo module chức năng
- **Đặc điểm**: Quản lý cấu hình tập trung, dễ bảo trì và chỉnh sửa
- **Thứ tự tải**: Được tải khi ứng dụng khởi động

## 5. Cấu trúc thư mục thư viện (libs/)

```
libs/
├── chat.js               # Chức năng liên quan đến chat
├── login.js              # Chức năng liên quan đến đăng nhập
├── new_chat.js           # Chức năng chat mới
├── order.js              # Chức năng liên quan đến đơn hàng
├── routine.js            # Chức năng liên quan đến Mini Program
├── uniApi.js             # UniApp API wrapper
└── wechat.js             # Chức năng liên quan đến WeChat
```

- **Chức năng**: Lưu trữ các file thư viện dùng chung và mô-đun chức năng
- **Cấu trúc**: Chia file thư viện theo chức năng
- **Đặc điểm**: Độc lập với code trang, dễ tái sử dụng
- **Vai trò**: Cung cấp các chức năng và dịch vụ dùng chung

## 6. Cấu trúc thư mục mixin (mixins/)

```
mixins/
└── color.js              # Mixin liên quan đến màu sắc
```

- **Chức năng**: Lưu trữ các đối tượng mixin của Vue
- **Cấu trúc**: Chia file mixin theo chức năng
- **Đặc điểm**: Tái sử dụng code, tránh lặp lại logic
- **Vai trò**: Cung cấp phương thức và dữ liệu dùng chung cho các component

## 7. Cấu trúc thư mục quản lý trạng thái (store/)

```
store/
├── getters.js            # Getter trạng thái
└── index.js              # Điểm vào quản lý trạng thái
```

- **Chức năng**: Lưu trữ các file liên quan đến quản lý trạng thái Vuex
- **Cấu trúc**: Chia file theo quy chuẩn của Vuex
- **Đặc điểm**: Quản lý tập trung trạng thái ứng dụng, cho phép các component giao tiếp với nhau
- **Vai trò**: Quản lý trạng thái toàn cục, như thông tin người dùng, dữ liệu giỏ hàng, v.v.

## 8. Cấu trúc thư mục lớp tiện ích (utils/)

```
utils/
├── cache.js              # Công cụ cache
├── emoji.js              # Công cụ emoji
├── index.js              # Điểm vào lớp tiện ích
├── lang.js               # Công cụ ngôn ngữ
├── request.js            # Công cụ request mạng
├── theme.js              # Công cụ chủ đề
├── util.js               # Công cụ dùng chung
└── validate.js           # Công cụ kiểm tra dữ liệu
```

- **Chức năng**: Lưu trữ các lớp tiện ích dùng chung
- **Cấu trúc**: Chia các tệp tiện ích theo chức năng
- **Đặc điểm**: Cung cấp các chức năng dùng chung, thuận tiện tái sử dụng
- **Vai trò**: Xử lý các thao tác dùng chung như bộ nhớ đệm, yêu cầu mạng, kiểm tra hợp lệ, v.v.

## 9. Cấu trúc thư mục trang

### 9.1 Cấu hình trang (pages.json)

```json
{
  "pages": [
    {
      "path": "pages/index/index",
      "style": {
        "navigationBarTitleText": "Trang chủ"
      }
    },
    {
      "path": "pages/user/index",
      "style": {
        "navigationBarTitleText": "Trang cá nhân"
      }
    }
  ],
  "subPackages": [
    {
      "root": "pages/order",
      "pages": [
        {
          "path": "index",
          "style": {
            "navigationBarTitleText": "Danh sách đơn hàng"
          }
        }
      ]
    }
  ]
}
```

- **Chức năng**: Cấu hình route trang, style thanh điều hướng, v.v.
- **Cấu trúc**: Cấu hình theo cấp bậc trang
- **Đặc điểm**: Hỗ trợ tải theo gói con (subpackage), tối ưu dung lượng ứng dụng
- **Vai trò**: Định nghĩa cấu trúc trang và style điều hướng của ứng dụng

## 10. File cấu hình ứng dụng

### 10.1 manifest.json

```json
{
  "name": "Cửa hàng CRMEB",
  "appid": "__UNI__APPID__",
  "description": "Cửa hàng CRMEB bản di động",
  "versionName": "1.0.0",
  "versionCode": "100",
  "transformPx": true,
  "uniStatistics": {
    "enable": true
  },
  "app-plus": {
    "usingComponents": true,
    "nvueStyleCompiler": "uni-app"
  },
  "mp-weixin": {
    "appid": "wx_appid",
    "setting": {
      "urlCheck": false
    },
    "usingComponents": true
  }
}
```

- **Chức năng**: Cấu hình thông tin cơ bản của ứng dụng, cấu hình nền tảng, v.v.
- **Cấu trúc**: Chia cấu hình theo nền tảng
- **Đặc điểm**: Hỗ trợ cấu hình đa nền tảng, như WeChat Mini Program, App, v.v.
- **Vai trò**: Định nghĩa thông tin cấu hình toàn cục của ứng dụng

### 10.2 package.json

```json
{
  "name": "crmeb-uni-app",
  "version": "1.0.0",
  "description": "Cửa hàng CRMEB bản di động",
  "main": "main.js",
  "scripts": {
    "dev": "npm run dev:mp-weixin",
    "dev:mp-weixin": "cross-env NODE_ENV=development UNI_PLATFORM=mp-weixin vue-cli-service uni-build --watch",
    "build": "npm run build:mp-weixin",
    "build:mp-weixin": "cross-env NODE_ENV=production UNI_PLATFORM=mp-weixin vue-cli-service uni-build"
  },
  "dependencies": {
    "vue": "^2.6.11",
    "vuex": "^3.4.0"
  }
}
```

- **Chức năng**: Cấu hình các gói phụ thuộc, script của dự án, v.v.
- **Cấu trúc**: Định dạng cấu hình npm chuẩn
- **Đặc điểm**: Quản lý các gói phụ thuộc của dự án, định nghĩa script build
- **Vai trò**: Quản lý các gói phụ thuộc và quy trình build của dự án

## 11. Thực tiễn tốt nhất cho cấu trúc thư mục

### 11.1 Quy tắc đặt tên

- **Tên thư mục**: Chữ thường, các từ phân cách bằng dấu gạch dưới
- **Tên tệp**: Chữ thường, các từ phân tách bằng dấu gạch dưới
- **Tên component**: Dùng kiểu đặt tên PascalCase
- **Tên phương thức**: Dùng kiểu đặt tên camelCase
- **Tên biến**: Dùng kiểu đặt tên camelCase

### 11.2 Nguyên tắc tổ chức

- **Module hóa**: Tổ chức cấu trúc thư mục theo module chức năng
- **Kiến trúc phân lớp**: Tuân theo kiến trúc phát triển theo hướng component của Vue
- **Đơn trách nhiệm**: Mỗi thư mục và tệp chỉ đảm nhận một chức năng
- **Khả năng mở rộng**: Dễ dàng thêm chức năng và module mới
- **Dễ bảo trì**: Dễ hiểu và dễ bảo trì

### 11.3 Khuyến nghị khi phát triển

- **Tuân thủ quy chuẩn UniApp**: Tuân thủ quy chuẩn cấu trúc thư mục chính thức của UniApp
- **Chia module hợp lý**: Chia module hợp lý theo chức năng nghiệp vụ
- **Tránh thư mục lồng quá sâu**: Cấp thư mục không nên quá sâu, thường không quá 4 cấp
- **Giữ thư mục gọn gàng**: Kịp thời dọn dẹp các tệp và thư mục không dùng đến
- **Tài liệu hóa**: Bổ sung tài liệu mô tả cho các thư mục quan trọng

## 12. Sự cố thường gặp

### 12.1 Vấn đề quyền thư mục

- **Vấn đề**: Một số thư mục không có quyền đọc/ghi
- **Giải pháp**: Đảm bảo thư mục dự án được cấp đúng quyền đọc/ghi

### 12.2 Vấn đề cấu hình route trang

- **Vấn đề**: Không truy cập được trang sau khi thêm mới
- **Giải pháp**: Thêm cấu hình route cho trang trong pages.json

### 12.3 Vấn đề tải theo gói con (subpackage)

- **Vấn đề**: Dung lượng ứng dụng quá lớn, không thể tải lên nền tảng Mini Program
- **Giải pháp**: Dùng cơ chế tải theo gói con, chia các trang vào những gói con khác nhau

### 12.4 Cấu trúc thư mục lộn xộn

- **Vấn đề**: Cấu trúc thư mục không rõ ràng, khó bảo trì
- **Giải pháp**: Tổ chức lại cấu trúc thư mục, tuân theo nguyên tắc module hóa

## 13. Tài liệu tham khảo

- [Tài liệu chính thức UniApp](https://uniapp.dcloud.io/)
- [Tài liệu chính thức của Vue](https://cn.vuejs.org/)
- [Tài liệu chính thức Vuex](https://vuex.vuejs.org/zh/)
- [Tài liệu phát triển WeChat Mini Program](https://developers.weixin.qq.com/miniprogram/dev/framework/)

## 14. Tổng kết

Tài liệu này mô tả cấu trúc thư mục của phần di động UniApp trong dự án CRMEB, bao gồm chức năng của từng thư mục, cách tổ chức tệp, v.v. Tuân thủ quy chuẩn cấu trúc thư mục trong tài liệu này giúp nâng cao khả năng bảo trì và khả năng mở rộng của dự án, thuận tiện cho việc phối hợp phát triển trong nhóm.

Cùng với sự phát triển của nghiệp vụ và sự tiến bộ của công nghệ, cấu trúc thư mục cũng có thể cần được liên tục tối ưu và điều chỉnh để thích ứng với các yêu cầu nghiệp vụ và thách thức kỹ thuật mới.