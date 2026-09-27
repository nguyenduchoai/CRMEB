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
 * @description Phần đầu trang chủ
 */
export function headerApi() {
  return request({
    url: 'home/header',
    method: 'get',
  });
}

/**
 * @description Biểu đồ đơn hàng trang chủ
 */
export function orderApi(params) {
  return request({
    url: 'home/order',
    method: 'get',
    params,
  });
}

/**
 * @description Biểu đồ đơn hàng trang chủ
 */
export function userApi() {
  return request({
    url: 'home/user',
    method: 'get',
  });
}

/**
 * @description Xếp hạng doanh thu sản phẩm trang chủ
 */
export function rankApi() {
  return request({
    url: 'home/rank',
    method: 'get',
  });
}
