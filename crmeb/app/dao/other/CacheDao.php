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

namespace app\dao\other;


use app\dao\BaseDao;
use app\model\other\Cache;

/**
 * Class CacheDao
 * @package app\dao\other
 */
class CacheDao extends BaseDao
{

    /**
     * @return string
     */
    public function setModel(): string
    {
        return Cache::class;
    }

    /**
     * Xóa bộ nhớ đệm đã hết hạn
     * @throws \Exception
     */
    public function delectDeOverdueDbCache()
    {
        $this->getModel()->where('expire_time', '<>', 0)->where('expire_time', '<', time())->delete();
    }
}
