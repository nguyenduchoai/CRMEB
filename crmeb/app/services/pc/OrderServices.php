<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace app\services\pc;


use app\services\BaseServices;
use app\services\order\StoreOrderServices;

class OrderServices extends BaseServices
{
    /**
     * Polling trạng thái đơn hàng
     * @param string $order_id
     * @return bool
     */
    public function checkOrderStatus(string $order_id)
    {
        /** @var StoreOrderServices $order */
        $order = app()->make(StoreOrderServices::class);
        $res = $order->count(['order_id' => $order_id, 'paid' => 1]);
        if ($res) return true;
        return false;
    }

    /**
     * Lấy danh sách đơn hàng
     * @param array $where
     * @param array|string[] $field
     * @param array $with
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOrderList(array $where, array $field = ['*'], array $with = [])
    {
        /** @var StoreOrderServices $order */
        $order = app()->make(StoreOrderServices::class);
        $data['list'] = $order->getOrderApiList($where, $field, $with);
        $data['count'] = $order->dao->count($where, false);
        return $data;
    }
}
