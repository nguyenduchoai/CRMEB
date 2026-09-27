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
export function adminListApi(data) {
  return request({
    url: '/setting/admin',
    method: 'get',
    params: data,
  });
}

/**
 * @description Form thêm quản trị viên
 */
export function adminFromApi() {
  return request({
    url: '/setting/admin/create',
    method: 'get',
  });
}

/**
 * @description Form sửa quản trị viên
 * @param {Number} param id {Number} ID quản trị viên
 */
export function adminEditFromApi(id) {
  return request({
    url: `/setting/admin/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Xóa quản trị viên
 * @param {Number} param id {Number} ID quản trị viên
 */
export function adminDelFromApi(id) {
  return request({
    url: `/setting/admin/${id}`,
    method: 'DELETE',
  });
}

/**
 * @description Quản trị viên, đổi trạng thái
 * @param {Object} param data {Object} Truyền giá trị
 */
export function setShowApi(data) {
  return request({
    url: `setting/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}
