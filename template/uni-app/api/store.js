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
 * Lấy chi tiết sản phẩm
 * @param int id
 * 
 */
export function getProductDetail(id) {
	return request.get('product/detail/' + id, {}, {
		noAuth: true
	});
}

/**
 * Mã QR chia sẻ sản phẩm, cộng tác viên (CTV)
 * @param int id
 */
// #ifdef H5  || APP-PLUS
export function getProductCode(id) {
	return request.get('product/code/' + id, {}, {
		noAuth: true
	});
}
// #endif
// #ifdef MP
export function getProductCode(id) {
	return request.get('product/code/' + id, {
		user_type: 'routine',
	}, {
		noAuth: true
	});
}
// #endif

/**
 * Thêm yêu thích
 * @param int id
 * @param string category product=sản phẩm thường, product_seckill=sản phẩm flash sale
 */
export function collectAdd(id, category) {
	return request.post('collect/add', {
		id: id,
		'product': category === undefined ? 'product' : category
	});
}

/**
 * Xóa sản phẩm yêu thích
 * @param int id
 * @param string category product=sản phẩm thường, product_seckill=sản phẩm flash sale
 */
export function collectDel(id, category) {
	return request.post('collect/del', {
		id: id,
		category: category === undefined ? 'product' : category
	});
}

/**
 * Thêm vào giỏ hàng
 * 
 */
export function postCartAdd(data) {
	return request.post('cart/add', data);
}

/**
 * Lấy danh sách danh mục
 * 
 */
export function getCategoryList() {
	return request.get('category', {}, {
		noAuth: true
	});
}

/**
 * Lấy danh sách sản phẩm
 * @param object data
 */
export function getProductslist(data) {
	return request.get('products', data, {
		noAuth: true
	});
}



/**
 * Lấy sản phẩm đề xuất
 * 
 */
export function getProductHot(page, limit) {
	return request.get("product/hot", {
		page: page === undefined ? 1 : page,
		limit: limit === undefined ? 4 : limit
	}, {
		noAuth: true
	});
}
/**
 * Yêu thích theo lô
 * 
 * @param object id  Mã sản phẩm join(',') cắt thành chuỗi
 * @param string category 
 */
export function collectAll(id, category) {
	return request.post('collect/all', {
		id: id,
		category: category === undefined ? 'product' : category
	});
}

/**
 * Banner trình chiếu sản phẩm và thông tin sản phẩm trang chủ
 * @param int type 
 * 
 */
export function getGroomList(type, data) {
	return request.get('groom/list/' + type, data, {
		noAuth: true
	});
}

/**
 * Lấy danh sách yêu thích
 * @param object data
 */
export function getCollectUserList(data) {
	return request.get('collect/user', data)
}

/**
 * Lấy đánh giá sản phẩm
 * @param int id
 * @param object data
 * 
 */
export function getReplyList(id, data) {
	return request.get('reply/list/' + id, data)
}

/**
 * Số lượng đánh giá sản phẩm và tỷ lệ đánh giá tốt
 * @param int id
 */
export function getReplyConfig(id) {
	return request.get('reply/config/' + id);
}

/**
 * Lấy từ khóa tìm kiếm
 * 
 */
export function getSearchKeyword() {
	return request.get('search/keyword', {}, {
		noAuth: true
	});
}

/**
 * Danh sách cửa hàng
 * @returns {*}
 */
export function storeListApi(data) {
	return request.get("store_list", data);
}

/**
 * Danh sách combo
 * @param int id
 * 
 */
export function storeDiscountsList(id) {
	return request.get('store_discounts/list/' + id, {}, {
		noAuth: true
	});
}

/**
 * Thêm, giảm, sửa giỏ hàng
 * 
 */
export function postCartNum(data) {
	return request.post('v2/set_cart_num', data);
}
/**
 * Đăng ký đại lý
 * 
 */
export function create(data) {
	return request.post(`agent/apply/${data.id}`, data);
}

/**
 * Quy định đại lý
 * @param object data
 */
export function getAgentAgreement(data) {
	return request.get('agent/get_agent_agreement', {}, {
		noAuth: true
	});
}

/**
 * h5 người dùng gửi mã xác thực (OTP)
 * @param data object Số điện thoại người dùng
 */
export function registerVerify(data) {
	return request.post("register/verify", data, {
		noAuth: true
	});
}

/**
 * Key mã xác thực (OTP)
 */
export function getCodeApi() {
	return request.get("verify_code", {}, {
		noAuth: true
	});
}
/**
 * Lấy thông tin form đại lý
 */
export function getHistoryData() {
	return request.get("agent/apply/info", {}, {
		noAuth: true
	});
}

/**
 * Lấy thuộc tính trang chủ
 * @returns {*}
 */
export function getAttr(id, type) {
	return request.get("v2/get_attr/" + id + "/" + type);
}
/**
 * Lấy danh sách sản phẩm trang chủ (tất cả hoạt động)
 * @param object data
 */
export function getHomeProducts(data) {
	return request.get('home/products', data, {
		noAuth: true
	});
}

/**
 * Chi tiết đặt trước
 * @returns {*}
 */
export function getPresellProductDetail(id) {
	return request.get("advance/detail/" + id);
}

/**
 * Lấy danh sách lịch sử xem
 * @param object data
 */
export function getVisitList(data) {
	return request.get('user/visit_list', data)
}

/**
 * Lấy danh sách lịch sử xem - xóa 
 * @param object data
 */
export function deleteVisitList(data) {
	return request.delete('user/visit', data)
}

/**
 * API chi tiết đăng ký cộng tác viên (CTV)
 *
 */
export function userSpreadInfo() {
	return request.get("user/spread/apply/info");
}

/**
 * Đơn đăng ký cộng tác viên
 * @param data
 * 
 */
export function spreadCreateApi(id, data) {
	return request.post(`user/spread/apply/${id}`, data);
}

/**
 * Lấy giá thực nhận
 * 
 */
export function realPrice(id, unique) {
	return request.get(`product/real_price/${id}/${unique}`, {}, {
		noAuth: true
	});
}