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
namespace crmeb\services\printer\storage;

use crmeb\services\CacheService;
use crmeb\services\printer\BasePrinter;

/**
 * Class YiLianYun
 * @package crmeb\services\printer\storage
 */
class YiLianYun extends BasePrinter
{

    /**
     * Khởi tạo
     * @param array $config
     * @return mixed|void
     */
    protected function initialize(array $config){}

    /**
     * Bắt đầu in
     * @return bool|mixed|string
     * @throws \Exception
     */
    public function startPrinter()
    {
        if (!$this->printerContent) {
            return $this->setError('Missing print');
        }
        $request = $this->accessToken->postRequest('https://open-api.10ss.net/print/index', [
            'client_id' => $this->accessToken->clientId,
            'access_token' => $this->accessToken->getAccessToken(),
            'machine_code' => $this->accessToken->machineCode,
            'content' => $this->printerContent,
            'origin_id' => 'crmeb' . time(),
            'sign' => strtolower(md5($this->accessToken->clientId . time() . $this->accessToken->apiKey)),
            'id' => $this->accessToken->createUuid(),
            'timestamp' => time()
        ]);
        if ($request === false) {
            return $this->setError('request was aborted');
        }
        $request = is_string($request) ? json_decode($request, true) : $request;
        if (isset($request['error']) && in_array($request['error'], [18, 14])) {
            CacheService::delete('YLY_access_token');
            return $this->setError('Accesstoken has expired');
        }
        return $request;
    }

    /**
     * Đặt nội dung in
     * @param $content
     * @param int $times
     * @return YiLianYun
     */
    public function setPrinterContent($content, int $times = 1): self
    {
        $this->printerContent = $content;
        return $this;
    }

//    /**
//     * Đặt nội dung in
//     * @param array $config
//     * @return YiLianYun
//     */
//    public function setPrinterContent(array $config): self
//    {
//        $printTime = date('Y-m-d H:i:s', time());
//        $goodsStr = '<table><tr><td>Tên sản phẩm</td><td>Số lượng</td><td>Đơn giá</td><td>Thành tiền</td></tr>';
//        $product = $config['product'];
//        foreach ($product as $item) {
//            $goodsStr .= '<tr>';
//            $price = bcmul((string)$item['cart_num'], (string)$item['truePrice'], 2);
//            $goodsStr .= "<td>{$item['productInfo']['store_name']} | {$item['productInfo']['attrInfo']['suk']}</td><td>{$item['cart_num']}</td><td>{$item['truePrice']}</td><td>{$price}</td>";
//            $goodsStr .= '</tr>';
//            unset($price);
//        }
//        $goodsStr .= '</table>';
//        $orderInfo = $config['orderInfo'];
//        $orderTime = date('Y-m-d H:i:s',$orderInfo['pay_time']);
//        $name = $config['name'];
//        $this->printerContent = <<<CONTENT
//<FB><center> ** {$name} **</center></FB>
//<FH2><FW2>----------------</FW2></FH2>
//Mã đơn hàng: {$orderInfo['order_id']}\r
//Thời gian in: {$printTime} \r
//Thời gian thanh toán: {$orderTime}\r
//Họ tên: {$orderInfo['real_name']}\r
//Điện thoại: {$orderInfo['user_phone']}\r
//Địa chỉ: {$orderInfo['user_address']}\r
//Điểm thưởng tặng: {$orderInfo['gain_integral']}\r
//Ghi chú đơn hàng: {$orderInfo['mark']}\r
//*************Sản phẩm***************\r
//{$goodsStr}
//********************************\r
//<FH>
//<LR>Tổng cộng: ₫{$orderInfo['total_price']}, ưu đãi: ₫{$orderInfo['coupon_price']}</LR>
//<LR>Phí vận chuyển: ₫{$orderInfo['pay_postage']}, khấu trừ: ₫{$orderInfo['deduction_price']}</LR>
//<right>Thực tế thanh toán: ₫{$orderInfo['pay_price']}</right>
//</FH>
//<FS><center> ** Hoàn tất **</center></FS>
//CONTENT;
//        return $this;
//    }
}
