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
declare (strict_types=1);

namespace app\services\user;

use app\services\BaseServices;
use app\dao\user\UserBillStoreOrderDao;

/**
 *
 * Class UserBillStoreOrderServices
 * @package app\services\user
 */
class UserBillStoreOrderServices extends BaseServices
{
    /**
     * UserBillStoreOrderServices constructor.
     * @param UserBillStoreOrderDao $dao
     */
    public function __construct(UserBillStoreOrderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * TODO Lấy bản ghi người dùng, tìm theo tháng
     * @param $uid $uid  ID người dùng
     * @param int $page $page Giá trị bắt đầu phân trang
     * @param int $limit $limit Số dòng truy vấn
     * @param string $category $category Loại bản ghi
     * @param string $type $type Danh mục bản ghi
     * @return mixed
     */
    public function getRecordList($uid, $uids, $category = 'now_money', $type = 'brokerage')
    {
        $where = $whereOr1 = $whereOr2 = [];
        $where['b.category'] = $category;
        $where['o.refund_status'] = 0;
        $where['b.take'] = 0;
        $field = "FROM_UNIXTIME(b.add_time, '%Y-%m') as time";

        $whereOr1 = [
            ['b.uid', '=', $uid],
            ['b.type', '=', $type]
        ];
        $whereOr2 = [
            ['b.uid', 'IN', $uids],
            ['b.type', '=', 'pay_money']
        ];
        [$page, $limit] = $this->getPageValue();
        return $this->dao->getListByGroup($where, [$whereOr1, $whereOr2], $field, 'time', $page, $limit);
    }

    /**
     * Lấy tổng số bản ghi trả hoa hồng của đơn hàng
     * @param $uid
     * @param $uids
     * @param string $category
     * @param string $type
     * @return mixed
     */
    public function getRecordOrderCount($uid, $uids, $category = 'now_money', $type = 'brokerage')
    {
        $where = $whereOr1 = $whereOr2 = [];
        $where['b.category'] = $category;
        $where['o.refund_status'] = 0;
        $where['b.take'] = 0;

        $whereOr1 = [
            ['b.uid', '=', $uid],
            ['b.type', '=', $type]
        ];
        $whereOr2 = [
            ['b.uid', 'IN', $uids],
            ['b.type', '=', 'pay_money']
        ];
        return $this->dao->getListCount($where, [$whereOr1, $whereOr2]);
    }

    /**
     * TODO Lấy bản ghi trả hoa hồng của đơn hàng
     * @param $uid
     * @param int $addTime
     * @param string $category
     * @param string $type
     * @return mixed
     */
    public function getRecordOrderListDraw($uid, $uids, $addTime = [], $category = 'now_money', $type = 'brokerage')
    {
        if(!$addTime) return [];
        $where = $whereOr1 = $whereOr2 = [];
        $where['b.category'] = $category;
        $where['o.refund_status'] = 0;
        $where['b.take'] = 0;

        $whereOr1 = [
            ['b.uid', '=', $uid],
            ['b.type', '=', $type]
        ];
        $whereOr2 = [
            ['b.uid', 'IN', $uids],
            ['b.type', '=', 'pay_money']
        ];
        $field = "o.id,o.uid,o.order_id,FROM_UNIXTIME(b.add_time, '%Y-%m-%d %H:%i') as time,b.number,b.type,FROM_UNIXTIME(b.add_time, '%Y-%m') as time_key";
        return $this->dao->getList($where, [$whereOr1, $whereOr2],$addTime, $field, 0, 0);
    }
}
