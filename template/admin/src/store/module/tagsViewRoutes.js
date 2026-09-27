const tagsViewRoutesModule = {
  namespaced: true,
  state: {
    tagsViewRoutes: [],
  },
  mutations: {
    // Đặt route cho TagsView
    getTagsViewRoutes(state, data) {
      state.tagsViewRoutes = data;
    },
  },
  actions: {
    // Đặt route cho TagsView
    async setTagsViewRoutes({ commit }, data) {
      commit('getTagsViewRoutes', data);
    },
  },
};

export default tagsViewRoutesModule;
