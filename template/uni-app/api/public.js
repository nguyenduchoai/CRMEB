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
import wechat from "@/libs/wechat.js";

/**
 * Lấy cấu hình sdk WeChat
 * @returns {*}
 */
export function getWechatConfig() {
  return request.get(
    "wechat/config",
    {
      url: wechat.signLink(),
    },
    {
      noAuth: true,
    },
  );
}

/**
 * Lấy cấu hình sdk WeChat
 * @returns {*}
 */
export function wechatAuth(code, spread, login_type) {
  return request.get(
    "wechat/auth",
    {
      code,
      spread,
      login_type,
    },
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
 * Đăng nhập người dùng Mini Program
 * @param data object Thông tin đăng nhập người dùng Mini Program
 */
export function login(data) {
  return request.post("wechat/mp_auth", data, {
    noAuth: true,
  });
}

/**
 * Ủy quyền im lặng
 * @param {Object} data
 */
export function silenceAuth(data) {
  //#ifdef MP
  return request.get("v2/wechat/silence_auth", data, {
    noAuth: true,
  });
  //#endif
  //#ifdef H5
  return request.get("v2/wechat/auth_type", data, {
    noAuth: true,
  });
  //#endif
}

/**
 * Chia sẻ
 * @returns {*}
 */
export function getShare() {
  return request.get(
    "share",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Đăng nhập OA WeChat
 * @returns {*}
 */
export function wechatAuthLogin(data) {
  return request.get("v2/wechat/auth_login", data, {
    noAuth: true,
  });
}

/**
 * Lấy poster theo dõi
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
 * Code tạo người dùng
 * @returns {*}
 */
export function authType(data) {
  return request.get("v2/routine/auth_type", data, {
    noAuth: true,
  });
}

/**
 * Đăng nhập ủy quyền
 * @returns {*}
 */
export function authLogin(data) {
  return request.get("v2/routine/auth_login", data, {
    noAuth: true,
  });
}

/**
 * Lấy base64 của ảnh
 * @retins {*}
 * */
export function imageBase64(image, code) {
  return request.post(
    "image_base64",
    {
      image: image,
      code: code,
    },
    {
      noAuth: true,
    },
  );
}

/**
 * Chức năng tự động copy mã lệnh
 * @returns {*}
 */
export function copyWords() {
  return request.get(
    "copy_words",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Lấy trạng thái shop có bắt buộc liên kết số điện thoại hay không
 */
export function getShopConfig() {
  return request.get(
    "v2/bind_status",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Liên kết số điện thoại trên Mini Program
 * @param {Object} data
 */
export function routineBindingPhone(data) {
  return request.post("v2/routine/auth_binding_phone", data, {
    noAuth: true,
  });
}
/**
 * Liên kết số điện thoại trên Mini Program
 * @param {Object} data
 */
export function wechatBindingPhone(data) {
  return request.post("v2/wechat/auth_binding_phone", data, {
    noAuth: true,
  });
}
/**
 * Đăng nhập bằng số điện thoại trên Mini Program
 * @param {Object} data
 */
export function phoneLogin(data) {
  return request.post("v2/routine/phone_login", data, {
    noAuth: true,
  });
}

/**
 * Đăng nhập người dùng Mini Program
 * @param data object Thông tin đăng nhập người dùng Mini Program
 */
export function routineLogin(data) {
  return request.get("v2/wechat/routine_auth", data, {
    noAuth: true,
  });
}

/**
 * Lấy cấu hình sdk WeChat
 * @returns {*}
 */
export function wechatAuthV2(code, spread) {
  return request.get(
    "v2/wechat/auth",
    {
      code,
      spread,
    },
    {
      noAuth: true,
    },
  );
}

/**
 * Lấy menu dưới cùng của component
 * @param data object Lấy menu dưới cùng của component
 */
export function getNavigation(data) {
  return request.get("theme/navigation", data, {
    noAuth: true,
  });
}
export function getSubscribe() {
  return request.get(
    "subscribe",
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Lấy thông tin phiên bản
 * @param Loại hệ thống
 */
export function getUpdateInfo(type) {
  return request.get(
    "get_new_app/" + type,
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Lấy số phiên bản dữ liệu DIY trang chủ
 *
 */
export function getVersion(name) {
  return request.get(
    `v2/diy/get_version/${name}`,
    {},
    {
      noAuth: true,
    },
  );
}
/**
 * Lấy số phiên bản danh mục sản phẩm
 *
 */
export function getCategoryVersion(name) {
  return request.get(
    `category_version`,
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Thông tin cấu hình
 *
 */
export function basicConfig(name) {
  return request.get(
    `basic_config`,
    {},
    {
      noAuth: true,
    },
  );
}
/**
 * Thông tin phiên bản backend
 *
 */
export function getSystemVersion() {
  return request.get(
    `version`,
    {},
    {
      noAuth: true,
    },
  );
}

/**
 * Đăng nhập iframe
 *
 */
export function remoteRegister(data) {
  return request.get(`remote_register`, data, {
    noAuth: true,
  });
}
