# Mô tả kiến trúc frontend di động của CRMEB

## 📱 Tổng quan bộ công nghệ

| Công nghệ | Phiên bản | Mô tả |
|------|------|------|
| UniApp | 2.x/3.x | Framework phát triển đa nền tảng |
| Vue.js | 2.x/3.x | Framework cốt lõi |
| Vuex | 3.x/4.x | Quản lý trạng thái |
| uView UI | 2.x | Thư viện component UI cho di động |
| Sass/Less | Bộ tiền xử lý CSS | |
| Axios | HTTP client | Đóng gói request |

## 🏗️ Kiến trúc tổng thể

```
┌─────────────────────────────────────────────────────────────┐
│                      UniApp - tầng framework                           │
│                    (Engine biên dịch đa nền tảng)                            │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                      Vue.js - tầng lõi                           │
│                    (Điều khiển bằng dữ liệu reactive)                          │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                      Tầng quản lý trạng thái                              │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│  │ Module User │ │ Module Cart │ │ Module Order│ │ Module Config│       │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                      UI - tầng thành phần                               │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  Thành phần cơ bản   │  Thành phần nghiệp vụ   │  Thành phần tùy chỉnh   │            │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                      Tầng thích ứng đa nền tảng                              │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│  │ WeChat Mini Program │ │  Trang H5  │ │  App   │ │ Nền tảng khác  │       │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
└─────────────────────────────────────────────────────────────┘
```

## 🧩 Các module chức năng cốt lõi

### 1. Mô-đun sản phẩm
- Hiển thị danh sách sản phẩm
- Trang chi tiết sản phẩm
- Tìm kiếm sản phẩm
- Danh mục sản phẩm

### 2. Module giỏ hàng
- Thêm sản phẩm vào giỏ hàng
- Quản lý giỏ hàng
- Thao tác hàng loạt

### 3. Mô-đun đơn hàng
- Tạo đơn hàng
- Danh sách đơn hàng
- Chi tiết đơn hàng
- Theo dõi trạng thái đơn hàng

### 4. Mô-đun người dùng
- Đăng nhập, đăng ký người dùng
- Trang cá nhân
- Quản lý địa chỉ
- Quản lý yêu thích

### 5. Mô-đun thanh toán
- WeChat Pay
- Thanh toán Alipay
- Thanh toán bằng số dư

## 🔧 Thực hành tốt nhất khi phát triển

### 1. Vòng đời trang
```javascript
export default {
  // Tải trang
  onLoad(options) {
    this.initPage(options)
  },

  // Hiển thị trang
  onShow() {
    this.refreshData()
  },

  // Kéo xuống để làm mới
  onPullDownRefresh() {
    this.loadData().finally(() => {
      uni.stopPullDownRefresh()
    })
  },

  // Kéo lên để tải thêm
  onReachBottom() {
    this.loadMore()
  },

  methods: {
    async initPage(options) {
      // Khởi tạo dữ liệu trang
    },

    async refreshData() {
      // Làm mới dữ liệu
    }
  }
}
```

### 2. Giao tiếp giữa các component
```javascript
// Truyền dữ liệu từ cha sang con
<child-component :data="parentData" @custom-event="handleEvent" />

// Thành phần con
export default {
  props: {
    data: {
      type: Object,
      default: () => ({})
    }
  },
  methods: {
    emitEvent() {
      this.$emit('custom-event', eventData)
    }
  }
}
```

### 3. Quản lý trạng thái
```javascript
// Đã sử dụng Vuex
import { mapState, mapActions } from 'vuex'

export default {
  computed: {
    ...mapState('user', ['userInfo']),
    ...mapState('cart', ['cartList'])
  },
  methods: {
    ...mapActions('user', ['login']),
    ...mapActions('cart', ['addToCart'])
  }
}
```

## 🎯 Tối ưu hiệu năng

### 1. Tối ưu hình ảnh
```javascript
// Dùng lazy load
<image lazy-load src="image.jpg" />

// Dùng định dạng webp
<image webp src="image.webp" />

// Dùng kích thước ảnh phù hợp
<image :src="imageUrl + '?x-oss-process=image/resize,w_300'" />
```

### 2. Bộ nhớ đệm dữ liệu
```javascript
// Dùng lưu trữ cục bộ
uni.setStorageSync('key', data)
const data = uni.getStorageSync('key')

// Dùng bộ nhớ đệm trong RAM
const cache = new Map()
cache.set('key', data)
const data = cache.get('key')
```

### 3. Tối ưu request
```javascript
// Xử lý debounce
const debounce = (func, wait) => {
  let timeout
  return function() {
    clearTimeout(timeout)
    timeout = setTimeout(() => func.apply(this, arguments), wait)
  }
}

// Đã sử dụng
search: debounce(function(keyword) {
  this.doSearch(keyword)
}, 300)
```

## 📦 Hỗ trợ đa nền tảng

### 1. Biên dịch có điều kiện
```javascript
// #ifdef H5
console.log('Chỉ hiển thị trên nền tảng H5')
// #endif

// #ifdef MP-WEIXIN
console.log('Chỉ hiển thị trên WeChat Mini Program')
// #endif

// #ifdef APP-PLUS  
console.log('Chỉ hiển thị trên App')
// #endif
```

### 2. Xử lý khác biệt giữa các nền tảng
```javascript
const platform = {
  isH5: uni.getSystemInfoSync().platform === 'h5',
  isWeapp: uni.getSystemInfoSync().platform === 'devtools',
  isApp: uni.getSystemInfoSync().platform === 'app'
}

// Đã sử dụng
if (platform.isH5) {
  // Logic riêng cho H5
}
```

## 🚀 Triển khai và phát hành

### 1. Phát hành Mini Program
```bash
# WeChat Mini Program
npm run build:mp-weixin
# Sau đó dùng WeChat Developer Tools để tải lên

# Mini Program Alipay  
npm run build:mp-alipay
```

### 2. Triển khai H5
```bash
# Build H5
npm run build:h5

# Triển khai lên server
scp -r dist/build/h5 user@server:/path/to/www
```

### 3. Phát hành App
```bash
# Build App
npm run build:app

# Dùng HBuilderX để đóng gói và phát hành
```

## 🔍 Mẹo gỡ lỗi

### 1. Sử dụng công cụ phát triển
```javascript
// In thông tin debug
console.log('Thông tin debug', data)

// Dùng debugger
debugger

// Giám sát hiệu năng
console.time('operation')
// Thực hiện thao tác
console.timeEnd('operation')
```

### 2. Gỡ lỗi trên thiết bị thật
```javascript
// Dùng công cụ debug của uni-app
// 1. Kết nối điện thoại với máy tính
// 2. Bật USB debugging
// 3. Dùng HBuilderX để chạy trên thiết bị thật
```

## 📝 Quy chuẩn code

### 1. Quy tắc đặt tên
```javascript
// Đặt tên biến - camelCase
const userName = 'John'
let isLoading = false

// Đặt tên hằng số - viết hoa toàn bộ
const API_BASE_URL = 'https://api.example.com'
const MAX_COUNT = 100

// Đặt tên component - PascalCase
// MyComponent.vue
export default {
  name: 'MyComponent'
}
```

### 2. Tổ chức code
```javascript
// Cấu trúc file
export default {
  // 1. Tên component
  name: 'ComponentName',

  // 2. Thuộc tính của thành phần
  props: {},

  // 3. Dữ liệu
  data() {
    return {}
  },

  // 4. Thuộc tính computed
  computed: {},

  // 5. Watcher
  watch: {},

  // 6. Vòng đời
  mounted() {},

  // 7. Phương thức
  methods: {}
}
```

---
**Phiên bản tài liệu**: v1.0  
**Ngày cập nhật**: 2024-01-17  
**Phiên bản áp dụng**: CRMEB 5.6.4+  

💡 Lưu ý: Phát triển cho di động cần đặc biệt chú ý tối ưu hiệu năng và tương thích đa nền tảng, khuyến nghị dùng tính năng Conditional Compile (biên dịch có điều kiện) của uni-app để xử lý khác biệt giữa các nền tảng.

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
