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

namespace app\api\controller\v2\user;


use app\Request;
use app\services\kefu\service\StoreServiceLogServices;
use app\services\kefu\service\StoreServiceServices;

/**
 * Class StoreService
 * @package app\api\controller\v2\user
 */
class StoreService
{

    /**
     * @var StoreServiceLogServices
     */
    protected $services;

    /**
     * StoreService constructor.
     * @param StoreServiceLogServices $services
     */
    public function __construct(StoreServiceLogServices $services)
    {
        $this->services = $services;
    }

    /**
     * Lịch sử trò chuyện CSKH
     * @param Request $request
     * @param StoreServiceServices $services
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function record(Request $request, StoreServiceServices $services)
    {
        [$uidTo, $limit, $toUid] = $request->getMore([
            [['uidTo', 'd'], 0],
            [['limit', 'd'], 10],
            [['toUid', 'd'], 0],
        ], true);
        $uid = $request->uid();
        return app('json')->success($services->getRecord($uid, $uidTo, $limit, $toUid));
    }
}
