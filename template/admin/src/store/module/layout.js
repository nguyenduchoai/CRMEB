export default {
  namespaced: true,
  state: {
    menuCollapse: false,
  },
  getters: {},
  mutations: {
    /**
     * @description Đặt mở/đóng thanh bên
     * @param {Object} state vuex state
     * @param {Array} status status
     */
    changeCol(state, status) {
      state.menuCollapse = status;
    },
  },
};
