# Mô tả kiến trúc frontend trang quản trị CRMEB

## 📋 Tổng quan bộ công nghệ 

| Công nghệ | Phiên bản | Mô tả |
|------|------|------|
| Vue.js | 2.x | Framework frontend cốt lõi |
| Element UI | 2.x | Thư viện component UI |
| Vuex | 3.x | Quản lý trạng thái |
| Axios | 0.x | HTTP client |
| VXE-Table | Thành phần bảng nâng cao | |
| WangEditor | Trình soạn thảo văn bản định dạng | |
| Sass | Bộ tiền xử lý CSS | |
| Prettier | Công cụ định dạng code | |
| Vue-Viewer | Trình xem ảnh | |

## ??️ Kiến trúc tổng thể

```
┌─────────────────────────────────────────────────────────────┐
│                      Vue Router                             │
│                    (Bộ quản lý route)                              │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                      Vuex Store                             │
│                    (Quản lý trạng thái)                               │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│  │ Module User │ │ Module Order│ │ Module Product│ │ Module System│      │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘      │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                    Vue Components                           │
│                    (Tầng thành phần giao diện)                              │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  Layout bố cục │ Table bảng │ Form biểu mẫu    │       │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                     API Services                            │
│                    (Tầng dịch vụ API)                              │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                    Axios HTTP Client                        │
│                    (Tầng request HTTP)                              │
└─────────────────────────────────────────────────────────────┘
```

## 📂 Cấu trúc thư mục

### Cấu trúc thư mục frontend quản trị tiêu chuẩn

```
admin-web/                          # Dự án frontend trang quản trị
├── public/                         # Tài nguyên công khai
│   ├── index.html                 # Template HTML
│   └── favicon.ico                # Biểu tượng website
│
├── src/                           # Thư mục mã nguồn
│   ├── api/                       # Định nghĩa API
│   │   ├── user.js                # API liên quan đến người dùng
│   │   ├── order.js               # API liên quan đến đơn hàng
│   │   ├── product.js             # API liên quan đến sản phẩm
│   │   ├── system.js              # API liên quan đến hệ thống
│   │   └── index.js               # Điểm vào API
│   │
│   ├── assets/                    # Tài nguyên tĩnh
│   │   ├── images/                # Tài nguyên hình ảnh
│   │   ├── styles/                # File style
│   │   └── fonts/                 # File font
│   │
│   ├── components/                # Thành phần (component) dùng chung
│   │   ├── Layout/                # Thành phần layout
│   │   │   ├── Header.vue         # Điều hướng trên cùng
│   │   │   ├── Sidebar.vue        # Menu bên
│   │   │   └── Breadcrumb.vue     # Breadcrumb
│   │   │
│   │   ├── Table/                 # Thành phần bảng
│   │   │   ├── TableColumn.vue    # Cột bảng
│   │   │   └── TablePage.vue      # Thành phần phân trang
│   │   │
│   │   ├── Form/                  # Thành phần form
│   │   │   ├── FormItem.vue       # Mục form
│   │   │   └── Upload.vue         # Thành phần tải lên
│   │   │
│   │   └── Common/                # Thành phần dùng chung
│   │       ├── Dialog.vue         # Hộp thoại
│   │       ├── Select.vue         # Bộ chọn
│   │       └── UploadImage.vue    # Tải lên ảnh
│   │
│   ├── views/                     # View của trang
│   │   ├── login/                 # Trang đăng nhập
│   │   │   └── index.vue
│   │   │
│   │   ├── dashboard/             # Bảng điều khiển
│   │   │   └── index.vue
│   │   │
│   │   ├── user/                  # Quản lý người dùng
│   │   │   ├── list.vue           # Danh sách người dùng
│   │   │   ├── detail.vue         # Chi tiết người dùng
│   │   │   └── edit.vue           # Sửa người dùng
│   │   │
│   │   ├── order/                 # Quản lý đơn hàng
│   │   │   ├── list.vue           # Danh sách đơn hàng
│   │   │   ├── detail.vue         # Chi tiết đơn hàng
│   │   │   └── export.vue         # Xuất đơn hàng
│   │   │
│   │   ├── product/               # Quản lý sản phẩm
│   │   │   ├── list.vue           # Danh sách sản phẩm
│   │   │   ├── edit.vue           # Sửa sản phẩm
│   │   │   └── category.vue       # Quản lý danh mục
│   │   │
│   │   ├── marketing/             # Quản lý marketing
│   │   │   ├── coupon.vue         # Phiếu giảm giá
│   │   │   ├── seckill.vue        # Hoạt động flash sale
│   │   │   └── combination.vue    # Hoạt động mua chung
│   │   │
│   │   └── system/                # Quản lý hệ thống
│   │       ├── config.vue         # Cấu hình hệ thống
│   │       ├── admin.vue          # Quản trị viên
│   │       └── role.vue           # Quyền theo vai trò
│   │
│   ├── router/                    # Cấu hình route
│   │   ├── index.js               # Điểm vào router
│   │   └── modules/               # Route theo module
│   │       ├── user.js
│   │       ├── order.js
│   │       └── system.js
│   │
│   ├── store/                     # Quản lý trạng thái Vuex
│   │   ├── index.js               # Điểm vào Store
│   │   ├── getters.js             # Getters
│   │   └── modules/               # Module trạng thái
│   │       ├── user.js            # Trạng thái người dùng
│   │       ├── app.js             # Trạng thái ứng dụng
│   │       └── permission.js      # Trạng thái quyền
│   │
│   ├── utils/                     # Hàm tiện ích (utility)
│   │   ├── request.js             # Đóng gói Axios
│   │   ├── auth.js                # Liên quan đến quyền
│   │   ├── validate.js            # Kiểm tra form
│   │   └── format.js              # Xử lý định dạng
│   │
│   ├── directive/                 # Directive tùy chỉnh
│   │   ├── permission.js          # Directive phân quyền
│   │   └── draggable.js           # Directive kéo thả
│   │
│   ├── filters/                   # Filter
│   │   ├── index.js               # Điểm vào filter
│   │   ├── date.js                # Định dạng ngày
│   │   └── currency.js            # Định dạng số tiền
│   │
│   ├── App.vue                    # Thành phần gốc
│   └── main.js                    # File điểm vào
│
├── .env                           # Biến môi trường
├── .env.development               # Cấu hình môi trường phát triển
├── .env.production                # Cấu hình môi trường production
│
├── package.json                   # Cấu hình dự án
├── vue.config.js                  # Vue CLI cấu hình
└── README.md                      # Giới thiệu dự án
```

## 🔧 Chi tiết các module cốt lõi

### 1. Module định tuyến (Router)

#### Cấu trúc cấu hình route
```javascript
// src/router/index.js
import Vue from 'vue'
import VueRouter from 'vue-router'
import { constantRoutes } from './modules'

Vue.use(VueRouter)

// Tạo instance router
export const createRouter = () => new VueRouter({
  routes: constantRoutes,
  mode: 'history',
  scrollBehavior: () => ({ y: 0 })
})

// Route tĩnh
export const constantRoutes = [
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    meta: { title: 'Đăng nhập' }
  },
  {
    path: '/404',
    component: () => import('@/views/error/404')
  },
  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import('@/views/dashboard/index'),
        meta: { title: 'Bảng điều khiển', icon: 'dashboard' }
      }
    ]
  }
]
```

#### Route động (kiểm soát quyền)
```javascript
// src/router/modules/permission.js
export const asyncRoutes = [
  {
    path: '/user',
    component: Layout,
    meta: {
      title: 'Quản lý người dùng',
      icon: 'user',
      roles: ['admin', 'manager']
    },
    children: [
      {
        path: 'list',
        name: 'UserList',
        component: () => import('@/views/user/list'),
        meta: {
          title: 'Danh sách người dùng',
          roles: ['admin']
        }
      }
    ]
  }
]
```

### 2. Quản lý trạng thái (Vuex)

#### Cấu trúc Store
```javascript
// src/store/index.js
import Vue from 'vue'
import Vuex from 'vuex'
import user from './modules/user'
import app from './modules/app'
import permission from './modules/permission'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    user,
    app,
    permission
  },
  getters: {
    token: state => state.user.token,
    roles: state => state.user.roles,
    sidebarOpened: state => app.sidebarOpened,
    permissionRoutes: state => permission.routes
  }
})
```

#### Module trạng thái người dùng
```javascript
// src/store/modules/user.js
const state = {
  token: localStorage.getItem('token') || '',
  userInfo: {},
  roles: []
}

const mutations = {
  SET_TOKEN(state, token) {
    state.token = token
    localStorage.setItem('token', token)
  },
  SET_USER_INFO(state, userInfo) {
    state.userInfo = userInfo
  },
  SET_ROLES(state, roles) {
    state.roles = roles
  }
}

const actions = {
  // Đăng nhập
  async login({ commit }, userInfo) {
    const res = await login(userInfo)
    commit('SET_TOKEN', res.data.token)
    return res
  },

  // Lấy thông tin người dùng
  async getInfo({ commit }) {
    const res = await getUserInfo()
    commit('SET_USER_INFO', res.data)
    commit('SET_ROLES', res.data.roles)
    return res
  },

  // Đăng xuất
  async logout({ commit }) {
    await logout()
    commit('SET_TOKEN', '')
    commit('SET_USER_INFO', {})
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
```

### 3. Tầng dịch vụ API

#### Đóng gói Axios
```javascript
// src/utils/request.js
import axios from 'axios'
import { Message, MessageBox } from 'element-ui'
import store from '@/store'
import router from '@/router'

// Tạo instance axios
const service = axios.create({
  baseURL: process.env.VUE_APP_BASE_API,
  timeout: 30000
})

// Interceptor cho request
service.interceptors.request.use(
  config => {
    // Thêm token
    if (store.getters.token) {
      config.headers['Authorization'] = `Bearer ${store.getters.token}`
    }
    return config
  },
  error => {
    return Promise.reject(error)
  }
)

// Interceptor cho response
service.interceptors.response.use(
  response => {
    const res = response.data
    if (res.code === 200) {
      return res
    }
    Message.error(res.msg || 'Yêu cầu thất bại')
    return Promise.reject(new Error(res.msg || 'Yêu cầu thất bại'))
  },
  error => {
    if (error.response) {
      const { status } = error.response
      if (status === 401) {
        MessageBox.confirm('Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại', 'Xác nhận đăng nhập', {
          confirmButtonText: 'Đăng nhập lại',
          cancelButtonText: 'Hủy',
          type: 'warning'
        }).then(() => {
          store.dispatch('user/logout')
          router.push('/login')
        })
      }
    }
    return Promise.reject(error)
  }
)

export default service
```

#### Định nghĩa API
```javascript
// src/api/user.js
import request from '@/utils/request'

// Lấy danh sách người dùng
export function getUserList(params) {
  return request({
    url: '/adminapi/v1/user/list',
    method: 'get',
    params
  })
}

// Lấy chi tiết người dùng
export function getUserDetail(id) {
  return request({
    url: `/adminapi/v1/user/${id}`,
    method: 'get'
  })
}

// Sửa người dùng
export function editUser(data) {
  return request({
    url: '/adminapi/v1/user/edit',
    method: 'post',
    data
  })
}

// Xóa người dùng
export function deleteUser(id) {
  return request({
    url: `/adminapi/v1/user/${id}`,
    method: 'delete'
  })
}
```

### 4. Component bố cục (Layout)

#### Component bố cục chính
```vue
<!-- src/components/Layout/index.vue -->
<template>
  <el-container class="app-wrapper">
    <!-- Thanh bên -->
    <el-aside :width="sidebarOpened ? '200px' : '64px'">
      <Sidebar />
    </el-aside>

    <el-container>
      <!-- Điều hướng trên cùng -->
      <el-header>
        <Navbar />
      </el-header>

      <!-- Vùng nội dung chính -->
      <el-main>
        <router-view />
      </el-main>
    </el-container>
  </el-container>
</template>

<script>
import { mapGetters } from 'vuex'
import Sidebar from './Sidebar'
import Navbar from './Navbar'

export default {
  name: 'Layout',
  components: { Sidebar, Navbar },
  computed: {
    ...mapGetters(['sidebarOpened'])
  }
}
</script>

<style lang="scss" scoped>
.app-wrapper {
  height: 100vh;
}
.el-aside {
  transition: width 0.3s;
}
.el-header {
  padding: 0;
  box-shadow: 0 1px 4px rgba(0, 21, 41, 0.08);
}
.el-main {
  background-color: #f0f2f5;
  padding: 20px;
}
</style>
```

#### Component thanh bên (sidebar)
```vue
<!-- src/components/Layout/Sidebar/index.vue -->
<template>
  <el-menu
    :default-active="activeMenu"
    :collapse="!sidebarOpened"
    :unique-opened="true"
    background-color="#304156"
    text-color="#bfcbd9"
    active-text-color="#409EFF"
    router
  >
    <sidebar-item
      v-for="route in permissionRoutes"
      :key="route.path"
      :item="route"
      :base-path="route.path"
    />
  </el-menu>
</template>

<script>
import { mapGetters } from 'vuex'
import SidebarItem from './SidebarItem'

export default {
  name: 'Sidebar',
  components: { SidebarItem },
  computed: {
    ...mapGetters(['permissionRoutes']),
    activeMenu() {
      const route = this.$route
      const { meta, path } = route
      if (meta.activeMenu) {
        return meta.activeMenu
      }
      return path
    }
  }
}
</script>
```

### 5. Component trang danh sách

#### Mẫu trang danh sách dùng chung
```vue
<!-- src/views/user/list.vue -->
<template>
  <div class="app-container">
    <!-- Form tìm kiếm -->
    <el-card class="filter-card">
      <el-form :model="queryParams" ref="queryForm" :inline="true">
        <el-form-item label="Tên người dùng" prop="nickname">
          <el-input v-model="queryParams.nickname" placeholder="Vui lòng nhập tên người dùng" clearable />
        </el-form-item>
        <el-form-item label="Số điện thoại" prop="phone">
          <el-input v-model="queryParams.phone" placeholder="Vui lòng nhập số điện thoại" clearable />
        </el-form-item>
        <el-form-item label="Trạng thái người dùng" prop="status">
          <el-select v-model="queryParams.status" placeholder="Vui lòng chọn trạng thái" clearable>
            <el-option label="Kích hoạt" :value="1" />
            <el-option label="Vô hiệu hóa" :value="0" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleQuery">Tìm kiếm</el-button>
          <el-button @click="resetQuery">Đặt lại</el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- Nút thao tác -->
    <el-card class="table-card">
      <div slot="header">
        <el-button type="primary" @click="handleAdd">Thêm người dùng</el-button>
        <el-button type="warning" @click="handleExport" :loading="exportLoading">
          Dữ liệu xuất
        </el-button>
      </div>

      <!-- Bảng dữ liệu -->
      <el-table
        v-loading="loading"
        :data="tableData"
        stripe
        border
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="50" align="center" />
        <el-table-column prop="id" label="ID người dùng" width="80" align="center" />
        <el-table-column prop="nickname" label="Tên người dùng" min-width="120" />
        <el-table-column prop="phone" label="Số điện thoại" width="120" align="center" />
        <el-table-column prop="avatar" label="Ảnh đại diện" width="80" align="center">
          <template slot-scope="scope">
            <el-avatar :src="scope.row.avatar" />
          </template>
        </el-table-column>
        <el-table-column prop="status" label="Trạng thái" width="100" align="center">
          <template slot-scope="scope">
            <el-tag :type="scope.row.status ? 'success' : 'danger'">
              {{ scope.row.status ? 'Kích hoạt' : 'Vô hiệu hóa' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="Thời gian tạo" width="160" align="center" />
        <el-table-column label="Thao tác" width="200" align="center" fixed="right">
          <template slot-scope="scope">
            <el-button size="mini" type="text" @click="handleView(scope.row)">Xem</el-button>
            <el-button size="mini" type="text" @click="handleEdit(scope.row)">Sửa</el-button>
            <el-button size="mini" type="text" @click="handleDelete(scope.row)" style="color: #f56c6c">
              Xóa
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- Phân trang -->
      <pagination
        v-show="total > 0"
        :total="total"
        :page.sync="queryParams.page"
        :limit.sync="queryParams.limit"
        @pagination="getList"
      />
    </el-card>
  </div>
</template>

<script>
import { getUserList, deleteUser } from '@/api/user'

export default {
  name: 'UserList',
  data() {
    return {
      // Lớp phủ
      loading: true,
      // Lớp phủ khi xuất
      exportLoading: false,
      // Mảng các mục đã chọn
      ids: [],
      // Tổng số bản ghi
      total: 0,
      // Dữ liệu bảng
      tableData: [],
      // Tham số truy vấn
      queryParams: {
        page: 1,
        limit: 20,
        nickname: undefined,
        phone: undefined,
        status: undefined
      }
    }
  },
  created() {
    this.getList()
  },
  methods: {
    // Lấy dữ liệu danh sách
    getList() {
      this.loading = true
      getUserList(this.queryParams).then(response => {
        this.tableData = response.data.list
        this.total = response.data.total
        this.loading = false
      }).catch(() => {
        this.loading = false
      })
    },

    // Tìm kiếm
    handleQuery() {
      this.queryParams.page = 1
      this.getList()
    },

    // Đặt lại
    resetQuery() {
      this.resetForm('queryForm')
      this.handleQuery()
    },

    // Thêm mới
    handleAdd() {
      this.$router.push({ path: '/user/edit' })
    },

    // Sửa
    handleEdit(row) {
      this.$router.push({ path: '/user/edit', query: { id: row.id } })
    },

    // Xem
    handleView(row) {
      this.$router.push({ path: '/user/detail', query: { id: row.id } })
    },

    // Xóa
    handleDelete(row) {
      this.$confirm(`Bạn có chắc muốn xóa người dùng"${row.nickname}"không?`, 'Thông báo', {
        confirmButtonText: 'Xác nhận',
        cancelButtonText: 'Hủy',
        type: 'warning'
      }).then(() => {
        deleteUser(row.id).then(() => {
          this.getList()
          this.$message.success('Xóa thành công')
        })
      }).catch(() => {})
    },

    // Xuất
    handleExport() {
      this.exportLoading = true
      exportUser(this.queryParams).finally(() => {
        this.exportLoading = false
      })
    },

    // Chọn nhiều
    handleSelectionChange(selection) {
      this.ids = selection.map(item => item.id)
    }
  }
}
</script>
```

### 6. Component trang biểu mẫu

#### Mẫu trang biểu mẫu dùng chung
```vue
<!-- src/views/user/edit.vue -->
<template>
  <div class="app-container">
    <el-card>
      <el-form ref="form" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="Tên người dùng" prop="nickname">
          <el-input v-model="form.nickname" placeholder="Vui lòng nhập tên người dùng" />
        </el-form-item>
        <el-form-item label="Số điện thoại" prop="phone">
          <el-input v-model="form.phone" placeholder="Vui lòng nhập số điện thoại" />
        </el-form-item>
        <el-form-item label="Mật khẩu người dùng" prop="password" v-if="!form.id">
          <el-input v-model="form.password" type="password" placeholder="Vui lòng nhập mật khẩu" />
        </el-form-item>
        <el-form-item label="Trạng thái người dùng" prop="status">
          <el-radio-group v-model="form.status">
            <el-radio :label="1">Kích hoạt</el-radio>
            <el-radio :label="0">Vô hiệu hóa</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="Ảnh đại diện người dùng">
          <UploadImage v-model="form.avatar" :limit="1" />
        </el-form-item>
        <el-form-item label="Ghi chú của người dùng">
          <el-input v-model="form.remark" type="textarea" rows="3" placeholder="Vui lòng nhập ghi chú" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitForm" :loading="loading">Lưu</el-button>
          <el-button @click="cancel">Hủy</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
import { getUserDetail, createUser, updateUser } from '@/api/user'
import UploadImage from '@/components/Upload/Image'

export default {
  name: 'UserEdit',
  components: { UploadImage },
  data() {
    return {
      loading: false,
      form: {
        id: undefined,
        nickname: '',
        phone: '',
        password: '',
        status: 1,
        avatar: '',
        remark: ''
      },
      rules: {
        nickname: [
          { required: true, message: 'Vui lòng nhập tên người dùng', trigger: 'blur' },
          { min: 2, max: 20, message: 'Độ dài từ 2 đến 20 ký tự', trigger: 'blur' }
        ],
        phone: [
          { required: true, message: 'Vui lòng nhập số điện thoại', trigger: 'blur' },
          { pattern: /^1[3-9]\d{9}$/, message: 'Số điện thoại không đúng định dạng', trigger: 'blur' }
        ],
        password: [
          { required: true, message: 'Vui lòng nhập mật khẩu', trigger: 'blur' },
          { min: 6, max: 20, message: 'Độ dài từ 6 đến 20 ký tự', trigger: 'blur' }
        ]
      }
    }
  },
  created() {
    const id = this.$route.query.id
    if (id) {
      this.getDetail(id)
    }
  },
  methods: {
    // Lấy chi tiết
    getDetail(id) {
      getUserDetail(id).then(response => {
        this.form = response.data
        delete this.form.password
      })
    },

    // Gửi
    submitForm() {
      this.$refs.form.validate(valid => {
        if (valid) {
          this.loading = true
          const func = this.form.id ? updateUser : createUser
          func(this.form).then(response => {
            this.$message.success('Lưu thành công')
            this.cancel()
          }).finally(() => {
            this.loading = false
          })
        }
      })
    },

    // Hủy
    cancel() {
      this.$router.back()
    }
  }
}
</script>
```

## 🎨 Quy chuẩn style

### Định nghĩa biến SCSS
```scss
// src/assets/styles/variables.scss

// Màu chủ đề
$primary-color: #409EFF;
$success-color: #67C23A;
$warning-color: #E6A23C;
$danger-color: #F56C6C;
$info-color: #909399;

// Màu nền
$bg-color: #f0f2f5;
$white: #ffffff;

// Màu chữ
$text-primary: #303133;
$text-regular: #606266;
$text-secondary: #909399;
$text-placeholder: #c0c4cc;

// Màu viền
$border-color: #dcdfe6;
$border-color-light: #e4e7ed;
$border-color-lighter: #ebeef5;

// Font chữ
$font-size-base: 14px;
$font-size-small: 12px;
$font-size-large: 16px;
```

### Kiểu chung
```scss
// src/assets/styles/common.scss

// Container trang dùng chung
.app-container {
  padding: 20px;
}

// Style cho card
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

// Nút thao tác trong bảng
.table-action-btn {
  margin: 0 5px;
}

// Style cho form
.form-container {
  max-width: 600px;
  margin: 0 auto;
}
```

## 🔐 Kiểm soát quyền

### Directive phân quyền
```javascript
// src/directive/permission.js
import Vue from 'vue'

// v-permission directive
Vue.directive('permission', {
  inserted: function(el, binding) {
    const { value } = binding
    const permissions = Vue.prototype.$store.getters.permissions

    if (value && value instanceof Array) {
      const hasPermission = value.some(permission => {
        return permissions.includes(permission)
      })

      if (!hasPermission) {
        el.parentNode && el.parentNode.removeChild(el)
      }
    } else {
      throw new Error('v-permission value must be array')
    }
  }
})
```

### Ví dụ sử dụng
```vue
<template>
  <div>
    <!-- Kiểm soát quyền theo nút -->
    <el-button v-permission="['user:add']" type="primary" @click="handleAdd">
      Thêm người dùng
    </el-button>

    <el-button v-permission="['user:export']" type="warning" @click="handleExport">
      Dữ liệu xuất
    </el-button>
  </div>
</template>
```

## 📊 Sử dụng các component thường dùng

### Component bảng (VXE-Table)
```vue
<template>
  <vxe-table
    :data="tableData"
    :loading="loading"
    :columns="columns"
    :pagination="pagination"
    @page-change="handlePageChange"
  >
    <vxe-column field="name" title="Tên" />
    <vxe-column field="status" title="Trạng thái">
      <template #default="{ row }">
        <el-tag :type="row.status ? 'success' : 'danger'">
          {{ row.status ? 'Kích hoạt' : 'Vô hiệu hóa' }}
        </el-tag>
      </template>
    </vxe-column>
    <vxe-column title="Thao tác">
      <template #default="{ row }">
        <el-button size="mini" type="text" @click="handleEdit(row)">Sửa</el-button>
      </template>
    </vxe-column>
  </vxe-table>
</template>
```

### Trình soạn thảo văn bản định dạng (WangEditor)
```vue
<template>
  <div class="editor-container">
    <wang-editor
      v-model="form.content"
      :height="400"
      :menus="menus"
    />
  </div>
</template>

<script>
import WangEditor from 'wangeditor'

export default {
  components: { WangEditor },
  data() {
    return {
      menus: [
        'head', 'bold', 'italic', 'underline', 'strikeThrough',
        'list', 'justify', 'quote', 'link', 'image'
      ],
      content: ''
    }
  }
}
</script>
```

## 🧪 Cấu hình kiểm thử

### Cấu hình kiểm thử Jest
```javascript
// jest.config.js
module.exports = {
  testURL: 'http://localhost',
  moduleFileExtensions: ['js', 'jsx', 'json', 'vue'],
  transform: {
    '^.+\\.vue$': 'vue-jest',
    '^.+\\.js$': 'babel-jest'
  },
  moduleNameMapper: {
    '^@/(.*)$': '<rootDir>/src/$1'
  },
  testMatch: ['<rootDir>/tests/**/*.spec.js'],
  collectCoverageFrom: [
    'src/**/*.{js,vue}',
    '!src/main.js'
  ]
}
```

### Ví dụ kiểm thử
```javascript
// tests/unit/user.spec.js
import { shallowMount } from '@vue/test-utils'
import UserList from '@/views/user/list.vue'

describe('UserList', () => {
  it('renders user list correctly', () => {
    const wrapper = shallowMount(UserList, {
      mocks: {
        $route: { path: '/user/list' },
        $store: {
          getters: { roles: ['admin'] }
        }
      }
    })

    expect(wrapper.find('.el-table').exists()).toBe(true)
    expect(wrapper.find('.pagination').exists()).toBe(true)
  })
})
```

## 🚀 Build và triển khai

### Cấu hình môi trường
```bash
# .env.development
VUE_APP_BASE_API = 'http://localhost:8000'
VUE_APP_WS_API = 'ws://localhost:8000'

# .env.production
VUE_APP_BASE_API = 'https://api.crmeb.com'
VUE_APP_WS_API = 'wss://api.crmeb.com'
```

### Lệnh build
```bash
# Cài đặt các gói phụ thuộc
npm install

# Môi trường phát triển
npm run dev

# Build môi trường production
npm run build:prod

# Kiểm tra code
npm run lint

# Kiểm thử đơn vị
npm run test
```

### Cấu hình Nginx
```nginx
server {
    listen 80;
    server_name admin.crmeb.com;

    # Tài nguyên tĩnh frontend
    location / {
        root /usr/share/nginx/html;
        index index.html;
        try_files $uri $uri/ /index.html;
    }

    # Proxy API
    location /adminapi/ {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }

    # Proxy WebSocket
    location /ws/ {
        proxy_pass http://127.0.0.1:8000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
    }
}
```

## 📝 Quy chuẩn phát triển

### Phong cách code
- Dùng ESLint + Prettier để định dạng code
- Component Vue viết dưới dạng single-file component (.vue)
- Tên component đặt theo kiểu PascalCase
- Style dùng bộ tiền xử lý SCSS

### Quy chuẩn commit Git
```
feat: Thêm chức năng quản lý người dùng
fix: Sửa lỗi phân trang danh sách đơn hàng
docs: Cập nhật tài liệu API
style: Điều chỉnh định dạng code
refactor: Refactor code module người dùng
test: Thêm kiểm thử đơn vị
chore: Cập nhật cấu hình build
```

---
**Phiên bản tài liệu**: v1.0  
**Cập nhật lần cuối**: 2024-01-17  
**Phiên bản áp dụng**: CRMEB 5.6.4+  
**Nhóm bảo trì**: Nhóm phát triển frontend  

💡 Lưu ý: Frontend trang quản trị sử dụng thư viện component Element UI, kết hợp Vuex để quản lý trạng thái, xử lý thống nhất các HTTP request thông qua lớp đóng gói Axios, hiện thực đầy đủ các chức năng kiểm soát quyền và tương tác dữ liệu.

---

> **Lưu ý**: Tài liệu này do AI tạo ra, chỉ mang tính tham khảo.
