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
 * @description Lấy dữ liệu danh sách quản lý tin nhắn
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function getNotificationList(type) {
  return request({
    url: `setting/notification/index?type=${type}`,
    method: 'get',
  });
}
/**
 * @description Lấy dữ liệu thiết lập quản lý tin nhắn
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function getNotificationInfo(id, type) {
  return request({
    url: `setting/notification/info?id=${id}&type=${type}`,
    method: 'get',
  });
}

/**
 * @description Lấy dữ liệu thiết lập quản lý tin nhắn
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function getNotificationSave(data) {
  return request({
    url: `setting/notification/save`,
    method: 'post',
    data,
  });
}

/**
 * @description Thiết lập thông báo nội bộ
 * @param {Number} param id {Number}
 */
export function noticeStatus(type, status, id) {
  return request({
    url: `setting/notification/set_status/${type}/${status}/${id}`,
    method: 'put',
  });
}

/**
 * @description Form thêm/sửa tin nhắn
 * @param {Number} param id {Number} Tham số truyền giá trị
 */
export function notificationForm(id) {
  return request({
    url: `setting/notification/not_form/${id}`,
    method: 'get',
  });
}
