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

/*
 * Đăng nhập
 * */
export function AccountLogin(data) {
  return request({
    url: '/login',
    method: 'post',
    data,
  });
}

/**
 * Đăng xuất
 * @constructor
 */
export function AccountLogout() {
  return request({
    url: '/setting/admin/logout',
    method: 'get',
  });
}

/**
 * Lấy banner và logo
 */
export function loginInfoApi() {
  return request({
    url: '/login/info',
    method: 'get',
  });
}

/**
 * Lấy dữ liệu menu
 */
export function menusApi() {
  return request({
    url: '/menus',
    method: 'get',
  });
}

/**
 * Tìm kiếm dữ liệu menu
 */
export function menusListApi() {
  return request({
    url: '/menusList',
    method: 'get',
  });
}

export function AccountRegister() {}
