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

export default {
  namespaced: true,
  state: {
    isMobile: false, // Có phải điện thoại hay không
    isTablet: false, // Có phải máy tính bảng hay không
    isDesktop: true, // Có phải máy tính để bàn hay không
    isFullscreen: false, // Có chuyển sang toàn màn hình hay không
  },
  mutations: {
    /**
     * @description Đặt loại thiết bị
     * @param {Object} state vuex state
     * @param {String} type Loại thiết bị, giá trị có thể chọn là Mobile, Tablet, Desktop
     */
    setDevice(state, type) {
      state.isMobile = false;
      state.isTablet = false;
      state.isDesktop = false;
      state[`is${type}`] = true;
    },
  },
  actions: {},
};
