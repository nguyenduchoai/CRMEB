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
 * @description Lấy danh sách sản phẩm
 */
export function getProProduct(data) {
  return request({
    url: 'diy_pro/get_product',
    method: 'get',
    params: data,
  });
}