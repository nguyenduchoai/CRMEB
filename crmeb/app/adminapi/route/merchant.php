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
 * Route liên quan Mẫu phí vận chuyển
 */
Route::group('merchant', function () {

    /** Cửa hàng */
    Route::group(function () {
        //Chi tiết cài đặt cửa hàng
        Route::get('store', 'v1.merchant.SystemStore/index')->option(['real_name' => 'Danh sách cửa hàng']);
        //Số lượng trong danh sách cửa hàng
        Route::get('store/get_header', 'v1.merchant.SystemStore/get_header')->option(['real_name' => 'Dữ liệu phần đầu danh sách cửa hàng']);
        //Số lượng trong danh sách cửa hàng
        Route::put('store/set_show/:id/:is_show', 'v1.merchant.SystemStore/set_show')->option(['real_name' => 'Hiện/ẩn cửa hàng']);
        //Số lượng trong danh sách cửa hàng
        Route::delete('store/del/:id', 'v1.merchant.SystemStore/delete')->option(['real_name' => 'Xóa cửa hàng']);
        //Chọn vị trí
        Route::get('store/address', 'v1.merchant.SystemStore/select_address')->option(['real_name' => 'Chọn vị trí cửa hàng']);
        //Chi tiết cài đặt cửa hàng
        Route::get('store/get_info/:id', 'v1.merchant.SystemStore/get_info')->option(['real_name' => 'Chi tiết cửa hàng']);
        //Lưu chỉnh sửa thông tin cửa hàng
        Route::post('store/:id', 'v1.merchant.SystemStore/save')->option(['real_name' => 'Lưu chỉnh sửa thông tin cửa hàng']);
    })->option(['parent' => 'merchant', 'cate_name' => 'Cửa hàng']);

    /** Nhân viên cửa hàng */
    Route::group(function () {
        //Lấy danh sách nhân viên cửa hàng
        Route::get('store_staff', 'v1.merchant.SystemStoreStaff/index')->option(['real_name' => 'Lấy danh sách nhân viên cửa hàng']);
        //Form thêm nhân viên cửa hàng
        Route::get('store_staff/create', 'v1.merchant.SystemStoreStaff/create')->option(['real_name' => 'Biểu mẫu thêm nhân viên cửa hàng']);
        //Danh sách tìm kiếm cửa hàng
        Route::get('store_list', 'v1.merchant.SystemStoreStaff/store_list')->option(['real_name' => 'Danh sách tìm kiếm cửa hàng']);
        //Sửa trạng thái nhân viên cửa hàng
        Route::put('store_staff/set_show/:id/:is_show', 'v1.merchant.SystemStoreStaff/set_show')->option(['real_name' => 'Sửa trạng thái nhân viên cửa hàng']);
        //Biểu mẫu sửa nhân viên cửa hàng
        Route::get('store_staff/:id/edit', 'v1.merchant.SystemStoreStaff/edit')->option(['real_name' => 'Biểu mẫu sửa nhân viên cửa hàng']);
        //Lưu nhân viên cửa hàng
        Route::post('store_staff/save/:id', 'v1.merchant.SystemStoreStaff/save')->option(['real_name' => 'Lưu nhân viên cửa hàng']);
        //Xóa nhân viên cửa hàng
        Route::delete('store_staff/del/:id', 'v1.merchant.SystemStoreStaff/delete')->option(['real_name' => 'Xóa nhân viên cửa hàng']);
    })->option(['parent' => 'merchant', 'cate_name' => 'Nhân viên cửa hàng']);

    /** Đơn hàng xác nhận sử dụng */
    Route::group(function () {
        //Lấy danh sách đơn hàng xác nhận sử dụng
        Route::get('verify_order', 'v1.merchant.SystemVerifyOrder/list')->option(['real_name' => 'Lấy danh sách đơn hàng xác nhận sử dụng']);
        //Lấy dữ liệu phần đầu đơn hàng xác nhận sử dụng
        Route::get('verify_badge', 'v1.merchant.SystemVerifyOrder/getVerifyBadge')->option(['real_name' => 'Lấy dữ liệu phần đầu đơn hàng xác nhận sử dụng']);
        //Lấy dữ liệu phần đầu đơn hàng xác nhận sử dụng
        Route::get('verify/spread_info/:uid', 'v1.merchant.SystemVerifyOrder/order_spread_user')->option(['real_name' => 'Thông tin người giới thiệu của đơn hàng xác nhận sử dụng']);
    })->option(['parent' => 'merchant', 'cate_name' => 'Đơn hàng xác nhận sử dụng']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'merchant', 'mark_name' => 'Xác nhận sử dụng tại cửa hàng']);
