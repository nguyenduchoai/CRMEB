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
import Vuex from 'vuex';
import VuexPersistence from 'vuex-persist';

import user from './module/user';
import app from './module/app';
import menus from './module/menus';
import menu from './module/menu';
import userInfo from './module/userInfo';
import userLevel from './module/userLevel';
import order from './module/order';
import media from './module/media';
import goodSelect from './module/goodSelect';
import moren from './module/moren';
import shopping from './module/shopping';
import fresh from './module/fresh';
import kefu from './module/kefu';
import integralOrder from './module/integralOrder';
import mobildConfig from './module/mobildConfig';
import upgrade from './module/upgrade';
import layout from './module/layout';
import themeConfig from './module/themeConfig';
import routesList from './module/routesList';
import tagsViewRoutes from './module/tagsViewRoutes';
import userInfos from './module/userInfos';
import keepAliveNames from './module/keepAliveNames';

Vue.use(Vuex);
// Lưu trữ bền vững
// const vuexLocal = new VuexPersistence({
//     storage: window.localStorage,
//
// })

export default new Vuex.Store({
  state: {
    //
  },
  mutations: {
    //
  },
  actions: {
    //
  },
  plugins: [
    new VuexPersistence({
      reducer: (state) => ({
        user: state.user, //Đây chính là giá trị lưu vào localStorage
        app: state.app,
        menus: state.menus,
        menu: state.menu,
        userInfo: state.userInfo,
        userLevel: state.userLevel,
        order: state.order,
        media: state.media,
        kefu: state.kefu,
        integralOrder: state.integralOrder,
        mobildConfig: state.mobildConfig,
        upgrade: state.upgrade,
        layout: state.layout,
        themeConfig: state.themeConfig,
        routesList: state.routesList,
        keepAliveNames: state.keepAliveNames,
      }),
      storage: window.localStorage,
    }).plugin,
  ],
  modules: {
    user,
    app,
    menus,
    menu,
    userInfo,
    userLevel,
    order,
    media,
    goodSelect,
    moren,
    shopping,
    fresh,
    kefu,
    mobildConfig,
    integralOrder,
    upgrade,
    layout,
    themeConfig,
    routesList,
    tagsViewRoutes,
    userInfos,
    keepAliveNames,
  },
});
