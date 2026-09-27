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
 * @description Quản lý đơn hàng -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function orderList(data) {
  return request({
    url: '/order/list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Thống kê phần đầu hóa đơn
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function orderInvoiceChart(data) {
  return request({
    url: 'order/invoice/chart',
    method: 'get',
    params: data,
  });
}

/**
 * @description Thống kê phần đầu hóa đơn
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function orderInvoiceList(data) {
  return request({
    url: 'order/invoice/list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Đơn hàng submit hóa đơn
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function orderInvoiceSet(id, data) {
  return request({
    url: `order/invoice/set/${id}`,
    method: 'post',
    data,
  });
}

/**
 * @description Chi tiết đơn hàng hóa đơn;
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function orderInvoiceInfo(id) {
  return request({
    url: `order/invoice_order_info/${id}`,
    method: 'get',
  });
}

/**
 * @description Dữ liệu đơn hàng -- Danh sách
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function getOrdes(data) {
  return request({
    url: '/order/chart',
    method: 'get',
    params: data,
  });
}

/**
 * @description Dữ liệu sửa form đơn hàng
 * @param {Number} param id {Number} ID đơn hàng
 */
export function getOrdeDatas(id) {
  return request({
    url: `/order/edit/${id}`,
    method: 'get',
  });
}

/**
 * @description Dữ liệu chi tiết form đơn hàng
 * @param {Number} param id {Number} ID đơn hàng
 */
export function getDataInfo(id) {
  return request({
    url: `/order/info/${id}`,
    method: 'get',
  });
}

/**
 * @description Dữ liệu chi tiết form đơn hàng - mới
 * @param {Number} param id {Number} ID đơn hàng
 */
export function getDataInfoNew(id) {
  return request({
    url: `/refund/info/${id}`,
    method: 'get',
  });
}

/**
 * @description Sửa thông tin ghi chú
 * @param {Number} param data.id {Number} ID đơn hàng
 * @param {String} param data.remark {String} Thông tin ghi chú
 */
export function putRemarkData(data) {
  return request({
    url: `/order/remark/${data.id}`,
    method: 'put',
    data: data.remark,
  });
}

/**
 * @description Lấy lịch sử đơn hàng
 * @param {Number} param data.id {Number} ID đơn hàng
 * @param {String} param data.datas {String} Tham số phân trang
 */
export function getOrderRecord(data) {
  return request({
    url: `/order/status/${data.id}`,
    method: 'get',
    params: data.datas,
  });
}

/**
 * @description Lấy dữ liệu form hoàn tiền
 * @param {Number} param id {Number} ID đơn hàng
 */
export function getRefundFrom(id) {
  return request({
    url: `/order/refund/${id}`,
    method: 'get',
  });
}
/**
 * @description Hoàn tiền
 * @param {Number} param id {Number} ID đơn hàng
 */
export function refundPrice(id, data) {
  return request({
    url: `/order/refund/${id}`,
    method: 'put',
    data,
  });
}

/**
 * @description Bản mới - Lấy dữ liệu form hoàn tiền
 * @param {Number} param id {Number} ID đơn hàng
 */
export function getNewRefundFrom(id) {
  return request({
    url: `/refund/refund/${id}`,
    method: 'get',
  });
}

/**
 * @description Lấy đơn vị vận chuyển
 */
export function getExpressData(status) {
  return request({
    url: `/order/express_list?status=${status || ''}`,
    method: 'get',
  });
}

/**
 * @description Lấy dữ liệu form không hoàn tiền
 * @param {Number} param id {Number} ID đơn hàng
 */
export function getnoRefund(id) {
  return request({
    url: `/order/no_refund/${id}`,
    method: 'get',
  });
}
/**
 * @description Bản mới - Lấy dữ liệu form không hoàn tiền
 * @param {Number} param id {Number} ID đơn hàng
 */
export function getNewnoRefundFrom(id) {
  return request({
    url: `/refund/no_refund/${id}`,
    method: 'get',
  });
}

/**
 * @description Form submit giao hàng
 * @param {Number} param data.id {Number} ID đơn hàng
 * @param {Object} param data.datas {Object} Thông tin biểu mẫu
 */
export function putDelivery(data) {
  return request({
    url: `/order/delivery/${data.id}`,
    method: 'put',
    data: data.datas,
  });
}

export function orderSheetInfo() {
  return request({
    url: '/order/sheet_info',
    method: 'get',
  });
}

/**
 * Danh sách tất cả người giao hàng
 */
export function deliveryList() {
  return request({
    url: '/order/delivery/index',
    method: 'get',
  });
}

/**
 * Lấy danh sách tất cả người giao hàng khi tạo đơn
 */
export function orderDeliveryList() {
  return request({
    url: '/order/delivery/list',
    method: 'get',
  });
}

/**
 * Danh sách đổi trạng thái tài khoản
 * @param {*} data data
 */
export function orderDeliveryStatus(data) {
  return request({
    url: `/order/delivery/set_status/${data.id}/${data.status}`,
    method: 'get',
  });
}

/**
 * Biểu mẫu sửa nhân viên giao hàng
 * @param {*} id id
 */
export function orderDeliveryEdit(id) {
  return request({
    url: `/order/delivery/${id}/edit`,
    method: 'get',
  });
}

/**
 * Form thêm người giao hàng
 */
export function orderDeliveryAdd() {
  return request({
    url: '/order/delivery/add',
    method: 'get',
  });
}

/**
 * Mẫu vận đơn điện tử
 * @param {com} data Mã đơn vị vận chuyển
 */
export function orderExpressTemp(data) {
  return request({
    url: '/order/express/temp',
    method: 'get',
    params: data,
  });
}

/**
 * @description Danh sách đơn hàng con -- Tách đơn
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function splitOrderList(id) {
  return request({
    url: `order/split_order/${id}`,
    method: 'get',
  });
}

/**
 * @description Lấy danh sách sản phẩm có thể tách của đơn hàng
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function splitCartInfo(id) {
  return request({
    url: `order/split_cart_info/${id}`,
    method: 'get',
  });
}

/**
 * @description Tách đơn để giao hàng
 * @param {Number} param data.id {Number} ID đơn hàng
 * @param {Object} param data.datas {Object} Thông tin biểu mẫu
 */
export function splitDelivery(data) {
  return request({
    url: `/order/split_delivery/${data.id}`,
    method: 'put',
    data: data.datas,
  });
}

/**
 * @description Lấy biểu mẫu hoàn điểm thưởng
 * @param {Number} param id {Number} ID đơn hàng
 */
export function refundIntegral(id) {
  return request({
    url: `/order/refund_integral/${id}`,
    method: 'get',
  });
}

/**
 * @description Thanh toán ngay
 * @param {String} param path {String} Địa chỉ request
 * @param {String} param method {String} Phương thức yêu cầu
 */
export function payOffline(path, method) {
  return request({
    url: path,
    method: method,
  });
}

/**
 * @description Form thông tin giao hàng
 * @param {Number} param id {Number} ID đơn hàng
 */
export function getDistribution(id) {
  return request({
    url: `/order/distribution/${id}`,
    method: 'get',
  });
}

/**
 * @description Thông tin vận chuyển đơn hàng
 * @param {Number} param id {Number} ID đơn hàng
 */
export function getExpress(id) {
  return request({
    url: `/order/express/${id}`,
    method: 'get',
  });
}

/**
 * @description  Xác nhận sử dụng đơn hàng
 * @param {String} param data {String} Nội dung xác nhận sử dụng
 */
export function putWrite(data) {
  return request({
    url: '/order/write',
    method: 'post',
    data: data,
  });
}

/**
 * @description Quản lý đơn hàng -- Xuất
 */
export function storeOrderApi(data) {
  return request({
    url: `export/storeOrder`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Xác nhận sử dụng cho một đơn hàng
 */
export function writeUpdate(order_id) {
  return request({
    url: `order/write_update/${order_id}`,
    method: 'put',
  });
}

/**
 * Đơn thu ngân
 */
export function orderScanList(data) {
  return request({
    url: 'order/scan_list',
    method: 'get',
    params: data,
  });
}

/**
 * Mã thu tiền ngoại tuyến
 */
export function orderOfflineScan(id) {
  return request({
    url: 'order/offline_scan',
    method: 'get',
    params: id,
  });
}

/**
 * @description Đơn đổi trả
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function orderRefundList(data) {
  return request({
    url: 'refund/list',
    method: 'get',
    params: data,
  });
}

/**
 * @description Lịch sử giao hàng hàng loạt
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function queueIndex(data) {
  return request({
    url: 'queue/index',
    method: 'get',
    params: data,
  });
}

/**
 * @description Giao hàng hàng loạt - Thủ công
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function handBatchDelivery(data) {
  return request({
    url: 'order/hand/batch_delivery',
    method: 'get',
    params: data,
  });
}
/**
 * @description Tải xuống
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function batchOrderDelivery(id, type, catchType) {
  return request({
    url: `export/batchOrderDelivery/${id}/${type}/${catchType}`,
    method: 'get',
  });
}
/**
 * @description Đơn hàng cửa hàng đổi điểm -- Xuất
 */
export function storeIntegralOrder(data) {
  return request({
    url: `export/storeIntegralOrder`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Danh sách task - Xem
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function deliveryLog(id, type, data) {
  return request({
    url: `queue/delivery/log/${id}/${type}`,
    method: 'get',
    params: data,
  });
}

/**
 * @description Tải bảng đối chiếu đơn vị vận chuyển
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function exportExpressList(id) {
  return request({
    url: 'export/expressList',
    method: 'get',
  });
}

/**
 * @description Giao hàng hàng loạt - Tự động
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function otherBatchDelivery(data) {
  return request({
    url: 'order/other/batch_delivery',
    method: 'post',
    data,
  });
}
/**
 * @description Tính phí gửi hàng của shop
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function orderPrice(data) {
  return request({
    url: 'order/price',
    method: 'post',
    data,
  });
}
/**
 * @description Hủy yêu cầu gửi hàng của người bán
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function shipmentCancelOrder(id, data) {
  return request({
    url: `order/shipment_cancel_order/${id}`,
    method: 'post',
    data: data,
  });
}
/**
 * @description Thực thi lại
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function queueAgain(id, type) {
  return request({
    url: `queue/again/do_queue/${id}/${type}`,
    method: 'get',
  });
}
/**
 * @description Xóa tác vụ lỗi
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function queueDel(id, type) {
  return request({
    url: `queue/del/wrong_queue/${id}/${type}`,
    method: 'get',
  });
}

/**
 * @description Dừng tác vụ
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function stopWrongQueue(id) {
  return request({
    url: `queue/stop/wrong_queue/${id}`,
    method: 'get',
  });
}

/**
 * @description Danh sách đơn vị chuyển phát gửi hàng đang hoạt động
 * @param {Object} param data {Object} Tham số truyền giá trị
 */
export function kuaidiComsList() {
  return request({
    url: `order/kuaidi_coms`,
    method: 'get',
  });
}

/**
 * @description Sửa ghi chú đơn hoàn tiền
 * @param {Number} param data.id {Number} ID đơn hàng
 * @param {String} param data.remark {String} Thông tin ghi chú
 */
export function putRefundRemarkData(data) {
  return request({
    url: `/refund/remark/${data.id}`,
    method: 'put',
    data: data.remark,
  });
}

/**
 * @description Nhập phiếu giao hàng
 */
export function importExpress(data) {
  return request({
    url: '/order/delivery/import_express',
    method: 'get',
    params: data,
  });
}

/**
 * @description Phiếu soạn hàng - In
 * @param id  ID đơn hàng
 */
export function distributionOrder(id) {
  return request({
    url: `/order/print/shipping/${id}`,
    method: 'get',
  });
}
/**
 * @description Quản lý hóa đơn
 * @param id  ID hóa đơn
 */
export function invoiceIssuanceUrl(id) {
  return request({
    url: `/order/invoice_issuance_url/${id}`,
    method: 'get',
  });
}
/**
 * @description Tải hóa đơn
 * @param id  ID hóa đơn
 */
export function downInvoice(id) {
  return request({
    url: `/order/down_invoice/${id}`,
    method: 'get',
  });
}
/**
 * @description Xuất hóa đơn điều chỉnh giảm
 * @param id  ID hóa đơn
 */
export function redInvoiceIssuance(id) {
  return request({
    url: `/order/red_invoice_issuance/${id}`,
    method: 'get',
  });
}
/**
 * @description Sửa trạng thái hóa đơn
 * @param id  ID hóa đơn
 * @param data  Thông tin hóa đơn
 */
export function saveInvoiceInfo(id, data) {
  return request({
    url: `/order/save_invoice_info/${id}`,
    method: 'post',
    data: data,
  });
}
/**
 * @description Tìm kiếm danh mục hóa đơn
 * @param name  Tên danh mục hóa đơn
 */
export function invoiceCategory(name) {
  return request({
    url: `/order/invoice_category`,
    method: 'get',
    params: name,
  });
}
/**
 * @description Submit cấu hình hóa đơn điện tử
 * @param data  Thông tin hóa đơn
 */
export function saveBasics(data) {
  return request({
    url: `/marketing/integral_config/save_basics`,
    method: 'post',
    data,
  });
}
/**
 * @description Lấy cấu hình hóa đơn điện tử
 */
export function invoiceConfig() {
  return request({
    url: `/order/elec_invoice_config`,
    method: 'get',
  });
}

// Sửa địa chỉ giao hàng
export function editAddress(data) {
  return request({
    url: `/order/edit_address/${data.id}`,
    method: 'post',
    data,
  });
}
