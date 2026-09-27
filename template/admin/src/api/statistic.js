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
 * @description Thống kê sản phẩm, Tổng quan sản phẩm
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticBasicApi(params) {
  return request({
    url: '/statistic/product/get_basic',
    method: 'get',
    params,
  });
}

/**
 * @description Thống kê sản phẩm, Tổng quan sản phẩm, Biểu đồ
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticTrendApi(params) {
  return request({
    url: '/statistic/product/get_trend',
    method: 'get',
    params,
  });
}

/**
 * @description Thống kê sản phẩm, Xếp hạng sản phẩm
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticProductListApi(params) {
  return request({
    url: '/statistic/product/get_product_ranking',
    method: 'get',
    params,
  });
}

/**
 * @description Thống kê sản phẩm, Xuất
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticProductExcel(params) {
  return request({
    url: '/statistic/product/get_excel',
    method: 'get',
    params,
  });
}

/**
 * @description Thống kê người dùng, Tổng quan người dùng
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticUserBasicApi(params) {
  return request({
    url: '/statistic/user/get_basic',
    method: 'get',
    params,
  });
}

/**
 * @description Thống kê người dùng, Xu hướng người dùng
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticUserTrendApi(params) {
  return request({
    url: '/statistic/user/get_trend',
    method: 'get',
    params,
  });
}

/**
 * @description Thống kê người dùng, Tổng quan người dùng WeChat
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticWechatApi(params) {
  return request({
    url: '/statistic/user/get_wechat',
    method: 'get',
    params,
  });
}

/**
 * @description Thống kê người dùng, Xu hướng người dùng WeChat
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticWechatTrendApi(params) {
  return request({
    url: '/statistic/user/get_wechat_trend',
    method: 'get',
    params,
  });
}

/**
 * @description Thống kê người dùng, Khu vực người dùng WeChat
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticWechatRegionApi(params) {
  return request({
    url: '/statistic/user/get_region',
    method: 'get',
    params,
  });
}

/**
 * @description Thống kê người dùng, Giới tính người dùng
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticWechatSexApi(params) {
  return request({
    url: '/statistic/user/get_sex',
    method: 'get',
    params,
  });
}

/**
 * @description Thống kê người dùng, Xuất
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticUserExcel(params) {
  return request({
    url: '/statistic/user/get_excel',
    method: 'get',
    params,
  });
}

/**
 * @description Dữ liệu giao dịch, Hôm nay
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticTopTradeApi(params) {
  return request({
    url: '/statistic/trade/top_trade',
    method: 'get',
    params,
  });
}

/**
 * @description Tổng quan giao dịch
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticBottomTradeApi(params) {
  return request({
    url: '/statistic/trade/bottom_trade',
    method: 'get',
    params,
  });
}

/**
 * @description Số lượng thống kê đơn hàng
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getBasic(params) {
  return request({
    url: '/statistic/order/get_basic',
    method: 'get',
    params,
  });
}

/**
 * @description Biểu đồ đường thống kê đơn hàng
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getTrend(params) {
  return request({
    url: '/statistic/order/get_trend',
    method: 'get',
    params,
  });
}
/**
 * @description Phân tích nguồn đơn hàng
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getChannel(params) {
  return request({
    url: '/statistic/order/get_channel',
    method: 'get',
    params,
  });
}
/**
 * @description Phân tích loại đơn hàng
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getType(params) {
  return request({
    url: '/statistic/order/get_type',
    method: 'get',
    params,
  });
}

/**
 * @description Danh sách bản ghi hóa đơn
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getRecord(params) {
  return request({
    url: '/statistic/flow/get_record',
    method: 'get',
    params,
  });
}

/**
 * @description Số lượng thống kê số dư
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getBalanceBasic(params) {
  return request({
    url: '/statistic/balance/get_basic',
    method: 'get',
    params,
  });
}

/**
 * @description Biểu đồ đường thống kê số dư
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getBalanceTrend(params) {
  return request({
    url: '/statistic/balance/get_trend',
    method: 'get',
    params,
  });
}
/**
 * @description Phân tích nguồn số dư
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getBalanceChannel(params) {
  return request({
    url: '/statistic/balance/get_channel',
    method: 'get',
    params,
  });
}
/**
 * @description Phân tích loại số dư
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getBalanceType(params) {
  return request({
    url: '/statistic/balance/get_type',
    method: 'get',
    params,
  });
}
/**
 * @description Thống kê mã kênh
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function wechatQrcodeStatistic(id, params) {
  return request({
    url: `app/wechat_qrcode/statistic/${id}`,
    method: 'get',
    params,
  });
}
