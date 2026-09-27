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

use app\services\user\UserBillServices;
use think\facade\App;
use app\adminapi\controller\AuthController;

/**
 * Class Finance
 * @package app\adminapi\controller\v1\finance
 */
class Finance extends AuthController
{
    /**
     * Finance constructor.
     * @param App $app
     * @param UserBillServices $services
     */
    public function __construct(App $app, UserBillServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Loại bộ lọc
     */
    public function bill_type()
    {
        return app('json')->success($this->services->bill_type());
    }

    /**
     * Lịch sử dòng tiền
     */
    public function list()
    {
        $where = $this->request->getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['nickname', ''],
            ['limit', 20],
            ['page', 1],
            ['type', ''],
        ]);
        return app('json')->success($this->services->getBillList($where));
    }

    /**
     * Lịch sử hoa hồng
     * @return mixed
     */
    public function get_commission_list()
    {
        $where = $this->request->getMore([
            ['nickname', ''],
            ['price_max', ''],
            ['price_min', ''],
            ['sum_number', 'normal'],
            ['brokerage_price', 'normal'],
            ['time', '']
        ]);
        return app('json')->success($this->services->getCommissionList($where));
    }

    /**
     * Thông tin người dùng trong chi tiết hoa hồng
     * @param $id
     * @return mixed
     */
    public function user_info($id)
    {
        return app('json')->success($this->services->user_info((int)$id));
    }

    /**
     * Danh sách lịch sử rút hoa hồng của cá nhân
     */
    public function get_extract_list($id = '')
    {
        if ($id == '') return app('json')->fail(100100);
        $where = $this->request->getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['nickname', '']
        ]);
        $where['category'] = 'now_money';
        $where['type'] = ['brokerage', 'brokerage_user'];
        return app('json')->success($this->services->getBillOneList((int)$id, $where));
    }

}
