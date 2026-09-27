const keepAliveNamesModule = {
  namespaced: true,
  state: {
    keepAliveNames: [],
  },
  mutations: {
    // Đặt cache route (trường name)
    getCacheKeepAlive(state, data) {
      state.keepAliveNames = data;
    },
  },
  actions: {
    // Đặt cache route (trường name)
    async setCacheKeepAlive({ commit }, data) {
      commit('getCacheKeepAlive', data);
    },
  },
};

export default keepAliveNamesModule;
