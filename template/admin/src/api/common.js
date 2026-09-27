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

export function ajCaptcha(params) {
  return request({
    url: 'ajcaptcha',
    method: 'get',
    params: params,
  });
}

export function ajCaptchaCheck(data) {
  return request({
    url: 'ajcheck',
    method: 'post',
    data: data,
  });
}

/**
 * @description Bảng -- Xóa
 * @param {Number} param id {Number} ID cấu hình
 */
export function tableDelApi(data) {
  return request({
    url: data.url,
    method: data.method,
    data: data.ids,
    kefu: data.kefu || '',
  });
}

/**
 * Lấy thông báo nhắc nhở
 */
export function jnoticeRequest() {
  return request({
    url: 'jnotice',
    method: 'GET',
  });
}

/**
 * Lấy logo
 */
export function getLogo() {
  return request({
    url: 'logo',
    method: 'GET',
  });
}
