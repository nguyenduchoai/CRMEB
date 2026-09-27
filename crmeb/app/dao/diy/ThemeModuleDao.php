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
use app\model\diy\ThemeModule;

class ThemeModuleDao extends BaseDao
{
    protected function setModel(): string
    {
        return ThemeModule::class;
    }

    /**
     * Truy vấn đối tượng model theo điều kiện
     * @param $where
     * @return \crmeb\basic\BaseModel
     */
    public function getConditionModel($where)
    {
        return $this->getModel()
            ->when(isset($where['type']) && $where['type'] !== '', function ($query) use ($where) {
                $query->where('type', $where['type']);
            });
    }

    /**
     * Lấy danh sách thành phần
     * @param $where
     * @param $field
     * @param int $page
     * @param int $limit
     * @param string $order
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function themeModuleList($where, $field, $page = 0, $limit = 0, $order = 'id desc')
    {
        return $this->getConditionModel($where)
            ->field($field)
            ->order($order)
            ->when($page != 0, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })->select()->toArray();
    }

    /**
     * Lấy số lượng thành phần
     * @param $where
     * @return int
     */
    public function themeModuleCount($where)
    {
        return $this->getConditionModel($where)->count();
    }
}
