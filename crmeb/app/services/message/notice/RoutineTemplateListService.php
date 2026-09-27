<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\services\message\notice;

use app\jobs\TemplateJob;
use app\services\message\NoticeService;
use app\services\user\UserServices;
use app\services\wechat\WechatUserServices;
use think\facade\Log;


/**
 * Hàng đợi tin nhắn mẫu Mini Program
 * Class RoutineTemplateJob
 * @package crmeb\jobs
 */
class RoutineTemplateListService extends NoticeService
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
            $openid = app()->make(WechatUserServices::class)->uidToOpenid($uid, 'routine');
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
            if ($this->noticeInfo['is_routine'] == 1) {
                $openid = $this->getOpenidByUid($uid);
                //Đưa vào hàng đợi để thực thi
                TemplateJob::dispatch('doJob', ['subscribe', $openid, $this->noticeInfo['routine_tempid'], $data, $link, $color]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return true;
        }
    }

    /**
     * Xác nhận đã nhận hàng
     * @param $uid
     * @param $order
     * @param $title
     * @return bool|void
     */
    public function sendOrderTakeOver($uid, $order, $title)
    {
        return $this->sendTemplate((int)$uid, [
            'thing1' => $order['order_id'],
            'thing2' => $title,
            'date5' => date('Y-m-d H:i:s', time()),
        ], '/pages/goods/order_details/index?order_id=' . $order['order_id']);
    }

    /**
     * Tin nhắn đăng ký giao hàng
     * @param $uid
     * @param $order
     * @param $storeTitle
     * @param int $isGive
     * @return bool|void
     */
    public function sendOrderPostage($uid, $order, $storeTitle, int $isGive = 0)
    {
        if ($isGive) {//Giao hàng qua vận chuyển
            return $this->sendTemplate((int)$uid, [
                'character_string2' => $order['delivery_id'],
                'thing1' => $order['delivery_name'],
                'time3' => date('Y-m-d H:i:s', time()),
                'thing5' => $storeTitle,
            ], '/pages/goods/order_details/index?order_id=' . $order['order_id']);
        } else {//Giao hàng nội thành
            return $this->sendTemplate((int)$uid, [
                'thing8' => $storeTitle,
                'character_string1' => $order['order_id'],
                'name4' => $order['delivery_name'],
                'phone_number10' => $order['delivery_id']
            ], '/pages/goods/order_details/index?order_id=' . $order['order_id']);
        }
    }

    /**
     * Hoàn tiền số tiền đã nạp
     * @param $uid
     * @param $UserRecharge
     * @param $now_money
     * @return bool|void
     */
    public function sendRechargeSuccess($uid, $UserRecharge, $now_money)
    {
        return $this->sendTemplate((int)$uid, [
            'character_string1' => $UserRecharge['order_id'],
            'amount3' => $UserRecharge['price'],
            'amount4' => $now_money,
            'date5' => date('Y-m-d H:i:s', time()),
        ], '/pages/users/user_bill/index?type=2');
    }

    /**
     * Gửi tin nhắn khi đơn hàng hoàn tiền thành công
     * @param $uid
     * @param $order
     * @param $storeTitle
     * @param $data
     * @return bool|void
     */
    public function sendOrderRefundSuccess($uid, $order, $storeTitle, $data)
    {
        return $this->sendTemplate((int)$uid, [
            'thing1' => 'Hoàn tiền thành công',
            'thing2' => $storeTitle,
            'amount3' => $order['pay_price'],
            'character_string6' => $data['order_id']
        ], '/pages/goods/order_details/index?order_id=' . $data['order_id'] . '&isReturen=1');
    }

    /**
     * Đơn hàng hoàn tiền thất bại
     * @param $uid
     * @param $order
     * @param $storeTitle
     * @return bool|void
     */
    public function sendOrderRefundFail($uid, $order, $storeTitle)
    {
        return $this->sendTemplate((int)$uid, [
            'thing1' => 'Hoàn tiền thất bại',
            'thing2' => $storeTitle,
            'amount3' => $order['pay_price'],
            'character_string6' => $order['order_id']
        ], '/pages/goods/order_details/index?order_id=' . $order['order_id'] . '&isReturen=1');
    }

    /**
     * Gửi tin nhắn cho quản trị viên khi người dùng yêu cầu hoàn tiền
     * @param $uid
     * @param $order
     * @return bool|void
     */
    public function sendOrderRefundStatus($uid, $order)
    {
        $data['character_string4'] = $order['order_id'];
        $data['date5'] = date('Y-m-d H:i:s', time());
        $data['amount2'] = $order['pay_price'];
        $data['phrase7'] = 'Đang yêu cầu hoàn tiền';
        $data['thing8'] = 'Vui lòng xử lý kịp thời';
        return $this->sendTemplate((int)$uid, $data);
    }

    /**
     * Thông báo săn giảm giá thành công
     * @param $uid
     * @param array $bargain
     * @param array $bargainUser
     * @param int $bargainUserId
     * @return bool|void
     */
    public function sendBargainSuccess($uid, $bargain = [], $bargainUser = [], $bargainUserId = 0)
    {
        $data['thing1'] = $bargain['title'];
        $data['amount2'] = $bargain['min_price'];
        $data['thing3'] = 'Chúc mừng bạn, đã săn được giá thấp nhất';
        return $this->sendTemplate((int)$uid, $data, '/pages/activity/goods_bargain_details/index?id=' . $bargain['id'] . '&bargain=' . $bargainUserId);
    }

    /**
     * Gửi tin nhắn mẫu khi đơn hàng thanh toán thành công
     * @param $uid
     * @param $pay_price
     * @param $orderId
     * @return bool|void
     */
    public function sendOrderSuccess($uid, $pay_price, $orderId)
    {
        if ($orderId == '') return true;
        $data['character_string1'] = $orderId;
        $data['amount2'] = $pay_price . 'đ';
        $data['date3'] = date('Y-m-d H:i:s', time());
        return $this->sendTemplate((int)$uid, $data, '/pages/goods/order_details/index?order_id=' . $orderId);
    }

    /**
     * Gửi tin nhắn khi đơn hàng thành viên thanh toán thành công
     * @param $uid
     * @param $pay_price
     * @param $orderId
     * @return bool|void
     */
    public function sendMemberOrderSuccess($uid, $pay_price, $orderId)
    {
        if ($orderId == '') return true;
        $data['character_string1'] = $orderId;
        $data['amount2'] = $pay_price . 'đ';
        $data['date3'] = date('Y-m-d H:i:s', time());
        return $this->sendTemplate((int)$uid, $data, '/pages/annex/vip_paid/index');
    }

    /**
     * Rút tiền thất bại
     * @param $uid
     * @param $msg
     * @param $extract_number
     * @param $nickname
     * @return bool|void
     */
    public function sendExtractFail($uid, $msg, $extract_number, $nickname)
    {
        return $this->sendTemplate((int)$uid, [
            'thing1' => 'Rút tiền thất bại:' . $msg,
            'amount2' => $extract_number . 'đ',
            'thing3' => $nickname,
            'date4' => date('Y-m-d H:i:s', time())
        ], '/pages/users/user_spread_money/index?type=1');
    }

    /**
     * Rút tiền thành công
     * @param $uid
     * @param $extract_number
     * @param $nickname
     * @return bool|void
     */
    public function sendExtractSuccess($uid, $extract_number, $nickname)
    {
        return $this->sendTemplate((int)$uid, [
            'thing1' => 'Rút tiền thành công',
            'amount2' => $extract_number,
            'thing3' => $nickname,
            'date4' => date('Y-m-d H:i:s', time())
        ], '/pages/users/user_spread_money/index?type=1');
    }

    /**
     * Người dùng yêu cầu rút tiền, sau khi trang quản trị đồng ý thì gửi cho người dùng
     * @param $uid
     * @param $extract_number
     * @param $order_id
     * @param $type
     * @return bool|void
     */
    public function sendRevenueReceived($uid, $extract_number, $order_id, $type)
    {
        return $this->sendTemplate((int)$uid, [
            'character_string1' => $order_id,
            'thing7' => 'Nền tảng chi trả hoa hồng',
            'amount3' => $extract_number,
            'time10' => date('Y-m-d H:i:s', time())
        ], '/pages/users/user_spread_money/receiving?id=' . $order_id . '&type=' . $type);
    }

    /**
     * Thông báo mua chung thành công
     * @param $uid
     * @param $pinkTitle
     * @param $nickname
     * @param $pinkTime
     * @param $count
     * @param string $link
     * @return bool|void
     */
    public function sendPinkSuccess($uid, $pinkTitle, $nickname, $pinkTime, $count, string $link = '')
    {
        return $this->sendTemplate((int)$uid, [
            'thing1' => $pinkTitle,
            'thing12' => $nickname,
            'date5' => date('Y-m-d H:i:s', $pinkTime),
            'number2' => $count
        ], $link);
    }

    /**
     * Thông báo trạng thái mua chung
     * @param $uid
     * @param $pinkTitle
     * @param $count
     * @param $remarks
     * @param $link
     * @return bool|void
     */
    public function sendPinkFail($uid, $pinkTitle, $count, $remarks, $link)
    {
        return $this->sendTemplate((int)$uid, [
            'thing2' => $pinkTitle,
            'thing1' => $count,
            'thing3' => $remarks
        ], $link);
    }

    /**
     * Nhắc nhở tin nhắn tặng điểm thưởng
     * @param $uid
     * @param $order
     * @param $storeTitle
     * @param $gainIntegral
     * @param $integral
     * @return bool|void
     */
    public function sendUserIntegral($uid, $order, $storeTitle, $gainIntegral, $integral)
    {
        if (!$order || !$uid) return true;
        if (is_string($order['cart_id']))
            $order['cart_id'] = json_decode($order['cart_id'], true);
        return $this->sendTemplate((int)$uid, [
            'character_string2' => $order['order_id'],
            'thing3' => $storeTitle,
            'amount4' => $order['pay_price'],
            'number5' => $gainIntegral,
            'number6' => $integral
        ], '/pages/users/user_integral/index');
    }

    /**
     * Gửi nhắc nhở khi nhận hoa hồng giới thiệu
     * @param $uid
     * @param string $brokeragePrice
     * @param string $goods_name
     * @return bool|void
     */
    public function sendOrderBrokerageSuccess($uid, string $brokeragePrice, string $goods_name)
    {
        return $this->sendTemplate((int)$uid, [
            'thing2' => $goods_name,
            'amount4' => $brokeragePrice . 'đ',
            'time1' => date('Y-m-d H:i:s', time())
        ], '/pages/users/user_spread_user/index');
    }

    /**
     * Gửi nhắc nhở khi liên kết quan hệ giới thiệu
     * @param $uid
     * @param string $userName
     * @return bool|void
     */
    public function sendBindSpreadUidSuccess($uid, string $userName)
    {
        return $this->sendTemplate((int)$uid, [
            'name3' => $userName . "đã tham gia nhóm của bạn",
            'date4' => date('Y-m-d H:i:s', time())
        ], '/pages/users/user_spread_user/index');
    }
}
