// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2024 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------


import request from "@/utils/request.js";

/**
 * Dữ liệu thống kê
 */
export function getStatisticsInfo() {
	return request.get("admin/order/statistics", {}, {
		login: true
	});
}
/**
 * Thống kê đơn hàng theo tháng
 */
export function getStatisticsMonth(where) {
	return request.get("admin/order/data", where, {
		login: true
	});
}
/**
 * Thống kê đơn hàng theo tháng
 */
export function getAdminOrderList(where) {
	return request.get("admin/order/list", where, {
		login: true
	});
}
/**
 * Sửa giá đơn hàng
 */
export function setAdminOrderPrice(data) {
	return request.post("admin/order/price", data, {
		login: true
	});
}
/**
 * Ghi chú đơn hàng
 */
export function setAdminOrderRemark(data) {
	return request.post("admin/order/remark", data, {
		login: true
	});
}
/**
 * Chi tiết đơn hàng
 */
export function getAdminOrderDetail(orderId) {
	return request.get("admin/order/detail/" + orderId, {}, {
		login: true
	});
}

/**
 * Chi tiết đơn hoàn tiền
 */
export function getAdminRefundOrderDetail(orderId) {
	return request.get("admin/refund_order/detail/" + orderId, {}, {
		login: true
	});
}

/**
 * Lấy thông tin giao hàng của đơn hàng
 */
export function getAdminOrderDelivery(orderId) {
	return request.get(
		"admin/order/delivery/gain/" + orderId, {}, {
			login: true
		}
	);
}

/**
 * Lưu giao hàng đơn hàng
 */
export function setAdminOrderDelivery(id, data) {
	return request.post("admin/order/delivery/keep/" + id, data, {
		login: true
	});
}
/**
 * Biểu đồ thống kê đơn hàng
 */
export function getStatisticsTime(data) {
	return request.get("admin/order/time", data, {
		login: true
	});
}
/**
 * Xác nhận thanh toán đơn hàng thanh toán ngoại tuyến
 */
export function setOfflinePay(data) {
	return request.post("admin/order/offline", data, {
		login: true
	});
}
/**
 * Xác nhận hoàn tiền đơn hàng
 */
export function setOrderRefund(data) {
	return request.post("admin/order/refund", data, {
		login: true
	});
}

/**
 * Lấy đơn vị vận chuyển
 * @returns {*}
 */
export function getLogistics(data) {
	return request.get("logistics", data, {
		login: false
	});
}

/**
 * Xác nhận sử dụng đơn hàng
 * @returns {*}
 */
export function orderVerific(verify_code, is_confirm) {
	return request.post("order/order_verific", {
		verify_code,
		is_confirm
	});
}

/**
 * Lấy mẫu của đơn vị vận chuyển
 * @returns {*}
 */
export function orderExportTemp(data) {
	return request.get("admin/order/export_temp", data);
}

/**
 * Lấy cấu hình in đơn hàng mặc định
 * @returns {*}
 */
export function orderDeliveryInfo() {
	return request.get("admin/order/delivery_info");
}

/**
 * Danh sách nhân viên giao hàng
 * @returns {*}
 */
export function orderOrderDelivery() {
	return request.get("admin/order/delivery");
}

/**
 * Danh sách hoàn tiền
 * @returns {*}
 */
export function orderRefund_order(where) {
	return request.get("admin/refund_order/list", where, {
		login: true
	});
}

/**
 * Ghi chú đơn hàng (hoàn tiền)
 */
export function setAdminRefundRemark(data) {
	return request.post("admin/refund_order/remark", data, {
		login: true
	});
}

/**
 * Đồng ý trả hàng cho đơn hàng
 */
export function agreeExpress(data) {
	return request.post("admin/order/agreeExpress", data, {
		login: true
	});
}