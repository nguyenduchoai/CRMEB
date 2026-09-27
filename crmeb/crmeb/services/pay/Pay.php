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


use crmeb\basic\BaseManager;
use crmeb\services\pay\storage\AliPay;
use crmeb\services\pay\storage\V3WechatPay;
use crmeb\services\pay\storage\WechatPay;
use think\facade\Config;

/**
 * Thanh toán bên thứ ba
 * Class AllinPay
 * @package crmeb\services\pay
 * @mixin WechatPay
 */
class Pay extends BaseManager
{
    /**
     * Tên space
     * @var string
     */
    protected $namespace = '\\crmeb\\services\\pay\\storage\\';

    /**
     * Driver mặc định
     * @return mixed
     */
    protected function getDefaultDriver()
    {
        return Config::get('pay.default', 'wechat_pay');
    }

}
