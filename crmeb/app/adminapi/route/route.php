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
use app\http\middleware\AllowOriginMiddleware;

/**
 * API không cần ủy quyền
 */
Route::group(function () {
    //Nâng cấp chương trình
    Route::get('upgrade', 'UpgradeController/index');
    Route::get('upgrade/run', 'UpgradeController/upgrade');
    //Đăng nhập bằng tên đăng nhập và mật khẩu
    Route::post('login', 'Login/login')->name('AdminLogin')->option(['real_name' => 'Tải xuống bản sao lưu bảng']);
    //Dữ liệu trang đăng nhập quản trị
    Route::get('login/info', 'Login/info')->option(['real_name' => 'Thông tin đăng nhập']);
    //Mã xác thực
    Route::get('captcha_pro', 'Login/captcha')->name('')->option(['real_name' => 'Lấy mã xác thực']);
    //Lấy mã xác thực
    Route::get('ajcaptcha', 'Login/ajcaptcha')->name('ajcaptcha')->option(['real_name' => 'Lấy mã xác thực']);
    //Xác minh lần đầu
    Route::post('ajcheck', 'Login/ajcheck')->name('ajcheck')->option(['real_name' => 'Xác minh lần đầu']);
    //Lấy dữ liệu CSKH
    Route::get('get_workerman_url', 'PublicController/getWorkerManUrl')->option(['real_name' => 'Lấy dữ liệu CSKH']);
    //Thử nghiệm
    Route::get('index', 'Test/index')->option(['real_name' => 'Địa chỉ thử nghiệm']);
    //Quét mã để tải lên ảnh
    Route::post('image/scan_upload', 'PublicController/scanUpload')->option(['real_name' => 'Quét mã để tải lên ảnh']);
    Route::get('custom_admin_js', 'PublicController/customAdminJs')->option(['real_name' => 'Địa chỉ thử nghiệm']);

})->middleware(AllowOriginMiddleware::class)->option(['mark' => 'login', 'mark_name' => 'Liên quan đăng nhập']);


/**
 * API cần ủy quyền
 */
Route::group(function () {
    //Thông tin máy chủ
    Route::get('system/info', 'PublicController/getSystemInfo')->option(['real_name' => 'Thông tin máy chủ']);
    //Nhập route
    Route::get('route/import_api', 'PublicController/import')->option(['real_name' => 'Nhập route']);
    //Tải xuống tệp
    Route::get('download/[:key]', 'PublicController/download')->option(['real_name' => 'Tải xuống tệp']);
})->middleware([
    AllowOriginMiddleware::class,
//    \app\adminapi\middleware\AdminAuthTokenMiddleware::class
])->option(['mark' => 'system', 'mark_name' => 'Liên quan hệ thống']);

/**
 * Route miss
 */
Route::miss(function () {
    if (app()->request->isOptions()) {
        $header = Config::get('cookie.header');
        $header['Access-Control-Allow-Origin'] = app()->request->header('origin');
        return Response::create('ok')->code(200)->header($header);
    } else
        return Response::create()->code(404);
});
