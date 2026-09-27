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
 * Lấy thông tin chi tiết vòng quay may mắn
 * 
 */
export function getLotteryData(type, lottery_id) {
	return request.get(`v2/lottery/info/${type}${lottery_id ? '/' + lottery_id : ''}`);
}

/**
 * Tham gia quay thưởng
 * 
 */
export function startLottery(data) {
	return request.post(`v2/lottery`, data);
}

/**
 * Nhận thưởng
 * 
 */
export function receiveLottery(data) {
	return request.post(`v2/lottery/receive`, data);
}

/**
 * Lấy lịch sử trúng thưởng
 * 
 */
export function getLotteryList(data) {
	return request.get(`v2/lottery/record`, data);
}