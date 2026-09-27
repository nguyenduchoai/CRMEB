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
declare (strict_types=1);

namespace app\services\activity\live;


use app\dao\activity\live\LiveRoomGoodsDao;
use app\services\BaseServices;

/**
 * Class LiveRoomGoodsServices
 * @package app\services\activity\live
 */
class LiveRoomGoodsServices extends BaseServices
{
    public function __construct(LiveRoomGoodsDao $dao)
    {
        $this->dao = $dao;
    }
}
