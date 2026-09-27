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
 * Route liên quan livestream
 */
Route::group('live', function () {

    /** Streamer */
    Route::group(function () {
        //Danh sách streamer
        Route::get('anchor/list', 'v1.marketing.live.LiveAnchor/list')->option(['real_name' => 'Danh sách streamer']);
        //Biểu mẫu thêm/sửa streamer
        Route::get('anchor/add/:id', 'v1.marketing.live.LiveAnchor/add')->option(['real_name' => 'Biểu mẫu thêm/sửa streamer']);
        //Lưu dữ liệu streamer
        Route::post('anchor/save', 'v1.marketing.live.LiveAnchor/save')->option(['real_name' => 'Lưu dữ liệu streamer']);
        //Xóa streamer
        Route::delete('anchor/del/:id', 'v1.marketing.live.LiveAnchor/delete')->option(['real_name' => 'Xóa streamer']);
        //Thiết lập hiện/ẩn
        Route::get('anchor/set_show/:id/:is_show', 'v1.marketing.live.LiveAnchor/setShow')->option(['real_name' => 'Đặt hiện/ẩn streamer']);
    })->option(['parent' => 'live', 'cate_name' => 'Streamer']);

    /** Sản phẩm livestream */
    Route::group(function () {
        //Danh sách sản phẩm livestream
        Route::get('goods/list', 'v1.marketing.live.LiveGoods/list')->option(['real_name' => 'Danh sách sản phẩm livestream']);
        //Tạo sản phẩm livestream
        Route::post('goods/create', 'v1.marketing.live.LiveGoods/create')->option(['real_name' => 'Tạo sản phẩm livestream']);
        //Thêm/sửa sản phẩm
        Route::post('goods/add', 'v1.marketing.live.LiveGoods/add')->option(['real_name' => 'Thêm/sửa sản phẩm livestream']);
        //Chi tiết sản phẩm
        Route::get('goods/detail/:id', 'v1.marketing.live.LiveGoods/detail')->option(['real_name' => 'Chi tiết sản phẩm livestream']);
        //Duyệt lại sản phẩm
        Route::get('goods/audit/:id', 'v1.marketing.live.LiveGoods/audit')->option(['real_name' => 'Gửi duyệt lại sản phẩm livestream']);
        //Rút lại yêu cầu duyệt sản phẩm
        Route::get('goods/resestAudit/:id', 'v1.marketing.live.LiveGoods/resetAudit')->option(['real_name' => 'Rút lại yêu cầu duyệt sản phẩm livestream']);
        //Xóa sản phẩm
        Route::delete('goods/del/:id', 'v1.marketing.live.LiveGoods/delete')->option(['real_name' => 'Xóa sản phẩm livestream']);
        //Thiết lập hiện/ẩn
        Route::get('goods/set_show/:id/:is_show', 'v1.marketing.live.liveGoods/setShow')->option(['real_name' => 'Đặt hiện/ẩn sản phẩm livestream']);
        //Đồng bộ trạng thái sản phẩm livestream
        Route::get('goods/syncGoods', 'v1.marketing.live.liveGoods/syncGoods')->option(['real_name' => 'Đồng bộ trạng thái sản phẩm livestream']);
    })->option(['parent' => 'live', 'cate_name' => 'Sản phẩm livestream']);

    /** Phòng streamer */
    Route::group(function () {
        //Danh sách phòng livestream
        Route::get('room/list', 'v1.marketing.live.LiveRoom/list')->option(['real_name' => 'Danh sách phòng livestream']);
        //Thêm phòng livestream
        Route::post('room/add', 'v1.marketing.live.LiveRoom/add')->option(['real_name' => 'Thêm phòng livestream']);
        //Chi tiết phòng livestream
        Route::get('room/detail/:id', 'v1.marketing.live.LiveRoom/detail')->option(['real_name' => 'Chi tiết phòng livestream']);
        //Thêm sản phẩm vào phòng livestream
        Route::post('room/add_goods', 'v1.marketing.live.LiveRoom/addGoods')->option(['real_name' => 'Thêm sản phẩm vào phòng livestream']);
        //Xóa livestream
        Route::delete('room/del/:id', 'v1.marketing.live.LiveRoom/delete')->option(['real_name' => 'Xóa phòng livestream']);
        //Thiết lập hiện/ẩn
        Route::get('room/set_show/:id/:is_show', 'v1.marketing.live.LiveRoom/setShow')->option(['real_name' => 'Đặt hiện/ẩn phòng livestream']);
        //Đồng bộ trạng thái phòng livestream
        Route::get('room/syncRoom', 'v1.marketing.live.LiveRoom/syncRoom')->option(['real_name' => 'Đồng bộ trạng thái phòng livestream']);
    })->option(['parent' => 'live', 'cate_name' => 'Phòng livestream']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'live', 'mark_name' => 'Quản lý livestream']);
