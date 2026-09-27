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

namespace app\dao\system\upgrade;

use app\dao\BaseDao;
use app\model\system\upgrade\UpgradeLog;

/**
 * DAO bản ghi nâng cấp
 * Class UpgradeLogDao
 * @package app\dao\system\upgrade
 */
class UpgradeLogDao extends BaseDao
{

    protected function setModel(): string
    {
        return UpgradeLog::class;
    }

    /**
     * Danh sách
     * @param array $where
     * @param array $field
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $field, int $page = 0, int $limit = 0): array
    {
        return $this->search()->field($field)->page($page, $limit)->select()->toArray();
    }
}