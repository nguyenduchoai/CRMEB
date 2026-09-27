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

namespace crmeb\services\sms;

use crmeb\basic\BaseManager;
use crmeb\services\AccessTokenServeService;
use crmeb\services\sms\storage\yihaotong;
use think\Container;
use think\facade\Config;


/**
 * Class Sms1
 * @package crmeb\services\sms
 * @mixin yihaotong
 */
class Sms extends BaseManager
{

    /**
     * Tên space
     * @var string
     */
    protected $namespace = '\\crmeb\\services\\sms\\storage\\';

    /**
     * Driver mặc định
     * @return mixed
     */
    protected function getDefaultDriver()
    {
        return Config::get('sms.default');
    }

    /**
     * Lấy instance của class
     * @param $class
     * @return mixed|void
     */
    protected function invokeClass($class)
    {
        if (!class_exists($class)) {
            throw new \RuntimeException('class not exists: ' . $class);
        }
        $this->getConfigFile();

        if (!$this->config) {
            $this->config = Config::get($this->configFile . '.stores.' . $this->name, []);
        }

        $handleAccessToken = new AccessTokenServeService($this->config['sms_account'] ?? '', $this->config['sms_token'] ?? '');
        $handle = Container::getInstance()->invokeClass($class, [$this->name, $handleAccessToken, $this->configFile, $this->config]);
        $this->config = [];
        return $handle;
    }
}
