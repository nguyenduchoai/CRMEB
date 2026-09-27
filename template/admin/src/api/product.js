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

/*
 * Lấy số lượng phần đầu form sản phẩm;
 * */
export function getGoodHeade(data) {
  return request({
    url: 'product/product/type_header',
    method: 'get',
    params: data,
  });
}

/*
 * Lấy số lượng phần đầu form sản phẩm;
 * */
export function getGoodsCategory(data) {
  return request({
    url: '/goods/goods_category',
    method: 'get',
    params: data,
  });
}

/**
 * @description Quản lý sản phẩm -- Danh sách
 */
export function getGoods(params) {
  return request({
    url: 'product/product',
    method: 'get',
    params,
  });
}

/**
 * @description Quản lý sản phẩm -- Lưu tạm
 */
export function productCache() {
  return request({
    url: 'product/cache',
    method: 'get',
  });
}

/**
 * @description Quản lý sản phẩm -- Hủy lưu tạm
 */
export function cacheDelete() {
  return request({
    url: 'product/cache',
    method: 'delete',
  });
}

/**
 * @description Quản lý sản phẩm -- Lên/xuống kệ
 */
export function PostgoodsIsShow(id, isShow) {
  return request({
    url: `product/product/set_show/${id}/${isShow}`,
    method: 'put',
  });
}

/**
 * @description Thuộc tính sản phẩm -- Lên/xuống kệ hàng loạt
 * @param {Object} param data {Object} Đối tượng truyền giá trị
 */
export function productShowApi(data) {
  return request({
    url: `product/product/product_show`,
    method: 'put',
    data,
  });
}

/**
 * Thêm đánh giá ảo
 * @param {*} data
 * @returns
 */
export function saveFictitiousReply(data) {
  return request({
    url: 'product/reply/save_fictitious_reply',
    method: 'post',
    data,
  });
}

/**
 * @description Thuộc tính sản phẩm -- Xuống kệ hàng loạt
 * @param {Object} param data {Object} Đối tượng truyền giá trị
 */
export function productUnshowApi(data) {
  return request({
    url: `product/product/product_unshow`,
    method: 'put',
    data,
  });
}

/**
 * @description Quản lý sản phẩm -- Danh mục
 */
export function treeListApi(type) {
  return request({
    url: `product/category/tree/${type}`,
    method: 'get',
  });
}

/**
 * @description Quản lý sản phẩm -- Danh mục bản mới
 */
export function cascaderListApi(type) {
  return request({
    url: `product/category/cascader/${type}`,
    method: 'get',
  });
}

/**
 * @description Quản lý sản phẩm -- Chi tiết
 */
export function productInfoApi(id) {
  return request({
    url: `product/product/${id}`,
    method: 'get',
  });
}

/**
 * @description Quản lý sản phẩm -- Submit
 */
export function productAddApi(data) {
  return request({
    url: `product/product/${data.id}`,
    method: 'POST',
    data,
  });
}

/**
 * @description Danh mục sản phẩm -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function productListApi(params) {
  return request({
    url: 'product/category',
    method: 'get',
    params,
  });
}

/**
 * @description Danh mục sản phẩm -- Form thêm
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function productCreateApi() {
  return request({
    url: 'product/category/create',
    method: 'get',
  });
}

/**
 * @description Danh mục sản phẩm -- Form sửa
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function productEditApi(id) {
  return request({
    url: `product/category/${id}`,
    method: 'get',
  });
}

/**
 * @description Danh mục sản phẩm -- Đổi trạng thái
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function setShowApi(data) {
  return request({
    url: `product/category/set_show/${data.id}/${data.is_show}`,
    method: 'PUT',
  });
}

/**
 * @description Chọn sản phẩm -- Danh sách
 */
export function changeListApi(params) {
  return request({
    url: `product/product/list`,
    method: 'GET',
    params,
  });
}

/**
 * @description Bình luận sản phẩm -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function replyListApi(params) {
  return request({
    url: `product/reply`,
    method: 'get',
    params,
  });
}

/**
 * @description Bình luận sản phẩm -- Trả lời
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function setReplyApi(data, id) {
  return request({
    url: `product/reply/set_reply/${id}`,
    method: 'PUT',
    data,
  });
}

/**
 * @description Lấy cấu hình sao chép sản phẩm
 */
export function copyConfigApi() {
  return request({
    url: `product/copy_config`,
    method: 'get',
  });
}

/**
 * @description Quản lý sản phẩm -- Lấy dữ liệu sản phẩm JD, Taobao
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function crawlFromApi(data) {
  return request({
    url: `product/copy`,
    method: 'POST',
    data,
  });
}

/**
 * @description Quản lý sản phẩm -- Submit dữ liệu sản phẩm JD, Taobao
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function crawlSaveApi(data) {
  return request({
    url: `product/crawl/save`,
    method: 'POST',
    data,
  });
}

/**
 * @description Quản lý sản phẩm -- Tạo thuộc tính
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function generateAttrApi(data, id, type) {
  return request({
    url: `product/generate_attr/${id}/${type}`,
    method: 'POST',
    data,
  });
}

/**
 * @description Thuộc tính sản phẩm -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function ruleListApi(params) {
  return request({
    url: `product/product/rule`,
    method: 'GET',
    params,
  });
}

/**
 * @description Thuộc tính sản phẩm -- Thêm
 * @param {Number} param id {Number} ID thuộc tính
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function ruleAddApi(data, id) {
  return request({
    url: `product/product/rule/${id}`,
    method: 'POST',
    data,
  });
}

/**
 * @description Thuộc tính sản phẩm -- Chi tiết
 * @param {Number} param id {Number} ID thuộc tính
 */
export function ruleInfoApi(id) {
  return request({
    url: `product/product/rule/${id}`,
    method: 'get',
  });
}

/**
 * @description Đánh giá sản phẩm -- Đánh giá ảo
 * @id -- ID sản phẩm;
 */
export function fictitiousReply(id) {
  return request({
    url: `product/reply/fictitious_reply/${id}`,
    method: 'get',
  });
}

/**
 * @description Thuộc tính sản phẩm -- Lấy mẫu quy tắc thuộc tính
 */
export function productGetRuleApi() {
  return request({
    url: `product/product/get_rule`,
    method: 'get',
  });
}

/**
 * @description Sản phẩm -- Lấy mẫu phí vận chuyển
 */
export function productGetTemplateApi() {
  return request({
    url: `product/product/get_template`,
    method: 'get',
  });
}

/**
 * @description Lấy tham số tải lên
 */
export function productGetTempKeysApi(data) {
  return request({
    url: `product/product/get_temp_keys`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Sản phẩm shop -- Xuất
 */
export function storeProductApi(data) {
  return request({
    url: `export/storeProduct`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Thêm sản phẩm -- Kiểm tra chương trình tồn tại
 */
export function checkActivityApi(id) {
  return request({
    url: `product/product/check_activity/${id}`,
    method: 'get',
  });
}

/**
 * @description Thêm/sửa sản phẩm -- Nhãn người dùng
 */
export function labelListApi() {
  return request({
    url: 'user/user_label',
    method: 'get',
  });
}
/**
 * @description Component lấy nhãn người dùng
 */
export function productUserLabel() {
  return request({
    url: 'user/user_tree_label',
    method: 'get',
  });
}
/**
 * @description Loại tải lên
 */
export function uploadType() {
  return request({
    url: 'file/upload_type',
    method: 'get',
  });
}

/**
 * @description Nhập mã thẻ
 */
export function importCard(data) {
  return request({
    url: 'product/product/import_card',
    method: 'get',
    params: data,
  });
}

/**
 * @description Cài đặt sản phẩm hàng loạt
 * @param {Number} param id {Number} ID thuộc tính
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function batchSetting(data) {
  return request({
    url: `product/batch/setting`,
    method: 'POST',
    data,
  });
}

/**
 * @description Cấu hình loại sản phẩm
 */
export function getProductTypeConfig() {
  return request({
    url: 'product/product_type_config',
    method: 'get',
  });
}

/**
 * @description Thêm sản phẩm -- Nhãn sản phẩm
 */
export function productStoreLabel() {
  return request({
    url: 'product/product_label',
    method: 'get',
  });
}

/**
 * @description Tham số sản phẩm -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function paramListApi(params) {
  return request({
    url: `product/param/list`,
    method: 'GET',
    params,
  });
}

/**
 * @description Tham số sản phẩm -- Chi tiết
 * @param {Number} param id {Number} ID tham số
 */
export function paramInfoApi(id) {
  return request({
    url: `product/param/info/${id}`,
    method: 'get',
  });
}

/**
 * @description Tham số sản phẩm -- Thêm
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function paramSaveApi(data) {
  return request({
    url: `product/param/save/${data.id}`,
    method: 'POST',
    data,
  });
}

/**
 * @description Danh mục nhãn sản phẩm -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function labelCateListApi(params) {
  return request({
    url: `product/label_cate/list`,
    method: 'GET',
    params,
  });
}

/**
 * @description Danh mục nhãn sản phẩm -- Thêm
 * Tham số request data
 */
export function productLabelCateFormApi(id) {
  return request({
    url: `product/label_cate/form/${id}`,
    method: 'get',
  });
}

/**
 * @description Danh sách nhãn sản phẩm
 * Tham số request data
 */
export function productLabelListApi(data) {
  return request({
    url: `product/label/list`,
    method: 'get',
    params: data,
  });
}
/**
 * @description Danh sách nhãn sản phẩm -- Tất cả
 * Tham số request data
 */
export function productLabelUseListApi(data) {
  return request({
    url: `product/label/use_list`,
    method: 'get',
  });
}

/**
 * @description Lấy nhãn sản phẩm
 * Tham số request data
 */
export function productLabelInfoApi(data) {
  return request({
    url: `product/label/info/${data.id}`,
    method: 'get',
  });
}

/**
 * @description Lưu nhãn sản phẩm
 * Tham số request data
 */
export function productLabelSaveApi(data) {
  return request({
    url: `product/label/save`,
    method: 'post',
    data: data,
  });
}
/**
 * @description Nhãn sản phẩm -- Đổi trạng thái
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function labelStatusApi(data) {
  return request({
    url: `product/label/status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}
/**
 * @description Nhãn sản phẩm -- Đổi trạng thái
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function labelIsShowApi(data) {
  return request({
    url: `product/label/is_show/${data.id}/${data.is_show}`,
    method: 'PUT',
  });
}

/**
 * @description Dịch vụ bảo đảm sản phẩm -- Danh sách
 * Tham số request data
 */
export function productProtectionListApi(data) {
  return request({
    url: `product/protection/list`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Dịch vụ bảo đảm sản phẩm -- Thêm
 * Tham số request data
 */
export function productProtectionFormApi(id) {
  return request({
    url: `product/protection/form/${id}`,
    method: 'get',
  });
}

/**
 * @description Dịch vụ bảo đảm sản phẩm
 * Tham số request data
 */
export function productProtectionInfoApi(data) {
  return request({
    url: `product/protection/info`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Dịch vụ bảo đảm sản phẩm -- Đổi trạng thái
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function protectionStatusApi(data) {
  return request({
    url: `product/protection/status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}

/**
 * @description Danh sách hoa hồng
 */
export function productBrokerage(id, type) {
  return request({
    url: `product/other_info/${id}/${type}`,
    method: 'get',
  });
}

/**
 * @description Hoa hồng, submit
 */
export function productBrokerageUpdate(id, type, data) {
  return request({
    url: `product/other_save/${id}/${type}`,
    method: 'post',
    data,
  });
}

/**
 * @description Duyệt bình luận hàng loạt
 */
export function replyBatchStatus(data) {
  return request({
    url: `product/reply/batch_set_status`,
    method: 'post',
    data,
  });
}
