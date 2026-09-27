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
use app\http\middleware\AllowOriginMiddleware;
use app\kefuapi\middleware\KefuAuthTokenMiddleware;
use think\facade\Config;
use think\facade\Route;
use think\Response;

Route::group(function () {

    Route::group(function () {
        Route::post('login', 'Login/login')->name('kefuLogin')->option(['real_name' => 'Đăng nhập tài khoản']);//Đăng nhập tài khoản
        Route::get('key', 'Login/getLoginKey')->name('getLoginKey')->option(['real_name' => 'Lấy key đăng nhập bằng quét mã']);//Lấy key đăng nhập bằng quét mã
        Route::get('scan/:key', 'Login/scanLogin')->name('scanLogin')->option(['real_name' => 'Kiểm tra trạng thái quét mã']);//Kiểm tra trạng thái quét mã
        Route::get('config', 'Login/getAppid')->name('getAppid')->option(['real_name' => 'Lấy cấu hình']);//Lấy cấu hình
        Route::get('wechat', 'Login/wechatAuth')->name('wechatAuth')->option(['real_name' => 'Đăng nhập bằng quét mã WeChat']);//Đăng nhập bằng quét mã WeChat
    })->option(['mark' => 'login', 'mark_name' => 'Đăng nhập']);


    Route::group(function () {

        Route::post('upload', 'User/upload')->name('upload')->option(['real_name' => 'Tải lên ảnh', 'mark' => 'common', 'mark_name' => 'API dùng chung']);//Tải lên ảnh

    })->middleware(KefuAuthTokenMiddleware::class);

    Route::group('user', function () {

        Route::get('record', 'User/recordList')->name('recordList')->option(['real_name' => 'Người dùng đã trò chuyện với CSKH']);//Người dùng đã trò chuyện với CSKH
        Route::get('info/:uid', 'User/userInfo')->name('getUserInfo')->option(['real_name' => 'Thông tin chi tiết người dùng']);//Thông tin chi tiết người dùng
        Route::get('label/:uid', 'User/getUserLabel')->name('getUserLabel')->option(['real_name' => 'Nhãn người dùng']);//Nhãn người dùng
        Route::put('label/:uid', 'User/setUserLabel')->name('setUserLabel')->option(['real_name' => 'Đặt nhãn người dùng']);//Đặt nhãn người dùng
        Route::get('group', 'User/getUserGroup')->name('getUserGroup')->option(['real_name' => 'Lấy nhóm người dùng']);//Đăng xuất
        Route::put('group/:uid/:id', 'User/setUserGroup')->name('setUserGroup')->option(['real_name' => 'Đặt nhóm người dùng']);//Đăng xuất
        Route::post('logout', 'User/logout')->name('logout')->option(['real_name' => 'Đăng xuất']);//Đăng xuất

    })->middleware(KefuAuthTokenMiddleware::class)
        ->option(['mark' => 'user', 'mark_name' => 'Người dùng']);

    Route::group('order', function () {

        Route::get('list/:uid', 'Order/getUserOrderList')->name('getUserOrderList')->option(['real_name' => 'Danh sách đơn hàng']);//Danh sách đơn hàng
        Route::post('delivery/:id', 'Order/delivery_keep')->name('orderDeliveryKeep')->option(['real_name' => 'Giao đơn hàng']);//Giao đơn hàng
        Route::put('update/:id', 'Order/update')->name('orderUpdate')->option(['real_name' => 'Sửa đơn hàng']);//Sửa đơn hàng
        Route::post('refund', 'Order/refund')->name('orderRefund')->option(['real_name' => 'Hoàn tiền đơn hàng']);//Hoàn tiền đơn hàng
        Route::get('refund_form/:id', 'Order/refundForm')->name('orderRefund')->option(['real_name' => 'Hoàn tiền đơn hàng']);//Hoàn tiền đơn hàng
        Route::get('edit/:id', 'Order/edit')->name('orderEdit')->option(['real_name' => 'Hoàn tiền đơn hàng']);//Hoàn tiền đơn hàng
        Route::post('remark', 'Order/remark')->name('remark')->option(['real_name' => 'Ghi chú đơn hàng']);//Ghi chú đơn hàng
        Route::get('info/:id', 'Order/orderInfo')->name('orderInfo')->option(['real_name' => 'Lấy chi tiết đơn hàng']);//Lấy chi tiết đơn hàng
        Route::get('export', 'Order/export')->name('export')->option(['real_name' => 'Lấy chi tiết đơn hàng']);//Lấy chi tiết đơn hàng
        Route::get('temp', 'Order/getExportTemp')->name('getExportTemp')->option(['real_name' => 'Lấy mẫu của đơn vị vận chuyển']);//Lấy mẫu của đơn vị vận chuyển
        Route::get('delivery_all', 'Order/getDeliveryAll')->name('getDeliveryAll')->option(['real_name' => 'Lấy toàn bộ danh sách nhân viên giao hàng']);//Lấy toàn bộ danh sách nhân viên giao hàng
        Route::get('delivery_info', 'Order/getDeliveryInfo')->name('getDeliveryInfo')->option(['real_name' => 'Lấy toàn bộ danh sách nhân viên giao hàng']);//Lấy toàn bộ danh sách nhân viên giao hàng
        Route::get('verific/:id', 'Order/order_verific')->name('orderVerific')->option(['real_name' => 'Xác nhận sử dụng theo một mã đơn hàng']);//Xác nhận sử dụng theo một mã đơn hàng

    })->middleware(KefuAuthTokenMiddleware::class)
        ->option(['mark' => 'order', 'mark_name' => 'Đơn hàng']);

    Route::group('product', function () {

        Route::get('hot/:uid', 'Product/getProductHotSale')->name('getProductHotSale')->option(['real_name' => 'Sản phẩm bán chạy']);//Sản phẩm bán chạy
        Route::get('visit/:uid', 'Product/getVisitProductList')->name('getVisitProductList')->option(['real_name' => 'Sản phẩm đã xem']);//Sản phẩm đã xem
        Route::get('cart/:uid', 'Product/getCartProductList')->name('getCartProductList')->option(['real_name' => 'Lịch sử mua hàng']);//Lịch sử mua hàng
        Route::get('info/:id', 'Product/getProductInfo')->name('getProductInfo')->option(['real_name' => 'Chi tiết sản phẩm']);//Chi tiết sản phẩm

    })->middleware(KefuAuthTokenMiddleware::class)
        ->option(['mark' => 'service', 'mark_name' => 'Sản phẩm']);

    Route::group('service', function () {

        Route::get('list', 'Service/getChatList')->name('getChatList')->option(['real_name' => 'Lịch sử trò chuyện']);//Lịch sử trò chuyện
        Route::get('info', 'Service/getServiceInfo')->name('getServiceInfo')->option(['real_name' => 'Thông tin chi tiết CSKH']);//Thông tin chi tiết CSKH
        Route::get('speechcraft', 'Service/getSpeechcraftList')->name('getSpeechcraftList')->option(['real_name' => 'Câu trả lời mẫu CSKH']);//Câu trả lời mẫu CSKH
        Route::post('transfer', 'Service/transfer')->name('transfer')->option(['real_name' => 'Chuyển tiếp CSKH']);//Chuyển tiếp CSKH
        Route::get('transfer_list', 'Service/getServiceList')->name('getServiceList')->option(['real_name' => 'Chuyển tiếp CSKH']);//Chuyển tiếp CSKH
        Route::get('cate', 'Service/getCateList')->name('getCateList')->option(['real_name' => 'Danh sách danh mục']);//Danh sách danh mục
        Route::post('cate', 'Service/saveCate')->name('saveCate')->option(['real_name' => 'Lưu danh mục']);//Lưu danh mục
        Route::put('cate/:id', 'Service/editCate')->name('editCate')->option(['real_name' => 'Sửa danh mục']);//Sửa danh mục
        Route::delete('cate/:id', 'Service/deleteCate')->name('deleteCate')->option(['real_name' => 'Xóa danh mục']);//Xóa danh mục
        Route::post('speechcraft', 'Service/saveSpeechcraft')->name('saveSpeechcraft')->option(['real_name' => 'Thêm câu trả lời mẫu']);//Thêm câu trả lời mẫu
        Route::put('speechcraft/:id', 'Service/editSpeechcraft')->name('editSpeechcraft')->option(['real_name' => 'Sửa câu trả lời mẫu']);//Sửa câu trả lời mẫu
        Route::delete('speechcraft/:id', 'Service/deleteSpeechcraft')->name('deleteSpeechcraft')->option(['real_name' => 'Xóa câu trả lời mẫu']);//Xóa câu trả lời mẫu

    })->middleware(KefuAuthTokenMiddleware::class)
        ->option(['mark' => 'service', 'mark_name' => 'CSKH']);

    Route::group('tourist', function () {
        Route::get('user', 'Common/getServiceUser')->name('getServiceUser')->option(['real_name' => 'Thông tin CSKH ngẫu nhiên']);//Thông tin CSKH ngẫu nhiên
        Route::get('adv', 'Common/getKfAdv')->name('getKfAdv')->option(['real_name' => 'Lấy quảng cáo CSKH']);//Lấy quảng cáo CSKH
        Route::post('feedback', 'Common/saveFeedback')->name('saveFeedback')->option(['real_name' => 'Lưu nội dung phản hồi CSKH']);//Lưu nội dung phản hồi CSKH
        Route::get('feedback', 'Common/getFeedbackInfo')->name('getFeedbackInfo')->option(['real_name' => 'Lấy nội dung vị trí quảng cáo trang phản hồi']);//Lấy nội dung vị trí quảng cáo trang phản hồi
        Route::get('order/:order_id', 'Common/getOrderInfo')->name('getOrderInfo')->option(['real_name' => 'Lấy thông tin đơn hàng']);//Lấy thông tin đơn hàng
        Route::get('product/:id', 'Common/getProductInfo')->name('getProductInfo')->option(['real_name' => 'Lấy thông tin sản phẩm']);//Lấy thông tin sản phẩm
        Route::get('chat', 'Common/getChatList')->name('getChatList')->option(['real_name' => 'Lấy lịch sử trò chuyện']);//Lấy lịch sử trò chuyện
        Route::post('upload', 'Common/upload')->name('upload')->option(['real_name' => 'Tải lên ảnh']);//Tải lên ảnh
    })->option(['mark' => 'tourist', 'mark_name' => 'CSKH cho khách vãng lai']);

})->middleware(AllowOriginMiddleware::class);

Route::miss(function () {
    if (app()->request->isOptions()) {
        $header = Config::get('cookie.header');
        $header['Access-Control-Allow-Origin'] = app()->request->header('origin');
        return Response::create('ok')->code(200)->header($header);
    } else
        return Response::create()->code(404);
});
