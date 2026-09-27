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

namespace app\dao\order;


use app\dao\BaseDao;
use app\model\order\StoreOrder;
use app\model\order\StoreOrderCartInfo;

/**
 *
 * Class StoreOrderStoreOrderCartInfoDao
 * @package app\dao\order
 */
class StoreOrderStoreOrderCartInfoDao extends BaseDao
{

    protected $alias = 'a';

    protected $joinAlis = 'c';

    /**
     * Thiết lập model bảng chính
     * @return string
     */
    protected function setModel(): string
    {
        return StoreOrder::class;
    }

    /**
     * Thiết lập model bảng liên kết
     * @return string
     */
    protected function setJoinModel(): string
    {
        return StoreOrderCartInfo::class;
    }

    /**
     * Thiết lập model
     * @return \crmeb\basic\BaseModel
     */
    public function getModel()
    {
        $name = app()->make($this->setJoinModel())->getName();
        return parent::getModel()->alias($this->alias)->join($name . ' ' . $this->joinAlis, $this->alias . '.id =' . $this->joinAlis . '.oid');
    }

    /**
     * Lấy id sản phẩm người dùng đã mua
     * @param array $where
     * @return array
     */
    public function getUserCartProductIds(array $where)
    {
        return $this->getModel()->when(isset($where['uid']), function ($query) use ($where) {
            $query->where($this->alias . '.uid', $where['uid']);
        })->column($this->joinAlis . '.product_id');
    }
}
