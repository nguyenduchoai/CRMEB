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

namespace app\services\serve;


use app\services\BaseServices;
use crmeb\services\copyproduct\CopyProduct;
use crmeb\services\express\Express;
use crmeb\services\FormBuilder;
use crmeb\services\invoice\Invoice;
use crmeb\services\printer\Printer;
use crmeb\services\serve\Serve;
use crmeb\services\sms\Sms;
use think\facade\Config;

/**
 * Cổng service nền tảng
 * Class ServeServices
 * @package crmeb\services
 */
class ServeServices extends BaseServices
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
     * Lấy cấu hình
     * @param array $config
     * @return array
     */
    public function getConfig(array $config = [])
    {
        return array_merge([
            'account' => sys_config('sms_account'),
            'secret' => sys_config('sms_token')
        ], $config);
    }


    /**
     * Lấy cấu hình gửi SMS theo loại
     * @param $type
     * @param array $configDefault
     * @return array
     */
    protected function getTypeConfig($type, array $configDefault = [])
    {
        if (!$type) {
            $type = Config::get('sms.default', '');
        }
        $config = Config::get('sms.stores.' . $type);
        foreach ($config as $key => &$item) {
            if (empty($item)) {
                $item = sys_config($key);
            }
        }
        if ($configDefault) {
            $config = array_merge($config, $configDefault);
        }
        return $config;
    }

    /**
     * SMS
     * @param string|null $type
     * @param array $config
     * @return Sms
     */
    public function sms(string $type = null, array $config = [])
    {
        return app()->make(Sms::class, [$type, $this->getTypeConfig($type, $config)]);
    }

    /**
     * Sao chép sản phẩm
     * @param string|null $type
     * @param array $config
     * @return CopyProduct
     */
    public function copy(string $type = null, array $config = [])
    {
        return app()->make(CopyProduct::class, [$type, $this->getConfig($config)]);
    }

    /**
     * Vận đơn điện tử
     * @param array $config
     * @return Express
     */
    public function express(array $config = [])
    {
        return app()->make(Express::class, [$this->getConfig($config)]);
    }

    /**
     * In biên lai
     * @param array $config
     * @return Express
     */
    public function orderPrint(array $config = [])
    {
        return app()->make(Printer::class, [$this->getConfig($config)]);
    }

    /**
     * Người dùng
     * @param array $config
     * @return Serve
     */
    public function user(array $config = [])
    {
        return app()->make(Serve::class, [$this->getConfig($config)]);
    }

    /**
     * Hóa đơn điện tử
     * @param array $config
     * @return Serve
     */
    public function invoice(array $config = [])
    {
        return app()->make(Invoice::class, [$this->getConfig($config)]);
    }

    /**
     * Lấy mẫu SMS
     * @param int $page
     * @param int $limit
     * @param int $type
     * @return array
     */
    public function getSmsTempsList(int $page, int $limit, int $type)
    {
        $list = $this->sms()->temps($page, $limit, $type);
        foreach ($list['data'] as &$item) {
            $item['templateid'] = $item['temp_id'];
            switch ((int)$item['temp_type']) {
                case 1:
                    $item['type'] = 'Mã xác thực';
                    break;
                case 2:
                    $item['type'] = 'Thông báo';
                    break;
                case 30:
                    $item['type'] = 'SMS marketing';
                    break;
            }
        }
        return $list;
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
            $this->builder->radio('type', 'Loại mẫu', 1)->options([['label' => 'Mã xác thực', 'value' => 1], ['label' => 'Thông báo', 'value' => 2], ['label' => 'Marketing', 'value' => 3]])
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
