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

namespace app\adminapi\controller\v1\statistic;


use app\adminapi\controller\AuthController;
use app\services\statistic\ProductStatisticServices;
use think\facade\App;

/**
 * Class ProductStatistic
 * @package app\adminapi\controller\v1\statistic
 */
class ProductStatistic extends AuthController
{
    /**
     * ProductStatistic constructor.
     * @param App $app
     * @param ProductStatisticServices $services
     */
    public function __construct(App $app, ProductStatisticServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Thông tin cơ bản sản phẩm
     * @return mixed
     */
    public function getBasic()
    {
        $where = $this->request->getMore([
            ['data', '', '', 'time']
        ]);
        return app('json')->success($this->services->getBasic($where));
    }

    /**
     * Xu hướng sản phẩm
     * @return mixed
     */
    public function getTrend()
    {
        $where = $this->request->getMore([
            ['data', '', '', 'time']
        ]);
        $where['time'] = $this->getDay($where['time']);
        return app('json')->success($this->services->getTrend($where));
    }

    /**
     * Xếp hạng sản phẩm
     * @return mixed
     */
    public function getProductRanking()
    {
        $where = $this->request->getMore([
            ['data', '', '', 'time'],
            ['sort', '']
        ]);
        $where['time'] = $this->getDay($where['time']);
        return app('json')->success($this->services->getProductRanking($where));
    }

    /**
     * Xuất
     * @return mixed
     */
    public function getExcel()
    {
        $where = $this->request->getMore([
            ['data', '', '', 'time']
        ]);
        $where['time'] = $this->getDay($where['time']);
        return app('json')->success($this->services->getTrend($where, true));
    }

    /**
     * Định dạng thời gian
     * @param $time
     * @return string
     */
    public function getDay($time)
    {
        if (strstr($time, '-') !== false) {
            [$startTime, $endTime] = explode('-', $time);
            if (!$startTime || !$endTime) {
                return date("Y/m/d 00:00:00", strtotime("-30 days", time())) . '-' . date("Y/m/d 23:59:59", time());
            } else {
                return date('Y/m/d 00:00:00', strtotime($startTime)).'-'.date('Y/m/d 23:59:59', strtotime($endTime));
            }
        } else {
            return date("Y/m/d 00:00:00", strtotime("-30 days", time())) . '-' . date("Y/m/d 23:59:59", time());
        }
    }
}
