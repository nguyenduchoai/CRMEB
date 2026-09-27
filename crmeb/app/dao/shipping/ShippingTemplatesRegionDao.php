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
use app\model\shipping\ShippingTemplatesRegion;

/**
 * Phí vận chuyển chỉ định
 * Class ShippingTemplatesRegionDao
 * @package app\dao\shipping
 */
class ShippingTemplatesRegionDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return ShippingTemplatesRegion::class;
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
    public function getShippingArray(array $where, string $field, string $key)
    {
        return $this->search($where)->column($field, $key);
    }

    /**
     * Lấy danh sách dữ liệu miễn phí vận chuyển theo id mẫu phí vận chuyển và id thành phố
     * @param array $tempIds
     * @param array $cityId
     * @param string $field
     * @param string $key
     * @return array
     */
    public function getTempRegionList(array $tempIds, array $cityId, string $field = '*', string $key = '*')
    {
        return $this->getModel()->whereIn('temp_id', $tempIds)->whereIn('city_id', $cityId)->order('city_id asc')->column($field, $key);
    }
}
