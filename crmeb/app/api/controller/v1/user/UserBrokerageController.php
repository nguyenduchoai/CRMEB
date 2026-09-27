<?php

namespace app\api\controller\v1\user;

use app\Request;
use app\services\user\UserBrokerageServices;

class UserBrokerageController
{
    /**
     * UserBrokerageController constructor.
     * @param UserBrokerageServices $services
     */
    public function __construct(UserBrokerageServices $services)
    {
        $this->services = $services;
    }

    /**
     * Dữ liệu giới thiệu    Hoa hồng hôm qua   Số tiền đã rút lũy kế  Hoa hồng hiện tại
     * @param Request $request
     * @return mixed
     */
    public function commission(Request $request)
    {
        $uid = (int)$request->uid();
        return app('json')->success($this->services->commission($uid));
    }

    /**
     * Xếp hạng hoa hồng
     * @param Request $request
     * @return mixed
     */
    public function brokerageRank(Request $request)
    {
        $data = $request->getMore([
            ['page', ''],
            ['limit'],
            ['type']
        ]);
        $uid = (int)$request->uid();
        return app('json')->success($this->services->brokerageRank($uid, $data['type']));
    }
}
