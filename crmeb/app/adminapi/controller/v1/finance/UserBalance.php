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
namespace app\adminapi\controller\v1\finance;

use app\adminapi\controller\AuthController;
use app\services\user\UserMoneyServices;
use think\facade\App;

class UserBalance extends AuthController
{
    /**
     * UserBalance constructor.
     * @param App $app
     * @param UserMoneyServices $services
     */
    public function __construct(App $app, UserMoneyServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lịch sử số dư
     * @return mixed
     */
    public function balanceList()
    {
        $where = $this->request->getMore([
            ['time', ''],
            ['trading_type', 0, '', 'type']
        ]);
        $date = $this->services->balanceList($where);
        return app('json')->success($date);
    }

    /**
     * Ghi chú lịch sử số dư
     * @return mixed
     */
    public function balanceRecordRemark($id = 0)
    {
        [$mark] = $this->request->postMore([
            ['mark', '']
        ], true);
        if (!$id) return app('json')->fail(100100);
        if ($mark === '') return app('json')->fail(400106);
        $this->services->recordRemark($id, $mark);
        return app('json')->success(100024);
    }
}
