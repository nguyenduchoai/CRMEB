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

namespace app\dao\user;

use app\dao\BaseDao;
use app\model\user\UserLabel;

/**
 *
 * Class UserLabelDao
 * @package app\dao\user
 */
class UserLabelDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return UserLabel::class;
    }

    /**
     * Lấy danh sách
     * @param string $field
     * @param int $page
     * @param int $limit
     * @param array $where
     * @param array $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(int $page = 0, int $limit = 0, array $where = [], array $field = ['*']): array
    {
        return $this->search($where)->with(['cateName'])->when(isset($where['label_cate']) && $where['label_cate'], function ($query) use ($where) {
            $query->where('label_cate', $where['label_cate']);
        })->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->field($field)->select()->toArray();
    }
}
