<?php

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