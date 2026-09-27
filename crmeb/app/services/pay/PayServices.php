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
declare (strict_types=1);

namespace app\services\pay;

use crmeb\exceptions\ApiException;
use crmeb\services\pay\Pay;

/**
 * Cổng thanh toán thống nhất
 * Class PayServices
 * @package app\services\pay
 */
class PayServices
{
    //Loại WeChat Pay
    const WEIXIN_PAY = 'weixin';

    //Thanh toán bằng số dư
    const YUE_PAY = 'yue';

    //Thanh toán ngoại tuyến
    const OFFLINE_PAY = 'offline';

    //Alipay
    const ALIAPY_PAY = 'alipay';

    //Allinpay
    const ALLIN_PAY = 'allinpay';

    //Bạn bè thanh toán hộ
    const FRIEND = 'friend';

    //Chuyển khoản ngân hàng
    const BANK = 'bank';

    //Phương thức thanh toán
    const PAY_TYPE = [
        PayServices::WEIXIN_PAY => 'WeChat Pay',
        PayServices::YUE_PAY => 'Thanh toán bằng số dư',
        PayServices::OFFLINE_PAY => 'Thanh toán ngoại tuyến',
        PayServices::ALIAPY_PAY => 'Alipay',
        PayServices::FRIEND => 'Bạn bè thanh toán hộ',
        PayServices::ALLIN_PAY => 'Allinpay',
        PayServices::BANK => 'Chuyển khoản ngân hàng',
    ];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param string $key
     * @param $value
     * @return $this
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/1/16
     */
    public function setOption(string $key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * @param array $value
     * @return $this
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/1/16
     */
    public function setOptions(array $value)
    {
        $this->options = $value;
        return $this;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/1/16
     */
    protected function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Khởi tạo thanh toán
     * @param string $payType
     * @param string $openid
     * @param string $orderId
     * @param string $price
     * @param string $successAction
     * @param string $body
     * @return array|string
     */
    public function pay(string $payType, string $orderId, string $price, string $successAction, string $body, array $options = [])
    {
        try {

            //Tất cả đều là WeChat Pay
            if (in_array($payType, ['routine', 'weixinh5', 'weixin', 'pc', 'store'])) {
                $payType = 'wechat_pay';
                //Kiểm tra có dùng v3 không
                if (sys_config('pay_wechat_type') == 1) {
                    $payType = 'v3_wechat_pay';
                }
            } else {
                if ($payType == 'alipay') {
                    $payType = 'ali_pay';
                } elseif ($payType == 'allinpay') {
                    $payType = 'allin_pay';
                }
            }

            /** @var Pay $pay */
            $pay = app()->make(Pay::class, [$payType]);


            return $pay->create($orderId, $price, $successAction, $body, '', ['pay_new_weixin_open' => (bool)sys_config('pay_new_weixin_open')] + $options);

        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'api unauthorized rid') !== false) {
                throw new ApiException('Vui lòng vào cấu hình WeChat Pay, chuyển tùy chọn mã merchant của Mini Program sang “Liên kết mã merchant”');
            }
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * TODO khởi tạo thanh toán, đã loại bỏ
     * @param string $payType
     * @param string $openid
     * @param string $orderId
     * @param string $price
     * @param string $successAction
     * @param string $body
     * @return array|string
     */
//    public function pay(string $payType, string $openid, string $orderId, string $price, string $successAction, string $body, bool $isCode = false)
//    {
//        try {
//
//            //Tất cả đều là WeChat Pay
//            if (in_array($payType, ['routine', 'weixinh5', 'weixin', 'pc', 'store'])) {
//                $payType = 'wechat_pay';
//                //Kiểm tra có dùng v3 không
//                if (sys_config('pay_wechat_type') == 1) {
//                    $payType = 'v3_wechat_pay';
//                }
//            }
//
//            if ($payType == 'alipay') {
//                $payType = 'ali_pay';
//            }
//
//
//            $options = [];
//            if (self::ALLIN_PAY === $payType) {
//                $options['returl'] = $this->getOption('returl');
//                if ($options['returl']) {
//                    $options['returl'] = str_replace('http://', 'https://', $options['returl']);
//                }
//                $options['is_wechat'] = $this->getOption('is_wechat', false);
//                $options['appid'] = sys_config('routine_appId');
//                $payType = 'allin_pay';
//            }
//
//            /** @var Pay $pay */
//            $pay = app()->make(Pay::class, [$payType]);
//
//
//            return $pay->create($orderId, $price, $successAction, $body, '', ['openid' => $openid, 'isCode' => $isCode, 'pay_new_weixin_open' => (bool)sys_config('pay_new_weixin_open')] + $options);
//
//        } catch (\Exception $e) {
//            if (strpos($e->getMessage(), 'api unauthorized rid') !== false) {
//                throw new ApiException('Vui lòng vào cấu hình WeChat Pay đổi lựa chọn mã merchant Mini Program thành liên kết mã merchant');
//            }
//            throw new ApiException($e->getMessage());
//        }
//    }
}
