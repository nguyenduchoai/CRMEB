// +---------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +---------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +---------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

export default {
  s: `1`,
  /**
   * @description Cấu hình title hiển thị trên tab trình duyệt
   */
  title: '',
  /**
   * @description Số ngày lưu token trong Cookie, mặc định 1 ngày
   */
  cookieExpires: 1,
  /**
   * @description Có dùng đa ngôn ngữ (i18n) không, mặc định là false
   *              Nếu không dùng, cần đặt meta: {title: 'xxx'} cho các route cần hiển thị trong menu
   *              Dùng để hiển thị chữ trong menu
   */
  useI18n: false,
  /**
   * @description Đường dẫn gốc (base path) của API request
   */
  baseUrl: {
    dev: '',
    pro: '',
  },
  /**
   * @description Giá trị name của route trang chủ mở mặc định, mặc định là home
   */
  homeName: 'home_index',
  /**
   * @description Plugin cần tải
   */
  plugin: {
    'error-store': {
      showInHeader: true, // Đặt là false thì sẽ không hiển thị biểu tượng log lỗi ở phía trên
      developmentOff: false, // Đặt là true thì ở môi trường dev sẽ không thu thập thông tin lỗi, giúp thuận tiện khi gỡ lỗi trong quá trình phát triển
    },
  },
};
