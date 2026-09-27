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
declare (strict_types=1);

namespace crmeb\basic;

use think\facade\App;

/**
 * Class cơ sở của controller
 */
abstract class BaseController
{
    /**
     * Instance Request
     * @var \app\Request
     */
    protected $request;

    /**
     * Instance ứng dụng
     * @var \think\App
     */
    protected $app;

    /**
     * Middleware controller
     * @var array
     */
    protected $middleware = [];

    /**
     * @var
     */
    protected $services;

    /**
     * Địa chỉ interface cần ủy quyền
     * @var string[]
     */
    private $authRule = [];
    /**
     * Phương thức khởi tạo
     * @access public
     * @param App $app Đối tượng ứng dụng
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = app('request');
        $this->initialize();
    }

    /**
     * @return mixed
     */
    abstract protected function initialize();


}
