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
 * Route liên quan tệp đính kèm
 */
Route::group('file', function () {
    //Danh sách tệp đính kèm
    Route::get('file', 'v1.file.SystemAttachment/index')->option(['real_name' => 'Danh sách ảnh đính kèm']);
    //Xóa ảnh và lịch sử dữ liệu
    Route::post('file/delete', 'v1.file.SystemAttachment/delete')->option(['real_name' => 'Xóa ảnh']);
    //Form phân loại di chuyển ảnh
    Route::get('file/move', 'v1.file.SystemAttachment/move')->option(['real_name' => 'Biểu mẫu chuyển danh mục ảnh']);
    //Chuyển danh mục ảnh
    Route::put('file/do_move', 'v1.file.SystemAttachment/moveImageCate')->option(['real_name' => 'Chuyển danh mục ảnh']);
    //Sửa tên ảnh
    Route::put('file/update/:id', 'v1.file.SystemAttachment/update')->option(['real_name' => 'Sửa tên ảnh']);
    //Tải lên ảnh
    Route::post('upload/[:upload_type]', 'v1.file.SystemAttachment/upload')->option(['real_name' => 'Tải lên ảnh']);
    //Route resource quản lý danh mục tệp đính kèm
    Route::resource('category', 'v1.file.SystemAttachmentCategory')->except(['read'])->option([
        'real_name' => [
            'index' => 'Lấy danh sách danh mục tệp đính kèm',
            'create' => 'Lấy biểu mẫu danh mục tệp đính kèm',
            'save' => 'Lưu danh mục tệp đính kèm',
            'edit' => 'Lấy biểu mẫu sửa danh mục tệp đính kèm',
            'update' => 'Sửa danh mục tệp đính kèm',
            'delete' => 'Xóa danh mục tệp đính kèm'
        ],

    ]);
    //Lấy loại tải lên
    Route::get('upload_type', 'v1.file.SystemAttachment/uploadType')->option(['real_name' => 'Loại tải lên']);
    //Tải lên video cục bộ theo phân đoạn
    Route::post('video_upload', 'v1.file.SystemAttachment/videoUpload')->option(['real_name' => 'Tải lên video cục bộ theo phân đoạn']);
    //Lưu dữ liệu video lưu trữ đám mây
    Route::post('video_data_save', 'v1.file.SystemAttachment/videoDataSave')->option(['real_name' => 'Lưu dữ liệu video lưu trữ đám mây']);
    //Lấy liên kết trang quét mã tải lên và tham số
    Route::get('scan_upload/qrcode', 'v1.file.SystemAttachment/scanUploadQrcode')->option(['real_name' => 'Liên kết trang tải lên bằng quét mã']);
    //Xóa token quét mã tải lên
    Route::delete('scan_upload/qrcode', 'v1.file.SystemAttachment/removeUploadQrcode')->option(['real_name' => 'Xóa liên kết trang tải lên bằng quét mã']);
    //Lấy dữ liệu ảnh tải lên bằng quét mã
    Route::get('scan_upload/image/:scan_token', 'v1.file.SystemAttachment/scanUploadImage')->option(['real_name' => 'Lấy dữ liệu ảnh tải lên bằng quét mã']);
    //Tải lên ảnh từ mạng
    Route::post('online_upload', 'v1.file.SystemAttachment/onlineUpload')->option(['real_name' => 'Tải lên ảnh từ mạng']);
})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'file', 'mark_name' => 'Quản lý tư liệu']);
