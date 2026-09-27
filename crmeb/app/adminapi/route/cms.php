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
 * Route liên quan Quản lý bài viết
 */
Route::group('cms', function () {

    /** Bài viết */
    Route::group(function () {
        //Route resource bài viết
        Route::resource('cms', 'v1.cms.Article')->option([
            'real_name' => [
                'index' => 'Lấy danh sách bài viết',
                'create' => 'Lấy biểu mẫu bài viết',
                'read' => 'Lấy thông tin chi tiết bài viết',
                'save' => 'Lưu bài viết',
                'edit' => 'Lấy biểu mẫu sửa bài viết',
                'update' => 'Chỉnh sửa bài viết',
                'delete' => 'Xóa bài viết'
            ]
        ]);
        //Liên kết sản phẩm
        Route::put('cms/relation/:id', 'v1.cms.Article/relation')->name('Relation')->option(['real_name' => 'Liên kết sản phẩm với bài viết']);
        //Hủy liên kết
        Route::put('cms/unrelation/:id', 'v1.cms.Article/unrelation')->name('UnRelation')->option(['real_name' => 'Hủy liên kết sản phẩm với bài viết']);
    })->option(['parent' => 'cms', 'cate_name' => 'Quản lý bài viết']);

    /** Danh mục bài viết */
    Route::group(function () {
        //Route resource danh mục bài viết
        Route::resource('category', 'v1.cms.ArticleCategory')->except(['read'])->option([
            'real_name' => [
                'index' => 'Lấy danh sách danh mục bài viết',
                'create' => 'Lấy biểu mẫu danh mục bài viết',
                'save' => 'Lưu danh mục bài viết',
                'edit' => 'Lấy biểu mẫu sửa danh mục bài viết',
                'update' => 'Sửa danh mục bài viết',
                'delete' => 'Xóa danh mục bài viết'
            ]
        ]);
        //Sửa trạng thái
        Route::put('category/set_status/:id/:status', 'v1.cms.ArticleCategory/set_status')->name('CategoryStatus')->option(['real_name' => 'Sửa trạng thái danh mục bài viết']);
        //Danh sách danh mục
        Route::get('category_list', 'v1.cms.ArticleCategory/categoryList')->name('categoryList')->option(['real_name' => 'Danh sách danh mục']);
        //Danh sách danh mục dạng cây
        Route::get('category_tree_list', 'v1.cms.ArticleCategory/getTreeList')->name('getTreeList')->option(['real_name' => 'Danh sách danh mục dạng cây']);
    })->option(['parent' => 'cms', 'cate_name' => 'Danh mục bài viết']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'cms', 'mark_name' => 'Mô-đun bài viết']);
