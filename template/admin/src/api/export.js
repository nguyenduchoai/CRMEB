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
 * Xuất danh sách người dùng
 */
export function exportUserList(data) {
  return request({
    url: '/export/user_list',
    method: 'get',
    params: data,
  });
}

/**
 * Xuất danh sách đơn hàng
 */
export function exportOrderList(data) {
  return request({
    url: '/export/order_list',
    method: 'get',
    params: data,
  });
}

/**
 * Xuất danh sách đơn hàng cần giao
 */
export function exportOrderDeliveryList(data) {
  return request({
    url: '/export/order_delivery_list',
    method: 'get',
    params: data,
  });
}

/**
 * Xuất danh sách sản phẩm
 */
export function exportProductList(data) {
  return request({
    url: '/export/product_list',
    method: 'get',
    params: data,
  });
}
/**
 * Xuất dữ liệu di chuyển sản phẩm
 */
export function exportProductExport(data) {
  return request({
    url: '/product/product_export',
    method: 'get',
    params: data,
  });
}
/**
 * Nhập dữ liệu sản phẩm di chuyển
 */
export function importProductImport(data) {
  return request({
    url: '/product/product_import',
    method: 'post',
    data,
  });
}

/**
 * Xuất danh sách săn giảm giá
 */
export function exportBargainList(data) {
  return request({
    url: '/export/bargain_list',
    method: 'get',
    params: data,
  });
}

/**
 * Xuất danh sách mua chung
 */
export function exportCombinationList(data) {
  return request({
    url: '/export/combination_list',
    method: 'get',
    params: data,
  });
}

/**
 * Xuất danh sách flash sale
 */
export function exportSeckillList(data) {
  return request({
    url: '/export/seckill_list',
    method: 'get',
    params: data,
  });
}

/**
 * Xuất thẻ thành viên
 */
export function exportmberCardList(id) {
  return request({
    url: `/export/member_card/${id}`,
    method: 'get',
  });
}
