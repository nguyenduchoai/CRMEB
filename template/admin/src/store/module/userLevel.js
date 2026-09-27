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
 * Cấu hình bố cục
 * */
import screenfull from 'screenfull';
// import router from '@/router';
// import Setting from '@/setting';

export default {
  namespaced: true,
  state: {
    taskId: 0,
    levelId: 0,
    categoryId: 0, // ID danh mục bài viết
  },
  mutations: {
    /**
     * @description Đặt loại thiết bị
     * @param {Object} state vuex state
     * @param {String} type Loại thiết bị, giá trị có thể chọn là Mobile, Tablet, Desktop
     */

    /**
     * @description Id nhiệm vụ thành viên
     */
    getTaskId(state, taskId) {
      state.taskId = taskId;
    },

    /**
     * @description ID hạng thành viên
     */
    getlevelId(state, levelId) {
      state.levelId = levelId;
    },

    /**
     * @description ID danh mục bài viết
     */
    getCategoryId(state, categoryId) {
      state.categoryId = categoryId;
    },
  },
  actions: {
    /**
     * @description Khởi tạo lắng nghe trạng thái toàn màn hình
     */
    listenFullscreen({ commit }) {
      return new Promise((resolve) => {
        if (screenfull.enabled) {
          screenfull.on('change', () => {
            if (!screenfull.isFullscreen) {
              commit('setFullscreen', false);
            }
          });
        }
        // end
        resolve();
      });
    },
    /**
     * @description Chuyển đổi toàn màn hình
     */
    toggleFullscreen({ commit }) {
      return new Promise((resolve) => {
        if (screenfull.isFullscreen) {
          screenfull.exit();
          commit('setFullscreen', false);
        } else {
          screenfull.request();
          commit('setFullscreen', true);
        }
        // end
        resolve();
      });
    },
  },
};
