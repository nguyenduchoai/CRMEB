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
 * Lấy danh sách giỏ hàng
 * @param numType boolean true Số lượng giỏ hàng, false=số lượng sản phẩm trong giỏ hàng
 */
export function getCartCounts(numType) {
	return request.get("cart/count", {
		numType: numType === undefined ? 0 : numType
	});
}
/**
 * Lấy danh sách giỏ hàng
 * 
 */
export function getCartList(data) {
	return request.get("cart/list", data);
}

/**
 * Sửa giỏ hàng
 * 
 */
export function getResetCart(data) {
	return request.post("v2/reset_cart", data);
}

/**
 * Cập nhật số lượng giỏ hàng
 * @param int cartId  ID giỏ hàng
 * @param int number Sửa số lượng
 */
export function changeCartNum(cartId, number) {
	return request.post("cart/num", {
		id: cartId,
		number: number
	});
}
/**
 * Xóa giỏ hàng
 * @param object ids join(',') Cắt thành chuỗi
 */
export function cartDel(ids) {
	if (typeof ids === 'object')
		ids = ids.join(',');
	return request.post('cart/del', {
		ids: ids
	});
}
/**
 * Danh sách đơn hàng
 * @param object data
 */
export function getOrderList(data) {
	return request.get('order/list', data);
}

/**
 * Thông tin sản phẩm đơn hàng
 * @param string unique 
 */
export function orderProduct(unique) {
	return request.post('order/product', {
		unique: unique
	});
}

/**
 * Đánh giá đơn hàng
 * @param object data
 * 
 */
export function orderComment(data) {
	return request.post('order/comment', data);
}

/**
 * Thanh toán đơn hàng
 * @param object data
 */
export function orderPay(data) {
	return request.post('order/pay', data);
}

/**
 * Xóa đơn hàng đã hoàn tiền và bị từ chối hoàn tiền
 * @param string uni
 * 
 */
export function refundOrderDel(uni) {
	return request.get('order/refund/del/' + uni, {});
}

/**
 * Dữ liệu thống kê đơn hàng
 */
export function orderData() {
	return request.get('order/data')
}

/**
 * Hủy đơn hàng
 * @param string id
 * 
 */
export function orderCancel(id) {
	return request.post('order/cancel', {
		id: id
	});
}

/**
 * Xóa đơn hàng đã hoàn thành
 * @param string uni
 * 
 */
export function orderDel(uni) {
	return request.post('order/del', {
		uni: uni
	});
}

/**
 * Chi tiết đơn hàng quà tặng
 * @param string uni 
 */
export function getGiftOrderDetail(id) {
	return request.get('order/gift_detail/' + id);
}
/**
 * Chi tiết đơn hàng
 * @param string uni 
 */
export function getOrderDetail(uni, cart_id) {
	return request.get('order/detail/' + uni + `${cart_id ? `/${cart_id}`:''}`);
}
/**
 * Chi tiết đơn hoàn tiền
 * @param string uni 
 */
export function getRefundOrderDetail(uni, cart_id) {
	return request.get('order/refund_detail/' + uni + `${cart_id ? `/${cart_id}`:''}`);
}

/**
 * Đặt hàng lại
 * @param string uni
 * 
 */
export function orderAgain(uni) {
	return request.post('order/again', {
		uni: uni
	});
}

/**
 * Xác nhận đã nhận hàng
 * @param string uni
 * 
 */
export function orderTake(uni) {
	return request.post('order/take', {
		uni: uni
	});
}

/**
 * Đơn hàng tra cứu thông tin vận chuyển
 * @returns {*}
 */
export function express(uni, type) {
	return request.get("order/express/" + uni + `${type?'/refund':''}`);
}
/**
 * Đơn hàng tra cứu thông tin vận chuyển
 * @returns {*}
 */
export function adminExpress(uni, type) {
	return request.get("admin/order/express/" + uni + `${type?'/refund':''}`);
}

/**
 * Lấy lý do hoàn tiền
 * 
 */
export function ordeRefundReason() {
	return request.get('order/refund/reason');
}

/**
 * Duyệt hoàn tiền đơn hàng
 * @param object data
 */
export function orderRefundVerify(data) {
	return request.post('order/refund/verify', data);
}

/**
 * Xác nhận đơn hàng lấy thông tin chi tiết đơn hàng
 * @param string cartId
 */
export function orderConfirm(data) {
	return request.post('order/confirm', data);
}

/**
 * Lấy trạng thái hiển thị giao hàng và nhận tại cửa hàng trên trang xác nhận đơn hàng
 * @param string cartId
 */
export function checkShipping(cartId, news) {
	return request.post('order/check_shipping', {
		cartId,
		'new': news
	});
}

/**
 * Lấy phiếu giảm giá có thể dùng với số tiền hiện tại
 * @param string price
 * 
 */
export function getCouponsOrderPrice(price, data) {
	return request.get('coupons/order/' + price, data)
}

/**
 * Tạo đơn hàng
 * @param string key
 * @param object data
 * 
 */
export function orderCreate(key, data) {
	return request.post('order/create/' + key, data);
}

/**
 * Tính số tiền đơn hàng
 * @param key
 * @param data
 * @returns {*}
 */
export function postOrderComputed(key, data) {
	return request.post("order/computed/" + key, data);
}

/**
 * Phiếu giảm giá đơn hàng
 * @param key
 * @param data
 * @returns {*}
 */
export function orderCoupon(orderId) {
	return request.post("v2/order/product_coupon/" + orderId);
}

/**
 * Tính số tiền thành viên thanh toán ngoại tuyến
 * @param {Object} data
 */
export function offlineCheckPrice(data) {
	return request.post("order/offline/check/price", data);
}

/**
 * Thanh toán quét mã ngoại tuyến
 * @param {Object} data
 */
export function offlineCreate(data) {
	return request.post("order/offline/create", data);
}

/**
 * Bật/tắt phương thức thanh toán
 */
export function orderOfflinePayType() {
	return request.get('order/offline/pay/type');
}

/**
 * Lịch sử xuất hóa đơn
 */
export function orderInvoiceList(data) {
	return request.get('v2/order/invoice_list', data);
}

/**
 * Chi tiết đơn hàng xuất hóa đơn
 * @param {Object} id
 */
export function orderInvoiceDetail(id) {
	return request.get(`v2/order/invoice_detail/${id}`);
}


/**
 * Thanh toán Alipay
 * @param {Object} key
 * @param {Object} quitUrl
 */
export function aliPay(key, quitUrl) {
	return request.get('ali_pay', {
		key,
		quitUrl
	}, {
		noAuth: true
	});
}


/**
 * Gửi mã vận đơn trả hàng
 * @param {Object} data
 */
export function refundExpress(data) {
	return request.post("order/refund/express", data);
}

/**
 * Danh sách giỏ hàng theo danh mục
 */
export function vcartList() {
	return request.get("v2/cart_list");
}

/**
 * Danh sách sản phẩm hoàn tiền
 */
export function refundGoodsList(orderId) {
	return request.get(`order/refund/cart_info/${orderId}`);
}

/**
 * Danh sách sản phẩm yêu cầu hoàn tiền
 */
export function postRefundGoods(data) {
	return request.post(`order/refund/cart_info`, data);
}

/**
 * Gửi sản phẩm hoàn tiền
 */
export function returnGoodsSubmit(id, data) {
	return request.post(`order/refund/apply/${id}`, data);
}

/**
 * Danh sách đơn hàng mới, phiên bản 2.1
 * @param object data
 */
export function getNewOrderList(data) {
	return request.get('order/refund/list', data);
}

/**
 * Chi tiết đơn hoàn tiền
 * @param string uni 
 */
export function refundOrderDetail(uni) {
	return request.get('order/refund/detail/' + uni);
}

/**
 * Hủy yêu cầu hoàn tiền
 * @param string uni 
 */
export function cancelRefundOrder(uni) {
	return request.post('order/refund/cancel/' + uni);
}

/**
 * Thông tin đơn hàng thu ngân
 * @param object data
 */
export function getCashierOrder(orderId, type) {
	return request.get(`order/cashier/${orderId}/${type}`);
}

/**
 * Lấy địa chỉ hóa đơn
 * @param object data
 */
export function getInvoiceLink(id) {
	return request.get(`v2/order/down_invoice/${id}`);
}

/**
 * Nhận quà
 * @param orderId
 * @param data
 */
export function orderReceiveGift(orderId, data) {
	return request.post("order/receive_gift/" + orderId, data);
}