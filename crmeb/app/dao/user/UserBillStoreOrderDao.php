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

namespace app\dao\user;

use app\dao\BaseDao;
use app\model\order\StoreOrder;
use app\model\user\UserBill;

/**
 *
 * Class UserBillStoreOrderDao
 * @package app\dao\user
 */
class UserBillStoreOrderDao extends BaseDao
{

    protected $alias = '';
    protected $join_alis = '';

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return UserBill::class;
    }

    public function joinModel(): string
    {
        return StoreOrder::class;
    }

    /**
     * Model liên kết
     * @param string $alias
     * @param string $join_alias
     * @return \crmeb\basic\BaseModel
     */
    public function getModel(string $table = '', string $alias = 'b', string $join_alias = 'o', $join = 'left')
    {
        $this->alias = $alias;
        $this->join_alis = $join_alias;
        if (!$table) {
            /** @var StoreOrder $storeOrder */
            $storeOrder = app()->make($this->joinModel());
            $table = $storeOrder->getName();
        }
        return parent::getModel()->join($table . ' ' . $join_alias, $alias . '.link_id = ' . $join_alias . '.id', $join)->alias($alias);
    }

    /**
     * Nhóm theo thời gian
     * @param array $where
     * @param array $whereOr
     * @param string $field
     * @param string $group
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function getList(array $where, array $whereOr, array $times, string $field, $page, $limit)
    {
        return $this->getModel()->where($where)->where("FROM_UNIXTIME(b.add_time, '%Y-%m')", 'in', $times)
            ->where(function ($q) use ($whereOr) {
                $q->whereOr($whereOr);
            })
            ->with([
                'user' => function ($query) {
                    $query->field('uid,avatar,nickname')->bind(['avatar' => 'avatar', 'nickname' => 'nickname']);
                }])->field($field)->order('id desc')->page($page, $limit)->select()->toArray();
    }

    /**
     * Nhóm theo thời gian
     * @param array $where
     * @param array $whereOr
     * @param string $field
     * @param string $group
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function getListByGroup(array $where, array $whereOr, string $field, string $group, $page, $limit)
    {
        return $this->getModel()->where($where)->where(function ($q) use ($whereOr) {
            $q->whereOr($whereOr);
        })->field($field)->order($group . ' desc')->group($group)->page($page, $limit)->select()->toArray();
    }

    /**
     * Nhóm theo thời gian
     * @param array $where
     * @param array $whereOr
     * @param string $field
     * @param string $group
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function getListCount(array $where, array $whereOr)
    {
        return $this->getModel()->where($where)->where(function ($q) use ($whereOr) {
            $q->whereOr($whereOr);
        })->count('b.id');
    }
}
