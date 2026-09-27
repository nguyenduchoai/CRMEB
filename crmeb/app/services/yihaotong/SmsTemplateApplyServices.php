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

namespace app\services\yihaotong;


use app\services\BaseServices;
use crmeb\services\FormBuilder;

/**
 * Mẫu SMS
 * Class SmsTemplateApplyServices
 * @package app\services\message\sms
 */
class SmsTemplateApplyServices extends BaseServices
{
    /**
     * @var FormBuilder
     */
    protected $builder;

    /**
     * SmsTemplateApplyServices constructor.
     * @param FormBuilder $builder
     */
    public function __construct(FormBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Tạo form mẫu SMS
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createSmsTemplateForm()
    {
        $field = [
            $this->builder->input('title', 'Tên mẫu')->placeholder('Tên mẫu, ví dụ: Thanh toán đơn hàng thành công'),
            $this->builder->input('content', 'Nội dung mẫu')->type('textarea')->placeholder('Nội dung mẫu, ví dụ: Sản phẩm bạn mua đã thanh toán thành công, số tiền thanh toán {$pay_price}đ, mã đơn hàng {$order_id}, cảm ơn bạn đã mua hàng! (Lưu ý: không thêm chữ ký SMS vào nội dung mẫu)'),
            $this->builder->radio('type', 'Loại mẫu', 1)->options([['label' => 'Mã xác thực', 'value' => 1], ['label' => 'Thông báo', 'value' => 2], ['label' => 'Quảng bá', 'value' => 3]])
        ];
        return $field;
    }

    /**
     * Lấy mẫu đăng ký SMS
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function getSmsTemplateForm()
    {
        return create_form('Đăng ký mẫu SMS', $this->createSmsTemplateForm(), $this->url('/notify/sms/temp'), 'POST');
    }

}
