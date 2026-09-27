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
 * Route đơn hàng
 */
Route::group('order', function () {
    //Lấy thông tin chuyển phát
    Route::get('kuaidi_coms', 'v1.order.StoreOrder/getKuaidiComs')->option(['real_name' => 'Lấy thông tin chuyển phát']);
    //Hủy yêu cầu gửi hàng của người bán
    Route::post('shipment_cancel_order/:id', 'v1.order.StoreOrder/shipmentCancelOrder')->option(['real_name' => 'Hủy yêu cầu gửi hàng của người bán']);
    //In đơn hàng
    Route::get('print/:id', 'v1.order.StoreOrder/order_print')->name('StoreOrderPrint')->option(['real_name' => 'In đơn hàng']);
    //Danh sách đơn hàng
    Route::get('list', 'v1.order.StoreOrder/lst')->name('StoreOrderList')->option(['real_name' => 'Danh sách đơn hàng']);
    //Dữ liệu đơn hàng
    Route::get('chart', 'v1.order.StoreOrder/chart')->name('StoreOrderChart')->option(['real_name' => 'Dữ liệu phần đầu đơn hàng']);
    //Xác nhận sử dụng đơn hàng
    Route::post('write', 'v1.order.StoreOrder/write_order')->name('writeOrder')->option(['real_name' => 'Xác nhận sử dụng đơn hàng']);
    //Xác nhận sử dụng theo mã đơn hàng
    Route::put('write_update/:order_id', 'v1.order.StoreOrder/write_update')->name('writeOrderUpdate')->option(['real_name' => 'Xác nhận sử dụng theo mã đơn hàng']);
    //Lấy bảng form sửa đơn hàng
    Route::get('edit/:id', 'v1.order.StoreOrder/edit')->name('StoreOrderEdit')->option(['real_name' => 'Lấy biểu mẫu sửa đơn hàng']);
    //Chỉnh sửa đơn hàng
    Route::put('update/:id', 'v1.order.StoreOrder/update')->name('StoreOrderUpdate')->option(['real_name' => 'Chỉnh sửa đơn hàng']);
    //Xác nhận đã nhận hàng
    Route::put('take/:id', 'v1.order.StoreOrder/take_delivery')->name('StoreOrderTakeDelivery')->option(['real_name' => 'Xác nhận đã nhận hàng']);
    //Giao hàng hàng loạt
    Route::get('delivery/import_express', 'v1.order.StoreOrder/importExpress')->name('importExpress')->option(['real_name' => 'Giao hàng hàng loạt']);
    //Giao hàng
    Route::put('delivery/:id', 'v1.order.StoreOrder/update_delivery')->name('StoreOrderUpdateDelivery')->option(['real_name' => 'Thực hiện giao đơn hàng']);
    //Lấy phí gửi hàng của người bán
    Route::post('price', 'v1.order.StoreOrder/getPrice')->name('getPrice')->option(['real_name' => 'Lấy phí gửi hàng của người bán']);
    //Lấy danh sách sản phẩm có thể tách của đơn hàng
    Route::get('split_cart_info/:id', 'v1.order.StoreOrder/split_cart_info')->name('StoreOrderSplitCartInfo')->option(['real_name' => 'Lấy danh sách sản phẩm có thể tách của đơn hàng']);
    //Tách đơn để giao hàng
    Route::put('split_delivery/:id', 'v1.order.StoreOrder/split_delivery')->name('StoreOrderSplitDelivery')->option(['real_name' => 'Tách đơn để giao hàng']);
    //Lấy danh sách đơn con đã tách của đơn hàng
    Route::get('split_order/:id', 'v1.order.StoreOrder/split_order')->name('StoreOrderSplitOrder')->option(['real_name' => 'Lấy danh sách đơn con đã tách của đơn hàng']);
    //Bảng form hoàn tiền đơn hàng
    Route::get('refund/:id', 'v1.order.StoreOrder/refund')->name('StoreOrderRefund')->option(['real_name' => 'Biểu mẫu hoàn tiền đơn hàng']);
    //Hoàn tiền đơn hàng
    Route::put('refund/:id', 'v1.order.StoreOrder/update_refund')->name('StoreOrderUpdateRefund')->option(['real_name' => 'Hoàn tiền đơn hàng']);
    //Lấy mẫu vận đơn điện tử
    Route::get('express/temp', 'v1.order.StoreOrder/express_temp')->option(['real_name' => 'Mẫu vận đơn điện tử của đơn vị vận chuyển']);
    //Lấy thông tin vận chuyển
    Route::get('express/:id', 'v1.order.StoreOrder/get_express')->name('StoreOrderUpdateExpress')->option(['real_name' => 'Lấy thông tin vận chuyển']);
    //Lấy đơn vị vận chuyển
    Route::get('express_list', 'v1.order.StoreOrder/express')->name('StoreOrdeRexpressList')->option(['real_name' => 'Lấy đơn vị vận chuyển']);
    //Chi tiết đơn hàng
    Route::get('info/:id', 'v1.order.StoreOrder/order_info')->name('StoreOrderorInfo')->option(['real_name' => 'Chi tiết đơn hàng']);
    //Lấy bảng form thông tin giao hàng
    Route::get('distribution/:id', 'v1.order.StoreOrder/distribution')->name('StoreOrderorDistribution')->option(['real_name' => 'Lấy biểu mẫu thông tin giao hàng']);
    //Sửa thông tin giao hàng
    Route::put('distribution/:id', 'v1.order.StoreOrder/update_distribution')->name('StoreOrderorUpdateDistribution')->option(['real_name' => 'Sửa thông tin giao hàng']);
    //Lấy bảng form không hoàn tiền
    Route::get('no_refund/:id', 'v1.order.StoreOrder/no_refund')->name('StoreOrderorNoRefund')->option(['real_name' => 'Lấy biểu mẫu từ chối hoàn tiền']);
    //Sửa lý do từ chối hoàn tiền
    Route::put('no_refund/:id', 'v1.order.StoreOrder/update_un_refund')->name('StoreOrderorUpdateNoRefund')->option(['real_name' => 'Sửa lý do từ chối hoàn tiền']);
    //Thanh toán ngoại tuyến
    Route::post('pay_offline/:id', 'v1.order.StoreOrder/pay_offline')->name('StoreOrderorPayOffline')->option(['real_name' => 'Thanh toán ngoại tuyến']);
    //Lấy bảng form trả điểm thưởng
    Route::get('refund_integral/:id', 'v1.order.StoreOrder/refund_integral')->name('StoreOrderorRefundIntegral')->option(['real_name' => 'Lấy biểu mẫu hoàn điểm thưởng']);
    //Sửa hoàn điểm thưởng
    Route::put('refund_integral/:id', 'v1.order.StoreOrder/update_refund_integral')->name('StoreOrderorUpdateRefundIntegral')->option(['real_name' => 'Sửa hoàn điểm thưởng']);
    //Sửa thông tin ghi chú
    Route::put('remark/:id', 'v1.order.StoreOrder/remark')->name('StoreOrderorRemark')->option(['real_name' => 'Sửa thông tin ghi chú']);
    //Lấy trạng thái đơn hàng
    Route::get('status/:id', 'v1.order.StoreOrder/status')->name('StoreOrderorStatus')->option(['real_name' => 'Lấy trạng thái đơn hàng']);
    //Xóa một đơn hàng
    Route::delete('del/:id', 'v1.order.StoreOrder/del')->name('StoreOrderorDel')->option(['real_name' => 'Xóa một đơn hàng']);
    //Xóa đơn hàng hàng loạt
    Route::post('dels', 'v1.order.StoreOrder/del_orders')->name('StoreOrderorDels')->option(['real_name' => 'Xóa đơn hàng hàng loạt']);
    //Thông tin cấu hình mặc định của vận đơn
    Route::get('sheet_info', 'v1.order.StoreOrder/getDeliveryInfo')->option(['real_name' => 'Thông tin cấu hình mặc định của vận đơn']);
    //Lấy mã QR thanh toán ngoại tuyến
    Route::get('offline_scan', 'v1.order.OtherOrder/offline_scan')->name('OfflineScan')->option(['real_name' => 'Lấy mã QR thanh toán ngoại tuyến']);
    //Danh sách thu ngân tại quầy
    Route::get('scan_list', 'v1.order.OtherOrder/scan_list')->name('ScanList')->option(['real_name' => 'Danh sách thu ngân tại quầy']);
    //Thống kê phần đầu danh sách hóa đơn
    Route::get('invoice/chart', 'v1.order.StoreOrderInvoice/chart')->name('StoreOrderorInvoiceChart')->option(['real_name' => 'Thống kê phần đầu danh sách hóa đơn']);
    //Danh sách yêu cầu xuất hóa đơn
    Route::get('invoice/list', 'v1.order.StoreOrderInvoice/list')->name('StoreOrderorInvoiceList')->option(['real_name' => 'Danh sách yêu cầu xuất hóa đơn']);
    //Đặt trạng thái hóa đơn
    Route::post('invoice/set/:id', 'v1.order.StoreOrderInvoice/set_invoice')->name('StoreOrderorInvoiceSet')->option(['real_name' => 'Đặt trạng thái hóa đơn']);
    //Chi tiết đơn hàng xuất hóa đơn
    Route::get('invoice_order_info/:id', 'v1.order.StoreOrderInvoice/orderInfo')->name('StoreOrderorInvoiceOrderInfo')->option(['real_name' => 'Chi tiết đơn hàng xuất hóa đơn']);
    //Lấy địa chỉ iframe của trang xuất hóa đơn
    Route::get('invoice_issuance_url/:id', 'v1.order.StoreOrderInvoice/invoiceIssuanceUrl')->name('invoiceIssuanceUrl')->option(['real_name' => 'Lấy địa chỉ iframe của trang xuất hóa đơn']);
    //Lưu thông tin hóa đơn
    Route::post('save_invoice_info/:id', 'v1.order.StoreOrderInvoice/saveInvoiceInfo')->name('saveInvoiceInfo')->option(['real_name' => 'Lưu thông tin hóa đơn']);
    //Danh mục hóa đơn điện tử
    Route::get('invoice_category', 'v1.order.StoreOrderInvoice/invoiceCategory')->name('invoiceCategory')->option(['real_name' => 'Danh mục hóa đơn điện tử']);
    //Xuất hóa đơn
    Route::post('invoice_issuance', 'v1.order.StoreOrderInvoice/invoiceIssuance')->name('invoiceIssuance')->option(['real_name' => 'Xuất hóa đơn']);
    //Xem chi tiết hóa đơn
    Route::get('invoice_info/:id', 'v1.order.StoreOrderInvoice/invoiceInfo')->name('invoiceInfo')->option(['real_name' => 'Xem chi tiết hóa đơn']);
    //Xuất hóa đơn điều chỉnh giảm
    Route::get('red_invoice_issuance/:id', 'v1.order.StoreOrderInvoice/redInvoiceIssuance')->name('redInvoiceIssuance')->option(['real_name' => 'Xuất hóa đơn điều chỉnh giảm']);
    //Tải xuống hóa đơn
    Route::get('down_invoice/:id', 'v1.order.StoreOrderInvoice/downInvoice')->name('downInvoice')->option(['real_name' => 'Tải xuống hóa đơn']);
    //Cấu hình hóa đơn điện tử
    Route::get('elec_invoice_config', 'v1.order.StoreOrderInvoice/elecInvoiceConfig')->name('elecInvoiceConfig')->option(['real_name' => 'Cấu hình hóa đơn điện tử']);
    //Danh sách nhân viên giao hàng
    Route::get('delivery/index', 'v1.order.DeliveryService/index')->option(['real_name' => 'Danh sách nhân viên giao hàng']);
    //Biểu mẫu thêm nhân viên giao hàng
    Route::get('delivery/add', 'v1.order.DeliveryService/add')->option(['real_name' => 'Biểu mẫu thêm nhân viên giao hàng']);
    //Lưu dữ liệu mới tạo
    Route::post('delivery/save', 'v1.order.DeliveryService/save')->option(['real_name' => 'Lưu nhân viên giao hàng mới tạo']);
    //Biểu mẫu sửa nhân viên giao hàng
    Route::get('delivery/:id/edit', 'v1.order.DeliveryService/edit')->option(['real_name' => 'Biểu mẫu sửa nhân viên giao hàng']);
    //Lưu dữ liệu đã sửa
    Route::put('delivery/update/:id', 'v1.order.DeliveryService/update')->option(['real_name' => 'Sửa nhân viên giao hàng']);
    //Xóa
    Route::delete('delivery/del/:id', 'v1.order.DeliveryService/delete')->option(['real_name' => 'Xóa nhân viên giao hàng']);
    //Sửa trạng thái
    Route::get('delivery/set_status/:id/:status', 'v1.order.DeliveryService/set_status')->option(['real_name' => 'Sửa trạng thái nhân viên giao hàng']);
    //Lấy nhân viên giao hàng cho danh sách đơn hàng
    Route::get('delivery/list', 'v1.order.DeliveryService/get_delivery_list')->option(['real_name' => 'Lấy nhân viên giao hàng cho danh sách đơn hàng']);
    //Danh sách mẫu vận đơn điện tử
    Route::get('expr/temp', 'v1.order.StoreOrder/expr_temp')->option(['real_name' => 'Danh sách mẫu vận đơn điện tử']);
    //In phiếu giao hàng
    Route::get('print/shipping/:order_id', 'v1.order.StoreOrder/printShipping')->option(['real_name' => 'In phiếu giao hàng']);
    //Thao tác khác: in vận đơn điện tử
    Route::get('order_dump/:order_id', 'v1.order.StoreOrder/order_dump')->option(['real_name' => 'Thao tác khác: in vận đơn điện tử']);
    //Sửa địa chỉ giao hàng của đơn hàng chưa giao
    Route::post('edit_address/:id', 'v1.order.StoreOrder/editAddress')->option(['real_name' => 'Sửa địa chỉ giao hàng của đơn hàng chưa giao']);
})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'order', 'mark_name' => 'Quản lý đơn hàng']);

/**
 * Route liên quan Hậu mãi
 */
Route::group('refund', function () {
    //Danh sách hậu mãi
    Route::get('list', 'v1.order.RefundOrder/getRefundList')->option(['real_name' => 'Danh sách đơn hậu mãi']);
    //Người bán đồng ý hoàn tiền, chờ người dùng trả hàng
    Route::get('agree/:id', 'v1.order.RefundOrder/agreeExpress')->option(['real_name' => 'Người bán đồng ý hoàn tiền, chờ người dùng trả hàng']);
    //Ghi chú đơn đổi trả
    Route::put('remark/:id', 'v1.order.RefundOrder/remark')->option(['real_name' => 'Ghi chú đơn đổi trả']);
    //Biểu mẫu hoàn tiền đơn đổi trả
    Route::get('refund/:id', 'v1.order.RefundOrder/refund')->option(['real_name' => 'Biểu mẫu hoàn tiền đơn đổi trả']);
    //Hoàn tiền đơn đổi trả
    Route::put('refund/:id', 'v1.order.RefundOrder/refundPrice')->option(['real_name' => 'Hoàn tiền đơn đổi trả']);
    //Lấy bảng form không hoàn tiền
    Route::get('no_refund/:id', 'v1.order.RefundOrder/noRefund')->option(['real_name' => 'Lấy biểu mẫu từ chối hoàn tiền']);
    //Sửa lý do từ chối hoàn tiền
    Route::put('no_refund/:id', 'v1.order.RefundOrder/refuseRefund')->option(['real_name' => 'Sửa lý do từ chối hoàn tiền']);
    //Thông tin đơn hoàn tiền
    Route::get('info/:uni', 'v1.order.RefundOrder/getRefundInfo')->option(['real_name' => 'Lấy chi tiết đơn hoàn tiền']);
})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'refund', 'mark_name' => 'Đơn hoàn tiền']);
