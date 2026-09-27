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

namespace app\dao\activity\advance;

use app\dao\BaseDao;
use app\model\activity\advance\StoreAdvance;

/**
 * Sản phẩm đặt trước
 * Class StoreAdvanceDao
 * @package app\dao\activity
 */
class StoreAdvanceDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreAdvance::class;
    }

    /**
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/20
     */
    public function getList(array $where, int $page = 0, int $limit = 0)
    {
        return $this->search($where, false)
            ->when($where['time_type'], function ($query) use ($where) {
                if ($where['time_type'] == 1) $query->whereTime('start_time', '>', time());
                if ($where['time_type'] == 2) $query->whereTime('start_time', '<=', time())->whereTime('stop_time', '>=', time());
                if ($where['time_type'] == 3) $query->whereTime('stop_time', '<', time());
            })
            ->when($page != 0 && $limit != 0, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })->with(['product'])->order('sort desc,id desc')->select()->toArray();
    }

    /**
     * @param array $where
     * @return int
     * @throws \ReflectionException
     */
    public function getCount(array $where)
    {
        return $this->search($where, false)
            ->when($where['time_type'], function ($query) use ($where) {
                if ($where['time_type'] == 1) $query->whereTime('start_time', '>', time());
                if ($where['time_type'] == 2) $query->whereTime('start_time', '<=', time())->whereTime('stop_time', '>=', time());
                if ($where['time_type'] == 3) $query->whereTime('stop_time', '<', time());
            })->count();
    }


    /**
     * Lấy trạng thái bật/tắt của sản phẩm đặt trước
     * @param array $ids
     * @return int
     */
    public function getAdvanceStatus(array $ids)
    {
        return $this->getModel()->whereIn('product_id', $ids)->where('is_del', 0)->where('status', 1)->count();
    }
}
