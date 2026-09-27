<?php

namespace app\dao\system\log;

use app\dao\BaseDao;
use app\model\system\log\SystemFileInfo;

/**
 * @author Wu Xi
 * @email 442384644@qq.com
 * @date 2023/04/07
 */
class SystemFileInfoDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return SystemFileInfo::class;
    }
}