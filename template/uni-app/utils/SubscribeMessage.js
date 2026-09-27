// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2024 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import {
	SUBSCRIBE_MESSAGE
} from '../config/cache.js';

export function auth() {
	let tmplIds = {};
	let messageTmplIds = uni.getStorageSync(SUBSCRIBE_MESSAGE);
	tmplIds = messageTmplIds ? JSON.parse(messageTmplIds) : {};
	return tmplIds;
}

/**
 * ID tin nhắn đăng ký sau khi thanh toán thành công
 * Đăng ký: thông báo xác nhận đã nhận hàng, thanh toán đơn hàng thành công, nhắc quản trị viên có đơn hàng mới 
 */
export function openPaySubscribe() {
	let tmplIds = auth();
	return subscribe([
		tmplIds.order_pay_success,
		tmplIds.order_deliver_success,
		tmplIds.order_postage_success,
	]);
}

/**
 * Tin nhắn đăng ký liên quan đến đơn hàng
 * Giao hàng, gửi hàng, hủy đơn hàng
 */
export function openOrderSubscribe() {
	let tmplIds = auth();
	return subscribe([
		tmplIds.order_take,
		tmplIds.integral_accout
	]);
}

/**
 * Đăng ký tin nhắn rút tiền
 * Tin nhắn thành công và thất bại
 */
export function openExtrctSubscribe() {
	let tmplIds = auth();
	return subscribe([
		tmplIds.user_extract
	]);
}

/**
 * Mua chung thành công
 */
export function openPinkSubscribe() {
	let tmplIds = auth();
	return subscribe([
		tmplIds.order_user_groups_success
	]);
}

/**
 * Săn giảm giá thành công
 */
export function openBargainSubscribe() {
	let tmplIds = auth();
	return subscribe([
		tmplIds.bargain_success
	]);
}

/**
 * Hoàn tiền đơn hàng
 */
export function openOrderRefundSubscribe() {
	let tmplIds = auth();
	return subscribe([
		tmplIds.order_refund
	]);
}

/**
 * Nạp tiền thành công
 */
export function openRechargeSubscribe() {
	let tmplIds = auth();
	return subscribe([
		tmplIds.recharge_success
	]);
}

/**
 * Rút tiền thành công
 */
export function openRevenueSubscribe() {
	let tmplIds = auth();
	return subscribe([
		tmplIds.revenue_received
	]);
}

/**
 * Mở màn hình đăng ký
 * array tmplIds ID mẫu
 */
export function subscribe(subscrip443tionmessagee502call) {
	 let weChat = wx;
	return new Promise((reslove, reject) => {
		weChat.requestSubscribeMessage({
			tmplIds: subscrip443tionmessagee502call,
			success(res) {
				return reslove(res);
			},
			fail(res) {
				return reslove(res);
			}
		})
	});
}
