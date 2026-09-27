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
use app\model\diy\ThemeDownload;

/**
 * Dao bản ghi tải xuống chủ đề
 * @author wuhaotian
 * @email 442384644@qq.com
 * @date 2026/3/10
 */
class ThemeDownloadDao extends BaseDao
{
    /**
     * Lấy tên lớp model
     * @return string
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/3/10
     */
    protected function setModel(): string
    {
        return ThemeDownload::class;
    }

    /**
     * Truy vấn đối tượng model theo điều kiện
     * @param array $where
     * @return \crmeb\basic\BaseModel
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/3/10
     */
    public function getConditionModel(array $where)
    {
        return $this->getModel()
            ->when(isset($where['tid']) && $where['tid'] !== '', function ($query) use ($where) {
                $query->where('tid', $where['tid']);
            })
            ->when(isset($where['title']) && $where['title'] !== '', function ($query) use ($where) {
                $query->where('title', 'like', '%' . $where['title'] . '%');
            });
    }

    /**
     * Lấy danh sách bản ghi tải xuống
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @param string $order
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/3/10
     */
    public function themeDownloadList(array $where, string $field = '*', int $page = 0, int $limit = 0, string $order = 'id desc'): array
    {
        return $this->getConditionModel($where)
            ->field($field)
            ->order($order)
            ->when($page != 0, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })->select()->toArray();
    }

    /**
     * Lấy số lượng bản ghi tải xuống
     * @param array $where
     * @return int
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/3/10
     */
    public function themeDownloadCount(array $where): int
    {
        return $this->getConditionModel($where)->count();
    }
}
