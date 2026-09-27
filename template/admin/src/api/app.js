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

/**
 * @description Tin nhắn mẫu Mini Program -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function routineListApi(data) {
  return request({
    url: 'app/routine',
    method: 'get',
    params: data,
  });
}

/**
 * @description  Đồng bộ tin nhắn đăng ký
 */
export function routineSyncTemplate() {
  return request({
    url: `app/routine/syncSubscribe`,
    method: 'GET',
  });
}

/**
 * @description  Đồng bộ tin nhắn mẫu WeChat
 */
export function wechatSyncTemplate() {
  return request({
    url: `app/wechat/syncSubscribe`,
    method: 'GET',
  });
}

/**
 * @description Tin nhắn mẫu Mini Program -- Form thêm mới
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function routineCreateApi() {
  return request({
    url: 'app/routine/create',
    method: 'get',
  });
}

/**
 * @description Tin nhắn mẫu Mini Program -- Form sửa
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function routineEditApi(id) {
  return request({
    url: `app/routine/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Tin nhắn mẫu Mini Program -- Đổi trạng thái
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function routineSetStatusApi(data) {
  return request({
    url: `app/routine/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}

/**
 * @description OA WeChat -- Cấu hình OA WeChat -- Menu WeChat
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function wechatMenuApi(data) {
  return request({
    url: `app/wechat/menu`,
    method: 'get',
  });
}

/**
 * @description OA WeChat -- Cấu hình OA WeChat -- Submit menu WeChat
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function MenuApi(data) {
  return request({
    url: `app/wechat/menu`,
    method: 'post',
    data,
  });
}

/**
 * @description Tin nhắn mẫu WeChat -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function wechatListApi(data) {
  return request({
    url: 'app/wechat/template',
    method: 'get',
    params: data,
  });
}
/**
 * @description Tin nhắn mẫu WeChat -- Form thêm mới
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function wechatCreateApi() {
  return request({
    url: 'app/wechat/template/create',
    method: 'get',
  });
}

/**
 * @description Tin nhắn mẫu WeChat -- Form sửa
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function wechatEditApi(id) {
  return request({
    url: `app/wechat/template/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Tin nhắn mẫu WeChat -- Đổi trạng thái
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function wechatSetStatusApi(data) {
  return request({
    url: `app/wechat/template/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}

/**
 * @description  Tự động trả lời -- Trả lời khi follow, trả lời theo từ khóa, lưu
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function replyApi(data) {
  return request({
    url: data.url,
    method: 'post',
    data: data.key,
  });
}
/**
 * @description  Tải gói Mini Program
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function routineDownload(data) {
  return request({
    url: 'app/routine/download',
    method: 'post',
    data,
  });
}
/**
 * @description  Dữ liệu trang tải Mini Program
 */
export function routineInfo() {
  return request({
    url: 'app/routine/info',
    method: 'get',
  });
}

// ==================== Tự động tải lên Mini Program qua CI ====================

/**
 * @description Lấy trạng thái môi trường chạy CI của Mini Program
 */
export function routineCIEnvironment() {
  return request({
    url: 'app/routine/ci/environment',
    method: 'get',
  });
}

/**
 * @description Lấy hướng dẫn cài đặt môi trường
 */
export function routineCIGuide() {
  return request({
    url: 'app/routine/ci/guide',
    method: 'get',
  });
}

/**
 * @description Lấy cấu hình tải lên Mini Program
 */
export function routineCIConfig() {
  return request({
    url: 'app/routine/ci/config',
    method: 'get',
  });
}

/**
 * @description Lưu khóa tải lên Mini Program
 * @param {Object} data { key_content: nội dung khóa }
 */
export function routineCISaveKey(data) {
  return request({
    url: 'app/routine/ci/private_key',
    method: 'post',
    data,
  });
}

/**
 * @description Tải lên mã nguồn Mini Program
 * @param {Object} data { version: số phiên bản, desc: mô tả, is_live: có bật livestream không }
 */
export function routineCIUpload(data) {
  return request({
    url: 'app/routine/ci/upload',
    method: 'post',
    data,
  });
}

/**
 * @description Lấy mã QR xem trước Mini Program
 * @param {Object} data { page_path: đường dẫn trang xem trước }
 */
export function routineCIPreview(data) {
  return request({
    url: 'app/routine/ci/preview',
    method: 'post',
    data,
  });
}

/**
 * @description  Tự động trả lời -- Từ khóa, danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function keywordListApi(params) {
  return request({
    url: `app/wechat/keyword`,
    method: 'get',
    params,
  });
}

/**
 * @description  Tự động trả lời -- Từ khóa, đổi trạng thái
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function keywordsetStatusApi(data) {
  return request({
    url: `app/wechat/keyword/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}

/**
 * @description  Tự động trả lời -- Chi tiết
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function keywordsinfoApi(url, data) {
  return request({
    url: url,
    method: 'get',
    params: data.key,
  });
}

/**
 * @description  Quản lý bài viết ảnh-văn -- Thêm mới
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function wechatNewsAddApi(data) {
  return request({
    url: `/app/wechat/news`,
    method: 'POST',
    data,
  });
}

/**
 * @description  Quản lý bài viết ảnh-văn -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function wechatNewsListApi(params) {
  return request({
    url: `app/wechat/news`,
    method: 'GET',
    params,
  });
}

/**
 * @description  Quản lý bài viết ảnh-văn -- Chi tiết
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function wechatNewsInfotApi(id) {
  return request({
    url: `app/wechat/news/${id}`,
    method: 'GET',
  });
}

/**
 * @description  Quản lý bài viết ảnh-văn -- Gửi bài viết
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function wechatPushApi(data) {
  return request({
    url: `app/wechat/push`,
    method: 'POST',
    data,
  });
}

/**
 * @description  Người dùng WeChat -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function wechatUserListtApi(params) {
  return request({
    url: `app/wechat/user`,
    method: 'GET',
    params,
  });
}

/**
 * @description  Người dùng WeChat -- Nhóm và nhãn người dùng
 */
export function tagListtApi() {
  return request({
    url: `app/wechat/user/tag_group`,
    method: 'GET',
  });
}

/**
 * @description  Người dùng WeChat -- Sửa nhóm và nhãn người dùng
 * @param {String} param url {String} Địa chỉ request
 */
export function groupsEditApi(url) {
  return request({
    url: url,
    method: 'GET',
  });
}

/**
 * @description  Nhãn người dùng -- Danh sách
 */
export function wechatTagListApi() {
  return request({
    url: `app/wechat/tag`,
    method: 'GET',
  });
}

/**
 * @description  Nhãn người dùng -- Form thêm
 */
export function wechatTagCreateApi() {
  return request({
    url: `app/wechat/tag/create`,
    method: 'GET',
  });
}

/**
 * @description  Nhãn người dùng -- Form sửa
 *  @param {Number} param id {Number} ID nhãn
 */
export function wechatTagEditApi(id) {
  return request({
    url: `app/wechat/tag/${id}/edit`,
    method: 'GET',
  });
}

/**
 * @description  Nhóm người dùng -- Danh sách
 */
export function wechatGroupListApi() {
  return request({
    url: `app/wechat/group`,
    method: 'GET',
  });
}

/**
 * @description  Nhóm người dùng -- Form thêm
 */
export function wechatGroupCreateApi() {
  return request({
    url: `app/wechat/group/create`,
    method: 'GET',
  });
}

/**
 * @description  Nhóm người dùng -- Form sửa
 *  @param {Number} param id {Number} ID nhãn
 */
export function wechatGroupEditApi(id) {
  return request({
    url: `app/wechat/group/${id}/edit`,
    method: 'GET',
  });
}

/**
 * @description  Hành vi người dùng -- Danh sách
 */
export function wechatActionListApi(params) {
  return request({
    url: `app/wechat/action`,
    method: 'GET',
    params,
  });
}

/**
 * Tải xuống mã QR
 * @param id
 */
export function downloadReplyCode(id) {
  return request({
    url: `app/wechat/code_reply/${id}`,
    method: 'GET',
  });
}

/**
 * Danh sách thành phố
 */
export function cityList() {
  return request({
    url: `setting/city/full_list`,
    method: 'GET',
  });
}

/**
 * @description  CSKH tự động trả lời -- Từ khóa, danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function kefuAutoReplyListApi(params) {
  return request({
    url: `app/kefu/auto_reply/list`,
    method: 'get',
    params,
  });
}

/**
 * @description  Form thêm/sửa tự động trả lời CSKH
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function kefuAutoReplyForm(id) {
  return request({
    url: `app/kefu/auto_reply/form/` + id,
    method: 'get',
  });
}

/**
 * @description Liên kết Mini Program -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function routineSchemeList(data) {
  return request({
    url: 'app/routine/scheme_list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Liên kết Mini Program -- Form tạo/sửa
 * @param {Number} param id {Number} ID nhãn
 */
export function routineSchemeForm(id) {
  return request({
    url: `app/routine/scheme_form/${id}`,
    method: 'get',
  });
}

/**
 * @description Liên kết Mini Program -- Xóa
 * @param {Number} param id {Number} ID nhãn
 */
export function routineSchemeDel(id) {
  return request({
    url: `app/routine/scheme_del/${id}`,
    method: 'delete',
  });
}
