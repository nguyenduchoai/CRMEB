<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
use think\facade\Route;

/**
 * Route liên quan tải file, xuất file
 */
Route::group(function () {
    //Tải bảng lịch sử backup
    Route::get('backup/download', 'v1.system.SystemDatabackup/downloadFile')->option(['real_name' => 'Tải xuống bản sao lưu bảng']);
    //Dữ liệu thống kê trang chủ
    Route::get('home/header', 'Common/homeStatics')->option(['real_name' => 'Dữ liệu thống kê trang chủ']);
    //Biểu đồ đơn hàng trang chủ
    Route::get('home/order', 'Common/orderChart')->option(['real_name' => 'Biểu đồ đơn hàng trang chủ']);
    //Biểu đồ người dùng trang chủ
    Route::get('home/user', 'Common/userChart')->option(['real_name' => 'Biểu đồ người dùng trang chủ']);
    //Xếp hạng giá trị giao dịch trang chủ
    Route::get('home/rank', 'Common/purchaseRanking')->option(['real_name' => 'Xếp hạng giá trị giao dịch trang chủ']);
    //Thông báo nhắc nhở
    Route::get('jnotice', 'Common/jnotice')->option(['real_name' => 'Thông báo nhắc nhở']);
    //Xác minh cấp phép
    Route::get('check_auth', 'Common/auth')->option(['real_name' => 'Xác minh cấp phép']);
    //Yêu cầu cấp phép
    Route::post('auth_apply', 'Common/auth_apply')->option(['real_name' => 'Yêu cầu cấp phép']);
    //Ủy quyền
    Route::get('auth', 'Common/auth')->option(['real_name' => 'Thông tin cấp phép']);
    //Lấy menu bên trái
    Route::get('menus', 'v1.setting.SystemMenus/menus')->option(['real_name' => 'Menu bên trái']);
    //Lấy danh sách menu tìm kiếm
    Route::get('menusList', 'Common/menusList')->option(['real_name' => 'Tìm kiếm danh sách menu']);
    //Lấy logo
    Route::get('logo', 'Common/getLogo')->option(['real_name' => 'Lấy logo']);
    //Tra cứu bản quyền
    Route::get('copyright', 'Common/copyright')->option(['real_name' => 'Đăng ký bản quyền']);
    //Lưu bản quyền
    Route::post('copyright', 'Common/saveCopyright')->option(['real_name' => 'Lưu bản quyền']);
    //Tìm kiếm menu quản trị
    Route::post('menusSearch', 'Common/menusSearch')->option(['real_name' => 'Tìm kiếm menu quản trị']);
})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'common', 'mark_name' => 'Dữ liệu hệ thống']);

