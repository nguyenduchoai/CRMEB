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
use app\http\middleware\AllowOriginMiddleware;
use app\outapi\middleware\AuthTokenMiddleware;
use think\facade\Config;
use think\facade\Route;
use think\Response;

Route::group(function () {

    Route::group(function () {
        //Lấy token
        Route::post('access_token', 'Login/getToken')->name('getToken')->option(['real_name' => 'Đăng nhập để lấy token']);
        //Làm mới token
        Route::post('refresh_token', 'Login/refreshToken')->name('refreshToken')->option(['real_name' => 'Làm mới token']);
    })->option(['mark' => 'common', 'mark_name' => 'API chung']);

    Route::group(function () {
        Route::group(function () {
            //Danh mục sản phẩm
            Route::get('category/list', 'StoreCategory/index')->option(['real_name' => 'Danh sách danh mục']);
            Route::get('category/:id', 'StoreCategory/read')->option(['real_name' => 'Lấy danh mục']);
            Route::post('category', 'StoreCategory/save')->option(['real_name' => 'Thêm danh mục']);
            Route::put('category/:id', 'StoreCategory/update')->option(['real_name' => 'Sửa danh mục']);
            Route::delete('category/:id', 'StoreCategory/delete')->option(['real_name' => 'Xóa danh mục']);
            Route::put('category/set_show/:id/:is_show', 'StoreCategory/set_show')->option(['real_name' => 'Chỉnh sửa trạng thái danh mục']);
        })->option(['mark' => 'category', 'mark_name' => 'Danh mục sản phẩm']);

        Route::group(function () {
            //Sản phẩm
            Route::get('product/list', 'StoreProduct/index')->option(['real_name' => 'Danh sách sản phẩm']);
            Route::post('product', 'StoreProduct/save')->option(['real_name' => 'Thêm sản phẩm']);
            Route::put('product/:id', 'StoreProduct/update')->option(['real_name' => 'Chỉnh sửa sản phẩm']);
            Route::get('product/:id', 'StoreProduct/read')->option(['real_name' => 'Lấy sản phẩm']);
            Route::put('product/set_show/:id/:is_show', 'StoreProduct/set_show')->option(['real_name' => 'Sửa trạng thái sản phẩm']);
            Route::put('product/stock/upload', 'StoreProduct/uploadStock')->option(['real_name' => 'Đồng bộ tồn kho sản phẩm']);
        })->option(['mark' => 'product', 'mark_name' => 'Sản phẩm']);

        Route::group(function () {
            //Đơn hàng
            Route::get('order/list', 'StoreOrder/lst')->name('StoreOrderList')->option(['real_name' => 'Danh sách đơn hàng']);
            Route::get('order/:order_id', 'StoreOrder/read')->name('StoreOrderInfo')->option(['real_name' => 'Chi tiết đơn hàng']);
            Route::put('order/remark/:order_id', 'StoreOrder/remark')->name('StoreOrderRemark')->option(['real_name' => 'Sửa thông tin ghi chú']);
            Route::put('order/receive/:order_id', 'StoreOrder/receive')->name('StoreOrderReceive')->option(['real_name' => 'Xác nhận đã nhận hàng']);
            Route::get('order/express_list', 'StoreOrder/express')->name('StoreOrderExpress')->option(['real_name' => 'Lấy đơn vị vận chuyển']);
            Route::put('order/delivery/:order_id', 'StoreOrder/delivery')->name('StoreOrderDelivery')->option(['real_name' => 'Giao đơn hàng']);
            Route::put('order/distribution/:order_id', 'StoreOrder/updateDistribution')->name('StoreOrderDistribution')->option(['real_name' => 'Sửa thông tin giao hàng']);
            Route::get('order/split_cart_info/:order_id', 'StoreOrder/splitCartInfo')->name('StoreOrderSplitCartInfo')->option(['real_name' => 'Lấy danh sách sản phẩm có thể tách của đơn hàng']);
            Route::put('order/split_delivery/:order_id', 'StoreOrder/splitDelivery')->name('StoreOrderSplitDelivery')->option(['real_name' => 'Tách đơn để giao hàng']);
            Route::put('order/invoice/:order_id', 'StoreOrder/setInvoice')->option(['real_name' => 'Chỉnh sửa hóa đơn của đơn hàng']);
            Route::put('order/invoice_status/:order_id', 'StoreOrder/setInvoiceStatus')->option(['real_name' => 'Chỉnh sửa trạng thái hóa đơn của đơn hàng']);
        })->option(['mark' => 'order', 'mark_name' => 'Đơn hàng']);

        Route::group(function () {
            //Đơn đổi trả
            Route::get('refund/list', 'RefundOrder/lst')->option(['real_name' => 'Danh sách đơn hậu mãi']);
            Route::put('refund/remark/:order_id', 'RefundOrder/remark')->option(['real_name' => 'Ghi chú đơn đổi trả']);
            Route::put('refund/:order_id', 'RefundOrder/refundPrice')->option(['real_name' => 'Hoàn tiền đơn đổi trả']);
            Route::put('refund/agree/:order_id', 'RefundOrder/agree')->option(['real_name' => 'Người bán đồng ý hoàn tiền']);
            Route::put('refund/refuse/:order_id', 'RefundOrder/refuse')->option(['real_name' => 'Cửa hàng từ chối hoàn tiền']);
            Route::get('refund/:order_id', 'RefundOrder/read')->option(['real_name' => 'Chi tiết đơn hậu mãi']);
        })->option(['mark' => 'refund', 'mark_name' => 'Đổi trả']);

        Route::group(function () {
            //Phiếu giảm giá
            Route::get('coupon/list', 'StoreCoupon/lst')->option(['real_name' => 'Danh sách phiếu giảm giá']);
            Route::post('coupon', 'StoreCoupon/save')->option(['real_name' => 'Thêm phiếu giảm giá']);
            Route::put('coupon/status/:id/:status', 'StoreCoupon/status')->option(['real_name' => 'Sửa trạng thái phiếu giảm giá']);
            Route::delete('coupon/:id', 'StoreCoupon/delete')->option(['real_name' => 'Xóa phiếu giảm giá']);
        })->option(['mark' => 'coupon', 'mark_name' => 'Phiếu giảm giá']);

        Route::group(function () {
            //Hạng người dùng
            Route::get('user_level/list', 'UserLevel/lst')->option(['real_name' => 'Danh sách hạng người dùng']);

            //Người dùng
            Route::get('user/list', 'User/lst')->option(['real_name' => 'Danh sách người dùng']);
            Route::get('user/info/:uid', 'User/info')->option(['real_name' => 'Chi tiết người dùng']);
            Route::post('user', 'User/save')->option(['real_name' => 'Thêm người dùng']);
            Route::put('user/:uid', 'User/update')->option(['real_name' => 'Sửa người dùng']);
            Route::put('user/give_balance/:uid', 'User/giveBalance')->option(['real_name' => 'Tặng số dư']);
            Route::put('user/give_point/:uid', 'User/givePoint')->option(['real_name' => 'Tặng điểm thưởng']);
            Route::put('user/change_balance/:uid', 'User/changeBalance')->option(['real_name' => 'Chỉnh sửa số dư']);
            Route::put('user/change_point/:uid', 'User/changePoint')->option(['real_name' => 'Chỉnh sửa điểm thưởng']);
        })->option(['mark' => 'user', 'mark_name' => 'Người dùng']);

    })->middleware(AuthTokenMiddleware::class);

})->middleware(AllowOriginMiddleware::class);

Route::miss(function () {
    if (app()->request->isOptions()) {
        $header = Config::get('cookie.header');
        $header['Access-Control-Allow-Origin'] = app()->request->header('origin');
        return Response::create('ok')->code(200)->header($header);
    } else
        return Response::create()->code(404);
});
