<?php

namespace app\api\controller\v1;

use app\services\activity\combination\StorePinkServices;
use app\services\activity\live\LiveGoodsServices;
use app\services\activity\live\LiveRoomServices;
use app\services\agent\AgentManageServices;
use app\services\order\StoreOrderServices;
use app\services\order\StoreOrderTakeServices;
use app\services\product\product\StoreProductServices;
use app\services\system\attachment\SystemAttachmentServices;
use app\services\system\crontab\SystemCrontabServices;

/**
 * Controller tác vụ định kỳ
 * @author Wu Xi
 * @email 442384644@qq.com
 * @date 2023/02/21
 */
class CrontabController
{
    /**
     * API gọi tác vụ định kỳ
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/02/17
     */
    public function crontabRun()
    {
        app()->make(SystemCrontabServices::class)->crontabApiRun();
    }

    /**
     * Kiểm tra tác vụ định kỳ có hoạt động bình thường không, phải chạy mỗi 6 giây một lần
     */
    public function crontabCheck()
    {
        file_put_contents(root_path() . 'runtime/.timer', time());
    }

    /**
     * Tự động hủy đơn hàng chưa thanh toán
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function orderUnpaidCancel()
    {
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $orderServices->orderUnpaidCancel();
    }

    /**
     * Xử lý đơn hàng mua chung hết hạn
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function pinkExpiration()
    {
        /** @var StorePinkServices $storePinkServices */
        $storePinkServices = app()->make(StorePinkServices::class);
        $storePinkServices->statusPink();
    }

    /**
     * Tự động hủy liên kết với người giới thiệu
     */
    public function agentUnbind()
    {
        /** @var AgentManageServices $agentManage */
        $agentManage = app()->make(AgentManageServices::class);
        $agentManage->removeSpread();
    }

    /**
     * Cập nhật trạng thái sản phẩm livestream
     */
    public function syncGoodStatus()
    {
        /** @var LiveGoodsServices $liveGoods */
        $liveGoods = app()->make(LiveGoodsServices::class);
        $liveGoods->syncGoodStatus();
    }

    /**
     * Cập nhật trạng thái phòng livestream
     */
    public function syncRoomStatus()
    {
        /** @var LiveRoomServices $liveRoom */
        $liveRoom = app()->make(LiveRoomServices::class);
        $liveRoom->syncRoomStatus();
    }

    /**
     * Tự động nhận hàng
     */
    public function autoTakeOrder()
    {
        /** @var StoreOrderTakeServices $services */
        $services = app()->make(StoreOrderTakeServices::class);
        $services->autoTakeOrder();
    }

    /**
     * Tra cứu sản phẩm đặt trước hết hạn và tự động ngừng bán
     */
    public function downAdvance()
    {
        /** @var StoreProductServices $product */
        $product = app()->make(StoreProductServices::class);
        $product->downAdvance();
    }

    /**
     * Tự động đánh giá tốt
     */
    public function autoComment()
    {
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $orderServices->autoComment();
    }

    /**
     * Xóa poster ngày hôm qua
     * @throws \Exception
     */
    public function emptyYesterdayAttachment()
    {
        /** @var SystemAttachmentServices $attach */
        $attach = app()->make(SystemAttachmentServices::class);
        $attach->emptyYesterdayAttachment();
    }
}
