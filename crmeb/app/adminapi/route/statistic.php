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
 * Route liên quan Quản lý CTV
 */
Route::group('statistic', function () {

    /** Thống kê người dùng */
    Route::group(function () {
        //Thông tin cơ bản người dùng
        Route::get('user/get_basic', 'v1.statistic.UserStatistic/getBasic')->option(['real_name' => 'Thống kê cơ bản người dùng']);
        //Xu hướng tăng trưởng người dùng
        Route::get('user/get_trend', 'v1.statistic.UserStatistic/getTrend')->option(['real_name' => 'Xu hướng tăng trưởng người dùng']);
        //Người dùng WeChat
        Route::get('user/get_wechat', 'v1.statistic.UserStatistic/getWechat')->option(['real_name' => 'Thống kê người dùng WeChat']);
        //Xu hướng tăng trưởng người dùng WeChat
        Route::get('user/get_wechat_trend', 'v1.statistic.UserStatistic/getWechatTrend')->option(['real_name' => 'Xu hướng tăng trưởng người dùng WeChat']);
        //Xếp hạng khu vực người dùng
        Route::get('user/get_region', 'v1.statistic.UserStatistic/getRegion')->option(['real_name' => 'Xếp hạng khu vực người dùng']);
        //Giới tính người dùng
        Route::get('user/get_sex', 'v1.statistic.UserStatistic/getSex')->option(['real_name' => 'Phân bố giới tính người dùng']);
        //Xuất dữ liệu sản phẩm
        Route::get('user/get_excel', 'v1.statistic.UserStatistic/getExcel')->option(['real_name' => 'Xuất dữ liệu người dùng']);
    })->option(['parent' => 'statistic', 'cate_name' => 'Thống kê người dùng']);

    /** Thống kê sản phẩm */
    Route::group(function () {
        //Thông tin cơ bản sản phẩm
        Route::get('product/get_basic', 'v1.statistic.ProductStatistic/getBasic')->option(['real_name' => 'Thống kê cơ bản sản phẩm']);
        //Xu hướng sản phẩm
        Route::get('product/get_trend', 'v1.statistic.ProductStatistic/getTrend')->option(['real_name' => 'Xu hướng sản phẩm']);
        //Xếp hạng sản phẩm
        Route::get('product/get_product_ranking', 'v1.statistic.ProductStatistic/getProductRanking')->option(['real_name' => 'Xếp hạng sản phẩm']);
        //Xuất dữ liệu sản phẩm
        Route::get('product/get_excel', 'v1.statistic.ProductStatistic/getExcel')->option(['real_name' => 'Xuất dữ liệu sản phẩm']);
    })->option(['parent' => 'statistic', 'cate_name' => 'Thống kê sản phẩm']);

    /** Thống kê giao dịch */
    Route::group(function () {
        //Thống kê doanh thu hôm nay
        Route::get('trade/top_trade', 'v1.statistic.TradeStatistic/topTrade')->option(['real_name' => 'Thống kê doanh thu hôm nay']);
        Route::get('trade/bottom_trade', 'v1.statistic.TradeStatistic/bottomTrade')->option(['real_name' => 'Dữ liệu phần dưới thống kê giao dịch']);
    })->option(['parent' => 'statistic', 'cate_name' => 'Thống kê giao dịch']);

    /** Thống kê đơn hàng */
    Route::group(function () {
        //Thông tin cơ bản đơn hàng
        Route::get('order/get_basic', 'v1.statistic.OrderStatistic/getBasic')->option(['real_name' => 'Thống kê cơ bản đơn hàng']);
        //Xu hướng đơn hàng
        Route::get('order/get_trend', 'v1.statistic.OrderStatistic/getTrend')->option(['real_name' => 'Xu hướng đơn hàng']);
        //Nguồn đơn hàng
        Route::get('order/get_channel', 'v1.statistic.OrderStatistic/getChannel')->option(['real_name' => 'Nguồn đơn hàng']);
        //Loại đơn hàng
        Route::get('order/get_type', 'v1.statistic.OrderStatistic/getType')->option(['real_name' => 'Loại đơn hàng']);
    })->option(['parent' => 'statistic', 'cate_name' => 'Thống kê đơn hàng']);

    /** Dòng tiền */
    Route::group(function () {
        Route::get('flow/get_list', 'v1.statistic.FlowStatistic/getFlowList')->option(['real_name' => 'Dòng tiền']);
        Route::post('flow/set_mark/:id', 'v1.statistic.FlowStatistic/setMark')->option(['real_name' => 'Đặt ghi chú']);
        Route::get('flow/get_record', 'v1.statistic.FlowStatistic/getFlowRecord')->option(['real_name' => 'Lịch sử sao kê']);
    })->option(['parent' => 'statistic', 'cate_name' => 'Dòng tiền']);

    /** Thống kê số dư */
    Route::group(function () {
        //Thống kê cơ bản số dư
        Route::get('balance/get_basic', 'v1.statistic.BalanceStatistic/getBasic')->option(['real_name' => 'Thống kê cơ bản số dư']);
        //Xu hướng số dư
        Route::get('balance/get_trend', 'v1.statistic.BalanceStatistic/getTrend')->option(['real_name' => 'Xu hướng số dư']);
        //Nguồn số dư
        Route::get('balance/get_channel', 'v1.statistic.BalanceStatistic/getChannel')->option(['real_name' => 'Nguồn số dư']);
        //Tiêu dùng số dư
        Route::get('balance/get_type', 'v1.statistic.BalanceStatistic/getType')->option(['real_name' => 'Tiêu dùng số dư']);
    })->option(['parent' => 'statistic', 'cate_name' => 'Thống kê số dư']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'statistic', 'mark_name' => 'Thống kê chương trình']);
