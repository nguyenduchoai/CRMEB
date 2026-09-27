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

namespace crmeb\basic;

use think\facade\Config;
use think\helper\Str;
use think\Container;

/**
 * Class BaseManager
 * @package crmeb\basic
 */
abstract class BaseManager
{
    /**
     * Namespace của driver
     * @var null
     */
    protected $namespace = null;

    /**
     * Cấu hình
     * @var null
     */
    protected $configFile = null;

    /**
     * Cấu hình
     * @var array
     */
    protected $config = [];

    /**
     * Driver
     * @var array
     */
    protected $drivers = [];

    /**
     * Loại driver
     * @var null
     */
    protected $name = null;

    /**
     * BaseManager constructor.
     * @param string|array|int $name Tên driver
     * @param array $config Cấu hình
     */
    public function __construct($name = null, array $config = [])
    {
        $type = null;
        if (is_array($name)) {
            $config = $name;
            $name = null;
        }

        if (is_int($name)) {
            $type = $name;
            $name = null;
        }

        if ($name)
            $this->name = $name;
        if ($type && is_null($this->name)) {
            $this->setHandleType((int)$type - 1);
        }
        $this->config = $config;
    }

    /**
     * Lấy tên file cấu hình
     * @return $this
     */
    protected function getConfigFile()
    {
        if (is_null($this->configFile)) {
            $this->configFile = strtolower((new \ReflectionClass($this))->getShortName());
        }
        return $this;
    }

    /**
     * Đặt handle file
     * @param int $type
     */
    protected function setHandleType(int $type)
    {
        $this->getConfigFile();
        $stores = array_keys(Config::get($this->configFile . '.stores', []));
        $name = $stores[$type] ?? null;
        if (!$name) {
            throw new \RuntimeException($this->configFile . ' type is not used');
        }
        $this->name = $name;
    }

    /**
     * Đặt handle mặc định
     * @return mixed
     */
    abstract protected function getDefaultDriver();

    /**
     * Gọi động
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->driver()->{$method}(...$arguments);
    }

    /**
     * Lấy instance driver
     * @param null|string $name
     * @return mixed
     */
    protected function driver(string $name = null)
    {
        $name = $name ?: $this->name;
        $name = $name ?: $this->getDefaultDriver();

        if (is_null($name)) {
            throw new \InvalidArgumentException(sprintf(
                'Unable to resolve NULL driver for [%s].', static::class
            ));
        }

        return $this->drivers[$name] = $this->getDriver($name);
    }

    /**
     * Lấy instance driver
     * @param string $name
     * @return mixed
     */
    protected function getDriver(string $name)
    {
        return $this->drivers[$name] ?? $this->createDriver($name);
    }

    /**
     * Lấy loại driver
     * @param string $name
     * @return mixed
     */
    protected function resolveType(string $name)
    {
        return $name;
    }

    /**
     * Tạo driver
     *
     * @param string $name
     * @return mixed
     *
     */
    protected function createDriver(string $name)
    {
        $type = $this->resolveType($name);

        $method = 'create' . Str::studly($type) . 'Driver';

        if (method_exists($this, $method)) {
            return $this->$method($name);
        }

        $class = $this->resolveClass($type);
        $this->name = $type;
        return $this->invokeClass($class);
    }


    /**
     * Lấy class driver
     * @param string $type
     * @return string
     */
    protected function resolveClass(string $type): string
    {
        if ($this->namespace || false !== strpos($type, '\\')) {
            $class = false !== strpos($type, '\\') ? $type : $this->namespace . Str::studly($type);
            if (class_exists($class)) {
                return $class;
            }
        }

        throw new \InvalidArgumentException("Driver [$type] not supported.");
    }

    /**
     * Khởi tạo instance class
     * @param $class
     * @return mixed
     */
    protected function invokeClass($class)
    {
        if (!class_exists($class)) {
            throw new \RuntimeException('class not exists: ' . $class);
        }
        $this->getConfigFile();
        $handle = Container::getInstance()->invokeClass($class, [$this->name, $this->config, $this->configFile]);
        $this->config = [];
        return $handle;
    }

}
