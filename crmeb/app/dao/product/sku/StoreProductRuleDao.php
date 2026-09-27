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

namespace app\dao\product\sku;

use app\dao\BaseDao;
use app\model\product\sku\StoreProductRule;

/**
 * Class StoreProductRuleDao
 * @package app\dao\product\sku
 */
class StoreProductRuleDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreProductRule::class;
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where = [], int $page = 0, int $limit = 0)
    {
        return $this->search($where)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order('id desc')->select()->toArray();
    }

    /**
     * Xóa dữ liệu
     * @param string $ids
     * @throws \Exception
     */
    public function del(string $ids)
    {
        return $this->getModel()->whereIn('id', $ids)->delete();
    }


    /**
     * @param array $where
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductRuleList(array $where, $field = "*"): array
    {

        return $this->search($where)->field($field)->select()->toArray();
    }
}
