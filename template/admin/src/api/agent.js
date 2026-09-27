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
 * @description Tiếp thị liên kết -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function agentListApi(params) {
  return request({
    url: 'agent/index',
    method: 'get',
    params,
  });
}

/**
 * @description Sửa người giới thiệu (cấp trên)
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function agentSpreadApi(data) {
  return request({
    url: 'agent/spread',
    method: 'PUT',
    data,
  });
}

/**
 * @description Tiếp thị liên kết -- Tiêu đề bảng
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function statisticsApi(params) {
  return request({
    url: 'agent/statistics',
    method: 'get',
    params,
  });
}

/**
 * @description Tiếp thị liên kết -- Người giới thiệu, danh sách đơn hàng
 * @param {Object} param params {Object} Tham số truyền giá trị
 * @param {String} param url {String} Địa chỉ request
 */
export function stairListApi(url, params) {
  return request({
    url: url,
    method: 'get',
    params,
  });
}

/**
 * @description Tiếp thị liên kết -- Mã QR giới thiệu OA WeChat
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function lookCodeApi(params) {
  return request({
    url: 'agent/look_code',
    method: 'get',
    params,
  });
}

/**
 * @description Tiếp thị liên kết -- Mã QR giới thiệu Mini Program
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function lookxcxCodeApi(params) {
  return request({
    url: 'agent/look_xcx_code',
    method: 'get',
    params,
  });
}

/**
 * @description Tiếp thị liên kết -- Mã QR giới thiệu H5
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function lookh5CodeApi(params) {
  return request({
    url: 'agent/look_h5_code',
    method: 'get',
    params,
  });
}

/**
 * @description Tiếp thị liên kết -- Xuất danh sách giới thiệu của người dùng
 */
export function userAgentApi(data) {
  return request({
    url: `export/userAgent`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Đại lý khu vực -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function regionList(data) {
  return request({
    url: 'agent/division/list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Đăng ký đại lý -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function divisionList(data) {
  return request({
    url: 'agent/division/agent_apply/list',
    method: 'get',
    params: data,
  });
}
/**
 * @description Thống kê đại lý khu vực -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function divisionStatistics(data) {
  return request({
    url: 'agent/division/statistics',
    method: 'get',
    params: data,
  });
}

/**
 * @description Thêm đại lý -- Form
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function agentFrom(uid) {
  return request({
    url: `agent/division/agent/create/${uid}`,
    method: 'get',
  });
}

/**
 * @description Duyệt đại lý
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function divisionFrom(id, type) {
  return request({
    url: `agent/division/examine_apply/${id}/${type}`,
    method: 'get',
  });
}

/**
 * @description Thêm đại lý khu vực -- Form
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function regionFrom(uid) {
  return request({
    url: `agent/division/create/${uid}`,
    method: 'get',
  });
}
/**
 * @description Danh sách đại lý khu vực
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function clerkList(data) {
  return request({
    url: `agent/division/down_list`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Chuyển trạng thái đại lý khu vực -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function isShowApi(data) {
  return request({
    url: `agent/division/set_status/${data.status}/${data.id}`,
    method: 'put',
  });
}

/**
 * @description Thêm nhân viên -- Form
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function staffAddFrom(uid) {
  return request({
    url: `agent/division/staff/create/${uid}`,
    method: 'get',
  });
}

/**
 * @description Danh sách đăng ký -- Cộng tác viên
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function spreadList(data) {
  return request({
    url: 'agent/spread/apply/list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Duyệt -- Cộng tác viên
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function spreadFrom(id, uid, type, data) {
  return request({
    url: `agent/spread/apply/examine/${id}/${uid}/${type}`,
    method: 'post',
    data,
  });
}
