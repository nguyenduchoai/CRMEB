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
import { getCookies } from '@/libs/util';

/**
 * @description Cài đặt, Cài đặt hệ thống, Phần đầu cài đặt ứng dụng
 * @param {Object} param data {Object} Tham số truyền giá trị, loại type
 */
export function headerListApi(data) {
  return request({
    url: 'setting/config/header_basics',
    method: 'get',
    params: data,
  });
}

/**
 * @description Cài đặt, Cài đặt hệ thống, Cài đặt ứng dụng, Form sửa
 * @param {Object} param data {Object} Tham số truyền giá trị, loại type
 */
export function dataFromApi(data, url) {
  return request({
    url: url,
    method: 'get',
    params: data,
  });
}

/**
 * @description Cài đặt, Cài đặt SMS, Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function tempListApi(params) {
  return request({
    url: params.url,
    method: 'get',
    params: params.data,
  });
}

/**
 * @description Cài đặt, Cài đặt SMS, Form đăng ký mẫu
 * @param {Object} param data {Object} Tham số truyền giá trị, loại type
 */
export function tempCreateApi() {
  return request({
    url: 'notify/sms/temp/create',
    method: 'get',
  });
}

/**
 * @description Cài đặt, Cài đặt SMS, Đăng nhập
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function configApi(data) {
  return request({
    url: 'serve/login',
    method: 'post',
    data,
  });
}

/**
 * @description Cài đặt, SMS, Đổi mật khẩu
 */
export function serveModifyApi(data) {
  return request({
    url: 'serve/modify',
    method: 'post',
    data,
  });
}

/**
 * @description Cài đặt, SMS, Đổi số điện thoại
 */
export function updateHoneApi(data) {
  return request({
    url: 'serve/update_phone',
    method: 'post',
    data,
  });
}

/**
 * @description Cài đặt, Cài đặt SMS, Đổi mật khẩu tài khoản
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
// export function configApi (data) {
//     return request({
//         url: 'notify/sms/config',
//         method: 'post',
//         data
//     });
// }

/**
 * @description Cài đặt, Cài đặt SMS, Gửi mã xác thực
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function captchaApi(data) {
  return request({
    url: 'serve/captcha',
    method: 'post',
    data,
  });
}
/**
 * @description Xác thực mã xác thực
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function checkCaptchaApi(data) {
  return request({
    url: 'serve/checkCode',
    method: 'post',
    data,
  });
}

/**
 * @description Cài đặt, Cài đặt SMS, Đăng ký
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function registerApi(data) {
  return request({
    url: 'serve/register',
    method: 'post',
    data,
  });
}

/**
 * @description Cài đặt, Cài đặt SMS, Số lượng SMS còn lại
 */
export function smsNumberApi() {
  return request({
    url: 'notify/sms/number',
    method: 'get',
  });
}

/**
 * @description Cài đặt, Cài đặt SMS, Thông tin người dùng nền tảng
 */
export function serveInfoApi() {
  return request({
    url: 'serve/info',
    method: 'get',
  });
}

/**
 * @description Sửa chữ ký SMS
 */
export function serveSign(data) {
  return request({
    url: 'serve/sms/sign',
    method: 'PUT',
    data,
  });
}

/**
 * Đăng nhập CSKH
 */
export function kefuLogin(id) {
  return request({
    url: `app/wechat/kefu/login/${id}`,
    method: 'get',
  });
}

/**
 * Danh sách mẫu câu CSKH
 */
export function wechatSpeechcraft(data) {
  return request({
    url: `app/wechat/speechcraft`,
    method: 'get',
    params: data,
  });
}

/**
 * Sửa mẫu câu CSKH
 */
export function speechcraftEdit(id) {
  return request({
    url: `app/wechat/speechcraft/${id}/edit`,
    method: 'get',
  });
}

/**
 * Thêm mẫu câu CSKH
 */
export function speechcraftCreate() {
  return request({
    url: `app/wechat/speechcraft/create`,
    method: 'get',
  });
}

/**
 * Phản hồi CSKH
 */
export function kefuFeedBack(params) {
  return request({
    url: `app/feedback`,
    method: 'get',
    params,
  });
}

/**
 * Phản hồi CSKH
 */
export function kefuFeedBackEdit(id) {
  return request({
    url: `app/feedback/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Cài đặt, SMS, Đơn vị vận chuyển
 */
export function exportAllApi() {
  return request({
    url: 'serve/export_all',
    method: 'get',
  });
}

/**
 * Có mở vận đơn điện tử không
 */
// export function serveDumpOpen () {
//     return request({
//         url: `serve/dump_open`,
//         method: 'get'
//     });
// }

/**
 * Mở dịch vụ vận chuyển
 */
export function serveOpen() {
  return request({
    url: `serve/open`,
    method: 'get',
  });
}

/**
 * @description Cài đặt, SMS, Bảng đơn vị vận chuyển
 */
export function exportTempApi(params) {
  return request({
    url: 'serve/export_temp',
    method: 'get',
    params,
  });
}

/**
 * @description Cài đặt, SMS, 2 = vận đơn điện tử, 3 = tra cứu vận chuyển, danh sách
 */
export function serveRecordListApi(params) {
  return request({
    url: 'serve/record',
    method: 'get',
    params,
  });
}

/**
 * @description Cài đặt, SMS, Mở dịch vụ khác
 */
export function serveOpnOtherApi(params) {
  return request({
    url: 'serve/open',
    method: 'get',
    params,
  });
}

/**
 * @description Cài đặt, SMS, Mở vận đơn điện tử
 */
export function serveOpnExpressApi(data) {
  return request({
    url: 'serve/opn_express',
    method: 'post',
    data,
  });
}

/**
 * @description Cài đặt, SMS, Mở dịch vụ SMS
 */
export function serveSmsOpenApi(params) {
  return request({
    url: 'serve/sms/open',
    method: 'get',
    params,
  });
}

/**
 * @description Cài đặt, Cài đặt SMS, Thanh toán gói
 */
export function smsPriceApi(params) {
  return request({
    url: 'serve/meal_list',
    method: 'get',
    params,
  });
}

/**
 * @description Cài đặt, Cài đặt SMS, Mã thanh toán
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function payCodeApi(data) {
  return request({
    url: 'serve/pay_meal',
    method: 'post',
    data,
  });
}

/**
 * @description Cài đặt, Cài đặt SMS, Lịch sử gửi
 */
export function smsRecordApi(params) {
  return request({
    url: 'notify/sms/record',
    method: 'get',
    params,
  });
}

/**
 * @description Cài đặt cửa hàng, Chi tiết
 */
export function storeApi() {
  return request({
    url: 'merchant/store',
    method: 'GET',
  });
}

/**
 * @description Cài đặt cửa hàng, Lấy key bản đồ
 */
export function keyApi() {
  return request({
    url: 'merchant/store/address',
    method: 'GET',
  });
}

/**
 * @description Cài đặt cửa hàng, Submit dữ liệu,
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function storeAddApi(data) {
  return request({
    url: `merchant/store/${data.id}`,
    method: 'POST',
    data,
  });
}

/**
 * @description Cài đặt, Đơn vị vận chuyển, Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function freightListApi(params) {
  return request({
    url: 'freight/express',
    method: 'get',
    params,
  });
}

/**
 * @description Cài đặt, Đơn vị vận chuyển, Form thêm mới
 */
export function freightCreateApi() {
  return request({
    url: '/freight/express/create',
    method: 'get',
  });
}

/**
 * @description Cài đặt, Đơn vị vận chuyển, Form sửa
 * @param {Number} param id {Number} ID đơn vị vận chuyển
 */
export function freightEditApi(id) {
  return request({
    url: `freight/express/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Cài đặt, Đơn vị vận chuyển, Đổi trạng thái
 * @param {Number} param id {Number} ID đơn vị vận chuyển
 */
export function freightStatusApi(data) {
  return request({
    url: `freight/express/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}

/**
 * @description Đồng bộ đơn vị vận chuyển
 */
export function freightSyncExpressApi() {
  return request({
    url: `freight/express/sync_express`,
    method: 'get',
  });
}

/**
 * @description Danh mục câu trả lời mẫu
 */
export function speechcraftcate() {
  return request({
    url: `app/wechat/speechcraftcate`,
    method: 'get',
  });
}
/**
 * @description Danh mục mã kênh
 */
export function wechatQrcodeTree() {
  return request({
    url: `app/wechat_qrcode/cate/list`,
    method: 'get',
  });
}

/**
 * @description Lấy form tạo danh mục
 */
export function speechcraftcateCreate() {
  return request({
    url: `app/wechat/speechcraftcate/create`,
    method: 'get',
  });
}
/**
 * @description Lấy form tạo, sửa danh mục mã kênh
 */
export function wechatQrcodeCreate(id) {
  return request({
    url: `app/wechat_qrcode/cate/create/${id}`,
    method: 'get',
  });
}

/**
 * @description Sửa danh mục mẫu câu (lấy form)
 */
export function speechcraftcateEdit(id) {
  return request({
    url: `app/wechat/speechcraftcate/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Cài đặt, Quản lý vai trò, Danh sách
 * @param {Number} param id {Number} ID đơn vị vận chuyển
 */
export function roleListApi(params) {
  return request({
    url: `setting/role`,
    method: 'GET',
    params,
  });
}
/**
 * @description Lấy danh sách mã kênh
 * @param {Number} param id {Number} ID đơn vị vận chuyển
 */
export function wechatQrcodeList(params) {
  return request({
    url: `app/wechat_qrcode/list`,
    method: 'GET',
    params,
  });
}

/**
 * @description Cài đặt, Quản lý vai trò, Đổi trạng thái
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function roleSetStatusApi(data) {
  return request({
    url: `setting/role/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}

/**
 * @description Cài đặt, Quản lý vai trò, Thêm mới/sửa
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function roleCreateApi(data) {
  return request({
    url: `setting/role/${data.id}`,
    method: 'post',
    data,
  });
}

/**
 * @description Cài đặt, Quản lý vai trò, Chi tiết
 * @param {Number} param id {Number} ID quản lý vai trò
 */
export function roleInfoApi(id) {
  return request({
    url: `setting/role/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Cài đặt, Quản lý vai trò, Danh sách quyền
 */
export function menusListApi() {
  return request({
    url: `setting/role/create`,
    method: 'get',
  });
}

/**
 * @description Cài đặt, Quản lý CSKH -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function kefuListApi(params) {
  return request({
    url: `app/wechat/kefu`,
    method: 'get',
    params,
  });
}

/**
 * @description Cài đặt, Quản lý CSKH -- Chọn người dùng
 *  @param {Object} param params {Object} Tham số truyền giá trị
 */
export function kefucreateApi(params) {
  return request({
    url: `app/wechat/kefu/create`,
    method: 'get',
    params,
  });
}

/**
 * @description Cài đặt, Quản lý CSKH -- Thêm CSKH
 *  @param {Object} param params {Object} Tham số truyền giá trị
 */
export function kefuaddApi() {
  return request({
    url: `app/wechat/kefu/add`,
    method: 'get',
  });
}

/**
 * @description Cài đặt, Quản lý CSKH -- Lưu thêm CSKH
 *  @param {Object} param params {Object} Tham số truyền giá trị
 */
export function kefuAddApi(data) {
  return request({
    url: `app/wechat/kefu`,
    method: 'post',
    data,
  });
}

/**
 * @description Cài đặt, Quản lý CSKH -- Đổi trạng thái
 *  @param {Object} param data {Object} Tham số truyền giá trị
 */
export function kefusetStatusApi(data) {
  return request({
    url: `app/wechat/kefu/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}

/**
 * @description Cài đặt, Mã kênh -- Đổi trạng thái
 *  @param {Object} param data {Object} Tham số truyền giá trị
 */
export function wechatQrcodeStatusApi(data) {
  return request({
    url: `app/wechat_qrcode/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}
/**
 * @description Lấy danh sách người dùng theo mã kênh
 *  @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getUserList(params) {
  return request({
    url: `app/wechat_qrcode/user_list/${params.id}`,
    method: 'get',
    params,
  });
}
/**
 * @description Cài đặt, Lấy chi tiết sửa mã kênh
 *  @param {Object} param data {Object} Tham số truyền giá trị
 */
export function wechatQrcodeDetail(id) {
  return request({
    url: `app/wechat_qrcode/info/${id}`,
    method: 'get',
  });
}
/**
 * @description  Tạo mã kênh -- Lưu
 */
export function wechatQrcodeSaveApi(id, data) {
  return request({
    url: `app/wechat_qrcode/save/${id}`,
    method: 'post',
    data,
  });
}
/**
 * @description Cài đặt, Quản lý CSKH -- Form sửa
 *  @param {Number} param id {Number} ID nhân viên CSKH
 */
export function kefuEditApi(id) {
  return request({
    url: `app/wechat/kefu/${id}/edit`,
    method: 'GET',
  });
}

/**
 * @description Cài đặt, Quản lý CSKH -- Danh sách lịch sử chat
 *  @param {Number} param id {Number} ID nhân viên CSKH
 *  @param {Object} param params {Object} Truyền tham số
 */
export function kefuRecordApi(params, id) {
  return request({
    url: `app/wechat/kefu/record/${id}`,
    method: 'GET',
    params,
  });
}

/**
 * @description Cài đặt, Quản lý CSKH -- Xem danh sách hội thoại
 *  @param {Object} param params {Object} Truyền tham số
 */
export function kefuChatlistApi(params) {
  return request({
    url: `app/wechat/kefu/chat_list`,
    method: 'GET',
    params,
  });
}

/**
 * @description Cài đặt SMS -- Xem có đang đăng nhập không
 */
export function isLoginApi() {
  return request({
    url: `notify/sms/is_login`,
    method: 'GET',
  });
}

/**
 * @description Cài đặt SMS -- Đăng xuất
 */
export function logoutApi() {
  return request({
    url: `notify/sms/logout`,
    method: 'GET',
  });
}

/**
 * @description Cài đặt, Dữ liệu thành phố -- Danh sách
 *  @param {Object} param data {Object} Tham số truyền giá trị
 */
export function cityListApi(id) {
  return request({
    url: `setting/city/list/${id}`,
    method: 'get',
  });
}

/**
 * @description Cài đặt, Thêm thành phố -- Form
 *  @param {Object} param data {Object} Tham số truyền giá trị
 */
export function cityAddApi(id) {
  return request({
    url: `setting/city/add/${id}`,
    method: 'get',
  });
}

/**
 * @description Cài đặt, Sửa thành phố -- Form
 *  @param {Object} param data {Object} Tham số truyền giá trị
 */
export function cityApi(id) {
  return request({
    url: `setting/city/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Cài đặt, Mẫu phí vận chuyển -- Danh sách
 *  @param {Object} param data {Object} Tham số truyền giá trị
 */
export function templatesApi(data) {
  return request({
    url: `setting/shipping_templates/list`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Cài đặt, Mẫu phí vận chuyển -- Dữ liệu thành phố
 */
export function templatesCityListApi(data) {
  return request({
    url: `setting/shipping_templates/city_list`,
    method: 'get',
  });
}

/**
 * @description Cài đặt, Mẫu phí vận chuyển -- Submit form sửa;
 */
export function templatesSaveApi(id, data) {
  return request({
    url: `setting/shipping_templates/save/${id}`,
    method: 'post',
    data,
  });
}

/**
 * @description Cài đặt, Mẫu phí vận chuyển -- Submit form sửa;
 */
export function shipTemplatesApi(id) {
  return request({
    url: `setting/shipping_templates/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Cài đặt cửa hàng -- Số lượng danh mục danh sách cửa hàng;
 */
export function storeGetHeaderApi() {
  return request({
    url: `merchant/store/get_header`,
    method: 'get',
  });
}

/**
 * @description Cài đặt cửa hàng -- Danh sách cửa hàng;
 */
export function merchantStoreApi(data) {
  return request({
    url: `merchant/store`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Cài đặt cửa hàng -- Cài đặt cửa hàng;
 */
export function storeSetShowApi(id, is_show) {
  return request({
    url: `merchant/store/set_show/${id}/${is_show}`,
    method: 'put',
  });
}

/**
 * @description Cài đặt cửa hàng -- Sửa thông tin cửa hàng;
 */
export function storeGetInfoApi(id) {
  return request({
    url: `merchant/store/get_info/${id}`,
    method: 'get',
  });
}

/**
 * @description Cài đặt cửa hàng -- Danh sách nhân viên;
 */
export function storeStaffApi(data) {
  return request({
    url: `merchant/store_staff`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Cài đặt cửa hàng -- Thêm nhân viên;
 */
export function storeStaffCreateApi() {
  return request({
    url: `merchant/store_staff/create`,
    method: 'get',
  });
}

/**
 * @description Cài đặt cửa hàng -- Thêm nhân viên;
 */
export function storeStaffEditApi(id) {
  return request({
    url: `merchant/store_staff/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Cài đặt nhân viên -- Cài đặt hiện/ẩn nhân viên;
 */
export function storeStaffSetShowApi(id, is_show) {
  return request({
    url: `merchant/store_staff/set_show/${id}/${is_show}`,
    method: 'put',
  });
}

/**
 * @description Cài đặt đơn hàng -- Danh sách đơn hàng xác nhận sử dụng;
 */
export function verifyOrderApi(data) {
  return request({
    url: `merchant/verify_order`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Cài đặt đơn hàng -- Phần đầu đơn hàng xác nhận sử dụng;
 */
export function verifySpreadInfoApi(uid) {
  return request({
    url: `merchant/verify/spread_info/${uid}`,
    method: 'get',
  });
}

/**
 * Lấy danh sách cửa hàng để nhân viên tìm kiếm
 */
export function merchantStoreListApi() {
  return request({
    url: `merchant/store_list`,
    method: 'get',
  });
}

/**
 * Xóa bộ nhớ đệm dữ liệu thành phố
 */
export function cityCleanCacheApi() {
  return request({
    url: `setting/city/clean_cache`,
    method: 'get',
  });
}
/**
 *Cấu hình lưu trữ - Lấy phần đầu cấu hình cloud storage
 */
export function storageConfigApi() {
  return request({
    url: `system/config/storage/config`,
    method: 'get',
  });
}
/**
 *Cấu hình lưu trữ - Lấy phần đầu cấu hình cloud storage
 */
export function storageSwitchApi(data) {
  return request({
    url: `system/config/storage/config`,
    method: 'post',
    data,
  });
}

/**
 * @description Cấu hình lưu trữ - Lấy form cấu hình cloud storage
 */
export function addConfigApi(type) {
  return request({
    url: `system/config/storage/form/${type}`,
    method: 'get',
  });
}

/**
 * @description Cấu hình lưu trữ - Lấy form tạo cloud storage
 */
export function addStorageApi(type) {
  return request({
    url: `system/config/storage/create/${type}`,
    method: 'get',
  });
}

/**
 * @description Cấu hình lưu trữ - Lấy danh sách cloud storage
 */
export function storageListApi(data) {
  return request({
    url: `system/config/storage`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Cấu hình lưu trữ - Đồng bộ không gian lưu trữ
 */
export function storageSynchApi(type) {
  return request({
    url: `system/config/storage/synch/${type}`,
    method: 'put',
  });
}
/**
 * @description Cấu hình lưu trữ - Đổi trạng thái
 */
export function storageStatusApi(id) {
  return request({
    url: `system/config/storage/status/${id}`,
    method: 'put',
  });
}

/**
 * @description Cấu hình lưu trữ - Sửa domain không gian lưu trữ
 */
export function editStorageApi(id) {
  return request({
    url: `system/config/storage/domain/${id}`,
    method: 'get',
  });
}
/**
 * @description Cấu hình lưu trữ - Lấy ảnh thu nhỏ
 */
export function positionInfoApi() {
  return request({
    url: `setting/config_list/31`,
    method: 'get',
  });
}
/**
 * @description Cấu hình lưu trữ - Lưu ảnh thu nhỏ
 */
export function positionPostApi(data) {
  return request({
    url: `setting/config/save_basics`,
    method: 'post',
    data,
  });
}

/**
 * @description Chuyển đổi cấu hình lưu trữ
 */
export function saveType(type) {
  return request({
    url: `system/config/storage/save_type/${type}`,
    method: 'get',
  });
}

/**
 * @description Đa ngôn ngữ - Danh sách loại ngôn ngữ
 */
export function langTypeList(data) {
  return request({
    url: `setting/lang_type/list`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Đa ngôn ngữ - Thêm/sửa loại ngôn ngữ
 * @param {Number} param id {Number}
 */
export function langTypeForm(id) {
  return request({
    url: `setting/lang_type/form/${id}`,
    method: 'get',
  });
}

/**
 * @description Đa ngôn ngữ - Danh sách chi tiết ngôn ngữ
 */
export function langCodeList(data) {
  return request({
    url: `setting/lang_code/list`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Lấy thông tin ngôn ngữ
 */
export function langCodeInfo(data) {
  return request({
    url: `setting/lang_code/info`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Sửa chi tiết ngôn ngữ
 */
export function langCodeSettingSave(data) {
  return request({
    url: `setting/lang_code/save`,
    method: 'post',
    data,
  });
}

/**
 * @description Danh sách quốc gia
 */
export function langCountryList(data) {
  return request({
    url: `setting/lang_country/list`,
    method: 'get',
    params: data,
  });
}
/**
 * Biểu mẫu thêm khu vực ngôn ngữ
 * @param {*} id
 * @returns
 */
export function langCountryForm(id) {
  return request({
    url: `setting/lang_country/form/${id}`,
    method: 'get',
  });
}
/**
 * Biểu mẫu thêm khu vực ngôn ngữ
 * @param {*} id
 * @returns
 */
export function langTypeStatus(id, status) {
  return request({
    url: `setting/lang_type/status/${id}/${status}`,
    method: 'put',
  });
}

/**
 * @description Dịch tự động 1 chạm
 */
export function langCodeTranslate(data) {
  return request({
    url: `setting/lang_code/translate`,
    method: 'post',
    data,
  });
}

/**
 * @description Tạo mã nguồn
 */
export function codeCrud(data) {
  return request({
    url: `system/crud`,
    method: 'post',
    data,
  });
}
/**
 * @description Lấy liên kết tải lên bằng quét mã
 */
export function scanUploadQrcode(pid) {
  return request({
    url: `file/scan_upload/qrcode?pid=${pid}`,
    method: 'get',
  });
}
/**
 * @description Lấy ảnh tải lên bằng quét mã
 */
export function scanUploadGet(scan_token) {
  return request({
    url: `file/scan_upload/image/${scan_token}`,
    method: 'get',
  });
}
/**
 * @description Tải lên ảnh
 */
export function fileUpload(data) {
  return request({
    url: `file/upload`,
    method: 'post',
    headers: {
      'Authori-zation': 'Bearer ' + getCookies('token'),
      'content-type': 'multipart/form-data;' + 'Bearer ' + getCookies('token'),
    },
    data,
  });
}
/**
 * @description Tải ảnh lên bằng quét mã
 */
export function scanUpload(data) {
  return request({
    url: `image/scan_upload`,
    method: 'post',
    headers: {
      'content-type': 'multipart/form-data;',
    },
    data,
  });
}
/**
 * Tìm kiếm menu
 */
export function menusSearch(data) {
  return request({
    url: `menusSearch`,
    method: 'post',
    data,
  });
}

/**
 * Cấu hình menu PC
 * @param {*} data
 * @returns
 */
export function pcHomeMenusSave(data) {
  return request({
    url: `setting/group_data/save_all`,
    method: 'post',
    data,
  });
}

/**
 * Lấy cấu hình menu PC
 * @param {*} data
 * @returns
 */
export function pcHomeMenus(name) {
  return request({
    url: `setting/group_data?config_name=${name}`,
    method: 'get',
  });
}

/**
 * Danh sách máy in
 * @param {*} type
 * @returns
 */
export function printList(data) {
  return request({
    url: `/system/ticket/list`,
    method: 'get',
    params: data,
  });
}

/**
 * Tạo máy in
 * @param {*} type
 * @returns
 */
export function printForm(id) {
  return request({
    url: `/system/ticket/form/${id}`,
    method: 'get',
  });
}
/**
 * Chuyển trạng thái máy in
 * @param {*} type
 * @returns
 */
export function printSetStatus(data) {
  return request({
    url: `/system/ticket/set_status/${data.id}/${data.status}`,
    method: 'post',
  });
}

/**
 * Lưu cấu hình hóa đơn
 * @returns
 */
export function printSaveContent(id, data) {
  return request({
    url: `/system/ticket/save_content/${id}`,
    method: 'post',
    data,
  });
}
/**
 * Lấy cấu hình hóa đơn
 */
export function printContent(id) {
  return request({
    url: `/system/ticket/content/${id}`,
    method: 'get',
  });
}

/**
 * Danh mục danh sách liên kết
 * @param {*} type
 * @returns
 */
export function diyLinkCategoryListApi() {
  return request({
    url: `/diy/link/category`,
    method: 'get',
  });
}
/**
 * @description Thêm/sửa danh mục
 */
export function linkCategoryFormApi(cate_id, pid) {
  return request({
    url: `diy/link/category/form/${cate_id}/${pid}`,
    method: 'get',
  });
}
/**
 * @description Danh sách
 */
export function linkListApi(data) {
  return request({
    url: `diy/link/list/${data.id}`,
    method: 'get',
    params: data,
  });
}
/**
 * @description Tạo/sửa liên kết
 */
export function linkCreateApi(data) {
  return request({
    url: `diy/link/save/${data.id}`,
    method: 'post',
    data,
  });
}
