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
 * Route liên quan diy
 */
Route::group('diy', function () {

    Route::get('get_list', 'v1.diy.Diy/getList')->option(['real_name' => 'Danh sách mẫu Diy']);
    Route::get('get_info/:id', 'v1.diy.Diy/getInfo')->option(['real_name' => 'Chi tiết dữ liệu mẫu Diy']);
    Route::get('get_diy_info/:id', 'v1.diy.Diy/getDiyInfo')->option(['real_name' => 'Chi tiết dữ liệu mẫu Diy']);
    Route::delete('del/:id', 'v1.diy.Diy/del')->option(['real_name' => 'Xóa mẫu DIY']);
    Route::put('set_status/:id', 'v1.diy.Diy/setStatus')->option(['real_name' => 'Sử dụng mẫu DIY']);
    Route::get('create', 'v1.diy.Diy/create')->option(['real_name' => 'Biểu mẫu thêm']);
    Route::post('create', 'v1.diy.Diy/save')->option(['real_name' => 'Thêm DIY']);
    Route::post('save/[:id]', 'v1.diy.Diy/saveData')->option(['real_name' => 'Thêm mẫu DIY']);
    Route::post('diy_save/[:id]', 'v1.diy.Diy/saveDiyData')->option(['real_name' => 'Thêm mẫu DIY']);
    Route::get('get_url', 'v1.diy.Diy/getUrl')->option(['real_name' => 'Lấy đường dẫn trang phía người dùng']);
    Route::get('get_category', 'v1.diy.Diy/getCategory')->option(['real_name' => 'Lấy danh mục sản phẩm']);
    Route::get('get_product', 'v1.diy.Diy/getProduct')->option(['real_name' => 'Lấy danh sách sản phẩm']);
    Route::get('get_store_status', 'v1.diy.Diy/getStoreStatus')->option(['real_name' => 'Lấy trạng thái bật nhận tại cửa hàng']);
    Route::get('recovery/:id', 'v1.diy.Diy/Recovery')->option(['real_name' => 'Khôi phục dữ liệu mặc định Diy']);
    Route::get('get_by_category', 'v1.diy.Diy/getByCategory')->option(['real_name' => 'Lấy tất cả danh mục cấp 2']);
    Route::get('set_recovery/:id', 'v1.diy.Diy/setRecovery')->option(['real_name' => 'Đặt dữ liệu mặc định Diy']);
    Route::get('get_product_list', 'v1.diy.Diy/getProductList')->option(['real_name' => 'Lấy danh sách sản phẩm']);
    Route::get('get_color_change/:type', 'v1.diy.Diy/getColorChange')->option(['real_name' => 'Lấy cài đặt phong cách']);
    Route::put('color_change/:status/:type', 'v1.diy.Diy/colorChange')->option(['real_name' => 'Lưu đổi màu và danh mục']);
    Route::get('get_member', 'v1.diy.Diy/getMember')->option(['real_name' => 'Chi tiết trang cá nhân']);
    Route::get('get_page_category', 'v1.diy.PageLink/getCategory')->option(['real_name' => 'Lấy danh mục liên kết trang']);
    Route::get('get_page_link/:cate_id', 'v1.diy.PageLink/getLinks')->option(['real_name' => 'Lấy liên kết trang']);
    Route::post('member_save', 'v1.diy.Diy/memberSaveData')->option(['real_name' => 'Lưu trang cá nhân']);
    Route::get('get_routine_code/:id', 'v1.diy.Diy/getRoutineCode')->option(['real_name' => 'Mã xem trước diy trên Mini Program']);
    Route::get('open_adv/info', 'v1.diy.Diy/getOpenAdv')->option(['real_name' => 'Lấy quảng cáo màn hình khởi động']);
    Route::post('open_adv/add', 'v1.diy.Diy/openAdvAdd')->option(['real_name' => 'Lưu quảng cáo màn hình khởi động']);
    Route::get('groom_list/:type', 'v1.diy.Diy/getGroomList')->option(['real_name' => 'Sản phẩm đề xuất']);
    Route::get('link/category', 'v1.diy.PageLink/getLinkCategory')->option(['real_name' => 'Lấy danh mục liên kết']);
    Route::get('link/category/form/:cate_id/[:pid]', 'v1.diy.PageLink/getLinkCategoryForm')->option(['real_name' => 'Biểu mẫu danh mục liên kết']);
    Route::post('link/category/save/:cate_id', 'v1.diy.PageLink/getLinkCategorySave')->option(['real_name' => 'Lưu danh mục liên kết']);
    Route::delete('link/category/del/:cate_id', 'v1.diy.PageLink/getLinkCategoryDel')->option(['real_name' => 'Xóa danh mục liên kết']);
    Route::get('link/list/:cate_id', 'v1.diy.PageLink/getLinkList')->option(['real_name' => 'Danh sách liên kết']);
    Route::post('link/save/:id', 'v1.diy.PageLink/getLinkSave')->option(['real_name' => 'Lưu liên kết']);
    Route::delete('link/del/:id', 'v1.diy.PageLink/getLinkDel')->option(['real_name' => 'Xóa liên kết']);
})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'diy', 'mark_name' => 'Thiết kế giao diện']);


/**
 * Route liên quan diy_pro
 */
Route::group('diy_pro', function () {
    Route::get('get_list', 'v1.diy.DiyPro/getList')->option(['real_name' => 'Danh sách mẫu DiyPro']);
    Route::get('get_info/:id', 'v1.diy.DiyPro/getInfo')->option(['real_name' => 'Chi tiết mẫu DiyPro']);
    Route::post('save/:id', 'v1.diy.DiyPro/saveInfo')->option(['real_name' => 'Lưu mẫu DiyPro']);
    Route::get('get_product', 'v1.diy.DiyPro/getProduct')->option(['real_name' => 'Lấy danh sách sản phẩm']);
    Route::post('update/name/:id', 'v1.diy.DiyPro/updateName')->option(['real_name' => 'Sửa tên']);
    Route::get('export/data/:id', 'v1.diy.DiyPro/exportDIYData')->option(['real_name' => 'Xuất dữ liệu DIY']);
    Route::post('import/data', 'v1.diy.DiyPro/importDIYData')->option(['real_name' => 'Nhập dữ liệu DIY']);
    Route::get('text/field', 'v1.diy.DiyPro/textField')->option(['real_name' => 'Trường văn bản']);
})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'diy_pro', 'mark_name' => 'Thiết kế giao diện']);


/**
 * Route liên quan đến chủ đề
 */
Route::group('theme', function () {
    Route::get('list', 'v1.diy.Theme/getThemeList')->option(['real_name' => 'Danh sách chủ đề']);
    Route::get('info/:id/[:type]', 'v1.diy.Theme/getThemeInfo')->option(['real_name' => 'Chi tiết chủ đề']);
    Route::post('save/:id', 'v1.diy.Theme/saveTheme')->option(['real_name' => 'Lưu chủ đề']);
    Route::post('save_title/:id', 'v1.diy.Theme/saveThemeTitle')->option(['real_name' => 'Lưu tên và mô tả ngắn của chủ đề']);
    Route::post('save_image/:id', 'v1.diy.Theme/saveThemeImage')->option(['real_name' => 'Lưu ảnh của chủ đề']);
    Route::get('article', 'v1.diy.Theme/getThemeArticleList')->option(['real_name' => 'Thành phần tùy chỉnh - bài viết']);
    Route::get('coupon', 'v1.diy.Theme/getThemeCouponList')->option(['real_name' => 'Thành phần tùy chỉnh - phiếu giảm giá']);
    Route::get('product', 'v1.diy.Theme/getThemeProductList')->option(['real_name' => 'Thành phần tùy chỉnh - sản phẩm']);
    Route::delete('del/:id', 'v1.diy.Theme/deleteTheme')->option(['real_name' => 'Xóa chủ đề']);
    Route::get('export/:id', 'v1.diy.Theme/exportTheme')->option(['real_name' => 'Xuất chủ đề']);
    Route::get('export_record/:record_id', 'v1.diy.Theme/getExportRecord')->option(['real_name' => 'Tra cứu lịch sử xuất chủ đề']);
    Route::post('import', 'v1.diy.Theme/importTheme')->option(['real_name' => 'Nhập chủ đề']);
    Route::get('use/:id', 'v1.diy.Theme/useTheme')->option(['real_name' => 'Áp dụng chủ đề']);
    Route::get('use_data/:id', 'v1.diy.Theme/useThemeData')->option(['real_name' => 'Áp dụng dữ liệu chủ đề']);
    Route::get('using', 'v1.diy.Theme/getUsingTheme')->option(['real_name' => 'Chủ đề đang sử dụng']);
    Route::get('restore/:id', 'v1.diy.Theme/restoreTheme')->option(['real_name' => 'Khôi phục chủ đề']);
    Route::get('micro_page', 'v1.diy.Theme/getMicroPageList')->option(['real_name' => 'Danh sách trang tùy chỉnh']);
})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'theme', 'mark_name' => 'Chủ đề']);

/**
 * Route liên quan đến thành phần chủ đề
 */
Route::group('theme_module', function () {
    Route::get('list', 'v1.diy.ThemeModule/index')->option(['real_name' => 'Danh sách thành phần chủ đề']);
    Route::post('save', 'v1.diy.ThemeModule/save')->option(['real_name' => 'Thêm mới thành phần chủ đề']);
    Route::delete('del/:id', 'v1.diy.ThemeModule/delete')->option(['real_name' => 'Xóa thành phần chủ đề']);
})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'theme_module', 'mark_name' => 'Thành phần chủ đề']);
