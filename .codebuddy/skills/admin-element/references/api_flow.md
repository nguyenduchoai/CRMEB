# Tài liệu quy trình request API của Admin-Element

## 1 Tổng quan quy trình request API

Quy trình request API của dự án Admin-Element tuân theo các bước sau:

1. **Định nghĩa API**: Định nghĩa API theo module trong thư mục `src/api/`
2. **Đóng gói request**: Dùng `axios` để đóng gói request mạng, thêm interceptor
3. **Gọi API**: Gọi API trong component hoặc logic nghiệp vụ
4. **Xử lý response**: Xử lý thống nhất response của API, bao gồm cả trường hợp thành công và thất bại
5. **Xử lý lỗi**: Xử lý thống nhất các tình huống bất thường như lỗi mạng, lỗi nghiệp vụ, v.v.
6. **Quản lý dữ liệu**: Lưu dữ liệu lấy được vào kho quản lý trạng thái hoặc vào component

## 2 Đóng gói request mạng

### 2.1 Tạo instance axios

Tạo instance axios và cấu hình trong `src/utils/request.js`:

```javascript
import axios from 'axios'
import { Message, Loading } from 'element-ui'
import store from '@/store'
import { getToken } from '@/utils/auth'

// Tạo instance axios
const service = axios.create({
  baseURL: process.env.VUE_APP_BASE_API, // API đường dẫn gốc
  timeout: 10000, // Thời gian timeout của request
  headers: {
    'Content-Type': 'application/json;charset=utf-8'
  }
})
```

### 2.2 Request interceptor

```javascript
// Interceptor cho request
service.interceptors.request.use(
  config => {
    // Hiển thị hiệu ứng đang tải
    if (config.loading !== false) {
      store.dispatch('app/showLoading')
    }
    
    // Tự động thêm token
    if (store.getters.token) {
      config.headers['Authorization'] = `Bearer ${getToken()}`
    }
    
    return config
  },
  error => {
    // Ẩn hiệu ứng đang tải
    store.dispatch('app/hideLoading')
    console.error('Lỗi request:', error)
    return Promise.reject(error)
  }
)
```

### 2.3 Response interceptor

```javascript
// Interceptor cho response
service.interceptors.response.use(
  response => {
    // Ẩn hiệu ứng đang tải
    store.dispatch('app/hideLoading')
    
    const res = response.data
    
    // Kiểm tra trạng thái response
    if (res.code !== 200) {
      // Hiển thị thông báo lỗi
      Message.error({
        message: res.message || 'Thao tác thất bại',
        duration: 3000
      })
      
      // Xử lý các trường hợp đặc biệt như token hết hạn
      if (res.code === 401) {
        // Chuyển đến trang đăng nhập
        store.dispatch('user/logout').then(() => {
          location.reload()
        })
      }
      
      return Promise.reject(new Error(res.message || 'Thao tác thất bại'))
    } else {
      return res
    }
  },
  error => {
    // Ẩn hiệu ứng đang tải
    store.dispatch('app/hideLoading')
    
    // Xử lý lỗi mạng
    let message = 'Yêu cầu mạng thất bại'
    if (error.response) {
      const status = error.response.status
      switch (status) {
        case 400:
          message = 'Tham số request không hợp lệ'
          break
        case 401:
          message = 'Chưa được ủy quyền, vui lòng đăng nhập lại'
          // Chuyển đến trang đăng nhập
          store.dispatch('user/logout').then(() => {
            location.reload()
          })
          break
        case 403:
          message = 'Từ chối truy cập'
          break
        case 404:
          message = 'Địa chỉ yêu cầu không tồn tại'
          break
        case 500:
          message = 'Lỗi máy chủ nội bộ'
          break
        default:
          message = `Yêu cầu thất bại (${status})`
      }
    } else if (error.message.includes('timeout')) {
      message = 'Request quá thời gian chờ'
    }
    
    // Hiển thị thông báo lỗi
    Message.error({
      message: message,
      duration: 3000
    })
    
    return Promise.reject(error)
  }
)

export default service
```

## 3 Định nghĩa API

### 3.1 Tổ chức file API

API được tổ chức theo module, đặt trong thư mục `src/api/`:

```
src/api/
├── index.js          # API File điểm vào
├── user.js           # API liên quan đến người dùng
├── goods.js          # API liên quan đến sản phẩm
├── order.js          # API liên quan đến đơn hàng
└── ...               # API của các module khác
```

### 3.2 Ví dụ định nghĩa API

Định nghĩa các API liên quan đến người dùng trong `src/api/user.js`:

```javascript
import request from '@/utils/request'

export default {
  // Đăng nhập
  login(data) {
    return request({
      url: '/admin/login',
      method: 'post',
      data
    })
  },
  
  // Lấy thông tin người dùng
  getUserInfo() {
    return request({
      url: '/admin/user/info',
      method: 'get'
    })
  },
  
  // Lấy danh sách người dùng
  getUserList(params) {
    return request({
      url: '/admin/user/list',
      method: 'get',
      params
    })
  },
  
  // Chỉnh sửa thông tin người dùng
  updateUser(data) {
    return request({
      url: '/admin/user/update',
      method: 'put',
      data
    })
  },
  
  // Xóa người dùng
  deleteUser(id) {
    return request({
      url: `/admin/user/delete/${id}`,
      method: 'delete'
    })
  }
}
```

### 3.3 File entry của API

Export toàn bộ module API trong `src/api/index.js`:

```javascript
import user from './user'
import goods from './goods'
import order from './order'

// Export module API
export default {
  user,
  goods,
  order
}
```

## 4 Xử lý request và response

### 4.1 Xử lý tham số request

#### 4.1.1 Request GET

```javascript
// Request GET kèm tham số truy vấn
api.user.getUserList({
  page: 1,
  limit: 10,
  keyword: 'test'
})
```

#### 4.1.2 Request POST

```javascript
// Request POST kèm request body
api.user.login({
  username: 'admin',
  password: '123456'
})
```

#### 4.1.3 Request PUT

```javascript
// Request PUT kèm request body
api.user.updateUser({
  id: 1,
  username: 'newadmin',
  nickname: 'Quản trị viên mới'
})
```

#### 4.1.4 Request DELETE

```javascript
// Request DELETE với tham số đường dẫn
api.user.deleteUser(1)
```

### 4.2 Cấu trúc dữ liệu response

Cấu trúc dữ liệu response của API backend cần tuân theo quy chuẩn sau:

```javascript
{
  "code": 200, // Mã trạng thái, 200 nghĩa là thành công
  "message": "Thao tác thành công", // Thông điệp phản hồi
  "data": { ... } // Dữ liệu phản hồi
}
```

### 4.3 Ví dụ xử lý response

```javascript
// Trong thành phần, gọi API
import api from '@/api'

export default {
  methods: {
    async fetchUserList() {
      try {
        const res = await api.user.getUserList({
          page: this.page,
          limit: this.limit
        })
        
        // Xử lý response thành công
        this.userList = res.data.list
        this.total = res.data.total
      } catch (error) {
        // Lỗi đã được xử lý trong interceptor, ở đây có thể xử lý thêm
        console.error('Lấy danh sách người dùng thất bại:', error)
      }
    }
  }
}
```

## 5 Cơ chế xử lý lỗi

### 5.1 Lỗi mạng

- Kết nối mạng thất bại
- Request quá thời gian chờ
- Máy chủ không phản hồi

### 5.2 Lỗi nghiệp vụ

- Tham số không hợp lệ (400)
- Chưa được ủy quyền (401)
- Từ chối truy cập (403)
- Địa chỉ request không tồn tại (404)
- Lỗi máy chủ nội bộ (500)

### 5.3 Lỗi logic nghiệp vụ

- Response có mã trạng thái khác 200
- Kiểm tra quy tắc nghiệp vụ thất bại

### 5.4 Thực tiễn tốt nhất khi xử lý lỗi

1. **Xử lý lỗi thống nhất**: Xử lý lỗi tập trung trong response interceptor
2. **Thông báo lỗi thân thiện**: Hiển thị thông báo lỗi rõ ràng cho người dùng
3. **Ghi log lỗi**: Ghi lại thông tin lỗi để thuận tiện truy vết sự cố
4. **Xử lý lỗi đặc biệt**: Xử lý các trường hợp đặc biệt như token hết hạn
5. **Xử lý dự phòng (fallback)**: Cung cấp phương án dự phòng hợp lý khi có lỗi mạng

## 6 Thực tiễn tốt nhất khi gọi API

### 6.1 Sử dụng async/await

```javascript
async fetchData() {
  try {
    const res = await api.goods.getGoodsList(this.queryParams)
    this.goodsList = res.data.list
    this.total = res.data.total
  } catch (error) {
    // Xử lý lỗi
  }
}
```

### 6.2 Quản lý trạng thái tải (loading)

```javascript
export default {
  data() {
    return {
      loading: false,
      goodsList: []
    }
  },
  methods: {
    async fetchData() {
      this.loading = true
      try {
        const res = await api.goods.getGoodsList(this.queryParams)
        this.goodsList = res.data.list
      } catch (error) {
        // Xử lý lỗi
      } finally {
        this.loading = false
      }
    }
  }
}
```

### 6.3 Debounce và throttle

Với các request được kích hoạt thường xuyên, hãy tối ưu bằng debounce hoặc throttle:

```javascript
import { debounce } from 'lodash'

export default {
  methods: {
    // Dùng debounce để tối ưu request tìm kiếm
    search: debounce(async function(query) {
      try {
        const res = await api.goods.searchGoods({ keyword: query })
        this.searchResults = res.data
      } catch (error) {
        // Xử lý lỗi
      }
    }, 300)
  }
}
```

### 6.4 Hủy request

Với các request có thể bị kích hoạt lặp lại, dùng cancel token để tránh gửi request trùng lặp:

```javascript
import axios from 'axios'

export default {
  data() {
    return {
      cancelToken: null
    }
  },
  methods: {
    async fetchData() {
      // Hủy request trước đó
      if (this.cancelToken) {
        this.cancelToken.cancel('Hủy request trùng lặp')
      }
      
      // Tạo cancel token mới
      this.cancelToken = axios.CancelToken.source()
      
      try {
        const res = await api.goods.getGoodsList({
          page: this.page,
          limit: this.limit
        }, {
          cancelToken: this.cancelToken.token
        })
        this.goodsList = res.data.list
      } catch (error) {
        if (axios.isCancel(error)) {
          console.log('Request đã bị hủy:', error.message)
        } else {
          // Xử lý lỗi
        }
      }
    }
  }
}
```

## 7 Bảo mật API

### 7.1 Xác thực và phân quyền

- Dùng token JWT để xác thực danh tính
- Gửi kèm trường Authorization trong header của request
- Làm mới token định kỳ để tránh hết hạn

### 7.2 Mã hóa dữ liệu

- Mã hóa dữ liệu nhạy cảm khi truyền tải
- Truyền các thông tin nhạy cảm như mật khẩu qua HTTPS

### 7.3 Phòng chống tấn công CSRF

- Sử dụng CSRF Token
- Xác minh nguồn gốc request

### 7.4 Giới hạn tần suất gọi API

- Backend triển khai giới hạn tần suất gọi API (rate limit)
- Frontend tránh gửi request quá thường xuyên

## 8 Tối ưu hiệu năng

### 8.1 Gộp request

Với nhiều request cùng loại, gộp chúng thành một request:

```javascript
// Lấy dữ liệu hàng loạt
api.goods.batchGetGoodsInfo(ids)
```

### 8.2 Chiến lược bộ nhớ đệm (cache)

- Cache những dữ liệu ít thay đổi
- Dùng localStorage hoặc sessionStorage để cache dữ liệu

### 8.3 Lazy load

- Tải dữ liệu theo nhu cầu
- Cuộn xuống cuối để tải thêm dữ liệu

### 8.4 Tải trước (preload)

- Tải trước những dữ liệu có thể sẽ cần dùng
- Nâng cao trải nghiệm người dùng

## 9 Mẹo gỡ lỗi

### 9.1 Công cụ gỡ lỗi API

- Dùng panel Network của Chrome DevTools
- Dùng các công cụ gỡ lỗi API như Postman

### 9.2 Ghi log

- Ở môi trường phát triển, in chi tiết thông tin request và response
- Ở môi trường production, chỉ ghi log thông tin lỗi

### 9.3 Dữ liệu giả lập (mock)

- Dùng dữ liệu Mock để phát triển frontend
- Giảm sự phụ thuộc vào API backend

## 10 Tổng kết

Quy trình request API của dự án Admin-Element áp dụng cơ chế đóng gói và xử lý thống nhất, dùng interceptor của axios để xử lý tập trung request và response, giúp nâng cao khả năng bảo trì và khả năng mở rộng của code. Lập trình viên cần tuân thủ quy chuẩn định nghĩa API và các thực tiễn tốt nhất để đảm bảo tính bảo mật, độ tin cậy và hiệu năng của các lệnh gọi API.