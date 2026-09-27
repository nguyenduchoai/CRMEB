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

namespace crmeb\services\easywechat;


use crmeb\services\easywechat\miniPayment\ServiceProvider;
use crmeb\services\easywechat\miniScheme\ProgramProvider as ProgramScheme;
use crmeb\services\easywechat\oauth2\wechat\WechatOauth2Provider;
use crmeb\services\easywechat\orderShipping\OrderClient;
use crmeb\services\easywechat\subscribe\ProgramProvider;
use crmeb\services\easywechat\v3pay\PayClient;
use crmeb\services\easywechat\wechatlive\ProgramProvider as LiveProgramProvider;
use crmeb\services\easywechat\wechatTemplate\ProgramProvider as TemplateProvider;
use crmeb\services\easywechat\v3pay\ServiceProvider as V3PayServiceProvider;
use crmeb\services\easywechat\orderShipping\ServiceProvider as OrderServiceProvider;


/**
 * Class Application
 * @package crmeb\services\easywechat
 * @property LiveProgramProvider $wechat_live
 * @property WechatOauth2Provider $oauth2
 * @property PayClient $v3pay
 * @property OrderClient $order_ship
 */
class Application extends \EasyWeChat\Foundation\Application
{

    /**
     * @var string[]
     */
    protected $providersNew = [
        LiveProgramProvider::class,
        WechatOauth2Provider::class,
        ServiceProvider::class,
        ProgramProvider::class,
        V3PayServiceProvider::class,
        \crmeb\services\easywechat\Open3rd\ProgramProvider::class,
        OrderServiceProvider::class,
        TemplateProvider::class,
        ProgramScheme::class
    ];

    /**
     * Application constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->providers = array_merge($this->providers, $this->providersNew);
        parent::__construct($config);
    }

}
