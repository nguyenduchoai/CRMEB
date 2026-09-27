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
 * Route liên quan Bảo trì
 */
Route::group('system', function () {

    /** Cấu hình lưu trữ */
    Route::group(function () {
        //Danh sách lưu trữ đám mây
        Route::get('config/storage/save_type/:type', 'v1.setting.SystemStorage/uploadType')->name('SystemStorageUploadType')->option(['real_name' => 'Chọn phương thức lưu trữ']);
        //Danh sách lưu trữ đám mây
        Route::get('config/storage', 'v1.setting.SystemStorage/index')->name('SystemStorageIndex')->option(['real_name' => 'Danh sách lưu trữ đám mây']);
        //Lấy biểu mẫu tạo lưu trữ đám mây
        Route::get('config/storage/create/:type', 'v1.setting.SystemStorage/create')->name('SystemStorageCreate')->option(['real_name' => 'Lấy biểu mẫu tạo lưu trữ đám mây']);
        //Lấy biểu mẫu cấu hình lưu trữ đám mây
        Route::get('config/storage/form/:type', 'v1.setting.SystemStorage/getConfigForm')->name('getConfigForm')->option(['real_name' => 'Lấy biểu mẫu cấu hình lưu trữ đám mây']);
        //Lấy cấu hình lưu trữ đám mây
        Route::get('config/storage/config', 'v1.setting.SystemStorage/getConfig')->name('SystemStorageConfig')->option(['real_name' => 'Lấy cấu hình lưu trữ đám mây']);
        //Lưu cấu hình lưu trữ đám mây
        Route::post('config/storage/config', 'v1.setting.SystemStorage/saveConfig')->name('SystemStorageSaveConfig')->option(['real_name' => 'Lưu cấu hình lưu trữ đám mây']);
        //Đồng bộ danh sách lưu trữ đám mây
        Route::put('config/storage/synch/:type', 'v1.setting.SystemStorage/synch')->name('SystemStorageSynch')->option(['real_name' => 'Đồng bộ danh sách lưu trữ đám mây']);
        //Lấy biểu mẫu sửa tên miền lưu trữ đám mây
        Route::get('config/storage/domain/:id', 'v1.setting.SystemStorage/getUpdateDomainForm')->name('getUpdateDomainForm')->option(['real_name' => 'Lấy biểu mẫu sửa tên miền lưu trữ đám mây']);
        //Sửa tên miền lưu trữ đám mây
        Route::post('config/storage/domain/:id', 'v1.setting.SystemStorage/updateDomain')->name('updateDomain')->option(['real_name' => 'Sửa tên miền lưu trữ đám mây']);
        //Lưu dữ liệu lưu trữ đám mây
        Route::post('config/storage/:type', 'v1.setting.SystemStorage/save')->name('SystemStorageSave')->option(['real_name' => 'Lưu dữ liệu lưu trữ đám mây']);
        //Xóa lưu trữ đám mây
        Route::delete('config/storage/:id', 'v1.setting.SystemStorage/delete')->name('SystemStorageDelete')->option(['real_name' => 'Xóa lưu trữ đám mây']);
        //Sửa trạng thái lưu trữ đám mây
        Route::put('config/storage/status/:id', 'v1.setting.SystemStorage/status')->name('SystemStorageStatus')->option(['real_name' => 'Sửa trạng thái lưu trữ đám mây']);
    })->option(['parent' => 'system', 'cate_name' => 'Cấu hình lưu trữ']);

    /** Nhật ký hệ thống */
    Route::group(function () {
        //Nhật ký hệ thống
        Route::get('log', 'v1.system.SystemLog/index')->name('SystemLog')->option(['real_name' => 'Nhật ký hệ thống']);
        //Điều kiện tìm kiếm quản trị viên trong nhật ký hệ thống
        Route::get('log/search_admin', 'v1.system.SystemLog/search_admin')->option(['real_name' => 'Điều kiện tìm kiếm quản trị viên trong nhật ký hệ thống']);
        //Kiểm tra tệp
        Route::get('file', 'v1.system.SystemFile/index')->name('SystemFile')->option(['real_name' => 'Kiểm tra tệp']);
    })->option(['parent' => 'system', 'cate_name' => 'Nhật ký hệ thống']);


    /** Sao lưu dữ liệu */
    Route::group(function () {
        //Tất cả bảng dữ liệu
        Route::get('backup', 'v1.system.SystemDatabackup/index')->option(['real_name' => 'Tất cả bảng cơ sở dữ liệu']);
        //Chi tiết sao lưu dữ liệu
        Route::get('backup/read', 'v1.system.SystemDatabackup/read')->option(['real_name' => 'Chi tiết sao lưu dữ liệu']);
        //Cập nhật ghi chú bảng dữ liệu hoặc trường của bảng
        Route::post('database/update_mark', 'v1.system.SystemDatabackup/updateMark')->option(['real_name' => 'Cập nhật ghi chú bảng dữ liệu hoặc trường của bảng']);
        //Backup dữ liệu - Tối ưu bảng
        Route::put('backup/optimize', 'v1.system.SystemDatabackup/optimize')->option(['real_name' => 'Tối ưu bảng dữ liệu']);
        //Backup dữ liệu - Sửa bảng
        Route::put('backup/repair', 'v1.system.SystemDatabackup/repair')->option(['real_name' => 'Sửa chữa bảng dữ liệu']);
        //Backup dữ liệu - Backup bảng
        Route::put('backup/backup', 'v1.system.SystemDatabackup/backup')->option(['real_name' => 'Sao lưu bảng dữ liệu']);
        //Lịch sử backup
        Route::get('backup/file_list', 'v1.system.SystemDatabackup/fileList')->option(['real_name' => 'Lịch sử sao lưu cơ sở dữ liệu']);
        //Xóa lịch sử backup
        Route::delete('backup/del_file', 'v1.system.SystemDatabackup/delFile')->option(['real_name' => 'Xóa bản sao lưu cơ sở dữ liệu']);
        //Nhập bảng lịch sử backup
        Route::post('backup/import', 'v1.system.SystemDatabackup/import')->option(['real_name' => 'Nhập bản sao lưu cơ sở dữ liệu']);
        //Tải bảng lịch sử backup
        //Route::get('backup/download', 'v1.system.SystemDatabackup/downloadFile');
    })->option(['parent' => 'system', 'cate_name' => 'Sao lưu dữ liệu']);

    /** Xóa dữ liệu */
    Route::group(function () {
        //Xóa dữ liệu người dùng
        Route::get('clear/:type', 'v1.system.SystemClearData/index')->option(['real_name' => 'Xóa dữ liệu người dùng']);
        //Xóa bộ nhớ đệm
        Route::get('refresh_cache/cache', 'v1.system.Clear/refresh_cache')->option(['real_name' => 'Xóa bộ nhớ đệm hệ thống']);
        //Xóa nhật ký
        Route::get('refresh_cache/log', 'v1.system.Clear/delete_log')->option(['real_name' => 'Xóa nhật ký hệ thống']);
        //API thay thế tên miền
        Route::post('replace_site_url', 'v1.system.SystemClearData/replaceSiteUrl')->option(['real_name' => 'Thay thế tên miền']);
        //Lấy danh sách phiên bản APP
        Route::get('version_list', 'v1.system.AppVersion/list')->option(['real_name' => 'Lấy danh sách phiên bản APP']);
        //Thêm thông tin phiên bản
        Route::get('version_crate/:id', 'v1.system.AppVersion/crate')->option(['real_name' => 'Thêm phiên bản']);
        //Thêm thông tin phiên bản
        Route::post('version_save', 'v1.system.AppVersion/save')->option(['real_name' => 'Lưu phiên bản']);
        //Xóa thông tin phiên bản
        Route::delete('version_del/:id', 'v1.system.AppVersion/del')->option(['real_name' => 'Xóa phiên bản']);
    })->option(['parent' => 'system', 'cate_name' => 'Xóa dữ liệu']);

    /** Nâng cấp trực tuyến */
    Route::group(function () {
        //Trạng thái nâng cấp
        Route::get('upgrade_status', 'UpgradeController/upgradeStatus')->option(['real_name' => 'Trạng thái nâng cấp']);
        //Danh sách gói nâng cấp
        Route::get('upgrade/list', 'UpgradeController/upgradeList')->option(['real_name' => 'Danh sách gói nâng cấp']);
        //Danh sách gói có thể nâng cấp
        Route::get('upgradeable/list', 'UpgradeController/upgradeableList')->option(['real_name' => 'Danh sách gói có thể nâng cấp']);
        //Thỏa thuận nâng cấp
        Route::get('upgrade/agreement', 'UpgradeController/agreement')->option(['real_name' => 'Thỏa thuận nâng cấp']);
        //Lịch sử nâng cấp
        Route::get('upgrade_log/list', 'UpgradeController/upgradeLogList')->option(['real_name' => 'Lịch sử nâng cấp']);
        //Kiểm tra tệp
        Route::get('upgrade/check_file', 'UpgradeController/checkFile')->option(['real_name' => 'Kiểm tra tệp']);
        //Thực thi lại
        Route::get('upgrade/reExecute', 'UpgradeController/reExecute')->option(['real_name' => 'Thực thi lại']);
        //Tải xuống gói nâng cấp
        Route::post('package_download/:package_key', 'UpgradeController/packageDownload')->option(['real_name' => 'Tải xuống gói nâng cấp']);
        //Tiến độ tải xuống gói nâng cấp
        Route::get('upgrade_download/progress', 'UpgradeController/downloadProgress')->option(['real_name' => 'Tiến độ tải xuống gói nâng cấp']);
        //Tiến trình nâng cấp
        Route::get('upgrade_progress', 'UpgradeController/progress')->option(['real_name' => 'Tiến trình nâng cấp']);
        //Xuất dự án backup
        Route::get('upgrade_export/:id/:type', 'UpgradeController/export')->option(['real_name' => 'Xuất bản sao lưu']);

        // API nâng cấp vượt phiên bản
        //Lấy tổng quan nâng cấp vượt phiên bản
        Route::get('cross_version/overview', 'UpgradeController/crossVersionOverview')->option(['real_name' => 'Tổng quan nâng cấp vượt phiên bản']);
        //Lấy danh sách phiên bản chờ nâng cấp
        Route::get('cross_version/pending', 'UpgradeController/pendingVersions')->option(['real_name' => 'Danh sách phiên bản chờ nâng cấp']);
        //Lấy SQL nâng cấp chờ thực thi
        Route::get('cross_version/pending_sql', 'UpgradeController/pendingUpgradeSql')->option(['real_name' => 'Danh sách SQL chờ thực thi']);
        //Thực hiện nâng cấp vượt phiên bản (từng bước)
        Route::post('cross_version/execute', 'UpgradeController/executeCrossVersionUpgrade')->option(['real_name' => 'Thực hiện nâng cấp vượt phiên bản']);
        //Thực thi toàn bộ nâng cấp vượt phiên bản bằng một cú nhấp
        Route::post('cross_version/execute_all', 'UpgradeController/executeAllCrossVersionUpgrade')->option(['real_name' => 'Nâng cấp bằng một cú nhấp']);
        //Kiểm tra có cần nâng cấp vượt phiên bản không
        Route::get('cross_version/check', 'UpgradeController/checkCrossVersionUpgrade')->option(['real_name' => 'Kiểm tra nâng cấp vượt phiên bản']);
        //Lấy trạng thái sao lưu
        Route::get('cross_version/backup_status', 'UpgradeController/backupStatus')->option(['real_name' => 'Trạng thái sao lưu']);
        //Lấy tiến độ nâng cấp
        Route::get('cross_version/progress', 'UpgradeController/upgradeProgress')->option(['real_name' => 'Tiến trình nâng cấp']);
        //Lấy danh sách phiên bản có thể rollback
        Route::get('rollback/versions', 'UpgradeController/rollbackVersions')->option(['real_name' => 'Danh sách phiên bản có thể quay lại']);
        //Thực hiện quay lại phiên bản
        Route::post('rollback/execute', 'UpgradeController/executeRollback')->option(['real_name' => 'Thực hiện quay lại phiên bản']);
    })->option(['parent' => 'system', 'cate_name' => 'Nâng cấp trực tuyến']);

    /** Tác vụ định kỳ */
    Route::group(function () {
        //Danh sách tác vụ định kỳ
        Route::get('crontab/list', 'v1.system.SystemCrontab/getTimerList')->option(['real_name' => 'Danh sách tác vụ định kỳ']);
        //Loại tác vụ định kỳ
        Route::get('crontab/mark', 'v1.system.SystemCrontab/getMarkList')->option(['real_name' => 'Loại tác vụ định kỳ']);
        //Chi tiết tác vụ định kỳ
        Route::get('crontab/info/:id', 'v1.system.SystemCrontab/getTimerInfo')->option(['real_name' => 'Chi tiết tác vụ định kỳ']);
        //Thêm/sửa tác vụ định kỳ
        Route::post('crontab/save', 'v1.system.SystemCrontab/saveTimer')->option(['real_name' => 'Thêm/sửa tác vụ định kỳ']);
        //Xóa tác vụ định kỳ
        Route::delete('crontab/del/:id', 'v1.system.SystemCrontab/delTimer')->option(['real_name' => 'Xóa tác vụ định kỳ']);
        //Công tắc bật/tắt tác vụ định kỳ
        Route::get('crontab/set_open/:id/:is_open', 'v1.system.SystemCrontab/setTimerStatus')->option(['real_name' => 'Công tắc bật/tắt tác vụ định kỳ']);
    })->option(['parent' => 'system', 'cate_name' => 'Tác vụ định kỳ']);

    /** Sự kiện tùy chỉnh */
    Route::group(function () {
        //Danh sách tác vụ định kỳ
        Route::get('event/list', 'v1.system.SystemEvent/getEventList')->option(['real_name' => 'Danh sách sự kiện tùy chỉnh']);
        //Loại tác vụ định kỳ
        Route::get('event/mark', 'v1.system.SystemEvent/getMarkList')->option(['real_name' => 'Loại sự kiện tùy chỉnh']);
        //Chi tiết tác vụ định kỳ
        Route::get('event/info/:id', 'v1.system.SystemEvent/getEventInfo')->option(['real_name' => 'Chi tiết sự kiện tùy chỉnh']);
        //Thêm/sửa tác vụ định kỳ
        Route::post('event/save', 'v1.system.SystemEvent/saveEvent')->option(['real_name' => 'Thêm/sửa sự kiện tùy chỉnh']);
        //Xóa tác vụ định kỳ
        Route::delete('event/del/:id', 'v1.system.SystemEvent/delEvent')->option(['real_name' => 'Xóa sự kiện tùy chỉnh']);
        //Công tắc bật/tắt tác vụ định kỳ
        Route::get('event/set_open/:id/:is_open', 'v1.system.SystemEvent/setEventStatus')->option(['real_name' => 'Công tắc bật/tắt sự kiện tùy chỉnh']);
    })->option(['parent' => 'system', 'cate_name' => 'Sự kiện tùy chỉnh']);

    /** Route hệ thống */
    Route::group(function () {
        //API đồng bộ route
        Route::get('route/sync_route/[:appName]', 'v1.setting.SystemRoute/syncRoute')->option(['real_name' => 'Đồng bộ route']);
        //Lấy dữ liệu dòng dạng tree của route
        Route::get('route/tree', 'v1.setting.SystemRoute/tree')->option(['real_name' => 'Lấy route dạng tree']);
        //Route quyền
        Route::delete('route/:id', 'v1.setting.SystemRoute/delete')->option(['real_name' => 'Xóa quyền route']);
        //Xem quyền route
        Route::get('route/:id', 'v1.setting.SystemRoute/read')->option(['real_name' => 'Xem quyền route']);
        //Lưu quyền route
        Route::post('route/:id', 'v1.setting.SystemRoute/save')->option(['real_name' => 'Lưu quyền route']);
        //Danh mục route
        Route::resource('route_cate', 'v1.setting.SystemRouteCate')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách danh mục route',
                'create' => 'Lấy biểu mẫu tạo danh mục route',
                'save' => 'Lưu danh mục route',
                'edit' => 'Lấy biểu mẫu sửa danh mục route',
                'update' => 'Sửa danh mục route',
                'delete' => 'Xóa danh mục route'
            ],
        ]);
    })->option(['parent' => 'system', 'cate_name' => 'Route hệ thống']);

    /** Tạo mã nguồn */
    Route::group(function () {
        //Lưu tệp CRUD đã sửa
        Route::post('crud/save_file/:id', 'v1.setting.SystemCrud/savefile')->option(['real_name' => 'Lưu tệp CRUD đã sửa']);
        //Lấy cấu hình CRUD
        Route::get('crud/config/:tableName', 'v1.setting.SystemCrud/getRouteList')->option(['real_name' => 'Lấy cấu hình CRUD']);
        //Tải xuống tệp đã tạo
        Route::get('crud/download/:id', 'v1.setting.SystemCrud/download')->option(['real_name' => 'Tải xuống tệp đã tạo']);
        //Lấy danh sách CRUD
        Route::get('crud/column_type', 'v1.setting.SystemCrud/columnType')->option(['real_name' => 'Lấy danh sách CRUD']);
        //Lấy dữ liệu menu dạng TREE
        Route::get('crud/menus', 'v1.setting.SystemCrud/getMenus')->option(['real_name' => 'Lấy dữ liệu menu dạng TREE']);
        //Lấy vị trí lưu tệp CRUD
        Route::post('crud/file_path', 'v1.setting.SystemCrud/getFilePath')->option(['real_name' => 'Lấy vị trí lưu tệp CRUD']);

        //Lấy danh sách từ điển dữ liệu
        Route::get('crud/data_dictionary_list', 'v1.setting.SystemCrud/dataDictionaryList')->option(['real_name' => 'Lấy danh sách từ điển dữ liệu']);
        //Lấy biểu mẫu thêm/sửa từ điển dữ liệu
        Route::get('crud/data_dictionary_list/create/:id', 'v1.setting.SystemCrud/dataDictionaryListCreate')->option(['real_name' => 'Lấy biểu mẫu thêm/sửa từ điển dữ liệu']);
        //Lưu từ điển dữ liệu
        Route::post('crud/data_dictionary_list/save/:id', 'v1.setting.SystemCrud/dataDictionaryListSave')->option(['real_name' => 'Lưu từ điển dữ liệu']);
        //Xóa từ điển dữ liệu
        Route::delete('crud/data_dictionary_list/del/:id', 'v1.setting.SystemCrud/dataDictionaryListDel')->option(['real_name' => 'Xóa từ điển dữ liệu']);
        //Xem danh sách nội dung từ điển dữ liệu
        Route::get('crud/data_dictionary/info_list/:cid', 'v1.setting.SystemCrud/dataDictionaryInfoList')->option(['real_name' => 'Xem danh sách nội dung từ điển dữ liệu']);
        //Xem biểu mẫu thêm/sửa nội dung từ điển dữ liệu
        Route::get('crud/data_dictionary/info_create/:cid/:id/:pid', 'v1.setting.SystemCrud/dataDictionaryInfoCreate')->option(['real_name' => 'Xem biểu mẫu thêm/sửa nội dung từ điển dữ liệu']);
        //Sửa hoặc lưu nội dung dữ liệu từ điển
        Route::post('crud/data_dictionary/info_save/:cid/:id', 'v1.setting.SystemCrud/dataDictionaryInfoSave')->option(['real_name' => 'Sửa hoặc lưu nội dung dữ liệu từ điển']);
        //Xóa nội dung từ điển dữ liệu
        Route::delete('crud/data_dictionary/info_del/:id', 'v1.setting.SystemCrud/dataDictionaryInfoDel')->option(['real_name' => 'Xóa nội dung từ điển dữ liệu']);

        //Lấy danh sách từ điển dữ liệu
        Route::get('crud/data_dictionary', 'v1.setting.SystemCrud/getDataDictionary')->option(['real_name' => 'Lấy danh sách từ điển dữ liệu']);
        //Xem từ điển dữ liệu
        Route::get('crud/data_dictionary/:id', 'v1.setting.SystemCrud/getDataDictionaryOne')->option(['real_name' => 'Xem từ điển dữ liệu']);
        //Sửa hoặc lưu dữ liệu từ điển
        Route::post('crud/data_dictionary/[:id]', 'v1.setting.SystemCrud/saveDataDictionary')->option(['real_name' => 'Sửa hoặc lưu dữ liệu từ điển']);
        //Xóa từ điển dữ liệu
        Route::delete('crud/data_dictionary/:id', 'v1.setting.SystemCrud/deleteDataDictionary')->option(['real_name' => 'Xóa từ điển dữ liệu']);
        //Lấy tên các bảng có thể liên kết
        Route::get('crud/association_table', 'v1.setting.SystemCrud/getAssociationTable')->option(['real_name' => 'Lấy tên các bảng có thể liên kết']);
        //Lấy thông tin chi tiết của bảng
        Route::get('crud/association_table/:tableName', 'v1.setting.SystemCrud/getAssociationTableInfo')->option(['real_name' => 'Lấy thông tin chi tiết của bảng']);
        //Xóa CRUD
        Route::delete('crud/:id', 'v1.setting.SystemCrud/delete')->option(['real_name' => 'Xóa CRUD']);
        //Xem CRUD
        Route::get('crud/:id', 'v1.setting.SystemCrud/read')->option(['real_name' => 'Xem CRUD']);
        //Lấy danh sách CRUD
        Route::get('crud', 'v1.setting.SystemCrud/index')->option(['real_name' => 'Lấy danh sách CRUD']);
        //Lưu và tạo CRUD
        Route::post('crud', 'v1.setting.SystemCrud/save')->option(['real_name' => 'Lưu và tạo CRUD']);
    })->option(['parent' => 'system', 'cate_name' => 'Tạo mã nguồn']);

    /** In biên lai */
    Route::group(function () {
        Route::get('ticket/list', 'v1.system.SystemTicket/ticketList')->option(['real_name' => 'Danh sách in biên lai']);
        Route::get('ticket/form/:id', 'v1.system.SystemTicket/ticketForm')->option(['real_name' => 'Biểu mẫu thêm/sửa in biên lai']);
        Route::post('ticket/save/:id', 'v1.system.SystemTicket/ticketSave')->option(['real_name' => 'Thêm/sửa in biên lai']);
        Route::post('ticket/set_status/:id/:status', 'v1.system.SystemTicket/ticketSetStatus')->option(['real_name' => 'Sửa trạng thái in biên lai']);
        Route::delete('ticket/del/:id', 'v1.system.SystemTicket/ticketDel')->option(['real_name' => 'Xóa in biên lai']);
        Route::get('ticket/content/:id', 'v1.system.SystemTicket/ticketContent')->option(['real_name' => 'Lấy chi tiết in biên lai']);
        Route::post('ticket/save_content/:id', 'v1.system.SystemTicket/ticketContentSave')->option(['real_name' => 'Lưu chi tiết in biên lai']);
    })->option(['parent' => 'system', 'cate_name' => 'In biên lai']);

    /** Quản lý tệp */
    Route::group(function () {
        //Đăng nhập quản lý tệp
        Route::post('file/login', 'v1.system.SystemFile/login')->option(['real_name' => 'Đăng nhập quản lý tệp']);
        //Thực hiện ghi giá trị md5 của tất cả file trong hai thư mục app, crmeb vào cơ sở dữ liệu
        Route::get('write_md5', 'v1.system.SystemFile/writeMd5')->option(['real_name' => 'Thực hiện ghi giá trị md5']);
    })->option(['parent' => 'system', 'cate_name' => 'Quản lý tệp']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'system', 'mark_name' => 'Bảo trì hệ thống']);

Route::group('system', function () {

    //Mở thư mục
    Route::get('file/opendir', 'v1.system.SystemFile/opendir')->option(['real_name' => 'Mở thư mục']);
    //Đọc tệp
    Route::get('file/openfile', 'v1.system.SystemFile/openfile')->option(['real_name' => 'Đọc tệp']);
    //Lưu tệp
    Route::post('file/savefile', 'v1.system.SystemFile/savefile')->option(['real_name' => 'Lưu tệp']);
    //Tạo thư mục
    Route::get('file/createFolder', 'v1.system.SystemFile/createFolder')->option(['real_name' => 'Tạo thư mục']);
    //Tạo tệp
    Route::get('file/createFile', 'v1.system.SystemFile/createFile')->option(['real_name' => 'Tạo tệp']);
    //Xóa thư mục hoặc file
    Route::get('file/delFolder', 'v1.system.SystemFile/delFolder')->option(['real_name' => 'Xóa thư mục']);
    //Đổi tên tệp
    Route::get('file/rename', 'v1.system.SystemFile/rename')->option(['real_name' => 'Đổi tên thư mục']);
    //Biểu mẫu ghi chú tệp trong thư mục
    Route::get('file/mark', 'v1.system.SystemFile/fileMark')->option(['real_name' => 'Biểu mẫu ghi chú tệp trong thư mục']);
    //Lưu ghi chú tệp trong thư mục
    Route::post('file/mark/save', 'v1.system.SystemFile/fileMarkSave')->option(['real_name' => 'Lưu ghi chú tệp trong thư mục']);
})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminEditorTokenMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'system_file', 'mark_name' => 'Quản lý tệp']);

