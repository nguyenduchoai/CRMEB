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

namespace app\dao\product\product;

use app\dao\BaseDao;
use app\model\product\product\StoreVisit;

/**
 * Class StoreVisitDao
 * @package app\dao\product\product
 */
class StoreVisitDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreVisit::class;
    }

    /**
     *
     * @param int $uid
     * @return array
     */
    public function getUserVisitProductId(int $uid)
    {
        return $this->getModel()->where('uid', $uid)->column('product_id');
    }

    public function getSum($where, $field)
    {
        return $this->search($where)->sum($field);
    }

    /**
     * Xu hướng sản phẩm
     * @param $time
     * @param $timeType
     * @param $str
     * @return mixed
     */
    public function getProductTrend($time, $timeType, $str)
    {
        return $this->getModel()->where(function ($query) use ($time) {
            if ($time[0] == $time[1]) {
                $query->whereDay('add_time', $time[0]);
            } else {
                $time[1] = date('Y/m/d', strtotime($time[1]) + 86400);
                $query->whereTime('add_time', 'between', $time);
            }
        })->field("FROM_UNIXTIME(add_time,'$timeType') as days,$str as num")->group('days')->select()->toArray();
    }
}
