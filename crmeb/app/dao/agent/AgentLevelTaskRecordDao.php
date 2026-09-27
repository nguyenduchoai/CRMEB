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

namespace app\dao\agent;

use app\dao\BaseDao;
use app\model\agent\AgentLevelTaskRecord;

/**
 * Class AgentLevelTaskRecordDao
 * @package app\dao\agent
 */
class AgentLevelTaskRecordDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return AgentLevelTaskRecord::class;
    }

    /**
     * Lấy tất cả hạng cộng tác viên
     * @param array $where
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where = [], string $field = '*')
    {
        return $this->search($where + ['is_del' => 0, 'status' => 1])->field($field)->order('sort desc,id desc')->select()->toArray();
    }
}
