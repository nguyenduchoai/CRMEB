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
namespace crmeb\services\pay;

use EasyWeChat\Payment\Order;
use crmeb\basic\BaseStorage;

/**
 * Class BasePay
 * @package crmeb\services\pay
 */
abstract class BasePay extends BaseStorage
{
    /**
     * @var string
     */
    protected $payType;

    /**
     * Đặt loại thanh toán
     * @param string $type
     * @return $this
     */
    public function setPayType(string $type)
    {
        $this->payType = $type;
        return $this;
    }

    /**
     * Đặt loại thanh toán
     * @param string $type
     * @return $this
     */
    public function authSetPayType()
    {
        if (!$this->payType) {
            if (request()->isPc()) {
                $this->payType = Order::NATIVE;
            }
            if (request()->isApp()) {
                $this->payType = Order::APP;
            }
            if (request()->isRoutine() || request()->isWechat()) {
                $this->payType = Order::JSAPI;
            }
            if (request()->isH5()) {
                $this->payType = 'h5';
            }
        }
    }


}
