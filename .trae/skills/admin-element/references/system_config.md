# Tài liệu cấu hình hệ thống Admin-Element

## 1 Cấu hình biến môi trường

### 1.1 File biến môi trường

Dự án Admin-Element dùng file `.env` để quản lý biến môi trường:

```
template/admin-element/
├── .env                # File biến môi trường cơ sở
├── .env.development    # Biến môi trường phát triển
├── .env.production     # Biến môi trường production
└── .env.staging        # Biến môi trường kiểm thử
```

### 1.2 Ví dụ cấu hình biến môi trường

Cấu hình biến môi trường cho môi trường phát triển trong `.env.development`:

```dotenv
# Đường dẫn API cơ sở
VUE_APP_BASE_API = 'http://localhost:8080/api'

# Tiêu đề dự án
VUE_APP_TITLE = 'CRMEB Trang quản trị'

# Môi trường phát triển
NODE_ENV = 'development'

# Cổng (port)
VUE_APP_PORT = '9527'

# Thư mục đầu ra build
VUE_APP_OUTPUT_DIR = 'dist'

# Có bật nén code không
VUE_APP_COMPRESS = 'false'

# Trạng thái kích hoạt source map
VUE_APP_SOURCE_MAP = 'true'
```

Cấu hình biến môi trường cho môi trường production trong `.env.production`:

```dotenv
# Đường dẫn API cơ sở
VUE_APP_BASE_API = 'https://api.example.com'

# Tiêu đề dự án
VUE_APP_TITLE = 'CRMEB Trang quản trị'

# Môi trường production
NODE_ENV = 'production'

# Cổng (port)
VUE_APP_PORT = '80'

# Thư mục đầu ra build
VUE_APP_OUTPUT_DIR = 'dist'

# Có bật nén code không
VUE_APP_COMPRESS = 'true'

# Trạng thái kích hoạt source map
VUE_APP_SOURCE_MAP = 'false'
```

## 2 Cấu hình cơ bản của dự án

### 2.1 Cấu hình package.json

Cấu hình thông tin cơ bản và các dependency của dự án trong file `package.json`:

```json
{
  "name": "admin-element",
  "version": "1.0.0",
  "description": "CRMEB Dự án frontend trang quản trị",
  "author": "CRMEB Team",
  "private": true,
  "scripts": {
    "dev": "vue-cli-service serve",
    "build": "vue-cli-service build",
    "lint": "vue-cli-service lint",
    "preview": "serve dist",
    "test": "vue-cli-service test:unit"
  },
  "dependencies": {
    "vue": "^2.6.11",
    "vue-router": "^3.2.0",
    "vuex": "^3.4.0",
    "axios": "^0.21.1",
    "element-ui": "^2.14.1",
    "lodash": "^4.17.21"
  },
  "devDependencies": {
    "@vue/cli-service": "^4.5.13",
    "@vue/cli-plugin-babel": "^4.5.13",
    "@vue/cli-plugin-eslint": "^4.5.13",
    "@vue/cli-plugin-unit-jest": "^4.5.13",
    "babel-eslint": "^10.1.0",
    "eslint": "^6.7.2",
    "eslint-plugin-vue": "^6.2.2"
  }
}
```

### 2.2 Cấu hình vue.config.js

Cấu hình các tùy chọn liên quan đến Vue CLI trong file `vue.config.js`:

```javascript
const { defineConfig } = require('@vue/cli-service')
const path = require('path')

module.exports = defineConfig({
  // Gói ứng dụng được triển khai tại base URL
  publicPath: process.env.NODE_ENV === 'production' ? '/' : '/',
  
  // Thư mục đầu ra build
  outputDir: process.env.VUE_APP_OUTPUT_DIR || 'dist',
  
  // Thư mục tài nguyên tĩnh
  assetsDir: 'static',
  
  // Môi trường production có tạo source map
  productionSourceMap: process.env.VUE_APP_SOURCE_MAP === 'true',
  
  // Cấu hình máy chủ phát triển
  devServer: {
    port: process.env.VUE_APP_PORT || 9527,
    open: true,
    overlay: {
      warnings: false,
      errors: true
    },
    proxy: {
      // API Cấu hình proxy
      '/api': {
        target: 'http://localhost:8080',
        changeOrigin: true,
        pathRewrite: {
          '^/api': '/api'
        }
      }
    }
  },
  
  // Cấu hình build
  configureWebpack: {
    // Cung cấp biến toàn cục cho webpack
    plugins: [
      // Cấu hình plugin khác
    ],
    // Cấu hình resolve
    resolve: {
      alias: {
        '@': path.resolve(__dirname, 'src'),
        '@components': path.resolve(__dirname, 'src/components'),
        '@views': path.resolve(__dirname, 'src/views'),
        '@api': path.resolve(__dirname, 'src/api'),
        '@utils': path.resolve(__dirname, 'src/utils')
      }
    }
  },
  
  // Cấu hình dạng chain
  chainWebpack: config => {
    // Cấu hình alias
    config.resolve.alias
      .set('@', path.resolve(__dirname, 'src'))
    
    // Cấu hình tối ưu build
    if (process.env.NODE_ENV === 'production') {
      // Cấu hình môi trường production
      config.optimization.minimizer('terser').tap(args => {
        // Cấu hình tùy chọn terser
        return args
      })
    }
  }
})
```

## 3 Cấu hình route

### 3.1 File cấu hình route

File cấu hình route nằm trong thư mục `src/router/`:

```
src/router/
├── index.js          # File cấu hình route chính
└── modules/          # Cấu hình route tổ chức theo module
    ├── user.js       # Route module người dùng
    ├── goods.js      # Route module sản phẩm
    └── order.js      # Route module đơn hàng
```

### 3.2 Ví dụ cấu hình route

Cấu hình route chính trong `src/router/index.js`:

```javascript
import Vue from 'vue'
import Router from 'vue-router'
import Layout from '@/layout'

Vue.use(Router)

// Route tĩnh
export const constantRoutes = [
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },
  {
    path: '/404',
    component: () => import('@/views/404'),
    hidden: true
  },
  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [{
      path: 'dashboard',
      name: 'Dashboard',
      component: () => import('@/views/dashboard/index'),
      meta: { title: 'Bảng điều khiển', icon: 'dashboard', affix: true }
    }]
  }
]

// Route động
export const asyncRoutes = [
  // Quản lý người dùng
  {
    path: '/user',
    component: Layout,
    redirect: '/user/list',
    name: 'User',
    meta: { title: 'Quản lý người dùng', icon: 'user', roles: ['admin'] },
    children: [
      {
        path: 'list',
        name: 'UserList',
        component: () => import('@/views/user/list'),
        meta: { title: 'Danh sách người dùng', roles: ['admin'] }
      },
      {
        path: 'add',
        name: 'UserAdd',
        component: () => import('@/views/user/add'),
        meta: { title: 'Thêm người dùng', roles: ['admin'] }
      },
      {
        path: 'edit/:id',
        name: 'UserEdit',
        component: () => import('@/views/user/edit'),
        meta: { title: 'Sửa người dùng', roles: ['admin'] },
        hidden: true
      }
    ]
  },
  // 404 page phải đặt ở cuối cùng
  { path: '*', redirect: '/404', hidden: true }
]

const createRouter = () => new Router({
  mode: 'history',
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRoutes
})

const router = createRouter()

export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher
}

export default router
```

## 4 Cấu hình menu

### 4.1 File cấu hình menu

File cấu hình menu nằm tại `src/config/menu.config.js`:

```javascript
export default [
  {
    path: '/dashboard',
    title: 'Bảng điều khiển',
    icon: 'dashboard',
    component: 'dashboard/index',
    meta: {
      roles: ['admin', 'editor']
    }
  },
  {
    path: '/user',
    title: 'Quản lý người dùng',
    icon: 'user',
    component: 'layout',
    redirect: '/user/list',
    meta: {
      roles: ['admin']
    },
    children: [
      {
        path: 'list',
        title: 'Danh sách người dùng',
        component: 'user/list',
        meta: {
          roles: ['admin']
        }
      },
      {
        path: 'add',
        title: 'Thêm người dùng',
        component: 'user/add',
        meta: {
          roles: ['admin']
        }
      }
    ]
  },
  {
    path: '/goods',
    title: 'Quản lý sản phẩm',
    icon: 'shopping',
    component: 'layout',
    redirect: '/goods/list',
    meta: {
      roles: ['admin', 'editor']
    },
    children: [
      {
        path: 'list',
        title: 'Danh sách sản phẩm',
        component: 'goods/list',
        meta: {
          roles: ['admin', 'editor']
        }
      },
      {
        path: 'category',
        title: 'Danh mục sản phẩm',
        component: 'goods/category',
        meta: {
          roles: ['admin']
        }
      }
    ]
  }
]
```

## 5 Cấu hình chủ đề (theme)

### 5.1 File cấu hình chủ đề

File cấu hình chủ đề nằm tại `src/config/theme.config.js`:

```javascript
export default {
  // Màu chủ đề
  primaryColor: '#409EFF',
  
  // Màu thành công
  successColor: '#67C23A',
  
  // Màu cảnh báo
  warningColor: '#E6A23C',
  
  // Màu lỗi
  errorColor: '#F56C6C',
  
  // Màu thông tin
  infoColor: '#909399',
  
  // Chủ đề menu
  menuTheme: 'dark', // dark, light
  
  // Chủ đề thanh điều hướng trên cùng
  navbarTheme: 'light', // dark, light
  
  // Chế độ bố cục
  layoutMode: 'side', // side, top
  
  // Có cố định thanh điều hướng trên cùng không
  fixedNavbar: true,
  
  // Có cố định thanh bên không
  fixedSidebar: true,
  
  // Có hiện thanh tab không
  showTagsView: true,
  
  // Có hiện logo không
  showLogo: true,
  
  // Có hiện breadcrumb không
  showBreadcrumb: true,
  
  // Có bật bố cục responsive không
  responsiveLayout: true
}
```

## 6 Cấu hình API

### 6.1 Cấu hình cơ bản của API

Cấu hình các thiết lập cơ bản cho request API trong `src/utils/request.js`:

```javascript
import axios from 'axios'

const service = axios.create({
  baseURL: process.env.VUE_APP_BASE_API,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json;charset=utf-8'
  }
})

// Cấu hình khác...

export default service
```

### 6.2 Cấu hình module API

Cấu hình API theo từng module trong thư mục `src/api/`:

```javascript
// src/api/user.js
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
  }
}
```

## 7 Cấu hình quyền

### 7.1 File cấu hình quyền

File cấu hình quyền nằm tại `src/config/permission.config.js`:

```javascript
export default {
  // Cấu hình quyền route
  routePermissions: {
    '/user': ['admin'],
    '/goods': ['admin', 'editor'],
    '/order': ['admin', 'editor'],
    '/finance': ['admin'],
    '/system': ['admin']
  },
  
  // Cấu hình quyền nút
  buttonPermissions: {
    'user:add': ['admin'],
    'user:edit': ['admin'],
    'user:delete': ['admin'],
    'goods:add': ['admin', 'editor'],
    'goods:edit': ['admin', 'editor'],
    'goods:delete': ['admin'],
    'order:edit': ['admin', 'editor'],
    'order:delete': ['admin']
  },
  
  // Cấu hình vai trò
  roles: {
    admin: {
      name: 'Quản trị viên',
      permissions: ['user:add', 'user:edit', 'user:delete', 'goods:add', 'goods:edit', 'goods:delete', 'order:edit', 'order:delete']
    },
    editor: {
      name: 'Sửa',
      permissions: ['goods:add', 'goods:edit', 'order:edit']
    }
  }
}
```

### 7.2 Directive phân quyền

Cấu hình directive phân quyền trong `src/directive/permission.js`:

```javascript
import permission from '@/config/permission.config'

export default {
  inserted(el, binding) {
    const { value } = binding
    const userRoles = JSON.parse(localStorage.getItem('userRoles') || '[]')
    
    if (value && value instanceof Array && value.length > 0) {
      const hasPermission = value.some(permission => {
        return userRoles.includes(permission)
      })
      
      if (!hasPermission) {
        el.parentNode && el.parentNode.removeChild(el)
      }
    } else {
      throw new Error('Directive phân quyền phải chỉ định giá trị quyền')
    }
  }
}
```

## 8 Cấu hình đa ngôn ngữ (i18n)

### 8.1 File cấu hình đa ngôn ngữ

File cấu hình đa ngôn ngữ nằm trong thư mục `src/lang/`:

```
src/lang/
├── index.js          # File điểm vào đa ngôn ngữ (i18n)
├── zh-CN.js          # Gói ngôn ngữ tiếng Trung
└── en-US.js          # Gói ngôn ngữ tiếng Anh
```

Cấu hình gói ngôn ngữ tiếng Trung trong `src/lang/zh-CN.js`:

```javascript
export default {
  login: {
    title: 'Đăng nhập',
    username: 'Tên người dùng',
    password: 'Mật khẩu',
    loginBtn: 'Đăng nhập',
    forgetPassword: 'Quên mật khẩu',
    register: 'Đăng ký'
  },
  dashboard: {
    title: 'Bảng điều khiển',
    welcome: 'Chào mừng quay lại',
    todayStats: 'Thống kê hôm nay',
    totalStats: 'Thống kê tổng'
  },
  user: {
    title: 'Quản lý người dùng',
    list: 'Danh sách người dùng',
    add: 'Thêm người dùng',
    edit: 'Sửa người dùng',
    delete: 'Xóa người dùng',
    username: 'Tên người dùng',
    nickname: 'Biệt danh',
    email: 'Email',
    phone: 'Số điện thoại',
    status: 'Trạng thái'
  }
}
```

## 9 Cấu hình build

### 9.1 Cấu hình script build

Cấu hình script build trong `package.json`:

```json
{
  "scripts": {
    "build": "vue-cli-service build",
    "build:dev": "vue-cli-service build --mode development",
    "build:prod": "vue-cli-service build --mode production",
    "build:staging": "vue-cli-service build --mode staging",
    "build:analyze": "vue-cli-service build --report"
  }
}
```

### 9.2 Cấu hình tối ưu build

Cấu hình tối ưu build trong `vue.config.js`:

```javascript
module.exports = {
  configureWebpack: {
    optimization: {
      // Tách các chunk code
      splitChunks: {
        chunks: 'all',
        cacheGroups: {
          // Thư viện bên thứ ba
          vendor: {
            name: 'chunk-vendors',
            test: /[\\/]node_modules[\\/]/,
            priority: 10,
            chunks: 'initial'
          },
          // Thành phần (component) dùng chung
          common: {
            name: 'chunk-common',
            minChunks: 2,
            priority: 5,
            chunks: 'initial',
            reuseExistingChunk: true
          }
        }
      }
    }
  }
}
```

## 10 Cấu hình phát triển

### 10.1 Cấu hình máy chủ phát triển (dev server)

Cấu hình máy chủ phát triển trong `vue.config.js`:

```javascript
module.exports = {
  devServer: {
    // Cổng (port)
    port: 9527,
    
    // Tự động mở trình duyệt
    open: true,
    
    // Hiển thị lỗi và cảnh báo
    overlay: {
      warnings: false,
      errors: true
    },
    
    // Cấu hình proxy
    proxy: {
      '/api': {
        target: 'http://localhost:8080',
        changeOrigin: true,
        pathRewrite: {
          '^/api': '/api'
        }
      }
    },
    
    // Cập nhật nóng (hot reload)
    hot: true,
    
    // Thư mục tài nguyên tĩnh
    static: {
      directory: path.join(__dirname, 'public')
    }
  }
}
```

### 10.2 Cấu hình ESLint

File cấu hình ESLint nằm tại `template/admin-element/.eslintrc.js`:

```javascript
module.exports = {
  root: true,
  env: {
    node: true
  },
  extends: [
    'plugin:vue/essential',
    '@vue/standard'
  ],
  parserOptions: {
    parser: 'babel-eslint'
  },
  rules: {
    'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'indent': ['error', 2],
    'linebreak-style': ['error', 'unix'],
    'quotes': ['error', 'single'],
    'semi': ['error', 'never']
  }
}
```

## 11 Cấu hình triển khai

### 11.1 File cấu hình triển khai

File cấu hình triển khai nằm tại `src/config/deploy.config.js`:

```javascript
export default {
  // Môi trường triển khai
  environments: {
    // Môi trường kiểm thử
    staging: {
      host: 'staging.example.com',
      port: 22,
      username: 'deploy',
      password: '',
      privateKey: '/path/to/privateKey',
      passphrase: '',
      from: 'dist/',
      to: '/var/www/html/admin-staging',
      timeout: 60000
    },
    // Môi trường production
    production: {
      host: 'production.example.com',
      port: 22,
      username: 'deploy',
      password: '',
      privateKey: '/path/to/privateKey',
      passphrase: '',
      from: 'dist/',
      to: '/var/www/html/admin',
      timeout: 60000
    }
  }
}
```

### 11.2 Cấu hình Nginx

Cấu hình Nginx trên máy chủ:

```nginx
server {
  listen 80;
  server_name admin.example.com;
  
  root /var/www/html/admin;
  index index.html;
  
  location / {
    try_files $uri $uri/ /index.html;
  }
  
  location /api {
    proxy_pass http://localhost:8080;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
  }
  
  # Cache tài nguyên tĩnh
  location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg)$ {
    expires 7d;
    add_header Cache-Control "public, max-age=604800";
  }
  
  # Trang lỗi
  error_page 404 /index.html;
  error_page 500 502 503 504 /50x.html;
  location = /50x.html {
    root /usr/share/nginx/html;
  }
}
```

## 12 Cấu hình hiệu năng

### 12.1 Cấu hình tối ưu hiệu năng

Cấu hình tối ưu hiệu năng trong `vue.config.js`:

```javascript
module.exports = {
  configureWebpack: {
    // Cấu hình hiệu năng
    performance: {
      maxAssetSize: 300000, // 300kb
      maxEntrypointSize: 300000, // 300kb
      hints: 'warning'
    }
  },
  
  // Tối ưu build
  chainWebpack: config => {
    // Cấu hình nén ảnh
    config.module
      .rule('images')
      .use('image-webpack-loader')
      .loader('image-webpack-loader')
      .options({
        mozjpeg: {
          progressive: true,
          quality: 65
        },
        optipng: {
          enabled: false
        },
        pngquant: {
          quality: [0.65, 0.90],
          speed: 4
        },
        gifsicle: {
          interlaced: false
        }
      })
    
    // Cấu hình tách code
    config.optimization
      .splitChunks({
        chunks: 'all',
        cacheGroups: {
          vendor: {
            name: 'vendor',
            test: /[\\/]node_modules[\\/]/,
            priority: 10,
            chunks: 'initial'
          },
          common: {
            name: 'common',
            minChunks: 2,
            priority: 5,
            chunks: 'all',
            reuseExistingChunk: true
          }
        }
      })
  }
}
```

## 13 Cấu hình bảo mật

### 13.1 File cấu hình bảo mật

File cấu hình bảo mật nằm tại `src/config/security.config.js`:

```javascript
export default {
  // Có bật bảo vệ CSRF không
  enableCsrf: true,
  
  // CSRF Token Tên
  csrfTokenName: 'X-CSRF-Token',
  
  // Có bật chống XSS không
  enableXss: true,
  
  // Trạng thái kích hoạt CSP (Content Security Policy)
  enableCsp: true,
  
  // CSP Cấu hình
  csp: {
    defaultSrc: "'self'",
    scriptSrc: "'self' 'unsafe-inline' 'unsafe-eval'",
    styleSrc: "'self' 'unsafe-inline'",
    imgSrc: "'self' data: https:",
    connectSrc: "'self'",
    fontSrc: "'self'",
    objectSrc: "'none'",
    frameSrc: "'none'",
    baseSrc: "'self'",
    formAction: "'self'"
  },
  
  // Trạng thái kích hoạt HTTP Strict Transport Security
  enableHsts: true,
  
  // HSTS Cấu hình
  hsts: {
    maxAge: 31536000,
    includeSubDomains: true,
    preload: true
  },
  
  // Trạng thái kích hoạt X-Content-Type-Options
  enableXContentTypeOptions: true,
  
  // Trạng thái kích hoạt X-Frame-Options
  enableXFrameOptions: true,
  
  // X-Frame-Options Cấu hình
  xFrameOptions: 'DENY', // DENY, SAMEORIGIN, ALLOW-FROM
  
  // Trạng thái kích hoạt X-XSS-Protection
  enableXXssProtection: true
}
```

## 14 Thực tiễn tốt nhất

### 14.1 Thực tiễn tốt nhất về quản lý cấu hình

1. **Quản lý biến môi trường**
   - Dùng file `.env` để quản lý cấu hình của từng môi trường
   - Không hard-code thông tin nhạy cảm trong code
   - Mỗi môi trường dùng một file cấu hình riêng

2. **Tổ chức file cấu hình**
   - Tổ chức file cấu hình theo module chức năng
   - Dùng một cách quản lý cấu hình thống nhất
   - File cấu hình cần có chú thích rõ ràng

3. **Thứ tự nạp cấu hình**
   - Biến môi trường > File cấu hình > Cấu hình mặc định
   - Đảm bảo tính nhất quán khi nạp cấu hình

4. **Kiểm tra tính hợp lệ của cấu hình**
   - Kiểm tra tính hợp lệ của các mục cấu hình
   - Cung cấp giá trị mặc định và xử lý lỗi

5. **Giám sát cấu hình**
   - Giám sát các thay đổi của cấu hình
   - Ghi log thay đổi cấu hình

6. **Bảo mật cấu hình**
   - Mã hóa khi lưu trữ các cấu hình nhạy cảm
   - Kiểm soát quyền truy cập file cấu hình
   - Tránh ghi cấu hình nhạy cảm ra log

7. **Khả năng bảo trì cấu hình**
   - Dùng định dạng cấu hình có cấu trúc
   - Quy tắc đặt tên cho các mục cấu hình
   - Định kỳ dọn dẹp các cấu hình không còn dùng

8. **Khả năng mở rộng cấu hình**
   - Thiết kế cấu trúc cấu hình có khả năng mở rộng
   - Hỗ trợ cập nhật cấu hình động
   - Các mục cấu hình cần có giá trị mặc định hợp lý

## 15 Tổng kết

Cấu hình hệ thống của dự án Admin-Element bao gồm nhiều khía cạnh như biến môi trường, route, menu, chủ đề, API, quyền, đa ngôn ngữ, build, phát triển, triển khai, hiệu năng và bảo mật. Quản lý cấu hình hợp lý giúp nâng cao khả năng bảo trì, khả năng mở rộng và tính bảo mật của dự án, đồng thời cải thiện hiệu suất phát triển và trải nghiệm người dùng.

Lập trình viên nên tuân thủ các thực tiễn tốt nhất về quản lý cấu hình, căn cứ vào yêu cầu của dự án và đặc điểm của từng môi trường để cấu hình hợp lý các tham số, đảm bảo dự án vận hành ổn định và liên tục phát triển.