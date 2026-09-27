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
 * @description Sinh mã (code generation) - Danh sách chọn menu
 */
export function crudMenus() {
  return request({
    url: '/system/crud/menus',
    method: 'get',
  });
}
/**
 * @description Sinh mã - Danh sách chọn bảng sql
 */
export function crudColumnType() {
  return request({
    url: '/system/crud/column_type',
    method: 'get',
  });
}
/**
 * @description Sinh mã - Submit bước 1
 */
export function crudFilePath(data) {
  return request({
    url: '/system/crud/file_path',
    method: 'post',
    data,
  });
}

/**
 * @description Sinh mã - Danh sách
 */
export function crudList(data) {
  return request({
    url: '/system/crud',
    method: 'get',
    params: data,
  });
}
/**
 * @description Sinh mã - Danh sách xem file
 */
export function crudDet(id) {
  return request({
    url: `/system/crud/${id}`,
    method: 'get',
  });
}

/**
 * @description Sinh mã - Tải xuống
 */
export function crudDownload(id) {
  return request({
    url: `/system/crud/download/${id}`,
    method: 'get',
  });
}
/**
 * @description Danh sách từ điển dữ liệu
 */
export function crudDataDictionary(where) {
  return request({
    url: `/system/crud/data_dictionary`,
    method: 'get',
    params: where,
  });
}
/**
 * @description Lấy tên các bảng có thể liên kết
 */
export function crudAssociationTable() {
  return request({
    url: `/system/crud/association_table`,
    method: 'get',
  });
}
/**
 * @description Lấy thông tin chi tiết của bảng
 */
export function crudAssociationTableName(tableName) {
  return request({
    url: `/system/crud/association_table/${tableName}`,
    method: 'get',
  });
}
/**
 * @description Xem từ điển dữ liệu
 */
export function crudDataDictionaryList(id) {
  return request({
    url: `/system/crud/data_dictionary/${id}`,
    method: 'get',
  });
}
/**
 * @description Lưu từ điển dữ liệu
 */
export function saveCrudDataDictionaryList(id, data) {
  return request({
    url: `/system/crud/data_dictionary/${id}`,
    method: 'post',
    data,
  });
}
/**
 * @description Sinh mã - Sửa file
 */
export function crudSaveFile(id, data) {
  return request({
    url: `/system/crud/save_file/${id}`,
    method: 'post',
    data,
  });
}

/**
 * @description Lấy danh sách từ điển dữ liệu
 */
export function getDataDictionaryList(data) {
  return request({
    url: `/system/crud/data_dictionary_list`,
    method: 'get',
    params: data,
  });
}
/**
 * @description Lấy biểu mẫu thêm/sửa từ điển dữ liệu
 */
export function getDataDictionaryForm(id) {
  return request({
    url: `/system/crud/data_dictionary_list/create/${id}`,
    method: 'get',
  });
}

/**
 * @description Xem danh sách nội dung từ điển dữ liệu
 */
export function getDataDictionaryInfoList(data) {
  return request({
    url: `/system/crud/data_dictionary/info_list/${data.id}`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Xem nội dung từ điển dữ liệu
 */
export function getDataDictionaryInfo(cid, id, pid) {
  return request({
    url: `/system/crud/data_dictionary/info_create/${cid}/${id}/${pid}`,
    method: 'get',
  });
}
