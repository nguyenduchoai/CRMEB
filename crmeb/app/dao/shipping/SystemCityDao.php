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
use app\model\shipping\SystemCity;

/**
 * Dữ liệu thành phố
 * Class SystemCityDao
 * @package app\dao\shipping
 */
class SystemCityDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return SystemCity::class;
    }

    /**
     * Lấy danh sách dữ liệu thành phố
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCityList(array $where, string $field = '*')
    {
        return $this->search($where)->field($field)->select()->toArray();
    }

    /**
     * Lấy dữ liệu thành phố, trả về dạng mảng
     * @param array $where
     * @param string $field
     * @param string $key
     * @return array
     */
    public function getCityArray(array $where, string $field, string $key)
    {
        return $this->search($where)->column($field, $key);
    }

    /**
     * Xóa id thành phố cấp trên và thành phố hiện tại
     * @param int $cityId
     * @return bool
     * @throws \Exception
     */
    public function deleteCity(int $cityId)
    {
        return $this->getModel()->where('city_id', $cityId)->whereOr('parent_id', $cityId)->delete();
    }

    /**
     * Lấy giá trị lớn nhất của city_id
     * @return mixed
     */
    public function getCityIdMax()
    {
        return $this->getModel()->max('city_id');
    }

    /**
     * Lấy lựa chọn thành phố của mẫu phí vận chuyển
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getShippingCity()
    {
        return $this->getModel()->with('children')->where('parent_id', 0)->order('id asc')->select()->toArray();
    }

    /**
     * Lấy danh sách đầy đủ dữ liệu thành phố
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/04/10
     */
    public function fullList($field = '*')
    {
        return $this->getModel()->order('id asc')->field($field)->select()->toArray();
    }
}
