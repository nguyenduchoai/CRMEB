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

namespace app\dao\system\config;


use app\dao\BaseDao;
use app\model\system\config\SystemConfigTab;

/**
 * Danh mục cấu hình
 * Class SystemConfigTabDao
 * @package app\dao\system\config
 */
class SystemConfigTabDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return SystemConfigTab::class;
    }

    /**
     * Lấy danh mục cấu hình
     * @param array $where
     * @param array $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getConfigTabAll(array $searchWhere, array $field = ['*'], array $where = [])
    {
        return $this->search($searchWhere)->when(count($where), function ($query) use ($where) {
            $query->where($where);
        })->field($field)->order('sort desc,id asc')->select()->toArray();
    }

    /**
     * Danh sách danh mục cấu hình
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getConfgTabList(array $where, int $page, int $limit)
    {
        return $this->search($where)->order('sort desc,id asc')->select();
    }

}
