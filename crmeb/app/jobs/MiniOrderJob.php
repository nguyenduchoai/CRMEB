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

use app\services\order\StoreOrderServices;
use crmeb\basic\BaseJobs;
use crmeb\services\easywechat\orderShipping\MiniOrderService;
use crmeb\traits\QueueTrait;
use EasyWeChat\Core\Exceptions\HttpException;
use think\Exception;

class MiniOrderJob extends BaseJobs
{
    use QueueTrait;

    /**
     * @throws HttpException
     */
    public function doJob(string $out_trade_no, int $logistics_type, array $shipping_list, string $payer_openid, string $path, int $delivery_mode = 1, bool $is_all_delivered = true)
    {
        try {
            MiniOrderService::shippingByTradeNo($out_trade_no, $logistics_type, $shipping_list, $payer_openid, $path, $delivery_mode, $is_all_delivered);
            return true;
        } catch (HttpException $e) {
            // Xử lý đơn hàng bất thường
            throw new HttpException($e);
        }
    }

    /**
     * Đồng bộ các đơn hàng đã giao hàng trên cửa hàng nhưng chưa giao hàng trên Mini Program
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/8/14
     */
    public function syncOrderShipping()
    {
        try {
            if (sys_config('order_shipping_open')) {
                $time = time();
                $params = [
                    'order_state' => 1,
                    'pay_time_range' => [
                        'begin_time' => $time - (86400 * 7),
                        'end_time' => $time
                    ]
                ];
                $res = MiniOrderService::shippingOrderList($params);
                if ($res['errcode'] == 0 && isset($res['order_list']) && count($res['order_list']) > 0) {
                    foreach ($res['order_list'] as $item) {
                        $shipping_list = [
                            ['item_desc' => 'Không xác định']
                        ];
                        $path = '/pages/index/index';
                        if (strpos($item['merchant_trade_no'], 'hy') !== false) {
                            $shipping_list = [
                                ['item_desc' => 'Người dùng mua thành viên trả phí']
                            ];
                            $path = '/pages/annex/vip_paid/index';
                        }
                        if (strpos($item['merchant_trade_no'], 'cz') !== false) {
                            $shipping_list = [
                                ['item_desc' => 'Nạp tiền người dùng']
                            ];
                            $path = '/pages/users/user_bill/index?type=2';
                        }
                        if (strpos($item['merchant_trade_no'], 'cp') !== false) {
                            $is_shipping = app()->make(StoreOrderServices::class)->value(['order_id' => $item['merchant_trade_no']], 'status');
                            if ((int)$is_shipping === 0) continue;
                            $shipping_list = [
                                ['item_desc' => 'Mua sản phẩm']
                            ];
                            $path = 'pages/goods/order_details/index?order_id=' . $item['merchant_trade_no'];
                        }
                        MiniOrderService::shippingByTradeNo($item['merchant_trade_no'], 4, $shipping_list, $item['openid'], $path, 1, true);
                    }
                }
            }
            return true;
        } catch (HttpException $e) {
            return true;
        }
    }
}
