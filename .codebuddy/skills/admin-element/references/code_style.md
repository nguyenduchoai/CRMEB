# Tài liệu quy chuẩn code frontend trang quản trị

## 1. Tổng quan

Tài liệu này mô tả quy chuẩn code frontend trang quản trị trong dự án CRMEB, bao gồm các quy chuẩn về đặt tên, phong cách code, cấu trúc file, phát triển component, v.v., nhằm thống nhất phong cách code, nâng cao chất lượng và khả năng bảo trì của code.

## 2. Quy tắc đặt tên

### 2.1 Đặt tên thư mục
- **Tên thư mục**: Chữ thường, các từ phân tách bằng dấu gạch ngang
  - **Ví dụ**: `components/common/`, `pages/user-management/`, `utils/`
- **Quy tắc**: Ngắn gọn, rõ ràng, phản ánh chức năng của thư mục

### 2.2 Đặt tên file
- **File component**: Kiểu đặt tên PascalCase, bắt đầu bằng chữ in hoa
  - **Ví dụ**: `Button.vue`, `UserList.vue`, `Navbar.vue`
- **File trang**: Chữ thường, các từ phân tách bằng dấu gạch ngang
  - **Ví dụ**: `login.vue`, `user-list.vue`, `dashboard.vue`
- **File JS**: Chữ thường, các từ phân tách bằng dấu gạch ngang
  - **Ví dụ**: `api.js`, `request.js`, `util.js`
- **File CSS/SCSS**: Chữ thường, các từ phân tách bằng dấu gạch ngang
  - **Ví dụ**: `common.scss`, `theme.scss`, `variables.scss`

### 2.3 Đặt tên biến
- **Biến thông thường**: Đặt tên kiểu camelCase
  - **Ví dụ**: `userInfo`, `goodsList`, `isLoading`
- **Hằng số**: Viết hoa toàn bộ, các từ phân tách bằng dấu gạch dưới
  - **Ví dụ**: `BASE_URL`, `MAX_COUNT`, `DEFAULT_PAGE_SIZE`
- **Biến boolean**: Đặt tên kiểu camelCase, bắt đầu bằng `is`
  - **Ví dụ**: `isShow`, `isLoading`, `isLogin`
- **Biến mảng**: Đặt tên ở dạng số nhiều
  - **Ví dụ**: `users`, `goods`, `orders`
- **Biến đối tượng**: Đặt tên ở dạng số ít
  - **Ví dụ**: `user`, `good`, `order`

### 2.4 Đặt tên hàm
- **Tên hàm**: Đặt tên kiểu camelCase, bắt đầu bằng động từ
  - **Ví dụ**: `getUserInfo`, `submitForm`, `handleClick`
- **Tên phương thức**: Đặt tên kiểu camelCase
  - **Ví dụ**: `data`, `methods`, `computed`, `watch`
- **Hàm vòng đời**: Đặt tên theo quy chuẩn của Vue
  - **Ví dụ**: `created`, `mounted`, `beforeDestroy`
- **Hàm xử lý sự kiện**: Bắt đầu bằng `handle`
  - **Ví dụ**: `handleSubmit`, `handleClick`, `handleChange`

### 2.5 Đặt tên component
- **Tên component**: Kiểu đặt tên PascalCase
  - **Ví dụ**: `Button`, `UserList`, `Navbar`
- **Thẻ component**: Chữ thường, các từ phân tách bằng dấu gạch ngang
  - **Ví dụ**: `<el-button>`, `<user-list>`, `<nav-bar>`
- **Tên file component**: Trùng với tên component, đặt tên kiểu PascalCase
  - **Ví dụ**: `Button.vue`, `UserList.vue`, `Navbar.vue`

### 2.6 Quy tắc đặt tên khác
- **Đặt tên route**: Chữ thường, các từ phân tách bằng dấu gạch ngang
  - **Ví dụ**: `/login`, `/user/list`, `/dashboard`
- **Tên class CSS**: Chữ thường, các từ phân tách bằng dấu gạch ngang
  - **Ví dụ**: `.user-info`, `.goods-list`, `.btn-primary`
- **Đặt tên ID**: Chữ thường, các từ phân tách bằng dấu gạch ngang
  - **Ví dụ**: `#app`, `#header`, `#footer`
- **Đặt tên module Vuex**: Chữ thường, các từ phân tách bằng dấu gạch ngang
  - **Ví dụ**: `user`, `goods`, `orders`

## 3. Phong cách code

### 3.1 Thụt lề
- **Cách thụt lề**: 4 dấu cách
- **Ví dụ**:
  ```vue
  <template>
      <div class="container">
          <h1>{{ title }}</h1>
      </div>
  </template>
  ```

### 3.2 Xuống dòng
- **Xuống dòng cho thẻ**: Thẻ có nhiều thuộc tính nên xuống dòng
- **Ví dụ**:
  ```vue
  <el-button
      type="primary"
      size="medium"
      @click="handleSubmit"
  >
      Gửi
  </el-button>
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
- **Dấu cách sau dấu hai chấm**: Trong object literal, sau dấu hai chấm nên có dấu cách
  - **Ví dụ**: `{ name: 'Nguyễn Văn A', age: 18 }`

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
- **Chú thích component**: Component dùng chú thích tài liệu
  - **Ví dụ**:
    ```vue
    /**
     * Thành phần danh sách người dùng
     * @props {Array} users - Dữ liệu danh sách người dùng
     * @props {Boolean} loading - Có đang tải hay không
     * @events {Function} select - Kích hoạt khi chọn người dùng
     */
    ```

### 3.5 Dấu nháy
- **Chuỗi**: Dùng dấu nháy đơn `''`
  - **Ví dụ**: `const name = 'Nguyễn Văn A'`, `<span>Hello</span>`
- **Chuỗi template**: Dùng dấu backtick `` ` ``
  - **Ví dụ**: `` const url = `${baseUrl}/api/user` ``
- **Thuộc tính HTML**: Dùng dấu nháy kép `""`
  - **Ví dụ**: `<div class="container">`, `<img src="https://example.com/img.jpg">`

### 3.6 Dấu chấm phẩy
- **Kết thúc câu lệnh**: Mỗi câu lệnh nên kết thúc bằng dấu chấm phẩy
  - **Ví dụ**: `const name = 'Nguyễn Văn A';`, `function() {};`

### 3.7 Dòng trống
- **Giữa các khối code**: Giữa các khối code nên có dòng trống
  - **Ví dụ**:
    ```javascript
    function first() {
        // Code
    }

    function second() {
        // Code
    }
    ```
- **Giữa các khối logic**: Giữa các khối logic nên có dòng trống
  - **Ví dụ**:
    ```javascript
    if (condition) {
        // Code
    }

    while (loop) {
        // Code
    }
    ```

## 4. Cấu trúc file

### 4.1 Cấu trúc file component
```vue
<template>
    <!-- Nội dung mẫu -->
</template>

<script>
// Import các phụ thuộc
import { mapState, mapActions } from 'vuex';
import UserApi from '@/api/user';

// Export thành phần
export default {
    // Tên thành phần
    name: 'UserList',
    
    // Thuộc tính của thành phần
    props: {
        pageSize: {
            type: Number,
            default: 10
        }
    },
    
    // Dữ liệu
    data() {
        return {
            users: [],
            loading: false,
            currentPage: 1,
            total: 0
        };
    },
    
    // Thuộc tính computed
    computed: {
        ...mapState('user', ['userInfo']),
        
        // Tính tổng số trang
        totalPages() {
            return Math.ceil(this.total / this.pageSize);
        }
    },
    
    // Theo dõi (listener)
    watch: {
        currentPage: {
            handler(newPage) {
                this.getUserList(newPage);
            }
        }
    },
    
    // Hàm vòng đời
    created() {
        this.getUserList();
    },
    
    // Phương thức
    methods: {
        // Phương thức được ánh xạ từ Vuex
        ...mapActions('user', ['setUserInfo']),
        
        // Lấy danh sách người dùng
        async getUserList(page = 1) {
            try {
                this.loading = true;
                const res = await UserApi.getList({
                    page,
                    pageSize: this.pageSize
                });
                this.users = res.data;
                this.total = res.total;
            } catch (error) {
                console.error('Lấy danh sách người dùng thất bại:', error);
            } finally {
                this.loading = false;
            }
        },
        
        // Chọn người dùng
        handleSelect(user) {
            this.$emit('select', user);
        }
    }
};
</script>

<style scoped>
.user-list {
    padding: 20px;
}

.user-item {
    margin-bottom: 10px;
    padding: 15px;
    border: 1px solid #eaeaea;
    border-radius: 4px;
}

.user-name {
    font-weight: bold;
    margin-bottom: 5px;
}

.user-email {
    color: #666;
    font-size: 14px;
}
</style>
```

### 4.2 Cấu trúc file trang
```vue
<template>
    <div class="user-management">
        <el-card>
            <template slot="header">
                <div class="card-header">
                    <span>Quản lý người dùng</span>
                    <el-button type="primary" @click="handleAdd">Thêm người dùng</el-button>
                </div>
            </template>
            
            <el-form :inline="true" :model="searchForm" class="search-form">
                <el-form-item label="Tên người dùng">
                    <el-input v-model="searchForm.username" placeholder="Vui lòng nhập tên đăng nhập"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSearch">Tìm kiếm</el-button>
                    <el-button @click="resetForm">Đặt lại</el-button>
                </el-form-item>
            </el-form>
            
            <el-table :data="users" style="width: 100%">
                <el-table-column prop="id" label="ID" width="80"></el-table-column>
                <el-table-column prop="username" label="Tên người dùng"></el-table-column>
                <el-table-column prop="email" label="Email"></el-table-column>
                <el-table-column prop="created_at" label="Thời gian tạo"></el-table-column>
                <el-table-column label="Thao tác" width="150">
                    <template slot-scope="scope">
                        <el-button size="small" @click="handleEdit(scope.row)">Sửa</el-button>
                        <el-button size="small" type="danger" @click="handleDelete(scope.row.id)">Xóa</el-button>
                    </template>
                </el-table-column>
            </el-table>
            
            <div class="pagination">
                <el-pagination
                    v-model="currentPage"
                    :page-size="pageSize"
                    :total="total"
                    layout="total, prev, pager, next, jumper"
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                ></el-pagination>
            </div>
        </el-card>
        
        <!-- Hộp thoại thêm/sửa -->
        <el-dialog
            :title="dialogTitle"
            :visible.sync="dialogVisible"
            width="500px"
        >
            <el-form :model="form" :rules="rules" ref="form">
                <el-form-item label="Tên người dùng" prop="username">
                    <el-input v-model="form.username"></el-input>
                </el-form-item>
                <el-form-item label="Email" prop="email">
                    <el-input v-model="form.email"></el-input>
                </el-form-item>
                <el-form-item label="Mật khẩu" v-if="!form.id">
                    <el-input v-model="form.password" type="password"></el-input>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="dialogVisible = false">Hủy</el-button>
                <el-button type="primary" @click="handleSubmit">Xác nhận</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
import UserApi from '@/api/user';

export default {
    name: 'UserManagement',
    
    data() {
        return {
            // Form tìm kiếm
            searchForm: {
                username: ''
            },
            // Danh sách người dùng
            users: [],
            // Thông tin phân trang
            currentPage: 1,
            pageSize: 10,
            total: 0,
            // Hộp thoại
            dialogVisible: false,
            dialogTitle: '',
            form: {},
            // Quy tắc xác thực form
            rules: {
                username: [
                    { required: true, message: 'Vui lòng nhập tên đăng nhập', trigger: 'blur' }
                ],
                email: [
                    { required: true, message: 'Vui lòng nhập email', trigger: 'blur' },
                    { type: 'email', message: 'Vui lòng nhập email đúng định dạng', trigger: 'blur' }
                ]
            }
        };
    },
    
    created() {
        this.getUserList();
    },
    
    methods: {
        // Lấy danh sách người dùng
        async getUserList() {
            try {
                const res = await UserApi.getList({
                    page: this.currentPage,
                    pageSize: this.pageSize,
                    username: this.searchForm.username
                });
                this.users = res.data;
                this.total = res.total;
            } catch (error) {
                console.error('Lấy danh sách người dùng thất bại:', error);
            }
        },
        
        // Tìm kiếm
        handleSearch() {
            this.currentPage = 1;
            this.getUserList();
        },
        
        // Đặt lại form
        resetForm() {
            this.searchForm = {
                username: ''
            };
            this.currentPage = 1;
            this.getUserList();
        },
        
        // Thay đổi số mục mỗi trang
        handleSizeChange(size) {
            this.pageSize = size;
            this.getUserList();
        },
        
        // Thay đổi trang hiện tại
        handleCurrentChange(current) {
            this.currentPage = current;
            this.getUserList();
        },
        
        // Thêm người dùng
        handleAdd() {
            this.dialogTitle = 'Thêm người dùng';
            this.form = {};
            this.dialogVisible = true;
        },
        
        // Sửa người dùng
        handleEdit(user) {
            this.dialogTitle = 'Sửa người dùng';
            this.form = { ...user };
            this.dialogVisible = true;
        },
        
        // Xóa người dùng
        handleDelete(id) {
            this.$confirm('Bạn có chắc muốn xóa người dùng này không?', 'Thông báo', {
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                type: 'warning'
            }).then(async () => {
                try {
                    await UserApi.delete(id);
                    this.$message({
                        type: 'success',
                        message: 'Xóa thành công'
                    });
                    this.getUserList();
                } catch (error) {
                    console.error('Xóa người dùng thất bại:', error);
                }
            });
        },
        
        // Gửi biểu mẫu
        async handleSubmit() {
            try {
                await this.$refs.form.validate();
                if (this.form.id) {
                    // Sửa
                    await UserApi.update(this.form.id, this.form);
                    this.$message({
                        type: 'success',
                        message: 'Cập nhật thành công'
                    });
                } else {
                    // Thêm
                    await UserApi.create(this.form);
                    this.$message({
                        type: 'success',
                        message: 'Thêm thành công'
                    });
                }
                this.dialogVisible = false;
                this.getUserList();
            } catch (error) {
                console.error('Gửi thất bại:', error);
            }
        }
    }
};
</script>

<style scoped>
.user-management {
    padding: 20px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.search-form {
    margin-bottom: 20px;
}

.pagination {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
}
</style>
```

### 4.3 Cấu trúc file JS
```javascript
// Import các phụ thuộc
import axios from 'axios';
import { Message } from 'element-ui';

// Định nghĩa hằng số
const BASE_URL = process.env.VUE_APP_API_BASE_URL;
const TIMEOUT = 10000;

// Tạo instance axios
const service = axios.create({
    baseURL: BASE_URL,
    timeout: TIMEOUT
});

// Interceptor cho request
service.interceptors.request.use(
    config => {
        // Thêm token
        const token = localStorage.getItem('token');
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        return config;
    },
    error => {
        console.error('Lỗi request:', error);
        return Promise.reject(error);
    }
);

// Interceptor cho response
service.interceptors.response.use(
    response => {
        const { data } = response;
        if (data.code !== 200) {
            Message.error(data.message || 'Yêu cầu thất bại');
            return Promise.reject(data);
        }
        return data;
    },
    error => {
        console.error('Lỗi response:', error);
        Message.error('Lỗi mạng, vui lòng thử lại sau');
        return Promise.reject(error);
    }
);

// Hàm tiện ích (utility)
function formatDate(date) {
    const d = new Date(date);
    return d.toLocaleString();
}

function deepClone(obj) {
    return JSON.parse(JSON.stringify(obj));
}

// Xuất
export {
    service,
    formatDate,
    deepClone
};

// Export mặc định
export default service;
```

## 5. Quy chuẩn phát triển component

### 5.1 Thiết kế component
- **Trách nhiệm đơn nhất**: Mỗi component chỉ đảm nhận một chức năng
- **Khả năng tái sử dụng**: Thiết kế component dùng chung, có thể tái sử dụng
- **Khả năng cấu hình**: Cung cấp tùy chọn cấu hình qua props
- **Giao tiếp qua sự kiện**: Giao tiếp với component cha thông qua sự kiện
- **Hỗ trợ slot**: Cung cấp slot để tăng tính linh hoạt cho component

### 5.2 Sử dụng component
- **Import component**: Dùng import để nhập component
  - **Ví dụ**: `import Button from '@/components/Button.vue'`
- **Đăng ký component**: Đăng ký component trong components
  - **Ví dụ**: 
    ```javascript
    components: {
        Button
    }
    ```
- **Sử dụng component**: Dùng thẻ component
  - **Ví dụ**: `<Button type="primary">Nhấn</Button>`
- **Truyền dữ liệu cho component**: Truyền dữ liệu qua props
  - **Ví dụ**: `<UserList :users="userList" :loading="loading" />`
- **Lắng nghe sự kiện**: Lắng nghe sự kiện của component
  - **Ví dụ**: `<UserList @select="handleSelect" />`

### 5.3 Giao tiếp giữa các component
- **props truyền xuống**: Component cha truyền dữ liệu cho component con qua props
- **events truyền lên**: Component con truyền sự kiện lên component cha qua events
- **Tham chiếu qua refs**: Tham chiếu tới instance của component con qua refs
- **provide/inject**: Component tổ tiên truyền dữ liệu cho component hậu duệ
- **Trạng thái toàn cục với Vuex**: Dùng Vuex để quản lý trạng thái toàn cục
- **EventBus**: Event bus dùng giữa các component

### 5.4 Vòng đời component
- **created**: Khởi tạo dữ liệu, gửi request
- **mounted**: Thao tác DOM, khởi tạo thư viện bên thứ ba
- **beforeUpdate**: Công việc chuẩn bị trước khi cập nhật
- **updated**: Thao tác sau khi dữ liệu được cập nhật
- **beforeDestroy**: Dọn dẹp timer, hủy đăng ký (unsubscribe)
- **destroyed**: Công việc dọn dẹp sau khi component bị hủy

### 5.5 Quy tắc đặt tên component
- **Tên component**: Kiểu đặt tên PascalCase
- **Tên file component**: Trùng với tên component
- **Thư mục component**: Lưu theo phân loại chức năng
- **Tiền tố component**: Component dùng chung sử dụng tiền tố thống nhất

## 6. Quy chuẩn phát triển trang

### 6.1 Cấu trúc trang
- **Cấu trúc template**: Rõ ràng, phân cấp mạch lạc
- **Cấu trúc script**: Tổ chức code theo quy chuẩn của Vue
- **Cấu trúc style**: Module hóa, dễ bảo trì
- **Quy chuẩn bố cục**: Tuân thủ quy chuẩn bố cục thống nhất

### 6.2 Quản lý dữ liệu
- **Dữ liệu cục bộ**: Dùng data để quản lý dữ liệu nội bộ của component
- **Dữ liệu tính toán**: Dùng computed để tính dữ liệu dẫn xuất
- **Theo dõi dữ liệu**: Dùng watch để theo dõi thay đổi của dữ liệu
- **Dữ liệu toàn cục**: Dùng Vuex để quản lý dữ liệu toàn cục

### 6.3 Quản lý định tuyến
- **Cấu hình route**: Cấu hình route trong thư mục router
- **Điều hướng route**: Dùng các phương thức như router.push, router.replace
- **Tham số route**: Lấy tham số route qua $route.params
- **Route guard**: Dùng route guard toàn cục/cục bộ

### 6.4 Gọi API
- **Đóng gói API**: Đóng gói thống nhất các lệnh gọi API
- **Xử lý bất đồng bộ**: Dùng async/await để xử lý request bất đồng bộ
- **Xử lý lỗi**: Dùng try/catch để bắt lỗi
- **Trạng thái tải**: Hiển thị trạng thái đang tải, nâng cao trải nghiệm người dùng

### 6.5 Trải nghiệm người dùng
- **Thiết kế responsive**: Tương thích với các kích thước màn hình khác nhau
- **Trạng thái tải**: Hiển thị thông báo đang tải
- **Thông báo lỗi**: Hiển thị thông tin lỗi
- **Thông báo thành công**: Hiển thị thông báo thao tác thành công
- **Kiểm tra biểu mẫu**: Kiểm tra dữ liệu biểu mẫu theo thời gian thực
- **Debounce và throttle**: Tối ưu các sự kiện được kích hoạt thường xuyên

## 7. Tối ưu hiệu năng

### 7.1 Tối ưu code
- **Giảm code dư thừa**: Tránh code lặp lại
- **Dùng thuộc tính computed**: Với các phép tính phức tạp, hãy dùng computed
- **Dùng v-if và v-show hợp lý**: Chọn directive phù hợp với từng tình huống
- **Dùng key**: Dùng key trong v-for để tăng hiệu năng render
- **Tránh cập nhật quá thường xuyên**: Dùng debounce và throttle

### 7.2 Tối ưu mạng
- **Dùng cache hợp lý**: Cache những dữ liệu ít thay đổi
- **Giảm số lượng request**: Gộp request, thao tác theo lô
- **Dùng CDN**: Dùng CDN cho tài nguyên tĩnh
- **Nén khi truyền tải**: Dùng gzip để nén dữ liệu truyền tải

### 7.3 Tối ưu build
- **Tree Shaking**: Loại bỏ code không sử dụng
- **Tách code (code splitting)**: Tách code theo route
- **Lazy load**: Lazy load route, lazy load component
- **Tải trước (preload)**: Tải trước các tài nguyên quan trọng

### 7.4 Tối ưu khác
- **Giảm số node DOM**: Đơn giản hóa cấu trúc DOM
- **Tối ưu hình ảnh**: Dùng định dạng và kích thước ảnh phù hợp
- **Dùng danh sách ảo**: Với danh sách dài, hãy dùng danh sách ảo (virtual list)
- **Tránh rò rỉ bộ nhớ**: Kịp thời dọn dẹp timer, event listener, v.v.

## 8. Sự cố thường gặp

### 8.1 Vấn đề về phong cách code
- **Vấn đề**: Phong cách code không thống nhất
- **Giải pháp**: Dùng ESLint và Prettier để thống nhất phong cách code

### 8.2 Vấn đề hiệu năng
- **Vấn đề**: Trang tải chậm, giật lag
- **Giải pháp**: Tối ưu code, giảm thao tác DOM, dùng danh sách ảo, v.v.

### 8.3 Vấn đề tương thích
- **Vấn đề**: Hiển thị không nhất quán trên các trình duyệt khác nhau
- **Giải pháp**: Tuân thủ tiêu chuẩn Web, dùng polyfill

### 8.4 Vấn đề bảo trì
- **Vấn đề**: Code khó bảo trì
- **Giải pháp**: Phát triển theo module, thêm chú thích, tuân thủ quy chuẩn code

### 8.5 Vấn đề xung đột tên
- **Vấn đề**: Xung đột tên
- **Giải pháp**: Dùng namespace, tránh biến toàn cục

## 9. Tài liệu tham khảo

- [Hướng dẫn phong cách chính thức của Vue](https://v2.vuejs.org/v2/style-guide/)
- [Tài liệu chính thức Element UI](https://element.eleme.io/#/zh-CN)
- [Tài liệu chính thức ESLint](https://eslint.org/docs/user-guide/)
- [Tài liệu chính thức Prettier](https://prettier.io/docs/en/)
- [Quy chuẩn code JavaScript](https://github.com/airbnb/javascript)
- [Quy chuẩn code CSS](https://github.com/airbnb/css)

## 10. Tổng kết

Tài liệu này mô tả quy chuẩn code của frontend trang quản trị trong dự án CRMEB, bao gồm các quy chuẩn về quy tắc đặt tên, phong cách code, cấu trúc tệp, phát triển thành phần (component), v.v. Tuân thủ các quy chuẩn trong tài liệu này giúp nâng cao khả năng đọc hiểu, khả năng bảo trì và khả năng mở rộng của code, đảm bảo chất lượng và sự ổn định của dự án.

Quy chuẩn code là nền tảng của việc làm việc nhóm; các thành viên trong đội phát triển nên tuân thủ nghiêm ngặt các quy chuẩn trong tài liệu này để cùng duy trì một codebase chất lượng cao.