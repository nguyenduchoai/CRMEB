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
 * Route liên quan Module người dùng
 */
Route::group('user', function () {

    /** Người dùng */
    Route::group(function () {
        //Route resource quản lý người dùng
        Route::resource('user', 'v1.user.User')->option([
            'real_name' => [
                'index' => 'Lấy danh sách người dùng',
                'create' => 'Lấy biểu mẫu người dùng',
                'save' => 'Lưu người dùng',
                'read' => 'Lấy chi tiết người dùng',
                'edit' => 'Lấy biểu mẫu sửa người dùng',
                'update' => 'Sửa người dùng',
                'delete' => 'Xóa người dùng'
            ]
        ]);
        //Lưu khi thêm người dùng
        Route::post('user/save', 'v1.user.User/save_info')->option(['real_name' => 'Thêm người dùng']);
        //Đồng bộ người dùng WeChat
        Route::get('user/syncUsers', 'v1.user.User/syncWechatUsers')->option(['real_name' => 'Đồng bộ người dùng WeChat']);
        //Thông tin người dùng
        Route::get('user/user_save_info/:uid', 'v1.user.User/userSaveInfo')->option(['real_name' => 'Thông tin khi thêm/sửa thông tin người dùng']);
        //Tặng hạng thành viên
        Route::get('give_level/:id', 'v1.user.User/give_level')->option(['real_name' => 'Tặng hạng người dùng']);
        //Thực hiện tặng hạng thành viên
        Route::put('save_give_level/:id', 'v1.user.User/save_give_level')->option(['real_name' => 'Thực hiện tặng hạng người dùng']);
        //Tặng thời hạn thành viên trả phí
        Route::get('give_level_time/:id', 'v1.user.User/give_level_time')->option(['real_name' => 'Tặng thời hạn thành viên trả phí']);
        //Thực hiện tặng thời hạn thành viên trả phí
        Route::put('save_give_level_time/:id', 'v1.user.User/save_give_level_time')->option(['real_name' => 'Thực hiện tặng thời hạn thành viên trả phí']);
        //Xóa hạng thành viên
        Route::delete('del_level/:id', 'v1.user.User/del_level')->option(['real_name' => 'Gỡ bỏ hạng người dùng']);
        //Sửa thông tin khác
        Route::get('edit_other/:id/:type', 'v1.user.User/edit_other')->option(['real_name' => 'Biểu mẫu sửa điểm thưởng và số dư']);
        //Sửa thông tin khác
        Route::put('update_other/:id', 'v1.user.User/update_other')->option(['real_name' => 'Sửa điểm thưởng và số dư']);
        //Sửa trạng thái người dùng
        Route::put('set_status/:status/:id', 'v1.user.User/set_status')->option(['real_name' => 'Sửa trạng thái người dùng']);
        //Lấy thông tin người dùng chỉ định
        Route::get('one_info/:id', 'v1.user.User/oneUserInfo')->option(['real_name' => 'Lấy thông tin người dùng chỉ định']);
        //Thiết lập nhóm thành viên
        Route::post('set_group', 'v1.user.User/set_group')->option(['real_name' => 'Biểu mẫu nhóm người dùng']);
        //Thực hiện thiết lập nhóm thành viên
        Route::put('save_set_group', 'v1.user.User/save_set_group')->option(['real_name' => 'Đặt nhóm người dùng']);
        //Thiết lập nhãn thành viên
        Route::post('set_label', 'v1.user.User/set_label')->option(['real_name' => 'Đặt nhãn người dùng']);
    })->option(['parent' => 'user', 'cate_name' => 'Người dùng']);

    /** Hạng người dùng */
    Route::group(function () {
        //Lấy form thêm hạng thành viên
        Route::get('user_level/create', 'v1.user.UserLevel/create')->option(['real_name' => 'Biểu mẫu thêm hạng người dùng']);
        //Thêm hoặc sửa hạng thành viên
        Route::post('user_level', 'v1.user.UserLevel/save')->option(['real_name' => 'Thêm hoặc sửa hạng người dùng']);
        //Chi tiết hạng
        Route::get('user_level/read/:id', 'v1.user.UserLevel/read')->option(['real_name' => 'Chi tiết hạng người dùng']);
        //Lấy danh sách vip đã cài đặt trong hệ thống
        Route::get('user_level/vip_list', 'v1.user.UserLevel/get_system_vip_list')->option(['real_name' => 'Lấy danh sách hạng người dùng do hệ thống thiết lập']);
        //Xóa hạng thành viên
        Route::put('user_level/delete/:id', 'v1.user.UserLevel/delete')->option(['real_name' => 'Xóa hạng người dùng']);
        //Thiết lập bật/tắt hiển thị cho một sản phẩm
        Route::put('user_level/set_show/:id/:is_show', 'v1.user.UserLevel/set_show')->option(['real_name' => 'Hiện/ẩn hạng người dùng']);
        //Sửa nhanh danh sách hạng
        Route::put('user_level/set_value/:id', 'v1.user.UserLevel/set_value')->option(['real_name' => 'Sửa nhanh danh sách hạng người dùng']);
        //Danh sách nhiệm vụ theo hạng
        Route::get('user_level/task/:level_id', 'v1.user.UserLevel/get_task_list')->option(['real_name' => 'Danh sách nhiệm vụ hạng người dùng']);
        //Sửa nhanh nhiệm vụ theo hạng
        Route::put('user_level/set_task/:id', 'v1.user.UserLevel/set_task_value')->option(['real_name' => 'Sửa nhanh nhiệm vụ hạng người dùng']);
        //Thiết lập hiện|ẩn nhiệm vụ theo hạng
        Route::put('user_level/set_task_show/:id/:is_show', 'v1.user.UserLevel/set_task_show')->option(['real_name' => 'Đặt hiện|ẩn nhiệm vụ hạng người dùng']);
        //Thiết lập có bắt buộc hoàn thành không
        Route::put('user_level/set_task_must/:id/:is_must', 'v1.user.UserLevel/set_task_must')->option(['real_name' => 'Đặt yêu cầu bắt buộc hoàn thành cho nhiệm vụ hạng người dùng']);
        //Form thêm nhiệm vụ theo hạng
        Route::get('user_level/create_task', 'v1.user.UserLevel/create_task')->option(['real_name' => 'Biểu mẫu thêm nhiệm vụ hạng người dùng']);
        //Lưu hoặc sửa nhiệm vụ
        Route::post('user_level/save_task', 'v1.user.UserLevel/save_task')->option(['real_name' => 'Lưu hoặc sửa nhiệm vụ hạng người dùng']);
        //Xóa nhiệm vụ
        Route::delete('user_level/delete_task/:id', 'v1.user.UserLevel/delete_task')->option(['real_name' => 'Xóa nhiệm vụ hạng người dùng']);
    })->option(['parent' => 'user', 'cate_name' => 'Hạng người dùng']);

    /** Nhóm người dùng */
    Route::group(function () {
        //Lấy danh sách nhóm người dùng
        Route::get('user_group/list', 'v1.user.UserGroup/index')->option(['real_name' => 'Lấy danh sách nhóm người dùng']);
        //Biểu mẫu thêm/sửa nhóm
        Route::get('user_group/add/:id', 'v1.user.UserGroup/add')->option(['real_name' => 'Biểu mẫu thêm/sửa nhóm']);
        //Lưu dữ liệu biểu mẫu nhóm
        Route::post('user_group/save', 'v1.user.UserGroup/save')->option(['real_name' => 'Lưu dữ liệu biểu mẫu nhóm']);
        //Xóa dữ liệu nhóm
        Route::delete('user_group/del/:id', 'v1.user.UserGroup/delete')->option(['real_name' => 'Xóa dữ liệu nhóm người dùng']);
    })->option(['parent' => 'user', 'cate_name' => 'Nhóm người dùng']);

    /** Nhãn người dùng */
    Route::group(function () {
        //Danh sách nhãn thành viên
        Route::get('user_label', 'v1.user.UserLabel/index')->option(['real_name' => 'Danh sách nhãn người dùng']);
        //Form thêm/sửa nhãn thành viên
        Route::get('user_label/add/:id', 'v1.user.UserLabel/add')->option(['real_name' => 'Biểu mẫu thêm hoặc sửa nhãn người dùng']);
        //Lưu dữ liệu form nhãn
        Route::post('user_label/save', 'v1.user.UserLabel/save')->option(['real_name' => 'Thêm hoặc sửa nhãn người dùng']);
        //Xóa nhãn thành viên
        Route::delete('user_label/del/:id', 'v1.user.UserLabel/delete')->option(['real_name' => 'Xóa nhãn người dùng']);
        //Lấy nhãn người dùng
        Route::get('label/:uid', 'v1.user.UserLabel/getUserLabel')->option(['real_name' => 'Lấy nhãn người dùng']);
        //Đặt và hủy nhãn người dùng
        Route::post('label/:uid', 'v1.user.UserLabel/setUserLabel')->option(['real_name' => 'Đặt và hủy nhãn người dùng']);
        //Thiết lập nhóm thành viên
        Route::put('save_set_label', 'v1.user.user/save_set_label')->option(['real_name' => 'Lưu nhãn người dùng']);
        //Danh mục nhãn
        Route::resource('user_label_cate', 'v1.user.UserLabelCate')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh mục nhãn',
                'create' => 'Lấy biểu mẫu danh mục nhãn',
                'save' => 'Lưu danh mục nhãn',
                'edit' => 'Lấy biểu mẫu sửa danh mục nhãn',
                'update' => 'Sửa danh mục nhãn',
                'delete' => 'Xóa danh mục nhãn'
            ]
        ]);
        Route::get('user_label_cate/all', 'v1.user.UserLabelCate/getAll')->option(['real_name' => 'Lấy tất cả danh mục nhãn người dùng']);
        //Danh sách dạng cây nhãn người dùng (danh mục)
        Route::get('user_tree_label', 'v1.user.UserLabel/tree_list')->option(['real_name' => 'Danh sách dạng cây nhãn người dùng (danh mục)']);
    })->option(['parent' => 'user', 'cate_name' => 'Nhãn người dùng']);

    /** Thành viên trả phí */
    Route::group(function () {
        //Resource danh sách lô thẻ thành viên
        Route::get('member_batch/index', 'v1.user.member.MemberCardBatch/index')->option(['real_name' => 'Danh sách lô thẻ thành viên']);
        //Thêm lô thẻ thành viên
        Route::post('member_batch/save/:id', 'v1.user.member.MemberCardBatch/save')->option(['real_name' => 'Thêm lô thẻ thành viên']);
        //Danh sách thẻ thành viên
        Route::get('member_card/index/:card_batch_id', 'v1.user.member.MemberCard/index')->option(['real_name' => 'Danh sách thẻ thành viên']);
        //Sửa trạng thái thẻ thành viên
        Route::get('member_card/set_status', 'v1.user.member.MemberCard/set_status')->option(['real_name' => 'Sửa trạng thái thẻ thành viên']);
        //Thao tác sửa một trường trong danh sách
        Route::get('member_batch/set_value/:id', 'v1.user.member.MemberCardBatch/set_value')->option(['real_name' => 'Sửa nhanh lô thẻ thành viên']);
        //Loại thành viên
        Route::get('member/ship', 'v1.user.member.MemberCard/member_ship')->option(['real_name' => 'Danh sách loại thành viên']);
        //Xóa loại thành viên
        Route::delete('member_ship/delete/:id', 'v1.user.member.MemberCard/delete')->option(['real_name' => 'Xóa loại thành viên']);
        //Sửa trạng thái loại thành viên
        Route::get('member_ship/set_ship_status', 'v1.user.member.MemberCard/set_ship_status')->option(['real_name' => 'Sửa trạng thái loại thành viên']);
        //Sửa loại thẻ thành viên
        Route::post('member_ship/save/:id', 'v1.user.member.MemberCard/ship_save')->option(['real_name' => 'Sửa loại thẻ thành viên']);
        //Mã QR đổi thẻ thành viên
        Route::get('member_scan', 'v1.user.member.MemberCardBatch/member_scan')->option(['real_name' => 'Mã QR đổi thẻ thành viên']);
        //Lịch sử thành viên
        Route::get('member/record', 'v1.user.member.MemberCard/member_record')->option(['real_name' => 'Lịch sử thành viên']);
        //Quyền lợi thành viên
        Route::get('member/right', 'v1.user.member.MemberCard/member_right')->option(['real_name' => 'Danh sách quyền lợi thành viên']);
        //Chỉnh sửa quyền lợi thành viên
        Route::post('member_right/save/:id', 'v1.user.member.MemberCard/right_save')->option(['real_name' => 'Chỉnh sửa quyền lợi thành viên']);
        //Thỏa thuận thành viên
        Route::post('member_agreement/save/:id', 'v1.user.member.MemberCardBatch/save_member_agreement')->option(['real_name' => 'Thỏa thuận thành viên']);
        //Lấy thỏa thuận thành viên
        Route::get('member/agreement', 'v1.user.member.MemberCardBatch/getAgreement')->option(['real_name' => 'Lấy thỏa thuận thành viên']);
    })->option(['parent' => 'user', 'cate_name' => 'Thành viên trả phí']);


    /** Hủy tài khoản người dùng */
    Route::group(function () {
        Route::get('cancel_list', 'v1.user.UserCancel/getCancelList')->option(['real_name' => 'Danh sách hủy tài khoản']);
        Route::post('cancel/set_mark', 'v1.user.UserCancel/setMark')->option(['real_name' => 'Ghi chú danh sách hủy tài khoản']);
        Route::get('cancel/agree/:id', 'v1.user.UserCancel/agreeCancel')->option(['real_name' => 'Đồng ý hủy tài khoản']);
        Route::get('cancel/refuse/:id', 'v1.user.UserCancel/refuseCancel')->option(['real_name' => 'Từ chối hủy tài khoản']);
    })->option(['parent' => 'user', 'cate_name' => 'Hủy tài khoản người dùng']);

    /** Quà tặng người mới */
    Route::group(function () {
        Route::get('new_gift', 'v1.user.User/getNewGift')->option(['real_name' => 'Lấy quà tặng người mới']);
        Route::post('new_gift/save', 'v1.user.User/saveNewGift')->option(['real_name' => 'Lưu quà tặng người mới']);
    })->option(['parent' => 'user', 'cate_name' => 'Quà tặng người mới']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'user', 'mark_name' => 'Quản lý người dùng']);
