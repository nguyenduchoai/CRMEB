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
 * @description Quản lý bài viết -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function cmsListApi(data) {
  return request({
    url: 'cms/cms',
    method: 'get',
    params: data,
  });
}

/**
 * @description Quản lý bài viết -- Thêm/sửa
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function cmsAddApi(data) {
  return request({
    url: 'cms/cms',
    method: 'post',
    data,
  });
}

/**
 * @description Quản lý bài viết -- Chi tiết bài viết
 * @param {Number} param id {Number} ID bài viết
 */
export function createApi(id) {
  return request({
    url: `cms/cms/${id}`,
    method: 'get',
  });
}

/**
 * @description Danh mục bài viết -- Form thêm mới
 */
export function categoryAddApi() {
  return request({
    url: `cms/category/create`,
    method: 'GET',
  });
}

/**
 * @description Danh mục bài viết -- Danh sách
 * @param {Object} param params {Object} Truyền giá trị
 */
export function categoryListApi(params) {
  return request({
    url: `cms/category`,
    method: 'GET',
    params,
  });
}
/**
 * @description Danh mục bài viết -- Danh sách bản mới
 * @param {Object} param params {Object} Truyền giá trị
 */
export function categoryTreeListApi() {
  return request({
    url: `cms/category_tree_list`,
    method: 'GET',
  });
}

/**
 * @description Danh mục bài viết -- Form sửa
 * @param {Number} param id {Number} ID bài viết
 */
export function categoryEditApi(id) {
  return request({
    url: `cms/category/${id}/edit`,
    method: 'GET',
  });
}

/**
 * @description Danh mục bài viết -- Đổi trạng thái
 * @param {Object} param data {Object} Truyền giá trị
 */
export function statusApi(data) {
  return request({
    url: `cms/category/set_status/${data.id}/${data.status}`,
    method: 'put',
  });
}

/**
 * @description Danh mục bài viết -- Liên kết sản phẩm
 * @param {Number} param id {Number} ID bài viết
 * @param {Object} param data {Object} Truyền giá trị
 */
export function relationApi(data, id) {
  return request({
    url: `cms/cms/relation/${id}`,
    method: 'put',
    data,
  });
}
