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
namespace app\adminapi\controller\v1\merchant;

use app\adminapi\controller\AuthController;
use app\services\order\StoreOrderServices;
use app\services\user\UserServices;
use think\facade\App;

/**
 * Đơn hàng xác nhận sử dụng
 * Class SystemVerifyOrder
 * @package app\adminapi\controller\v1\merchant
 */
class SystemVerifyOrder extends AuthController
{
    /**
     * Phương thức khởi tạo
     * SystemVerifyOrder constructor.
     * @param App $app
     * @param StoreOrderServices $services
     */
    public function __construct(App $app, StoreOrderServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy danh sách đơn hàng xác nhận sử dụng
     * return json
     */
    public function list()
    {
        $where = $this->request->getMore([
            ['data', '', '', 'time'],
            ['real_name', ''],
            ['store_id', ''],
            ['type', ''],
            ['field_key', ''],
        ]);
        $data = $this->services->getOrderList($where + ['status' => 6], ['*'], ['store', 'staff']);
        return app('json')->success(['count' => $data['count'], 'data' => $data['data']]);
    }

    /**
     * Không dùng, lấy phần đầu đơn hàng xác nhận sử dụng
     * @return mixed
     */
    public function getVerifyBadge()
    {
        return app('json')->success([]);
    }

    /**
     * Chi tiết người giới thiệu trong danh sách đơn hàng
     * @param $uid
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function order_spread_user($uid)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $spread = [];
        $spread = $userServices->getUserInfo((int)$uid);
        if ($spread) {
            $spread = $spread->toArray();
            $spread['brokerage_pric'] = $spread['brokerage_price'];
            $spread['birthday'] = $spread['birthday'] ? date('Y-m-d', $spread['birthday']) : '';
            $spread['last_time'] = $spread['last_time'] ? date('Y-m-d H:i:s', $spread['last_time']) : '';
        }
        return app('json')->success(['spread' => $spread]);
    }
}
