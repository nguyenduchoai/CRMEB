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

namespace app\adminapi\controller\v1\serve;


use app\adminapi\controller\AuthController;
use app\services\serve\ServeServices;
use app\services\shipping\ExpressServices;
use think\facade\App;

/**
 * Dịch vụ vận chuyển nền tảng Yihaotong
 * Class Export
 * @package app\adminapi\controller\v1\serve
 */
class Export extends AuthController
{

    /**
     * Export constructor.
     * @param App $app
     * @param ExpressServices $services
     */
    public function __construct(App $app, ExpressServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Đơn vị vận chuyển
     * @return mixed
     */
    public function getExportAll()
    {
        return app('json')->success($this->services->expressList());
    }

    /**
     *
     * Lấy thông tin vận đơn
     * @param string $com
     * @return mixed
     */
    public function getExportTemp(ServeServices $services)
    {
        [$com] = $this->request->getMore([
            ['com', ''],
        ], true);
        return app('json')->success($services->express()->temp($com));
    }

    /**
     * Có mở in vận đơn điện tử không
     * @return mixed
     */
    public function dumpIsOpen(ServeServices $services)
    {
        $userInfo = $services->user()->getUser();
        $res = false;
        if ($userInfo['dump']['open']) {
            $res = true;
            if (!sys_config('config_export_siid')
                && !sys_config('config_export_com')
                && !sys_config('config_export_to_name')
                && !sys_config('config_export_to_tel')
                && !sys_config('config_export_to_address')
            ) {
                $res = false;
            }
        }
        return app('json')->success(['isOpen' => $res]);
    }

    /**
     * @param ServeServices $services
     * @return \think\Response
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/15
     */
    public function getShipmentOrderList(ServeServices $services)
    {
        $where = $this->request->getMore([
            ['page', 1],
            ['limit', 10],
        ]);

        return app('json')->success($services->express()->getShipmentOrderList($where));
    }
}
