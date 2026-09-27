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
 * @description Danh mục tệp đính kèm -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getCategoryListApi(data) {
  return request({
    url: 'file/category',
    method: 'get',
    params: data,
  });
}

/**
 * @description Thêm danh mục
 */
export function createApi(id) {
  return request({
    url: 'file/category/create',
    method: 'get',
    params: id,
  });
}

/**
 * @description Sửa danh mục
 * @param {Number} param id {Number} ID danh mục
 */
export function categoryEditApi(id) {
  return request({
    url: `file/category/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Xóa danh mục
 * @param {Number} param id {Number} ID danh mục
 */
export function categoryDelApi(id) {
  return request({
    url: `file/category/${id}`,
    method: 'DELETE',
  });
}

/**
 * @description Danh sách tệp đính kèm
 * @param {Object} param data {Object} Truyền giá trị
 */
export function fileListApi(data) {
  return request({
    url: 'file/file',
    method: 'get',
    params: data,
  });
}

/**
 * @description Di chuyển danh mục, form sửa danh mục tệp đính kèm
 * @param {Object} param data {Object} Truyền giá trị
 */
export function moveApi(data) {
  return request({
    url: 'file/file/do_move',
    method: 'put',
    data,
  });
}

/**
 * @description Sửa tên tệp đính kèm
 * @param {String} param ids {String} Chuỗi ghép từ các ID hình ảnh
 */
export function fileUpdateApi(ids, data) {
  return request({
    url: 'file/file/update/' + ids,
    method: 'put',
    data,
  });
}

/**
 * @description Xóa tệp đính kèm
 * @param {String} param ids {String} Chuỗi ghép từ các ID hình ảnh
 */
export function fileDelApi(ids) {
  return request({
    url: 'file/file/delete',
    method: 'post',
    data: ids,
  });
}
/**
 * @description Tải lên ảnh từ mạng
 */
export function onlineUpload(data) {
  return request({
    url: 'file/online_upload',
    method: 'post',
    data,
  });
}

/**
 * @description Xóa code quét mã tải lên
 */
export function scanUploadCode() {
  return request({
    url: 'file/scan_upload/qrcode ',
    method: 'delete',
  });
}

/**
 * @description Quản lý tư liệu - Tải video lên
 */
export function videoCloudUpload(data) {
  return request({
    url: 'file/video_data_save',
    method: 'post',
    data,
  });
}