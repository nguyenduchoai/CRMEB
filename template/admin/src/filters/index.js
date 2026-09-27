// +---------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +---------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +---------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

// import parseTime, formatTime and set to filter
/**
 * Trạng thái livestream
 * @param {String} value
 */
export function liveReviewStatusFilter(value) {
  const statusMap = {
    101: 'Đang livestream',
    102: 'Chưa bắt đầu',
    103: 'Đã kết thúc',
    104: 'Đã kết thúc',
    105: 'Đang livestream',
    106: 'Đang livestream',
    107: 'Đã kết thúc',
  };
  return statusMap[value];
}

/**
 * Trạng thái duyệt
 * @param {String} value
 */
export function liveStatusFilter(value) {
  const statusMap = {
    0: 'Chưa duyệt ',
    1: 'Đang duyệt',
    2: 'Đã duyệt',
    3: 'Duyệt thất bại',
  };
  return statusMap[value];
}

/**
 * Chuyển timestamp thành thời gian
 * @param {String} data
 */
export function formatDate(data) {
  let date = new Date(data);
  let YY = date.getFullYear() + '-';
  let MM = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
  let DD = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
  let hh = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
  let mm = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
  let ss = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();
  return YY + MM + DD + ' ' + hh + mm + ss;
}

/**
 * @description Loại phòng livestream
 */
export function broadcastType(type) {
  const typeMap = {
    0: 'Livestream bằng điện thoại',
    1: 'Đẩy luồng',
  };
  return typeMap[type];
}

/**
 * @description Có tắt lượt thích, bình luận không
 */
export function filterClose(value) {
  return value ? '✔' : '✖';
}

/**
 * @description Loại hiển thị livestream
 */
export function broadcastDisplayType(type) {
  const typeMap = {
    0: 'Màn hình dọc',
    1: 'Màn hình ngang',
  };
  return typeMap[type];
}

// Filter chung
export function filterEmpty(val) {
  let _result = '-';
  if (!val) {
    return _result;
  }
  _result = val;
  return _result;
}

/**
 * @description Loại người dùng
 */
export function userType(type) {
  const typeMap = {
    routine: 'Mini Program',
    'wechat ': 'WeChat',
    h5: 'H5',
  };
  return typeMap[type];
}

/**
 * @description Loại nguồn truy cập
 */
export function sourceType(type) {
  const typeMap = {
    0: 'PC',
    1: 'OA WeChat',
    2: 'Mini Program',
    3: 'H5',
  };
  return typeMap[type];
}
