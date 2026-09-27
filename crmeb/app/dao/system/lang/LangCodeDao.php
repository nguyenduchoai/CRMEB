<?php

namespace app\dao\system\lang;

use app\dao\BaseDao;
use app\model\system\lang\LangCode;

class LangCodeDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return LangCode::class;
    }
}