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
 * Dữ liệu thống kê
 */
export function getStatisticsInfo() {
  return request.get(
    "admin/order/statistics",
    {},
    {
      login: true,
    },
  );
}
/**
 * Thống kê đơn hàng theo tháng
 */
export function getStatisticsMonth(where) {
  return request.get("admin/order/data", where, {
    login: true,
  });
}
/**
 * Thống kê đơn hàng theo tháng
 */
export function getAdminOrderList(where) {
  return request.get("admin/order/list", where, {
    login: true,
  });
}
/**
 * Sửa giá đơn hàng
 */
export function setAdminOrderPrice(data) {
  return request.post("admin/order/price", data, {
    login: true,
  });
}
/**
 * Ghi chú đơn hàng
 */
export function setAdminOrderRemark(data) {
  return request.post("admin/order/remark", data, {
    login: true,
  });
}
/**
 * Chi tiết đơn hàng
 */
export function getAdminOrderDetail(orderId) {
  return request.get(
    "admin/order/detail/" + orderId,
    {},
    {
      login: true,
    },
  );
}

/**
 * Chi tiết đơn hoàn tiền
 */
export function getAdminRefundOrderDetail(orderId) {
  return request.get(
    "admin/refund_order/detail/" + orderId,
    {},
    {
      login: true,
    },
  );
}

/**
 * Lấy thông tin giao hàng của đơn hàng
 */
export function getAdminOrderDelivery(orderId) {
  return request.get(
    "admin/order/delivery/gain/" + orderId,
    {},
    {
      login: true,
    },
  );
}

/**
 * Lưu giao hàng đơn hàng
 */
export function setAdminOrderDelivery(id, data) {
  return request.post("admin/order/delivery/keep/" + id, data, {
    login: true,
  });
}
/**
 * Biểu đồ thống kê đơn hàng
 */
export function getStatisticsTime(data) {
  return request.get("admin/order/time", data, {
    login: true,
  });
}
/**
 * Xác nhận thanh toán đơn hàng thanh toán ngoại tuyến
 */
export function setOfflinePay(data) {
  return request.post("admin/order/offline", data, {
    login: true,
  });
}
/**
 * Xác nhận hoàn tiền đơn hàng
 */
export function setOrderRefund(data) {
  return request.post("admin/order/refund", data, {
    login: true,
  });
}

/**
 * Lấy đơn vị vận chuyển
 * @returns {*}
 */
export function getLogistics(data) {
  return request.get("logistics", data, {
    login: false,
  });
}

/**
 * Xác nhận sử dụng đơn hàng
 * @returns {*}
 */
export function orderVerific(verify_code, is_confirm, auth = 0) {
  return request.post("order/order_verific", {
    verify_code,
    is_confirm,
    auth,
  });
}

/**
 * Lấy mẫu của đơn vị vận chuyển
 * @returns {*}
 */
export function orderExportTemp(data) {
  return request.get("admin/order/export_temp", data);
}

/**
 * Lấy cấu hình in đơn hàng mặc định
 * @returns {*}
 */
export function orderDeliveryInfo() {
  return request.get("admin/order/delivery_info");
}

/**
 * Danh sách nhân viên giao hàng
 * @returns {*}
 */
export function orderOrderDelivery() {
  return request.get("admin/order/delivery");
}

/**
 * Danh sách hoàn tiền
 * @returns {*}
 */
export function orderRefund_order(where) {
  return request.get("admin/refund_order/list", where, {
    login: true,
  });
}

/**
 * Ghi chú đơn hàng (hoàn tiền)
 */
export function setAdminRefundRemark(data) {
  return request.post("admin/refund_order/remark", data, {
    login: true,
  });
}

/**
 * Đồng ý trả hàng cho đơn hàng
 */
export function agreeExpress(data) {
  return request.post("admin/order/agreeExpress", data, {
    login: true,
  });
}

/**
 * Thống kê quản lý cửa hàng
 */
export function getManageStatistics() {
  return request.get(
    "admin/manage/statistics",
    {},
    {
      login: true,
    },
  );
}

/**
 * Nền tảng - danh sách sản phẩm
 */
export function adminProductList(data) {
  return request.get("admin/manage/product", data);
}

/**
 * Đăng bán/ngừng bán sản phẩm
 */
export function productSetShow(data) {
  return request.post("admin/manage/product/set_show", data, {
    login: true,
  });
}

/**
 * Lấy dữ liệu nhãn
 */
export function getProductLabel() {
  return request.get(
    "admin/manage/product/label",
    {},
    {
      login: true,
    },
  );
}

/**
 * Lấy dữ liệu danh mục
 */
export function getProductCate() {
  return request.get(
    "admin/manage/product/cate",
    {},
    {
      login: true,
    },
  );
}

/**
 * Sửa nhãn sản phẩm
 */
export function postBatchProcess(data) {
  return request.post("admin/manage/product/save_label", data, {
    login: true,
  });
}

/**
 * Sửa danh mục sản phẩm
 */
export function postManageSaveCate(data) {
  return request.post("admin/manage/product/save_cate", data, {
    login: true,
  });
}
/**
 * Quy cách sản phẩm
 */
export function getManageProductAttr(id) {
  return request.get(
    `admin/manage/product/attr/${id}`,
    {},
    {
      login: true,
    },
  );
}

export function postUpdateAttrs(id, data) {
  return request.post(`admin/manage/product/save_attr/${id}`, data, {
    login: true,
  });
}

/**
 * Quản lý thống kê - lấy danh sách sản phẩm có thể tách của đơn hàng
 */
export function orderSplitInfo(id) {
  return request.get("admin/order/split_cart_info/" + id);
}

/**
 * Quản lý thống kê - gửi
 */
export function orderSplitDelivery(id, data) {
  return request.put("admin/order/split_delivery/" + id, data);
}

/**
 * Nền tảng - danh sách người dùng
 */
export function getUserList(data) {
  return request.get(`admin/manage/user`, data);
}

/**
 * Nền tảng - sửa số dư, điểm thưởng
 */
export function postUserUpdateOther(uid, data) {
  return request.post(`admin/manage/user/update/${uid}`, data);
}

/**
 * Nền tảng - danh sách nhóm
 */
export function getGroupList() {
  return request.get(`admin/manage/user/group`);
}

/**
 * Nền tảng - sửa thông tin người dùng
 */
export function postUserUpdate(data) {
  return request.post(`admin/user/update`, data);
}

/**
 * Nền tảng - phiếu giảm giá
 */
export function getUserCoupon(data) {
  return request.get(`admin/manage/user/coupon`, data);
}

/**
 * Nền tảng - nhãn người dùng
 */
export function getUserLabel(uid) {
  return request.get(`admin/manage/user/label/${uid}`);
}

/**
 * Nền tảng - danh sách hạng
 */
export function getLevelList() {
  return request.get(`admin/manage/user/level`);
}

/**
 * Nền tảng - chi tiết người dùng
 */
export function getUserInfo(uid) {
  return request.get(`admin/manage/user/info/${uid}`);
}

/**
 * Nền tảng - danh sách hoàn tiền
 */
export function adminRefundList(data) {
  return request.get("admin/refund_order/list", data);
}

/**
 * Chi tiết đơn hàng (hoàn tiền)
 */
export function getAdminRefundDetail(orderId) {
  return request.get(
    "admin/refund_order/detail/" + orderId,
    {},
    {
      login: true,
    },
  );
}

export function getTemplateOption() {
  return request.get(`admin/manage/product/shipping_temp`);
}

export function productCreate(data) {
  return request.post(`admin/manage/product/create`, data);
}

/**
 * Người giao hàng - lấy thông tin sản phẩm của đơn hàng cần xác nhận sử dụng
 */
export function orderCartInfo(data) {
  return request.post("store/order/cart_info", data);
}

/**
 * Người giao hàng - xác nhận sử dụng đơn hàng
 */
export function orderWriteoff(data) {
  return request.post("store/order/writeoff", data);
}
