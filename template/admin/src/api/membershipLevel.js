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
 * @param {Number} param id {Number} ID dữ liệu tổ hợp
 */
export function membershipDataListApi(data) {
  return request({
    url: 'agent/level',
    // url: `setting/group_data`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Danh sách dữ liệu tổ hợp -- Form sửa
 * @param {Number} param id {Number} ID danh sách dữ liệu tổ hợp
 * @param {Object} param data {Object} Đối tượng ID dữ liệu tổ hợp
 */
export function membershipDataEditApi(data, url) {
  return request({
    url: url,
    method: 'get',
    params: data,
  });
}

/**
 * @description Danh sách dữ liệu tổ hợp -- Form thêm mới
 * @param {Number} param id {Number} ID dữ liệu tổ hợp
 */
export function membershipDataAddApi(id, url) {
  return request({
    url: url,
    // url: `setting/group_data/create`,
    method: 'get',
    params: id,
  });
}
/**
 * @description Cấu hình nhiệm vụ tiếp thị liên kết
 * @param {Number} param id {Number} ID dữ liệu tổ hợp
 */
export function getTaskNumFormApi(id) {
  return request({
    url: `agent/get_task_num_form/${id}`,
    method: 'get',
  });
}
/**
 * @description Danh sách dữ liệu tổ hợp -- Đổi trạng thái
 * @param {Object} param data {Object} Truyền giá trị danh sách dữ liệu tổ hợp
 */
export function membershipSetApi(url) {
  return request({
    url: url,
    // url: `/setting/group_data/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}

/**
 * @description Danh sách dữ liệu tổ hợp -- Đổi trạng thái
 * @param {Object} param data {Object} Truyền giá trị danh sách dữ liệu tổ hợp
 */
export function levelTaskSetApi(url) {
  return request({
    url: url,
    // url: `/setting/group_data/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}

/**
 * @description Danh sách nhiệm vụ theo hạng
 * @param {Number} param id {Number} ID dữ liệu tổ hợp
 */
export function levelTaskListDataAddApi(data) {
  return request({
    url: 'agent/level_task',
    // url: `setting/group_data`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Danh sách dữ liệu tổ hợp -- Form sửa
 * @param {Number} param id {Number} ID danh sách dữ liệu tổ hợp
 * @param {Object} param data {Object} Đối tượng ID dữ liệu tổ hợp
 */
export function levelTaskDataEditApi(data, url) {
  return request({
    url: url,
    method: 'get',
    params: data,
  });
}

/**
 * @description Danh sách dữ liệu tổ hợp -- Form thêm mới
 * @param {Number} param id {Number} ID dữ liệu tổ hợp
 */
export function levelTaskDataAddApi(id, url) {
  return request({
    url: url,
    // url: `setting/group_data/create`,
    method: 'get',
    params: id,
  });
}
