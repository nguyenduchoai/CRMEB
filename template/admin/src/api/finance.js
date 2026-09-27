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
 * @description Giám sát dòng tiền -- Loại lọc
 */
export function billTypeApi() {
  return request({
    url: 'finance/finance/bill_type',
    method: 'get',
  });
}

/**
 * @description Giám sát dòng tiền -- Danh sách
 * @param {Object} param data {Object} Truyền giá trị
 */
export function billListApi(data) {
  return request({
    url: 'finance/finance/list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Lịch sử hoa hồng -- Danh sách
 * @param {Object} param data {Object} Truyền giá trị
 */
export function commissionListApi(data) {
  return request({
    url: 'finance/finance/commission_list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Lịch sử hoa hồng -- Chi tiết
 * @param {Number} param id {Number} ID lịch sử hoa hồng
 */
export function commissionDetailApi(id) {
  return request({
    url: `finance/finance/user_info/${id}`,
    method: 'get',
  });
}

/**
 * @description Lịch sử hoa hồng -- Danh sách rút tiền cá nhân
 * @param {Number} param id {Number} Lịch sử hoa hồng, ID người dùng
 */
export function extractlistApi(id, data) {
  return request({
    url: `finance/finance/extract_list/${id}`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Yêu cầu rút tiền -- Danh sách
 * @param {Object} param data {Object} Truyền giá trị yêu cầu rút tiền
 */
export function cashListApi(data) {
  return request({
    url: `finance/extract`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Yêu cầu rút tiền -- Form sửa
 * @param {Number} param id {Number} ID yêu cầu rút tiền
 */
export function cashEditApi(id) {
  return request({
    url: `finance/extract/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Yêu cầu rút tiền -- Từ chối yêu cầu
 * @param {Number} param id {Number} ID yêu cầu rút tiền
 */
export function refuseApi(id, data) {
  return request({
    url: `finance/extract/refuse/${id}`,
    method: 'put',
    data,
  });
}

/**
 * @description Yêu cầu rút tiền -- Duyệt yêu cầu
 * @param {Number} param id {Number} ID yêu cầu rút tiền
 */
export function adoptApi(id, data) {
  return request({
    url: `finance/extract/adopt/${id}`,
    method: 'put',
    data,
  });
}

/**
 * @description Lịch sử nạp tiền -- Danh sách
 * @param {Object} param data {Object} Truyền giá trị lịch sử nạp tiền
 */
export function rechargelistApi(data) {
  return request({
    url: `finance/recharge`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Lịch sử nạp tiền -- Dữ liệu nạp tiền của người dùng
 * @param {Object} param data {Object} Truyền giá trị dữ liệu nạp tiền người dùng
 */
export function userRechargeApi(data) {
  return request({
    url: `finance/recharge/user_recharge`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Lịch sử nạp tiền -- Form hoàn tiền
 * @param {Number} param data {Number} ID lịch sử nạp tiền
 */
export function refundEditApi(id) {
  return request({
    url: `finance/recharge/${id}/refund_edit`,
    method: 'get',
  });
}

/**
 * @description Ghi chép tài chính -- Xuất dữ liệu tiền của người dùng
 * @param {Number} param data {Number} Tham số request data
 */
export function userFinanceApi(data) {
  return request({
    url: `export/userFinance`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Lịch sử hoa hồng -- Xuất hoa hồng người dùng
 * @param {Number} param data {Number} Tham số request data
 */
export function userCommissionApi(data) {
  return request({
    url: `export/userCommission`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Lịch sử nạp tiền người dùng -- Xuất lịch sử nạp tiền người dùng
 * @param {Number} param data {Number} Tham số request data
 */
export function exportUserRechargeApi(data) {
  return request({
    url: `export/userRecharge`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Quản lý tài chính -- Thống kê dòng tiền
 * @param {Number} param data {Number} Tham số request data
 */
export function getFlowList(data) {
  return request({
    url: `statistic/flow/get_list`,
    method: 'get',
    params: data,
  });
}
/**
 * @description Dòng tiền -- Ghi chú
 * @param {Number} param id {Number} ID yêu cầu rút tiền
 */
export function setMarks(id, data) {
  return request({
    url: `statistic/flow/set_mark/${id}`,
    method: 'post',
    data,
  });
}
/**
 * @description Quản lý tài chính -- Danh sách số dư
 * @param {Number} param data {Number} Tham số request data
 */
export function getBalanceList(data) {
  return request({
    url: `finance/balance/list`,
    method: 'get',
    params: data,
  });
}
/**
 * @description Danh sách số dư -- Ghi chú
 * @param {Number} balanceMark id {Number} ID yêu cầu rút tiền
 */
export function setBalanceMark(id, data) {
  return request({
    url: `finance/balance/set_mark/${id}`,
    method: 'post',
    data,
  });
}
