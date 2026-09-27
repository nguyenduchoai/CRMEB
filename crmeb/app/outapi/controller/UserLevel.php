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
namespace app\outapi\controller;

use app\services\user\OutUserLevelServices;
use think\facade\App;

/**
 * Hạng thành viên
 * Class UserLevel
 * @package app\outapi\controller
 */
class UserLevel extends AuthController
{

    /**
     * UserLevel constructor.
     * @param App $app
     * @param OutUserLevelServices $services
     */
    public function __construct(App $app, OutUserLevelServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách hạng
     * @return void
     */
    public function lst()
    {
        $where = $this->request->getMore([
            ['title', ''],
            ['is_show', ''],
        ]);

        return app('json')->success($this->services->levelList($where));
    }

}