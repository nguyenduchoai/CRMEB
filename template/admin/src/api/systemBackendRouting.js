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
 * Đồng bộ quyền route
 */
export function syncRoute(appName) {
  return request({
    url: `system/route/sync_route/${appName}`,
    method: 'get',
  });
}
/**
 * Thêm danh mục route
 */
export function routeCate(appName) {
  return request({
    url: `system/route_cate/create?app_name=${appName}`,
    method: 'get',
  });
}
/**
 * Cây route
 */
export function routeList(apiType) {
  return request({
    url: `system/route/tree?app_name=${apiType}`,
    method: 'get',
  });
}

/**
 * Thêm/sửa API
 * @param {*} data
 * @returns
 */
export function routeSave(data) {
  return request({
    url: `system/route/${data.id}`,
    method: 'post',
    data,
  });
}

/**
 * Chi tiết thông tin API
 * @param {*} data
 * @returns
 */
export function routeDet(id) {
  return request({
    url: `system/route/${id}`,
    method: 'get',
  });
}
/**
 * Sửa danh mục API
 * @param {*} data
 * @returns
 */
export function routeEdit(id, appName) {
  return request({
    url: `system/route_cate/${id}/edit?app_name=${appName}`,
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
export function routeDel(id) {
  return request({
    url: 'system/route/' + id,
    method: 'delete',
  });
}
/**
 * @description Xóa
 */
export function routeCateDel(id) {
  return request({
    url: 'system/route_cate/' + id,
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
