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

namespace app\dao\system\store;


use app\dao\BaseDao;
use app\model\system\store\SystemStore;

/**
 * DAO cửa hàng
 * Class SystemStoreDao
 * @package app\dao\system\store
 */
class SystemStoreDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return SystemStore::class;
    }

    /**
     * Tính toán sắp xếp theo kinh độ vĩ độ
     * @param string $latitude
     * @param string $longitude
     * @return string
     */
    public function distance(string $latitude, string $longitude)
    {
        return "(round(6367000 * 2 * asin(sqrt(pow(sin(((latitude * pi()) / 180 - ({$latitude} * pi()) / 180) / 2), 2) + cos(({$latitude} * pi()) / 180) * cos((latitude * pi()) / 180) * pow(sin(((longitude * pi()) / 180 - ({$longitude} * pi()) / 180) / 2), 2))))) AS distance";
    }

    /**
     * Lấy
     * @param array $where
     * @param int $page
     * @param int $limit
     * @param string $latitude
     * @param string $longitude
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStoreList(array $where, array $field, int $page = 0, int $limit = 0, string $latitude = '', string $longitude = '')
    {
        return $this->search($where)->when($longitude && $latitude, function ($query) use ($longitude, $latitude) {
            $query->field(['*', $this->distance($latitude, $longitude)])->order('distance ASC');
        })->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->field($field)->order('id desc')->select()->toArray();
    }

    /**
     * Lấy cửa hàng không phân trang
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStore(array $where)
    {
        return $this->search($where)->order('add_time DESC')->field(['id', 'name'])->select()->toArray();
    }
}
