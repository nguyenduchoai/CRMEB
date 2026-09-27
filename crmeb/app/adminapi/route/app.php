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
 * Route liên quan Module ứng dụng
 */
Route::group('app', function () {

    /** OA WeChat */
    Route::group(function () {
        //Giá trị menu
        Route::get('wechat/menu', 'v1.application.wechat.menus/index')->option(['real_name' => 'Danh sách menu OA WeChat']);
        //Lưu menu
        Route::post('wechat/menu', 'v1.application.wechat.menus/save')->option(['real_name' => 'Lưu menu OA WeChat']);
        //Danh sách tin bài
        Route::get('wechat/news', 'v1.application.wechat.WechatNewsCategory/index')->option(['real_name' => 'Danh sách tin bài']);
        //Chi tiết
        Route::get('wechat/news/:id', 'v1.application.wechat.WechatNewsCategory/read')->option(['real_name' => 'Chi tiết tin bài']);
        //Lưu tin bài
        Route::post('wechat/news', 'v1.application.wechat.WechatNewsCategory/save')->option(['real_name' => 'Lưu tin bài']);
        //Xóa tin bài
        Route::delete('wechat/news/:id', 'v1.application.wechat.WechatNewsCategory/delete')->option(['real_name' => 'Xóa tin bài']);
        //Gửi tin bài
        Route::post('wechat/push', 'v1.application.wechat.WechatNewsCategory/push')->option(['real_name' => 'Gửi tin bài']);
        //Trả lời khi theo dõi
        Route::get('wechat/reply', 'v1.application.wechat.Reply/reply')->option(['real_name' => 'Trả lời khi theo dõi']);
        //Lấy mã QR trả lời khi theo dõi
        Route::get('wechat/code_reply/:id', 'v1.application.wechat.Reply/code_reply')->option(['real_name' => 'Lấy mã QR trả lời khi theo dõi']);
        //Danh sách trả lời theo từ khóa
        Route::get('wechat/keyword', 'v1.application.wechat.Reply/index')->option(['real_name' => 'Danh sách trả lời theo từ khóa']);
        //Chi tiết từ khóa
        Route::get('wechat/keyword/:id', 'v1.application.wechat.Reply/read')->option(['real_name' => 'Chi tiết trả lời theo từ khóa']);
        //Lưu sửa từ khóa
        Route::post('wechat/keyword/:id', 'v1.application.wechat.Reply/save')->option(['real_name' => 'Lưu trả lời theo từ khóa']);
        //Xóa từ khóa
        Route::delete('wechat/keyword/:id', 'v1.application.wechat.Reply/delete')->option(['real_name' => 'Xóa trả lời theo từ khóa']);
        //Sửa trạng thái từ khóa
        Route::put('wechat/keyword/set_status/:id/:status', 'v1.application.wechat.Reply/set_status')->option(['real_name' => 'Sửa trạng thái trả lời theo từ khóa']);
        //Đồng bộ tin nhắn mẫu WeChat bằng một cú nhấp
        Route::get('wechat/syncSubscribe', 'v1.application.wechat.WechatTemplate/syncSubscribe')->name('syncSubscribe')->option(['real_name' => 'Đồng bộ nhanh tin nhắn mẫu']);
    })->option(['parent' => 'app', 'cate_name' => 'OA WeChat']);

    /** Mini Program */
    Route::group(function () {
        //Đồng bộ nhanh tin nhắn đăng ký
        Route::get('routine/syncSubscribe', 'v1.application.routine.RoutineTemplate/syncSubscribe')->name('syncSubscribe')->option(['real_name' => 'Đồng bộ nhanh tin nhắn đăng ký']);
        //Tải dữ liệu trang mẫu Mini Program
        Route::get('routine/info', 'v1.application.routine.RoutineTemplate/getDownloadInfo')->option(['real_name' => 'Tải xuống dữ liệu trang Mini Program']);
        //Tải xuống mẫu Mini Program
        Route::post('routine/download', 'v1.application.routine.RoutineTemplate/downloadTemp')->option(['real_name' => 'Tải xuống mẫu Mini Program']);

        Route::get('routine/scheme_list', 'v1.application.routine.RoutineScheme/schemeList')->name('schemeList')->option(['real_name' => 'Danh sách liên kết ngoài Mini Program']);
        Route::get('routine/scheme_form/:id', 'v1.application.routine.RoutineScheme/schemeForm')->name('schemeForm')->option(['real_name' => 'Biểu mẫu thêm/sửa liên kết ngoài Mini Program']);
        Route::post('routine/scheme_save/:id', 'v1.application.routine.RoutineScheme/schemeSave')->name('schemeSave')->option(['real_name' => 'Lưu thêm/sửa liên kết ngoài Mini Program']);
        Route::delete('routine/scheme_del/:id', 'v1.application.routine.RoutineScheme/schemeDel')->name('schemeDel')->option(['real_name' => 'Xóa liên kết ngoài Mini Program']);


    })->option(['parent' => 'app', 'cate_name' => 'Mini Program']);

    /** Mã kênh OA WeChat */
    Route::group(function () {
        Route::get('wechat_qrcode/cate/list', 'v1.application.wechat.WechatQrcode/getCateList')->option(['real_name' => 'Danh sách danh mục mã kênh']);
        Route::get('wechat_qrcode/cate/create/:id', 'v1.application.wechat.WechatQrcode/createForm')->option(['real_name' => 'Biểu mẫu thêm/sửa danh mục mã kênh']);
        Route::post('wechat_qrcode/cate/save', 'v1.application.wechat.WechatQrcode/saveCate')->option(['real_name' => 'Lưu danh mục mã kênh']);
        Route::delete('wechat_qrcode/cate/del/:id', 'v1.application.wechat.WechatQrcode/delCate')->option(['real_name' => 'Xóa danh mục mã kênh']);
        Route::post('wechat_qrcode/save/:id', 'v1.application.wechat.WechatQrcode/saveQrcode')->option(['real_name' => 'Lưu mã kênh']);
        Route::get('wechat_qrcode/info/:id', 'v1.application.wechat.WechatQrcode/qrcodeInfo')->option(['real_name' => 'Chi tiết mã kênh']);
        Route::get('wechat_qrcode/list', 'v1.application.wechat.WechatQrcode/qrcodeList')->option(['real_name' => 'Danh sách mã kênh']);
        Route::delete('wechat_qrcode/del/:id', 'v1.application.wechat.WechatQrcode/delQrcode')->option(['real_name' => 'Xóa mã kênh']);
        Route::put('wechat_qrcode/set_status/:id/:status', 'v1.application.wechat.WechatQrcode/setStatus')->option(['real_name' => 'Chuyển trạng thái mã kênh']);
        Route::get('wechat_qrcode/user_list/:qid', 'v1.application.wechat.WechatQrcode/userList')->option(['real_name' => 'Danh sách người dùng mã kênh']);
        Route::get('wechat_qrcode/statistic/:qid', 'v1.application.wechat.WechatQrcode/qrcodeStatistic')->option(['real_name' => 'Thống kê mã kênh']);
    })->option(['parent' => 'app', 'cate_name' => 'Mã kênh OA WeChat']);

    /** Liên quan đến CSKH */
    Route::group(function () {
        //API phản hồi chăm sóc khách hàng
        Route::resource('feedback', 'v1.kefu.StoreServiceFeedback')->only(['index', 'delete', 'update', 'edit'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách phản hồi người dùng',
                'edit' => 'Lấy biểu mẫu sửa phản hồi người dùng',
                'update' => 'Sửa phản hồi người dùng',
                'delete' => 'Xóa phản hồi người dùng'
            ]
        ]);
        //API mẫu trả lời nhanh
        Route::resource('wechat/speechcraft', 'v1.kefu.StoreServiceSpeechcraft')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách câu trả lời mẫu CSKH',
                'create' => 'Lấy biểu mẫu câu trả lời mẫu CSKH',
                'save' => 'Lưu câu trả lời mẫu CSKH',
                'edit' => 'Lấy biểu mẫu sửa câu trả lời mẫu CSKH',
                'update' => 'Sửa câu trả lời mẫu CSKH',
                'delete' => 'Xóa câu trả lời mẫu CSKH'
            ]
        ]);
        //API danh mục mẫu trả lời nhanh
        Route::resource('wechat/speechcraftcate', 'v1.kefu.StoreServiceSpeechcraftCate')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách danh mục câu trả lời mẫu CSKH',
                'create' => 'Lấy biểu mẫu danh mục câu trả lời mẫu CSKH',
                'save' => 'Lưu danh mục câu trả lời mẫu CSKH',
                'edit' => 'Lấy biểu mẫu sửa danh mục câu trả lời mẫu CSKH',
                'update' => 'Sửa danh mục câu trả lời mẫu CSKH',
                'delete' => 'Xóa danh mục câu trả lời mẫu CSKH'
            ]
        ]);
        //Danh sách nhân viên CSKH
        Route::get('wechat/kefu', 'v1.kefu.StoreService/index')->option(['real_name' => 'Danh sách nhân viên CSKH']);
        //Đăng nhập CSKH
        Route::get('wechat/kefu/login/:id', 'v1.kefu.StoreService/keufLogin')->option(['real_name' => 'Đăng nhập CSKH']);
        //Danh sách chọn người dùng khi thêm CSKH
        Route::get('wechat/kefu/create', 'v1.kefu.StoreService/create')->option(['real_name' => 'Danh sách chọn người dùng khi thêm CSKH']);
        //Form thêm nhân viên chăm sóc khách hàng
        Route::get('wechat/kefu/add', 'v1.kefu.StoreService/add')->option(['real_name' => 'Biểu mẫu thêm nhân viên CSKH']);
        //Lưu dữ liệu mới tạo
        Route::post('wechat/kefu', 'v1.kefu.StoreService/save')->option(['real_name' => 'Thêm nhân viên CSKH']);
        //Form sửa nhân viên chăm sóc khách hàng
        Route::get('wechat/kefu/:id/edit', 'v1.kefu.StoreService/edit')->option(['real_name' => 'Biểu mẫu sửa nhân viên CSKH']);
        //Lưu dữ liệu đã sửa
        Route::put('wechat/kefu/:id', 'v1.kefu.StoreService/update')->option(['real_name' => 'Chỉnh sửa nhân viên CSKH']);
        //Xóa
        Route::delete('wechat/kefu/:id', 'v1.kefu.StoreService/delete')->option(['real_name' => 'Xóa nhân viên CSKH']);
        //Sửa trạng thái
        Route::put('wechat/kefu/set_status/:id/:status', 'v1.kefu.StoreService/set_status')->option(['real_name' => 'Sửa trạng thái nhân viên CSKH']);
        //Lịch sử trò chuyện
        Route::get('wechat/kefu/record/:id', 'v1.kefu.StoreService/chat_user')->option(['real_name' => 'Lịch sử trò chuyện']);
        //Xem cuộc trò chuyện
        Route::get('wechat/kefu/chat_list', 'v1.kefu.StoreService/chat_list')->option(['real_name' => 'Xem cuộc trò chuyện']);

        //Danh sách trả lời tự động CSKH
        Route::get('kefu/auto_reply/list', 'v1.kefu.StoreServiceAutoReply/autoReplyList')->option(['real_name' => 'Danh sách trả lời tự động CSKH']);
        //Biểu mẫu thêm/sửa trả lời tự động CSKH
        Route::get('kefu/auto_reply/form/:id', 'v1.kefu.StoreServiceAutoReply/autoReplyForm')->option(['real_name' => 'Biểu mẫu thêm/sửa trả lời tự động CSKH']);
        //Lưu thêm/sửa trả lời tự động CSKH
        Route::post('kefu/auto_reply/save/:id', 'v1.kefu.StoreServiceAutoReply/autoReplySave')->option(['real_name' => 'Lưu thêm/sửa trả lời tự động CSKH']);
        //Sửa trạng thái trả lời tự động CSKH
        Route::put('kefu/auto_reply/status/:id/:status', 'v1.kefu.StoreServiceAutoReply/autoReplyStatus')->option(['real_name' => 'Sửa trạng thái trả lời tự động CSKH']);
        //Xóa trả lời tự động CSKH
        Route::delete('kefu/auto_reply/del/:id', 'v1.kefu.StoreServiceAutoReply/autoReplyDel')->option(['real_name' => 'Xóa trả lời tự động CSKH']);

    })->option(['parent' => 'app', 'cate_name' => 'Liên quan đến CSKH']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'app', 'mark_name' => 'Mô-đun ứng dụng']);
