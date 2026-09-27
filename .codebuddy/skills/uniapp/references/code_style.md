# Tài liệu quy chuẩn code UniApp

## 1. Tổng quan

Tài liệu này mô tả quy chuẩn code cho phía di động UniApp trong dự án CRMEB, bao gồm quy chuẩn đặt tên, phong cách code, cấu trúc file, phát triển component, v.v., nhằm thống nhất phong cách code, nâng cao chất lượng code và khả năng bảo trì.

## 2. Quy tắc đặt tên

### 2.1 Đặt tên thư mục

- **Tên thư mục**: Chữ thường, các từ phân cách bằng dấu gạch dưới
- **Ví dụ**: `pages/index/`, `components/common/`, `utils/`
- **Quy tắc**: Ngắn gọn, rõ ràng, phản ánh chức năng của thư mục

### 2.2 Đặt tên file

- **File trang**: Chữ thường, các từ phân tách bằng dấu gạch dưới
  - **Ví dụ**: `index.vue`, `login.vue`, `user_info.vue`
- **File component**: Kiểu đặt tên PascalCase, bắt đầu bằng chữ in hoa
  - **Ví dụ**: `Button.vue`, `NavBar.vue`, `GoodsList.vue`
- **File JS**: Chữ thường, các từ phân tách bằng dấu gạch dưới
  - **Ví dụ**: `user.js`, `request.js`, `util.js`
- **File CSS/SCSS**: Chữ thường, các từ phân tách bằng dấu gạch dưới
  - **Ví dụ**: `common.scss`, `theme.scss`

### 2.3 Đặt tên biến

- **Biến thông thường**: Đặt tên kiểu camelCase
  - **Ví dụ**: `userInfo`, `goodsList`, `isLoading`
- **Hằng số**: Viết hoa toàn bộ, các từ phân tách bằng dấu gạch dưới
  - **Ví dụ**: `BASE_URL`, `MAX_COUNT`, `DEFAULT_PAGE_SIZE`
- **Biến boolean**: Đặt tên kiểu camelCase, bắt đầu bằng `is`
  - **Ví dụ**: `isShow`, `isLoading`, `isLogin`

### 2.4 Đặt tên hàm

- **Tên hàm**: Đặt tên kiểu camelCase, bắt đầu bằng động từ
  - **Ví dụ**: `getUserInfo`, `submitForm`, `handleClick`
- **Tên phương thức**: Đặt tên kiểu camelCase
  - **Ví dụ**: `data`, `methods`, `computed`, `watch`
- **Hàm vòng đời**: Đặt tên theo quy chuẩn của Vue
  - **Ví dụ**: `created`, `mounted`, `beforeDestroy`

### 2.5 Đặt tên component

- **Tên component**: Kiểu đặt tên PascalCase
  - **Ví dụ**: `Button`, `NavBar`, `GoodsList`
- **Thẻ component**: Chữ thường, các từ phân tách bằng dấu gạch ngang
  - **Ví dụ**: `<my-button>`, `<nav-bar>`, `<goods-list>`

### 2.6 Quy tắc đặt tên khác

- **Đặt tên route**: Chữ thường, các từ phân tách bằng dấu gạch ngang
  - **Ví dụ**: `/pages/index/index`, `/pages/user/login`
- **Tên class CSS**: Chữ thường, các từ phân tách bằng dấu gạch ngang
  - **Ví dụ**: `.user-info`, `.goods-list`, `.btn-primary`
- **Đặt tên ID**: Chữ thường, các từ phân tách bằng dấu gạch ngang
  - **Ví dụ**: `#app`, `#header`, `#footer`

## 3. Phong cách code

### 3.1 Thụt lề

- **Cách thụt lề**: 4 dấu cách
- **Ví dụ**:
  ```vue
  <template>
      <view class="container">
          <text>{{ message }}</text>
      </view>
  </template>
  ```

### 3.2 Xuống dòng

- **Xuống dòng cho thẻ**: Thẻ có nhiều thuộc tính nên xuống dòng
- **Ví dụ**:
  ```vue
  <view
      class="container"
      :class="{ active: isActive }"
      @click="handleClick"
  >
      <text>{{ message }}</text>
  </view>
  ```
- **Xuống dòng cho khối code**: Các khối code logic nên được xuống dòng
- **Ví dụ**:
  ```javascript
  if (condition) {
      // Khối code
  } else {
      // Khối code
  }
  ```

### 3.3 Dấu cách

- **Dấu cách quanh toán tử**: Hai bên toán tử nên có dấu cách
  - **Ví dụ**: `a + b`, `x = y`, `i < 10`
- **Dấu cách sau dấu phẩy**: Sau dấu phẩy nên có dấu cách
  - **Ví dụ**: `[1, 2, 3]`, `{ name: 'Nguyễn Văn A', age: 18 }`
- **Dấu cách trong ngoặc**: Không nên có dấu cách ở phía trong dấu ngoặc
  - **Ví dụ**: `if (condition)`, `function (param)`

### 3.4 Chú thích

- **Chú thích một dòng**: Dùng chú thích `//`
  - **Ví dụ**: `// Đây là chú thích một dòng`
- **Chú thích nhiều dòng**: Dùng chú thích `/* */`
  - **Ví dụ**:
    ```javascript
    /*
     * Đây là chú thích nhiều dòng
     * Dòng thứ hai
     */
    ```
- **Chú thích tài liệu**: Dùng chú thích theo phong cách JSDoc
  - **Ví dụ**:
    ```javascript
    /**
     * Lấy thông tin người dùng
     * @param {number} id - ID người dùng
     * @returns {Promise} Thông tin người dùng
     */
    async function getUserInfo(id) {
        // Code
    }
    ```

### 3.5 Dấu nháy

- **Chuỗi**: Dùng dấu nháy đơn `''`
  - **Ví dụ**: `const name = 'Nguyễn Văn A'`, `<text>Hello</text>`
- **Chuỗi template**: Dùng dấu backtick `` ` ``
  - **Ví dụ**: `` const url = `${baseUrl}/api/user` ``
- **Thuộc tính HTML**: Dùng dấu nháy kép `""`
  - **Ví dụ**: `<view class="container">`, `<image src="https://example.com/img.jpg">`

## 4. Cấu trúc file

### 4.1 Cấu trúc file trang

```vue
<template>
    <!-- Nội dung mẫu -->
</template>

<script>
// Import các phụ thuộc
import userApi from '../../api/user';

// Export thành phần
export default {
    // Tên thành phần
    name: 'UserInfo',
    
    // Thuộc tính của thành phần
    props: {
        userId: {
            type: Number,
            required: true
        }
    },
    
    // Dữ liệu
    data() {
        return {
            userInfo: {},
            loading: false
        };
    },
    
    // Thuộc tính computed
    computed: {
        fullName() {
            return this.userInfo.firstName + ' ' + this.userInfo.lastName;
        }
    },
    
    // Theo dõi (listener)
    watch: {
        userId: {
            handler(newVal) {
                this.getUserInfo(newVal);
            },
            immediate: true
        }
    },
    
    // Hàm vòng đời
    created() {
        // Khởi tạo
    },
    
    mounted() {
        // Sau khi mount
    },
    
    // Phương thức
    methods: {
        async getUserInfo(id) {
            try {
                this.loading = true;
                const res = await userApi.getUserInfo(id);
                this.userInfo = res.data;
            } catch (error) {
                console.error('Lấy thông tin người dùng thất bại:', error);
            } finally {
                this.loading = false;
            }
        },
        
        handleEdit() {
            // Sửa thông tin người dùng
        }
    }
};
</script>

<style scoped>
/* Kiểu */
.user-info {
    padding: 20rpx;
    background-color: #f5f5f5;
}

.name {
    font-size: 32rpx;
    font-weight: bold;
}
</style>
```

### 4.2 Cấu trúc file component

```vue
<template>
    <!-- Nội dung mẫu -->
    <view class="my-button">
        <button
            :class="[type, { disabled }]"
            :disabled="disabled"
            @click="handleClick"
        >
            <slot></slot>
        </button>
    </view>
</template>

<script>
export default {
    name: 'MyButton',
    
    props: {
        type: {
            type: String,
            default: 'primary',
            validator: (value) => {
                return ['primary', 'success', 'warning', 'danger'].includes(value);
            }
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    
    methods: {
        handleClick() {
            if (!this.disabled) {
                this.$emit('click');
            }
        }
    }
};
</script>

<style scoped>
.my-button {
    display: inline-block;
}

button {
    padding: 20rpx 40rpx;
    border-radius: 8rpx;
    font-size: 28rpx;
    border: none;
    outline: none;
    cursor: pointer;
}

.primary {
    background-color: #007aff;
    color: #fff;
}

.success {
    background-color: #4cd964;
    color: #fff;
}

.warning {
    background-color: #ff9500;
    color: #fff;
}

.danger {
    background-color: #ff3b30;
    color: #fff;
}

.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
```

### 4.3 Cấu trúc file JS

```javascript
// Import các phụ thuộc
import request from './request';

// Định nghĩa hằng số
const BASE_URL = 'https://api.crmeb.net';
const TIMEOUT = 10000;

// Hàm tiện ích (utility)
function formatTime(time) {
    const date = new Date(time);
    return date.toLocaleString();
}

function getRandomNum(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

// Xuất
export {
    formatTime,
    getRandomNum
};

// Export mặc định
export default {
    BASE_URL,
    TIMEOUT
};
```

## 5. Quy chuẩn phát triển component

### 5.1 Thiết kế component

- **Trách nhiệm đơn nhất**: Mỗi component chỉ đảm nhận một chức năng
- **Khả năng tái sử dụng**: Thiết kế component dùng chung, có thể tái sử dụng
- **Khả năng cấu hình**: Cung cấp tùy chọn cấu hình qua props
- **Giao tiếp qua sự kiện**: Giao tiếp với component cha thông qua sự kiện

### 5.2 Sử dụng component

- **Import component**: Dùng import để nhập component
  - **Ví dụ**: `import Button from '../../components/Button.vue'`
- **Đăng ký component**: Đăng ký component trong components
  - **Ví dụ**: 
    ```javascript
    components: {
        Button
    }
    ```
- **Sử dụng component**: Dùng thẻ component
  - **Ví dụ**: `<Button type="primary">Nhấn</Button>`

### 5.3 Giao tiếp giữa các component

- **props truyền xuống**: Component cha truyền dữ liệu cho component con qua props
- **events truyền lên**: Component con truyền sự kiện lên component cha qua events
- **Tham chiếu qua refs**: Tham chiếu tới instance của component con qua refs
- **provide/inject**: Component tổ tiên truyền dữ liệu cho component hậu duệ

## 6. Quy chuẩn phát triển trang

### 6.1 Cấu trúc trang

- **Cấu trúc template**: Rõ ràng, phân cấp mạch lạc
- **Cấu trúc script**: Tổ chức code theo quy chuẩn của Vue
- **Cấu trúc style**: Module hóa, dễ bảo trì

### 6.2 Quản lý dữ liệu

- **Dữ liệu cục bộ**: Dùng data để quản lý dữ liệu nội bộ của component
- **Dữ liệu tính toán**: Dùng computed để tính dữ liệu dẫn xuất
- **Theo dõi dữ liệu**: Dùng watch để theo dõi thay đổi của dữ liệu
- **Dữ liệu toàn cục**: Dùng Vuex để quản lý dữ liệu toàn cục

### 6.3 Quản lý vòng đời (lifecycle)

- **created**: Khởi tạo dữ liệu, gửi request
- **mounted**: Thao tác DOM, khởi tạo thư viện bên thứ ba
- **beforeDestroy**: Dọn dẹp timer, hủy đăng ký (unsubscribe)

### 6.4 Quản lý định tuyến

- **Cấu hình định tuyến**: Cấu hình route trong pages.json
- **Chuyển trang**: Dùng các phương thức như uni.navigateTo, uni.redirectTo
- **Tham số định tuyến**: Lấy tham số route qua options hoặc $route.params

## 7. Quy chuẩn gọi API

### 7.1 Đóng gói API

- **Mô-đun hóa**: Đóng gói API theo mô-đun nghiệp vụ
- **Xử lý thống nhất**: Xử lý thống nhất header request, chặn response (interceptor), xử lý lỗi
- **Promise hóa**: Dùng Promise để xử lý request bất đồng bộ

### 7.2 Gọi API

- **Xử lý bất đồng bộ**: Dùng async/await để xử lý request bất đồng bộ
- **Xử lý lỗi**: Dùng try/catch để bắt lỗi
- **Trạng thái tải**: Hiển thị trạng thái đang tải, nâng cao trải nghiệm người dùng
- **Thông báo lỗi**: Xử lý thống nhất việc hiển thị thông báo lỗi

### 7.3 Code mẫu

```javascript
async function getUserInfo() {
    try {
        this.loading = true;
        const res = await userApi.getUserInfo();
        this.userInfo = res.data;
    } catch (error) {
        console.error('Lấy thông tin người dùng thất bại:', error);
        uni.showToast({
            title: 'Lấy thông tin người dùng thất bại',
            icon: 'none'
        });
    } finally {
        this.loading = false;
    }
}
```

## 8. Tối ưu hiệu năng

### 8.1 Tối ưu code

- **Giảm code dư thừa**: Tránh code lặp lại
- **Dùng thuộc tính computed**: Với các phép tính phức tạp, hãy dùng computed
- **Dùng v-if và v-show hợp lý**: Chọn directive phù hợp với từng tình huống
- **Dùng key**: Dùng key trong v-for để tăng hiệu năng render

### 8.2 Tối ưu mạng

- **Dùng cache hợp lý**: Cache những dữ liệu ít thay đổi
- **Giảm số lượng request**: Gộp request, thao tác theo lô
- **Dùng debounce và throttle**: Tránh sự kiện bị kích hoạt quá thường xuyên
- **Tải trễ (lazy load)**: Với nội dung không thuộc màn hình đầu tiên, dùng tải trễ

### 8.3 Các tối ưu khác

- **Giảm số node DOM**: Đơn giản hóa cấu trúc DOM
- **Tối ưu hình ảnh**: Dùng định dạng và kích thước ảnh phù hợp
- **Dùng danh sách ảo**: Với danh sách dài, hãy dùng danh sách ảo (virtual list)
- **Tránh rò rỉ bộ nhớ**: Kịp thời dọn dẹp timer, event listener, v.v.

## 9. Sự cố thường gặp

### 9.1 Vấn đề về phong cách code

- **Vấn đề**: Phong cách code không thống nhất
- **Giải pháp**: Dùng các công cụ như ESLint để kiểm tra code

### 9.2 Vấn đề hiệu năng

- **Vấn đề**: Trang tải chậm, giật lag
- **Giải pháp**: Tối ưu code, giảm thao tác DOM, dùng danh sách ảo, v.v.

### 9.3 Vấn đề tương thích

- **Vấn đề**: Hoạt động không nhất quán giữa các nền tảng khác nhau
- **Giải pháp**: Tuân theo quy chuẩn UniApp, dùng biên dịch có điều kiện

### 9.4 Vấn đề bảo trì

- **Vấn đề**: Code khó bảo trì
- **Giải pháp**: Phát triển theo module, thêm chú thích, tuân thủ quy chuẩn code

## 10. Tài liệu tham khảo

- [Tài liệu chính thức của Vue](https://cn.vuejs.org/)
- [Tài liệu chính thức UniApp](https://uniapp.dcloud.io/)
- [Tài liệu chính thức ESLint](https://eslint.org/docs/user-guide/)
- [Tài liệu chính thức Prettier](https://prettier.io/docs/en/)
- [Hướng dẫn quy chuẩn code front-end](https://github.com/ecomfe/spec)

## 11. Tổng kết

Tài liệu này mô tả quy chuẩn code cho phía di động UniApp trong dự án CRMEB, bao gồm quy chuẩn đặt tên, phong cách code, cấu trúc file, phát triển component, v.v. Tuân theo các quy chuẩn trong tài liệu này sẽ giúp nâng cao khả năng đọc hiểu, khả năng bảo trì và khả năng mở rộng của code, đảm bảo chất lượng và sự ổn định của dự án.

Quy chuẩn code là nền tảng của việc làm việc nhóm; các thành viên trong đội phát triển nên tuân thủ nghiêm ngặt các quy chuẩn trong tài liệu này để cùng duy trì một codebase chất lượng cao.