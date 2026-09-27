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

namespace app\services\agent;

use app\dao\agent\AgentLevelTaskRecordDao;
use app\services\BaseServices;


/**
 * Class AgentLevelTaskRecordServices
 * @package app\services\agent
 */
class AgentLevelTaskRecordServices extends BaseServices
{
    /**
     * AgentLevelTaskRecordServices constructor.
     * @param AgentLevelTaskRecordDao $dao
     */
    public function __construct(AgentLevelTaskRecordDao $dao)
    {
        $this->dao = $dao;
    }
}
