// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2024 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import request from "@/utils/request.js";
/**
 * API chung, API phiếu giảm giá, thông tin ngành, đăng ký số điện thoại
 *
 */
export function getAjcaptcha(data) {
  return request.get("ajcaptcha", data, {
    noAuth: true,
  });
}

export function ajcaptchaCheck(data) {
  return request.post("ajcheck", data, {
    noAuth: true,
  });
}

/**
 * Lấy dữ liệu trang chủ, không cần ủy quyền
 *
 */
export function getIndexData() {
  return request.get(
    "v2/index",
    {},
    {
      noAuth: true,
    },
  );
}
/**
 * Lấy loại server
 *
 */
export function getServerType() {
  return request.get(
    "v2/site_serve",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Lấy ủy quyền đăng nhập (login)
 *
 */
export function getLogo() {
  return request.get(
    "wechat/get_logo",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Lưu form_id
 * @param string formId
 */
export function setFormId(formId) {
  return request.post("wechat/set_form_id", {
    formId: formId,
  });
}

/**
 * Nhận phiếu giảm giá
 * @param int couponId
 *
 */
export function setCouponReceive(couponId) {
  return request.post("coupon/receive", {
    couponId: couponId,
  });
}
/**
 * Danh sách phiếu giảm giá
 * @param object data
 */
export function getCoupons(data) {
  return request.get("v2/coupons", data, {
    noAuth: true,
  });
}
/**
 * Dữ liệu component danh sách phiếu giảm giá trang chủ
 * @param object data
 */
export function getCouponsIndex(data) {
  return request.get("coupons", data, {
    noAuth: true,
  });
}

/**
 * Phiếu giảm giá của tôi
 * @param int types 0 tất cả  1 chưa dùng 2 đã dùng
 */
export function getUserCoupons(types, data) {
  return request.get("coupons/user/" + types, data);
}

/**
 * Phiếu giảm giá cho người mới ở trang chủ
 *
 */
export function getNewCoupon() {
  return request.get("v2/new_coupon");
}

/**
 * Danh sách danh mục bài viết
 *
 */
export function getArticleCategoryList() {
  return request.get(
    "article/category/list",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Danh sách bài viết
 * @param int cid
 *
 */
export function getArticleList(cid, data) {
  return request.get("article/list/" + cid, data, {
    noAuth: true,
  });
}

/**
 * Danh sách bài viết nổi bật
 *
 */
export function getArticleHotList() {
  return request.get(
    "article/hot/list",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Danh sách bài viết trình chiếu (banner)
 *
 */
export function getArticleBannerList() {
  return request.get(
    "article/banner/list",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Chi tiết bài viết
 * @param int id
 *
 */
export function getArticleDetails(id) {
  return request.get(
    "article/details/" + id,
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * API đăng nhập bằng số điện thoại + mã xác thực (OTP)
 * @param object data
 */
export function loginMobile(data) {
  return request.post("login/mobile", data, {
    noAuth: true,
  });
}

/**
 * Lấy KEY SMS
 * @param object phone
 */
export function verifyCode() {
  return request.get(
    "verify_code",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Gửi mã xác thực
 * @param object phone
 */
export function registerVerify(
  phone,
  reset,
  key,
  captchaType,
  captchaVerification,
) {
  return request.post(
    "register/verify",
    {
      phone: phone,
      type: reset === undefined ? "reset" : reset,
      key: key,
      captchaType: captchaType,
      captchaVerification: captchaVerification,
    },
    {
      noAuth: true,
    },
  );
}

/**
 * Đăng ký bằng số điện thoại
 * @param object data
 *
 */
export function phoneRegister(data) {
  return request.post("register", data, {
    noAuth: true,
  });
}

/**
 * Đổi mật khẩu bằng số điện thoại
 * @param object data
 *
 */
export function phoneRegisterReset(data) {
  return request.post("register/reset", data, {
    noAuth: true,
  });
}

/**
 * Đăng nhập bằng số điện thoại + mật khẩu
 * @param object data
 *
 */
export function phoneLogin(data) {
  return request.post("login", data, {
    noAuth: true,
  });
}

/**
 * Chuyển đổi đăng nhập H5
 * @param object data
 */
// #ifdef MP
export function switchH5Login() {
  return request.post("switch_h5", {
    from: "routine",
  });
}
// #endif

/*
 * h5 chuyển sang đăng nhập OA WeChat
 * */
// #ifdef H5
export function switchH5Login() {
  return request.post("switch_h5", {
    from: "wechat",
  });
}
// #endif

/**
 * Liên kết số điện thoại
 *
 */
export function bindingPhone(data) {
  return request.post("binding", data, {
    noAuth: true,
  });
}

/**
 * Liên kết số điện thoại
 *
 */
export function bindingUserPhone(data) {
  return request.post("user/binding", data);
}

/**
 * Đăng xuất
 *
 */
export function logout() {
  return request.get("logout");
}

/**
 * Lấy id tin nhắn đăng ký
 */
export function getTempIds() {
  return request.get(
    "wechat/temp_ids",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Dữ liệu mua chung trang chủ
 */
export function pink() {
  return request.get(
    "pink",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Lấy thông tin thành phố
 */
export function getCity() {
  return request.get(
    "city_list",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Lấy danh sách
 */
export function getLiveList(page, limit) {
  return request.get(
    "wechat/live",
    {
      page,
      limit,
    },
    {
      noAuth: true,
    },
  );
}

/**
 * Lấy DIY trang chủ;
 */
export function getDiy(id) {
  return request.get(
    `v2/diy/get_diy/default${id ? "?id=" + id : ""}`,
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Đổi màu một cú nhấp;
 */
export function colorChange(name) {
  return request.get(
    "v2/diy/color_change/" + name,
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Lấy trạng thái theo dõi OA WeChat
 * @returns {*}
 */
export function follow() {
  return request.get(
    "wechat/follow",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Đổi số điện thoại
 * @returns {*}
 */
export function updatePhone(data) {
  return request.post("user/updatePhone", data, {
    noAuth: true,
  });
}

/**
 * Popup phiếu giảm giá trang chủ
 * @returns {*}
 */
export function getCouponV2() {
  return request.get(
    "v2/get_today_coupon",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Popup phiếu giảm giá người dùng mới
 * @returns {*}
 */
export function getCouponNewUser() {
  return request.get(
    "v2/new_coupon",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Dữ liệu chọn nhanh trang chủ
 * @param {Object} data
 */
export function category(data) {
  return request.get("category", data, {
    noAuth: true,
  });
}

/**
 * Lịch sử tìm kiếm cá nhân
 * @param {Object} data
 */
export function searchList(data) {
  return request.get("v2/user/search_list", data, {
    noAuth: true,
  });
}

/**
 * Xóa lịch sử tìm kiếm
 */
export function clearSearch() {
  return request.get("v2/user/clean_search");
}
/**
 * Lấy cấu hình cơ bản của website
 */
export function siteConfig(data) {
  return request.get("site_config", data, {
    noAuth: true,
  });
}

/**
 * Đăng nhập WeChat trên App
 * @returns {*}
 */
export function wechatAppAuth(data) {
  return request.post("wechat/app_auth", data, {
    noAuth: true,
  });
}
/**
 * Lấy loại CSKH
 * @returns {*}
 */
export function getCustomerType(data) {
  return request.get(
    "get_customer_type",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Lấy quảng cáo màn hình khởi động
 * @returns {*}
 */
export function getOpenAdv(data) {
  return request.get(
    "get_open_adv",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Lấy thông tin bản quyền
 */
export function getCrmebCopyRight() {
  return request.get(
    "copyright",
    {},
    {
      noAuth: true,
    },
  );
}
/**
 * API lấy phiên bản DIY
 * @param {Object} id
 */
export function getDiyVersion(name) {
  return request.get(
    `v2/diy/get_version/${name}`,
    {},
    {
      noAuth: true,
    },
  );
}
/**
 * API lấy thông tin chủ đề
 * @param {Object} id
 */
export function getThemeInfo(type, data) {
  return request.get(`theme_info/${type}`, data || {}, {
    noAuth: true,
  });
}

/**
 * Lấy thông tin điểm danh DIY
 * @param {Object} id
 */
export function getSign() {
  return request.get(
    "v2/diy/sign",
    {},
    {
      noAuth: true,
    },
  );
}
/**
 * @description Lấy danh sách sản phẩm của chủ đề
 */
export function getThemeProduct(data) {
  return request.get("theme/product", data, {
    noAuth: true,
  });
}
/**
 * @description Lấy danh sách bài viết
 */
export function getThemeArticle(data) {
  return request.get("theme/article", data, {
    noAuth: true,
  });
}
/**
 * @description Lấy danh sách phiếu giảm giá
 */
export function getThemeCoupon(data) {
  return request.get("theme/coupon", data, {
    noAuth: true,
  });
}

/**
 * Lấy thông tin người dùng (DIY)
 *
 */
export function getThemeUser() {
  return request.get(
    "theme/user",
    {},
    {
      noAuth: true,
    },
  );
}
