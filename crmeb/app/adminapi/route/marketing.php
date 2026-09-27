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
 * Route phiếu giảm giá, săn giảm giá, mua chung, flash sale
 */
Route::group('marketing', function () {

    /** Phiếu giảm giá */
    Route::group(function () {
        //Danh sách phiếu giảm giá đã phát hành
        Route::get('coupon/released', 'v1.marketing.StoreCouponIssue/index')->option(['real_name' => 'Danh sách phiếu giảm giá đã phát hành']);
        //Thêm phiếu giảm giá
        Route::post('coupon/save_coupon', 'v1.marketing.StoreCouponIssue/saveCoupon')->option(['real_name' => 'Thêm phiếu giảm giá']);
        //Sửa trạng thái phiếu giảm giá
        Route::get('coupon/status/:id/:status', 'v1.marketing.StoreCouponIssue/status')->option(['real_name' => 'Sửa trạng thái phiếu giảm giá']);
        //Sao chép nhanh phiếu giảm giá
        Route::get('coupon/copy/:id', 'v1.marketing.StoreCouponIssue/copy')->option(['real_name' => 'Sao chép nhanh phiếu giảm giá']);
        //Danh sách phiếu giảm giá để gửi
        Route::get('coupon/grant', 'v1.marketing.StoreCouponIssue/index')->option(['real_name' => 'Danh sách phiếu giảm giá để gửi']);
        //Xóa phiếu giảm giá đã phát hành
        Route::delete('coupon/released/:id', 'v1.marketing.StoreCouponIssue/delete')->option(['real_name' => 'Xóa phiếu giảm giá đã phát hành']);
        //Biểu mẫu sửa trạng thái phiếu giảm giá đã phát hành
        Route::get('coupon/released/:id/status', 'v1.marketing.StoreCouponIssue/edit')->option(['real_name' => 'Biểu mẫu sửa trạng thái phiếu giảm giá đã phát hành']);
        //Sửa trạng thái phiếu giảm giá đã phát hành
        Route::put('coupon/released/status/:id', 'v1.marketing.StoreCouponIssue/status')->option(['real_name' => 'Sửa trạng thái phiếu giảm giá đã phát hành']);
        //Lịch sử nhận phiếu giảm giá đã phát hành
        Route::get('coupon/released/issue_log/:id', 'v1.marketing.StoreCouponIssue/issue_log')->option(['real_name' => 'Lịch sử nhận phiếu giảm giá đã phát hành']);
        //Lịch sử nhận của thành viên
        Route::get('coupon/user', 'v1.marketing.StoreCouponUser/index')->option(['real_name' => 'Lịch sử nhận của thành viên']);
        //Gửi phiếu giảm giá
        Route::post('coupon/user/grant', 'v1.marketing.StoreCouponUser/grant')->option(['real_name' => 'Gửi phiếu giảm giá']);
    })->option(['parent' => 'marketing', 'cate_name' => 'Phiếu giảm giá']);

    /** Hoạt động săn giảm giá */
    Route::group(function () {
        //Danh sách sản phẩm săn giảm giá
        Route::get('bargain', 'v1.marketing.StoreBargain/index')->option(['real_name' => 'Danh sách sản phẩm săn giảm giá']);
        //Chi tiết săn giảm giá
        Route::get('bargain/:id', 'v1.marketing.StoreBargain/read')->option(['real_name' => 'Chi tiết sản phẩm săn giảm giá']);
        //Lưu khi thêm mới hoặc sửa săn giảm giá
        Route::post('bargain/:id', 'v1.marketing.StoreBargain/save')->option(['real_name' => 'Thêm mới hoặc sửa sản phẩm săn giảm giá']);
        //Xóa săn giảm giá
        Route::delete('bargain/:id', 'v1.marketing.StoreBargain/delete')->option(['real_name' => 'Xóa sản phẩm săn giảm giá']);
        //Sửa trạng thái săn giảm giá
        Route::put('bargain/set_status/:id/:status', 'v1.marketing.StoreBargain/set_status')->option(['real_name' => 'Sửa trạng thái sản phẩm săn giảm giá']);
        //Danh sách săn giảm giá
        Route::get('bargain_list', 'v1.marketing.StoreBargain/bargainList')->option(['real_name' => 'Danh sách tham gia săn giảm giá']);
        //Danh sách người giúp săn giảm giá
        Route::get('bargain_list_info/:id', 'v1.marketing.StoreBargain/bargainListInfo')->option(['real_name' => 'Danh sách người giúp săn giảm giá']);
        //Thống kê săn giảm giá
        Route::get('bargain/statistics/head/:id', 'v1.marketing.StoreBargain/bargainStatistics')->option(['real_name' => 'Thống kê săn giảm giá']);
        //Danh sách săn giảm giá
        Route::get('bargain/statistics/list/:id', 'v1.marketing.StoreBargain/bargainStatisticsList')->option(['real_name' => 'Danh sách thống kê săn giảm giá']);
        //Đơn săn giảm giá
        Route::get('bargain/statistics/order/:id', 'v1.marketing.StoreBargain/bargainStatisticsOrder')->option(['real_name' => 'Đơn hàng thống kê săn giảm giá']);
    })->option(['parent' => 'marketing', 'cate_name' => 'Hoạt động săn giảm giá']);

    /** Hoạt động mua chung */
    Route::group(function () {
        //Danh sách sản phẩm mua chung
        Route::get('combination', 'v1.marketing.StoreCombination/index')->option(['real_name' => 'Danh sách sản phẩm mua chung']);
        //Thống kê mua chung
        Route::get('combination/statistics', 'v1.marketing.StoreCombination/statistics')->option(['real_name' => 'Thống kê sản phẩm mua chung']);
        //Chi tiết sản phẩm mua chung
        Route::get('combination/:id', 'v1.marketing.StoreCombination/read')->option(['real_name' => 'Chi tiết sản phẩm mua chung']);
        //Lưu khi thêm mới hoặc sửa
        Route::post('combination/:id', 'v1.marketing.StoreCombination/save')->option(['real_name' => 'Thêm mới hoặc sửa sản phẩm mua chung']);
        //Xóa
        Route::delete('combination/:id', 'v1.marketing.StoreCombination/delete')->option(['real_name' => 'Xóa sản phẩm mua chung']);
        //Sửa trạng thái mua chung
        Route::put('combination/set_status/:id/:status', 'v1.marketing.StoreCombination/set_status')->option(['real_name' => 'Sửa trạng thái sản phẩm mua chung']);
        //Danh sách mua chung
        Route::get('combination/combine/list', 'v1.marketing.StoreCombination/combine_list')->option(['real_name' => 'Danh sách tham gia mua chung']);
        //Danh sách người mua chung
        Route::get('combination/order_pink/:id', 'v1.marketing.StoreCombination/order_pink')->option(['real_name' => 'Danh sách người mua chung']);
        //Thống kê mua chung
        Route::get('combination/statistics/head/:id', 'v1.marketing.StoreCombination/combinationStatistics')->option(['real_name' => 'Thống kê mua chung']);
        //Danh sách mua chung
        Route::get('combination/statistics/list/:id', 'v1.marketing.StoreCombination/combinationStatisticsList')->option(['real_name' => 'Danh sách thống kê mua chung']);
        //Đơn mua chung
        Route::get('combination/statistics/order/:id', 'v1.marketing.StoreCombination/combinationStatisticsOrder')->option(['real_name' => 'Đơn hàng thống kê mua chung']);
        //Thành nhóm ngay
        Route::get('combination/immediately/:id', 'v1.marketing.StoreCombination/immediatelyCombination')->option(['real_name' => 'Thành nhóm ngay']);

    })->option(['parent' => 'marketing', 'cate_name' => 'Hoạt động mua chung']);

    /** Hoạt động flash sale */
    Route::group(function () {
        //Danh sách flash sale
        Route::get('seckill', 'v1.marketing.StoreSeckill/index')->option(['real_name' => 'Danh sách sản phẩm flash sale']);
        //Danh sách khung giờ flash sale
        Route::get('seckill/time_list', 'v1.marketing.StoreSeckill/time_list')->option(['real_name' => 'Danh sách khung giờ flash sale']);
        //Chi tiết flash sale
        Route::get('seckill/:id', 'v1.marketing.StoreSeckill/read')->option(['real_name' => 'Chi tiết sản phẩm flash sale']);
        //Lưu khi thêm mới hoặc sửa flash sale
        Route::post('seckill/:id', 'v1.marketing.StoreSeckill/save')->option(['real_name' => 'Thêm mới hoặc sửa sản phẩm flash sale']);
        //Xóa flash sale
        Route::delete('seckill/:id', 'v1.marketing.StoreSeckill/delete')->option(['real_name' => 'Xóa sản phẩm flash sale']);
        //Sửa trạng thái flash sale
        Route::put('seckill/set_status/:id/:status', 'v1.marketing.StoreSeckill/set_status')->option(['real_name' => 'Sửa trạng thái sản phẩm flash sale']);
        //Thống kê flash sale
        Route::get('seckill/statistics/head/:id', 'v1.marketing.StoreSeckill/seckillStatistics')->option(['real_name' => 'Thống kê flash sale']);
        //Người tham gia hoạt động
        Route::get('seckill/statistics/people/:id', 'v1.marketing.StoreSeckill/seckillPeople')->option(['real_name' => 'Người tham gia flash sale']);
        //Đơn flash sale
        Route::get('seckill/statistics/order/:id', 'v1.marketing.StoreSeckill/seckillOrder')->option(['real_name' => 'Người tham gia flash sale']);

        Route::get('seckill_activity/list', 'v1.marketing.StoreSeckill/seckillActivityList')->option(['real_name' => 'Danh sách hoạt động flash sale']);
        Route::get('seckill_activity/info/:id', 'v1.marketing.StoreSeckill/seckillActivityInfo')->option(['real_name' => 'Chi tiết hoạt động flash sale']);
        Route::post('seckill_activity/save/:id', 'v1.marketing.StoreSeckill/seckillActivitySave')->option(['real_name' => 'Thêm mới hoặc sửa hoạt động flash sale']);
        Route::delete('seckill_activity/del/:id', 'v1.marketing.StoreSeckill/seckillActivityDel')->option(['real_name' => 'Xóa hoạt động flash sale']);
        Route::put('seckill_activity/status/:id/:status', 'v1.marketing.StoreSeckill/seckillActivityStatus')->option(['real_name' => 'Sửa trạng thái hoạt động flash sale']);



    })->option(['parent' => 'marketing', 'cate_name' => 'Hoạt động flash sale']);

    /** Hoạt động điểm thưởng */
    Route::group(function () {
        //Danh sách nhật ký điểm thưởng
        Route::get('integral', 'v1.marketing.UserPoint/index')->option(['real_name' => 'Danh sách nhật ký điểm thưởng']);
        //Dữ liệu phần đầu nhật ký điểm thưởng
        Route::get('integral/statistics', 'v1.marketing.UserPoint/integral_statistics')->option(['real_name' => 'Dữ liệu phần đầu nhật ký điểm thưởng']);
        //Biểu mẫu sửa cấu hình điểm thưởng
        Route::get('integral_config/edit_basics', 'v1.setting.SystemConfig/edit_basics')->option(['real_name' => 'Biểu mẫu sửa cấu hình điểm thưởng']);
        //Lưu dữ liệu cấu hình điểm thưởng
        Route::post('integral_config/save_basics', 'v1.setting.SystemConfig/save_basics')->option(['real_name' => 'Lưu dữ liệu cấu hình điểm thưởng']);
        //Danh sách sản phẩm đổi điểm
        Route::get('integral_product', 'v1.marketing.integral.StoreIntegral/index')->option(['real_name' => 'Danh sách sản phẩm đổi điểm']);
        //Thêm mới hoặc sửa sản phẩm đổi điểm
        Route::post('integral/:id', 'v1.marketing.integral.StoreIntegral/save')->option(['real_name' => 'Thêm mới hoặc sửa sản phẩm đổi điểm']);
        //Chi tiết sản phẩm đổi điểm
        Route::get('integral/:id', 'v1.marketing.integral.StoreIntegral/read')->option(['real_name' => 'Chi tiết sản phẩm đổi điểm']);
        //Xóa sản phẩm đổi điểm
        Route::delete('integral/:id', 'v1.marketing.integral.StoreIntegral/delete')->option(['real_name' => 'Xóa sản phẩm đổi điểm']);
        //Sửa trạng thái sản phẩm đổi điểm
        Route::put('integral/set_show/:id/:is_show', 'v1.marketing.integral.StoreIntegral/set_show')->option(['real_name' => 'Sửa trạng thái sản phẩm đổi điểm']);
        //Danh sách đơn hàng cửa hàng đổi điểm
        Route::get('integral/order/list', 'v1.marketing.integral.StoreIntegralOrder/lst')->option(['real_name' => 'Danh sách đơn hàng cửa hàng đổi điểm']);
        //Dữ liệu đơn hàng cửa hàng đổi điểm
        Route::get('integral/order/chart', 'v1.marketing.integral.StoreIntegralOrder/chart')->option(['real_name' => 'Dữ liệu đơn hàng cửa hàng đổi điểm']);
        //Dữ liệu chi tiết đơn hàng cửa hàng đổi điểm
        Route::get('integral/order/info/:id', 'v1.marketing.integral.StoreIntegralOrder/order_info')->option(['real_name' => 'Dữ liệu chi tiết đơn hàng cửa hàng đổi điểm']);
        //Sửa ghi chú đơn hàng sản phẩm đổi điểm
        Route::put('integral/order/remark/:id', 'v1.marketing.integral.StoreIntegralOrder/remark')->option(['real_name' => 'Sửa ghi chú đơn hàng sản phẩm đổi điểm']);
        //Lấy trạng thái đơn đổi điểm
        Route::get('integral/order/status/:id', 'v1.marketing.integral.StoreIntegralOrder/status')->option(['real_name' => 'Lấy trạng thái đơn đổi điểm']);
        //Xóa đơn đổi điểm
        Route::delete('integral/order/del/:id', 'v1.marketing.integral.StoreIntegralOrder/del')->option(['real_name' => 'Xóa đơn đổi điểm']);
        //Giao đơn đổi điểm
        Route::put('integral/order/delivery/:id', 'v1.marketing.integral.StoreIntegralOrder/update_delivery')->option(['real_name' => 'Giao đơn đổi điểm']);
        //Lấy biểu mẫu thông tin giao hàng đơn đổi điểm
        Route::get('integral/order/distribution/:id', 'v1.marketing.integral.StoreIntegralOrder/distribution')->option(['real_name' => 'Lấy biểu mẫu thông tin giao hàng đơn đổi điểm']);
        //Sửa thông tin giao hàng đơn đổi điểm
        Route::put('integral/order/distribution/:id', 'v1.marketing.integral.StoreIntegralOrder/update_distribution')->option(['real_name' => 'Sửa thông tin giao hàng đơn đổi điểm']);
        //Xác nhận đã nhận hàng đơn đổi điểm
        Route::put('integral/order/take/:id', 'v1.marketing.integral.StoreIntegralOrder/take_delivery')->option(['real_name' => 'Xác nhận đã nhận hàng đơn đổi điểm']);
        //Lấy đơn vị vận chuyển cho đơn đổi điểm
        Route::get('integral/order/express_list', 'v1.marketing.integral.StoreIntegralOrder/express')->option(['real_name' => 'Lấy đơn vị vận chuyển cho đơn đổi điểm']);
        //Mẫu vận đơn điện tử của đơn vị vận chuyển cho đơn đổi điểm
        Route::get('integral/order/express/temp', 'v1.marketing.integral.StoreIntegralOrder/express_temp')->option(['real_name' => 'Mẫu vận đơn điện tử của đơn vị vận chuyển cho đơn đổi điểm']);
        //Lấy thông tin vận chuyển đơn đổi điểm
        Route::get('integral/order/express/:id', 'v1.marketing.integral.StoreIntegralOrder/get_express')->option(['real_name' => 'Lấy thông tin vận chuyển đơn đổi điểm']);
        //In đơn đổi điểm
        Route::get('integral/order/print/:id', 'v1.marketing.integral.StoreIntegralOrder/order_print')->option(['real_name' => 'In đơn đổi điểm']);
        //Lấy nhân viên giao hàng cho danh sách đơn đổi điểm
        Route::get('integral/order/delivery/list', 'v1.order.DeliveryService/get_delivery_list')->option(['real_name' => 'Lấy nhân viên giao hàng cho danh sách đơn đổi điểm']);
        //Lấy thông tin cấu hình vận đơn mặc định cho đơn đổi điểm
        Route::get('integral/order/sheet_info', 'v1.marketing.integral.StoreIntegralOrder/getDeliveryInfo')->option(['real_name' => 'Lấy thông tin cấu hình vận đơn mặc định cho đơn đổi điểm']);
        //Lịch sử điểm thưởng
        Route::get('point_record', 'v1.marketing.integral.StorePointRecord/pointRecord')->option(['real_name' => 'Danh sách lịch sử điểm thưởng']);
        Route::post('point_record/remark/:id', 'v1.marketing.integral.StorePointRecord/pointRecordRemark')->option(['real_name' => 'Ghi chú danh sách lịch sử điểm thưởng']);
        Route::get('point/get_basic', 'v1.marketing.integral.StorePointRecord/getBasic')->option(['real_name' => 'Thông tin cơ bản thống kê điểm thưởng']);
        Route::get('point/get_trend', 'v1.marketing.integral.StorePointRecord/getTrend')->option(['real_name' => 'Biểu đồ xu hướng thống kê điểm thưởng']);
        //Thống kê nguồn điểm thưởng
        Route::get('point/get_channel', 'v1.marketing.integral.StorePointRecord/getChannel')->option(['real_name' => 'Thống kê nguồn điểm thưởng']);
        //Thống kê điểm thưởng đã dùng
        Route::get('point/get_type', 'v1.marketing.integral.StorePointRecord/getType')->option(['real_name' => 'Thống kê điểm thưởng đã dùng']);
    })->option(['parent' => 'marketing', 'cate_name' => 'Hoạt động điểm thưởng']);

    /** Hoạt động quay thưởng */
    Route::group(function () {
        //Danh sách hoạt động quay thưởng
        Route::get('lottery/list', 'v1.marketing.lottery.LuckLottery/index')->option(['real_name' => 'Danh sách hoạt động quay thưởng']);
        //Chi tiết hoạt động quay thưởng
        Route::get('lottery/detail/:id', 'v1.marketing.lottery.LuckLottery/detail')->option(['real_name' => 'Chi tiết hoạt động quay thưởng']);
        //Thêm hoạt động quay thưởng
        Route::post('lottery/add', 'v1.marketing.lottery.LuckLottery/add')->option(['real_name' => 'Thêm hoạt động quay thưởng']);
        //Sửa dữ liệu hoạt động quay thưởng
        Route::put('lottery/edit/:id', 'v1.marketing.lottery.LuckLottery/edit')->option(['real_name' => 'Sửa dữ liệu hoạt động quay thưởng']);
        //Xóa hoạt động quay thưởng
        Route::delete('lottery/del/:id', 'v1.marketing.lottery.LuckLottery/delete')->option(['real_name' => 'Xóa hoạt động quay thưởng']);
        //Đặt hiện/ẩn hoạt động quay thưởng
        Route::put('lottery/set_status/:id/:status', 'v1.marketing.lottery.LuckLottery/setStatus')->option(['real_name' => 'Đặt hiện/ẩn hoạt động quay thưởng']);
        //Danh sách lịch sử quay thưởng
        Route::get('lottery/record/list', 'v1.marketing.lottery.LuckLotteryRecord/index')->option(['real_name' => 'Danh sách lịch sử quay thưởng']);
        //Xử lý giao hàng, ghi chú cho giải trúng thưởng
        Route::post('lottery/record/deliver', 'v1.marketing.lottery.LuckLotteryRecord/deliver')->option(['real_name' => 'Xử lý giao hàng, ghi chú cho giải trúng thưởng']);
        //Danh sách quay thưởng theo loại
        Route::get('lottery/factor/list', 'v1.marketing.lottery.LuckLottery/factorList')->option(['real_name' => 'Danh sách quay thưởng theo loại']);
        //Lưu cấu hình vòng quay may mắn
        Route::post('lottery/factor/use', 'v1.marketing.lottery.LuckLottery/factorUse')->option(['real_name' => 'Lưu trạng thái sử dụng quay thưởng']);

    })->option(['parent' => 'marketing', 'cate_name' => 'Hoạt động quay thưởng']);

    /** Điểm danh hằng ngày */
    Route::group(function () {
        //Danh sách phần thưởng điểm danh
        Route::get('sign/rewards', 'v1.marketing.SignRewards/index')->option(['real_name' => 'Danh sách phần thưởng điểm danh']);
        //Thêm phần thưởng điểm danh
        Route::get('sign/add_rewards', 'v1.marketing.SignRewards/addRewards')->option(['real_name' => 'Thêm phần thưởng điểm danh']);
        //Sửa thưởng điểm danh
        Route::get('sign/edit_rewards/:id', 'v1.marketing.SignRewards/editRewards')->option(['real_name' => 'Lưu phần thưởng điểm danh']);
        //Lưu phần thưởng điểm danh
        Route::post('sign/save_rewards/:id', 'v1.marketing.SignRewards/saveRewards')->option(['real_name' => 'Lưu phần thưởng điểm danh']);
        //Xóa phần thưởng điểm danh
        Route::delete('sign/del_rewards/:id', 'v1.marketing.SignRewards/delRewards')->option(['real_name' => 'Xóa phần thưởng điểm danh']);
    })->option(['parent' => 'marketing', 'cate_name' => 'Điểm danh hằng ngày']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'marketing', 'mark_name' => 'Hoạt động marketing']);
