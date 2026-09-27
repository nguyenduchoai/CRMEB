const routesListModule = {
  namespaced: true,
  state: {
    routesList: [],
  },
  mutations: {
    // Đặt route, dùng trong menu
    getRoutesList(state, data) {
      state.routesList = data;
    },
  },
  actions: {
    // Đặt route, dùng trong menu
    async setRoutesList({ commit }, data) {
      commit('getRoutesList', data);
    },
  },
};

export default routesListModule;
