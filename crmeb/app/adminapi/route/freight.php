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

/**
 * Route liên quan Quản lý cửa hàng
 */
Route::group('freight', function () {
    //Route resource đơn vị vận chuyển
    Route::resource('express', 'v1.freight.Express')->except(['read'])->name('ExpressResource')->option([
        'real_name' => [
            'index' => 'Lấy danh sách đơn vị vận chuyển',
            'create' => 'Lấy biểu mẫu đơn vị vận chuyển',
            'save' => 'Lưu đơn vị vận chuyển',
            'edit' => 'Lấy biểu mẫu sửa đơn vị vận chuyển',
            'update' => 'Sửa đơn vị vận chuyển',
            'delete' => 'Xóa đơn vị vận chuyển'
        ],
    ]);
    //Sửa trạng thái
    Route::put('express/set_status/:id/:status', 'v1.freight.Express/set_status')->option(['real_name' => 'Sửa trạng thái đơn vị vận chuyển']);
    //Đồng bộ đơn vị vận chuyển
    Route::get('express/sync_express', 'v1.freight.Express/syncExpress')->option(['real_name' => 'Đồng bộ đơn vị vận chuyển']);
    //Biểu mẫu sửa cấu hình vận chuyển
    Route::get('config/edit_basics', 'v1.setting.SystemConfig/edit_basics')->option(['real_name' => 'Biểu mẫu sửa cấu hình vận chuyển']);
    //Lưu dữ liệu cấu hình vận chuyển
    Route::post('config/save_basics', 'v1.setting.SystemConfig/save_basics')->option(['real_name' => 'Lưu dữ liệu cấu hình vận chuyển']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'freight', 'mark_name' => 'Quản lý vận chuyển']);
