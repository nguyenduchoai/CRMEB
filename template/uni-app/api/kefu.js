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
 * Đăng nhập CSKH
 * @param data object Tài khoản mật khẩu người dùng
 */
export function kefuLogin(data) {
	return request.post("login", data, {
		noAuth: true,
		kefu: true
	});
}

/**
 * Lấy danh sách người dùng chat bên trái (CSKH)
 * @constructor
 */
export function record(data) {
	return request.get("user/record", data, {
		noAuth: true,
		kefu: true
	});
}

/**
 * Câu trả lời mẫu CSKH
 * @constructor
 */
export function speeChcraft(data) {
	return request.get("service/speechcraft", data, {
		noAuth: true,
		kefu: true
	});
}

/**
 * Danh sách chuyển tiếp CSKH
 * @constructor
 */
export function transferList(data) {
	return request.get("service/transfer_list", data, {
		noAuth: true,
		kefu: true
	});
}

/**
 * Lịch sử mua sản phẩm
 * @constructor
 */
export function productCart(id, data) {
	return request.get("product/cart/" + id, data, {
		noAuth: true,
		kefu: true
	});
}

/**
 * Sản phẩm bán chạy
 * @constructor
 */
export function productHot(id, data) {
	return request.get("product/hot/" + id, data, {
		noAuth: true,
		kefu: true
	});
}

/**
 * Sản phẩm đã xem
 * @constructor
 */
export function productVisit(id, data) {
	return request.get("product/visit/" + id, data, {
		noAuth: true,
		kefu: true
	});
}

/**
 * Danh sách chat người dùng của CSKH
 * @constructor
 */
export function serviceList(data) {
	return request.get("service/list", data, {
		noAuth: true,
		kefu: true
	});
}

/**
 * Chuyển tiếp CSKH
 * @constructor
 */
export function serviceTransfer(data) {
	return request.post("service/transfer", data, {
		noAuth: true,
		kefu: true
	});
}

/**
 * Thông tin chi tiết CSKH
 * @constructor
 */
export function serviceInfo(data) {
	return request.get("service/info", data, {
		noAuth: true,
		kefu: true
	});
}

/**
 * Thông tin đầu phản hồi CSKH
 * @constructor
 */
export function serviceFeedBack() {
	return request.get("user/service/feedback");
}

/**
 * Phản hồi CSKH
 * @constructor
 */
export function feedBackPost(data) {
	return request.post("user/service/feedback", data);
}

/**
 * Kiểm tra code đăng nhập
 * @constructor
 */
export function codeStauts(data) {
	return request.get("user/code", data);
}
/**
 * Lấy cổng CSKH
 * @constructor
 */
export function getWorkermanUrl(data) {
	return request.get('get_workerman_url', {}, {
		noAuth: true
	})
}

/**
 * Code đăng nhập quét mã CSKH
 * @constructor
 */
export function kefuScanLogin(data) {
	return request.post("user/code", data);
}
