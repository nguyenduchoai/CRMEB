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
 * @description Quản lý người dùng -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function userList(data) {
  return request({
    url: 'user/user',
    method: 'get',
    params: data,
  });
}

/**
 * @description Dữ liệu form sửa
 * @param {Number} param id {Number} ID thành viên
 */
export function getUserData(id) {
  return request({
    url: `user/user/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Công tắc
 * @param {Number} param id {Number}
 */
export function memberCard(data) {
  return request({
    url: `user/member_ship/set_ship_status`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Công tắc bật/tắt danh sách thành viên
 * @param {Number} param id {Number}
 */
export function memberCardStatus(data) {
  return request({
    url: `user/member_card/set_status`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Quản lý thành viên, sửa hiển thị
 * @param {Object} param data {Object} Giá trị trạng thái truyền vào, ID người dùng
 */
export function isShowApi(data) {
  return request({
    url: `user/set_status/${data.status}/${data.id}`,
    method: 'put',
  });
}

/**
 * @description Danh sách phiếu giảm giá
 * @param {Object} param params {Object} Truyền giá trị
 */
export function couponApi(params) {
  return request({
    url: `marketing/coupon/grant`,
    method: 'get',
    params,
  });
}

/**
 * @description Gửi phiếu giảm giá
 * @param {Object} param data {Object} Truyền giá trị
 */
export function sendCouponApi(data) {
  return request({
    url: `marketing/coupon/user/grant`,
    method: 'POST',
    data,
  });
}

/**
 * @description Biểu mẫu sửa điểm thưởng và số dư
 * @param {Number} param id {Number} id người dùng
 */
export function editOtherApi(id, type) {
  return request({
    url: `user/edit_other/${id}/${type}`,
    method: 'get',
  });
}

/**
 * @description Quản lý thành viên - Chi tiết
 * @param {Number} param id {Number} id người dùng
 */
export function detailsApi(id) {
  return request({
    url: `user/user/${id}`,
    method: 'get',
  });
}

/**
 * @description Tab trong chi tiết quản lý thành viên
 * @param {Number} param id {Number} id người dùng
 */
export function infoApi(data) {
  return request({
    url: `user/one_info/${data.id}`,
    method: 'get',
    params: data.datas,
  });
}

/**
 * @description Hạng thành viên - Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function levelListApi(data) {
  return request({
    url: 'user/user_level/vip_list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Hạng thành viên - Form sửa
 * @param {Number} param id {Number} ID hạng thành viên
 */
export function levelEditApi(id) {
  return request({
    url: `user/user_level/set_value/${id}`,
    method: 'PUT',
  });
}

/**
 * @description Hạng thành viên - Sửa hiện/ẩn
 * @param {Number} param id {Number} ID hạng thành viên
 */
export function setShowApi(data) {
  return request({
    url: `user/user_level/set_show/${data.id}/${data.is_show}`,
    method: 'PUT',
  });
}

/**
 * @description Hạng thành viên - Form sửa
 * @param {Number} param id {Number} ID hạng thành viên
 */
// export function addApi (data) {
//     return request({
//         url: 'user/user_level',
//         method: 'post',
//         data
//     });
// }

/**
 * @description Nhiệm vụ hạng thành viên - Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function taskListApi(id, data) {
  return request({
    url: `user/user_level/task/${id}`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Nhiệm vụ hạng thành viên - Sửa hiện/ẩn
 * @param {Number} param data.id {Number} ID nhiệm vụ hạng thành viên
 * @param {Number} param data.is_show {Number} Hiện/ẩn nhiệm vụ hạng thành viên
 */
export function setTaskShowApi(data) {
  return request({
    url: `user/user_level/set_task_show/${data.id}/${data.is_show}`,
    method: 'PUT',
  });
}

/**
 * @description Nhiệm vụ hạng thành viên - Nhiệm vụ có đạt không
 * @param {Number} param data.id {Number} ID nhiệm vụ hạng thành viên
 * @param {Number} param data.is_must {Number} Nhiệm vụ hạng thành viên có bắt buộc đạt không
 */
export function setTaskMustApi(data) {
  return request({
    url: `user/user_level/set_task_must/${data.id}/${data.is_must}`,
    method: 'PUT',
  });
}

/**
 * @description Nhiệm vụ hạng thành viên - Form tạo mới, form sửa
 * @param {Object} param data {Object} Truyền giá trị đối tượng nhiệm vụ hạng thành viên
 */
export function createTaskApi(data) {
  return request({
    url: `/user/user_level/create_task`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Hạng thành viên - Form tạo
 * @param {Object} param data {Object} Truyền giá trị đối tượng nhiệm vụ hạng thành viên
 */
export function createApi(id) {
  return request({
    url: `user/user_level/create`,
    method: 'get',
    params: id,
  });
}

/**
 * @description Quản lý thành viên --- Tặng hạng thành viên
 * @param {Number} param id {Number} ID thành viên
 */
export function giveLevelApi(id) {
  return request({
    url: `user/give_level/${id}`,
    method: 'get',
  });
}

/**
 * @description Quản lý thành viên --- Tặng thời hạn thành viên
 * @param {Number} param id {Number} ID thành viên
 */
export function giveLevelTimeApi(id) {
  return request({
    url: `user/give_level_time/${id}`,
    method: 'get',
  });
}

/**
 * @description Hạng thành viên - Xóa
 * @param {Number} param id {Number} ID hạng thành viên
 */
export function delLevelApi(id) {
  return request({
    url: `user/user_level/delete/${id}`,
    method: 'PUT',
  });
}

/**
 * @description Nhóm thành viên - Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function userGroupApi(data) {
  return request({
    url: 'user/user_group/list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Xóa thành viên --- Xóa nhóm
 * @param {Number} param id {Number} ID thành viên
 */
export function groupDelApi(id) {
  return request({
    url: `user/user_group/del/${id}`,
    method: 'DELETE',
  });
}

/**
 * @description Form thêm/xóa thành viên --- Form
 * @param {Number} param id {Number} ID thành viên
 */
export function groupAddApi(id) {
  return request({
    url: `user/user_group/add/${id}`,
    method: 'get',
  });
}

/**
 * @description Trang cá nhân --- Đổi mật khẩu
 * Tham số request data
 */
export function updtaeAdmin(data) {
  return request({
    url: `setting/update_admin`,
    method: 'PUT',
    data,
  });
}
/**
 * @description Quản lý file --- Thiết lập mật khẩu
 * Tham số request data
 */
export function setFilePassword(data) {
  return request({
    url: `setting/set_file_password`,
    method: 'PUT',
    data,
  });
}

/**
 * @description Trang cá nhân --- Thiết lập hạng thành viên
 * Tham số request data
 */
export function userSetGroup(data) {
  return request({
    url: `user/set_group`,
    method: 'post',
    data,
  });
}

/**
 * @description Trang cá nhân --- Danh sách nhãn thành viên
 * Tham số request data
 */
export function userLabelApi(data) {
  return request({
    url: `user/user_label`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Lấy danh mục nhãn (tất cả)
 * Tham số request data
 */
export function userLabelAll(data) {
  return request({
    url: `user/user_label_cate/all`,
    method: 'get',
    params: data,
  });
}

/**
 * Thêm người dùng
 */
export function getUserSaveForm() {
  return request({
    url: `/user/user/create`,
    method: 'get',
  });
}

/**
 * Đồng bộ người dùng
 */
export function userSynchro() {
  return request({
    url: `/user/user/syncUsers`,
    method: 'get',
  });
}

/**
 * @description Lấy form sửa danh mục nhãn người dùng
 * Tham số request data
 */
export function userLabelEdit(id) {
  return request({
    url: `user/user_label_cate/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Lấy form tạo danh mục nhãn người dùng
 * Tham số request data
 */
export function userLabelCreate(id) {
  return request({
    url: `user/user_label_cate/create`,
    method: 'get',
  });
}

/**
 * @description Trang cá nhân --- Sinh form nhãn thành viên
 * Tham số request data
 */
export function userLabelAddApi(id, cate_id) {
  return request({
    url: `user/user_label/add/${id}?cate_id=${cate_id ? cate_id : 0}`,
    method: 'get',
  });
}

/**
 * @description Trang cá nhân --- Lấy form thiết lập nhãn thành viên
 * Tham số request data
 */
export function userSetLabelApi(data) {
  return request({
    url: `user/set_label`,
    method: 'post',
    data,
  });
}

/**
 * Danh sách thẻ theo lô
 */
export function userMemberBatch(data) {
  return request({
    url: '/user/member_batch/index',
    method: 'get',
    params: data,
  });
}

/**
 * Tạo thẻ theo lô
 * @param {*} id id
 */
export function memberBatchSave(id, data) {
  return request({
    url: `/user/member_batch/save/${id}`,
    method: 'post',
    data,
  });
}

/**
 * Thao tác danh sách (kích hoạt, đổi tên)
 * @param {*} id id
 */
export function memberBatchSetValue(id, data) {
  return request({
    url: `/user/member_batch/set_value/${id}`,
    method: 'get',
    params: data,
  });
}

/**
 * Danh sách thẻ thành viên
 * @param {*} id id
 */
export function userMemberCard(id, data) {
  return request({
    url: `/user/member_card/index/${id}`,
    method: 'get',
    params: data,
  });
}

/**
 * Xuất thẻ thành viên
 * @param {*} id id
 */
export function exportMemberCard(id) {
  return request({
    url: `/export/memberCard/${id}`,
    method: 'get',
  });
}

/**
 * Loại thành viên
 */
export function userMemberShip() {
  return request({
    url: '/user/member/ship',
    method: 'get',
  });
}

/**
 * Sửa loại thành viên
 * @param {*} id id
 * @param {*} data data
 */
export function memberShipSave(id, data) {
  return request({
    url: `/user/member_ship/save/${id}`,
    method: 'post',
    data,
  });
}

/**
 * Mã QR đổi thẻ thành viên
 */
export function userMemberScan() {
  return request({
    url: '/user/member_scan',
    method: 'get',
  });
}

/**
 * Lịch sử thẻ thành viên
 */
export function memberRecord(data) {
  return request({
    url: '/user/member/record',
    method: 'get',
    params: data,
  });
}

/**
 * Quyền lợi thành viên
 */
export function memberRight() {
  return request({
    url: 'user/member/right',
    method: 'get',
  });
}

/**
 * Sửa quyền lợi thành viên
 * @param {*} data
 */
export function memberRightSave(data) {
  return request({
    url: `user/member_right/save/${data.id}`,
    method: 'post',
    data,
  });
}

/**
 * Sửa thỏa thuận thành viên
 * @param {*} id
 */
export function memberAgreementSave(id, data) {
  return request({
    url: `user/member_agreement/save/${id}`,
    method: 'post',
    data,
  });
}

/**
 * Thỏa thuận thành viên
 */
export function memberAgreement() {
  return request({
    url: `user/member/agreement`,
    method: 'get',
  });
}
/**
 * Thỏa thuận đăng ký đại lý
 */
export function agentAgreement() {
  return request({
    url: `agent/division/agent_agreement/info`,
    method: 'get',
  });
}

/**
 * Lưu thỏa thuận đại lý
 * @param {*} id
 */
export function agentAgreementSave(data) {
  return request({
    url: `agent/division/agent_agreement/save`,
    method: 'post',
    data,
  });
}

/**
 * Lấy nhãn người dùng
 */
export function getUserLabel(uid) {
  return request({
    url: `user/label/${uid}`,
    method: 'get',
  });
}

/**
 * Đặt nhãn người dùng
 */
export function putUserLabel(uid, data) {
  return request({
    url: `user/label/${uid}`,
    method: 'post',
    data,
  });
}

/**
 * @description Tạo người dùng
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function setUser(data) {
  return request({
    url: 'user/user',
    method: 'post',
    data,
  });
}

/**
 * @description Sửa người dùng
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function editUser(data) {
  return request({
    url: 'user/user/' + data.uid,
    method: 'put',
    data,
  });
}
/**
 * @description Sửa người dùng
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function saveSetLabel(data) {
  return request({
    url: 'user/save_set_label',
    method: 'put',
    data,
  });
}

/**
 * Lấy thông tin người dùng
 */
export function getUserInfo(uid) {
  return request({
    url: `user/user/user_save_info/${uid}`,
    method: 'get',
  });
}

/**
 * Danh sách hủy tài khoản
 */
export function userCancelList(data) {
  return request({
    url: '/user/cancel_list',
    method: 'get',
    params: data,
  });
}
/**
 * Danh sách hủy tài khoản
 */
export function userCancelSetMark(data) {
  return request({
    url: '/user/cancel/set_mark',
    method: 'post',
    data,
  });
}
