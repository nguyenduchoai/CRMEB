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
 * Route nền tảng dịch vụ
 */
Route::group('serve', function () {
    //Đăng nhập nền tảng
    Route::post('login', 'v1.serve.Login/login')->option(['real_name' => 'Đăng nhập nền tảng Yihaotong']);
    //Mã xác thực
    Route::post('captcha', 'v1.serve.Login/captcha')->option(['real_name' => 'Lấy mã xác thực Yihaotong']);
    //Xác thực mã xác thực
    Route::post('checkCode', 'v1.serve.Login/checkCode')->option(['real_name' => 'Xác minh mã xác thực Yihaotong']);
    //Đăng ký
    Route::post('register', 'v1.serve.Login/register')->option(['real_name' => 'Đăng ký Yihaotong']);
    //Mở dịch vụ vận đơn điện tử
    Route::post('opn_express', 'v1.serve.Serve/openExpress')->option(['real_name' => 'Kích hoạt vận đơn điện tử Yihaotong']);
    //Lấy thông tin người dùng
    Route::get('info', 'v1.serve.Serve/getUserInfo')->option(['real_name' => 'Thông tin tài khoản Yihaotong']);
    //Lấy mẫu danh sách
    Route::get('meal_list', 'v1.serve.Serve/mealList')->option(['real_name' => 'Danh sách gói dịch vụ Yihaotong']);
    //Lấy thanh toán
    Route::post('pay_meal', 'v1.serve.Serve/payMeal')->option(['real_name' => 'Mã QR thanh toán Yihaotong']);
    //Kích hoạt dịch vụ SMS
    Route::get('sms/open', 'v1.serve.Sms/openServe')->option(['real_name' => 'Kích hoạt dịch vụ SMS Yihaotong']);
    //Mở dịch vụ khác
    Route::get('open', 'v1.serve.Serve/openServe')->option(['real_name' => 'Kích hoạt dịch vụ khác Yihaotong']);
    //Sửa chữ ký
    Route::put('sms/sign', 'v1.serve.Sms/editSign')->option(['real_name' => 'Sửa chữ ký Yihaotong']);
    //Lấy mẫu SMS
    Route::get('sms/temps', 'v1.serve.Sms/temps')->option(['real_name' => 'Lấy mẫu SMS Yihaotong']);
    //Đăng ký mẫu
    Route::post('sms/apply', 'v1.serve.Sms/apply')->option(['real_name' => 'Đăng ký mẫu Yihaotong']);
    //Lấy lịch sử yêu cầu
    Route::get('sms/apply_record', 'v1.serve.Sms/applyRecord')->option(['real_name' => 'Lấy lịch sử đăng ký Yihaotong']);
    //Lịch sử
    Route::get('record', 'v1.serve.Serve/getRecord')->option(['real_name' => 'Lịch sử sử dụng Yihaotong']);
    //Có mở in vận đơn điện tử không
    Route::get('dump_open', 'v1.serve.Export/dumpIsOpen')->name('dumpIsOpen')->option(['real_name' => 'Trạng thái bật in vận đơn điện tử Yihaotong']);
    //Lấy tất cả đơn vị vận chuyển
    Route::get('export_all', 'v1.serve.Export/getExportAll')->option(['real_name' => 'Lấy tất cả đơn vị vận chuyển Yihaotong']);
    //Lấy mẫu của đơn vị vận chuyển
    Route::get('export_temp', 'v1.serve.Export/getExportTemp')->option(['real_name' => 'Lấy mẫu đơn vị vận chuyển Yihaotong']);
    //Đổi mật khẩu
    Route::post('modify', 'v1.serve.Serve/modify')->option(['real_name' => 'Đổi mật khẩu Yihaotong']);
    //Đổi số điện thoại
    Route::post('update_phone', 'v1.serve.Serve/updatePhone')->option(['real_name' => 'Đổi số điện thoại Yihaotong']);
    //Form sửa cấu hình SMS
    Route::get('sms_config/edit_basics', 'v1.setting.SystemConfig/edit_basics')->option(['real_name' => 'Biểu mẫu sửa cấu hình SMS Yihaotong']);
    //Lưu dữ liệu cấu hình SMS
    Route::post('sms_config/save_basics', 'v1.setting.SystemConfig/save_basics')->option(['real_name' => 'Lưu dữ liệu cấu hình SMS Yihaotong']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'serve', 'mark_name' => 'Yihaotong']);
