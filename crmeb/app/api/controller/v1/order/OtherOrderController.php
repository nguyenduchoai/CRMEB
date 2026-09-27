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

namespace app\api\controller\v1\order;


use app\services\order\OtherOrderServices;
use app\services\pay\OrderPayServices;
use app\services\pay\PayServices;
use app\services\pay\YuePayServices;
use app\services\user\member\MemberCardServices;
use app\services\user\UserServices;
use app\Request;

/**
 * Class OtherOrderController
 * @package app\api\controller\v1\order
 */
class OtherOrderController
{
    /**
     * @var OtherOrderServices
     */
    protected $services;
    protected $channelType = ['weixin' => 'wechat', 'weixinh5' => 'weixinh5', 'routine' => 'routine'];

    /**
     * OtherOrderController constructor.
     * @param OtherOrderServices $services
     */
    public function __construct(OtherOrderServices $services)
    {
        $this->services = $services;
    }

    /**
     * Tính số tiền thành viên thanh toán ngoại tuyến
     * @param Request $request
     * @return mixed
     */
    public function computed_offline_pay_price(Request $request)
    {
        list($pay_price) = $request->getMore([['pay_price', 0]], true);
        $old_price = $pay_price;
        if (!$pay_price || !is_numeric($pay_price)) return app('json')->fail(410190);
        $uid = $request->uid();
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        $user_info = $userService->get($uid, ['is_money_level']);
        //Thành viên được chiết khấu khi thanh toán ngoại tuyến
        if ($user_info->is_money_level > 0) {
            //Kiểm tra có mở chiết khấu khi thanh toán ngoại tuyến không
            /** @var MemberCardServices $memberCardService */
            $memberCardService = app()->make(MemberCardServices::class);
            $offline_rule_number = $memberCardService->isOpenMemberCard('offline');
            if ($offline_rule_number) {
                $pay_price = bcmul($pay_price, bcdiv($offline_rule_number, '100', 2), 2);
            }
        }
        $show = true;
        if ($old_price == $pay_price) $show = false;
        return app('json')->success(['pay_price' => $pay_price, 'show' => $show]);

    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function create(Request $request)
    {
        $uid = (int)$request->uid();
        /** @var OtherOrderServices $OtherOrderServices */
        $OtherOrderServices = app()->make(OtherOrderServices::class);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        [$payType, $type, $from, $memberType, $price, $money, $quitUrl, $mcId] = $request->postMore([
            ['pay_type', 'yue'],
            ['type', 0],
            ['from', 'weixin'],
            ['member_type', ''],
            ['price', 0.00],
            ['money', 0.00],
            ['quitUrl', ''],
            ['mc_id', 0]
        ], true);
        if ($money <= 0.00) return app('json')->fail(410191);
        $payType = strtolower($payType);
        if (in_array($type, [1, 2])) {
            /** @var MemberCardServices $memberCardService */
            $memberCardService = app()->make(MemberCardServices::class);
            $isOpenMember = $memberCardService->isOpenMemberCard();
            if (!$isOpenMember) return app('json')->fail(410192);
        }
        $channelType = $userServices->getUserInfo($uid)['user_type'];
        $order = $OtherOrderServices->createOrder($uid, $channelType, $memberType, $price, $payType, $type, $money, $mcId);
        if ($order === false) return app('json')->fail(410193);
        $order_id = $order['order_id'];
        $orderInfo = $OtherOrderServices->getOne(['order_id' => $order_id]);
        if (!$orderInfo) return app('json')->fail(410194);
        $orderInfo = $orderInfo->toArray();

        $info = compact('order_id');

        $payType = app()->make(OrderPayServices::class)->getPayType($payType);

        //Số tiền thanh toán bằng 0
        if (bcsub((string)$orderInfo['pay_price'], '0', 2) <= 0) {
            //Tạo đơn hàng thanh toán jspay
            $payPriceStatus = $OtherOrderServices->zeroYuanPayment($orderInfo);
            if ($payPriceStatus)//Thanh toán 0đ thành công
                return app('json')->status('success', 410217, $info);
            else
                return app('json')->status('pay_error');
        }

        if ($order_id) {
            switch ($payType) {
                case PayServices::YUE_PAY:
                    /** @var YuePayServices $yueServices */
                    $yueServices = app()->make(YuePayServices::class);
                    $pay = $yueServices->yueOrderPay($orderInfo, $uid);
                    if ($pay['status'] === true)
                        return app('json')->status('success', 410197, $info);
                    else {
                        if (is_array($pay))
                            return app('json')->status($pay['status'], $pay['msg'], $info);
                        else
                            return app('json')->status('pay_error', $pay);
                    }
                case PayServices::OFFLINE_PAY:
                    return app('json')->status('success', 410196, $info);
                default:
                    $payServices = app()->make(OrderPayServices::class);
                    $payInfo = $payServices->beforePay($order->toArray(), $payType, ['quitUrl' => $quitUrl]);
                    return app('json')->status($payInfo['status'], $payInfo['payInfo']);
            }
        } else return app('json')->fail(410200);
    }

    /**
     * Phương thức thanh toán ngoại tuyến
     * @return mixed
     */
    public function pay_type(Request $request)
    {
        $payType['ali_pay_status'] = sys_config('ali_pay_status', '0') != '0';
        $payType['pay_weixin_open'] = sys_config('pay_weixin_open', '0') != '0';
        $payType['site_name'] = sys_config('site_name');
        $payType['now_money'] = $request->user('now_money');
        $payType['offline_pay_status'] = true;
        $payType['yue_pay_status'] = (int)sys_config('balance_func_status') && (int)sys_config('yue_pay_status') == 1 ? 1 : 0;//Thanh toán bằng số dư: 1 là bật, 2 là tắt
        return app('json')->success($payType);
    }
}
