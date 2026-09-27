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
namespace crmeb\utils;

use think\helper\Str;

/**
 *
 * Class Hook
 * @package crmeb\utils
 */
class Hook
{
    /**
     * Tên class
     * @var string
     */
    protected $namespace;

    /**
     * Tiền tố phương thức
     * @var string
     */
    protected $prefix;

    /**
     * Phương thức khởi tạo
     * Hook constructor.
     * @param string $namespace
     * @param string|null $prefix
     */
    public function __construct(string $namespace, string $prefix = null)
    {
        $this->namespace = $namespace;
        if ($prefix) {
            $this->prefix = $prefix;
        }
    }

    /**
     * Thực thi phương thức mount (gắn)
     * @param string $hookName
     * @param mixed ...$arguments
     * @return bool
     */
    public function listen(string $hookName, ...$arguments)
    {
        if (class_exists($this->namespace)) {
            $handle = app()->make($this->namespace);
            $hookName = Str::studly(($this->prefix ?: '') . ucfirst($hookName));
            if (method_exists($handle, $hookName)) {
                try {
                    return call_user_func_array([$handle, $hookName], $arguments);
                } catch (\Throwable $e) {
                }
            }
        }
        return false;
    }
}
