# Tài liệu quy trình phát triển API UniApp

## 1. Tổng quan

Tài liệu này mô tả quy trình phát triển API cho phía di động UniApp trong dự án CRMEB, bao gồm thiết kế API, luồng request, xử lý response, xử lý lỗi, v.v., nhằm chuẩn hóa việc phát triển API, nâng cao hiệu quả phát triển và chất lượng code.

## 2. Cấu trúc thư mục API

```
template/uni-app/api/
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

## 3. Cấu hình API cơ bản (api.js)

```javascript
// Cấu hình API cơ sở
const baseURL = 'https://api.crmeb.net';

// Thời gian timeout của request
const timeout = 10000;

// Interceptor cho request
const requestInterceptor = (config) => {
  // Thêm token
  const token = uni.getStorageSync('token');
  if (token) {
    config.header['Authorization'] = `Bearer ${token}`;
  }
  
  // Thêm thông tin thiết bị
  config.header['X-Device-Type'] = uni.getSystemInfoSync().platform;
  
  return config;
};

// Interceptor cho response
const responseInterceptor = (response) => {
  const { data } = response;
  
  // Xử lý lỗi tập trung
  if (data.code !== 200) {
    uni.showToast({
      title: data.message || 'Yêu cầu thất bại',
      icon: 'none'
    });
    
    // Xử lý phiên đăng nhập hết hạn
    if (data.code === 401) {
      uni.redirectTo({
        url: '/pages/login/index'
      });
    }
    
    return Promise.reject(data);
  }
  
  return data;
};

// Xử lý lỗi
const errorHandler = (error) => {
  uni.showToast({
    title: 'Lỗi mạng, vui lòng thử lại sau',
    icon: 'none'
  });
  
  return Promise.reject(error);
};

// Export cấu hình
export default {
  baseURL,
  timeout,
  requestInterceptor,
  responseInterceptor,
  errorHandler
};
```

## 4. Đóng gói API

### 4.1 Phương thức request dùng chung

```javascript
// utils/request.js
import apiConfig from '../api/api';

class Request {
  constructor() {
    this.baseURL = apiConfig.baseURL;
    this.timeout = apiConfig.timeout;
  }
  
  // Phương thức request dùng chung
  request(options) {
    return new Promise((resolve, reject) => {
      // Áp dụng interceptor cho request
      if (apiConfig.requestInterceptor) {
        options = apiConfig.requestInterceptor(options);
      }
      
      uni.request({
        url: this.baseURL + options.url,
        method: options.method || 'GET',
        data: options.data || {},
        header: options.header || {
          'Content-Type': 'application/json'
        },
        timeout: this.timeout,
        success: (response) => {
          // Áp dụng interceptor cho response
          if (apiConfig.responseInterceptor) {
            try {
              const result = apiConfig.responseInterceptor(response);
              resolve(result);
            } catch (error) {
              reject(error);
            }
          } else {
            resolve(response.data);
          }
        },
        fail: (error) => {
          // Áp dụng bộ xử lý lỗi
          if (apiConfig.errorHandler) {
            apiConfig.errorHandler(error);
          }
          reject(error);
        }
      });
    });
  }
  
  // GET Gửi yêu cầu
  get(url, params = {}) {
    return this.request({
      url,
      method: 'GET',
      data: params
    });
  }
  
  // POST Gửi yêu cầu
  post(url, data = {}) {
    return this.request({
      url,
      method: 'POST',
      data
    });
  }
  
  // PUT Gửi yêu cầu
  put(url, data = {}) {
    return this.request({
      url,
      method: 'PUT',
      data
    });
  }
  
  // DELETE Gửi yêu cầu
  delete(url, params = {}) {
    return this.request({
      url,
      method: 'DELETE',
      data: params
    });
  }
}

export default new Request();
```

### 4.2 Đóng gói API nghiệp vụ

```javascript
// api/user.js
import request from '../utils/request';

// API liên quan đến người dùng
export default {
  // Đăng nhập
  login: (data) => request.post('/api/user/login', data),
  
  // Đăng ký
  register: (data) => request.post('/api/user/register', data),
  
  // Lấy thông tin người dùng
  getUserInfo: () => request.get('/api/user/info'),
  
  // Cập nhật thông tin người dùng
  updateUserInfo: (data) => request.put('/api/user/info', data),
  
  // Đổi mật khẩu
  changePassword: (data) => request.post('/api/user/password', data),
  
  // Lấy danh sách địa chỉ
  getAddressList: () => request.get('/api/user/address'),
  
  // Thêm địa chỉ
  addAddress: (data) => request.post('/api/user/address', data),
  
  // Cập nhật địa chỉ
  updateAddress: (id, data) => request.put(`/api/user/address/${id}`, data),
  
  // Xóa địa chỉ
  deleteAddress: (id) => request.delete(`/api/user/address/${id}`),
  
  // Đặt địa chỉ mặc định
  setDefaultAddress: (id) => request.put(`/api/user/address/${id}/default`)
};
```

## 5. Ví dụ gọi API

### 5.1 Gọi API trong trang

```vue
<template>
  <view class="user-info">
    <view v-if="loading">Đang tải...</view>
    <view v-else>
      <image :src="userInfo.avatar" class="avatar"></image>
      <view class="name">{{ userInfo.nickname }}</view>
      <view class="mobile">{{ userInfo.mobile }}</view>
    </view>
  </view>
</template>

<script>
import userApi from '../../api/user';

export default {
  data() {
    return {
      loading: false,
      userInfo: {}
    };
  },
  
  onLoad() {
    this.getUserInfo();
  },
  
  methods: {
    async getUserInfo() {
      try {
        this.loading = true;
        const res = await userApi.getUserInfo();
        this.userInfo = res.data;
      } catch (error) {
        console.error('Lấy thông tin người dùng thất bại:', error);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
```

### 5.2 Gọi API trong component

```vue
<template>
  <view class="address-item" v-for="item in addressList" :key="item.id">
    <view class="address-info">
      <view class="name">{{ item.name }} {{ item.mobile }}</view>
      <view class="detail">{{ item.province }}{{ item.city }}{{ item.district }}{{ item.detail }}</view>
    </view>
    <view class="address-actions">
      <button @click="editAddress(item)">Sửa</button>
      <button @click="deleteAddress(item.id)">Xóa</button>
      <button v-if="!item.is_default" @click="setDefault(item.id)">Đặt làm mặc định</button>
    </view>
  </view>
</template>

<script>
import userApi from '../../api/user';

export default {
  data() {
    return {
      addressList: []
    };
  },
  
  mounted() {
    this.getAddressList();
  },
  
  methods: {
    async getAddressList() {
      try {
        const res = await userApi.getAddressList();
        this.addressList = res.data;
      } catch (error) {
        console.error('Lấy danh sách địa chỉ thất bại:', error);
      }
    },
    
    editAddress(item) {
      uni.navigateTo({
        url: `/pages/address/edit?id=${item.id}`
      });
    },
    
    async deleteAddress(id) {
      uni.showModal({
        title: 'Thông báo',
        content: 'Bạn có chắc muốn xóa địa chỉ này không?',
        success: async (res) => {
          if (res.confirm) {
            try {
              await userApi.deleteAddress(id);
              uni.showToast({
                title: 'Xóa thành công',
                icon: 'success'
              });
              this.getAddressList();
            } catch (error) {
              console.error('Xóa địa chỉ thất bại:', error);
            }
          }
        }
      });
    },
    
    async setDefault(id) {
      try {
        await userApi.setDefaultAddress(id);
        uni.showToast({
          title: 'Cài đặt thành công',
          icon: 'success'
        });
        this.getAddressList();
      } catch (error) {
        console.error('Đặt địa chỉ mặc định thất bại:', error);
      }
    }
  }
};
</script>
```

## 6. Thực tiễn tốt nhất khi phát triển API

### 6.1 Quy tắc đặt tên

- **Đặt tên file**: Chữ thường, các từ phân tách bằng dấu gạch dưới, ví dụ `user.js`
- **Đặt tên phương thức**: Kiểu camelCase, ví dụ `getUserInfo`
- **Đặt tên URL**: Chữ thường, các từ phân tách bằng dấu gạch nối, ví dụ `/api/user/info`
- **Đặt tên tham số**: Kiểu camelCase, thống nhất với backend

### 6.2 Quy chuẩn thiết kế API

- **Phong cách RESTful**: Tuân theo quy chuẩn thiết kế RESTful API
- **Quản lý phiên bản**: Đưa số phiên bản vào URL, ví dụ `/api/v1/user/info`
- **Định dạng response thống nhất**: Tất cả API đều trả về cùng một định dạng response
- **Xử lý lỗi**: Mã lỗi và thông báo lỗi thống nhất

### 6.3 Quy chuẩn request

- **Phương thức request**: Chọn phương thức HTTP phù hợp theo loại thao tác
  - GET: Lấy tài nguyên
  - POST: Tạo tài nguyên
  - PUT: Cập nhật tài nguyên
  - DELETE: Xóa tài nguyên
- **Header request**: Thống nhất thêm các header cần thiết, như Authorization, Content-Type, v.v.
- **Truyền tham số**: Chọn cách truyền tham số phù hợp theo phương thức request
  - GET: Tham số truy vấn (query)
  - POST/PUT: Body của request
  - DELETE: Tham số truy vấn hoặc tham số đường dẫn (path)

### 6.4 Quy chuẩn response

- **Response thành công**: 
  ```json
  {
    "code": 200,
    "message": "Yêu cầu thành công",
    "data": {}
  }
  ```
- **Response thất bại**: 
  ```json
  {
    "code": 400,
    "message": "Yêu cầu thất bại",
    "data": {}
  }
  ```

### 6.5 Quy chuẩn xử lý lỗi

- **Lỗi mạng**: Xử lý thống nhất lỗi mạng, như hết thời gian chờ (timeout), mất kết nối mạng, v.v.
- **Lỗi nghiệp vụ**: Xử lý các lỗi nghiệp vụ khác nhau theo mã lỗi
- **Hết hạn đăng nhập**: Xử lý thống nhất trường hợp hết hạn đăng nhập, chuyển hướng tới trang đăng nhập
- **Thông báo lỗi**: Cách hiển thị thông báo lỗi thống nhất, dùng uni.showToast

## 7. Tối ưu hiệu năng API

### 7.1 Tối ưu request

- **Gộp request**: Gộp nhiều request liên quan thành một
- **Chiến lược cache**: Dùng cache cho dữ liệu ít thay đổi
- **Debounce request**: Tránh gửi liên tục các request giống nhau
- **Thao tác hàng loạt**: Hỗ trợ thao tác hàng loạt, giảm số lần gửi request

### 7.2 Tối ưu response

- **Tối ưu cấu trúc dữ liệu**: Tối ưu cấu trúc dữ liệu response, giảm lượng dữ liệu truyền tải
- **Phân trang**: Dùng phân trang cho dữ liệu dạng danh sách
- **Lọc trường**: Hỗ trợ lọc trường, chỉ trả về các trường cần thiết
- **Nén khi truyền tải**: Dùng gzip để nén dữ liệu truyền tải

### 7.3 Tối ưu code

- **Mô-đun hóa**: Chia file API theo mô-đun nghiệp vụ
- **Tái sử dụng code**: Tách riêng logic request dùng chung
- **Giảm dư thừa**: Tránh gọi API trùng lặp
- **Tính dễ đọc của code**: Giữ code rõ ràng, dễ đọc

## 8. Sự cố thường gặp

### 8.1 Vấn đề truy cập chéo miền (CORS)

- **Vấn đề**: Gặp vấn đề cross-domain trong môi trường phát triển
- **Giải pháp**: Cấu hình proxy cross-domain trên máy chủ phát triển cục bộ

### 8.2 Vấn đề Token hết hạn

- **Vấn đề**: Request thất bại sau khi Token hết hạn
- **Giải pháp**: Xử lý Token hết hạn trong interceptor response, chuyển hướng tới trang đăng nhập

### 8.3 Vấn đề request bị timeout

- **Vấn đề**: Request bị timeout khi mạng không ổn định
- **Giải pháp**: Đặt thời gian timeout hợp lý, bổ sung kiểm tra trạng thái mạng

### 8.4 Vấn đề request trùng lặp

- **Vấn đề**: Bấm nút nhanh liên tục dẫn đến request trùng lặp
- **Giải pháp**: Thêm debounce cho request hoặc cơ chế khóa

### 8.5 Vấn đề cache dữ liệu

- **Vấn đề**: Dữ liệu cache không khớp với dữ liệu trên máy chủ
- **Giải pháp**: Đặt thời gian hết hạn cache hợp lý, cung cấp cơ chế làm mới thủ công

## 9. Tài liệu tham khảo

- [Tài liệu request mạng của UniApp](https://uniapp.dcloud.io/api/request/request)
- [Hướng dẫn thiết kế RESTful API](https://restfulapi.cn/)
- [Tài liệu Axios](https://axios-http.com/zh/docs/intro)
- [Phương thức HTTP](https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Methods)

## 10. Tổng kết

Tài liệu này mô tả quy trình phát triển API cho phía di động UniApp trong dự án CRMEB, bao gồm thiết kế API, luồng request, xử lý response, xử lý lỗi, v.v. Tuân theo các quy chuẩn phát triển trong tài liệu này sẽ giúp nâng cao hiệu quả và chất lượng phát triển API, đảm bảo tính ổn định và độ tin cậy của ứng dụng.

Cùng với sự phát triển của nghiệp vụ và sự tiến bộ của công nghệ, quy trình phát triển API cũng cần liên tục được tối ưu và điều chỉnh để đáp ứng các yêu cầu nghiệp vụ và thách thức kỹ thuật mới.