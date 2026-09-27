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
 * Route liên quan Quản lý CTV
 */
Route::group('agent', function () {

    /** Quản lý cộng tác viên */
    Route::group(function () {
        //Danh sách cộng tác viên
        Route::get('index', 'v1.agent.AgentManage/index')->option(['real_name' => 'Danh sách cộng tác viên']);
        //Sửa người giới thiệu
        Route::put('spread', 'v1.agent.AgentManage/editSpread')->option(['real_name' => 'Sửa người giới thiệu']);
        //Thống kê phần đầu
        Route::get('statistics', 'v1.agent.AgentManage/get_badge')->option(['real_name' => 'Thống kê phần đầu danh sách cộng tác viên']);
        //Danh sách người được giới thiệu
        Route::get('stair', 'v1.agent.AgentManage/get_stair_list')->option(['real_name' => 'Danh sách người được giới thiệu']);
        //Thống kê danh sách đơn hàng giới thiệu
        Route::get('stair/order', 'v1.agent.AgentManage/get_stair_order_list')->option(['real_name' => 'Danh sách đơn hàng giới thiệu']);
        //Gỡ bỏ người giới thiệu
        Route::put('stair/delete_spread/:uid', 'v1.agent.AgentManage/delete_spread')->option(['real_name' => 'Gỡ bỏ người giới thiệu']);
        //Hủy tư cách cộng tác viên
        Route::put('stair/delete_system_spread/:uid', 'v1.agent.AgentManage/delete_system_spread')->option(['real_name' => 'Hủy tư cách cộng tác viên']);
        //Xem mã QR giới thiệu OA WeChat
        Route::get('look_code', 'v1.agent.AgentManage/look_code')->option(['real_name' => 'Xem mã QR giới thiệu OA WeChat']);
        //Xem mã QR giới thiệu Mini Program
        Route::get('look_xcx_code', 'v1.agent.AgentManage/look_xcx_code')->option(['real_name' => 'Xem mã QR giới thiệu Mini Program']);
        //Xem mã QR giới thiệu H5
        Route::get('look_h5_code', 'v1.agent.AgentManage/look_h5_code')->option(['real_name' => 'Xem mã QR giới thiệu H5']);
    })->option(['parent' => 'agent', 'cate_name' => 'Quản lý cộng tác viên']);

    /** Cài đặt tiếp thị liên kết */
    Route::group(function () {
        //Form sửa cấu hình CTV
        Route::get('config/edit_basics', 'v1.setting.SystemConfig/edit_basics')->option(['real_name' => 'Biểu mẫu sửa cấu hình điểm thưởng']);
        //Lưu dữ liệu cấu hình CTV
        Route::post('config/save_basics', 'v1.setting.SystemConfig/save_basics')->option(['real_name' => 'Lưu dữ liệu cấu hình điểm thưởng']);
    })->option(['parent' => 'agent', 'cate_name' => 'Cài đặt tiếp thị liên kết']);

    /** Cấp độ CTV */
    Route::group(function () {
        //Route resource hạng CTV
        Route::resource('level', 'v1.agent.AgentLevel')->except(['read'])->name('AgentLevel')->option([
            'real_name' => [
                'index' => 'Lấy danh sách cấp độ CTV',
                'create' => 'Lấy biểu mẫu cấp độ CTV',
                'save' => 'Lưu cấp độ CTV',
                'edit' => 'Lấy biểu mẫu sửa cấp độ CTV',
                'update' => 'Chỉnh sửa cấp độ CTV',
                'delete' => 'Xóa cấp độ CTV'
            ]
        ]);
        //Sửa trạng thái cấp độ CTV
        Route::put('level/set_status/:id/:status', 'v1.agent.AgentLevel/set_status')->name('levelSetStatus')->option(['real_name' => 'Sửa trạng thái cấp độ CTV']);
        //Route resource nhiệm vụ hạng CTV
        Route::resource('level_task', 'v1.agent.AgentLevelTask')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách nhiệm vụ cấp độ CTV',
                'create' => 'Lấy biểu mẫu nhiệm vụ cấp độ CTV',
                'save' => 'Lưu nhiệm vụ cấp độ CTV',
                'edit' => 'Lấy biểu mẫu sửa nhiệm vụ cấp độ CTV',
                'update' => 'Sửa nhiệm vụ cấp độ CTV',
                'delete' => 'Xóa nhiệm vụ cấp độ CTV'
            ]
        ]);
        //Sửa trạng thái nhiệm vụ CTV
        Route::put('level_task/set_status/:id/:status', 'v1.agent.AgentLevelTask/set_status')->name('levelTaskSetStatus')->option(['real_name' => 'Sửa trạng thái nhiệm vụ cấp độ CTV']);
        //Lấy biểu mẫu tặng cấp độ CTV
        Route::get('get_level_form', 'v1.agent.AgentManage/getLevelForm')->name('getLevelForm')->option(['real_name' => 'Lấy biểu mẫu tặng cấp độ CTV']);
        //Tặng cấp độ CTV
        Route::post('give_level', 'v1.agent.AgentManage/giveAgentLevel')->name('giveAgentLevel')->option(['real_name' => 'Tặng cấp độ CTV']);
        //Form thiết lập số lượng hoàn thành nhiệm vụ
        Route::get('get_task_num_form/:id', 'v1.agent.AgentLevel/getTaskNumForm')->name('getTaskNumForm')->option(['real_name' => 'Lấy biểu mẫu số lượng nhiệm vụ hoàn thành']);
        //Đặt số lượng nhiệm vụ hoàn thành
        Route::post('set_task_num/:id', 'v1.agent.AgentLevel/setTaskNum')->name('setTaskNum')->option(['real_name' => 'Đặt số lượng nhiệm vụ hoàn thành']);
    })->option(['parent' => 'agent', 'cate_name' => 'Cấp độ CTV']);

    /** Đại lý khu vực */
    Route::group(function () {
        Route::get('division/list', 'v1.agent.Division/divisionList')->name('divisionList')->option(['real_name' => 'Danh sách đại lý khu vực']);//Danh sách đại lý khu vực/đại lý/nhân viên
        Route::get('division/down_list', 'v1.agent.Division/divisionDownList')->name('divisionDownList')->option(['real_name' => 'Danh sách cấp dưới']);//Danh sách cấp dưới
        Route::get('division/create/:uid', 'v1.agent.Division/divisionCreate')->name('divisionCreate')->option(['real_name' => 'Thêm đại lý khu vực']);//Thêm đại lý khu vực
        Route::post('division/save', 'v1.agent.Division/divisionSave')->name('divisionSave')->option(['real_name' => 'Lưu đại lý khu vực']);//Lưu đại lý khu vực
        Route::get('division/agent/create/:uid', 'v1.agent.Division/divisionAgentCreate')->name('divisionAgentCreate')->option(['real_name' => 'Thêm đại lý khu vực']);//Thêm đại lý
        Route::post('division/agent/save', 'v1.agent.Division/divisionAgentSave')->name('divisionAgentSave')->option(['real_name' => 'Lưu đại lý khu vực']);//Lưu đại lý
        Route::put('division/set_status/:status/:uid', 'v1.agent.Division/setDivisionStatus')->name('setDivisionStatus')->option(['real_name' => 'Chuyển trạng thái']);//Chuyển trạng thái
        Route::delete('division/del/:type/:uid', 'v1.agent.Division/delDivision')->name('delDivision')->option(['real_name' => 'Xóa đại lý']);//Chuyển trạng thái
        Route::get('division/staff/create/:uid', 'v1.agent.Division/divisionStaffCreate')->name('divisionStaffCreate')->option(['real_name' => 'Thêm đại lý khu vực']);//Thêm đại lý
        Route::post('division/staff/save', 'v1.agent.Division/divisionStaffSave')->name('divisionStaffSave')->option(['real_name' => 'Lưu đại lý khu vực']);//Lưu đại lý
        Route::get('division/agent_apply/list', 'v1.agent.Division/AdminApplyList')->name('AdminApplyList')->option(['real_name' => 'Danh sách đăng ký đại lý']);//Danh sách đăng ký đại lý
        Route::get('division/examine_apply/:id/:type', 'v1.agent.Division/examineApply')->name('examineApply')->option(['real_name' => 'Biểu mẫu duyệt']);//Biểu mẫu duyệt
        Route::post('division/apply_agent/save', 'v1.agent.Division/applyAgentSave')->name('applyAgentSave')->option(['real_name' => 'Gửi duyệt']);//Gửi duyệt
        Route::delete('division/del_apply/:id', 'v1.agent.Division/delApply')->name('delApply')->option(['real_name' => 'Xóa yêu cầu duyệt']);//Xóa yêu cầu duyệt
        Route::get('division/statistics', 'v1.agent.Division/divisionStatistics')->name('divisionStatistics')->option(['real_name' => 'Thống kê đại lý khu vực']);//Thống kê đại lý khu vực
    })->option(['parent' => 'agent', 'cate_name' => 'Đại lý khu vực']);

    /** Đơn đăng ký cộng tác viên */
    Route::group(function () {
        Route::get('spread/apply/list', 'v1.agent.SpreadApply/applyList')->name('applyList')->option(['real_name' => 'Danh sách đơn đăng ký cộng tác viên']);
        Route::post('spread/apply/examine/:id/:uid/:status', 'v1.agent.SpreadApply/applyExamine')->name('applyExamine')->option(['real_name' => 'Duyệt cộng tác viên']);
        Route::delete('spread/apply/del/:id', 'v1.agent.SpreadApply/applyDelete')->name('applyDelete')->option(['real_name' => 'Xóa đơn đăng ký cộng tác viên']);
    })->option(['parent' => 'agent', 'cate_name' => 'Đơn đăng ký cộng tác viên']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'agent', 'mark_name' => 'Mô-đun tiếp thị liên kết']);
