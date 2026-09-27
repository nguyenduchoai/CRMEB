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
namespace app\services\system\crontab;

use app\services\activity\combination\StorePinkServices;
use app\services\activity\live\LiveGoodsServices;
use app\services\activity\live\LiveRoomServices;
use app\services\agent\AgentManageServices;
use app\services\order\StoreOrderInvoiceServices;
use app\services\order\StoreOrderServices;
use app\services\order\StoreOrderTakeServices;
use app\services\product\product\StoreProductServices;
use app\services\system\attachment\SystemAttachmentServices;
use app\services\user\UserSignServices;
use think\facade\Log;

/**
 * Thực thi tác vụ định kỳ
 * @author Wu Xi
 * @email 442384644@qq.com
 * @date 2023/03/01
 */
class CrontabRunServices
{
    /**
     * Loại tác vụ định kỳ, mỗi loại được định nghĩa sẽ tương ứng với một phương thức trong class CrontabRunServices
     * @var string[]
     */
    public $markList = [
        'orderCancel' => 'Tự động hủy đơn hàng chưa thanh toán',
        'pinkExpiration' => 'Xử lý đơn hàng mua chung hết hạn',
        'agentUnbind' => 'Tự động hủy liên kết người giới thiệu khi hết hạn',
        'liveProductStatus' => 'Tự động cập nhật trạng thái sản phẩm livestream',
        'liveRoomStatus' => 'Tự động cập nhật trạng thái phòng livestream',
        'takeDelivery' => 'Tự động xác nhận đã nhận hàng',
        'advanceOff' => 'Tự động gỡ sản phẩm đặt trước khi hết hạn',
        'productReplay' => 'Tự động đánh giá tốt sản phẩm trong đơn hàng',
        'clearPoster' => 'Xóa poster ngày hôm qua',
        'autoInvoice' => 'Tự động xuất hóa đơn và tự động hủy hóa đơn khi hoàn tiền',
        'signRemind' => 'Nhắc nhở chưa điểm danh',
        'customTimer' => 'Tác vụ định kỳ tùy chỉnh',
    ];

    /**
     * Gọi phương thức không tồn tại
     * @param $name
     * @param $arguments
     * @return mixed|void
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function __call($name, $arguments)
    {
        $this->crontabLog($name . 'phương thức không tồn tại');
    }

    /**
     * Log tác vụ định kỳ
     * @param $msg
     */
    protected function crontabLog($msg)
    {
        $timer_log_open = config("log.timer_log", false);
        if ($timer_log_open) {
            $date = date('Y-m-d H:i:s', time());
            Log::write($date . $msg, 'crontab');
        }
    }

    /**
     * Tự động hủy đơn hàng chưa thanh toán
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function orderCancel()
    {
        try {
            app()->make(StoreOrderServices::class)->orderUnpaidCancel();
            $this->crontabLog(' Thực thi tự động hủy đơn hàng chưa thanh toán');
        } catch (\Throwable $e) {
            $this->crontabLog('Tự động hủy đơn hàng thất bại, nguyên nhân:' . $e->getMessage());
        }
    }

    /**
     * Xử lý đơn hàng mua chung hết hạn
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function pinkExpiration()
    {
        try {
            app()->make(StorePinkServices::class)->statusPink();
            $this->crontabLog(' Thực thi xử lý đơn hàng mua chung hết hạn');
        } catch (\Throwable $e) {
            $this->crontabLog('Xử lý đơn hàng mua chung hết hạn thất bại, lý do:' . $e->getMessage());
        }
    }

    /**
     * Tự động hủy liên kết với người giới thiệu
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function agentUnbind()
    {
        try {
            app()->make(AgentManageServices::class)->removeSpread();
            $this->crontabLog(' Thực thi tự động hủy liên kết người giới thiệu');
        } catch (\Throwable $e) {
            $this->crontabLog('Tự động hủy liên kết người giới thiệu thất bại, lý do:' . $e->getMessage());
        }
    }

    /**
     * Cập nhật trạng thái sản phẩm livestream
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function liveProductStatus()
    {
        try {
            app()->make(LiveGoodsServices::class)->syncGoodStatus();
            $this->crontabLog(' Thực thi cập nhật trạng thái sản phẩm livestream');
        } catch (\Throwable $e) {
            $this->crontabLog('Cập nhật trạng thái sản phẩm livestream thất bại, nguyên nhân:' . $e->getMessage());
        }
    }

    /**
     * Cập nhật trạng thái phòng livestream
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function liveRoomStatus()
    {
        try {
            app()->make(LiveRoomServices::class)->syncRoomStatus();
            $this->crontabLog(' Thực thi cập nhật trạng thái phòng livestream');
        } catch (\Throwable $e) {
            $this->crontabLog('Cập nhật trạng thái phòng livestream thất bại, nguyên nhân:' . $e->getMessage());
        }
    }

    /**
     * Tự động nhận hàng
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function takeDelivery()
    {
        try {
            app()->make(StoreOrderTakeServices::class)->autoTakeOrder();
            $this->crontabLog(' Thực thi tự động xác nhận đã nhận hàng');
        } catch (\Throwable $e) {
            $this->crontabLog('Tự động xác nhận đã nhận hàng thất bại, lý do:' . $e->getMessage());
        }
    }

    /**
     * Sản phẩm đặt trước hết hạn tự động ẩn khỏi kệ
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function advanceOff()
    {
        try {
            app()->make(StoreProductServices::class)->downAdvance();
            $this->crontabLog(' Thực thi tự động ngừng bán sản phẩm đặt trước đã hết hạn');
        } catch (\Throwable $e) {
            $this->crontabLog('Tự động ngừng bán sản phẩm đặt trước đã hết hạn thất bại, lý do:' . $e->getMessage());
        }
    }

    /**
     * Tự động đánh giá tốt
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function productReplay()
    {
        try {
            app()->make(StoreOrderServices::class)->autoComment();
            $this->crontabLog(' Thực thi tự động đánh giá tốt');
        } catch (\Throwable $e) {
            $this->crontabLog('Tự động đánh giá tốt thất bại, lý do:' . $e->getMessage());
        }
    }

    /**
     * Xóa poster ngày hôm qua
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function clearPoster()
    {
        try {
            app()->make(SystemAttachmentServices::class)->emptyYesterdayAttachment();
            $this->crontabLog(' Thực thi xóa poster của ngày hôm qua');
        } catch (\Throwable $e) {
            $this->crontabLog('Xóa poster của ngày hôm qua thất bại, lý do:' . $e->getMessage());
        }
    }

    /**
     * Thực hiện tự động xuất/hủy (đảo bút toán đỏ) hóa đơn điện tử
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function autoInvoice()
    {
        try {
            $invoiceServices = app()->make(StoreOrderInvoiceServices::class);
            $invoiceServices->autoInvoice();
            $invoiceServices->autoInvoiceRed();
            $this->crontabLog(' Thực thi tự động xuất/hủy hóa đơn điện tử');
        } catch (\Throwable $e) {
            $this->crontabLog('Tự động xuất/hủy hóa đơn điện tử thất bại, lý do:' . $e->getMessage());
        }
    }

    /**
     * Nhắc nhở chưa điểm danh
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2023/9/30
     */
    public function signRemind()
    {
        try {
            app()->make(UserSignServices::class)->sendSignRemind();
            $this->crontabLog(' Thực thi nhắc nhở chưa điểm danh');
        } catch (\Throwable $e) {
            $this->crontabLog('Nhắc nhở chưa điểm danh thất bại, lý do:' . $e->getMessage());
        }
    }

    /**
     * Bộ đếm thời gian tùy chỉnh
     * @param string $customCode
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/6
     */
    public function customTimer($customCode = '')
    {
        try {
            eval($customCode);
            $this->crontabLog(' Thực thi tác vụ định kỳ tùy chỉnh thành công');
        } catch (\Throwable $e) {
            $this->crontabLog('Thực thi tác vụ định kỳ tùy chỉnh thất bại, lý do:' . $e->getMessage());
        }
    }
}
