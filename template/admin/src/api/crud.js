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
 * @description Lấy cấu hình API
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function crudApi(table_name) {
  return request({
    url: `system/crud/config/${table_name}`,
    method: 'get',
  });
}

/**
 * @description API danh sách
 */
export function getList(url, params) {
  return request({
    url: url,
    method: 'get',
    params,
  });
}
/**
 * @description Tạo API
 */
export function getCreateApi(url) {
  return request({
    url: url,
    method: 'get',
  });
}

export function getStatusApi(url, data) {
  return request({
    url: url,
    method: 'put',
    data,
  });
}
/**
 * @description Tạo API
 */
export function getEditApi(url) {
  return request({
    url: url,
    method: 'get',
  });
}
