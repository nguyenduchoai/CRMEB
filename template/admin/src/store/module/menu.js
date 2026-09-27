// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
/**
 * Menu
 * */
import { cloneDeep } from 'lodash';
import { includeArray } from '@/libs/system';

// Lọc menu theo quyền được cấu hình trong menu
function filterMenu(menuList, access, lastList) {
  menuList.forEach((menu) => {
    let menuAccess = menu.auth;

    if (!menuAccess || includeArray(menuAccess, access)) {
      let newMenu = {};
      for (let i in menu) {
        if (i !== 'children') newMenu[i] = cloneDeep(menu[i]);
      }
      if (menu.children && menu.children.length) newMenu.children = [];

      lastList.push(newMenu);
      menu.children && filterMenu(menu.children, access, newMenu.children);
    }
  });
  return lastList;
}
// Xử lý đệ quy vấn đề menu trên cùng
function getChilden(data) {
  if (data.children) {
    return getChilden(data.children[0]);
  }
  return data.path;
}

export default {
  namespaced: true,
  state: {
    // Menu trên cùng
    header: [],
    // Tên menu cấp 1
    oneMenuName: '',
    // Menu thanh bên
    sider: [],
    // Name của menu thanh trên cùng hiện tại
    headerName: '',
    // Path của menu hiện tại
    activePath: '',
    // Tập hợp name của menu con đang mở
    openNames: [],
  },
  getters: {
    /**
     * @description Lọc xác thực menu thanh bên theo quyền người dùng đăng nhập trong user
     * */
    filterSider(state, getters, rootState) {
      const userInfo = rootState.user.info;
      // @Quyền
      const access = userInfo.access;
      if (access && access.length) {
        return filterMenu(state.sider, access, []);
      } else {
        return filterMenu(state.sider, [], []);
      }
    },
    // Xử lý đệ quy route trên cùng

    /**
     * @description Lọc xác thực menu trên cùng theo quyền người dùng đăng nhập trong user
     * */
    filterHeader(state, getters, rootState) {
      //  Gọi hàm đệ quy
      state.header.forEach((item) => {
        item.path = getChilden(item);
      });

      // @Quyền
      const userInfo = rootState.admin.user.info;
      const access = userInfo.access;
      if (access && access.length) {
        return state.header.filter((item) => {
          let state = true;
          if (item.auth && !includeArray(item.auth, access)) state = false;
          return state;
        });
      } else {
        return state.header.filter((item) => {
          let state = true;
          if (item.auth && item.auth.length) state = false;
          return state;
        });
      }
    },
    /**
     * @description Toàn bộ thông tin của header hiện tại
     * */
    currentHeader(state) {
      return state.header.find((item) => item.name === state.headerName);
    },
    /**
     * @description Ở header hiện tại, có ẩn sider (và nút thu gọn) hay không
     * */
    hideSider(state, getters) {
      let visible = false;
      if (getters.currentHeader && 'hideSider' in getters.currentHeader) visible = getters.currentHeader.hideSider;
      return visible;
    },
  },
  mutations: {
    /**
     * @description Đặt menu thanh bên
     * @param {Object} state vuex state
     * @param {Array} menu menu
     */
    setSider(state, menu) {
      state.sider = menu;
    },
    /**
     * @description Đặt menu thanh bên
     * @param {Object} state vuex state
     * @param {Array} menu menu
     */
    setOpenMenuName(state, menu) {
      state.oneMenuName = menu;
    },
    /**
     * @description Đặt menu trên cùng
     * @param {Object} state vuex state
     * @param {Array} menu menu
     */
    setHeader(state, menu) {
      state.header = menu;
    },
    /**
     * @description Đặt name menu trên cùng hiện tại
     * @param {Object} state vuex state
     * @param {Array} name headerName
     */
    setHeaderName(state, name) {
      state.headerName = name;
    },
    /**
     * @description Đặt path của menu hiện tại, dùng để làm nổi bật mục hiện tại trong menu thanh bên
     * @param {Object} state vuex state
     * @param {Array} path fullPath
     */
    setActivePath(state, path) {
      state.activePath = path;
    },
    /**
     * @description Đặt tập hợp names của toàn bộ menu cha đang mở của menu hiện tại
     * @param {Object} state vuex state
     * @param {Array} names openNames
     */
    setOpenNames(state, names) {
      state.openNames = names;
    },
  },
};
