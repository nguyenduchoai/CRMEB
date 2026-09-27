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
 * @description Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function accountListApi(data) {
  return request({
    url: '/setting/system_out_account/index',
    method: 'get',
    params: data,
  });
}

/**
 * @description Tài khoản đối ngoại, đổi trạng thái
 * @param {Object} param data {Object} Truyền giá trị
 */
export function setShowApi(data) {
  return request({
    url: `setting/system_out_account/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}

/**
 * @description Thêm tài khoản API bên ngoài
 * @param {Object} param data {Object} Truyền giá trị
 */
export function outSaveApi(data) {
  return request({
    url: `setting/system_out_account/save`,
    method: 'post',
    data,
  });
}

/**
 * @description Sửa tài khoản đối ngoại
 * @param {Object} param id {Number} ID tài khoản
 * @param {Object} param data {Object} Truyền giá trị
 */
export function outSavesApi(data) {
  return request({
    url: `setting/system_out_account/update/${data.id}`,
    method: 'post',
    data,
  });
}

/**
 * Tài khoản đối ngoại thiết lập push
 * @param {*} id
 * @returns
 */
export function outSetUp(id) {
  return request({
    url: `setting/system_out_account/set_up/${id}`,
    method: 'get',
  });
}

/**
 * Danh sách API bên ngoài
 */
export function interfaceList() {
  return request({
    url: `setting/system_out_interface/list`,
    method: 'get',
  });
}

/**
 * Thiết lập thông tin push
 * @param {*} data
 * @returns
 */
export function setUpPush(data) {
  return request({
    url: `setting/system_out_account/set_up/${data.id}`,
    method: 'put',
    data,
  });
}
/**
 * Thêm/sửa API
 * @param {*} data
 * @returns
 */
export function interfaceSave(data) {
  return request({
    url: `setting/system_out_interface/save/${data.id}`,
    method: 'post',
    data,
  });
}

/**
 * Chi tiết thông tin API
 * @param {*} data
 * @returns
 */
export function interfaceDet(id) {
  return request({
    url: `setting/system_out_interface/info/${id}`,
    method: 'get',
  });
}

/**
 * @description Sửa tên
 * @param {Object} data data {Object} Truyền giá trị
 */
export function interfaceEditName(data) {
  return request({
    url: `setting/system_out_interface/edit_name`,
    method: 'PUT',
    data,
  });
}

/**
 * @description Xóa
 */
export function interfaceDel(id) {
  return request({
    url: 'setting/system_out_interface/del/' + id,
    method: 'delete',
  });
}

/**
 * Chi tiết thông tin API
 * @param {*} data
 * @returns
 */
export function textOutUrl(data) {
  return request({
    url: `setting/system_out_account/text_out_url`,
    method: 'post',
    data,
  });
}
