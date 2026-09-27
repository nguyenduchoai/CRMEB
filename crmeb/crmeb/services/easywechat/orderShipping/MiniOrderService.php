<?php

namespace crmeb\services\easywechat\orderShipping;

use crmeb\services\easywechat\Application;
use crmeb\services\SystemConfigService;
use EasyWeChat\Core\Exceptions\HttpException;

class MiniOrderService
{
    /**
     * @var Application
     */
    protected static $instance;

    /**
     * @param array $config
     * @return array[]
     *
     * @date 2023/05/09
     * @author yyw
     */
    protected static function options(array $config = [])
    {
        $payment = SystemConfigService::more(['routine_appId', 'routine_appsecret', 'pay_weixin_mchid', 'pay_new_weixin_open', 'pay_new_weixin_mchid', 'wechat_token', 'wechat_encodingaeskey']);
        return [
            'mini_program' => [
                'app_id' => $payment['routine_appId'] ?? '',
                'secret' => $payment['routine_appsecret'] ?? '',
                'merchant_id' => empty($payment['pay_new_weixin_open']) ? trim($payment['pay_weixin_mchid']) : trim($payment['pay_new_weixin_mchid']),
            ]
        ];
    }

    /**
     * Khởi tạo
     * @param bool $cache
     * @return Application
     */
    protected static function application($cache = false)
    {
        (self::$instance === null || $cache === true) && (self::$instance = new Application(self::options()));
        return self::$instance;
    }

    protected static function order()
    {
        return self::application()->order_ship;
    }


    /**
     * Tải lên đơn hàng
     * @param string $out_trade_no Mã đơn hàng (mã đơn hàng của shop)
     * @param int $logistics_type Chế độ vận chuyển, giá trị enum cách giao hàng: 1. Vận chuyển vật lý, dùng đơn vị vận chuyển để giao hàng vật lý 2. Giao hàng nội thành 3. Sản phẩm ảo, sản phẩm ảo ví dụ nạp tiền điện thoại, thẻ game..., không có hình thức giao hàng vật lý 4. Người dùng tự lấy hàng
     * @param array $shipping_list Danh sách thông tin vận chuyển, danh sách vận đơn giao hàng, hỗ trợ hai chế độ: giao hàng thống nhất (một vận đơn) và giao hàng chia nhỏ (nhiều vận đơn), số lượng: [1, 10]
     * @param string $payer_openid Người thanh toán, thông tin người thanh toán
     * @param int $delivery_mode Chế độ giao hàng, giá trị enum chế độ giao hàng: 1. UNIFIED_DELIVERY (giao hàng thống nhất) 2. SPLIT_DELIVERY (giao hàng chia nhỏ). Giá trị ví dụ: UNIFIED_DELIVERY
     * @param bool $is_all_delivered Bắt buộc điền khi ở chế độ giao hàng chia nhỏ, dùng để xác định đã giao hàng xong toàn bộ ở chế độ chia nhỏ chưa, chỉ khi giao hàng xong toàn bộ mới đẩy thông báo hoàn tất giao hàng cho người dùng. Giá trị ví dụ: true/false
     * @return array
     *
     * @throws HttpException
     * @date 2023/05/09
     * @author yyw
     */
    public static function shippingByTradeNo(string $out_trade_no, int $logistics_type, array $shipping_list, string $payer_openid, string $path, int $delivery_mode = 1, bool $is_all_delivered = true)
    {
        return self::order()->shippingByTradeNo($out_trade_no, $logistics_type, $shipping_list, $payer_openid, $path, $delivery_mode, $is_all_delivered);
    }

    /**
     * Hợp đơn
     * @param string $out_trade_no
     * @param int $logistics_type
     * @param array $sub_orders
     * @param string $payer_openid
     * @param int $delivery_mode
     * @param bool $is_all_delivered
     * @return array
     * @throws HttpException
     *
     * @date 2023/05/10
     * @author yyw
     */
    public static function combinedShippingByTradeNo(string $out_trade_no, int $logistics_type, array $sub_orders, string $payer_openid, int $delivery_mode = 2, bool $is_all_delivered = false)
    {
        return self::order()->combinedShippingByTradeNo($out_trade_no, $logistics_type, $sub_orders, $payer_openid, $delivery_mode, $is_all_delivered);
    }

    /**
     * Thông báo ký nhận
     * @param string $merchant_trade_no
     * @param string $received_time
     * @return array
     *
     * @date 2023/05/09
     * @author yyw
     */
    public static function notifyConfirmByTradeNo(string $merchant_trade_no, string $received_time)
    {
        return self::order()->notifyConfirmByTradeNo($merchant_trade_no, $received_time);
    }

    /**
     * Kiểm tra đã mở chưa
     * @return bool
     * @throws HttpException
     *
     * @date 2023/05/17
     * @author yyw
     */
    public static function isManaged()
    {
        return self::order()->checkManaged();
    }


    /**
     * Đặt đường dẫn chuyển trang Mini Program
     * @param $path
     * @return array
     * @throws HttpException
     *
     * @date 2023/05/10
     * @author yyw
     */
    public static function setMesJumpPathAndCheck($path)
    {
        return self::order()->setMesJumpPathAndCheck($path);
    }


}
