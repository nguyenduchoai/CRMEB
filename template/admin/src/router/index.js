// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import Vue from 'vue';
import Router from 'vue-router';
import routes from './routers';
import Setting from '@/setting';
import store from '@/store';
import { removeCookies, getCookies, setTitle } from '@/libs/util';
import { includeArray } from '@/libs/auth';
import { PrevLoading } from '@/utils/loading.js';

Vue.use(Router);
// Xử lý lỗi báo khi click lại menu trên navbar của `element ui`
const originalPush = Router.prototype.push;
Router.prototype.push = function push(location) {
  return originalPush.call(this, location).catch((err) => err);
};

const router = new Router({
  routes,
  mode: Setting.routerMode,
});

// Kiểm tra meta.roles của route có chứa trường quyền của người dùng đang đăng nhập hay không
export function hasAuth(roles, route) {
  if (route.meta && route.meta.auth) return roles.some((role) => route.meta.auth.includes(role));
  else return true;
}

// Lọc đệ quy các route có quyền
export function setFilterMenuFun(routes, role) {
  const menu = [];
  routes.forEach((route) => {
    const item = { ...route };
    if (hasAuth(role, item)) {
      if (item.children) item.children = setFilterMenuFun(item.children, role);
      menu.push(item);
    }
  });
  return menu;
}

// Xử lý đệ quy layout dư thừa: <router-view>, giữ component cần truy cập ở tầng layout đầu tiên.
// Vì `keep-alive` chỉ cache được route cấp 2
// Mặc định thực thi ngay khi khởi tạo
export function keepAliveSplice(to) {
  if (to.matched && to.matched.length > 2) {
    to.matched.map((v, k) => {
      if (v.components.default instanceof Function) {
        v.components.default().then((components) => {
          if (components.default.name === 'parent') {
            to.matched.splice(k, 1);
            router.push({ path: to.path, query: to.query });
            keepAliveSplice(to);
          }
        });
      } else {
        if (v.components.default.name === 'parent') {
          to.matched.splice(k, 1);
          keepAliveSplice(to);
        }
      }
    });
  }
}

// Sửa module
export function editRouterFun(to, from) {
  const onRoutes = to.meta.activeMenu ? to.meta.activeMenu : to.meta.path;
  store.commit('menu/setActivePath', onRoutes);
  if (to.name == 'crud_crud') {
    store.state.menus.oneLvRoutes.map((e) => {
      if (e.path === to.path) {
        to.meta.title = e.title;
      }
    });
  }
  if (
    [
      'product_productAdd',
      'marketing_bargainCreate',
      'marketing_storeSeckillCreate',
      'marketing_storeIntegralCreate',
      'marketing_storeCouponCreate',
    ].includes(to.name)
  ) {
    let route = to.matched[1].path.split(':')[0];
    store.state.menus.oneLvRoutes.map((e) => {
      if (route.indexOf(e.path) != -1) {
        to.meta.title = `${to.params.id ? e.title + 'ID: ' + to.params.id : 'Thêm' + e.title}`;
      }
    });
  }
}

// Trì hoãn tắt thanh tiến trình
export function delayNProgressDone(time = 300) {
  setTimeout(() => {
    NProgress.done();
  }, time);
}

/**
 * Chặn route
 * Xác thực quyền
 */

router.beforeEach(async (to, from, next) => {
  // PrevLoading.start();
  keepAliveSplice(to);
  editRouterFun(to, from);
  if (to.fullPath.indexOf('kefu') != -1 || to.name == 'mobile_upload') {
    return next();
  }
  // Kiểm tra có cần đăng nhập mới được vào hay không
  if (to.matched.some((_) => _.meta.auth)) {
    // Ở đây dựa vào token để kiểm tra đã đăng nhập hay chưa, có thể sửa theo tình huống thực tế
    const token = getCookies('token');
    if (token && token !== 'undefined') {
      const access = store.state.userInfo.uniqueAuth;
      const isPermission = includeArray(to.meta.auth, access); //  Kiểm tra có quyền hay không  TODO
      if (access.length) {
        next();
      } else {
        if (access.length == 0) {
          next({
            name: 'login',
            query: {
              redirect: to.fullPath,
            },
          });
          localStorage.clear();
          removeCookies('token');
          removeCookies('expires_time');
          removeCookies('uuid');
        } else {
          next({
            name: '403',
          });
        }
      }
      // next();
    } else {
      // Khi chưa đăng nhập thì chuyển đến trang đăng nhập
      // Kèm theo đường dẫn đầy đủ của trang cần chuyển đến sau khi đăng nhập thành công
      next({
        name: 'login',
        query: {
          redirect: to.fullPath,
        },
      });
      localStorage.clear();
      removeCookies('token');
      removeCookies('expires_time');
      removeCookies('uuid');
    }
  } else {
    // Không cần xác thực danh tính, cho qua luôn
    next();
  }
});
router.afterEach((to) => {
  // Đổi tiêu đề
  setTitle(to, router.app);
  // Về đầu trang
  window.scrollTo(0, 0);
  PrevLoading.done();
});
export default router;
