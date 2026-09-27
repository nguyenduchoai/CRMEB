// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import request from '@/libs/request';

/*
 * Đăng nhập
 * */
export function AccountLogin(data) {
  return request({
    url: '/login',
    method: 'post',
    data,
    kefu: true,
  });
}

/**
 * Lấy danh sách người dùng chat bên trái (CSKH)
 * @constructor
 */
export function record(params) {
  return request({
    url: '/user/record',
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * Lấy chi tiết người dùng bên trái
 * @constructor
 */
export function userInfo(id) {
  return request({
    url: '/user/info/' + id,
    method: 'get',
    kefu: true,
  });
}

/**
 * Lấy danh sách đơn hàng của người dùng bên trái
 * @constructor
 */
export function getorderList(id, params) {
  return request({
    url: '/order/list/' + id,
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * CSKH giao hàng cho đơn
 * @constructor
 */
export function orderDelivery(id, data) {
  return request({
    url: '/order/delivery/' + id,
    method: 'post',
    data,
    kefu: true,
  });
}

/**
 * Sửa giá nhanh
 */
export function editPriceApi(id, data) {
  return request({
    url: `/order/update/${id}`,
    method: 'put',
    data,
    kefu: true,
  });
}

/**
 * CSKH đổi giá đơn hàng
 * @constructor
 */
export function orderEdit(id) {
  return request({
    url: 'order/edit/' + id,
    method: 'get',
    kefu: true,
  });
}

/**
 * Form hoàn tiền đơn hàng của CSKH
 * @constructor
 */
export function orderRecord(id) {
  return request({
    url: 'order/refund_form/' + id,
    method: 'get',
    kefu: true,
  });
}

/**
 * CSKH hoàn tiền đơn hàng
 * @constructor
 */
export function orderRefundApi(data) {
  return request({
    url: 'order/refund',
    method: 'post',
    data,
    kefu: true,
  });
}

/**
 * Lịch sử mua sản phẩm
 * @constructor
 */
export function productCart(uid, params) {
  return request({
    url: 'product/cart/' + uid,
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * Sản phẩm đã xem
 * @constructor
 */
export function productVisit(uid, params) {
  return request({
    url: 'product/visit/' + uid,
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * Sản phẩm bán chạy
 * @constructor
 */
export function productHot(uid, params) {
  return request({
    url: 'product/hot/' + uid,
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * Câu trả lời mẫu CSKH
 * @constructor
 */
export function speeChcraft(params) {
  return request({
    url: 'service/speechcraft',
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * Danh sách chuyển tiếp CSKH
 * @constructor
 */
export function transferList(params) {
  return request({
    url: 'service/transfer_list',
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * Danh sách chuyển tiếp CSKH
 * @constructor
 */
export function serviceTransfer(params) {
  return request({
    url: 'service/transfer',
    method: 'post',
    params,
    kefu: true,
  });
}

/**
 * Nhãn người dùng của CSKH
 * @constructor
 */
export function userLabel(id) {
  return request({
    url: `user/label/${id}`,
    method: 'get',
    kefu: true,
  });
}

/**
 * Cập nhật nhãn người dùng của CSKH
 * @constructor
 */
export function userLabelPut(id, data) {
  return request({
    url: `user/label/${id}`,
    method: 'put',
    data,
    kefu: true,
  });
}

/**
 * Danh sách chat người dùng của CSKH
 * @constructor
 */
export function serviceList(params) {
  return request({
    url: `service/list`,
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * Đăng xuất
 * @constructor
 */
export function AccountLogoutKefu() {
  return request({
    url: `user/logout`,
    method: 'post',
    kefu: true,
  });
}

/**
 * Lấy thông tin xác thực đăng nhập bằng quét mã
 * @constructor
 */
export function getSanCodeKey() {
  return request({
    url: `/key`,
    method: 'get',
    kefu: true,
  });
}

/**
 * Chi tiết sản phẩm
 * @constructor
 */
export function productInfo(id) {
  return request({
    url: `product/info/${id}`,
    method: 'get',
    kefu: true,
  });
}

/**
 * Lấy banner và logo
 */
export function loginInfoApi() {
  return request({
    url: '/login/info',
    method: 'get',
    kefu: true,
  });
}

/**
 * Ghi chú đơn hàng
 */
export function orderRemark(data) {
  return request({
    url: '/order/remark',
    method: 'post',
    data,
    kefu: true,
  });
}

/**
 * Chi tiết đơn hàng
 */
export function orderInfo(id) {
  return request({
    url: '/order/info/' + id,
    method: 'get',
    kefu: true,
  });
}

/**
 * Đơn vị vận chuyển
 */
export function orderExport() {
  return request({
    url: '/order/export',
    method: 'get',
    kefu: true,
  });
}

/**
 * Mẫu đơn vị vận chuyển
 */
export function orderTemp(params) {
  return request({
    url: '/order/temp',
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * Lấy danh sách nhân viên giao hàng
 */
export function orderDeliveryAll() {
  return request({
    url: '/order/delivery_all',
    method: 'get',
    kefu: true,
  });
}

/**
 * Lấy nhân viên giao hàng
 */
export function getSender() {
  return request({
    url: '/order/delivery_info',
    method: 'get',
    kefu: true,
  });
}

/**
 * Lấy danh mục mẫu câu CSKH
 */
export function serviceCate(params) {
  return request({
    url: '/service/cate',
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * Sửa câu trả lời mẫu
 */
export function serviceCateUpdate(id, params) {
  return request({
    url: 'service/speechcraft/' + id,
    method: 'PUT',
    params,
    kefu: true,
  });
}

/**
 * Thêm câu trả lời mẫu
 */
export function addSpeeChcraft(data) {
  return request({
    url: 'service/speechcraft',
    method: 'post',
    data,
    kefu: true,
  });
}

/**
 * Thêm danh mục
 */
export function addServiceCate(data) {
  return request({
    url: 'service/cate',
    method: 'post',
    data,
    kefu: true,
  });
}

/**
 * Sửa danh mục
 */
export function editServiceCate(id, params) {
  return request({
    url: 'service/cate/' + id,
    method: 'PUT',
    params,
    kefu: true,
  });
}

/**
 * Tình trạng đăng nhập quét mã
 */
export function scanStatus(key, params) {
  return request({
    url: 'scan/' + key,
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * Quét mã xác nhận sử dụng
 */
export function orderVerificApi(id) {
  return request({
    url: `/order/verific/${id}`,
    method: 'get',
    kefu: true,
  });
}

/**
 * Nhóm người dùng của CSKH
 * @constructor
 */
export function userGroupApi() {
  return request({
    url: `user/group`,
    method: 'get',
    kefu: true,
  });
}

/**
 * CSKH thiết lập nhóm người dùng
 * @constructor
 */
export function putGroupApi(uid, id) {
  return request({
    url: `user/group/${uid}/${id}`,
    method: 'put',
    kefu: true,
  });
}

/**
 * Cấu hình CSKH
 * @constructor
 */
export function kefuConfig() {
  return request({
    url: `config`,
    method: 'get',
    kefu: true,
  });
}

/**
 * Client -- CSKH ngẫu nhiên
 * @constructor
 */
export function serviceListApi(params) {
  return request({
    url: `tourist/user`,
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * Client -- Vị trí quảng cáo
 * @constructor
 */
export function getAdvApi() {
  return request({
    url: `tourist/adv`,
    method: 'get',
    kefu: true,
  });
}

/**
 * Client -- Lịch sử chat
 * @constructor
 */
export function chatListApi(params) {
  return request({
    url: `tourist/chat`,
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * Client -- Phản hồi CSKH
 * @constructor
 */
export function feedbackDataApi() {
  return request({
    url: `tourist/feedback`,
    method: 'get',
    kefu: true,
  });
}

/**
 * Client -- Câu gợi ý phản hồi
 * @constructor
 */
export function feedbackFromApi(data) {
  return request({
    url: `tourist/feedback`,
    method: 'post',
    data,
    kefu: true,
  });
}

/**
 * Client -- Khách vãng lai lấy mã đơn hàng người dùng
 * @constructor
 */
export function getOrderApi(order_id, params) {
  return request({
    url: `tourist/order/${order_id}`,
    method: 'get',
    params,
    kefu: true,
  });
}

/**
 * Client -- Chi tiết sản phẩm
 * @constructor
 */
export function productApi(id) {
  return request({
    url: `tourist/product/${id}`,
    method: 'get',
    kefu: true,
  });
}

/**
 * Lấy liên kết CSKH
 * @constructor
 */
export function getWorkermanUrl() {
  return request({
    url: `get_workerman_url`,
    method: 'get',
  });
}

/**
 * Copy dán để tải ảnh lên
 */
export function uploadImg(data) {
  return request({
    url: `upload`,
    method: 'post',
    data,
    kefu: true,
  });
}
