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
declare (strict_types = 1);

namespace app\dao\agent;

use app\dao\BaseDao;
use app\model\agent\AgentLevel;

/**
 * Class AgentLevelDao
 * @package app\dao\agent
 */
class AgentLevelDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return AgentLevel::class;
    }

    /**
     * Lấy tất cả hạng cộng tác viên
     * @param array $where
     * @param string $field
     * @param array $with
     * @param int $page
     * @param int $limit
     * @param int $grade
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where = [], string $field = '*', array $with = [], int $page = 0, int $limit = 0, $grade = 0)
    {
        return $this->search($where, false)->when($grade, function ($query) use ($grade) {
            $query->where('grade', '>=', $grade);
        })->field($field)->when($with, function ($query) use ($with) {
            $query->with($with);
        })->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order('grade asc,id desc')->select()->toArray();
    }
}
