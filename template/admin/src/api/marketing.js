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
 * @description Tạo phiếu giảm giá -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function couponListApi(params) {
  return request({
    url: 'marketing/coupon/list',
    method: 'get',
    params,
  });
}

/**
 * @description Tạo phiếu giảm giá -- Form thêm mới
 * type: thêm loại phiếu giảm giá 0: chung, 1: theo ngành hàng, 2: theo sản phẩm
 */
export function couponCreateApi(type) {
  return request({
    url: `marketing/coupon/create/${type}`,
    method: 'get',
  });
}

/**
 * @description Tạo phiếu giảm giá -- Form sửa
 */
export function couponEditeApi(id) {
  return request({
    url: `marketing/coupon/${id}/edit`,
    method: 'get',
  });
}

/**
 * @description Tạo phiếu giảm giá -- Form phát hành phiếu giảm giá
 * @param {Number} param id {Number} ID phiếu giảm giá
 */
export function couponSendApi(id) {
  return request({
    url: `marketing/coupon/issue/${id}`,
    method: 'get',
  });
}

/**
 * @description Quản lý đã phát hành -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function releasedListApi(params) {
  return request({
    url: 'marketing/coupon/released',
    method: 'get',
    params,
  });
}

/**
 * @description Quản lý đã phát hành -- Lịch sử nhận
 * @param {Number} param id {Number} ID phiếu giảm giá đã phát hành
 */
export function releasedissueLogApi(id, params) {
  return request({
    url: `marketing/coupon/released/issue_log/${id}`,
    method: 'get',
    params,
  });
}

/**
 * @description Quản lý đã phát hành -- Form đổi trạng thái
 * @param {Number} param id {Number} ID phiếu giảm giá đã phát hành
 */
export function releaseStatusApi(id) {
  return request({
    url: `marketing/coupon/released/${id}/status`,
    method: 'get',
  });
}

/**
 * @description Danh sách phiếu giảm giá -- Có kích hoạt không
 * @param {*} data
 */
export function couponStatusApi(data) {
  return request({
    url: `marketing/coupon/status/${data.id}/${data.status}`,
    method: 'get',
  });
}

/**
 * @description Tạo phiếu giảm giá -- Lưu
 */
export function couponSaveApi(data) {
  return request({
    url: `marketing/coupon/save_coupon`,
    method: 'post',
    data,
  });
}

/**
 * @description Phiếu giảm giá
 * @param {*} id
 */
export function couponDetailApi(id) {
  return request({
    url: `marketing/coupon/copy/${id}`,
    method: 'get',
  });
}

/**
 * @description Lịch sử thành viên nhận -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function userListApi(params) {
  return request({
    url: `/marketing/coupon/user`,
    method: 'get',
    params,
  });
}

/**
 * @description Sản phẩm săn giảm giá -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function bargainListApi(params) {
  return request({
    url: `marketing/bargain`,
    method: 'get',
    params,
  });
}

/**
 * @description Sản phẩm săn giảm giá -- Chi tiết
 * @param {Number} param id {Number} ID sản phẩm săn giảm giá
 */
export function bargainInfoApi(id) {
  return request({
    url: `marketing/bargain/${id}`,
    method: 'get',
  });
}

/**
 * @description Sản phẩm săn giảm giá -- Lưu chỉnh sửa
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function bargainCreatApi(data) {
  return request({
    url: `marketing/bargain/${data.id}`,
    method: 'POST',
    data,
  });
}

/**
 * @description Sản phẩm săn giảm giá -- Đổi trạng thái
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function bargainSetStatusApi(data) {
  return request({
    url: `marketing/bargain/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}
/**
 * @description Sản phẩm đặt trước -- Đổi trạng thái
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function advanceSetStatusApi(data) {
  return request({
    url: `marketing/advance/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}

/**
 * @description Sản phẩm đặt trước -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function presellListApi(params) {
  return request({
    url: `marketing/advance/index`,
    method: 'get',
    params,
  });
}

/**
 * @description Sản phẩm đặt trước -- Lưu chỉnh sửa
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function presellCreatApi(data) {
  return request({
    url: `marketing/advance/save/${data.id}`,
    method: 'POST',
    data,
  });
}

/**
 * @description Sản phẩm đặt trước -- Chi tiết
 * @param {Number} param id {Number} ID sản phẩm mua chung
 */
export function presellInfoApi(id) {
  return request({
    url: `marketing/advance/info/${id}`,
    method: 'get',
  });
}

/**
 * @description Sản phẩm mua chung -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function combinationListApi(params) {
  return request({
    url: `marketing/combination`,
    method: 'get',
    params,
  });
}

/**
 * @description Sản phẩm mua chung -- Đổi trạng thái
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function combinationSetStatusApi(data) {
  return request({
    url: `marketing/combination/set_status/${data.id}/${data.status}`,
    method: 'PUT',
  });
}

/**
 * @description Sản phẩm mua chung -- Thống kê mua chung
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function statisticsApi() {
  return request({
    url: `marketing/combination/statistics`,
    method: 'GET',
  });
}

/**
 * @description Sản phẩm mua chung -- Chi tiết
 * @param {Number} param id {Number} ID sản phẩm mua chung
 */
export function combinationInfoApi(id) {
  return request({
    url: `marketing/combination/${id}`,
    method: 'get',
  });
}

/**
 * @description Sản phẩm mua chung -- Lưu chỉnh sửa
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function combinationCreatApi(data) {
  return request({
    url: `marketing/combination/${data.id}`,
    method: 'POST',
    data,
  });
}

/**
 * @description Sản phẩm mua chung -- Danh sách nhóm mua chung
 */
export function combineListApi(params) {
  return request({
    url: `marketing/combination/combine/list`,
    method: 'GET',
    params,
  });
}

/**
 * @description Sản phẩm mua chung -- Danh sách người mua chung
 * @param {Number} param id {Number} ID sản phẩm mua chung
 */
export function orderPinkListApi(id) {
  return request({
    url: `marketing/combination/order_pink/${id}`,
    method: 'GET',
  });
}

/**
 * @description Sản phẩm flash sale -- Danh sách
 */
export function seckillListApi(params) {
  return request({
    url: `marketing/seckill`,
    method: 'GET',
    params,
  });
}

/**
 * @description Sản phẩm flash sale -- Chi tiết
 */
export function seckillInfoApi(id) {
  return request({
    url: `marketing/seckill/${id}`,
    method: 'GET',
  });
}

/**
 * @description Sản phẩm flash sale -- Lưu chỉnh sửa
 */
export function seckillAddApi(data) {
  return request({
    url: `marketing/seckill/${data.id}`,
    method: 'post',
    data,
  });
}

/**
 * @description Sản phẩm flash sale -- Đổi trạng thái
 */
export function seckillStatusApi(data) {
  return request({
    url: `marketing/seckill/set_status/${data.id}/${data.status}`,
    method: 'put',
  });
}

/**
 * @description Chương trình flash sale -- Danh sách
 */
export function seckillActivityListApi(params) {
  return request({
    url: `marketing/seckill_activity/list`,
    method: 'GET',
    params,
  });
}

/**
 * @description Sản phẩm flash sale -- Lưu chỉnh sửa hàng loạt
 */
export function seckillActivityAddApi(data) {
  return request({
    url: `marketing/seckill_activity/save/${data.id}`,
    method: 'post',
    data,
  });
}
/**
 * @description Chương trình flash sale hàng loạt -- Chi tiết
 */
export function seckillActivityInfoApi(id) {
  return request({
    url: `marketing/seckill_activity/info/${id}`,
    method: 'get',
  });
}

/**
 * @description Chương trình flash sale -- Đổi trạng thái
 */
export function seckillActivityStatusApi(data) {
  return request({
    url: `marketing/seckill_activity/status/${data.id}/${data.status}`,
    method: 'put',
  });
}

/**
 * @description Log điểm thưởng -- Danh sách
 */
export function integralListApi(params) {
  return request({
    url: `marketing/integral`,
    method: 'GET',
    params,
  });
}

/**
 * @description Log điểm thưởng -- Phần đầu
 */
export function integralStatisticsApi(params) {
  return request({
    url: `marketing/integral/statistics`,
    method: 'GET',
    params,
  });
}

/**
 * @description Log điểm thưởng -- Phần đầu
 */
export function seckillTimeListApi() {
  return request({
    url: `marketing/seckill/time_list`,
    method: 'GET',
  });
}

/**
 * @description Danh sách sản phẩm -- Phần đầu
 */
export function productAttrsApi(id, type) {
  return request({
    url: `product/product/attrs/${id}/${type}`,
    method: 'GET',
  });
}

/**
 * @description Sản phẩm săn giảm giá -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function bargainUserListApi(params) {
  return request({
    url: `marketing/bargain_list`,
    method: 'get',
    params,
  });
}

/**
 * @description Sản phẩm săn giảm giá -- Danh sách
 * @param {Object} param params {Object} Tham số truyền giá trị
 */
export function bargainUserInfoApi(id) {
  return request({
    url: `marketing/bargain_list_info/${id}`,
    method: 'get',
  });
}

/**
 * @description Quản lý đã phát hành -- Xóa
 */
export function delCouponReleased(id) {
  return request({
    url: `marketing/coupon/released/${id}`,
    method: 'DELETE',
  });
}

/**
 * @description Log điểm thưởng -- Xuất
 */
export function userPointApi(data) {
  return request({
    url: `export/userPoint`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Chương trình săn giảm giá của shop -- Xuất
 */
export function stroeBargainApi(data) {
  return request({
    url: `export/storeBargain`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Mua chung của shop -- Xuất
 */
export function storeCombinationApi(data) {
  return request({
    url: `export/storeCombination`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Flash sale của shop -- Xuất
 */
export function storeSeckillApi(data) {
  return request({
    url: `export/storeSeckill`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Sản phẩm đổi điểm -- Danh sách
 */
export function integralProductListApi(params) {
  return request({
    url: `marketing/integral_product`,
    method: 'GET',
    params,
  });
}

/**
 * @description Sản phẩm đổi điểm -- Lưu chỉnh sửa
 */
export function integralAddApi(data) {
  return request({
    url: `marketing/integral/${data.id}`,
    method: 'post',
    data,
  });
}

/**
 * @description Sản phẩm đổi điểm -- Lưu (nhiều sản phẩm)
 */
export function integralAddBatch(data) {
  return request({
    url: `marketing/integral/batch`,
    method: 'post',
    data,
  });
}

/**
 * @description Sản phẩm đổi điểm -- Chi tiết
 */
export function integralInfoApi(id) {
  return request({
    url: `marketing/integral/${id}`,
    method: 'GET',
  });
}
/**
 * @description Sản phẩm đổi điểm -- Đổi trạng thái
 */
export function integralIsShowApi(data) {
  return request({
    url: `marketing/integral/set_show/${data.id}/${data.is_show}`,
    method: 'put',
  });
}
/**
 * @description Quản lý đơn hàng đổi điểm -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function integralOrderList(data) {
  return request({
    url: 'marketing/integral/order/list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Dữ liệu đơn hàng đổi điểm -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function integralGetOrdes(data) {
  return request({
    url: 'marketing/integral/order/chart',
    method: 'get',
    params: data,
  });
}
/**
 * @description Thông tin vận chuyển đơn hàng
 * @param {Number} param id {Number} ID đơn hàng
 */
export function getExpress(id) {
  return request({
    url: `marketing/integral/order/express/${id}`,
    method: 'get',
  });
}
/**
 * @description Lấy đơn vị vận chuyển
 */
export function getExpressData(status) {
  return request({
    url: `marketing/integral/order/express_list?status=` + status,
    method: 'get',
  });
}

/**
 * @description Dữ liệu chi tiết form đơn hàng
 * @param {Number} param id {Number} ID đơn hàng
 */
export function getIntegralOrderDataInfo(id) {
  return request({
    url: `marketing/integral/order/info/${id}`,
    method: 'get',
  });
}

/**
 * @description Form thông tin giao hàng
 * @param {Number} param id {Number} ID đơn hàng
 */
export function getIntegralOrderDistribution(id) {
  return request({
    url: `marketing/integral/order/distribution/${id}`,
    method: 'get',
  });
}

/**
 * @description Lấy lịch sử đơn hàng
 * @param {Number} param data.id {Number} ID đơn hàng
 * @param {String} param data.datas {String} Tham số phân trang
 */
export function getIntegralOrderRecord(data) {
  return request({
    url: `marketing/integral/order/status/${data.id}`,
    method: 'get',
    params: data.datas,
  });
}

/**
 * @description Form submit giao hàng
 * @param {Number} param data.id {Number} ID đơn hàng
 * @param {Object} param data.datas {Object} Thông tin biểu mẫu
 */
export function integralOrderPutDelivery(data) {
  return request({
    url: `marketing/integral/order/delivery/${data.id}`,
    method: 'put',
    data: data.datas,
  });
}

/**
 * @description Sửa thông tin ghi chú
 * @param {Number} param data.id {Number} ID đơn hàng
 * @param {String} param data.remark {String} Thông tin ghi chú
 */
export function integralOrderPutRemarkData(data) {
  return request({
    url: `marketing/integral/order/remark/${data.id}`,
    method: 'put',
    data: data.remark,
  });
}
/**
 * @description Ghi chú điểm thưởng
 * @param {Number} param data.id {Number} ID đơn hàng
 * @param {String} param data.remark {String} Thông tin ghi chú
 */
export function setPointRecordMark(id, data) {
  return request({
    url: `marketing/point_record/remark/${id}`,
    method: 'post',
    data,
  });
}

/**
 * Lấy danh sách tất cả người giao hàng khi tạo đơn
 */
export function orderDeliveryList() {
  return request({
    url: 'marketing/integral/order/delivery/list',
    method: 'get',
  });
}

/**
 * Mẫu vận đơn điện tử
 * @param {com} data Mã đơn vị vận chuyển
 */
export function orderExpressTemp(data) {
  return request({
    url: 'marketing/integral/order/express/temp',
    method: 'get',
    params: data,
  });
}
/**
 * Danh sách thống kê điểm thưởng
 * @param {com} data
 */
export function pointRecordList(data) {
  return request({
    url: 'marketing/point_record',
    method: 'get',
    params: data,
  });
}
/**
 * Danh sách thống kê điểm thưởng, ghi chú
 * @param {com} data
 */
export function pointRecordRemark(id, data) {
  return request({
    url: `marketing/point_record/remark/${id}`,
    method: 'post',
    data,
  });
}

export function orderSheetInfo() {
  return request({
    url: 'marketing/integral/order/sheet_info',
    method: 'get',
  });
}
/**
 * Phần trên thống kê điểm thưởng
 * @param {com} data
 */
export function getPointBasic(data) {
  return request({
    url: 'marketing/point/get_basic',
    method: 'get',
    params: data,
  });
}

/**
 * Thống kê điểm thưởng, biểu đồ đường
 * @param {com} data
 */
export function getPointTrend(data) {
  return request({
    url: 'marketing/point/get_trend',
    method: 'get',
    params: data,
  });
}

/**
 * @description Phân tích nguồn điểm thưởng
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getChannel(params) {
  return request({
    url: '/marketing/point/get_channel',
    method: 'get',
    params,
  });
}
/**
 * @description Phân tích tiêu điểm thưởng
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getType(params) {
  return request({
    url: '/marketing/point/get_type',
    method: 'get',
    params,
  });
}

/**
 * Thống kê flash sale
 * @param {*} id
 * @param {*} params
 * @returns
 */
export function getseckillStatistics(id, params) {
  return request({
    url: `marketing/seckill/statistics/head/${id}`,
    method: 'get',
    params,
  });
}

/**
 * Người tham gia flash sale
 * @param {*} id
 * @param {*} params
 * @returns
 */
export function getseckillStatisticsPeople(id, params) {
  return request({
    url: `marketing/seckill/statistics/people/${id}`,
    method: 'get',
    params,
  });
}

/**
 * Đơn flash sale
 * @param {*} id
 * @param {*} params
 * @returns
 */
export function getseckillStatisticsOrder(id, params) {
  return request({
    url: `marketing/seckill/statistics/order/${id}`,
    method: 'get',
    params,
  });
}

/**
 * Thống kê mua chung
 * @param {*} id
 * @param {*} params
 * @returns
 */
export function getcombinationStatistics(id, params) {
  return request({
    url: `marketing/combination/statistics/head/${id}`,
    method: 'get',
    params,
  });
}

/**
 * Danh sách mua chung
 * @param {*} id
 * @param {*} params
 * @returns
 */
export function getcombinationStatisticsPeople(id, params) {
  return request({
    url: `marketing/combination/statistics/list/${id}`,
    method: 'get',
    params,
  });
}

/**
 * Đơn mua chung
 * @param {*} id
 * @param {*} params
 * @returns
 */
export function getcombinationStatisticsOrder(id, params) {
  return request({
    url: `marketing/combination/statistics/order/${id}`,
    method: 'get',
    params,
  });
}

/**
 * Thống kê săn giảm giá
 * @param {*} id
 * @param {*} params
 * @returns
 */
export function getbargainStatistics(id, params) {
  return request({
    url: `marketing/bargain/statistics/head/${id}`,
    method: 'get',
    params,
  });
}

/**
 * Danh sách săn giảm giá
 * @param {*} id
 * @param {*} params
 * @returns
 */
export function getbargainStatisticsPeople(id, params) {
  return request({
    url: `marketing/bargain/statistics/list/${id}`,
    method: 'get',
    params,
  });
}

/**
 * Đơn săn giảm giá
 * @param {*} id
 * @param {*} params
 * @returns
 */
export function getbargainStatisticsOrder(id, params) {
  return request({
    url: `marketing/bargain/statistics/order/${id}`,
    method: 'get',
    params,
  });
}
/**
 * Danh sách phần thưởng điểm danh
 * @param {com} data
 */
export function signRewards(data) {
  return request({
    url: 'marketing/sign/rewards',
    method: 'get',
    params: data,
  });
}
/**
 * Thêm thưởng điểm danh
 * @param {com} data
 */
export function addSignRewards(data) {
  return request({
    url: 'marketing/sign/add_rewards',
    method: 'get',
    params: data,
  });
}
/**
 * Sửa thưởng điểm danh
 */
export function editSignRewards(id) {
  return request({
    url: 'marketing/sign/edit_rewards/' + id,
    method: 'get',
  });
}

/**
 * Sửa quà tặng thành viên mới
 */
export function editNewbie(data) {
  return request({
    url: 'user/new_gift/save',
    method: 'post',
    data,
  });
}
/**
 * Sửa quà tặng thành viên mới
 */
export function getNewbie(data) {
  return request({
    url: 'user/new_gift',
    method: 'get',
  });
}

/**
 * Mua chung thành nhóm ngay
 */
export function combineJoinApi(id) {
  return request({
    url: 'marketing/combination/immediately/' + id,
    method: 'get',
  });
}
