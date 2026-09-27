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
 * @description Lấy danh mục
 */
export function categoryList() {
  return request({
    url: '/cms/category_list',
    method: 'get',
  });
}

/**
 * @description Khôi phục dữ liệu ban đầu của mẫu
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function recovery(id) {
  return request({
    url: 'diy/recovery/' + id,
    method: 'get',
  });
}

/**
 * @description Thiết lập dữ liệu ban đầu
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function setDefault(id) {
  return request({
    url: 'diy/set_recovery/' + id,
    method: 'get',
  });
}

/**
 * @description Lưu dữ liệu DIY
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function diySave(id, data) {
  return request({
    url: 'diy/save/' + id,
    method: 'post',
    data: data,
  });
}

/**
 * @description Lưu dữ liệu DIY
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function saveDiy(id, data) {
  return request({
    url: 'diy/diy_save/' + id,
    method: 'post',
    data: data,
  });
}

/**
 * @description Lấy dữ liệu trực quan (visualization)
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function diyGetInfo(id, data) {
  return request({
    url: 'diy/get_info/' + id,
    method: 'get',
    params: data,
  });
}

/**
 * @description Dùng mẫu DIY (sản phẩm sự kiện)
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getGroomList(type, data) {
  return request({
    url: 'diy/groom_list/' + type,
    method: 'get',
    params: data,
  });
}

/**
 * @description Lấy danh sách sản phẩm
 */
export function getProduct(data) {
  return request({
    url: 'diy/get_product',
    method: 'get',
    params: data,
  });
}

/**
 * @description Lấy các trường của thành phần tùy chỉnh
 */
export function getDiyField() {
  return request({
    url: 'diy_pro/text/field',
    method: 'get',
  });
}

/**
 * @description Lấy dữ liệu DIY
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getDiyInfo(id) {
  return request({
    url: 'diy/get_diy_info/' + id,
    method: 'get',
  });
}

/**
 * @description Lấy danh sách liên kết
 */
export function getUrl() {
  return request({
    url: 'diy/get_url',
    method: 'get',
  });
}

/**
 * @description Lấy danh mục sản phẩm
 */
export function getCategory() {
  return request({
    url: 'diy/get_category',
    method: 'get',
  });
}

/**
 * @description Lấy danh mục sản phẩm cấp 1 hoặc cấp 2
 */
export function getByCategory(data) {
  return request({
    url: 'diy/get_by_category',
    method: 'get',
    params: data,
  });
}

/**
 * @description Danh sách mẫu DIY
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function diyList(data) {
  return request({
    url: 'diy/get_list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Xóa dữ liệu DIY
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function diyDel(id) {
  return request({
    url: 'diy/del/' + id,
    method: 'delete',
  });
}

/**
 * @description Dùng mẫu DIY
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function setStatus(id) {
  return request({
    url: 'diy/set_status/' + id,
    method: 'put',
  });
}

/**
 * @description Dùng mẫu DIY (kiểm tra có hiển thị danh sách cửa hàng lân cận không)
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function storeStatus() {
  return request({
    url: 'diy/get_store_status',
    method: 'get',
  });
}

/**
 * @description Thêm mẫu
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getDiyCreate() {
  return request({
    url: 'diy/create',
    method: 'get',
  });
}

/**
 * @description Đặt dữ liệu mặc định
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getRecovery(id) {
  return request({
    url: 'diy/set_recovery/' + id,
    method: 'get',
  });
}

/**
 * @description Thêm thủ công, dữ liệu danh sách popup
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getProductList(params) {
  return request({
    url: 'diy/get_product_list',
    method: 'get',
    params,
  });
}

/**
 * @description Đổi màu -- Đổi màu 1 chạm, submit danh mục;
 */
export function colorChange(status, name) {
  return request({
    url: `diy/color_change/${status}/${name}`,
    method: 'put',
  });
}

/**
 * @description Đổi màu -- Đổi màu 1 chạm, thông tin danh mục;
 */
export function getColorChange(name) {
  return request({
    url: `diy/get_color_change/${name}`,
    method: 'get',
  });
}

/**
 * @description Trang cá nhân - Lấy thông tin;
 */
export function getMember() {
  return request({
    url: `diy/get_member`,
    method: 'get',
  });
}

/**
 * @description Mini Program -- Mã QR;
 */
export function getRoutineCode(id) {
  return request({
    url: `diy/get_routine_code/${id}`,
    method: 'get',
  });
}

/**
 * @description Trang cá nhân - Submit thông tin;
 */
export function memberSave(data) {
  return request({
    url: `diy/member_save`,
    method: 'post',
    data: data,
  });
}

/**
 * @description Liên kết trang - Lấy danh mục;
 */
export function pageCategory() {
  return request({
    url: `diy/get_page_category`,
    method: 'get',
  });
}

/**
 * @description Liên kết trang - Lấy liên kết;
 */
export function pageLink(id) {
  return request({
    url: `diy/get_page_link/${id}`,
    method: 'get',
  });
}

/**
 * @description Liên kết trang - Submit liên kết tùy chỉnh;
 */
export function saveLink(data, id) {
  return request({
    url: `diy/save_link/${id}`,
    method: 'post',
    data: data,
  });
}

/**
 * @description Trang diy - Từ khóa tìm kiếm hot;
 */
export function getWordsAll() {
  return request({
    url: `product/words/get_all`,
    method: 'get',
  });
}
/**
 * @description Xuất mẫu diy
 */
export function exportDiyDataApi(id) {
  return request({
    url: `diy_pro/export/data/${id}`,
    method: 'get',
  });
}

/**
 * @description Lưu tên DIY
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function diyUpdateName(id, data) {
  return request({
    url: 'diy_pro/update/name/' + id,
    method: 'post',
    data: data,
  });
}

/** Dùng cho phiên bản 5.6+ */

/**
 * @description Danh sách mẫu DIY
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function diyProList(data) {
  return request({
    url: 'diy_pro/get_list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Lấy dữ liệu trực quan (visualization)
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function diyProInfo(id, data) {
  return request({
    url: 'diy_pro/get_info/' + id,
    method: 'get',
    params: data,
  });
}

/**
 * @description Lưu dữ liệu DIY
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function diyProSave(id, data) {
  return request({
    url: 'diy_pro/save/' + id,
    method: 'post',
    data: data,
  });
}
/**
 * @description Lưu chủ đề cửa hàng
 * @param type Lưu loại
 * @param value Giá trị diy
 */
export function themeSave(id, data) {
  return request({
    url: 'theme/save/' + id,
    method: 'post',
    data: data,
  });
}
/**
 * @description Lấy chủ đề cửa hàng
 * @param id ID chủ đề
 * @param type Loại
 * @return {Object} Dữ liệu chủ đề
 */
export function themeInfo(id, type) {
  return request({
    url: 'theme/info/' + id + '/' + type,
    method: 'get',
  });
}

/**
 * @description Lấy danh sách bài viết
 */
export function getArticleList(data) {
  return request({
    url: 'theme/article',
    method: 'get',
    params: data,
  });
}

/**
 * @description Lấy danh sách phiếu giảm giá
 */
export function getCouponList(data) {
  return request({
    url: 'theme/coupon',
    method: 'get',
    params: data,
  });
}

/**
 * @description Lấy danh sách sản phẩm
 */
export function getProProduct(data) {
  return request({
    url: 'diy_pro/get_product',
    method: 'get',
    params: data,
  });
}

/**
 * @description Lấy danh sách sản phẩm của chủ đề
 */
export function getThemeProduct(data) {
  return request({
    url: 'theme/product',
    method: 'get',
    params: data,
  });
}

/**
 * @description Nhập chủ đề
 * @param data
 */
export function importTheme(data) {
  return request({
    url: 'theme/import',
    method: 'post',
    data,
  });
}
/**
 * @description Lưu tên chủ đề
 * @param id ID chủ đề
 * @param data Tên chủ đề
 */
export function saveThemeTitle(id, data) {
  return request({
    url: 'theme/save_title/' + id,
    method: 'post',
    data: data,
  });
}

/**
 * @description Lưu ảnh bìa chủ đề
 * @param id ID chủ đề
 * @param data Địa chỉ ảnh bìa
 */
export function saveThemeImage(id, data) {
  return request({
    url: 'theme/save_image/' + id,
    method: 'post',
    data: data,
  });
}

/**
 * @description Lấy danh sách chủ đề
 */
export function getThemeList(data) {
  return request({
    url: 'theme/list',
    method: 'get',
    params: data,
  });
}
/**
 * @description Xuất chủ đề
 * @param id ID chủ đề
 */
export function exportTheme(id) {
  return request({
    url: 'theme/export/' + id,
    method: 'get',
  });
}

/**
 * @description Tra cứu bản ghi xuất chủ đề (dùng cho polling)
 * @param recordId ID bản ghi tải xuống
 */
export function getExportRecord(recordId) {
  return request({
    url: 'theme/export_record/' + recordId,
    method: 'get',
  });
}

/**
 * @description Áp dụng chủ đề
 * @param id ID chủ đề
 */
export function useTheme(id) {
  return request({
    url: 'theme/use/' + id,
    method: 'get',
  });
}

/**
 * @description Lấy chủ đề đang sử dụng
 */
export function getThemeUsing() {
  return request({
    url: 'theme/using',
    method: 'get',
  });
}
/**
 * @description Khôi phục chủ đề
 * @param id ID chủ đề
 */
export function restoreTheme(id) {
  return request({
    url: 'theme/restore/' + id,
    method: 'get',
  });
}

/**
 * @description Áp dụng dữ liệu chủ đề
 * @param id ID chủ đề hiện tại
 * @param data {theme_id, type}
 */
export function useThemeData(id, data) {
  return request({
    url: 'theme/use_data/' + id,
    method: 'get',
    params: data,
  });
}

/**
 * @description Xóa chủ đề
 * @param id ID chủ đề
 */
export function deleteTheme(id) {
  return request({
    url: 'theme/del/' + id,
    method: 'delete',
  });
}

/**
 * @description Lấy danh sách trang micro
 * @param data
 */
export function getMicroPageList(data) {
  return request({
    url: 'theme/micro_page',
    method: 'get',
    params: data,
  });
}

