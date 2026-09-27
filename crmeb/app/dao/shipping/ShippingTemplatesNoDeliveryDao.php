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

namespace app\dao\shipping;


use app\dao\BaseDao;
use app\model\shipping\ShippingTemplatesNoDelivery;

/**
 * Không giao đến
 * Class ShippingTemplatesNoDeliveryDao
 * @package app\dao\shipping
 */
class ShippingTemplatesNoDeliveryDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return ShippingTemplatesNoDelivery::class;
    }

    /**
     * Lấy danh sách mẫu phí vận chuyển và nhóm theo trường chỉ định
     * @param array $where
     * @param string $group
     * @param string $field
     * @param string $key
     * @return mixed
     */
    public function getShippingGroupArray(array $where, string $group, string $field, string $key)
    {
        return $this->search($where)->group($group)->column($field, $key);
    }

    /**
     * Lấy danh sách mẫu phí vận chuyển
     * @param array $where
     * @param string $field
     * @param string $key
     * @return array
     */
    public function getShippingArray(array $where)
    {
        return $this->search($where)->select()->toArray();
    }

    /**
     * Có thuộc khu vực không giao đến hay không
     * @param $tempId
     * @param $cityid
     * @return int
     */
    public function isNoDelivery($tempId, $cityid)
    {
        if (is_array($tempId)) {
            return $this->getModel()->where('temp_id', 'in', $tempId)->where('city_id', $cityid)->column('temp_id');
        } else {
            return $this->getModel()->where('temp_id', $tempId)->where('city_id', $cityid)->count();
        }
    }

}
