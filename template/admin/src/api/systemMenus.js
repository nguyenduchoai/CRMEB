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
 * @description Quyền -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getTable(data) {
  return request({
    url: '/setting/menus',
    method: 'get',
    params: data,
  });
}
/**
 * @description Quyền -- Làm mới menu và quyền
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getMenusUnique(data) {
  return request({
    url: '/setting/menus/unique',
    method: 'get',
    params: data,
  });
}

/**
 * Quyền -- Thêm
 */
export function addMenus() {
  return request({
    url: '/setting/menus/create',
    method: 'get',
  });
}

/**
 * Quyền -- Sửa
 * @param id
 */
export function editMenus(id) {
  return request({
    url: '/setting/menus/' + id + '/edit',
    method: 'get',
  });
}

/**
 * @description Thêm, sửa
 * @param {Object} param data {Object} Tập hợp (collection)
 * @param {String} param data.url {String} Địa chỉ
 * @param {String} param data.method {String} Phương thức yêu cầu
 * @param {Object} param data.datas {Object} Tham số truyền giá trị
 */
export function addMenusApi(data) {
  return request({
    url: data.url,
    method: data.method,
    data: data.datas,
  });
}

/**
 * @description Chi tiết form
 * @param {Number} param id {Number} ID quy tắc
 */
export function menusDetailsApi(id) {
  return request({
    url: `/setting/menus/${id}`,
    method: 'get',
  });
}

/**
 * @description Sửa hiển thị
 * @param {Number} param data.id {Number} ID quy tắc
 * @param {Number} param data.is_show {Number} Giá trị trạng thái
 */
export function isShowApi(data) {
  return request({
    url: `/setting/menus/show/${data.id}`,
    method: 'put',
    data,
  });
}

/**
 * @description Danh sách quyền
 */
export function getRuleList(cate_id) {
  return request({
    url: `/setting/ruleList?cate_id=${cate_id}`,
    method: 'get',
  });
}
/**
 * @description Danh sách quyền
 */
export function menusBatch(data) {
  return request({
    url: `setting/menus/batch`,
    method: 'post',
    data,
  });
}

/**
 * @description Danh sách cây danh mục quyền
 */
export function menusRuleCate(data) {
  return request({
    url: `setting/rule_cate`,
    method: 'get',
  });
}
