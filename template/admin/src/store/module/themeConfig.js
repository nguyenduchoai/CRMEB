/**
 * Khi sửa cấu hình, mỗi lần đều cần xóa cache vĩnh viễn `window.localStorage` của trình duyệt thì cấu hình mới có hiệu lực
 */
const themeConfigModule = {
  namespaced: true,
  state: {
    themeConfig: {
      // Có mở drawer cấu hình layout hay không
      isDrawer: false,

      /**
       * Chủ đề chung
       */
      // Màu chủ đề primary mặc định
      primary: '#409eff',
      // Màu nền menu
      menuBgColor: '#282c34',
      // Có bật chế độ tối hay không
      isIsDark: false,
      themeStyle: 'theme-1',
      /**
       * Menu / Thanh trên cùng
       * Xin lưu ý:
       * Cần đồng thời sửa giá trị tương ứng trong `/@/theme/common/var.scss`,
       */
      // Màu nền điều hướng trên cùng mặc định
      topBar: '#ffffff',
      // Màu chữ điều hướng trên cùng mặc định
      topBarColor: '#606266',
      // Màu nền điều hướng menu mặc định
      menuBar: '#282c34',
      // Màu chữ điều hướng menu mặc định
      menuBarColor: '#eaeaea',
      // Màu nền menu phân cột mặc định
      columnsMenuBar: '#282c34',
      // Màu chữ menu phân cột mặc định
      columnsMenuBarColor: '#e6e6e6',

      /**
       * Cài đặt giao diện
       */
      // Có bật hiệu ứng thu gọn menu theo chiều ngang hay không
      isCollapse: false,
      // Có bật hiệu ứng accordion cho menu hay không
      isUniqueOpened: true,
      // Có bật cố định Header hay không
      isFixedHeader: true,

      /**
       * Hiển thị giao diện
       */
      // Có bật Logo thanh bên hay không
      isShowLogo: true,
      // Có bật Breadcrumb hay không
      isBreadcrumb: true,
      // Có bật icon Breadcrumb hay không
      isBreadcrumbIcon: false,
      // Có bật Tagsview hay không
      isTagsview: true,
      // Có bật icon Tagsview hay không
      isTagsviewIcon: false,
      // Có bật cache TagsView hay không
      isCacheTagsView: false,
      // Có bật thông tin bản quyền ở Footer hay không
      isFooter: true,
      // Có bật chế độ xám hay không
      isGrayscale: false,
      // Có bật chế độ dành cho người mù màu hay không
      isInvert: false,
      /**
       * Cài đặt khác
       */
      // Kiểu Tagsview mặc định, có thể chọn 1. tags-style-one, tự mở rộng thêm:
      // 1. Cần sửa `getThemeConfig.tagsStyle` el-option trong @/layout/navBars/breadcrumb/setings.vue
      // 2. Cần sửa style css ở phần chú thích cuối code trong @/layout/navBars/tagsView/tagsView.vue
      tagsStyle: 'tags-style-five',
      // Hiệu ứng chuyển trang chính: giá trị có thể chọn "<slide-right|slide-left|opacitys>", mặc định slide-right
      animation: 'opacitys',
      // Kiểu highlight phân cột: giá trị có thể chọn "<columns-round|columns-card>", mặc định columns-round
      columnsAsideStyle: 'columns-card',
      // Kiểu bố cục phân cột: giá trị có thể chọn "<columns-horizontal|columns-vertical>", mặc định columns-horizontal
      columnsAsideLayout: 'columns-vertical',

      /**
       * Chuyển bố cục
       * Lưu ý: để minh họa, khi chuyển layout thì màu sẽ được đặt lại về mặc định, vị trí code: /@/layout/navBars/breadcrumb/setings.vue
       * trong phương thức `initSetLayoutChange(đặt chuyển layout, reset style theme)`
       */
      // Chuyển đổi layout: giá trị có thể chọn "<defaults|classic|transverse|columns>", mặc định defaults
      layout: 'columns',

      /**
       * Tiêu đề / phụ đề website toàn cục
       */
      // Tiêu đề chính của website (điều hướng menu, tiêu đề trang hiện tại trên trình duyệt)
      globalTitle: 'crmeb-admin',
      // Phụ đề website (chữ ở đầu trang đăng nhập)
      globalViceTitle: '',
      // Mô tả website (chữ ở đầu trang đăng nhập)
      globalViceDes: 'vue2',
      // Ngôn ngữ khởi tạo mặc định, giá trị có thể chọn "<zh-cn|en|zh-tw>", mặc định zh-cn
      globalI18n: 'vi',
      // Kích thước component toàn cục mặc định, giá trị có thể chọn "<|medium|small|mini>", mặc định ''
      globalComponentSize: '',
    },
  },
  mutations: {
    // Đặt cấu hình layout
    getThemeConfig(state, data) {
      state.themeConfig = data;
    },
  },
  actions: {
    // Đặt cấu hình layout
    setThemeConfig({ commit }, data) {
      commit('getThemeConfig', data);
    },
  },
};

export default themeConfigModule;
