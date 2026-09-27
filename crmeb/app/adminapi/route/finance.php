<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
use think\facade\Route;

/**
 * Route liên quan Module tài chính
 */
Route::group('finance', function () {

    /** Rút tiền */
    Route::group(function () {
        //Danh sách yêu cầu
        Route::get('extract', 'v1.finance.UserExtract/index')->option(['real_name' => 'Danh sách yêu cầu rút tiền']);
        //Form sửa
        Route::get('extract/:id/edit', 'v1.finance.UserExtract/edit')->option(['real_name' => 'Biểu mẫu sửa bản ghi rút tiền']);
        //Lưu thay đổi
        Route::put('extract/:id', 'v1.finance.UserExtract/update')->option(['real_name' => 'Sửa bản ghi rút tiền']);
        //Từ chối yêu cầu
        Route::put('extract/refuse/:id', 'v1.finance.UserExtract/refuse')->option(['real_name' => 'Từ chối yêu cầu rút tiền']);
        //Duyệt yêu cầu
        Route::put('extract/adopt/:id', 'v1.finance.UserExtract/adopt')->option(['real_name' => 'Duyệt yêu cầu rút tiền']);
    })->option(['parent' => 'finance', 'cate_name' => 'Rút tiền']);

    /** Lịch sử dòng tiền */
    Route::group(function () {
        //Loại bộ lọc
        Route::get('finance/bill_type', 'v1.finance.Finance/bill_type')->option(['real_name' => 'Loại bản ghi dòng tiền']);
        //Lịch sử dòng tiền
        Route::get('finance/list', 'v1.finance.Finance/list')->option(['real_name' => 'Danh sách lịch sử dòng tiền']);
        //Lịch sử hoa hồng
        Route::get('finance/commission_list', 'v1.finance.Finance/get_commission_list')->option(['real_name' => 'Danh sách lịch sử hoa hồng']);
        //Thông tin người dùng trong chi tiết hoa hồng
        Route::get('finance/user_info/:id', 'v1.finance.Finance/user_info')->option(['real_name' => 'Thông tin người dùng trong chi tiết hoa hồng']);
        //Danh sách lịch sử rút hoa hồng của cá nhân
        Route::get('finance/extract_list/:id', 'v1.finance.Finance/get_extract_list')->option(['real_name' => 'Danh sách lịch sử rút hoa hồng của cá nhân']);
        /** Lịch sử số dư */
        Route::get('balance/list', 'v1.finance.UserBalance/balanceList')->option(['real_name' => 'Danh sách lịch sử số dư']);
        Route::post('balance/set_mark/:id', 'v1.finance.UserBalance/balanceRecordRemark')->option(['real_name' => 'Ghi chú lịch sử số dư']);
    })->option(['parent' => 'finance', 'cate_name' => 'Lịch sử dòng tiền']);

    /** Nạp tiền */
    Route::group(function () {
        //Danh sách lịch sử nạp tiền
        Route::get('recharge', 'v1.finance.UserRecharge/index')->option(['real_name' => 'Danh sách lịch sử nạp tiền']);
        //Xóa lịch sử
        Route::delete('recharge/:id', 'v1.finance.UserRecharge/delete')->option(['real_name' => 'Xóa bản ghi nạp tiền']);
        //Lấy dữ liệu nạp tiền của người dùng
        Route::get('recharge/user_recharge', 'v1.finance.UserRecharge/user_recharge')->option(['real_name' => 'Lấy dữ liệu nạp tiền của người dùng']);
        //Form hoàn tiền
        Route::get('recharge/:id/refund_edit', 'v1.finance.UserRecharge/refund_edit')->option(['real_name' => 'Biểu mẫu hoàn tiền nạp']);
        //Hoàn tiền
        Route::put('recharge/:id', 'v1.finance.UserRecharge/refund_update')->option(['real_name' => 'Hoàn tiền nạp']);
    })->option(['parent' => 'finance', 'cate_name' => 'Nạp tiền']);


})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'finance', 'mark_name' => 'Quản lý tài chính']);
