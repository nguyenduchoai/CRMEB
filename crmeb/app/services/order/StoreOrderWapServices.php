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

namespace app\services\order;


use app\dao\order\StoreOrderDao;
use app\services\BaseServices;

/**
 * Class StoreOrderWapServices
 * @package app\services\order
 * @method getOne(array $where, ?string $field = '*', ?array $with = []) Lấy một dòng dữ liệu
 */
class StoreOrderWapServices extends BaseServices
{
    /**
     * StoreOrderWapServices constructor.
     * @param StoreOrderDao $dao
     */
    public function __construct(StoreOrderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy số tiền đơn hàng hôm nay, hôm qua, tháng này
     * @return mixed
     */
    public function getOrderTimeData()
    {
        $where = ['timeKey' => 'add_time', 'paid' => 1, 'refund_status' => 0, 'pid' => 0];
        //Doanh số hôm nay
        $data['todayPrice'] = $this->dao->together($where + ['time' => 'today'], 'pay_price');
        //Số đơn hàng hôm nay
        $data['todayCount'] = $this->dao->count($where + ['time' => 'today']);
        //Doanh số hôm qua
        $data['proPrice'] = $this->dao->together($where + ['time' => 'yesterday'], 'pay_price');
        //Số đơn hàng hôm qua
        $data['proCount'] = $this->dao->count($where + ['time' => 'yesterday']);
        //Doanh số tháng này
        $data['monthPrice'] = $this->dao->together($where + ['time' => 'month'], 'pay_price');
        //Số đơn hàng tháng này
        $data['monthCount'] = $this->dao->count($where + ['time' => 'month']);
        return $data;
    }
    

    /**
     * Dữ liệu thống kê đơn hàng hằng tháng
     * @param array $where
     * @param int $store_id
     * @return array
     */
    public function getOrderDataPriceCount(array $where = [], int $store_id = 0)
    {
        [$page, $limit] = $this->getPageValue();
        return $this->dao->getOrderDataPriceCount($where + ['pid' => 0, 'is_del' => 0, 'paid' => 1, 'refund_status' => [0, 3], 'is_system_del' => 0, 'store_id' => $store_id], ['sum(pay_price) as price', 'count(id) as count', 'FROM_UNIXTIME(add_time, \'%m-%d\') as time'], $page, $limit);
    }

    /**
     * Lấy quản lý đơn hàng trên di động
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getWapAdminOrderList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getOrderList($where, ['*'], $page, $limit, ['pink']);
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $list = $orderServices->tidyOrderList($list);
        foreach ($list as &$item) {
            $refund_num = array_sum(array_column($item['refund'], 'refund_num'));
            $cart_num = 0;
            foreach ($item['_info'] as $items) {
                $cart_num += $items['cart_info']['cart_num'];
            }
            $item['is_all_refund'] = $refund_num == $cart_num ? true : false;
        }
        return $list;
    }

}
