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

namespace app\adminapi\validate\setting;


use think\Validate;

/**
 * Class SystemConfigValidata
 * @package app\adminapi\validate\setting
 */
class SystemConfigValidata extends Validate
{

    protected $regex = ['float_two' => '/^[0-9]+(.[0-9]{1,2})?$/'];
    /**
     * Định nghĩa quy tắc xác thực
     * Định dạng: 'tên trường'    =>    ['quy tắc 1','quy tắc 2'...]
     *
     * @var array
     */
    protected $rule = [
        'site_url' => 'url',
        'store_brokerage_ratio' => 'float|egt:0|elt:100|regex:float_two',
        'store_brokerage_two' => 'float|egt:0|elt:100|regex:float_two',
        'user_extract_min_price' => 'float|gt:0',
        'extract_time' => 'number|between:0,180',
        'replenishment_num' => 'number',
        'store_stock' => 'number',
        'store_brokerage_price' => 'float',
        'integral_ratio' => 'float|egt:0|elt:1000|regex:float_two',
        'order_give_integral' => 'float|egt:0|elt:1000',
        'order_cancel_time' => 'float',
        'order_activity_time' => 'float',
        'order_bargain_time' => 'float',
        'order_seckill_time' => 'float',
        'order_pink_time' => 'float',
        'system_delivery_time' => 'float',
        'store_free_postage' => 'float',
        'integral_rule_number' => 'number|gt:0',
        'express_rule_number' => 'number|gt:0',
        'sign_rule_number' => 'number|gt:0',
        'offline_rule_number' => 'number|gt:0',
        'order_give_exp' => 'number|egt:0',
        'invite_user_exp' => 'number|egt:0',
        'config_export_to_name' => 'chs|length:2,10',
        'config_export_to_tel' => 'mobile|number',
        'config_export_to_address' => 'chsAlphaNum|length:10,100',
        'config_export_siid' => 'alphaNum|length:10,50',
        'service_feedback' => 'length:10,90',
        'thumb_big_height' => 'number|egt:0',
        'thumb_big_width' => 'number|egt:0',
        'thumb_mid_height' => 'number|egt:0',
        'thumb_mid_width' => 'number|egt:0',
        'thumb_small_height' => 'number|egt:0',
        'thumb_small_width' => 'number|egt:0',
        'watermark_opacity' => 'number|between:0,100',
        'watermark_text' => 'chsAlphaNum|length:1,10',
        'watermark_text_size' => 'number|egt:0',
        'watermark_x' => 'number|egt:0',
        'watermark_y' => 'number|egt:0',
    ];

    /**
     * Định nghĩa thông báo lỗi
     * Định dạng: 'tên trường.tên quy tắc'    =>    'thông báo lỗi'
     *
     * @var array
     */
    protected $message = [
        'site_url.url' => 'Vui lòng nhập URL hợp lệ',
        'store_brokerage_ratio.float' => 'Tỷ lệ trả hoa hồng cấp 1 phải là số',
        'store_brokerage_ratio.regex' => 'Tỷ lệ trả hoa hồng cấp 1 tối đa hai chữ số thập phân',
        'store_brokerage_ratio.egt' => 'Tỷ lệ trả hoa hồng cấp 1 phải trong khoảng 0-100',
        'store_brokerage_ratio.elt' => 'Tỷ lệ trả hoa hồng cấp 1 phải trong khoảng 0-100',
        'store_brokerage_two.float' => 'Tỷ lệ trả hoa hồng cấp 2 phải là số',
        'store_brokerage_two.regex' => 'Tỷ lệ trả hoa hồng cấp 2 tối đa hai chữ số thập phân',
        'store_brokerage_two.egt' => 'Tỷ lệ trả hoa hồng cấp 2 phải trong khoảng 0-100',
        'store_brokerage_two.elt' => 'Tỷ lệ trả hoa hồng cấp 2 phải trong khoảng 0-100',
        'replenishment_num.number' => 'Số lượng cần bổ sung hàng phải là số',
        'store_stock.number' => 'Ngưỡng cảnh báo tồn kho phải là số',
        'store_brokerage_two.between' => 'Tỷ lệ trả hoa hồng cấp 2 phải trong khoảng 0-100',
        'user_extract_min_price.float' => 'Số tiền rút tối thiểu chỉ được là số',
        'user_extract_min_price.gt' => 'Số tiền rút tối thiểu phải lớn hơn 0',
        'extract_time.number' => 'Thời gian đóng băng hoa hồng phải trong khoảng 0-180',
        'extract_time.between' => 'Thời gian đóng băng hoa hồng phải trong khoảng 0-180',
        'store_brokerage_price.float' => 'Mức chi tiêu để trở thành CTV phải là số',
        'integral_ratio.float' => 'Tỷ lệ khấu trừ bằng điểm thưởng phải là số',
        'integral_ratio.regex' => 'Tỷ lệ khấu trừ bằng điểm thưởng tối đa hai chữ số thập phân',
        'integral_ratio.egt' => 'Tỷ lệ khấu trừ bằng điểm thưởng phải trong khoảng 0-1000',
        'integral_ratio.elt' => 'Tỷ lệ khấu trừ bằng điểm thưởng phải trong khoảng 0-1000',
        'order_give_integral.float' => 'Điểm thưởng tặng khi đặt hàng phải là số',
        'order_give_integral.egt' => 'Điểm thưởng tặng khi đặt hàng phải trong khoảng 0-1000',
        'order_give_integral.elt' => 'Điểm thưởng tặng khi đặt hàng phải trong khoảng 0-1000',
        'order_cancel_time.float' => 'Thời gian hủy đơn chưa thanh toán đối với sản phẩm thường phải là số',
        'order_activity_time.float' => 'Thời gian hủy đơn chưa thanh toán đối với sản phẩm khuyến mãi phải là số',
        'order_bargain_time.float' => 'Thời gian hủy đơn chưa thanh toán đối với sản phẩm săn giảm giá phải là số',
        'order_pink_time.float' => 'Thời gian hủy đơn chưa thanh toán đối với sản phẩm mua chung phải là số',
        'system_delivery_time.float' => 'Thời gian tự động xác nhận nhận hàng sau khi giao phải là số',
        'store_free_postage.float' => 'Giá trị đơn tối thiểu để miễn phí vận chuyển phải là số',
        'integral_rule_number.number' => 'Hệ số nhân điểm thưởng phải lớn hơn 0',
        'express_rule_number.number' => 'Mức chiết khấu phải lớn hơn 0',
        'sign_rule_number.number' => 'Hệ số nhân điểm thưởng phải lớn hơn 0',
        'offline_rule_number.number' => 'Mức chiết khấu phải lớn hơn 0',
        'order_give_exp.number' => 'Tỷ lệ điểm kinh nghiệm tặng khi đặt hàng phải là số',
        'order_give_exp.egt' => 'Tỷ lệ điểm kinh nghiệm tặng khi đặt hàng phải lớn hơn 0',
        'invite_user_exp.number' => 'Điểm kinh nghiệm tặng khi mời người dùng mới phải là số',
        'invite_user_exp.egt' => 'Điểm kinh nghiệm tặng khi mời người dùng mới phải lớn hơn 0',
        'config_export_to_name.chs' => 'Họ tên người gửi hàng phải là chữ Hán',
        'config_export_to_name.length' => 'Họ tên người gửi hàng phải dài 2-10 ký tự',
        'config_export_to_tel.number' => 'Số điện thoại người gửi hàng phải là số',
        'config_export_to_tel.mobile' => 'Vui lòng điền số điện thoại di động hợp lệ cho người gửi hàng',
        'config_export_to_address.chsAlphaNum' => 'Địa chỉ người gửi hàng chỉ được gồm chữ Hán, chữ cái, chữ số',
        'config_export_to_address.length' => 'Địa chỉ người gửi hàng phải dài 10-100 ký tự',
        'config_export_siid.alphaNum' => 'Mã máy in vận đơn điện tử phải gồm chữ số, chữ cái',
        'config_export_siid.length' => 'Mã máy in vận đơn điện tử phải dài 10-50 ký tự',
        'service_feedback.length' => 'Phản hồi CSKH phải dài 10-90 ký tự',
        'thumb_big_height.number' => 'Kích thước ảnh thu nhỏ cỡ lớn (cao) phải là số',
        'thumb_big_height.egt' => 'Kích thước ảnh thu nhỏ cỡ lớn (cao) phải lớn hơn hoặc bằng 0',
        'thumb_big_width.number' => 'Kích thước ảnh thu nhỏ cỡ lớn (rộng) phải là số',
        'thumb_big_width.egt' => 'Kích thước ảnh thu nhỏ cỡ lớn (rộng) phải lớn hơn hoặc bằng 0',
        'thumb_mid_height.number' => 'Kích thước ảnh thu nhỏ cỡ vừa (cao) phải là số',
        'thumb_mid_height.egt' => 'Kích thước ảnh thu nhỏ cỡ vừa (cao) phải lớn hơn hoặc bằng 0',
        'thumb_mid_width.number' => 'Kích thước ảnh thu nhỏ cỡ vừa (rộng) phải là số',
        'thumb_mid_width.egt' => 'Kích thước ảnh thu nhỏ cỡ vừa (rộng) phải lớn hơn hoặc bằng 0',
        'thumb_small_height.number' => 'Kích thước ảnh thu nhỏ cỡ nhỏ (cao) phải là số',
        'thumb_small_height.egt' => 'Kích thước ảnh thu nhỏ cỡ nhỏ (cao) phải lớn hơn hoặc bằng 0',
        'thumb_small_width.number' => 'Kích thước ảnh thu nhỏ cỡ nhỏ (rộng) phải là số',
        'thumb_small_width.egt' => 'Kích thước ảnh thu nhỏ cỡ nhỏ (rộng) phải lớn hơn hoặc bằng 0',
        'watermark_text.chsAlphaNum' => 'Văn bản watermark chỉ được gồm chữ Hán, chữ cái, chữ số',
        'watermark_text.length' => 'Văn bản watermark phải dài 1-10 ký tự',
        'watermark_text_size.number' => 'Cỡ chữ watermark phải là số',
        'watermark_text_size.egt' => 'Cỡ chữ watermark phải lớn hơn hoặc bằng 0',
        'watermark_x.number' => 'Độ lệch ngang của watermark phải là số',
        'watermark_x.egt' => 'Độ lệch ngang của watermark phải lớn hơn hoặc bằng 0',
        'watermark_y.number' => 'Độ lệch dọc của watermark phải là số',
        'watermark_y.egt' => 'Độ lệch dọc của watermark phải lớn hơn hoặc bằng 0',
    ];

    protected $scene = [

    ];
}
