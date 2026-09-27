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
use think\facade\Config;
use think\Response;

Route::group(function () {
    Route::any('wechat/serve', 'v1.wechat.WechatController/serve')->option(['real_name' => 'Dịch vụ OA WeChat']);//Dịch vụ OA WeChat
    Route::any('wechat/miniServe', 'v1.wechat.WechatController/miniServe')->option(['real_name' => 'Dịch vụ Mini Program']);//Dịch vụ OA WeChat
    Route::any('pay/notify/:type', 'v1.PayController/notify')->option(['real_name' => 'Callback thanh toán']);//Callback thanh toán
    Route::any('transfer/notify/:type', 'v1.PayController/transferNotify')->option(['real_name' => 'Callback chuyển khoản merchant']);//Callback chuyển khoản merchant
    Route::any('order_call_back', 'v1.order.StoreOrderController/callBack')->option(['real_name' => 'Callback gửi hàng của cửa hàng']);//Callback gửi hàng của cửa hàng
    Route::get('get_script', 'v1.PublicController/getScript')->option(['real_name' => 'JS tùy chỉnh cho thiết bị di động']);//JS tùy chỉnh cho thiết bị di động
    Route::get('custom_pc_js', 'v1.PublicController/customPcJs')->option(['real_name' => 'JS tùy chỉnh cho PC']);//JS tùy chỉnh cho PC
    Route::get('version', 'v1.PublicController/getVersion')->option(['real_name' => 'Lấy số phiên bản mã nguồn']);
    Route::get('service_pay_result', 'v1.PublicController/servicePayResult')->option(['real_name' => 'API biên lai cửa hàng khi thanh toán qua nhà cung cấp dịch vụ']);
})->middleware(\app\http\middleware\AllowOriginMiddleware::class)->option(['mark' => 'serve', 'mark_name' => 'API dịch vụ']);

Route::group(function () {
    //Đăng nhập nhanh bằng Apple
    Route::post('apple_login', 'v1.LoginController/appleLogin')->name('appleLogin')->option(['real_name' => 'Ủy quyền WeChat APP']);//Ủy quyền WeChat APP
    //Đăng nhập bằng tài khoản và mật khẩu
    Route::post('login', 'v1.LoginController/login')->name('login')->option(['real_name' => 'Đăng nhập bằng tài khoản và mật khẩu']);
    // Lấy key gửi SMS
    Route::get('verify_code', 'v1.LoginController/verifyCode')->name('verifyCode')->option(['real_name' => 'Lấy key gửi SMS']);
    //Đăng nhập bằng số điện thoại
    Route::post('login/mobile', 'v1.LoginController/mobile')->name('loginMobile')->option(['real_name' => 'Đăng nhập bằng số điện thoại']);
    //Mã xác thực hình ảnh
    Route::get('sms_captcha', 'v1.LoginController/captcha')->name('captcha')->option(['real_name' => 'Mã xác thực hình ảnh']);
    //Mã xác thực đồ họa
    Route::get('ajcaptcha', 'v1.LoginController/ajcaptcha')->name('ajcaptcha')->option(['real_name' => 'Mã xác thực đồ họa']);
    //Xác minh mã xác thực đồ họa
    Route::post('ajcheck', 'v1.LoginController/ajcheck')->name('ajcheck')->option(['real_name' => 'Xác minh mã xác thực đồ họa']);
    //Gửi mã xác thực qua điện thoại
    Route::post('register/verify', 'v1.LoginController/verify')->name('registerVerify')->option(['real_name' => 'Gửi mã xác thực qua điện thoại']);
    //Đăng ký bằng số điện thoại
    Route::post('register', 'v1.LoginController/register')->name('register')->option(['real_name' => 'Đăng ký bằng số điện thoại']);
    //Đổi mật khẩu bằng số điện thoại
    Route::post('register/reset', 'v1.LoginController/reset')->name('registerReset')->option(['real_name' => 'Đổi mật khẩu bằng số điện thoại']);
    // Liên kết số điện thoại (ủy quyền ngầm, chưa có thông tin người dùng)
    Route::post('binding', 'v1.LoginController/binding_phone')->name('bindingPhone')->option(['real_name' => 'Liên kết số điện thoại']);
    // Thanh toán Alipay bằng sao chép liên kết, đã ngừng dùng
//    Route::get('ali_pay', 'v1.order.StoreOrderController/aliPay')->name('aliPay');
    //Tra cứu bản quyền
    Route::get('copyright', 'v1.PublicController/copyright')->option(['real_name' => 'Đăng ký bản quyền'])->option(['real_name' => 'Tra cứu bản quyền']);
    //API tổng hợp cấu hình cơ bản của cửa hàng
    Route::get('basic_config', 'v1.PublicController/getMallBasicConfig')->option(['real_name' => 'API tổng hợp cấu hình cơ bản của cửa hàng']);
    //API url chuyển hướng Mini Program
    Route::get('get_scheme_url/:id', 'v1.PublicController/getSchemeUrl')->option(['real_name' => 'API url chuyển hướng Mini Program']);
    //Đăng ký người dùng từ xa
    Route::get('remote_register', 'v1.LoginController/remoteRegister')->option(['real_name' => 'Đăng ký người dùng từ xa']);

})->middleware(\app\http\middleware\AllowOriginMiddleware::class)
    ->middleware(\app\api\middleware\StationOpenMiddleware::class)
    ->option(['mark' => 'base', 'mark_name' => 'API cơ bản']);


//Lớp thao tác đơn hàng của quản trị viên
Route::group(function () {
    Route::get('admin/order/statistics', 'v1.admin.StoreOrderController/statistics')->name('adminOrderStatistics')->option(['real_name' => 'Thống kê dữ liệu đơn hàng']);//Thống kê dữ liệu đơn hàng
    Route::get('admin/order/data', 'v1.admin.StoreOrderController/data')->name('adminOrderData')->option(['real_name' => 'Dữ liệu thống kê đơn hàng hằng tháng']);//Dữ liệu thống kê đơn hàng hằng tháng
    Route::get('admin/order/list', 'v1.admin.StoreOrderController/lst')->name('adminOrderList')->option(['real_name' => 'Danh sách đơn hàng']);//Danh sách đơn hàng
    Route::get('admin/refund_order/list', 'v1.admin.StoreOrderController/refundOrderList')->name('adminOrderRefundList')->option(['real_name' => 'Danh sách đơn hoàn tiền']);//Danh sách đơn hoàn tiền
    Route::get('admin/order/detail/:orderId', 'v1.admin.StoreOrderController/detail')->name('adminOrderDetail')->option(['real_name' => 'Chi tiết đơn hàng']);//Chi tiết đơn hàng
    Route::get('admin/refund_order/detail/:uni', 'v1.admin.StoreOrderController/refundOrderDetail')->name('RefundOrderDetail')->option(['real_name' => 'Chi tiết đơn hoàn tiền']);//Chi tiết đơn hoàn tiền
    Route::get('admin/order/delivery/gain/:orderId', 'v1.admin.StoreOrderController/delivery_gain')->name('adminOrderDeliveryGain')->option(['real_name' => 'Lấy thông tin đơn hàng để giao hàng']);//Lấy thông tin đơn hàng để giao hàng
    Route::post('admin/order/delivery/keep/:id', 'v1.admin.StoreOrderController/delivery_keep')->name('adminOrderDeliveryKeep')->option(['real_name' => 'Giao đơn hàng']);//Giao đơn hàng
    Route::post('admin/order/price', 'v1.admin.StoreOrderController/price')->name('adminOrderPrice')->option(['real_name' => 'Sửa giá đơn hàng']);//Sửa giá đơn hàng
    Route::post('admin/order/remark', 'v1.admin.StoreOrderController/remark')->name('adminOrderRemark')->option(['real_name' => 'Ghi chú đơn hàng']);//Ghi chú đơn hàng
    Route::post('admin/order/agreeExpress', 'v1.admin.StoreOrderController/agreeExpress')->name('adminOrderAgreeExpress')->option(['real_name' => 'Đồng ý trả hàng cho đơn hàng']);//Đồng ý trả hàng cho đơn hàng
    Route::post('admin/refund_order/remark', 'v1.admin.StoreOrderController/refundRemark')->name('refundRemark')->option(['real_name' => 'Ghi chú đơn hoàn tiền']);//Ghi chú đơn hoàn tiền
    Route::get('admin/order/time', 'v1.admin.StoreOrderController/time')->name('adminOrderTime')->option(['real_name' => 'Thống kê giá trị giao dịch đơn hàng theo thời gian']);//Thống kê giá trị giao dịch đơn hàng theo thời gian
    Route::post('admin/order/offline', 'v1.admin.StoreOrderController/offline')->name('adminOrderOffline')->option(['real_name' => 'Thanh toán đơn hàng']);//Thanh toán đơn hàng
    Route::post('admin/order/refund', 'v1.admin.StoreOrderController/refund')->name('adminOrderRefund')->option(['real_name' => 'Hoàn tiền đơn hàng']);//Hoàn tiền đơn hàng
    Route::post('order/order_verific', 'v1.admin.StoreOrderController/order_verific')->name('order')->option(['real_name' => 'Xác nhận sử dụng đơn hàng']);//Xác nhận sử dụng đơn hàng
    Route::get('admin/order/delivery', 'v1.admin.StoreOrderController/getDeliveryAll')->name('getDeliveryAll')->option(['real_name' => 'Lấy nhân viên giao hàng']);//Lấy nhân viên giao hàng
    Route::get('admin/order/delivery_info', 'v1.admin.StoreOrderController/getDeliveryInfo')->name('getDeliveryInfo')->option(['real_name' => 'Lấy thông tin mặc định của vận đơn điện tử']);//Lấy thông tin mặc định của vận đơn điện tử
    Route::get('admin/order/export_temp', 'v1.admin.StoreOrderController/getExportTemp')->name('getExportTemp')->option(['real_name' => 'Lấy mẫu vận đơn điện tử']);//Lấy mẫu vận đơn điện tử
    Route::get('admin/order/export_all', 'v1.admin.StoreOrderController/getExportAll')->name('getExportAll')->option(['real_name' => 'Lấy đơn vị vận chuyển']);//Lấy đơn vị vận chuyển
    Route::get('admin/order/express/:uni/[:type]', 'v1.admin.StoreOrderController/express')->name('orderExpress')->option(['real_name' => 'Xem vận chuyển của đơn hàng']); //Xem vận chuyển của đơn hàng
})->middleware(\app\http\middleware\AllowOriginMiddleware::class)
    ->middleware(\app\api\middleware\StationOpenMiddleware::class)
    ->middleware(\app\api\middleware\AuthTokenMiddleware::class, true)
    ->middleware(\app\api\middleware\CustomerMiddleware::class)
    ->option(['mark' => 'admin', 'mark_name' => 'Quản lý đơn hàng trên di động']);;

//API ủy quyền thành viên
Route::group(function () {
    Route::group(function () {
        //Lấy phương thức thanh toán
        Route::get('pay/config', 'v1.PayController/config')->name('payConfig')->option(['real_name' => 'Lấy phương thức thanh toán']);
        //Người dùng đổi số điện thoại
        Route::post('user/updatePhone', 'v1.LoginController/update_binding_phone')->name('updateBindingPhone')->option(['real_name' => 'Người dùng đổi số điện thoại']);
        //Đặt code đăng nhập
        Route::post('user/code', 'v1.user.StoreService/setLoginCode')->name('setLoginCode')->option(['real_name' => 'Đặt code đăng nhập']);
        //Kiểm tra code có khả dụng không
        Route::get('user/code', 'v1.LoginController/setLoginKey')->name('getLoginKey')->option(['real_name' => 'Kiểm tra code có khả dụng không']);
        //Người dùng liên kết số điện thoại
        Route::post('user/binding', 'v1.LoginController/user_binding_phone')->name('userBindingPhone')->option(['real_name' => 'Người dùng liên kết số điện thoại']);
        Route::get('logout', 'v1.LoginController/logout')->name('logout')->option(['real_name' => 'Đăng xuất']);// Đăng xuất
        Route::post('switch_h5', 'v1.LoginController/switch_h5')->name('switch_h5')->option(['real_name' => 'Chuyển tài khoản']);// Chuyển tài khoản
        //Lớp dùng chung
        Route::post('upload/image', 'v1.PublicController/upload_image')->name('uploadImage')->option(['real_name' => 'Tải lên ảnh']);//Tải lên ảnh
        // API chi tiết chuyển khoản WeChat của người dùng
        Route::get('transfer/info', 'v1.PublicController/getTransferInfo')->name('getTransferInfo')->option(['real_name' => 'API chi tiết chuyển khoản WeChat của người dùng']);// API chi tiết chuyển khoản WeChat của người dùng

    })->option(['mark' => 'common', 'mark_name' => 'API chung']);

    Route::group(function () {
        //Lớp người dùng - Lịch sử chat CSKH
        Route::get('user/service/list', 'v1.user.StoreService/lst')->name('userServiceList')->option(['real_name' => 'Danh sách nhân viên CSKH']);//Danh sách nhân viên CSKH
        Route::get('user/service/record', 'v1.user.StoreService/record')->name('userServiceRecord')->option(['real_name' => 'Lịch sử trò chuyện CSKH']);//Lịch sử trò chuyện CSKH
        Route::post('user/service/feedback', 'v1.user.StoreService/saveFeedback')->name('saveFeedback')->option(['real_name' => 'Lưu thông tin phản hồi CSKH']);//Lưu thông tin phản hồi CSKH
        Route::get('user/service/feedback', 'v1.user.StoreService/getFeedbackInfo')->name('getFeedbackInfo')->option(['real_name' => 'Lấy thông tin tiêu đề phản hồi CSKH']);//Lấy thông tin tiêu đề phản hồi CSKH
        Route::get('user/service/get_adv', 'v1.user.StoreService/getKfAdv')->name('userServiceGetKfAdv')->option(['real_name' => 'Lấy quảng cáo trang CSKH']);//Lấy quảng cáo trang CSKH
    })->option(['parent' => 'user', 'cate_name' => 'CSKH']);

    Route::group(function () {
        //Lớp người dùng - coupons/order của người dùng
        Route::get('user', 'v1.user.UserController/user')->name('user')->option(['real_name' => 'Trang cá nhân']);//Trang cá nhân
        Route::post('user/spread', 'v1.user.UserController/spread')->name('userSpread')->option(['real_name' => 'Liên kết ủy quyền ngầm']);//Liên kết ủy quyền ngầm
        Route::post('user/edit', 'v1.user.UserController/edit')->name('userEdit')->option(['real_name' => 'Người dùng sửa thông tin']);//Người dùng sửa thông tin
        Route::get('user/balance', 'v1.user.UserController/balance')->name('userBalance')->option(['real_name' => 'Thống kê tài chính người dùng']);//Thống kê tài chính người dùng
        Route::get('userinfo', 'v1.user.UserController/userinfo')->name('userinfo')->option(['real_name' => 'Thông tin người dùng']);// Thông tin người dùng
    })->option(['parent' => 'user', 'cate_name' => 'Trung tâm người dùng']);

    Route::group(function () {
        //Lớp người dùng - Địa chỉ
        Route::get('address/detail/:id', 'v1.user.UserAddressController/address')->name('address')->option(['real_name' => 'Lấy một địa chỉ']);//Lấy một địa chỉ
        Route::get('address/list', 'v1.user.UserAddressController/address_list')->name('addressList')->option(['real_name' => 'Danh sách địa chỉ']);//Danh sách địa chỉ
        Route::post('address/default/set', 'v1.user.UserAddressController/address_default_set')->name('addressDefaultSet')->option(['real_name' => 'Đặt địa chỉ mặc định']);//Đặt địa chỉ mặc định
        Route::get('address/default', 'v1.user.UserAddressController/address_default')->name('addressDefault')->option(['real_name' => 'Lấy địa chỉ mặc định']);//Lấy địa chỉ mặc định
        Route::post('address/edit', 'v1.user.UserAddressController/address_edit')->name('addressEdit')->option(['real_name' => 'Sửa/thêm địa chỉ']);//Sửa - Thêm địa chỉ
        Route::post('address/del', 'v1.user.UserAddressController/address_del')->name('addressDel')->option(['real_name' => 'Xóa địa chỉ']);//Xóa địa chỉ
    })->option(['parent' => 'user', 'cate_name' => 'Địa chỉ người dùng']);

    Route::group(function () { //Lớp người dùng - Yêu thích
        Route::get('collect/user', 'v1.user.UserCollectController/collect_user')->name('collectUser')->option(['real_name' => 'Danh sách sản phẩm yêu thích']);//Danh sách sản phẩm yêu thích
        Route::post('collect/add', 'v1.user.UserCollectController/collect_add')->name('collectAdd')->option(['real_name' => 'Thêm yêu thích']);//Thêm yêu thích
        Route::post('collect/del', 'v1.user.UserCollectController/collect_del')->name('collectDel')->option(['real_name' => 'Bỏ yêu thích']);//Bỏ yêu thích
        Route::post('collect/all', 'v1.user.UserCollectController/collect_all')->name('collectAll')->option(['real_name' => 'Thêm yêu thích hàng loạt']);//Thêm yêu thích hàng loạt
    })->option(['parent' => 'user', 'cate_name' => 'Yêu thích của người dùng']);

    Route::group(function () {
        Route::get('rank', 'v1.user.UserController/rank')->name('rank')->option(['real_name' => 'Đăng nhập ủy quyền OA WeChat']);//Xếp hạng người giới thiệu
        //Lớp người dùng - Chia sẻ
        Route::post('user/share', 'v1.PublicController/user_share')->name('user_share')->option(['real_name' => 'Ghi nhận chia sẻ của người dùng']);//Ghi nhận chia sẻ của người dùng
        Route::get('user/share/words', 'v1.PublicController/copy_share_words')->name('user_share_words')->option(['real_name' => 'Chia sẻ từ khóa']);//Chia sẻ từ khóa
    })->option(['parent' => 'user', 'cate_name' => 'Chia sẻ của người dùng']);

    Route::group(function () {
        //Lớp người dùng - Điểm danh
        Route::get('sign/config', 'v1.user.UserSignController/sign_config')->name('signConfig')->option(['real_name' => 'Cấu hình điểm danh']);//Cấu hình điểm danh
        Route::get('sign/list', 'v1.user.UserSignController/sign_list')->name('signList')->option(['real_name' => 'Danh sách điểm danh']);//Danh sách điểm danh
        Route::get('sign/month', 'v1.user.UserSignController/sign_month')->name('signIntegral')->option(['real_name' => 'Danh sách điểm danh (theo năm tháng)']);//Danh sách điểm danh (theo năm tháng)
        Route::get('sign/remind/:status', 'v1.user.UserSignController/sign_remind')->name('signRemind')->option(['real_name' => 'Bật/tắt nhắc điểm danh']);//Danh sách điểm danh (theo năm tháng)
        Route::post('sign/user', 'v1.user.UserSignController/sign_user')->name('signUser')->option(['real_name' => 'Thông tin người dùng điểm danh']);//Thông tin người dùng điểm danh
        Route::post('sign/integral', 'v1.user.UserSignController/sign_integral')->name('signIntegral')->option(['real_name' => 'Đăng nhập ủy quyền OA WeChat'])->middleware(BlockerMiddleware::class);//Điểm danh
    })->option(['mark' => 'sign', 'mark_name' => 'Điểm danh']);

    Route::group(function () {
        //Lớp phiếu giảm giá
        Route::post('coupon/receive', 'v1.store.StoreCouponsController/receive')->name('couponReceive')->option(['real_name' => 'Nhận phiếu giảm giá']); //Nhận phiếu giảm giá
        Route::post('coupon/receive/batch', 'v1.store.StoreCouponsController/receive_batch')->name('couponReceiveBatch')->option(['real_name' => 'Nhận phiếu giảm giá hàng loạt']); //Nhận phiếu giảm giá hàng loạt
        Route::get('coupons/user/:types', 'v1.store.StoreCouponsController/user')->name('couponsUser')->option(['real_name' => 'Phiếu giảm giá người dùng đã nhận']);//Phiếu giảm giá người dùng đã nhận
        Route::get('coupons/order/:price', 'v1.store.StoreCouponsController/order')->name('couponsOrder')->option(['real_name' => 'Danh sách phiếu giảm giá cho đơn hàng']);//Danh sách phiếu giảm giá cho đơn hàng
    })->option(['mark' => 'coupons', 'mark_name' => 'Phiếu giảm giá']);

    Route::group(function () {
        //Lớp giỏ hàng
        Route::get('cart/list', 'v1.store.StoreCartController/lst')->name('cartList')->option(['real_name' => 'Danh sách giỏ hàng']); //Danh sách giỏ hàng
        Route::post('cart/add', 'v1.store.StoreCartController/add')->name('cartAdd')->option(['real_name' => 'Thêm vào giỏ hàng']); //Thêm vào giỏ hàng
        Route::post('cart/del', 'v1.store.StoreCartController/del')->name('cartDel')->option(['real_name' => 'Xóa khỏi giỏ hàng']); //Xóa khỏi giỏ hàng
        Route::post('order/cancel', 'v1.order.StoreOrderController/cancel')->name('orderCancel')->option(['real_name' => 'Hủy đơn hàng']); //Hủy đơn hàng
        Route::post('cart/num', 'v1.store.StoreCartController/num')->name('cartNum')->option(['real_name' => 'Sửa số lượng sản phẩm trong giỏ hàng']); //Giỏ hàng - Sửa số lượng sản phẩm
        Route::get('cart/count', 'v1.store.StoreCartController/count')->name('cartCount')->option(['real_name' => 'Số lượng trong giỏ hàng']); //Giỏ hàng - Lấy số lượng
    })->option(['mark' => 'cart', 'mark_name' => 'Giỏ hàng']);

    Route::group(function () {
        //Lớp đơn hàng
        Route::post('order/check_shipping', 'v1.order.StoreOrderController/checkShipping')->name('checkShipping')->option(['real_name' => 'Kiểm tra có hiển thị nhãn chuyển phát và nhận tại cửa hàng không']); //Kiểm tra có hiển thị nhãn chuyển phát và nhận tại cửa hàng không
        Route::post('order/confirm', 'v1.order.StoreOrderController/confirm')->name('orderConfirm')->option(['real_name' => 'Xác nhận đơn hàng']); //Xác nhận đơn hàng
        Route::post('order/computed/:key', 'v1.order.StoreOrderController/computedOrder')->name('computedOrder')->option(['real_name' => 'Tính số tiền đơn hàng']); //Tính số tiền đơn hàng
        Route::post('order/create/:key', 'v1.order.StoreOrderController/create')->name('orderCreate')->middleware(BlockerMiddleware::class)->option(['real_name' => 'Tạo đơn hàng']); //Tạo đơn hàng
        Route::get('order/data', 'v1.order.StoreOrderController/data')->name('orderData')->option(['real_name' => 'Dữ liệu thống kê đơn hàng']); //Dữ liệu thống kê đơn hàng
        Route::get('order/list', 'v1.order.StoreOrderController/lst')->name('orderList')->option(['real_name' => 'Danh sách đơn hàng']); //Danh sách đơn hàng
        Route::get('order/detail/:uni/[:cartId]', 'v1.order.StoreOrderController/detail')->name('orderDetail')->option(['real_name' => 'Chi tiết đơn hàng']); //Chi tiết đơn hàng
        Route::get('order/refund_detail/:uni/[:cartId]', 'v1.order.StoreOrderController/refund_detail')->name('refundDetail')->option(['real_name' => 'Chi tiết đơn hoàn tiền']); //Chi tiết đơn hoàn tiền
        Route::get('order/refund/reason', 'v1.order.StoreOrderController/refund_reason')->name('orderRefundReason')->middleware(BlockerMiddleware::class)->option(['real_name' => 'Lý do hoàn tiền đơn hàng']); //Lý do hoàn tiền đơn hàng
        Route::post('order/refund/verify', 'v1.order.StoreOrderController/refund_verify')->name('orderRefundVerify')->middleware(BlockerMiddleware::class)->option(['real_name' => 'Duyệt hoàn tiền đơn hàng']); //Duyệt hoàn tiền đơn hàng
        Route::post('order/take', 'v1.order.StoreOrderController/take')->name('orderTake')->middleware(BlockerMiddleware::class)->option(['real_name' => 'Xác nhận đã nhận hàng']); //Xác nhận đã nhận hàng
        Route::get('order/express/:uni/[:type]', 'v1.order.StoreOrderController/express')->name('orderExpress')->option(['real_name' => 'Xem vận chuyển của đơn hàng']); //Xem vận chuyển của đơn hàng
        Route::post('order/del', 'v1.order.StoreOrderController/del')->name('orderDel')->option(['real_name' => 'Xóa đơn hàng']); //Xóa đơn hàng
        Route::post('order/again', 'v1.order.StoreOrderController/again')->name('orderAgain')->option(['real_name' => 'Đặt hàng lại']); //Đơn hàng - Đặt lại
        Route::post('order/pay', 'v1.order.StoreOrderController/pay')->name('orderPay')->option(['real_name' => 'Thanh toán đơn hàng']); //Thanh toán đơn hàng
        Route::post('order/product', 'v1.order.StoreOrderController/product')->name('orderProduct')->option(['real_name' => 'Thông tin sản phẩm của đơn hàng']); //Thông tin sản phẩm của đơn hàng
        Route::post('order/comment', 'v1.order.StoreOrderController/comment')->name('orderComment')->option(['real_name' => 'Đánh giá đơn hàng']); //Đánh giá đơn hàng
        Route::get('order/cashier/:orderId/[:type]', 'v1.order.StoreOrderController/cashier')->name('orderCashier')->option(['real_name' => 'Trang thanh toán đơn hàng']); //Trang thanh toán đơn hàng
        Route::get('order/friend_detail', 'v1.order.StoreOrderController/friendDetail')->name('friendDetail')->option(['real_name' => 'Chi tiết thanh toán hộ']);//Chi tiết thanh toán hộ
        Route::post('order/receive_gift/:oid', 'v1.order.StoreOrderController/receiveGift')->name('receiveGift')->option(['real_name' => 'Nhận quà']);//Nhận quà
        Route::get('order/gift_detail/:oid', 'v1.order.StoreOrderController/giftDetail')->name('giftDetail')->option(['real_name' => 'Chi tiết quà tặng']); //Chi tiết quà tặng

    })->option(['mark' => 'order', 'mark_name' => 'Đơn hàng']);

    Route::group(function () {
        //Hoạt động --- Săn giảm giá
        Route::get('bargain/detail/:id', 'v1.activity.StoreBargainController/detail')->name('bargainDetail')->option(['real_name' => 'Chi tiết sản phẩm săn giảm giá']);//Chi tiết sản phẩm săn giảm giá
        Route::post('bargain/start', 'v1.activity.StoreBargainController/start')->name('bargainStart')->option(['real_name' => 'Bắt đầu săn giảm giá']);//Bắt đầu săn giảm giá
        Route::post('bargain/start/user', 'v1.activity.StoreBargainController/start_user')->name('bargainStartUser')->option(['real_name' => 'Thông tin người dùng săn giảm giá']);//Săn giảm giá - Thông tin người dùng bắt đầu săn giảm giá
        Route::post('bargain/share', 'v1.activity.StoreBargainController/share')->name('bargainShare')->option(['real_name' => 'Chia sẻ săn giảm giá']);//Săn giảm giá - Số lần xem/chia sẻ/tham gia
        Route::post('bargain/help', 'v1.activity.StoreBargainController/help')->name('bargainHelp')->option(['real_name' => 'Giúp bạn bè săn giảm giá']);//Săn giảm giá - Giúp bạn bè giảm giá
        Route::post('bargain/help/price', 'v1.activity.StoreBargainController/help_price')->name('bargainHelpPrice')->option(['real_name' => 'Số tiền đã được giảm khi săn giảm giá']);//Săn giảm giá - Số tiền đã giảm
        Route::post('bargain/help/count', 'v1.activity.StoreBargainController/help_count')->name('bargainHelpCount')->option(['real_name' => 'Thống kê người giúp săn giảm giá']);//Săn giảm giá - Tổng số người giúp giảm giá, số tiền còn lại, thanh tiến trình, giá đã giảm được
        Route::post('bargain/help/list', 'v1.activity.StoreBargainController/help_list')->name('bargainHelpList')->option(['real_name' => 'Săn giảm giá - Người giúp giảm giá']);//Săn giảm giá - Người giúp giảm giá
        Route::post('bargain/poster', 'v1.activity.StoreBargainController/poster')->name('bargainPoster')->option(['real_name' => 'Poster săn giảm giá']);//Poster săn giảm giá
        Route::get('bargain/user/list', 'v1.activity.StoreBargainController/user_list')->name('bargainUserList')->option(['real_name' => 'Danh sách săn giảm giá']);//Danh sách săn giảm giá (đã tham gia)
        Route::post('bargain/user/cancel', 'v1.activity.StoreBargainController/user_cancel')->name('bargainUserCancel')->option(['real_name' => 'Hủy săn giảm giá']);//Hủy săn giảm giá
        Route::get('bargain/poster_info/:bargainId', 'v1.activity.StoreBargainController/posterInfo')->name('posterInfo')->option(['real_name' => 'Thông tin chi tiết poster săn giảm giá']);//Thông tin chi tiết poster săn giảm giá
    })->option(['parent' => 'activity_nologin', 'cate_name' => 'Săn giảm giá']);

    Route::group(function () {
        //Hoạt động --- Mua chung
        Route::get('combination/pink/:id', 'v1.activity.StoreCombinationController/pink')->name('combinationPink')->option(['real_name' => 'Mở nhóm mua chung']);//Mở nhóm mua chung
        Route::post('combination/remove', 'v1.activity.StoreCombinationController/remove')->name('combinationRemove')->option(['real_name' => 'Mua chung - Hủy mở nhóm']);//Mua chung - Hủy mở nhóm
        Route::post('combination/poster', 'v1.activity.StoreCombinationController/poster')->name('combinationPoster')->option(['real_name' => 'Poster mua chung']);//Poster mua chung
        Route::get('combination/poster_info/:id', 'v1.activity.StoreCombinationController/posterInfo')->name('pinkPosterInfo')->option(['real_name' => 'Lấy chi tiết poster mua chung']);//Lấy chi tiết poster mua chung
        Route::get('combination/code/:id', 'v1.activity.StoreCombinationController/code')->name('combinationCode')->option(['real_name' => 'Poster sản phẩm mua chung']);//Poster sản phẩm mua chung
        Route::get('seckill/code/:id', 'v1.activity.StoreSeckillController/code')->name('seckillCode')->option(['real_name' => 'Poster sản phẩm flash sale']);//Poster sản phẩm flash sale
    })->option(['parent' => 'activity_nologin', 'cate_name' => 'Mua chung']);;

    Route::group(function () {
        //Lớp hóa đơn/sao kê
        Route::post('spread/people', 'v1.user.UserController/spread_people')->name('spreadPeople')->option(['real_name' => 'Người dùng được giới thiệu']);//Người dùng được giới thiệu
        Route::post('spread/order', 'v1.user.UserBillController/spread_order')->name('spreadOrder')->option(['real_name' => 'Đơn hàng giới thiệu']);//Đơn hàng giới thiệu
        Route::get('spread/commission/:type', 'v1.user.UserBillController/spread_commission')->name('spreadCommission')->option(['real_name' => 'Chi tiết hoa hồng giới thiệu']);//Chi tiết hoa hồng giới thiệu
        Route::get('spread/count/:type', 'v1.user.UserBillController/spread_count')->name('spreadCount')->option(['real_name' => 'Hoa hồng giới thiệu']);//Giới thiệu - Tổng hoa hồng (3)/rút tiền (4)
        Route::get('spread/banner', 'v1.user.UserBillController/spread_banner')->name('spreadBanner')->option(['real_name' => 'Tạo poster mã QR giới thiệu CTV']);//Tạo poster mã QR giới thiệu CTV
        Route::get('integral/list', 'v1.user.UserBillController/integral_list')->name('integralList')->option(['real_name' => 'Lịch sử điểm thưởng']);//Lịch sử điểm thưởng
        Route::get('user/routine_code', 'v1.user.UserBillController/getRoutineCode')->name('getRoutineCode')->option(['real_name' => 'Mã QR Mini Program']);//Mã QR Mini Program
        Route::get('user/spread_info', 'v1.user.UserBillController/getSpreadInfo')->name('getSpreadInfo')->option(['real_name' => 'Lấy hình nền CTV và các thông tin khác']);//Lấy hình nền CTV và các thông tin khác
        Route::post('division/order', 'v1.user.UserBillController/divisionOrder')->name('divisionOrder')->option(['real_name' => 'Đơn hàng giới thiệu của đại lý khu vực']);//Đơn hàng giới thiệu của đại lý khu vực
    })->option(['mark' => 'division', 'mark_name' => 'Sao kê']);

    Route::group(function () {
        //Lớp rút tiền
        Route::get('extract/bank', 'v1.user.UserExtractController/bank')->name('extractBank')->option(['real_name' => 'Ngân hàng rút tiền']);//Ngân hàng rút tiền/số tiền rút tối thiểu
        Route::post('extract/cash', 'v1.user.UserExtractController/cash')->name('extractCash')->option(['real_name' => 'Yêu cầu rút tiền']);//Yêu cầu rút tiền
    })->option(['mark' => 'extract', 'mark_name' => 'Rút tiền']);

    Route::group(function () {
        //Lớp nạp tiền
        Route::post('recharge/recharge', 'v1.user.UserRechargeController/recharge')->name('rechargeRecharge')->option(['real_name' => 'Nạp tiền hợp nhất']);//Nạp tiền hợp nhất
        Route::post('recharge/routine', 'v1.user.UserRechargeController/routine')->name('rechargeRoutine')->option(['real_name' => 'Nạp tiền qua Mini Program']);//Nạp tiền qua Mini Program
        Route::post('recharge/wechat', 'v1.user.UserRechargeController/wechat')->name('rechargeWechat')->option(['real_name' => 'Nạp tiền qua OA WeChat']);//Nạp tiền qua OA WeChat
        Route::get('recharge/index', 'v1.user.UserRechargeController/index')->name('rechargeQuota')->option(['real_name' => 'Chọn mức nạp số dư']);//Chọn mức nạp số dư
    })->option(['mark' => 'recharge', 'mark_name' => 'Nạp tiền']);

    Route::group(function () {
        //Lớp hạng thành viên
        Route::get('user/level/detection', 'v1.user.UserLevelController/detection')->name('userLevelDetection')->option(['real_name' => 'Kiểm tra người dùng có thể trở thành thành viên không']);//Kiểm tra người dùng có thể trở thành thành viên không
        Route::get('user/level/grade', 'v1.user.UserLevelController/grade')->name('userLevelGrade')->option(['real_name' => 'Danh sách hạng thành viên']);//Danh sách hạng thành viên
        Route::get('user/level/task/:id', 'v1.user.UserLevelController/task')->name('userLevelTask')->option(['real_name' => 'Lấy nhiệm vụ hạng']);//Lấy nhiệm vụ hạng
        Route::get('user/level/info', 'v1.user.UserLevelController/userLevelInfo')->name('levelInfo')->option(['real_name' => 'Lấy nhiệm vụ hạng']);//Lấy nhiệm vụ hạng
        Route::get('user/level/expList', 'v1.user.UserLevelController/expList')->name('expList')->option(['real_name' => 'Lấy nhiệm vụ hạng']);//Lấy nhiệm vụ hạng
        Route::get('user/record', 'v1.user.StoreService/recordList')->name('recordList')->option(['real_name' => 'Lấy danh sách tin nhắn giữa người dùng và CSKH']);//Lấy danh sách tin nhắn giữa người dùng và CSKH
    })->option(['mark' => 'user_level', 'mark_name' => 'Hạng thành viên']);

    Route::group(function () {
        //Thẻ thành viên
        Route::get('user/member/card/index', 'v1.user.MemberCardController/index')->name('userMemberCardIndex')->option(['real_name' => 'Trang giới thiệu quyền lợi thành viên']);// Trang giới thiệu quyền lợi thành viên
        Route::post('user/member/card/draw', 'v1.user.MemberCardController/draw_member_card')->name('userMemberCardDraw')->option(['real_name' => 'Nhận thẻ thành viên bằng mã thẻ']);//Nhận thẻ thành viên bằng mã thẻ
        Route::post('user/member/card/create', 'v1.order.OtherOrderController/create')->name('userMemberCardCreate')->option(['real_name' => 'Tạo đơn hàng mua thẻ']);//Tạo đơn hàng mua thẻ
        Route::get('user/member/coupons/list', 'v1.user.MemberCardController/memberCouponList')->name('userMemberCouponsList')->option(['real_name' => 'Danh sách phiếu giảm giá thành viên']);//Danh sách phiếu giảm giá thành viên
        Route::get('user/member/overdue/time', 'v1.user.MemberCardController/getOverdueTime')->name('userMemberOverdueTime')->option(['real_name' => 'Thời hạn thành viên']);//Thời hạn thành viên
    })->option(['parent' => 'user', 'cate_name' => 'Thẻ thành viên']);

    Route::group(function () {
        //Thanh toán ngoại tuyến
        Route::post('order/offline/check/price', 'v1.order.OtherOrderController/computed_offline_pay_price')->name('orderOfflineCheckPrice')->option(['real_name' => 'Kiểm tra số tiền thanh toán ngoại tuyến']); //Kiểm tra số tiền thanh toán ngoại tuyến
        Route::post('order/offline/create', 'v1.order.OtherOrderController/create')->name('orderOfflineCreate')->option(['real_name' => 'Kiểm tra số tiền thanh toán ngoại tuyến']); //Kiểm tra số tiền thanh toán ngoại tuyến
        Route::get('order/offline/pay/type', 'v1.order.OtherOrderController/pay_type')->name('orderOfflineCreate')->option(['real_name' => 'Phương thức thanh toán ngoại tuyến']); //Phương thức thanh toán ngoại tuyến
    })->option(['mark' => 'offline', 'mark_name' => 'Thanh toán ngoại tuyến']);

    Route::group(function () {
        //Thông báo - Thông báo nội bộ
        Route::get('user/message_system/list', 'v1.user.MessageSystemController/message_list')->name('MessageSystemList')->option(['real_name' => 'Danh sách thông báo nội bộ']); //Danh sách thông báo nội bộ
        Route::get('user/message_system/detail/:id', 'v1.user.MessageSystemController/detail')->name('MessageSystemDetail')->option(['real_name' => 'Chi tiết']); //Chi tiết
        Route::get('user/message_system/edit_message', 'v1.user.MessageSystemController/edit_message')->name('EditMessage')->option(['real_name' => 'Cài đặt thông báo nội bộ']);//Đặt thông báo nội bộ thành chưa đọc/xóa
    })->option(['mark' => 'message_system', 'mark_name' => 'Thông báo nội bộ']);

    Route::group(function () {
        //Đơn hàng cửa hàng đổi điểm
        Route::post('store_integral/order/confirm', 'v1.order.StoreIntegralOrderController/confirm')->name('storeIntegralOrderConfirm')->option(['real_name' => 'Xác nhận đơn hàng']); //Xác nhận đơn hàng
        Route::post('store_integral/order/create', 'v1.order.StoreIntegralOrderController/create')->name('storeIntegralOrderCreate')->option(['real_name' => 'Tạo đơn hàng']); //Tạo đơn hàng
        Route::get('store_integral/order/detail/:uni', 'v1.order.StoreIntegralOrderController/detail')->name('storeIntegralOrderDetail')->option(['real_name' => 'Chi tiết đơn hàng']); //Chi tiết đơn hàng
        Route::get('store_integral/order/list', 'v1.order.StoreIntegralOrderController/lst')->name('storeIntegralOrderList')->option(['real_name' => 'Danh sách đơn hàng']); //Danh sách đơn hàng
        Route::post('store_integral/order/take', 'v1.order.StoreIntegralOrderController/take')->name('storeIntegralOrderTake')->option(['real_name' => 'Xác nhận đã nhận hàng']); //Xác nhận đã nhận hàng
        Route::get('store_integral/order/express/:uni', 'v1.order.StoreIntegralOrderController/express')->name('storeIntegralOrderExpress')->option(['real_name' => 'Xem vận chuyển của đơn hàng']); //Xem vận chuyển của đơn hàng
        Route::post('store_integral/order/del', 'v1.order.StoreIntegralOrderController/del')->name('storeIntegralOrderDel')->option(['real_name' => 'Xóa đơn hàng']); //Xóa đơn hàng
    })->option(['mark' => 'order_integral', 'mark_name' => 'Đơn đổi điểm']);;

    Route::group(function () {
        /** Liên quan hoàn tiền */
        Route::get('order/refund/cart_info/:id', 'v1.order.StoreOrderController/refundCartInfo')->name('refundCartInfo')->option(['real_name' => 'Danh sách sản phẩm đơn hàng tại trang trung gian hoàn tiền']);//Danh sách sản phẩm đơn hàng tại trang trung gian hoàn tiền
        Route::post('order/refund/cart_info', 'v1.order.StoreOrderController/refundCartInfoList')->name('StoreOrderRefundCartInfoList')->option(['real_name' => 'Lấy danh sách sản phẩm hoàn tiền']);//Lấy danh sách sản phẩm hoàn tiền
        Route::post('order/refund/apply/:id', 'v1.order.StoreOrderController/applyRefund')->name('StoreOrderApplyRefund')->option(['real_name' => 'Yêu cầu hoàn tiền đơn hàng']);//Yêu cầu hoàn tiền đơn hàng
        Route::get('order/refund/list', 'v1.order.StoreOrderRefundController/refundList')->name('refundList')->option(['real_name' => 'Danh sách đơn hoàn tiền']);//Danh sách đơn hoàn tiền
        Route::get('order/refund/detail/:uni', 'v1.order.StoreOrderRefundController/refundDetail')->name('refundDetail')->option(['real_name' => 'Chi tiết đơn hoàn tiền']);//Chi tiết đơn hoàn tiền
        Route::post('order/refund/cancel/:uni', 'v1.order.StoreOrderRefundController/cancelApply')->name('cancelApply')->option(['real_name' => 'Người dùng hủy yêu cầu hoàn tiền']);//Người dùng hủy yêu cầu hoàn tiền
        Route::post('order/refund/express', 'v1.order.StoreOrderRefundController/applyExpress')->name('refundDetail')->option(['real_name' => 'Chi tiết đơn hoàn tiền']);//Chi tiết đơn hoàn tiền
        Route::get('order/refund/del/:uni', 'v1.order.StoreOrderRefundController/delRefund')->name('delRefund')->option(['real_name' => 'Người dùng hủy yêu cầu hoàn tiền']);//Người dùng hủy yêu cầu hoàn tiền
    })->option(['mark' => 'refund', 'mark_name' => 'Đổi trả']);

    Route::group(function () {
        /** Liên quan đại lý */
        Route::get('agent/apply/info', 'v1.user.DivisionController/applyInfo')->name('Chi tiết đăng ký')->option(['real_name' => 'Chi tiết đăng ký']);//Chi tiết đăng ký
        Route::post('agent/apply/:id', 'v1.user.DivisionController/applyAgent')->name('applyAgent')->option(['real_name' => 'Đăng ký làm đại lý']);//Đăng ký làm đại lý
        Route::get('agent/get_agent_agreement', 'v1.user.DivisionController/getAgentAgreement')->name('getAgentAgreement')->option(['real_name' => 'Quy định đại lý']);//Quy định đại lý
        Route::get('agent/get_staff_list', 'v1.user.DivisionController/getStaffList')->name('getStaffList')->option(['real_name' => 'Danh sách nhân viên']);//Danh sách nhân viên
        Route::post('agent/set_staff_percent', 'v1.user.DivisionController/setStaffPercent')->name('setStaffPercent')->option(['real_name' => 'Đặt tỷ lệ chia hoa hồng cho nhân viên']);//Đặt tỷ lệ chia hoa hồng cho nhân viên
        Route::get('agent/del_staff/:uid', 'v1.user.DivisionController/delStaff')->name('delStaff')->option(['real_name' => 'Xóa nhân viên']);//Xóa nhân viên
        Route::post('agent/spread', 'v1.user.DivisionController/agentSpread')->name('agentSpread')->option(['real_name' => 'Đại lý liên kết nhân viên']);//Đại lý liên kết nhân viên
    })->option(['mark' => 'agent', 'mark_name' => 'Đại lý']);

    Route::group(function () {
        /** Liên quan hoa hồng */
        Route::get('commission', 'v1.user.UserBrokerageController/commission')->name('commission')->option(['real_name' => 'Dữ liệu giới thiệu']);//Dữ liệu giới thiệu, hoa hồng hôm qua, số tiền đã rút lũy kế, hoa hồng hiện tại
        Route::get('brokerage_rank', 'v1.user.UserBrokerageController/brokerageRank')->name('brokerageRank')->option(['real_name' => 'Xếp hạng hoa hồng']);//Xếp hạng hoa hồng
        /** Hủy tài khoản người dùng */
        Route::get('user_cancel', 'v1.user.UserController/SetUserCancel')->name('SetUserCancel')->option(['real_name' => 'Hủy tài khoản người dùng']);//Hủy tài khoản người dùng
        /** Lịch sử xem của người dùng */
        Route::get('user/visit_list', 'v1.user.UserController/visitList')->name('visitList')->option(['real_name' => 'Danh sách sản phẩm đã xem']);//Danh sách sản phẩm đã xem
        Route::delete('user/visit', 'v1.user.UserController/visitDelete')->name('visitDelete')->option(['real_name' => 'Xóa lịch sử xem sản phẩm']);//Xóa lịch sử xem sản phẩm
    })->option(['mark' => 'user', 'mark_name' => 'Người dùng']);

    Route::group(function () {
        /** Đơn đăng ký cộng tác viên */
        Route::get('user/spread/apply/info', 'v1.user.SpreadApplyController/applyInfo')->name('Thông tin đăng ký');//Thông tin đăng ký
        Route::post('user/spread/apply/:id', 'v1.user.SpreadApplyController/applyPromoter')->name('Đăng ký làm CTV');//Đăng ký làm CTV
    })->option(['mark' => 'spread', 'mark_name' => 'Đơn đăng ký cộng tác viên']);

})->middleware(\app\http\middleware\AllowOriginMiddleware::class)->middleware(\app\api\middleware\StationOpenMiddleware::class)->middleware(\app\api\middleware\AuthTokenMiddleware::class, true);
//API chưa ủy quyền
Route::group(function () {
    Route::group(function () {
        Route::get('menu/user', 'v1.PublicController/menu_user')->name('menuUser')->option(['real_name' => 'Menu trang cá nhân']);//Menu trang cá nhân
        //Lớp dùng chung
        Route::get('index', 'v1.PublicController/index')->name('index')->option(['real_name' => 'Trang chủ']);//Trang chủ
        Route::get('site_config', 'v1.PublicController/getSiteConfig')->name('getSiteConfig')->option(['real_name' => 'Lấy cấu hình website']);//Lấy cấu hình website
        //API DIY
        Route::get('diy/get_diy/[:id]', 'v1.PublicController/getDiy');
        Route::get('home/products', 'v1.PublicController/home_products_list')->name('homeProductsList')->option(['real_name' => 'Lấy ảnh trình chiếu và sản phẩm của các loại sản phẩm đề xuất trên trang chủ']);//Lấy ảnh trình chiếu và sản phẩm của các loại sản phẩm đề xuất trên trang chủ
    })->option(['mark' => 'index', 'mark_name' => 'API trang chủ']);

    Route::group(function () {
        Route::get('search/keyword', 'v1.PublicController/search')->name('searchKeyword')->option(['real_name' => 'Lấy từ khóa tìm kiếm phổ biến']);//Lấy từ khóa tìm kiếm phổ biến
        //Danh mục sản phẩm
        Route::get('category', 'v1.store.CategoryController/category')->name('category')->option(['real_name' => 'Danh mục sản phẩm']);
        Route::get('category_version', 'v1.store.CategoryController/getCategoryVersion')->name('getCategoryVersion')->option(['real_name' => 'Phiên bản danh mục sản phẩm']);//Phiên bản danh mục sản phẩm

        //Lớp sản phẩm
        Route::post('image_base64', 'v1.PublicController/get_image_base64')->name('getImageBase64')->option(['real_name' => 'Lấy base64 của ảnh']);// Lấy base64 của ảnh
        Route::get('product/detail/:id/[:type]', 'v1.store.StoreProductController/detail')->name('detail')->option(['real_name' => 'Chi tiết sản phẩm']);//Chi tiết sản phẩm
        Route::get('groom/list/:type', 'v1.store.StoreProductController/groom_list')->name('groomList')->option(['real_name' => 'Lấy ảnh trình chiếu và sản phẩm của các loại sản phẩm đề xuất trên trang chủ']);//Lấy ảnh trình chiếu và sản phẩm của các loại sản phẩm đề xuất trên trang chủ
        Route::get('products', 'v1.store.StoreProductController/lst')->name('products')->option(['real_name' => 'Danh sách sản phẩm']);//Danh sách sản phẩm
        Route::get('product/hot', 'v1.store.StoreProductController/product_hot')->name('productHot')->option(['real_name' => 'Gợi ý cho bạn']);//Gợi ý cho bạn
        Route::get('reply/list/:id', 'v1.store.StoreProductController/reply_list')->name('replyList')->option(['real_name' => 'Danh sách đánh giá sản phẩm']);//Danh sách đánh giá sản phẩm
        Route::get('reply/config/:id', 'v1.store.StoreProductController/reply_config')->name('replyConfig')->option(['real_name' => 'Số lượng đánh giá và tỷ lệ đánh giá tốt của sản phẩm']);//Số lượng đánh giá và tỷ lệ đánh giá tốt của sản phẩm
        Route::get('advance/list', 'v1.store.StoreProductController/advanceList')->name('advanceList')->option(['real_name' => 'Danh sách sản phẩm đặt trước']);//Danh sách sản phẩm đặt trước
        Route::get('product/code/:id', 'v1.store.StoreProductController/code')->name('productCode')->option(['real_name' => 'Mã QR chia sẻ sản phẩm']);//Mã QR chia sẻ sản phẩm - Cộng tác viên
        Route::get('product/real_price/:id/:unique', 'v1.store.StoreProductController/realPrice')->name('realPrice')->option(['real_name' => 'Giá cuối cùng của sản phẩm']);//Giá cuối cùng của sản phẩm
    })->option(['mark' => 'product', 'mark_name' => 'Sản phẩm']);

    Route::group(function () {

        Route::group(function () {
            //Lớp danh mục bài viết
            Route::get('article/category/list', 'v1.publics.ArticleCategoryController/lst')->name('articleCategoryList')->option(['real_name' => 'Danh sách danh mục bài viết']);//Danh sách danh mục bài viết
            //Lớp bài viết
            Route::get('article/list/:cid', 'v1.publics.ArticleController/lst')->name('articleList')->option(['real_name' => 'Danh sách bài viết']);//Danh sách bài viết
            Route::get('article/details/:id', 'v1.publics.ArticleController/details')->name('articleDetails')->option(['real_name' => 'Chi tiết bài viết']);//Chi tiết bài viết
            Route::get('article/hot/list', 'v1.publics.ArticleController/hot')->name('articleHotList')->option(['real_name' => 'Bài viết nổi bật']);//Bài viết nổi bật
            Route::get('article/new/list', 'v1.publics.ArticleController/new')->name('articleNewList')->option(['real_name' => 'Bài viết mới nhất']);//Bài viết mới nhất
            Route::get('article/banner/list', 'v1.publics.ArticleController/banner')->name('articleBannerList')->option(['real_name' => 'Banner bài viết']);//Banner bài viết
        })->option(['parent' => 'activity_nologin', 'cate_name' => 'Bài viết (chưa ủy quyền)']);

        Route::group(function () {
            //Hoạt động --- Flash sale
            Route::get('seckill/index', 'v1.activity.StoreSeckillController/index')->name('seckillIndex')->option(['real_name' => 'Khung thời gian sản phẩm flash sale']);//Khung thời gian sản phẩm flash sale
            Route::get('seckill/list/:time', 'v1.activity.StoreSeckillController/lst')->name('seckillList')->option(['real_name' => 'Danh sách sản phẩm flash sale']);//Danh sách sản phẩm flash sale
            Route::get('seckill/detail/:id', 'v1.activity.StoreSeckillController/detail')->name('seckillDetail')->option(['real_name' => 'Chi tiết sản phẩm flash sale']);//Chi tiết sản phẩm flash sale
        })->option(['parent' => 'activity_nologin', 'cate_name' => 'Flash sale (chưa ủy quyền)']);

        Route::group(function () {
            //Hoạt động --- Săn giảm giá
            Route::get('bargain/config', 'v1.activity.StoreBargainController/config')->name('bargainConfig')->option(['real_name' => 'Cấu hình danh sách sản phẩm săn giảm giá']);//Cấu hình danh sách sản phẩm săn giảm giá
            Route::get('bargain/list', 'v1.activity.StoreBargainController/lst')->name('bargainList')->option(['real_name' => 'Danh sách sản phẩm săn giảm giá']);//Danh sách sản phẩm săn giảm giá
        })->option(['parent' => 'activity_nologin', 'cate_name' => 'Săn giảm giá (chưa ủy quyền)']);

        Route::group(function () {
            //Hoạt động --- Mua chung
            Route::get('combination/list', 'v1.activity.StoreCombinationController/lst')->name('combinationList')->option(['real_name' => 'Danh sách sản phẩm mua chung']);//Danh sách sản phẩm mua chung
            Route::get('combination/banner_list', 'v1.activity.StoreCombinationController/banner_list')->name('banner_list')->option(['real_name' => 'Danh sách sản phẩm mua chung']);//Danh sách sản phẩm mua chung
            Route::get('combination/detail/:id', 'v1.activity.StoreCombinationController/detail')->name('combinationDetail')->option(['real_name' => 'Chi tiết sản phẩm mua chung']);//Chi tiết sản phẩm mua chung
        })->option(['parent' => 'activity_nologin', 'cate_name' => 'Mua chung (chưa ủy quyền)']);

        //Hoạt động - Đặt trước
        Route::get('advance/detail/:id', 'v1.activity.StoreAdvanceController/detail')->name('advanceDetail')->option(['real_name' => 'Chi tiết sản phẩm đặt trước']);//Chi tiết sản phẩm đặt trước

        //Lớp người dùng
        Route::get('user/activity', 'v1.user.UserController/activity')->name('userActivity')->option(['real_name' => 'Trạng thái chương trình']);//Trạng thái chương trình

    })->option(['mark' => 'activity_nologin', 'mark_name' => 'Chương trình']);

    Route::group(function () {
        //WeChat
        Route::get('wechat/config', 'v1.wechat.WechatController/config')->name('wechatConfig')->option(['real_name' => 'Cấu hình sdk WeChat']);//Cấu hình sdk WeChat
        Route::get('wechat/auth', 'v1.wechat.WechatController/auth')->name('wechatAuth')->option(['real_name' => 'Ủy quyền WeChat']);//Ủy quyền WeChat
        Route::post('wechat/app_auth', 'v1.wechat.WechatController/appAuth')->name('appAuth')->option(['real_name' => 'Ủy quyền WeChat APP']);//Ủy quyền WeChat APP

    })->option(['mark' => 'wechat', 'mark_name' => 'WeChat']);

    Route::group(function () {
        //Đăng nhập Mini Program
        Route::post('wechat/mp_auth', 'v1.wechat.AuthController/mp_auth')->name('mpAuth')->option(['real_name' => 'Đăng nhập Mini Program']);//Đăng nhập Mini Program
        Route::get('wechat/get_logo', 'v1.wechat.AuthController/get_logo')->name('getLogo')->option(['real_name' => 'Logo hiển thị khi ủy quyền đăng nhập Mini Program']);//Logo hiển thị khi ủy quyền đăng nhập Mini Program
        Route::get('wechat/temp_ids', 'v1.wechat.AuthController/temp_ids')->name('wechatTempIds')->option(['real_name' => 'Tin nhắn đăng ký Mini Program']);//Tin nhắn đăng ký Mini Program
        Route::get('wechat/live', 'v1.wechat.AuthController/live')->name('wechatLive')->option(['real_name' => 'Danh sách livestream Mini Program']);//Danh sách livestream Mini Program
        Route::get('wechat/livePlaybacks/:id', 'v1.wechat.AuthController/livePlaybacks')->name('livePlaybacks')->option(['real_name' => 'Phát lại livestream Mini Program']);//Phát lại livestream Mini Program
    })->option(['mark' => 'mini', 'mark_name' => 'Mini Program']);

    Route::group(function () {
        //Đơn vị vận chuyển
        Route::get('logistics', 'v1.PublicController/logistics')->name('logistics')->option(['real_name' => 'Danh sách đơn vị vận chuyển']);//Danh sách đơn vị vận chuyển

        //Cấu hình chia sẻ
        Route::get('share', 'v1.PublicController/share')->name('share')->option(['real_name' => 'Cấu hình chia sẻ']);//Cấu hình chia sẻ

        //Phiếu giảm giá
        Route::get('coupons', 'v1.store.StoreCouponsController/lst')->name('couponsList')->option(['real_name' => 'Danh sách phiếu giảm giá có thể nhận']); //Danh sách phiếu giảm giá có thể nhận

        //Thông báo bất đồng bộ mua SMS
        Route::post('sms/pay/notify', 'v1.PublicController/sms_pay_notify')->name('smsPayNotify')->option(['real_name' => 'Thông báo bất đồng bộ mua SMS']); //Thông báo bất đồng bộ mua SMS

        //Lấy poster theo dõi OA WeChat
        Route::get('wechat/follow', 'v1.wechat.WechatController/follow')->name('Follow')->option(['real_name' => 'Lấy poster theo dõi OA WeChat']);
        //Người dùng đã theo dõi chưa
        Route::get('subscribe', 'v1.user.UserController/subscribe')->name('Subscribe')->option(['real_name' => 'Người dùng đã theo dõi chưa']);
        //Danh sách cửa hàng
        Route::get('store_list', 'v1.PublicController/store_list')->name('storeList')->option(['real_name' => 'Danh sách cửa hàng']);
        //Lấy danh sách thành phố
        Route::get('city_list', 'v1.PublicController/city_list')->name('cityList')->option(['real_name' => 'Lấy danh sách thành phố']);
        //Dữ liệu mua chung
        Route::get('pink', 'v1.PublicController/pink')->name('pinkData')->option(['real_name' => 'Dữ liệu mua chung']);
        //Lấy thanh điều hướng dưới cùng
        Route::get('navigation/[:template_name]', 'v1.PublicController/getNavigation')->name('getNavigation')->option(['real_name' => 'Lấy thanh điều hướng dưới cùng']);
        //Truy cập của người dùng
        Route::post('user/set_visit', 'v1.user.UserController/set_visit')->name('setVisit')->option(['real_name' => 'Thêm lịch sử truy cập của người dùng']);// Thêm lịch sử truy cập của người dùng
        //API sao chép mã chia sẻ
        Route::get('copy_words', 'v1.PublicController/copy_words')->name('copyWords')->option(['real_name' => 'API sao chép mã chia sẻ']);// API sao chép mã chia sẻ
        //Lấy cấu hình website
        Route::get('site_config', 'v1.PublicController/getSiteConfig')->name('getSiteConfig')->option(['real_name' => 'Lấy cấu hình website']);//Lấy cấu hình website
    })->option(['mark' => 'setting', 'mark_name' => 'Cấu hình cửa hàng']);

    Route::group(function () {
        //Hoạt động --- Cửa hàng đổi điểm
        Route::get('store_integral/index', 'v1.activity.StoreIntegralController/index')->name('storeIntegralIndex')->option(['real_name' => 'Dữ liệu trang chủ cửa hàng đổi điểm']);//Dữ liệu trang chủ cửa hàng đổi điểm
        Route::get('store_integral/list', 'v1.activity.StoreIntegralController/lst')->name('storeIntegralList')->option(['real_name' => 'Danh sách sản phẩm đổi điểm']);//Danh sách sản phẩm đổi điểm
        Route::get('store_integral/detail/:id', 'v1.activity.StoreIntegralController/detail')->name('storeIntegralDetail')->option(['real_name' => 'Chi tiết sản phẩm đổi điểm']);//Chi tiết sản phẩm đổi điểm

    })->option(['mark' => 'integral_nologin', 'mark_name' => 'Cửa hàng đổi điểm (chưa ủy quyền)']);

    Route::group(function () {
        //Lấy phiên bản app mới nhất
        Route::get('get_new_app/:platform', 'v1.PublicController/getNewAppVersion')->name('getNewAppVersion')->option(['real_name' => 'Lấy phiên bản app mới nhất']);//Lấy phiên bản app mới nhất
        //Lấy loại CSKH
        Route::get('get_customer_type', 'v1.PublicController/getCustomerType')->name('getCustomerType')->option(['real_name' => 'Lấy loại CSKH']);//Lấy loại CSKH
        //Cài đặt kết nối liên tục
        Route::get('get_workerman_url', 'v1.PublicController/getWorkerManUrl')->name('getWorkerManUrl')->option(['real_name' => 'Cài đặt kết nối liên tục']);
        //Quảng cáo màn hình khởi động trang chủ
        Route::get('get_open_adv', 'v1.PublicController/getOpenAdv')->name('getOpenAdv')->option(['real_name' => 'Quảng cáo màn hình khởi động trang chủ']);
        //Lấy thỏa thuận người dùng
        Route::get('user_agreement', 'v1.PublicController/getUserAgreement')->name('getUserAgreement')->option(['real_name' => 'Lấy thỏa thuận người dùng']);
        //Lấy thỏa thuận
        Route::get('get_agreement/:type', 'v1.PublicController/getAgreement')->name('getAgreement')->option(['real_name' => 'Lấy thỏa thuận']);

    })->option(['mark' => 'other', 'mark_name' => 'API khác']);

    Route::group(function () {
        //Lấy danh sách loại ngôn ngữ
        Route::get('get_lang_type_list', 'v1.PublicController/getLangTypeList')->name('getLangTypeList')->option(['real_name' => 'Lấy danh sách loại ngôn ngữ']);
        //Lấy json ngôn ngữ hiện tại
        Route::get('get_lang_json', 'v1.PublicController/getLangJson')->name('getLangJson')->option(['real_name' => 'Lấy json ngôn ngữ hiện tại']);
        //Lấy loại ngôn ngữ mặc định được thiết lập trong trang quản trị
        Route::get('get_default_lang_type', 'v1.PublicController/getDefaultLangType')->name('getLangJson')->option(['real_name' => 'Lấy loại ngôn ngữ mặc định được thiết lập trong trang quản trị']);
        //Lấy loại ngôn ngữ mặc định được thiết lập trong trang quản trị
        Route::get('lang_version', 'v1.PublicController/getLangVersion')->name('getLangVersion')->option(['real_name' => 'Lấy loại ngôn ngữ mặc định được thiết lập trong trang quản trị']);
    })->option(['mark' => 'lang', 'mark_name' => 'Đa ngôn ngữ']);

    Route::group(function () {
        /** API tác vụ định kỳ */
        //API gọi tác vụ định kỳ
        Route::get('crontab/run', 'v1.CrontabController/crontabRun')->name('crontabRun')->option(['real_name' => 'API gọi tác vụ định kỳ']);
        //API kiểm tra tác vụ định kỳ
        Route::get('crontab/check', 'v1.CrontabController/crontabCheck')->name('crontabCheck')->option(['real_name' => 'API kiểm tra tác vụ định kỳ']);
        //Tự động hủy đơn hàng chưa thanh toán
        Route::get('crontab/order_cancel', 'v1.CrontabController/orderUnpaidCancel')->name('orderUnpaidCancel')->option(['real_name' => 'Tự động hủy đơn hàng chưa thanh toán']);
        //Xử lý đơn hàng mua chung hết hạn
        Route::get('crontab/pink_expiration', 'v1.CrontabController/pinkExpiration')->name('pinkExpiration')->option(['real_name' => 'Xử lý đơn hàng mua chung hết hạn']);
        //Tự động hủy liên kết với người giới thiệu
        Route::get('crontab/agent_unbind', 'v1.CrontabController/agentUnbind')->name('agentUnbind')->option(['real_name' => 'Tự động hủy liên kết với người giới thiệu']);
        //Cập nhật trạng thái sản phẩm livestream
        Route::get('crontab/live_product_status', 'v1.CrontabController/syncGoodStatus')->name('syncGoodStatus')->option(['real_name' => 'Cập nhật trạng thái sản phẩm livestream']);
        //Cập nhật trạng thái phòng livestream
        Route::get('crontab/live_room_status', 'v1.CrontabController/syncRoomStatus')->name('syncRoomStatus')->option(['real_name' => 'Cập nhật trạng thái phòng livestream']);
        //Tự động nhận hàng
        Route::get('crontab/take_delivery', 'v1.CrontabController/autoTakeOrder')->name('autoTakeOrder')->option(['real_name' => 'Tự động nhận hàng']);
        //Tra cứu sản phẩm đặt trước hết hạn và tự động ngừng bán
        Route::get('crontab/advance_off', 'v1.CrontabController/downAdvance')->name('downAdvance')->option(['real_name' => 'Tra cứu sản phẩm đặt trước hết hạn và tự động ngừng bán']);
        //Tự động đánh giá tốt
        Route::get('crontab/product_replay', 'v1.CrontabController/autoComment')->name('autoComment')->option(['real_name' => 'Tự động đánh giá tốt']);
        //Xóa poster ngày hôm qua
        Route::get('crontab/clear_poster', 'v1.CrontabController/emptyYesterdayAttachment')->name('emptyYesterdayAttachment')->option(['real_name' => 'Xóa poster ngày hôm qua']);

    })->option(['mark' => 'crontab', 'mark_name' => 'Tác vụ định kỳ']);

})->middleware(\app\http\middleware\AllowOriginMiddleware::class)
    ->middleware(\app\api\middleware\StationOpenMiddleware::class)
    ->middleware(\app\api\middleware\AuthTokenMiddleware::class, false);

Route::miss(function () {
    if (app()->request->isOptions()) {
        $header = Config::get('cookie.header');
        unset($header['Access-Control-Allow-Credentials']);
        return Response::create('ok')->code(200)->header($header);
    } else
        return Response::create()->code(404);
});
