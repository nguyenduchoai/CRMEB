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
 * Route liên quan xuất excel
 */
Route::group('export', function () {
    //Danh sách người dùng
    Route::get('user_list', 'v1.export.ExportExcel/userList')->option(['real_name' => 'Xuất danh sách người dùng']);
    //Danh sách đơn hàng
    Route::get('order_list', 'v1.export.ExportExcel/orderList')->option(['real_name' => 'Xuất danh sách đơn hàng']);
    //Danh sách đơn hàng đã giao
    Route::get('order_delivery_list', 'v1.export.ExportExcel/orderDeliveryList')->option(['real_name' => 'Xuất danh sách đơn hàng cần giao']);
    //Danh sách sản phẩm
    Route::get('product_list', 'v1.export.ExportExcel/productList')->option(['real_name' => 'Xuất danh sách sản phẩm']);
    //Danh sách săn giảm giá
    Route::get('bargain_list', 'v1.export.ExportExcel/bargainList')->option(['real_name' => 'Xuất danh sách sản phẩm săn giảm giá']);
    //Danh sách mua chung
    Route::get('combination_list', 'v1.export.ExportExcel/combinationList')->option(['real_name' => 'Xuất danh sách sản phẩm mua chung']);
    //Danh sách flash sale
    Route::get('seckill_list', 'v1.export.ExportExcel/seckillList')->option(['real_name' => 'Xuất danh sách sản phẩm flash sale']);
    //Xuất thẻ thành viên
    Route::get('member_card/:id', 'v1.export.ExportExcel/memberCardList')->option(['real_name' => 'Xuất thẻ thành viên']);
    //Danh sách người dùng CTV giới thiệu
    Route::get('userAgent', 'v1.export.ExportExcel/userAgent')->option(['real_name' => 'Xuất danh sách giới thiệu của cộng tác viên']);
    //Theo dõi tài chính người dùng
    Route::get('userFinance', 'v1.export.ExportExcel/userFinance')->option(['real_name' => 'Xuất dòng tiền người dùng']);
    //Hoa hồng người dùng
    Route::get('userCommission', 'v1.export.ExportExcel/userCommission')->option(['real_name' => 'Xuất hoa hồng người dùng']);
    //Điểm thưởng người dùng
    Route::get('userPoint', 'v1.export.ExportExcel/userPoint')->option(['real_name' => 'Xuất điểm thưởng người dùng']);
    //Nạp tiền người dùng
    Route::get('userRecharge', 'v1.export.ExportExcel/userRecharge')->option(['real_name' => 'Xuất lịch sử nạp tiền người dùng']);
})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'export', 'mark_name' => 'Xuất dữ liệu']);
