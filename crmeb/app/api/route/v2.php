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
use app\api\middleware\BlockerMiddleware;
use think\facade\Route;

/**
 * Route phiên bản v1.1
 */
Route::group('v2', function () {
    //API không cần ủy quyền
    Route::group(function () {
        Route::group(function () {
            //Tự động nạp khi vào trang đăng nhập Mini Program, trả về key cache thông tin người dùng, trả về có bắt buộc liên kết số điện thoại không
            Route::get('routine/auth_type', 'v2.wechat.AuthController/authType')->option(['real_name' => 'Loại đăng nhập trang Mini Program']);
            //Đăng nhập ủy quyền Mini Program, trả về token
            Route::get('routine/auth_login', 'v2.wechat.AuthController/authLogin')->option(['real_name' => 'Đăng nhập ủy quyền Mini Program']);
            //Liên kết số điện thoại qua ủy quyền Mini Program
            Route::post('routine/auth_binding_phone', 'v2.wechat.AuthController/authBindingPhone')->option(['real_name' => 'Liên kết số điện thoại qua ủy quyền Mini Program']);
            //Đăng nhập trực tiếp bằng số điện thoại trên Mini Program
            Route::post('routine/phone_login', 'v2.wechat.AuthController/phoneLogin')->option(['real_name' => 'Đăng nhập trực tiếp bằng số điện thoại']);
            //Liên kết số điện thoại sau khi ủy quyền Mini Program
            Route::post('routine/binding_phone', 'v2.wechat.AuthController/BindingPhone')->option(['real_name' => 'Liên kết số điện thoại sau khi ủy quyền Mini Program']);

            //Đăng nhập ủy quyền OA WeChat, trả về token
            Route::get('wechat/auth_login', 'v2.wechat.WechatController/authLogin')->option(['real_name' => 'Đăng nhập ủy quyền OA WeChat']);
            //Ủy quyền liên kết số điện thoại qua OA WeChat
            Route::post('wechat/auth_binding_phone', 'v2.wechat.WechatController/authBindingPhone')->option(['real_name' => 'Liên kết số điện thoại qua ủy quyền Mini Program']);

        })->option(['mark' => 'wechat_auto', 'mark_name' => 'Ủy quyền WeChat']);

        Route::group(function () {
            Route::get('diy/get_store_status', 'v2.PublicController/getStoreStatus')->option(['real_name' => 'Lấy trạng thái bật nhận tại cửa hàng']);
            Route::get('diy/color_change/:name', 'v2.PublicController/colorChange')->option(['real_name' => 'Đổi màu nhanh']);
            Route::get('diy/get_diy/[:name]', 'v2.PublicController/getDiy')->option(['real_name' => 'Lấy dữ liệu DIY']);
            Route::get('diy/get_version/[:name]', 'v2.PublicController/getVersion')->option(['real_name' => 'Lấy số phiên bản DIY']);
        })->option(['mark' => 'diy', 'mark_name' => 'DIY']);
    });
    //Cần ủy quyền
    Route::group(function () {

        Route::post('reset_cart', 'v2.store.StoreCartController/resetCart')->name('resetCart')->option(['real_name' => 'Xóa giỏ hàng', 'mark' => 'cart', 'mark_name' => 'Giỏ hàng']);
        Route::get('new_coupon', 'v2.store.StoreCouponsController/getNewCoupon')->name('getNewCoupon')->option(['real_name' => 'Lấy phiếu giảm giá người mới', 'mark' => 'coupons', 'mark_name' => 'Phiếu giảm giá']);//Lấy phiếu giảm giá người mới
        Route::post('order/product_coupon/:orderId', 'v2.store.StoreCouponsController/getOrderProductCoupon')->option(['real_name' => 'Lấy phiếu giảm giá liên quan đến đơn hàng', 'mark' => 'coupons', 'mark_name' => 'Phiếu giảm giá']);
        Route::get('user/service/record', 'v2.user.StoreService/record')->name('userServiceRecord')->option(['real_name' => 'Lịch sử trò chuyện CSKH', 'parent' => 'user', 'cate_name' => 'CSKH']);//Lịch sử trò chuyện CSKH
        Route::get('cart_list', 'v2.store.StoreCartController/getCartList')->option(['real_name' => 'Lấy danh sách giỏ hàng', 'mark' => 'cart', 'mark_name' => 'Giỏ hàng']);
        Route::get('get_attr/:id/:type', 'v2.store.StoreProductController/getProductAttr')->option(['real_name' => 'Lấy quy cách sản phẩm', 'mark' => 'cart', 'mark_name' => 'Giỏ hàng']);
        Route::post('set_cart_num', 'v2.store.StoreCartController/setCartNum')->option(['real_name' => 'Lấy số lượng giỏ hàng', 'mark' => 'cart', 'mark_name' => 'Giỏ hàng']);

        Route::group(function () {
            //Yêu cầu xuất hóa đơn cho đơn hàng
            Route::post('order/make_up_invoice', 'v2.order.StoreOrderInvoiceController/makeUp')->name('orderMakeUpInvoice')->option(['real_name' => 'Yêu cầu xuất hóa đơn cho đơn hàng']);
            //Danh sách hóa đơn của người dùng
            Route::get('invoice', 'v2.user.UserInvoiceController/invoiceList')->name('userInvoiceLIst')->option(['real_name' => 'Danh sách hóa đơn của người dùng']);
            //Chi tiết một hóa đơn
            Route::get('invoice/detail/:id', 'v2.user.UserInvoiceController/invoice')->name('userInvoiceDetail')->option(['real_name' => 'Chi tiết một hóa đơn']);
            //Sửa|thêm hóa đơn
            Route::post('invoice/save', 'v2.user.UserInvoiceController/saveInvoice')->name('userInvoiceSave')->option(['real_name' => 'Sửa|thêm hóa đơn']);
            //Đặt hóa đơn mặc định
            Route::post('invoice/set_default/:id', 'v2.user.UserInvoiceController/setDefaultInvoice')->name('userInvoiceSetDefault')->option(['real_name' => 'Đặt hóa đơn mặc định']);
            //Lấy hóa đơn mặc định
            Route::get('invoice/get_default/:type', 'v2.user.UserInvoiceController/getDefaultInvoice')->name('userInvoiceGetDefault')->option(['real_name' => 'Lấy hóa đơn mặc định']);
            //Xóa hóa đơn
            Route::get('invoice/del/:id', 'v2.user.UserInvoiceController/delInvoice')->name('userInvoiceDel')->option(['real_name' => 'Xóa hóa đơn']);
            //Lịch sử yêu cầu xuất hóa đơn của đơn hàng
            Route::get('order/invoice_list', 'v2.order.StoreOrderInvoiceController/list')->name('orderInvoiceList')->option(['real_name' => 'Lịch sử yêu cầu xuất hóa đơn của đơn hàng']);
            //Chi tiết xuất hóa đơn của đơn hàng
            Route::get('order/invoice_detail/:uni', 'v2.order.StoreOrderInvoiceController/detail')->name('orderInvoiceList')->option(['real_name' => 'Chi tiết xuất hóa đơn của đơn hàng']);
            //Tải xuống hóa đơn điện tử
            Route::get('order/down_invoice/:id', 'v2.order.StoreOrderInvoiceController/downInvoice')->name('downInvoice')->option(['real_name' => 'Tải xuống hóa đơn điện tử']);
        })->option(['mark' => 'invoice', 'mark_name' => 'Hóa đơn']);

        //Xóa lịch sử tìm kiếm
        Route::get('user/clean_search', 'v2.user.UserSearchController/cleanUserSearch')->name('cleanUserSearch')->option(['real_name' => 'Xóa lịch sử tìm kiếm']);

        //Chi tiết hoạt động quay thưởng
        Route::get('lottery/info/:factor/[:lottery_id]', 'v2.activity.LuckLotteryController/lotteryInfo')->name('lotteryInfo')->option(['real_name' => 'Chi tiết hoạt động quay thưởng']);
        //Tham gia quay thưởng
        Route::post('lottery', 'v2.activity.LuckLotteryController/luckLottery')->name('luckLottery')->middleware(BlockerMiddleware::class)->option(['real_name' => 'Tham gia quay thưởng']);
        //Nhận phần thưởng
        Route::post('lottery/receive', 'v2.activity.LuckLotteryController/lotteryReceive')->name('lotteryReceive')->middleware(BlockerMiddleware::class)->option(['real_name' => 'Nhận phần thưởng']);
        //Lịch sử quay thưởng
        Route::get('lottery/record', 'v2.activity.LuckLotteryController/lotteryRecord')->name('lotteryRecord')->option(['real_name' => 'Lịch sử quay thưởng']);

        //Lấy danh sách cấp độ CTV
        Route::get('agent/level_list', 'v2.agent.AgentLevel/levelList')->name('agentLevelList')->option(['real_name' => 'Lấy danh sách cấp độ CTV']);
        //Lấy danh sách nhiệm vụ cấp độ CTV
        Route::get('agent/level_task_list', 'v2.agent.AgentLevel/levelTaskList')->name('agentLevelTaskList')->option(['real_name' => 'Lấy danh sách nhiệm vụ cấp độ CTV']);

    })->middleware(\app\api\middleware\AuthTokenMiddleware::class, true);

    //Ủy quyền không qua thì không throw exception, vẫn tiếp tục thực thi
    Route::group(function () {
        Route::get('user/search_list', 'v2.user.UserSearchController/getUserSeachList')->name('userSearchList')->option(['real_name' => 'Lịch sử tìm kiếm của người dùng']);
        Route::get('get_today_coupon', 'v2.store.StoreCouponsController/getTodayCoupon')->option(['real_name' => 'API cửa sổ bật lên phiếu giảm giá mới']);//API cửa sổ bật lên phiếu giảm giá mới
        Route::get('subscribe', 'v2.PublicController/subscribe')->name('WechatSubscribe')->option(['real_name' => 'Người dùng đã theo dõi OA WeChat chưa']);// Người dùng đã theo dõi OA WeChat chưa
        Route::get('index', 'v2.PublicController/index')->name('index')->option(['real_name' => 'Trang chủ']);//Trang chủ
        Route::get('coupons', 'v2.store.StoreCouponsController/lst')->name('couponsList')->option(['real_name' => 'Danh sách phiếu giảm giá có thể nhận']); //Danh sách phiếu giảm giá có thể nhận
        Route::get('diy/sign', 'v2.PublicController/getDiySign')->name('getDiySign')->option(['real_name' => 'Lấy điểm danh Diy']);
    })->middleware(\app\api\middleware\AuthTokenMiddleware::class, false)
        ->option(['mark' => 'common', 'mark_name' => 'API chung']);

})->middleware(\app\http\middleware\AllowOriginMiddleware::class)->middleware(\app\api\middleware\StationOpenMiddleware::class);
