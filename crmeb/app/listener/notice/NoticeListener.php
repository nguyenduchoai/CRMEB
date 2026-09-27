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

namespace app\listener\notice;

use app\jobs\notice\PrintJob;
use app\services\message\NoticeService;
use app\services\message\notice\{
    EnterpriseWechatService,
    RoutineTemplateListService,
    SmsService,
    SystemMsgService,
    WechatTemplateListService
};
use app\services\order\StoreOrderCartInfoServices;
use app\services\user\UserServices;
use crmeb\interfaces\ListenerInterface;
use crmeb\utils\Str;

/**
 * Class tin nhắn
 * @author: Wu Xi
 * @email: 442384644@qq.com
 * @date: 2023/8/29
 */
class NoticeListener implements ListenerInterface
{
    /**
     * @var array
     */
    protected $services = [];

    /**
     * Phương thức
     * @var string[]
     */
    protected $eventMethods = [
        'bind_spread_uid' => 'handleBindSpreadUid',
        'order_pay_success' => 'handleOrderPaySuccess',
        'order_deliver_success' => 'handleOrderDeliverSuccess',
        'order_postage_success' => 'handleOrderPostageSuccess',
        'order_take' => 'handleOrderTake',
        'price_revision' => 'handlePriceRevision',
        'order_refund' => 'handleOrderRefund',
        'send_order_refund_no_status' => 'handleSendOrderRefundNoStatus',
        'recharge_success' => 'handleRechargeSuccess',
        'recharge_order_refund_status' => 'handleRechargeOrderRefundStatus',
        'integral_accout' => 'handleIntegralAccout',
        'order_brokerage' => 'handleOrderBrokerage',
        'bargain_success' => 'handleBargainSuccess',
        'can_pink_success' => 'handlePinkSuccess',
        'open_pink_success' => 'handlePinkSuccess',
        'order_user_groups_success' => 'handleGroupsSuccess',
        'send_order_pink_fial' => 'handlePinkFail',
        'send_order_pink_clone' => 'handlePinkFail',
        'user_extract' => 'handleUserExtract',
        'user_balance_change' => 'handleUserBalanceChange',
        'order_pay_false' => 'handleOrderPayFalse',
        'admin_pay_success_code' => 'handleAdminPaySuccessCode',
        'send_admin_confirm_take_over' => 'handleSendAdminConfirmTakeOver',
        'send_order_apply_refund' => 'handleSendOrderApplyRefund',
        'kefu_send_extract_application' => 'handleKefuSendExtractApplication',
        'sign_remind' => 'handleSignRemind',
        'revenue_received' => 'handleRevenueReceived',
        // add more event-method mappings here...
    ];

    /**
     * Khởi động và nạp
     */
    public function __construct()
    {
        $this->services = [
            'Wechat' => app()->make(WechatTemplateListService::class),
            'Routine' => app()->make(RoutineTemplateListService::class),
            'SysMsg' => app()->make(SystemMsgService::class),
            'WeWork' => app()->make(EnterpriseWechatService::class),
            'Sms' => app()->make(SmsService::class)
        ];
    }

    /**
     * Lấy đối tượng
     * @param $mark
     * @return NoticeService
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    private function getNoticeService($mark)
    {
        return $this->services[$mark];
    }

    /**
     * Thực thi phương thức
     * @param $event
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    public function handle($event): void
    {
        try {
            [$data, $mark] = $event;
            if ($mark) {
                $this->getNoticeService('SysMsg')->setEvent($mark);     //Thông báo nội bộ
                $this->getNoticeService('Sms')->setEvent($mark);        //SMS
                $this->getNoticeService('Wechat')->setEvent($mark);     //Tin nhắn mẫu
                $this->getNoticeService('Routine')->setEvent($mark);    //Tin nhắn đăng ký
                $this->getNoticeService('WeWork')->setEvent($mark);     //Tin nhắn WeCom
                if (isset($this->eventMethods[$mark])) {
                    $method = $this->eventMethods[$mark];
                    call_user_func([$this, $method], $data);
                }
            }
        } catch (\Throwable $e) {
        }
    }

    /**
     * Gửi tin nhắn cho cấp trên khi giới thiệu người dùng mới
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleBindSpreadUid($data)
    {
        if (isset($data['spreadUid']) && $data['spreadUid']) {
            $name = $data['nickname'] ?? '';
            //Thông báo nội bộ
            $this->getNoticeService('SysMsg')->sendMsg($data['spreadUid'], ['nickname' => $name]);
        }
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi thanh toán thành công
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleOrderPaySuccess($data)
    {
        $pay_price = $data['pay_price'];
        $order_id = $data['order_id'];
        $data['is_channel'] = $data['is_channel'] ?? 2;
        $data['total_num'] = $data['total_num'] ?? 1;
        $data['storeName'] = Str::substrUTf8($data['storeName'], 20, 'UTF-8', '');

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($data['uid'], ['order_id' => $data['order_id'], 'total_num' => $data['total_num'], 'pay_price' => $data['pay_price']]);
        //SMS
        $this->getNoticeService('Sms')->sendSms($data['user_phone'], compact('order_id', 'pay_price'));
        //Tin nhắn mẫu OA WeChat
        $this->getNoticeService('Wechat')->sendOrderPaySuccess($data['uid'], $data);
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendOrderSuccess($data['uid'], $data['pay_price'], $data['order_id']);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi giao hàng
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleOrderDeliverSuccess($data)
    {
        $orderInfo = $data['orderInfo'];
        $storeTitle = $data['storeName'];
        $order_id = $orderInfo->order_id;
        $store_name = $storeTitle;
        $storeTitle = Str::substrUTf8($storeTitle, 20, 'UTF-8', '');
        $nickname = app()->make(UserServices::class)->value(['uid' => $orderInfo->uid], 'nickname');

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($orderInfo['uid'], ['nickname' => $nickname, 'store_name' => $storeTitle, 'order_id' => $orderInfo['order_id'], 'delivery_name' => $orderInfo['delivery_name'], 'delivery_id' => $orderInfo['delivery_id'], 'user_address' => $orderInfo['user_address']]);
        //SMS
        $this->getNoticeService('Sms')->sendSms($orderInfo->user_phone, compact('order_id', 'store_name', 'nickname'));
        //Tin nhắn mẫu OA WeChat
        $this->getNoticeService('Wechat')->sendOrderDeliver($orderInfo['uid'], $storeTitle, $orderInfo->toArray());
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendOrderPostage($orderInfo['uid'], $orderInfo->toArray(), $storeTitle, 0);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi gửi chuyển phát
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleOrderPostageSuccess($data)
    {
        $orderInfo = $data['orderInfo'];
        $storeTitle = $data['storeName'];
        $order_id = $orderInfo->order_id;
        $store_name = $storeTitle;
        $storeTitle = Str::substrUTf8($storeTitle, 20, 'UTF-8', '');
        $nickname = app()->make(UserServices::class)->value(['uid' => $orderInfo->uid], 'nickname');

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($orderInfo['uid'], ['nickname' => $nickname, 'store_name' => $storeTitle, 'order_id' => $orderInfo['order_id'], 'delivery_name' => $orderInfo['delivery_name'], 'delivery_id' => $orderInfo['delivery_id'], 'user_address' => $orderInfo['user_address']]);
        //SMS
        $this->getNoticeService('Sms')->sendSms($orderInfo->user_phone, compact('order_id', 'store_name', 'nickname'));
        //Tin nhắn mẫu OA WeChat
        $this->getNoticeService('Wechat')->sendOrderPostage($orderInfo['uid'], $orderInfo->toArray(), $storeTitle);
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendOrderPostage($orderInfo['uid'], $orderInfo->toArray(), $storeTitle, 1);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi xác nhận đã nhận hàng
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleOrderTake($data)
    {
        $order = is_object($data['order']) ? $data['order']->toArray() : $data['order'];
        $store_name = Str::substrUTf8($data['storeTitle'], 20, 'UTF-8', '');
        $order_id = $order['order_id'];

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($order['uid'], ['order_id' => $order['order_id'], 'store_name' => $store_name]);
        //SMS
        $this->getNoticeService('Sms')->sendSms($order['user_phone'], compact('store_name', 'order_id'));
        //Tin nhắn mẫu OA WeChat
        $this->getNoticeService('Wechat')->sendOrderTakeSuccess($order['uid'], $order, $store_name);
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendOrderTakeOver($order['uid'], $order, $store_name);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi đổi giá
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handlePriceRevision($data)
    {
        $order = $data['order'];
        $pay_price = $data['pay_price'];
        $order['storeName'] = app()->make(StoreOrderCartInfoServices::class)->getCarIdByProductTitle((int)$order['id']);

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($order['uid'], ['order_id' => $order['order_id'], 'pay_price' => $pay_price]);
        //SMS
        $this->getNoticeService('Sms')->sendSms($order['user_phone'], ['order_id' => $order['order_id'], 'pay_price' => $pay_price]);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi hoàn tiền thành công
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleOrderRefund($data)
    {
        $datas = $data['data'];
        $order = $data['order'];
        $order['refund_price'] = $datas['refund_price'];
        $order['refund_no'] = $datas['refund_no'];
        $storeName = app()->make(StoreOrderCartInfoServices::class)->getCarIdByProductTitle((int)$order['id']);
        $storeTitle = Str::substrUTf8($storeName, 20, 'UTF-8', '');

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($order['uid'], ['order_id' => $order['order_id'], 'pay_price' => $order['pay_price'], 'refund_price' => $datas['refund_price']]);
        //SMS
        $this->getNoticeService('Sms')->sendSms($order['user_phone'], ['order_id' => $order['order_id'], 'refund_price' => $order['refund_price']]);
        //Tin nhắn mẫu OA WeChat
        $this->getNoticeService('Wechat')->sendOrderRefund($order['uid'], $order, $storeTitle);
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendOrderRefundSuccess($order['uid'], $order, $storeTitle, $datas);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi hoàn tiền không được duyệt
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleSendOrderRefundNoStatus($data)
    {
        $order = $data['orderInfo'];
        $order['pay_price'] = $order['refund_price'];
        $order['refund_no'] = $order['order_id'];
        $storeTitle = Str::substrUTf8($order['cart_info'][0]['productInfo']['store_name'], 20, 'UTF-8', '');

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($order['uid'], ['order_id' => $order['order_id'], 'pay_price' => $order['refund_price'], 'store_name' => $storeTitle]);
        //Tin nhắn mẫu
        $this->getNoticeService('Wechat')->sendOrderNoRefund($order['uid'], $order, $storeTitle);
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendOrderRefundFail($order['uid'], $order, $storeTitle);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi nạp tiền thành công
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleRechargeSuccess($data)
    {
        $order = $data['order'];
        $order['now_money'] = $data['now_money'];

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($order['uid'], ['order_id' => $order['order_id'], 'price' => $order['price'], 'now_money' => $order['now_money']]);
        //Tin nhắn mẫu OA WeChat
        $this->getNoticeService('Wechat')->sendRechargeSuccess($order['uid'], $order);
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendRechargeSuccess($order['uid'], $order, $order['now_money']);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi hoàn tiền nạp
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleRechargeOrderRefundStatus($data)
    {
        $datas = $data['data'];
        $UserRecharge = $data['UserRecharge'];
        $now_money = $data['now_money'];

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($UserRecharge['uid'], ['refund_price' => $datas['refund_price'], 'order_id' => $UserRecharge['order_id'], 'price' => $UserRecharge['price']]);
        //Tin nhắn mẫu OA WeChat
        $this->getNoticeService('Wechat')->sendOrderRefund($UserRecharge['uid'], ['refund_no' => $UserRecharge['order_id'], 'refund_price' => $UserRecharge['price']], 'Hoàn tiền nạp');
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendRechargeSuccess($UserRecharge['uid'], $UserRecharge, $now_money);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi điểm thưởng vào tài khoản
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleIntegralAccout($data)
    {
        $order = $data['order'];
        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($order['uid'], ['order_id' => $order['order_id'], 'store_name' => $data['storeTitle'], 'pay_price' => $order['pay_price'], 'gain_integral' => $data['give_integral'], 'integral' => $data['integral']]);
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendUserIntegral($order['uid'], $data['order'], $data['storeTitle'], $data['give_integral'], $data['integral']);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi hoa hồng vào tài khoản
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleOrderBrokerage($data)
    {
        $brokeragePrice = $data['brokeragePrice'];
        $goodsName = $data['goodsName'];
        $goodsPrice = $data['goodsPrice'];
        $spread_uid = $data['spread_uid'];

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($spread_uid, ['goods_name' => $goodsName, 'goods_price' => $goodsPrice, 'brokerage_price' => $brokeragePrice]);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi săn giảm giá thành công
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleBargainSuccess($data)
    {
        $uid = $data['uid'];
        $bargainInfo = $data['bargainInfo'];
        $bargainUserInfo = $data['bargainUserInfo'];
        $bargainInfo['title'] = Str::substrUTf8($bargainInfo['title'], 20, 'UTF-8', '');

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($uid, ['title' => $bargainInfo['title'], 'min_price' => $bargainInfo['min_price']]);
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendBargainSuccess($uid, $bargainInfo, $bargainUserInfo, $uid);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi mở nhóm thành công, tham gia nhóm thành công
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handlePinkSuccess($data)
    {
        $orderInfo = $data['orderInfo'];
        $title = $data['title'];
        $pink = $data['pink'];
        $nickname = app()->make(UserServices::class)->value(['uid' => $orderInfo['uid']], 'nickname');

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($orderInfo['uid'], ['title' => $title, 'nickname' => $nickname, 'count' => $pink['people'], 'pink_time' => date('Y-m-d H:i:s', $pink['add_time'])]);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi mua chung thành công
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleGroupsSuccess($data)
    {
        $list = $data['list'];
        $title = $data['title'];
        $url = '/pages/goods/order_details/index?order_id=' . $list['order_id'];
        $title = Str::substrUTf8($title, 20, 'UTF-8', '');

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($list['uid'], ['title' => $title, 'nickname' => $list['nickname'], 'count' => $list['people'], 'pink_time' => date('Y-m-d H:i:s', $list['add_time'])]);
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendPinkSuccess($list['uid'], $title, $list['nickname'], $list['add_time'], $list['people'], $url);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi mua chung thất bại, mua chung bị hủy
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handlePinkFail($data)
    {
        $uid = $data['uid'];
        $pink = $data['pink'];

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($uid, ['title' => $pink->title, 'count' => $pink->people]);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi rút tiền thành công
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleUserExtract($data)
    {
        $extractNumber = $data['extractNumber'];
        $nickname = $data['nickname'];
        $uid = $data['uid'];

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($uid, ['extract_number' => $extractNumber, 'nickname' => $nickname, 'date' => date('Y-m-d H:i:s', time())]);
        //Tin nhắn mẫu OA WeChat
        $this->getNoticeService('Wechat')->sendUserExtract($uid, $extractNumber);
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendExtractSuccess($uid, $extractNumber, $nickname);
        return true;
    }

    /**
     * Gửi tin nhắn cho người dùng khi rút tiền thất bại
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleUserBalanceChange($data)
    {
        $extract_number = $data['extract_number'];
        $message = $data['message'];
        $uid = $data['uid'];
        $nickname = $data['nickname'];

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($uid, ['extract_number' => $extract_number, 'nickname' => $nickname, 'date' => date('Y-m-d H:i:s', time()), 'message' => $message]);
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendExtractFail($uid, $message, $extract_number, $nickname);
        return true;
    }

    /**
     * Gửi tin nhắn nhắc thanh toán cho người dùng
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleOrderPayFalse($data)
    {
        $order = $data['order'];
        $order_id = $order['order_id'];
        $order['storeName'] = app()->make(StoreOrderCartInfoServices::class)->getCarIdByProductTitle((int)$order['id']);

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($order['uid'], ['order_id' => $order_id]);
        //SMS
        $this->getNoticeService('Sms')->sendSms($order['user_phone'], compact('order_id'));
        return true;
    }

    /**
     * Gửi tin nhắn cho CSKH khi có đơn hàng mới
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleAdminPaySuccessCode($data)
    {
        $order = $data;
        $storeName = app()->make(StoreOrderCartInfoServices::class)->getCarIdByProductTitle((int)$order['id']);
        $title = 'Bạn ơi, có đơn hàng mới rồi!';
        $status = 'Đơn hàng mới';
        $link = '/pages/admin/orderDetail/index?id=' . $order['order_id'];

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->kefuSystemSend(['order_id' => $order['order_id']]);
        //SMS
        $this->getNoticeService('Sms')->sendAdminPaySuccess($order);
        //Tin nhắn mẫu
        $this->getNoticeService('Wechat')->sendAdminOrder($order['order_id'], $storeName, $title, $status, $link);
        //Thông báo WeCom
        $this->getNoticeService('WeWork')->weComSend(['order_id' => $order['order_id']]);
        return true;
    }

    /**
     * Gửi tin nhắn cho CSKH khi xác nhận đã nhận hàng
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleSendAdminConfirmTakeOver($data)
    {
        $order = $data['order'];
        $storeTitle = $data['storeTitle'];
        $storeName = app()->make(StoreOrderCartInfoServices::class)->getCarIdByProductTitle((int)$order['id']);
        $title = 'Bạn ơi, khách hàng đã nhận được hàng rồi!';
        $status = 'Xác nhận đã nhận hàng';
        $link = '/pages/admin/orderDetail/index?id=' . $order['order_id'];

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->kefuSystemSend(['storeTitle' => $storeTitle, 'order_id' => $order['order_id']]);
        //SMS
        $this->getNoticeService('Sms')->sendAdminConfirmTakeOver($order);
        //OA WeChat
        $this->getNoticeService('Wechat')->sendAdminOrder($order['order_id'], $storeName, $title, $status, $link);
        //Thông báo WeCom
        $this->getNoticeService('WeWork')->weComSend(['storeTitle' => $storeTitle, 'order_id' => $order['order_id']]);
        return true;
    }

    /**
     * Gửi tin nhắn cho CSKH khi có yêu cầu hoàn tiền
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleSendOrderApplyRefund($data)
    {
        $order = $data['order'];
        $storeName = app()->make(StoreOrderCartInfoServices::class)->getCarIdByProductTitle((int)$order['id']);
        $title = 'Bạn ơi, bạn có một đơn hoàn tiền cần xử lý!';
        $status = 'Hoàn tiền đơn hàng';
        $link = '/pages/admin/orderDetail/index?id=' . $order['refund_no'] . '&types=-3';

        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->kefuSystemSend(['order_id' => $order['order_id']]);
        //SMS
        $this->getNoticeService('Sms')->sendAdminRefund($order);
        //OA WeChat
        $this->getNoticeService('Wechat')->sendAdminOrder($order['refund_no'], $storeName, $title, $status, $link);
        //Thông báo WeCom
        $this->getNoticeService('WeWork')->weComSend(['order_id' => $order['order_id']]);
        return true;
    }

    /**
     * Gửi tin nhắn cho CSKH khi có yêu cầu rút tiền
     * @param $data
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/29
     */
    protected function handleKefuSendExtractApplication($data)
    {
        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->kefuSystemSend($data);
        //Thông báo WeCom
        $this->getNoticeService('WeWork')->weComSend($data);
        return true;
    }

    /**
     * Nhắc nhở điểm danh
     * @param $data
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2023/9/30
     */
    public function handleSignRemind($data)
    {
        //Thông báo nội bộ
        $this->getNoticeService('SysMsg')->sendMsg($data['uid'], ['site_name' => sys_config('site_name')]);
        //SMS
        if ($data['phone']) {
            $this->getNoticeService('Sms')->sendSms($data['phone'], ['site_name' => sys_config('site_name')]);
        }
        return true;
    }

    protected function handleRevenueReceived($data)
    {
        $extractNumber = $data['extractNumber'];
        $uid = $data['uid'];
        $order_id = $data['order_id'];
        $type = $data['type'];

        //Tin nhắn mẫu OA WeChat
        $this->getNoticeService('Wechat')->sendRevenueReceived($uid, $extractNumber, $order_id, $type);
        //Tin nhắn đăng ký Mini Program
        $this->getNoticeService('Routine')->sendRevenueReceived($uid, $extractNumber, $order_id, $type);
        return true;
    }
}
