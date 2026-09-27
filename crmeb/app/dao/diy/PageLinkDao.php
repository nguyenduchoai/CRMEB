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

namespace app\dao\diy;

use app\dao\BaseDao;
use app\model\diy\PageLink;

/**
 * Liên kết trang
 * Class PageLinkDao
 * @package app\dao\diy
 */
class PageLinkDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return PageLink::class;
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, string $field = '*', int $page = 0, int $limit = 0)
    {
        $where['no_model'] = sys_config('model_checkbox', ['seckill', 'bargain', 'combination']);
        return $this->search($where)->field($field)
            ->when(isset($where['cate_ids']), function ($query) use ($where) {

            })->when($page && $limit, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })->order('sort desc,id asc')->select()->toArray();
    }
}
