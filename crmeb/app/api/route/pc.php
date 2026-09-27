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
use think\facade\Config;
use think\Response;

Route::group('pc', function () {
    //API đăng nhập
    Route::group(function () {
        Route::get('key', 'pc.LoginController/getLoginKey')->name('getLoginKey')->option(['real_name' => 'Lấy key đăng nhập bằng quét mã']);//Lấy key đăng nhập bằng quét mã
        Route::get('scan/:key', 'pc.LoginController/scanLogin')->name('scanLogin')->option(['real_name' => 'Kiểm tra trạng thái quét mã']);//Kiểm tra trạng thái quét mã
        Route::get('get_appid', 'pc.LoginController/getAppid')->name('getAppid')->option(['real_name' => 'Lấy appid nền tảng mở']);//Kiểm tra trạng thái quét mã
        Route::get('wechat_auth', 'pc.LoginController/wechatAuth')->name('wechatAuth')->option(['real_name' => 'Kiểm tra trạng thái quét mã']);//Kiểm tra trạng thái quét mã
    })->middleware(\app\http\middleware\AllowOriginMiddleware::class)
        ->middleware(\app\api\middleware\StationOpenMiddleware::class)
        ->option(['parent' => 'PC', 'cate_name' => 'Đăng nhập ủy quyền']);

    //API chưa ủy quyền
    Route::group(function () {
        Route::get('get_pay_vip_code', 'pc.HomeController/getPayVipCode')->name('getPayVipCode')->option(['real_name' => 'Lấy mã QR trang mua thành viên trả phí']);//Lấy mã QR trang mua thành viên trả phí
        Route::get('get_product_phone_buy', 'pc.HomeController/getProductPhoneBuy')->name('getProductPhoneBuy')->option(['real_name' => 'Cấu hình url chuyển hướng khi mua trên điện thoại']);//Cấu hình url chuyển hướng khi mua trên điện thoại
        Route::get('get_banner', 'pc.HomeController/getBanner')->name('getBanner')->option(['real_name' => 'Ảnh trình chiếu trang chủ PC']);//Ảnh trình chiếu trang chủ PC
        Route::get('get_category_product', 'pc.HomeController/getCategoryProduct')->name('getCategoryProduct')->option(['real_name' => 'Sản phẩm theo danh mục trên trang chủ']);//Sản phẩm theo danh mục trên trang chủ
        Route::get('get_products', 'pc.ProductController/getProductList')->name('getProductList')->option(['real_name' => 'Danh sách sản phẩm']);//Danh sách sản phẩm
        Route::get('get_product_code/:product_id/[:type]', 'pc.ProductController/getProductRoutineCode')->name('getProductRoutineCode')->option(['real_name' => 'Mã QR Mini Program của chi tiết sản phẩm']);//Mã QR Mini Program của chi tiết sản phẩm
        Route::get('get_city/:pid', 'pc.PublicController/getCity')->name('getCity')->option(['real_name' => 'Lấy dữ liệu thành phố']);//Lấy dữ liệu thành phố
        Route::get('check_order_status/:order_id/:end_time', 'pc.OrderController/checkOrderStatus')->name('checkOrderStatus')->option(['real_name' => 'API truy vấn liên tục trạng thái đơn hàng']);//API truy vấn liên tục trạng thái đơn hàng
        Route::get('get_company_info', 'pc.PublicController/getCompanyInfo')->name('getCompanyInfo')->option(['real_name' => 'Lấy thông tin công ty']);//Lấy thông tin công ty
        Route::get('get_recommend/:type', 'pc.ProductController/getRecommendList')->name('getRecommendList')->option(['real_name' => 'Lấy sản phẩm đề xuất']);//Lấy sản phẩm đề xuất
        Route::get('get_wechat_qrcode', 'pc.PublicController/getWechatQrcode')->name('getWechatQrcode')->option(['real_name' => 'Lấy mã QR theo dõi']);//Lấy mã QR theo dõi
        Route::get('get_good_product', 'pc.ProductController/getGoodProduct')->name('getGoodProduct')->option(['real_name' => 'Lấy sản phẩm tốt được đề xuất']);//Lấy sản phẩm tốt được đề xuất
        Route::get('get_news_category', 'pc.PublicController/getNewsCategory')->name('getNewsCategory')->option(['real_name' => 'Lấy danh mục bài viết']);//Lấy danh mục bài viết
        Route::get('get_news_list', 'pc.PublicController/getNewsList')->name('getNewsList')->option(['real_name' => 'Lấy danh sách bài viết']);//Lấy danh sách bài viết
        Route::get('get_news_detail/:id', 'pc.PublicController/getNewsDetail')->name('getNewsDetail')->option(['real_name' => 'Lấy chi tiết bài viết']);//Lấy chi tiết bài viết
    })->middleware(\app\http\middleware\AllowOriginMiddleware::class)
        ->middleware(\app\api\middleware\StationOpenMiddleware::class)
        ->middleware(\app\api\middleware\AuthTokenMiddleware::class, false)
        ->option(['parent' => 'PC', 'cate_name' => 'API người dùng chưa ủy quyền']);

    //API ủy quyền thành viên
    Route::group(function () {
        Route::get('get_cart_list', 'pc.CartController/getCartList')->name('getCartList')->option(['real_name' => 'Danh sách giỏ hàng']);//Danh sách giỏ hàng
        Route::get('get_balance_record/:type', 'pc.UserController/getBalanceRecord')->name('getBalanceRecord')->option(['real_name' => 'Lịch sử số dư']);//Lịch sử số dư
        Route::get('get_order_list', 'pc.OrderController/getOrderList')->name('getOrderList')->option(['real_name' => 'Danh sách đơn hàng']);//Danh sách đơn hàng
        Route::get('get_refund_order_list', 'pc.OrderController/getRefundOrderList')->name('getRefundOrderList')->option(['real_name' => 'Danh sách đơn hoàn tiền']);//Danh sách đơn hoàn tiền
        Route::get('get_collect_list', 'pc.UserController/getCollectList')->name('getCollectList')->option(['real_name' => 'Danh sách yêu thích']);//Danh sách yêu thích
    })->middleware(\app\http\middleware\AllowOriginMiddleware::class)
        ->middleware(\app\api\middleware\StationOpenMiddleware::class)
        ->middleware(\app\api\middleware\AuthTokenMiddleware::class, true)
        ->option(['parent' => 'PC', 'cate_name' => 'API người dùng đã ủy quyền']);

    Route::miss(function () {
        if (app()->request->isOptions()) {
            $header = Config::get('cookie.header');
            unset($header['Access-Control-Allow-Credentials']);
            return Response::create('ok')->code(200)->header($header);
        } else
            return Response::create()->code(404);
    });
})->middleware(\app\http\middleware\AllowOriginMiddleware::class)
    ->middleware(\app\api\middleware\StationOpenMiddleware::class)
    ->option(['mark' => 'PC', 'mark_name' => 'PC']);
