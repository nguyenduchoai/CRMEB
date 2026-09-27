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

namespace app\model\activity\live;


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * Class LiveRoomGoods
 * @package app\model\live
 */
class LiveRoomGoods extends BaseModel
{
    use ModelTrait;

    protected $name = 'live_room_goods';

    public function goods()
    {
        return $this->hasOne(LiveGoods::class, 'id', 'live_goods_id');
    }

    public function room()
    {
        return $this->hasOne(LiveRoom::class, 'id', 'live_room_id');
    }
}
