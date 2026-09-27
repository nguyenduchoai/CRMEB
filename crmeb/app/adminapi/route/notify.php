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
 * Route liên quan Quản lý thông báo, tin nhắn mẫu (danh sách, thông báo, thêm, sửa), SMS
 */
Route::group('notify', function () {
    //Lưu cấu hình đăng nhập
    Route::post('sms/config', 'v1.notification.sms.SmsConfig/save_basics')->option(['real_name' => 'Lưu cấu hình SMS']);
    //Lịch sử gửi SMS
    Route::get('sms/record', 'v1.notification.sms.SmsConfig/record')->option(['real_name' => 'Lịch sử gửi SMS']);
    //Dữ liệu tài khoản SMS
    Route::get('sms/data', 'v1.notification.sms.SmsConfig/data')->option(['real_name' => 'Dữ liệu tài khoản SMS']);
    //Kiểm tra đã đăng nhập chưa
    Route::get('sms/is_login', 'v1.notification.sms.SmsConfig/is_login')->option(['real_name' => 'Kiểm tra tài khoản SMS đã đăng nhập chưa']);
    //Kiểm tra đã đăng nhập chưa
    Route::get('sms/logout', 'v1.notification.sms.SmsConfig/logout')->option(['real_name' => 'Đăng xuất tài khoản SMS']);
    //Gửi mã xác thực SMS
    Route::post('sms/captcha', 'v1.notification.sms.SmsAdmin/captcha')->option(['real_name' => 'Gửi mã xác thực SMS']);
    //Sửa/đăng ký tài khoản nền tảng SMS
    Route::post('sms/register', 'v1.notification.sms.SmsAdmin/save')->option(['real_name' => 'Sửa hoặc đăng ký tài khoản nền tảng SMS']);
    //Danh sách mẫu SMS
    Route::get('sms/temp', 'v1.notification.sms.SmsTemplateApply/index')->option(['real_name' => 'Danh sách mẫu SMS']);
    //Biểu mẫu đăng ký mẫu SMS
    Route::get('sms/temp/create', 'v1.notification.sms.SmsTemplateApply/create')->option(['real_name' => 'Biểu mẫu đăng ký mẫu SMS']);
    //Đăng ký mẫu SMS
    Route::post('sms/temp', 'v1.notification.sms.SmsTemplateApply/save')->option(['real_name' => 'Đăng ký mẫu SMS']);
    //Danh sách mẫu SMS chung
    Route::get('sms/public_temp', 'v1.notification.sms.SmsPublicTemp/index')->option(['real_name' => 'Danh sách mẫu SMS chung']);
    //Số lượng còn lại
    Route::get('sms/number', 'v1.notification.sms.SmsPay/number')->option(['real_name' => 'Số tin SMS còn lại']);
    //Lấy gói thanh toán
    Route::get('sms/price', 'v1.notification.sms.SmsPay/price')->option(['real_name' => 'Lấy gói mua SMS']);
    //Lấy mã thanh toán
    Route::post('sms/pay_code', 'v1.notification.sms.SmsPay/pay')->option(['real_name' => 'Lấy mã thanh toán mua SMS']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'notify', 'mark_name' => 'Thông báo']);
