// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import { AccountLogoutKefu } from '@/api/kefu';
import { getCookies, removeCookies, setCookies } from '@/libs/util';
import router from '@/router';
import { Socket } from '@/libs/socket';
export default {
  namespaced: true,
  state: {
    kefuInfo: null,
  },
  mutations: {
    setInfo(state, val) {
      state.kefuInfo = val;
    },
  },
  actions: {
    /**
     * @description Đăng xuất
     * */
    logoutKefu({ commit, dispatch }, { confirm = false, vm } = {}) {
      async function logout() {
        AccountLogoutKefu()
          .then(() => {
            Socket.then((ws) => {
              ws.send({
                type: 'logout',
                data: { uid: getCookies('kefu_uuid') },
              });
            });
            // localStorage.clear();
            removeCookies('kefu_token');
            removeCookies('kefu_expires_time');
            removeCookies('kefuInfo');
            removeCookies('kefu_uuid');
            // Xóa localStorage
            // Xóa thông tin người dùng trong vuex
            // Chuyển route
            router.push({
              path: '/kefu',
            });
          })
          .catch((res) => {
            console.log(res);
          });
      }
      logout();
    },
  },
};
