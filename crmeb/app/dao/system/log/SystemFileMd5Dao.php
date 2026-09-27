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

namespace app\dao\system\log;

use app\dao\BaseDao;
use app\model\system\log\SystemFileMd5;

class SystemFileMd5Dao extends BaseDao
{
    protected function setModel(): string
    {
        return SystemFileMd5::class;
    }

    public function getList()
    {
        return $this->getModel()->select()->toArray();
    }
}
