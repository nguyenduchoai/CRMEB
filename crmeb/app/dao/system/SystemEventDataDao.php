<?php

namespace app\dao\system;

use app\dao\BaseDao;
use app\model\system\SystemEventData;

class SystemEventDataDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return SystemEventData::class;
    }
}