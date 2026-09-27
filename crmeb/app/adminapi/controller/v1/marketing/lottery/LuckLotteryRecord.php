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
declare (strict_types=1);

namespace app\adminapi\controller\v1\marketing\lottery;

use app\adminapi\controller\AuthController;
use app\services\activity\lottery\LuckLotteryRecordServices;
use think\facade\App;

/**
 * Lịch sử trúng thưởng
 * Class LuckLotteryRecord
 * @package app\controller\admin\v1\marketing\lottery
 */
class LuckLotteryRecord extends AuthController
{

    /**
     * LuckLotteryRecord constructor.
     * @param App $app
     * @param LuckLotteryRecordServices $services
     */
    public function __construct(App $app, LuckLotteryRecordServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách lịch sử quay thưởng
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->postMore([
            ['type', ''],
            ['keyword', ''],
            ['time', ''],
            ['lottery_id', ''],
        ]);
        return app('json')->success($this->services->getList($where));
    }

    /**
     * Giao hàng cho người trúng thưởng
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function deliver($id)
    {
        $data = $this->request->postMore([
            ['deliver_name', ''],
            ['deliver_number', ''],
            ['mark', ''],
        ]);
        if (!$id) {
            return app('json')->fail(100100);
        }
        $this->services->setDeliver((int)$id, $data);
        return app('json')->success($this->services->setDeliver((int)$id, $data) ? 100014 : 100015);
    }
}
