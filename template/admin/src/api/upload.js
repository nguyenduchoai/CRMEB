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
 * @description Tải lên
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function upload(data, config) {
  return request({
    url: 'file/video_upload',
    method: 'post',
    file: true,
    data,
  });
}
/**
 * @description Tải lên bằng url cloud storage
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function ossUpload(url, data) {
  return request({
    url,
    method: 'post',
    file: true,
    data,
  });
}
