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
use app\model\shipping\ShippingTemplatesFree;

/**
 * Miễn phí vận chuyển
 * Class ShippingTemplatesFreeDao
 * @package app\dao\shipping
 */
class ShippingTemplatesFreeDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return ShippingTemplatesFree::class;
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
     * Có đủ điều kiện miễn phí vận chuyển hay không
     * @param $tempId
     * @param $cityid
     * @param $number
     * @param $price
     * @return int
     */
    public function isFree($tempId, $cityid, $number, $price)
    {
        return $this->getModel()->where('temp_id', $tempId)
            ->where('city_id', $cityid)
            ->where('number', '<=', $number)
            ->where('price', '<=', $price)->count();
    }

    /**
     * Danh sách dữ liệu mẫu có miễn phí vận chuyển hay không
     * @param $tempId
     * @param $cityid
     * @param int $price
     * @param string $field
     * @param string $key
     * @return array
     */
    public function isFreeList($tempId, $cityid, $price = 0, string $field = '*', string $key = '')
    {
        return $this->getModel()->where('city_id', $cityid)
            ->when($tempId, function ($query) use ($tempId) {
                if (is_array($tempId)) {
                    $query->whereIn('temp_id', $tempId);
                } else {
                    $query->where('temp_id', $tempId);
                }
            })->when($price, function ($query) use ($price) {
                $query->where('price', '<=', $price);
            })->column($field, $key);
    }
}
