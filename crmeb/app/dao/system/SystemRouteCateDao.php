<?php
/**
 *  +----------------------------------------------------------------------
 *  | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2016~2022 https://www.crmeb.com All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
 *  +----------------------------------------------------------------------
 *  | Author: CRMEB Team <admin@crmeb.com>
 *  +----------------------------------------------------------------------
 */

namespace app\dao\system;


use app\dao\BaseDao;
use app\model\system\SystemRouteCate;

/**
 * Class SystemRouteCateDao
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/4/6
 * @package app\dao\system
 */
class SystemRouteCateDao extends BaseDao
{

    /**
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/6
     */
    protected function setModel(): string
    {
        return SystemRouteCate::class;
    }
}
