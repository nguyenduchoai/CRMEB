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
namespace app\adminapi\controller\v1\system;

use think\facade\App;
use app\services\system\log\ClearServices;
use app\adminapi\controller\AuthController;

/**
 * Controller trang chủ
 * Class Clear
 * @package app\admin\controller
 *
 */
class Clear extends AuthController
{
    public function __construct(App $app, ClearServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Làm mới cache dữ liệu
     */
    public function refresh_cache()
    {
        $this->services->refresCache();
        return app('json')->success('Làm mới bộ nhớ đệm dữ liệu thành công');
    }


    /**
     * Xóa log
     */
    public function delete_log()
    {
        $this->services->deleteLog();
        return app('json')->success('Xóa thành công');
    }
}


