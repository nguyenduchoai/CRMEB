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

Route::group('product', function () {

    /** Danh mục sản phẩm */
    Route::group(function () {
        Route::get('category', 'v1.product.StoreCategory/index')->option(['real_name' => 'Danh sách danh mục sản phẩm']);
        //Danh sách sản phẩm dạng cây
        Route::get('category/tree/:type', 'v1.product.StoreCategory/tree_list')->option(['real_name' => 'Danh sách danh mục sản phẩm dạng cây']);
        //Danh sách danh mục sản phẩm dạng cây
        Route::get('category/cascader/:type', 'v1.product.StoreCategory/cascader_list')->option(['real_name' => 'Danh sách danh mục sản phẩm dạng cây']);
        //Biểu mẫu thêm danh mục sản phẩm
        Route::get('category/create', 'v1.product.StoreCategory/create')->option(['real_name' => 'Biểu mẫu thêm danh mục sản phẩm']);
        //Thêm danh mục sản phẩm
        Route::post('category', 'v1.product.StoreCategory/save')->option(['real_name' => 'Thêm danh mục sản phẩm']);
        //Biểu mẫu sửa danh mục sản phẩm
        Route::get('category/:id', 'v1.product.StoreCategory/edit')->option(['real_name' => 'Biểu mẫu sửa danh mục sản phẩm']);
        //Sửa danh mục sản phẩm
        Route::put('category/:id', 'v1.product.StoreCategory/update')->option(['real_name' => 'Sửa danh mục sản phẩm']);
        //Xóa danh mục sản phẩm
        Route::delete('category/:id', 'v1.product.StoreCategory/delete')->option(['real_name' => 'Xóa danh mục sản phẩm']);
        //Sửa trạng thái danh mục sản phẩm
        Route::put('category/set_show/:id/:is_show', 'v1.product.StoreCategory/set_show')->option(['real_name' => 'Sửa trạng thái danh mục sản phẩm']);
        //Sửa nhanh danh mục sản phẩm
        Route::put('category/set_category/:id', 'v1.product.StoreCategory/set_category')->option(['real_name' => 'Sửa nhanh danh mục sản phẩm']);
    })->option(['parent' => 'product', 'cate_name' => 'Danh mục sản phẩm']);

    /** Sản phẩm */
    Route::group(function () {
        //Danh sách sản phẩm
        Route::get('product', 'v1.product.StoreProduct/index')->option(['real_name' => 'Danh sách sản phẩm']);
        //Lấy dữ liệu chưa lưu khi thoát
        Route::get('cache', 'v1.product.StoreProduct/getCacheData')->option(['real_name' => 'Lấy dữ liệu chưa lưu khi thoát']);
        //Cứ 1 phút lưu dữ liệu một lần
        Route::post('cache', 'v1.product.StoreProduct/saveCacheData')->option(['real_name' => 'Lưu dữ liệu chưa gửi']);
        //Lấy danh sách tất cả sản phẩm
        Route::get('product/list', 'v1.product.StoreProduct/search_list')->option(['real_name' => 'Lấy danh sách tất cả sản phẩm']);
        //Lấy quy cách sản phẩm
        Route::get('product/attrs/:id/:type', 'v1.product.StoreProduct/get_attrs')->option(['real_name' => 'Lấy quy cách sản phẩm']);
        //Phần đầu danh sách sản phẩm
        Route::get('product/type_header', 'v1.product.StoreProduct/type_header')->option(['real_name' => 'Dữ liệu phần đầu danh sách sản phẩm']);
        //Sửa trạng thái sản phẩm
        Route::put('product/set_show/:id/:is_show', 'v1.product.StoreProduct/set_show')->option(['real_name' => 'Sửa trạng thái sản phẩm']);
        //Sửa nhanh sản phẩm
//        Route::put('product/set_product/:id', 'v1.product.StoreProduct/set_product')->option(['real_name' => 'Sửa nhanh sản phẩm']);
        //Đăng bán sản phẩm hàng loạt
        Route::put('product/product_show', 'v1.product.StoreProduct/product_show')->option(['real_name' => 'Đăng bán sản phẩm hàng loạt']);
        //Ngừng bán sản phẩm hàng loạt
        Route::put('product/product_unshow', 'v1.product.StoreProduct/product_unshow')->option(['real_name' => 'Ngừng bán sản phẩm hàng loạt']);
        //Danh sách quy tắc
        Route::get('product/rule', 'v1.product.StoreProductRule/index')->option(['real_name' => 'Danh sách mẫu quy cách sản phẩm']);
        //Quy tắc - lưu khi thêm mới hoặc sửa
        Route::post('product/rule/:id', 'v1.product.StoreProductRule/save')->option(['real_name' => 'Tạo mới hoặc sửa mẫu quy cách sản phẩm']);
        //Chi tiết quy tắc
        Route::get('product/rule/:id', 'v1.product.StoreProductRule/read')->option(['real_name' => 'Chi tiết mẫu quy cách sản phẩm']);
        //Xóa quy tắc thuộc tính
        Route::delete('product/rule/delete', 'v1.product.StoreProductRule/delete')->option(['real_name' => 'Xóa mẫu quy cách sản phẩm']);
        //Lấy mẫu thuộc tính quy tắc
        Route::get('product/get_rule', 'v1.product.StoreProduct/get_rule')->option(['real_name' => 'Lấy mẫu thuộc tính quy cách sản phẩm']);
        //Lấy mẫu phí vận chuyển
        Route::get('product/get_template', 'v1.product.StoreProduct/get_template')->option(['real_name' => 'Lấy mẫu phí vận chuyển']);
        //API khóa tải lên video
        Route::get('product/get_temp_keys', 'v1.product.StoreProduct/getTempKeys')->option(['real_name' => 'API khóa tải lên video']);
        //Kiểm tra có hoạt động đang mở không
        Route::get('product/check_activity/:id', 'v1.product.StoreProduct/check_activity')->option(['real_name' => 'Kiểm tra sản phẩm có hoạt động đang bật không']);
        //Nhập mã thẻ sản phẩm ảo
        Route::get('product/import_card', 'v1.product.StoreProduct/import_card')->option(['real_name' => 'Nhập mã thẻ sản phẩm ảo']);
        //Chi tiết sản phẩm
        Route::get('product/:id', 'v1.product.StoreProduct/get_product_info')->option(['real_name' => 'Chi tiết sản phẩm']);
        //Chuyển vào thùng rác
        Route::delete('product/:id', 'v1.product.StoreProduct/delete')->option(['real_name' => 'Chuyển sản phẩm vào thùng rác']);
        //Chuyển vào thùng rác
        Route::post('product/batch_delete', 'v1.product.StoreProduct/batchDelete')->option(['real_name' => 'Chuyển sản phẩm hàng loạt vào thùng rác']);
        //Khôi phục hàng loạt từ thùng rác
        Route::post('product/batch_recover', 'v1.product.StoreProduct/batchRecover')->option(['real_name' => 'Khôi phục hàng loạt từ thùng rác']);
        //Lưu khi thêm mới hoặc lưu
        Route::post('product/:id', 'v1.product.StoreProduct/save')->option(['real_name' => 'Tạo mới hoặc sửa sản phẩm']);
        //Tạo thuộc tính
        Route::post('generate_attr/:id/:type', 'v1.product.StoreProduct/is_format_attr')->option(['real_name' => 'Tạo danh sách quy cách sản phẩm']);
        //Thao tác sản phẩm theo lô
        Route::post('batch/setting', 'v1.product.StoreProduct/batchSetting')->option(['real_name' => 'Cài đặt sản phẩm hàng loạt']);
        //API loại sản phẩm
        Route::get('product_type_config', 'v1.product.StoreProduct/productTypeConfig')->option(['real_name' => 'API loại sản phẩm']);
        //Xuất dữ liệu di chuyển sản phẩm
        Route::get('product_export', 'v1.product.StoreProduct/productExport')->option(['real_name' => 'Xuất dữ liệu di chuyển sản phẩm']);
        //Nhập dữ liệu sản phẩm di chuyển
        Route::post('product_import', 'v1.product.StoreProduct/productImport')->option(['real_name' => 'Xuất dữ liệu di chuyển sản phẩm']);
        //Xóa vĩnh viễn sản phẩm trong thùng rác
        Route::delete('full_del/:id', 'v1.product.StoreProduct/fullDel')->option(['real_name' => 'Xóa vĩnh viễn sản phẩm trong thùng rác']);

        Route::get('other_info/:id/:type', 'v1.product.StoreProduct/otherInfo')->option(['real_name' => 'Thông tin khác của sản phẩm']);
        Route::post('other_save/:id/:type', 'v1.product.StoreProduct/otherSave')->option(['real_name' => 'Sửa thông tin khác của sản phẩm']);

    })->option(['parent' => 'product', 'cate_name' => 'Sản phẩm']);

    /** Đánh giá sản phẩm */
    Route::group(function () {
        //Danh sách đánh giá
        Route::get('reply', 'v1.product.StoreProductReply/index')->option(['real_name' => 'Danh sách đánh giá sản phẩm']);
        //Trả lời đánh giá
        Route::put('reply/set_reply/:id', 'v1.product.StoreProductReply/set_reply')->option(['real_name' => 'Trả lời đánh giá sản phẩm']);
        //Xóa đánh giá
        Route::delete('reply/:id', 'v1.product.StoreProductReply/delete')->option(['real_name' => 'Xóa đánh giá sản phẩm']);
        //Mở form đánh giá ảo
        Route::get('reply/fictitious_reply/:product_id', 'v1.product.StoreProductReply/fictitious_reply')->option(['real_name' => 'Biểu mẫu đánh giá ảo']);
        //Lưu đánh giá ảo
        Route::post('reply/save_fictitious_reply', 'v1.product.StoreProductReply/save_fictitious_reply')->option(['real_name' => 'Lưu đánh giá ảo']);
        //Duyệt đánh giá sản phẩm
        Route::put('reply/set_status/:id/:status', 'v1.product.StoreProductReply/set_status')->option(['real_name' => 'Duyệt đánh giá sản phẩm']);
        //Duyệt đánh giá sản phẩm hàng loạt
        Route::post('reply/batch_set_status', 'v1.product.StoreProductReply/batch_set_status')->option(['real_name' => 'Duyệt đánh giá sản phẩm hàng loạt']);
    })->option(['parent' => 'product', 'cate_name' => 'Đánh giá sản phẩm']);

    /** Thu thập sản phẩm */
    Route::group(function () {
        //Lấy dữ liệu sản phẩm
        Route::post('crawl', 'v1.product.CopyTaobao/get_request_contents')->option(['real_name' => 'Lấy dữ liệu sản phẩm thu thập']);
        //Lấy cấu hình sao chép sản phẩm
        Route::get('copy_config', 'v1.product.CopyTaobao/getConfig')->option(['real_name' => 'Lấy cấu hình sao chép sản phẩm']);
        //Sao chép sản phẩm từ nền tảng khác
        Route::post('copy', 'v1.product.CopyTaobao/copyProduct')->option(['real_name' => 'Sao chép sản phẩm từ nền tảng khác']);
        //Lưu dữ liệu sản phẩm
        Route::post('crawl/save', 'v1.product.CopyTaobao/save_product')->option(['real_name' => 'Lưu dữ liệu sản phẩm thu thập']);
    })->option(['parent' => 'product', 'cate_name' => 'Thu thập sản phẩm']);

    /** Nhãn sản phẩm */
    Route::group(function () {
        //Danh mục nhãn sản phẩm
        Route::get('label_cate/list', 'v1.product.StoreProductLabel/labelCateList')->option(['real_name' => 'Danh mục nhãn sản phẩm']);
        Route::get('label_cate/form/:id', 'v1.product.StoreProductLabel/labelCateForm')->option(['real_name' => 'Biểu mẫu thêm danh mục nhãn sản phẩm']);
        Route::post('label_cate/save/:id', 'v1.product.StoreProductLabel/labelCateSave')->option(['real_name' => 'Lưu danh mục nhãn sản phẩm']);
        Route::delete('label_cate/del/:id', 'v1.product.StoreProductLabel/labelCateDel')->option(['real_name' => 'Xóa danh mục nhãn sản phẩm']);
        Route::get('label/list', 'v1.product.StoreProductLabel/labelList')->option(['real_name' => 'Danh sách nhãn sản phẩm']);
        Route::get('label/info/:id', 'v1.product.StoreProductLabel/labelInfo')->option(['real_name' => 'Chi tiết nhãn sản phẩm']);
        Route::post('label/save', 'v1.product.StoreProductLabel/labelSave')->option(['real_name' => 'Lưu nhãn sản phẩm']);
        Route::delete('label/del/:id', 'v1.product.StoreProductLabel/labelDel')->option(['real_name' => 'Xóa nhãn sản phẩm']);
        Route::put('label/status/:id/:status', 'v1.product.StoreProductLabel/labelStatus')->option(['real_name' => 'Sửa trạng thái nhãn sản phẩm']);
        Route::put('label/is_show/:id/:is_show', 'v1.product.StoreProductLabel/labelIsShow')->option(['real_name' => 'Sửa hiển thị nhãn sản phẩm']);
        Route::get('label/use_list', 'v1.product.StoreProductLabel/labelUseList')->option(['real_name' => 'Danh sách nhãn sản phẩm khả dụng']);
    })->option(['parent' => 'product', 'cate_name' => 'Nhãn sản phẩm']);

    /** Thông số sản phẩm */
    Route::group(function () {
        Route::get('param/list', 'v1.product.StoreProductParam/getParamList')->option(['real_name' => 'Danh sách thông số sản phẩm']);
        Route::get('param/info/:id', 'v1.product.StoreProductParam/getParamInfo')->option(['real_name' => 'Chi tiết thông số sản phẩm']);
        Route::get('param/value/:id', 'v1.product.StoreProductParam/getParamValue')->option(['real_name' => 'Giá trị thông số sản phẩm']);
        Route::post('param/save/:id', 'v1.product.StoreProductParam/saveParamData')->option(['real_name' => 'Lưu thông số sản phẩm']);
        Route::put('param/status/:id/:status', 'v1.product.StoreProductParam/setParamStatus')->option(['real_name' => 'Sửa trạng thái thông số sản phẩm']);
        Route::delete('param/del/:id', 'v1.product.StoreProductParam/delParamData')->option(['real_name' => 'Xóa thông số sản phẩm']);
    })->option(['parent' => 'product', 'cate_name' => 'Thông số sản phẩm']);

    /** Đảm bảo sản phẩm */
    Route::group(function () {
        Route::get('protection/list', 'v1.product.StoreProductProtection/protectionList')->option(['real_name' => 'Danh sách đảm bảo sản phẩm']);
        Route::get('protection/info/:id', 'v1.product.StoreProductProtection/protectionInfo')->option(['real_name' => 'Chi tiết đảm bảo sản phẩm']);
        Route::get('protection/form/:id', 'v1.product.StoreProductProtection/protectionForm')->option(['real_name' => 'Biểu mẫu đảm bảo sản phẩm']);
        Route::post('protection/save/:id', 'v1.product.StoreProductProtection/protectionSave')->option(['real_name' => 'Lưu đảm bảo sản phẩm']);
        Route::put('protection/status/:id/:status', 'v1.product.StoreProductProtection/protectionStatus')->option(['real_name' => 'Sửa trạng thái đảm bảo sản phẩm']);
        Route::delete('protection/del/:id', 'v1.product.StoreProductProtection/protectionDel')->option(['real_name' => 'Xóa đảm bảo sản phẩm']);
    })->option(['parent' => 'product', 'cate_name' => 'Thông số sản phẩm']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'product', 'mark_name' => 'Quản lý sản phẩm']);
