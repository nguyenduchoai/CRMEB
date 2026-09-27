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

namespace app\dao\shipping;


use app\dao\BaseDao;
use app\model\shipping\ShippingTemplatesNoDelivery;
use app\model\shipping\SystemCity;

/**
 * Class ShippingTemplatesFreeCityDao
 * @package app\dao\shipping
 */
class ShippingTemplatesNoDeliveryCityDao extends BaseDao
{
    /**
     * Alias bảng chính
     * @var string
     */
    protected $alias = 'a';

    /**
     * Alias bảng phụ
     * @var string
     */
    protected $joinAlis = 'c';

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return ShippingTemplatesNoDelivery::class;
    }

    /**
     * Thiết lập model join bảng liên kết
     * @return string
     */
    protected function setJoinModel(): string
    {
        return SystemCity::class;
    }

    /**
     * Model liên kết
     * @param string $alias
     * @param string $join_alias
     * @return \crmeb\basic\BaseModel
     */
    protected function getModel(string $key = 'province_id', string $join = 'LEFT')
    {
        /** @var SystemCity $city */
        $city = app()->make($this->setJoinModel());
        $name = $city->getName();
        return parent::getModel()->join($name . ' ' . $this->joinAlis, $this->alias . '.' . $key . ' = ' . $this->joinAlis . '.city_id', $join)->alias($this->alias);
    }

    /**
     * Lấy danh sách miễn phí vận chuyển theo điều kiện chỉ định
     * @param array $where
     * @return mixed
     */
    public function getUniqidList(array $where, bool $group = true)
    {
        return $this->getModel($group ? 'province_id' : 'city_id')->when(isset($where['uniqid']), function ($query) use ($where) {
            $query->where($this->alias . '.uniqid', $where['uniqid']);
        })->when(isset($where['province_id']), function ($query) use ($where) {
            $query->where($this->alias . '.province_id', $where['province_id']);
        })->when($group, function ($query) {
            $query->group($this->alias . '.province_id');
        })->field($this->alias . '.province_id,' . $this->joinAlis . '.name,' . $this->alias . '.city_id')->select()->toArray();
    }
}
