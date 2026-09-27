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

namespace app\api\controller\v1;


use app\Request;
use crmeb\services\app\MiniProgramService;
use crmeb\services\pay\Pay;

/**
 * Callback thanh toán
 * Class PayController
 * @package app\api\controller\v1
 */
class PayController
{

    /**
     * Callback thanh toán
     * @param string $type
     * @return string|void
     * @throws \EasyWeChat\Core\Exceptions\FaultException
     */
    public function notify(string $type)
    {
        switch (urldecode($type)) {
            case 'alipay':
                /** @var Pay $pay */
                $pay = app()->make(Pay::class, ['ali_pay']);
                return $pay->handleNotify();
            case 'v3wechat':
                return app()->make(Pay::class, ['v3_wechat_pay'])->handleNotify()->getContent();
            case 'routine':
                return MiniProgramService::handleNotify();
            case 'wechat':
                if (sys_config('pay_wechat_type')) {
                    /** @var Pay $pay */
                    $pay = app()->make(Pay::class, ['v3_wechat_pay']);
                } else {
                    /** @var Pay $pay */
                    $pay = app()->make(Pay::class);
                }
                return $pay->handleNotify()->getContent();
            default:
                if (strstr($type, 'allin') !== false) {
                    /** @var Pay $pay */
                    $pay = app()->make(Pay::class, ['allin_pay']);
                    return $pay->handleNotify($type);
                }
        }
    }

    /**
     * Cấu hình thanh toán
     * @param Request $request
     * @return mixed
     */
    public function config(Request $request)
    {
        $config = [
            [
                'icon' => 'icon-weixinzhifu',
                'name' => 'WeChat Pay',
                'value' => 'weixin',
                'title' => 'Thanh toán nhanh qua WeChat',
                'number' => null,
                'payStatus' => !!sys_config('pay_weixin_open', 0),
            ],
            [
                'icon' => 'icon-zhifubao',
                'name' => 'Thanh toán Alipay',
                'value' => 'alipay',
                'title' => 'Thanh toán trực tuyến qua Alipay',
                'number' => null,
                'payStatus' => !!sys_config('ali_pay_status', 0),
            ],
            [
                'icon' => 'icon-yuezhifu',
                'name' => 'Thanh toán bằng số dư',
                'value' => 'yue',
                'title' => 'Số dư khả dụng hiện tại',
                'number' => $request->user('now_money'),
                'payStatus' => (int)sys_config('yue_pay_status', 0) === 1,
            ],
            [
                'icon' => 'icon-yuezhifu1',
                'name' => 'Thanh toán ngoại tuyến',
                'value' => 'offline',
                'title' => 'Chọn phương thức thanh toán ngoại tuyến',
                'number' => null,
                'payStatus' => (int)sys_config('offline_pay_status', 0) === 1,
            ],
            [
                'icon' => 'icon-haoyoudaizhifu',
                'name' => 'Bạn bè thanh toán hộ',
                'value' => 'friend',
                'title' => 'Nhờ bạn bè WeChat thanh toán',
                'number' => null,
                'payStatus' => !!sys_config('friend_pay_status', 0),
            ]
        ];

        return app('json')->success($config);
    }

    public function transferNotify()
    {
        return app()->make(Pay::class, ['v3_wechat_pay'])->handleTransferNotify()->getContent();
    }
}
