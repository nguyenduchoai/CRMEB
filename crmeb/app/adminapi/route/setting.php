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
 * Route liên quan Bảo trì cài đặt hệ thống - Quản lý quyền hệ thống, Quản lý menu hệ thống, Cấu hình hệ thống
 */
Route::group('setting', function () {

    /** Quản trị viên */
    Route::group(function () {
        //Route resource quản trị viên
        Route::resource('admin', 'v1.setting.SystemAdmin')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách quản trị viên',
                'create' => 'Lấy biểu mẫu quản trị viên',
                'save' => 'Lưu quản trị viên',
                'edit' => 'Lấy biểu mẫu sửa quản trị viên',
                'update' => 'Sửa quản trị viên',
                'delete' => 'Xóa quản trị viên'
            ]
        ]);
        //Đăng xuất
        Route::get('admin/logout', 'v1.setting.SystemAdmin/logout')->name('SystemAdminLogout')->option(['real_name' => 'Đăng xuất']);
        //Sửa trạng thái
        Route::put('set_status/:id/:status', 'v1.setting.SystemAdmin/set_status')->name('SystemAdminSetStatus')->option(['real_name' => 'Sửa trạng thái quản trị viên']);
        //Lấy thông tin quản trị viên hiện tại
        Route::get('info', 'v1.setting.SystemAdmin/info')->name('SystemAdminInfo')->option(['real_name' => 'Lấy thông tin quản trị viên hiện tại']);
        //Sửa thông tin quản trị viên hiện tại
        Route::put('update_admin', 'v1.setting.SystemAdmin/update_admin')->name('SystemAdminUpdateAdmin')->option(['real_name' => 'Sửa thông tin quản trị viên hiện tại']);
        //Thiết lập mật khẩu quản lý file
        Route::put('set_file_password', 'v1.setting.SystemAdmin/set_file_password')->name('SystemAdminSetFilePassword')->option(['real_name' => 'Đặt mật khẩu quản lý tệp hiện tại']);
    })->option(['parent' => 'setting', 'cate_name' => 'Quản trị viên']);

    /** Menu quyền */
    Route::group(function () {
        //Lấy quyền menu và mã định danh quyền
        Route::get('menus/unique', 'v1.setting.SystemMenus/unique')->name('SystemMenusUnique')->option(['real_name' => 'Lấy quyền menu và mã định danh quyền']);
        //Lưu quyền hàng loạt
        Route::post('menus/batch', 'v1.setting.SystemMenus/batchSave')->name('SystemMenusBatchSave')->option(['real_name' => 'Lưu quyền hàng loạt']);
        //Route resource quyền menu
        Route::resource('menus', 'v1.setting.SystemMenus')->option([
            'real_name' => [
                'index' => 'Lấy danh sách menu quyền',
                'create' => 'Lấy biểu mẫu menu quyền',
                'save' => 'Lưu menu quyền',
                'edit' => 'Lấy biểu mẫu sửa menu quyền',
                'read' => 'Xem thông tin menu quyền',
                'update' => 'Sửa menu quyền',
                'delete' => 'Xóa menu quyền'
            ],
        ]);
        //Danh sách quy tắc quyền chưa thêm
        Route::get('ruleList', 'v1.setting.SystemMenus/ruleList')->option(['real_name' => 'Danh sách quy tắc quyền']);
        //Danh mục quy tắc quyền
        Route::get('rule_cate', 'v1.setting.SystemMenus/ruleCate')->option(['real_name' => 'Danh mục quy tắc quyền']);
        //Sửa hiển thị
        Route::put('menus/show/:id', 'v1.setting.SystemMenus/show')->name('SystemMenusShow')->option(['real_name' => 'Sửa trạng thái hiển thị quy tắc quyền']);
    })->option(['parent' => 'setting', 'cate_name' => 'Menu quyền']);

    /** Vai trò quản trị viên */
    Route::group(function () {
        //Danh sách vai trò
        Route::get('role', 'v1.setting.SystemRole/index')->option(['real_name' => 'Danh sách vai trò quản trị viên']);
        //Danh sách quyền theo vai trò
        Route::get('role/create', 'v1.setting.SystemRole/create')->option(['real_name' => 'Danh sách quyền theo vai trò quản trị viên']);
        //Chi tiết sửa
        Route::get('role/:id/edit', 'v1.setting.SystemRole/edit')->option(['real_name' => 'Sửa chi tiết quản trị viên']);
        //Lưu khi tạo mới hoặc sửa
        Route::post('role/:id', 'v1.setting.SystemRole/save')->option(['real_name' => 'Tạo mới hoặc sửa quản trị viên']);
        //Sửa trạng thái vai trò
        Route::put('role/set_status/:id/:status', 'v1.setting.SystemRole/set_status')->option(['real_name' => 'Sửa trạng thái vai trò quản trị viên']);
        //Xóa vai trò
        Route::delete('role/:id', 'v1.setting.SystemRole/delete')->option(['real_name' => 'Xóa vai trò quản trị viên']);
    })->option(['parent' => 'setting', 'cate_name' => 'Vai trò quản trị viên']);

    /** Cấu hình hệ thống */
    Route::group(function () {
        //Route resource danh mục cấu hình
        Route::resource('config_class', 'v1.setting.SystemConfigTab')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách danh mục cấu hình hệ thống',
                'create' => 'Lấy biểu mẫu danh mục cấu hình hệ thống',
                'save' => 'Lưu danh mục cấu hình hệ thống',
                'edit' => 'Lấy biểu mẫu sửa danh mục cấu hình hệ thống',
                'update' => 'Sửa danh mục cấu hình hệ thống',
                'delete' => 'Xóa danh mục cấu hình hệ thống'
            ],
        ]);
        //Sửa trạng thái danh mục cấu hình
        Route::put('config_class/set_status/:id/:status', 'v1.setting.SystemConfigTab/set_status')->option(['real_name' => 'Sửa trạng thái danh mục cấu hình']);
        //Route resource cấu hình
        Route::resource('config', 'v1.setting.SystemConfig')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách cấu hình hệ thống',
                'create' => 'Lấy biểu mẫu cấu hình hệ thống',
                'save' => 'Lưu cấu hình hệ thống',
                'edit' => 'Lấy biểu mẫu sửa cấu hình hệ thống',
                'update' => 'Sửa cấu hình hệ thống',
                'delete' => 'Xóa cấu hình hệ thống'
            ]
        ]);
        //Sửa trạng thái cấu hình
        Route::put('config/set_status/:id/:status', 'v1.setting.SystemConfig/set_status')->option(['real_name' => 'Sửa trạng thái cấu hình']);
        //Biểu mẫu sửa cấu hình cơ bản
        Route::get('config/header_basics', 'v1.setting.SystemConfig/header_basics')->option(['real_name' => 'Dữ liệu tiêu đề sửa cấu hình cơ bản']);
        //Biểu mẫu sửa cấu hình cơ bản
        Route::get('config/edit_basics', 'v1.setting.SystemConfig/edit_basics')->option(['real_name' => 'Biểu mẫu sửa cấu hình cơ bản']);
        //Lưu dữ liệu cấu hình cơ bản
        Route::post('config/save_basics', 'v1.setting.SystemConfig/save_basics')->option(['real_name' => 'Lưu dữ liệu cấu hình cơ bản']);
        //Tải lên tệp cấu hình cơ bản
        Route::post('config/upload', 'v1.setting.SystemConfig/file_upload')->option(['real_name' => 'Tải lên tệp cấu hình cơ bản']);
        //Lấy giá trị của một cấu hình
        Route::get('config/get_system/:name', 'v1.setting.SystemConfig/get_system')->option(['real_name' => 'Biểu mẫu sửa cấu hình cơ bản']);
        //Lấy tất cả thông tin cấu hình trong một danh mục
        Route::get('config_list/:tabId', 'v1.setting.SystemConfig/get_config_list')->option(['real_name' => 'Lấy tất cả thông tin cấu hình trong một danh mục']);
    })->option(['parent' => 'setting', 'cate_name' => 'Cấu hình hệ thống']);

    /** Dữ liệu tổ hợp */
    Route::group(function () {
        //Route resource dữ liệu tổ hợp
        Route::resource('group', 'v1.setting.SystemGroup')->option([
            'real_name' => [
                'index' => 'Lấy danh sách dữ liệu tổ hợp',
                'create' => 'Lấy biểu mẫu dữ liệu tổ hợp',
                'save' => 'Lưu dữ liệu tổ hợp',
                'edit' => 'Lấy biểu mẫu sửa dữ liệu tổ hợp',
                'update' => 'Sửa dữ liệu tổ hợp',
                'delete' => 'Xóa dữ liệu tổ hợp'
            ]
        ]);
        //Tất cả dữ liệu tổ hợp
        Route::get('group_all', 'v1.setting.SystemGroup/getGroup')->option(['real_name' => 'Tất cả dữ liệu tổ hợp']);
        //Route resource dữ liệu con của dữ liệu tổ hợp
        Route::resource('group_data', 'v1.setting.SystemGroupData')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách dữ liệu con của dữ liệu tổ hợp',
                'create' => 'Lấy biểu mẫu dữ liệu con của dữ liệu tổ hợp',
                'save' => 'Lưu dữ liệu con của dữ liệu tổ hợp',
                'edit' => 'Lấy biểu mẫu sửa dữ liệu con của dữ liệu tổ hợp',
                'update' => 'Sửa dữ liệu con của dữ liệu tổ hợp',
                'delete' => 'Xóa dữ liệu con của dữ liệu tổ hợp'
            ]
        ]);
        //Sửa trạng thái dữ liệu
        Route::get('group_data/header', 'v1.setting.SystemGroupData/header')->option(['real_name' => 'Tiêu đề dữ liệu tổ hợp']);
        //Sửa trạng thái dữ liệu
        Route::put('group_data/set_status/:id/:status', 'v1.setting.SystemGroupData/set_status')->option(['real_name' => 'Sửa trạng thái dữ liệu tổ hợp']);
        //Lưu cấu hình dữ liệu
        Route::post('group_data/save_all', 'v1.setting.SystemGroupData/saveAll')->option(['real_name' => 'Gửi cấu hình dữ liệu']);
        //Lấy quảng cáo CSKH
        Route::get('get_kf_adv', 'v1.setting.SystemGroupData/getKfAdv')->option(['real_name' => 'Lấy quảng cáo CSKH']);
        //Đặt quảng cáo CSKH
        Route::post('set_kf_adv', 'v1.setting.SystemGroupData/setKfAdv')->option(['real_name' => 'Đặt quảng cáo CSKH']);
        //Resource cấu hình số ngày điểm danh
        Route::resource('sign_data', 'v1.setting.SystemGroupData')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách cấu hình số ngày điểm danh',
                'create' => 'Lấy biểu mẫu cấu hình số ngày điểm danh',
                'save' => 'Lưu cấu hình số ngày điểm danh',
                'edit' => 'Lấy biểu mẫu sửa cấu hình số ngày điểm danh',
                'update' => 'Sửa cấu hình số ngày điểm danh',
                'delete' => 'Xóa cấu hình số ngày điểm danh'
            ]
        ]);
        //Trường dữ liệu điểm danh
        Route::get('sign_data/header', 'v1.setting.SystemGroupData/header')->option(['real_name' => 'Tiêu đề dữ liệu điểm danh']);
        //Sửa trạng thái dữ liệu điểm danh
        Route::put('sign_data/set_status/:id/:status', 'v1.setting.SystemGroupData/set_status')->option(['real_name' => 'Sửa trạng thái dữ liệu điểm danh']);
        //Resource cấu hình ảnh động chi tiết đơn hàng
        Route::resource('order_data', 'v1.setting.SystemGroupData')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách ảnh động chi tiết đơn hàng',
                'create' => 'Lấy biểu mẫu ảnh động chi tiết đơn hàng',
                'save' => 'Lưu ảnh động chi tiết đơn hàng',
                'edit' => 'Lấy biểu mẫu sửa ảnh động chi tiết đơn hàng',
                'update' => 'Sửa ảnh động chi tiết đơn hàng',
                'delete' => 'Xóa ảnh động chi tiết đơn hàng'
            ]
        ]);
        //Trường dữ liệu đơn hàng
        Route::get('order_data/header', 'v1.setting.SystemGroupData/header')->option(['real_name' => 'Trường dữ liệu đơn hàng']);
        //Trạng thái dữ liệu đơn hàng
        Route::put('order_data/set_status/:id/:status', 'v1.setting.SystemGroupData/set_status')->option(['real_name' => 'Trạng thái dữ liệu đơn hàng']);
        //Resource cấu hình menu trang cá nhân
        Route::resource('usermenu_data', 'v1.setting.SystemGroupData')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách menu trang cá nhân',
                'create' => 'Lấy biểu mẫu menu trang cá nhân',
                'save' => 'Lưu menu trang cá nhân',
                'edit' => 'Lấy biểu mẫu sửa menu trang cá nhân',
                'update' => 'Sửa menu trang cá nhân',
                'delete' => 'Xóa menu trang cá nhân'
            ]
        ]);
        //Trường dữ liệu menu trang cá nhân
        Route::get('usermenu_data/header', 'v1.setting.SystemGroupData/header')->option(['real_name' => 'Trường dữ liệu menu trang cá nhân']);
        //Trạng thái dữ liệu menu trang cá nhân
        Route::put('usermenu_data/set_status/:id/:status', 'v1.setting.SystemGroupData/set_status')->option(['real_name' => 'Trạng thái dữ liệu menu trang cá nhân']);
        //Resource cấu hình poster chia sẻ
        Route::resource('poster_data', 'v1.setting.SystemGroupData')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách poster chia sẻ',
                'create' => 'Lấy biểu mẫu poster chia sẻ',
                'save' => 'Lưu poster chia sẻ',
                'edit' => 'Lấy biểu mẫu sửa poster chia sẻ',
                'update' => 'Sửa poster chia sẻ',
                'delete' => 'Xóa poster chia sẻ'
            ]
        ]);
        //Trường dữ liệu poster chia sẻ
        Route::get('poster_data/header', 'v1.setting.SystemGroupData/header')->option(['real_name' => 'Trường dữ liệu poster chia sẻ']);
        //Trạng thái dữ liệu poster chia sẻ
        Route::put('poster_data/set_status/:id/:status', 'v1.setting.SystemGroupData/set_status')->option(['real_name' => 'Trạng thái dữ liệu poster chia sẻ']);
        //Resource cấu hình flash sale
        Route::resource('seckill_data', 'v1.setting.SystemGroupData')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách cấu hình flash sale',
                'create' => 'Lấy biểu mẫu cấu hình flash sale',
                'save' => 'Lưu cấu hình flash sale',
                'edit' => 'Lấy biểu mẫu sửa cấu hình flash sale',
                'update' => 'Sửa cấu hình flash sale',
                'delete' => 'Xóa cấu hình flash sale'
            ]
        ]);
        //Trường dữ liệu flash sale
        Route::get('seckill_data/header', 'v1.setting.SystemGroupData/header')->option(['real_name' => 'Trường dữ liệu flash sale']);
        //Trạng thái dữ liệu flash sale
        Route::put('seckill_data/set_status/:id/:status', 'v1.setting.SystemGroupData/set_status')->option(['real_name' => 'Trạng thái dữ liệu flash sale']);
        //Lấy chính sách bảo mật
        Route::get('get_user_agreement', 'v1.setting.SystemGroupData/getUserAgreement')->option(['real_name' => 'Lấy chính sách bảo mật']);
        //Đặt chính sách bảo mật
        Route::post('set_user_agreement', 'v1.setting.SystemGroupData/setUserAgreement')->option(['real_name' => 'Đặt chính sách bảo mật']);
    })->option(['parent' => 'setting', 'cate_name' => 'Dữ liệu tổ hợp']);

    /** Dữ liệu thành phố */
    Route::group(function () {
        //Lấy danh sách đầy đủ dữ liệu thành phố
        Route::get('city/full_list', 'v1.setting.SystemCity/fullList')->option(['real_name' => 'Lấy danh sách đầy đủ dữ liệu thành phố']);
        //Lấy danh sách dữ liệu thành phố
        Route::get('city/list/:parent_id', 'v1.setting.SystemCity/index')->option(['real_name' => 'Lấy danh sách dữ liệu thành phố']);
        //Biểu mẫu thêm dữ liệu thành phố
        Route::get('city/add/:parent_id', 'v1.setting.SystemCity/add')->option(['real_name' => 'Biểu mẫu thêm dữ liệu thành phố']);
        //Biểu mẫu sửa dữ liệu thành phố
        Route::get('city/:id/edit', 'v1.setting.SystemCity/edit')->option(['real_name' => 'Biểu mẫu sửa dữ liệu thành phố']);
        //Thêm mới/sửa dữ liệu thành phố
        Route::post('city/save', 'v1.setting.SystemCity/save')->option(['real_name' => 'Thêm mới/sửa dữ liệu thành phố']);
        //Biểu mẫu sửa dữ liệu thành phố
        Route::delete('city/del/:city_id', 'v1.setting.SystemCity/delete')->option(['real_name' => 'Xóa dữ liệu thành phố']);
        //Xóa bộ nhớ đệm dữ liệu thành phố
        Route::get('city/clean_cache', 'v1.setting.SystemCity/clean_cache')->option(['real_name' => 'Xóa bộ nhớ đệm dữ liệu thành phố']);
    })->option(['parent' => 'setting', 'cate_name' => 'Dữ liệu thành phố']);

    /** Mẫu phí vận chuyển */
    Route::group(function () {
        //Danh sách mẫu phí vận chuyển
        Route::get('shipping_templates/list', 'v1.setting.ShippingTemplates/temp_list')->option(['real_name' => 'Danh sách mẫu phí vận chuyển']);
        //Sửa dữ liệu mẫu phí vận chuyển
        Route::get('shipping_templates/:id/edit', 'v1.setting.ShippingTemplates/edit')->option(['real_name' => 'Sửa dữ liệu mẫu phí vận chuyển']);
        //Lưu khi thêm mới hoặc sửa
        Route::post('shipping_templates/save/:id', 'v1.setting.ShippingTemplates/save')->option(['real_name' => 'Thêm mới hoặc sửa mẫu phí vận chuyển']);
        //Xóa mẫu phí vận chuyển
        Route::delete('shipping_templates/del/:id', 'v1.setting.ShippingTemplates/delete')->option(['real_name' => 'Xóa mẫu phí vận chuyển']);
        //API dữ liệu thành phố
        Route::get('shipping_templates/city_list', 'v1.setting.ShippingTemplates/city_list')->option(['real_name' => 'API dữ liệu thành phố']);
    })->option(['parent' => 'setting', 'cate_name' => 'Mẫu phí vận chuyển']);


    /** Thông báo hệ thống */
    Route::group(function () {
        //Danh sách thông báo hệ thống
        Route::get('notification/index', 'v1.setting.SystemNotification/index')->option(['real_name' => 'Danh sách thông báo hệ thống']);
        //Biểu mẫu thêm/sửa thông báo tùy chỉnh
        Route::get('notification/not_form/:id', 'v1.setting.SystemNotification/notForm')->option(['real_name' => 'Biểu mẫu thêm/sửa thông báo tùy chỉnh']);
        //Xóa thông báo tùy chỉnh
        Route::delete('notification/del_not/:id', 'v1.setting.SystemNotification/delNot')->option(['real_name' => 'Xóa thông báo tùy chỉnh']);
        //Lưu thông báo tùy chỉnh
        Route::post('notification/not_form_save/:id', 'v1.setting.SystemNotification/notFormSave')->option(['real_name' => 'Lưu thông báo tùy chỉnh']);
        //Lấy một dòng dữ liệu
        Route::get('notification/info', 'v1.setting.SystemNotification/info')->option(['real_name' => 'Lấy dữ liệu một thông báo']);
        //Lưu cài đặt thông báo
        Route::post('notification/save', 'v1.setting.SystemNotification/save')->option(['real_name' => 'Lưu cài đặt thông báo']);
        //Sửa trạng thái thông báo
        Route::put('notification/set_status/:type/:status/:id', 'v1.setting.SystemNotification/set_status')->option(['real_name' => 'Sửa trạng thái thông báo']);
    })->option(['parent' => 'setting', 'cate_name' => 'Thông báo hệ thống']);

    /** Thỏa thuận và bản quyền */
    Route::group(function () {
        //Cài đặt thỏa thuận
        Route::get('get_agreement/:type', 'v1.setting.SystemAgreement/getAgreement')->option(['real_name' => 'Lấy nội dung thỏa thuận']);
        Route::post('save_agreement', 'v1.setting.SystemAgreement/saveAgreement')->option(['real_name' => 'Đặt nội dung thỏa thuận']);
        //Lấy thông tin bản quyền
        Route::get('get_version', 'v1.setting.SystemConfig/getVersion')->option(['real_name' => 'Lấy thông tin bản quyền']);
    })->option(['parent' => 'setting', 'cate_name' => 'Thỏa thuận và bản quyền']);


    /** API bên ngoài */
    Route::group(function () {
        //Thông tin tài khoản API bên ngoài
        Route::get('system_out_account/index', 'v1.setting.SystemOutAccount/index')->option(['real_name' => 'Thông tin tài khoản API bên ngoài']);
        //Thêm tài khoản API bên ngoài
        Route::post('system_out_account/save', 'v1.setting.SystemOutAccount/save')->option(['real_name' => 'Thêm tài khoản API bên ngoài']);
        //Sửa tài khoản API bên ngoài
        Route::post('system_out_account/update/:id', 'v1.setting.SystemOutAccount/update')->option(['real_name' => 'Sửa tài khoản API bên ngoài']);
        //Đặt trạng thái vô hiệu hóa tài khoản
        Route::put('system_out_account/set_status/:id/:status', 'v1.setting.SystemOutAccount/set_status')->option(['real_name' => 'Đặt trạng thái vô hiệu hóa tài khoản']);
        //Cài đặt API đẩy dữ liệu cho tài khoản
        Route::put('system_out_account/set_up/:id', 'v1.setting.SystemOutAccount/outSetUpSave')->option(['real_name' => 'Cài đặt API đẩy dữ liệu cho tài khoản']);
        //Xóa tài khoản
        Route::delete('system_out_account/:id', 'v1.setting.SystemOutAccount/delete')->option(['real_name' => 'Xóa tài khoản']);
        //Kiểm thử API lấy token
        Route::post('system_out_account/text_out_url', 'v1.setting.SystemOutAccount/textOutUrl')->option(['real_name' => 'Kiểm thử API lấy token']);

        //Danh sách API bên ngoài
        Route::get('system_out_interface/list', 'v1.setting.SystemOutAccount/outInterfaceList')->option(['real_name' => 'Danh sách API bên ngoài']);
        //Thêm/sửa API bên ngoài
        Route::post('system_out_interface/save/:id', 'v1.setting.SystemOutAccount/saveInterface')->option(['real_name' => 'Thêm/sửa API bên ngoài']);
        //Thông tin API bên ngoài
        Route::get('system_out_interface/info/:id', 'v1.setting.SystemOutAccount/interfaceInfo')->option(['real_name' => 'Thông tin API bên ngoài']);
        //Sửa tên API
        Route::put('system_out_interface/edit_name', 'v1.setting.SystemOutAccount/editInterfaceName')->option(['real_name' => 'Sửa tên API']);
        //Xóa API
        Route::delete('system_out_interface/del/:id', 'v1.setting.SystemOutAccount/delInterface')->option(['real_name' => 'Xóa API']);
    })->option(['parent' => 'setting', 'cate_name' => 'API bên ngoài']);


    /** Đa ngôn ngữ */
    Route::group(function () {
        //Danh sách quốc gia theo ngôn ngữ
        Route::get('lang_country/list', 'v1.setting.LangCountry/langCountryList')->option(['real_name' => 'Danh sách quốc gia theo ngôn ngữ']);
        //Biểu mẫu thêm khu vực ngôn ngữ
        Route::get('lang_country/form/:id', 'v1.setting.LangCountry/langCountryForm')->option(['real_name' => 'Biểu mẫu thêm khu vực ngôn ngữ']);
        //Lưu khu vực ngôn ngữ
        Route::post('lang_country/save/:id', 'v1.setting.LangCountry/langCountrySave')->option(['real_name' => 'Lưu khu vực ngôn ngữ']);
        //Xóa khu vực ngôn ngữ
        Route::delete('lang_country/del/:id', 'v1.setting.LangCountry/langCountryDel')->option(['real_name' => 'Xóa khu vực ngôn ngữ']);
        //Danh sách loại ngôn ngữ
        Route::get('lang_type/list', 'v1.setting.LangType/langTypeList')->option(['real_name' => 'Danh sách loại ngôn ngữ']);
        //Biểu mẫu thêm/sửa loại ngôn ngữ
        Route::get('lang_type/form/:id', 'v1.setting.LangType/langTypeForm')->option(['real_name' => 'Biểu mẫu thêm/sửa loại ngôn ngữ']);
        //Lưu thêm/sửa ngôn ngữ
        Route::post('lang_type/save/:id', 'v1.setting.LangType/langTypeSave')->option(['real_name' => 'Lưu thêm/sửa ngôn ngữ']);
        //Xóa ngôn ngữ
        Route::delete('lang_type/del/:id', 'v1.setting.LangType/langTypeDel')->option(['real_name' => 'Xóa ngôn ngữ']);
        //Sửa trạng thái loại ngôn ngữ
        Route::put('lang_type/status/:id/:status', 'v1.setting.LangType/langTypeStatus')->option(['real_name' => 'Sửa trạng thái loại ngôn ngữ']);
        //Lấy danh sách ngôn ngữ
        Route::get('lang_code/list', 'v1.setting.LangCode/langCodeList')->option(['real_name' => 'Danh sách ngôn ngữ']);
        //Lấy thông tin ngôn ngữ
        Route::get('lang_code/info', 'v1.setting.LangCode/langCodeInfo')->option(['real_name' => 'Chi tiết ngôn ngữ']);
        //Lưu chỉnh sửa ngôn ngữ
        Route::post('lang_code/save', 'v1.setting.LangCode/langCodeSave')->option(['real_name' => 'Lưu chỉnh sửa ngôn ngữ']);
        //Xóa ngôn ngữ
        Route::delete('lang_code/del/:id', 'v1.setting.LangCode/langCodeDel')->option(['real_name' => 'Xóa ngôn ngữ']);
        //Dịch máy
        Route::post('lang_code/translate', 'v1.setting.LangCode/langCodeTranslate')->option(['real_name' => 'Dịch máy']);
    })->option(['parent' => 'setting', 'cate_name' => 'Đa ngôn ngữ']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'setting', 'mark_name' => 'Cài đặt hệ thống']);
