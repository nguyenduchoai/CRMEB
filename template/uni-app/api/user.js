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
 * Lấy thông tin người dùng
 * 
 */
export function getUserInfo() {
	return request.get('user');
}


/**
 * Đặt chia sẻ người dùng
 * 
 */
export function userShare() {
	return request.post('user/share');
}

/**
 * Đăng nhập người dùng h5
 * @param data object Tài khoản mật khẩu người dùng
 */
export function loginH5(data) {
	return request.post("login", data, {
		noAuth: true
	});
}

/**
 * Đăng nhập số điện thoại người dùng h5
 * @param data object Số điện thoại người dùng, cũng chỉ có thể
 */
export function loginMobile(data) {
	return request.post("login/mobile", data, {
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
 * h5 người dùng gửi mã xác thực (OTP)
 * @param data object Số điện thoại người dùng
 */
export function registerVerify(data) {
	return request.post("register/verify", data, {
		noAuth: true
	});
}

/**
 * Đăng ký số điện thoại người dùng h5
 * @param data object Số điện thoại người dùng, mã xác thực, mật khẩu
 */
export function register(data) {
	return request.post("register", data, {
		noAuth: true
	});
}

/**
 * Đổi mật khẩu bằng số điện thoại người dùng
 * @param data object Số điện thoại người dùng, mã xác thực, mật khẩu
 */
export function registerReset(data) {
	return request.post("register/reset", data, {
		noAuth: true
	});
}

/**
 * Lấy menu trang cá nhân người dùng
 *
 */
export function getMenuList() {
	return request.get("menu/user", {}, {
		noAuth: true
	});
}

/*
 * Thông tin người dùng điểm danh
 * */
export function postSignUser(sign) {
	return request.post("sign/user", sign);
}

/**
 * Lấy cấu hình điểm danh
 * 
 */
export function getSignConfig() {
	return request.get('sign/config')
}

/**
 * Lấy danh sách điểm danh
 * @param object data
 */
export function getSignList(data) {
	return request.get('sign/list', data);
}

/**
 * Người dùng điểm danh
 */
export function setSignIntegral() {
	return request.post('sign/integral')
}

/**
 * Danh sách điểm danh (năm tháng)
 * @param object data
 * 
 */
export function getSignMonthList(data) {
	return request.get('sign/month', data)
}

/**
 * Trạng thái chương trình
 * 
 */
export function userActivity() {
	return request.get('user/activity');
}

/*
 * Chi tiết dòng tiền (types|0=tất cả,1=tiêu dùng,2=nạp tiền,3=trả hoa hồng,4=rút tiền)
 * */
export function getCommissionInfo(q, types) {
	return request.get("spread/commission/" + types, q);
}

/*
 * Lịch sử điểm thưởng
 * */
export function getIntegralList(q) {
	return request.get("integral/list", q);
}

/**
 * Lấy ảnh poster tiếp thị liên kết
 * 
 */
export function spreadBanner() {
	//#ifdef H5 || APP-PLUS
	return request.get('spread/banner', {
		type: 2
	});
	//#endif
	//#ifdef MP
	return request.get('spread/banner', {
		type: 1
	});
	//#endif

}

/**
 *
 * Lấy người dùng giới thiệu cấp 1 và cấp 2
 * @param object data
 */
export function spreadPeople(data) {
	return request.post('spread/people', data);
}

/**
 * 
 * Tổng hoa hồng giới thiệu/rút tiền
 * @param int type
 */
export function spreadCount(type) {
	return request.get('spread/count/' + type);
}

/*
 * Dữ liệu giới thiệu
 * */
export function getSpreadInfo() {
	return request.get("commission");
}


/**
 * 
 * Đơn hàng giới thiệu
 * @param object data
 */
export function spreadOrder(data) {
	return request.post('spread/order', data);
}

/**
 * 
 * Đơn hàng đại lý khu vực/giới thiệu
 * @param object data
 */
export function divisionOrder(data) {
	return request.post('division/order', data);
}

/*
 * Lấy bảng xếp hạng người giới thiệu
 * */
export function getRankList(q) {
	return request.get("rank", q);
}

/*
 * Lấy xếp hạng hoa hồng
 * */
export function getBrokerageRank(q) {
	return request.get("brokerage_rank", q);
}

/**
 * Yêu cầu rút tiền
 * @param object data
 */
export function extractCash(data) {
	return request.post('extract/cash', data)
}

/**
 * Ngân hàng rút tiền/số tiền rút tối thiểu
 * 
 */
export function extractBank() {
	return request.get('extract/bank');
}

/**
 * Danh sách hạng thành viên
 * 
 */
export function userLevelGrade() {
	return request.get('user/level/grade');
}

/**
 * Lấy nhiệm vụ của một hạng nào đó
 * @param int id ID nhiệm vụ
 */
export function userLevelTask(id) {
	return request.get('user/level/task/' + id);
}


/**
 * Kiểm tra người dùng có thể trở thành thành viên hay không
 * 
 */
export function userLevelDetection() {
	return request.get('user/level/detection');
}

/**
 * 
 * Danh sách địa chỉ
 * @param object data
 */
export function getAddressList(data) {
	return request.get('address/list', data);
}

/**
 * Đặt địa chỉ mặc định
 * @param int id
 */
export function setAddressDefault(id) {
	return request.post('address/default/set', {
		id: id
	})
}

/**
 * Sửa - Thêm địa chỉ
 * @param object data
 */
export function editAddress(data) {
	return request.post('address/edit', data);
}

/**
 * Xóa địa chỉ
 * @param int id
 * 
 */
export function delAddress(id) {
	return request.post('address/del', {
		id: id
	})
}

/**
 * Lấy một địa chỉ
 * @param int id 
 */
export function getAddressDetail(id) {
	return request.get('address/detail/' + id);
}

/**
 * Chỉnh sửa thông tin người dùng
 * @param object
 */
export function userEdit(data) {
	return request.post('user/edit', data);
}

/*
 * Đăng xuất
 * */
export function getLogout() {
	return request.get("logout");
}
/**
 * Nạp tiền qua Mini Program
 * 
 */
export function rechargeRoutine(data) {
	return request.post('recharge/routine', data)
}
/*
 * Nạp tiền qua OA WeChat
 * 
 */
export function rechargeWechat(data) {
	return request.post("recharge/wechat", data);
}
/*
 * Nạp tiền qua OA WeChat
 * 
 */
export function recharge(data) {
	return request.post("recharge/recharge", data);
}
/**
 * Lấy địa chỉ mặc định
 * 
 */
export function getAddressDefault() {
	return request.get('address/default');
}

/**
 * Chọn số tiền nạp
 */
export function getRechargeApi() {
	return request.get("recharge/index");
}

/**
 * Lịch sử đăng nhập
 */
export function setVisit(data) {
	return request.post('user/set_visit', {
		...data
	}, {
		noAuth: true
	});
}

/**
 * Danh sách nhân viên CSKH
 */
export function serviceList() {
	return request.get("user/service/list");
}
/**
 * Chi tiết CSKH
 */
export function getChatRecord(data) {
	return request.get("v2/user/service/record", data);
}

/**
 * Liên kết ngầm với người giới thiệu
 * @param {Object} puid
 */
export function spread(puid) {
	return request.post("user/spread", puid);
}

/**
 * Chi tiết thành viên
 */
export function getlevelInfo() {
	return request.get("user/level/info");
}

/**
 * Danh sách điểm kinh nghiệm thành viên
 */
export function getlevelExpList(data) {
	return request.get("user/level/expList", data);
}


/**
 * Đăng nhập trực tiếp bằng số điện thoại qua WeChat
 */
export function phoneWxSilenceAuth(data) {
	return request.post('v2/phone_wx_silence_auth', data, {
		noAuth: true
	});
}

/**
 * Đăng nhập trực tiếp bằng số điện thoại qua Mini Program
 */
export function phoneSilenceAuth(data) {
	return request.post('v2/phone_silence_auth', data, {
		noAuth: true
	});
}

/**
 * Danh sách hóa đơn của người dùng
 * @param {Object} data
 */
export function invoiceList(data) {
	return request.get('v2/invoice', data, {
		noAuth: true
	});
}

/**
 * Người dùng thêm|sửa hóa đơn
 * @param {Object} data
 */
export function invoiceSave(data) {
	return request.post('v2/invoice/save', data, {
		noAuth: true
	});
}

/**
 * Người dùng xóa hóa đơn
 * @param {Object} data
 */
export function invoiceDelete(id) {
	return request.get('v2/invoice/del/' + id);
}

/**
 * Lấy hóa đơn mặc định của người dùng
 * @param {Object} type
 */
export function invoiceDefault(type) {
	return request.get('v2/invoice/get_default/' + type);
}

/**
 * Chi tiết một hóa đơn của người dùng
 * @param {Object} id
 */
export function invoiceDetail(id) {
	return request.get('v2/invoice/detail/' + id);
}

/**
 * Đơn hàng yêu cầu xuất hóa đơn
 * @param {Object} id
 */
export function invoiceOrder(data) {
	return request.post('v2/order/make_up_invoice', data);
}

/**
 * Yêu cầu xuất hóa đơn trong chi tiết đơn hàng
 * @param {Object} id
 */
export function makeUpinvoice(data) {
	return request.post('v2/order/make_up_invoice', data);
}

/**
 * Giao diện chính thẻ thành viên
 */
export function memberCard() {
	return request.get('user/member/card/index');
}

/**
 * Nhận thẻ thành viên bằng mã thẻ
 * @param {Object} data
 */
export function memberCardDraw(data) {
	return request.post('user/member/card/draw', data);
}

/**
 * Mua thẻ thành viên
 * @param {Object} data
 */
export function memberCardCreate(data) {
	return request.post('user/member/card/create', data);
}

/**
 * Phiếu giảm giá thành viên
 */
export function memberCouponsList() {
	return request.get('user/member/coupons/list');
}

/**
 * Sản phẩm đề xuất svip
 * @param {Object} id
 */
export function groomList(id, data) {
	return request.get(`groom/list/${id}`, data);
}

/**
 * Kết thúc thành viên trả phí
 * @param {Object} data
 */
export function memberOverdueTime(data) {
	return request.get('user/member/overdue/time', data);
}

/**
 * Lấy thông tin poster chia sẻ phiên bản mới
 * 
 */
export function spreadMsg() {
	return request.get('user/spread_info');
}


/**
 * Chuyển link ảnh sang base64
 * 
 */
export function imgToBase(data) {
	return request.post('image_base64', data);
}

/**
 * Lấy mã QR Mini Program
 * 
 */
export function routineCode(data) {
	return request.get('user/routine_code', data);
}

/**
 * Trung tâm tin nhắn
 */
export function serviceRecord(data) {
	return request.get('user/record', data);
}

/**
 * Trung tâm tin nhắn - danh sách thông báo nội bộ
 */
export function messageSystem(data) {
	return request.get('user/message_system/list', data);
}

/**
 * Trung tâm tin nhắn - chi tiết danh sách thông báo nội bộ
 */
export function getMsgDetails(id) {
	return request.get('user/message_system/detail/' + id);
}

/**
 * Trung tâm tin nhắn - tin nhắn đã đọc/xóa
 */
export function msgLookDel(data) {
	return request.get('user/message_system/edit_message', data);
}

/**
 * Đăng nhập tài khoản Apple
 * @param {Object} data
 */
export function appleLogin(data) {
	return request.post('apple_login', data, {
		noAuth: true
	});
}

/*
 * Lấy chính sách bảo mật
 * */
export function getUserAgreement(type) {
	return request.get(`get_agreement/${type}`, {}, {
		noAuth: true
	});
}

/**
 * Lấy danh sách cấp độ CTV
 * @param int id ID nhiệm vụ
 */
export function agentLevelList() {
	return request.get('v2/agent/level_list');
}

/**
 * Lấy danh sách nhiệm vụ tiếp thị liên kết
 * @param int id ID nhiệm vụ
 */
export function agentLevelTaskList(id) {
	return request.get('v2/agent/level_task_list?id=' + id);
}

/**
 * Lấy chi tiết thanh toán hộ
 * @param int id ID nhiệm vụ
 */
export function friendDetail(id) {
	return request.get('order/friend_detail?order_id=' + id);
}

/**
 * Danh sách nhân viên
 * @param object data
 * 
 */
export function clerkPeople(data) {
	return request.get('agent/get_staff_list', data)
}

/**
 * 
 * Tỷ lệ nhân viên
 * @param object data
 */
export function setClerkPercent(data) {
	return request.post('agent/set_staff_percent', data);
}

/**
 * 
 * Xóa nhân viên
 * @param object data
 */
export function delClerkPercent(id) {
	return request.get(`agent/del_staff/${id}`);
}

/**
 * Hủy tài khoản người dùng
 * @param int id
 * 
 */
export function cancelUser() {
	return request.get('user_cancel');
}
/**
 * Lấy loại đa ngôn ngữ
 */

export function getLangList() {
	return request.get('get_lang_type_list', {}, {
		noAuth: true
	})
}

/**
 * Lấy JSON đa ngôn ngữ
 */

export function getLangJson() {
	return request.get('get_lang_json', {}, {
		noAuth: true
	})
}

/**
 * Lấy trạng thái có chuyển đổi đa ngôn ngữ hay không
 */

export function getLangVersion() {
	return request.get('lang_version', {}, {
		noAuth: true
	})
}

/**
 * 
 * Liên kết số điện thoại trên Mini Program
 * @param object data
 */
export function mpBindingPhone(data) {
	return request.post('v2/routine/binding_phone', data);
}

/**
 *  Chuyển đổi nhắc nhở điểm danh
 */

export function changeRemindStatus(status) {
	return request.get(`sign/remind/${status}`, {}, {
		noAuth: true
	})
}


/**
 * Liên kết nhân viên
 * 
 */
export function spreadAgent(data) {
	return request.post(`agent/spread`, data);
}

// Người dùng xác nhận chuyển khoản cho shop
export function transferInfoApi(data) {
	return request.get(`transfer/info`, data);
}