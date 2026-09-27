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

namespace crmeb\services\express;


use crmeb\basic\BaseManager;
use crmeb\services\AccessTokenServeService;
use think\Container;
use think\facade\Config;

/**
 * Class Express
 * @package crmeb\services\express
 * @mixin \crmeb\services\express\storage\Express
 */
class Express extends BaseManager
{
    /**
     * Tên space
     * @var string
     */
    protected $namespace = '\\crmeb\\services\\express\\storage\\';

    /**
     * Driver mặc định
     * @return mixed
     */
    protected function getDefaultDriver()
    {
        return Config::get('express.default', 'express');
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
        $handleAccessToken = new AccessTokenServeService($this->config['account'] ?? '', $this->config['secret'] ?? '');
        $handle = Container::getInstance()->invokeClass($class, [$this->name, $handleAccessToken, $this->configFile]);
        $this->config = [];
        return $handle;
    }
}
