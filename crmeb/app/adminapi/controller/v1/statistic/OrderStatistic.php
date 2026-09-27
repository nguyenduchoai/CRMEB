<?php

namespace app\adminapi\controller\v1\statistic;

use app\adminapi\controller\AuthController;
use app\services\statistic\OrderStatisticServices;
use think\facade\App;

class OrderStatistic extends AuthController
{
    public function __construct(App $app, OrderStatisticServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Thông tin cơ bản thống kê đơn hàng
     * @return mixed
     */
    public function getBasic()
    {
        $where = $this->request->getMore([
            ['time', '']
        ]);
        $data = $this->services->getBasic($where);
        return app('json')->success($data);
    }

    /**
     * Biểu đồ xu hướng thống kê đơn hàng
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
     * Nguồn đơn hàng
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
     * Loại đơn hàng
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