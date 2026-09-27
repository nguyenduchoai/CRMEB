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

namespace app\jobs;

use app\services\activity\live\LiveGoodsServices;
use app\services\activity\live\LiveRoomServices;
use crmeb\basic\BaseJobs;
use crmeb\traits\QueueTrait;
use think\facade\Log;

class LiveJob extends BaseJobs
{
    use QueueTrait;

    /**
     * Sau khi thực hiện đồng bộ dữ liệu
     * @param $order
     * @return bool
     */
    public function doJob()
    {
        //Cập nhật trạng thái sản phẩm livestream
        try {
            /** @var LiveGoodsServices $liveGoods */
            $liveGoods = app()->make(LiveGoodsServices::class);
            $liveGoods->syncGoodStatus(true);
        } catch (\Throwable $e) {
            Log::error('Cập nhật trạng thái sản phẩm livestream thất bại, nguyên nhân:' . $e->getMessage());
        }
        //Cập nhật trạng thái phòng livestream
        try {
            /** @var LiveRoomServices $liveRoom */
            $liveRoom = app()->make(LiveRoomServices::class);
            $liveRoom->syncRoomStatus(true);
        } catch (\Throwable $e) {
            Log::error('Cập nhật trạng thái phòng livestream thất bại, nguyên nhân:' . $e->getMessage());
        }
        return true;
    }
}
