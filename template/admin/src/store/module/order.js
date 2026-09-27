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
import { getOrdes } from '@/api/order';
// function today () {
//     const end = new Date();
//     const start = new Date();
//     var datetimeStart = start.getFullYear() + '/' + (start.getMonth() + 1) + '/' + start.getDate();
//     var datetimeEnd = end.getFullYear() + '/' + (end.getMonth() + 1) + '/' + end.getDate();
//     return [datetimeStart, datetimeEnd];
// }
export default {
  namespaced: true,
  state: {
    orderStatus: '', // Trạng thái đơn hàng
    // orderTime: today().join('-'), // Thời gian đặt hàng
    orderTime: '',
    orderNum: '',
    orderType: 0, // Trạng thái đơn hàng
    fieldKey: '',
    orderChartType: {},
    isDels: false,
    delIdList: [],
    iconsaaaa: '',
    orderPayType: '',
    real_name: '',
    // modelLists: function
  },
  mutations: {
    /**
     * @description Đặt loại thiết bị
     * @param {Object} state vuex state
     * @param {String} type Loại thiết bị, giá trị có thể chọn là Mobile, Tablet, Desktop
     */

    /**
     * @description Tìm kiếm theo trạng thái đơn hàng
     */
    getOrderStatus(state, orderStatus) {
      state.orderStatus = orderStatus;
    },

    /**
     * @description Tìm kiếm theo trạng thái đơn hàng
     */
    getOrderType(state, orderPayType) {
      state.orderPayType = orderPayType;
    },

    /**
     * @description Trạng thái thời gian
     */
    getOrderTime(state, orderTime) {
      state.orderTime = orderTime;
    },

    /**
     * @description Trạng thái chọn mã đơn hàng
     */
    getOrderNum(state, orderNum) {
      state.orderNum = orderNum;
    },

    getfieldKey(state, fieldKey) {
      state.fieldKey = fieldKey;
    },
    /**
     * @description Từ khóa tìm kiếm
     * */
    setOrderKeyword(state, real_name) {
      state.real_name = real_name;
    },
    /**
     * @description Chuyển tab, chọn trạng thái đơn hàng
     */
    onChangeTabs(state, orderType) {
      state.orderType = orderType;
    },

    /**
     * @description  Trạng thái đơn hàng, đối tượng tất cả
     */
    onChangeChart(state, orderChartType) {
      state.orderChartType = orderChartType;
    },

    /**
     * @description  Có thể xóa đơn hàng theo lô hay không
     */
    getIsDel(state, isDels) {
      state.isDels = isDels;
    },

    /**
     * @description  Tập hợp id đơn hàng xóa theo lô
     */
    getisDelIdListl(state, delIdList) {
      state.delIdList = delIdList;
    },
    resetSearch(state) {
      state.orderType = '';
      state.orderTime = '';
      state.orderNum = '';
      state.orderPayType = '';
      state.real_name = '';
    },
  },
  actions: {
    /**
     * @description Trạng thái đơn hàng
     */
    getOrderTabs({ commit }, data) {
      return new Promise((resolve, reject) => {
        getOrdes(data)
          .then(async (res) => {
            resolve(res);
            commit('onChangeChart', res.data);
          })
          .catch((res) => {
            reject(res);
          });
      });
    },
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
