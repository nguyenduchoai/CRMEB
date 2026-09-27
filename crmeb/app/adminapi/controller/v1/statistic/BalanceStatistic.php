<?php

namespace app\adminapi\controller\v1\statistic;

use app\adminapi\controller\AuthController;
use app\services\user\UserMoneyServices;
use think\facade\App;

class BalanceStatistic extends AuthController
{
    /**
     * @param App $app
     * @param UserMoneyServices $services
     */
    public function __construct(App $app, UserMoneyServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Thông tin cơ bản thống kê số dư
     * @return mixed
     */
    public function getBasic()
    {
        $data = $this->services->getBasic();
        return app('json')->success($data);
    }

    /**
     * Biểu đồ xu hướng thống kê số dư
     * @return mixed
     */
    public function getTrend()
    {
        $where = $this->request->getMore([
            ['time', '']
        ]);
        $data = $this->services->getTrend($where);
        return app('json')->success($data);
    }

    /**
     * Nguồn số dư
     * @return mixed
     */
    public function getChannel()
    {
        $where = $this->request->getMore([
            ['time', '']
        ]);
        $data = $this->services->getChannel($where);
        return app('json')->success($data);
    }

    /**
     * Loại số dư
     * @return mixed
     */
    public function getType()
    {
        $where = $this->request->getMore([
            ['time', '']
        ]);
        $data = $this->services->getType($where);
        return app('json')->success($data);
    }
}
