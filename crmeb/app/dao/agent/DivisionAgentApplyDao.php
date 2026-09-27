<?php


namespace app\dao\agent;


use app\dao\BaseDao;
use app\model\agent\DivisionAgentApply;

class DivisionAgentApplyDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return DivisionAgentApply::class;
    }

    /**
     * Danh sách đăng ký làm đại lý
     * @param array $where
     * @param string $field
     * @param array $with
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where = [], int $page = 0, int $limit = 0, string $field = '*', array $with = [])
    {
        return $this->search($where)->field($field)->when($with, function ($query) use ($with) {
            $query->with($with);
        })->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order('id desc')->select()->toArray();
    }
}