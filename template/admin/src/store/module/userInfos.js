import { Session } from '@/utils/storage.js';

const userInfosModule = {
  namespaced: true,
  state: {
    userInfos: {},
  },
  mutations: {
    // Đặt thông tin người dùng
    getUserInfos(state, data) {
      state.userInfos = data;
    },
  },
  actions: {
    // Đặt thông tin người dùng
    async setUserInfos({ commit }, data) {
      if (data) {
        commit('getUserInfos', data);
      } else {
        if (Session.get('userInfo')) commit('getUserInfos', Session.get('userInfo'));
      }
    },
  },
};

export default userInfosModule;
