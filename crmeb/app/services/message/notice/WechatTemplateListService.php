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

namespace app\services\message\notice;

use app\jobs\TemplateJob;
use app\services\message\NoticeService;
use app\services\kefu\service\StoreServiceServices;
use app\services\user\UserServices;
use app\services\wechat\WechatUserServices;
use think\facade\Log;


/**
 * Danh sách tin nhắn mẫu WeChat
 * Created by PhpStorm.
 * User: xurongyao <763569752@qq.com>
 * Date: 2021/9/22 1:23 PM
 */
class WechatTemplateListService extends NoticeService
{
    /**
     * Lấy openid theo UID
     * @param int $uid
     * @return mixed
     */
    public function getOpenidByUid(int $uid)
    {
        $isDel = app()->make(UserServices::class)->value(['uid' => $uid], 'is_del');
        if ($isDel) {
            $openid = '';
        } else {
            $openid = app()->make(WechatUserServices::class)->uidToOpenid($uid, 'wechat');
        }
        return $openid;
    }

    /**
     * Gửi tin nhắn mẫu
     * @param int $uid
     * @param array $data
     * @param string|null $link
     * @param string|null $color
     * @return bool|void
     */
    public function sendTemplate(int $uid, array $data, string $link = null, string $color = null)
    {
        try {
            if ($this->noticeInfo['is_wechat'] === 1) {
                $openid = $this->getOpenidByUid($uid);
                if ($openid != '') {
                    //Đưa vào hàng đợi để thực thi
                    TemplateJob::dispatch('doJob', ['wechat', $openid, $this->noticeInfo['wechat_tempid'], $data, $link, $color]);
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return true;
        }
    }

    /**
     * Gửi tin nhắn mẫu khi thanh toán thành công
     * @param $uid
     * @param $order
     * @return bool|void
     */
    public function sendOrderPaySuccess($uid, $order)
    {
        return $this->sendTemplate((int)$uid, [
            'character_string2' => $order['order_id'],
            'time4' => date('Y-m-d H:i:s', $order['pay_time']),
            'thing3' => $order['storeName'],
            'amount5' => $order['pay_price'],
        ], '/pages/goods/order_details/index?order_id=' . $order['order_id']);
    }

    /**
     * Thông báo giao hàng đơn hàng
     * @param $uid
     * @param string $goodsName
     * @param $order
     * @param array $data
     * @return bool|void
     */
    public function sendOrderDeliver($uid, string $goodsName, $order)
    {
        return $this->sendTemplate((int)$uid, [
            'character_string1' => $order['order_id'],
            'time8' => date('Y-m-d H:i:s', time()),
            'thing5' => $goodsName,
            'thing9' => $order['delivery_name'],
            'phone_number10' => $order['delivery_id'],
        ], '/pages/goods/order_details/index?order_id=' . $order['order_id']);
    }

    /**
     * Giao đơn hàng
     * @param $uid
     * @param $order
     * @param $storeTitle
     * @return bool|void
     */
    public function sendOrderPostage($uid, $order, $storeTitle)
    {
        return $this->sendTemplate((int)$uid, [
            'character_string2' => $order['order_id'],
            'time12' => date('Y-m-d H:i:s', time()),
            'thing4' => $storeTitle,
            'thing13' => $order['delivery_name'],
            'character_string14' => $order['delivery_id'],
        ], '/pages/goods/order_details/index?order_id=' . $order['order_id']);
    }

    /**
     * Gửi tin nhắn mẫu khi xác nhận đã nhận hàng
     * @param $uid
     * @param $order
     * @param $title
     * @return bool|void
     */
    public function sendOrderTakeSuccess($uid, $order, $title)
    {
        return $this->sendTemplate((int)$uid, [
            'character_string2' => $order['order_id'],
            'character_string7' => date('Y-m-d H:i:s', time()),
            'thing4' => $title,
            'amount9' => $order['pay_price'],
        ], '/pages/goods/order_details/index?order_id=' . $order['order_id']);
    }

    /**
     * Gửi tin nhắn mẫu hoàn tiền
     * @param $uid
     * @param $order
     * @param $title
     * @return bool|void
     */
    public function sendOrderRefund($uid, $order, $title)
    {
        return $this->sendTemplate((int)$uid, [
            'character_string1' => $order['refund_no'],
            'time5' => date('Y-m-d H:i:s', time()),
            'thing2' => $title,
            'amount3' => $order['refund_price'],
        ], '/pages/goods/order_details/index?order_id=' . $order['refund_no'] . '&isReturn=1');
    }

    /**
     * Gửi tin nhắn mẫu hoàn tiền
     * @param $uid
     * @param $order
     * @param $title
     * @return bool|void
     */
    public function sendOrderNoRefund($uid, $order, $title)
    {
        return $this->sendTemplate((int)$uid, [
            'character_string1' => $order['refund_no'],
            'thing2' => $title,
            'amount3' => $order['refund_price'],
            'thing4' => $order['refuse_reason'],
        ], '/pages/goods/order_details/index?order_id=' . $order['refund_no'] . '&isReturn=1');
    }

    /**
     * Nạp tiền thành công
     * @param $uid
     * @param $order
     * @return bool|void
     */
    public function sendRechargeSuccess($uid, $order)
    {
        return $this->sendTemplate((int)$uid, [
            'time1' => date('Y-m-d H:i:s', $order['add_time']),
            'amount3' => $order['price'],
            'amount4' => $order['give_price'],
            'amount5' => $order['now_money'],
        ]);
    }

    /**
     * Rút tiền thành công
     * @param $uid
     * @param $extractNumber
     * @return bool|void
     */
    public function sendUserExtract($uid, $extractNumber)
    {
        return $this->sendTemplate((int)$uid, [
            'time3' => date('Y-m-d H:i:s', time()),
            'amount2' => $extractNumber,
        ]);
    }

    /**
     * Rút tiền thành công
     * @param $uid
     * @param $extractNumber
     * @param $order_id
     * @param $type
     * @return bool|void
     */
    public function sendRevenueReceived($uid, $extractNumber, $order_id, $type)
    {
        return $this->sendTemplate((int)$uid, [
            'character_string9' => $order_id,
            'thing3' => 'Nền tảng chi trả hoa hồng',
            'amount4' => $extractNumber,
            'time2' => date('Y-m-d H:i:s', time()),
        ], '/pages/users/user_spread_money/receiving?id=' . $order_id . '&type=' . $type);
    }

    /**
     * Nhắc nhở nhân viên CSKH về đơn hàng
     * @param $orderId
     * @param $storeName
     * @param $title
     * @param $status
     * @param $link
     * @return bool
     */
    public function sendAdminOrder($orderId, $storeName, $title, $status, $link)
    {
        /** @var StoreServiceServices $StoreServiceServices */
        $StoreServiceServices = app()->make(StoreServiceServices::class);
        $adminList = $StoreServiceServices->getStoreServiceOrderNotice();
        foreach ($adminList as $item) {
            $this->sendTemplate((int)$item['uid'], [
                'short_thing6' => $status,
                'character_string1' => $orderId,
                'time2' => date('Y-m-d H:i:s', time()),
            ], $link);
        }
        return true;
    }
}
