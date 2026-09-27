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
 * 
 * API của tất cả hoạt động, bao gồm: mua chung, săn giảm giá, flash sale
 * 
 */

/**
 * Danh sách mua chung
 * 
 */
export function getCombinationList(data) {
	return request.get('combination/list', data, {
		noAuth: true
	});
}

/**
 * Chi tiết mua chung
 * 
 */
export function getCombinationDetail(id) {
	return request.get('combination/detail/' + id);
}

/**
 * Mua chung - Mở nhóm
 */
export function getCombinationPink(id) {
	return request.get("combination/pink/" + id);
}

/**
 * Mua chung - Hủy mở nhóm
 */
export function postCombinationRemove(data) {
	return request.post("combination/remove", data);
}

/**
 * Danh sách săn giảm giá
 */
export function getBargainList(data) {
	return request.get("bargain/list", data, {
		noAuth: true
	});
}

/**
 * Banner trình chiếu mua chung
 * 
 */
export function getCombinationBannerList(data) {
	return request.get('combination/banner_list', data, {
		noAuth: true
	});
}

/**
 * Số người mua chung
 * 
 */
export function getPink(data) {
	return request.get('pink', data, {
		noAuth: true
	});
}

/**
 * 
 * Danh sách săn giảm giá (đã tham gia)
 * @param object data
 */
export function getBargainUserList(data) {
	return request.get('bargain/user/list', data);
}


/**
 * Chi tiết sản phẩm săn giảm giá
 */
export function getBargainDetail(id, uid) {
	return request.get(`bargain/detail/${id}?bargainUid=${uid}`);
}

/**
 * Săn giảm giá - Thông tin người dùng bắt đầu săn giảm giá
 */
export function postBargainStartUser(data) {
	return request.post("bargain/start/user", data);
}

/**
 * Bắt đầu săn giảm giá
 */
export function postBargainStart(bargainId) {
	return request.post("bargain/start", {
		bargainId: bargainId
	});
}

/**
 * Săn giảm giá - Giúp bạn bè giảm giá
 */
export function postBargainHelp(data) {
	return request.post("bargain/help", data);
}

/**
 * Săn giảm giá - Số tiền đã giảm
 */
export function postBargainHelpPrice(data) {
	return request.post("bargain/help/price", data);
}

/**
 * Săn giảm giá - Người giúp giảm giá
 */
export function postBargainHelpList(data) {
	return request.post("bargain/help/list", data);
}

/**
 * Săn giảm giá - Tổng số người giúp giảm giá, số tiền còn lại, thanh tiến trình, giá đã giảm được
 */
export function postBargainHelpCount(data) {
	return request.post("bargain/help/count", data);
}

/**
 * Săn giảm giá - Số lần xem/chia sẻ/tham gia
 */
export function postBargainShare(bargainId) {
	return request.post("bargain/share", {
		bargainId: bargainId
	});
}

/**
 * Khoảng thời gian sản phẩm flash sale
 * 
 */
export function getSeckillIndexTime() {
	return request.get('seckill/index', {}, {
		noAuth: true
	});
}

/**
 * Danh sách sản phẩm flash sale
 * @param int time
 * @param object data
 */
export function getSeckillList(time, data) {
	return request.get('seckill/list/' + time, data, {
		noAuth: true
	});
}

/**
 * Chi tiết sản phẩm flash sale
 * @param int id
 */
export function getSeckillDetail(id, data) {
	return request.get(`seckill/detail/${id}`, data);
}

/**
 * Poster săn giảm giá
 * @param object data
 * 
 */
export function getBargainPoster(data) {
	return request.post('bargain/poster', data)
}

/**
 * Poster mua chung
 * @param object data
 * 
 */
export function getCombinationPoster(data) {
	return request.post('combination/poster', data)
}

/**
 * Hủy săn giảm giá
 */
export function getBargainUserCancel(data) {
	return request.post("bargain/user/cancel", data);
}

/**
 * Lấy mã QR Mini Program flash sale
 */
export function seckillCode(id, data) {
	return request.get("seckill/code/" + id, data);
}

/**
 * Lấy mã QR Mini Program mua chung
 */
export function scombinationCode(id) {
	return request.get("combination/code/" + id);
}

/**
 * Lấy thông tin chi tiết poster săn giảm giá
 */
export function getCombinationPosterData(id) {
	return request.get("combination/poster_info/" + id);
}


/**
 * Lấy thông tin chi tiết poster săn giảm giá
 */
export function getBargainPosterData(id) {
	return request.get("bargain/poster_info/" + id);
}

/**
 * Lấy thông tin chi tiết đơn hàng đổi điểm
 */
export function integralOrderConfirm(data) {
	return request.post('store_integral/order/confirm', data);
}

/**
 * Lấy tạo đơn hàng đổi điểm
 */
export function integralOrderCreate(data) {
	return request.post('store_integral/order/create', data);
}
/**
 * Lấy chi tiết đơn hàng đổi điểm
 * @param string cartId
 */
export function integralOrderDetails(order) {
	return request.get(`store_integral/order/detail/${order}`);
}

/**
 * Chi tiết sản phẩm đổi điểm
 * @param int id
 * 
 */
export function getIntegralProductDetail(id) {
	return request.get('store_integral/detail/' + id, {}, {
		noAuth: true
	});
}

/**
 * Danh sách sản phẩm cửa hàng đổi điểm
 * @param object data
 */
export function getStoreIntegralList(data) {
	return request.get('store_integral/list', data, {
		noAuth: true
	});
}

/**
 * Danh sách đổi điểm
 * @param object data
 */
export function getIntegralOrderList(data) {
	return request.get('store_integral/order/list', data);
}

/**
 * Chi tiết đổi điểm
 */
export function getLogisticsDetails(orderId) {
	return request.get(`store_integral/order/express/${orderId}`);
}

/**
 * Xác nhận đã nhận hàng đơn đổi điểm
 * @param object data
 */
export function orderTake(data) {
	return request.post(`store_integral/order/take`, data);
}

/**
 * Xóa đơn đổi điểm
 * @param object data
 */
export function orderDel(data) {
	return request.post(`store_integral/order/del`, data);
}

/**
 * Danh sách sản phẩm đặt trước
 */
export function getPresellList(data) {
	return request.get("advance/list", data);
}