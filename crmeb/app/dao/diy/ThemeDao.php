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
namespace app\dao\diy;

use app\dao\BaseDao;
use app\model\diy\Theme;

/**
 * Chủ đề tùy chỉnh
 * @author wuhaotian
 * @email 442384644@qq.com
 * @date 2025/12/18
 */
class ThemeDao extends BaseDao
{
    /**
     * Lấy tên lớp model
     * @return string
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/18
     */
    protected function setModel(): string
    {
        return Theme::class;
    }

    /**
     * Truy vấn đối tượng model theo điều kiện
     * @param $where
     * @return \crmeb\basic\BaseModel
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/18
     */
    public function getConditionModel($where)
    {
        return $this->getModel()->where('is_del', 0)
            ->when(isset($where['title']) && $where['title'] !== '', function ($query) use ($where) {
                $query->where('title|info', 'like', '%' . $where['title'] . '%');
            })->when(isset($where['is_del']) && $where['is_del'] !== '', function ($query) use ($where) {
                $query->where('is_del', $where['is_del']);
            })->when(isset($where['is_use']) && $where['is_use'] !== '', function ($query) use ($where) {
                $query->where('is_use', $where['is_use']);
            })->when(isset($where['type']) && $where['type'] !== '', function ($query) use ($where) {
                $query->where('type', $where['type']);
            })->when(isset($where['page_type']) && $where['page_type'] !== '', function ($query) use ($where) {
                $query->where('page_type', $where['page_type']);
            });
    }

    /**
     * Lấy danh sách chủ đề tùy chỉnh
     * @param $where
     * @param $field
     * @param int $page
     * @param int $limit
     * @param string $order
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/18
     */
    public function themeList($where, $field, $page = 0, $limit = 0, $order = 'id desc')
    {
        return $this->getConditionModel($where)
            ->field($field)
            ->order($order)
            ->when($page != 0, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })->select()->toArray();
    }

    /**
     * Lấy số lượng chủ đề tùy chỉnh
     * @param $where
     * @return int
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/18
     */
    public function themeCount($where)
    {
        return $this->getConditionModel($where)->count();
    }
}
