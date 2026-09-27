// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

/**
 * Cấu hình menu layout
 * */
import { menusApi } from '@/api/account';
function getMenusName() {
  let storage = window.localStorage;
  let menuList = JSON.parse(storage.getItem('menuList'));
  if (typeof menuList !== 'object' || menuList === null) {
    menuList = [];
  }
  return menuList;
}
export default {
  namespaced: true,
  state: {
    menusName: getMenusName(),
    openMenus: [],
    childMenuList: [],
    oneLvMenus: [],
    oneLvRoutes: [],
  },
  mutations: {
    getmenusNav(state, menuList) {
      state.menusName = menuList;
    },
    // getopenMenus (state, openList) {
    //   state.openMenus = openList
    // }
    setopenMenus(state, openList) {
      state.openMenus = openList;
    },
    setOneLvMenus(state, oneLvMenus) {
      state.oneLvMenus = oneLvMenus;
    },
    setOneLvRoute(state, oneLvMenus) {
      state.oneLvRoutes = oneLvMenus;
    },
    childMenuList(state, list) {
      state.childMenuList = list;
    },
  },
  actions: {
    getMenusNavList({ commit }) {
      return new Promise((resolve, reject) => {
        menusApi()
          .then(async (res) => {
            resolve(res);
            commit('getmenusNav', res.data.menus);
          })
          .catch((res) => {
            reject(res);
          });
      });
    },
  },
};
