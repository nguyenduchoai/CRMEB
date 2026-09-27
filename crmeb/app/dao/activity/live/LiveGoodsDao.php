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
declare (strict_types=1);

namespace app\dao\activity\live;


use app\dao\BaseDao;
use app\model\activity\live\LiveGoods;

/**
 * Class LiveGoodsDao
 * @package app\dao\live
 */
class LiveGoodsDao extends BaseDao
{

    protected function setModel(): string
    {
        return LiveGoods::class;
    }

    /**
     * @param array $where
     * @param string $field
     * @param array $with
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, string $field = '*', array $with = [], int $page, int $limit)
    {
        return $this->search($where)->field($field)->with($with)->page($page, $limit)->order('sort desc,add_time desc')->select()->toArray();
    }

    public function goodsStatusAll()
    {
        return $this->getModel()->where('goods_id', '>', 0)->whereIn('audit_status', [0, 1])->column('id,audit_status', 'goods_id');
    }

    /**
     * @param array $ids
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function goodsList(array $ids)
    {
        return $this->getModel()->whereIn('id', $ids)->where('is_del', 0)->where('audit_status', 2)->select()->toArray();
    }
}
