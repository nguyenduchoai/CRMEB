<?php

namespace app\dao\system\crontab;

use app\dao\BaseDao;
use app\model\system\crontab\SystemCrontab;

class SystemCrontabDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return SystemCrontab::class;
    }
}