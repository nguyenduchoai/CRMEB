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
namespace app\dao\system;

use app\dao\BaseDao;
use app\model\system\SystemSignReward;

/**
 * @author: Wu Xi
 * @email: 442384644@qq.com
 * @date: 2023/7/28
 */
class SystemSignRewardDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/7/28
     */
    protected function setModel(): string
    {
        return SystemSignReward::class;
    }
}