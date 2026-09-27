<?php

namespace crmeb\services\printer\storage;

use crmeb\services\printer\BasePrinter;

class FeiEYun extends BasePrinter
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
        $time = time();
        $request = $this->accessToken->postRequest('http://api.feieyun.cn/Api/Open/', [
            'user' => $this->accessToken->feyUser,
            'stime' => $time,
            'sig' => sha1($this->accessToken->feyUser . $this->accessToken->feyUkey . $time),
            'apiname' => 'Open_printMsg',
            'sn' => $this->accessToken->feySn,
            'content' => $this->printerContent,
            'times' => $this->times
        ]);
        $res = json_decode($request, true);
        if ($res['msg'] == 'ok') {
            return $res;
        } else {
            return $this->setError($res['msg']);
        }
    }

    public function setPrinterContent($content, $times = 1): self
    {
        $this->times = $times;
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
//        $product = $config['product'];
//        $orderInfo = $config['orderInfo'];
//        $orderTime = date('Y-m-d H:i:s', $orderInfo['pay_time']);
//        $this->printerContent = '<CB>**' . $config['name'] . '**</CB><BR>';
//        $this->printerContent .= '--------------------------------<BR>';
//        $this->printerContent .= 'Mã đơn hàng: ' . $orderInfo['order_id'] . '<BR>';
//        $this->printerContent .= 'Thời gian in: ' . $printTime . '<BR>';
//        $this->printerContent .= 'Thời gian thanh toán: ' . $orderTime . '<BR>';
//        $this->printerContent .= 'Họ tên: ' . $orderInfo['real_name'] . '<BR>';
//        $this->printerContent .= 'Điện thoại: ' . $orderInfo['user_phone'] . '<BR>';
//        $this->printerContent .= 'Địa chỉ: ' . $orderInfo['user_address'] . '<BR>';
//        $this->printerContent .= 'Điểm thưởng tặng: ' . $orderInfo['gain_integral'] . '<BR>';
//        $this->printerContent .= 'Ghi chú đơn hàng: ' . $orderInfo['mark'] . '<BR>';
//        $this->printerContent .= '**************Sản phẩm**************<BR>';
//        $this->printerContent .= 'Tên           Đơn giá  SL Thành tiền<BR>';
//        $this->printerContent .= '--------------------------------<BR>';
//        foreach ($product as $item) {
//            $name = $item['productInfo']['store_name'] . " | " . $item['productInfo']['attrInfo']['suk'];
//            $price = $item['truePrice'];
//            $num = $item['cart_num'];
//            $prices = bcmul((string)$item['cart_num'], (string)$item['truePrice'], 2);
//            $kw3 = '';
//            $kw1 = '';
//            $kw2 = '';
//            $kw4 = '';
//            $str = $name;
//            $blankNum = 14;//tên giới hạn 14 byte
//            $lan = mb_strlen($str, 'utf-8');
//            $m = 0;
//            $j = 1;
//            $blankNum++;
//            $result = array();
//            if (strlen($price) < 6) {
//                $k1 = 6 - strlen($price);
//                for ($q = 0; $q < $k1; $q++) {
//                    $kw1 .= ' ';
//                }
//                $price = $price . $kw1;
//            }
//            if (strlen($num) < 3) {
//                $k2 = 3 - strlen($num);
//                for ($q = 0; $q < $k2; $q++) {
//                    $kw2 .= ' ';
//                }
//                $num = $num . $kw2;
//            }
//            if (strlen($prices) < 6) {
//                $k3 = 6 - strlen($prices);
//                for ($q = 0; $q < $k3; $q++) {
//                    $kw4 .= ' ';
//                }
//                $prices = $prices . $kw4;
//            }
//            for ($i = 0; $i < $lan; $i++) {
//                $new = mb_substr($str, $m, $j, 'utf-8');
//                $j++;
//                if (mb_strwidth($new, 'utf-8') < $blankNum) {
//                    if ($m + $j > $lan) {
//                        $m = $m + $j;
//                        $tail = $new;
//                        $lenght = iconv("UTF-8", "GBK//IGNORE", $new);
//                        $k = 14 - strlen($lenght);
//                        for ($q = 0; $q < $k; $q++) {
//                            $kw3 .= ' ';
//                        }
//                        if ($m == $j) {
//                            $tail .= $kw3 . ' ' . $price . ' ' . $num . ' ' . $prices;
//                        } else {
//                            $tail .= $kw3 . '<BR>';
//                        }
//                        break;
//                    } else {
//                        $next_new = mb_substr($str, $m, $j, 'utf-8');
//                        if (mb_strwidth($next_new, 'utf-8') < $blankNum) {
//                            continue;
//                        } else {
//                            $m = $i + 1;
//                            $result[] = $new;
//                            $j = 1;
//                        }
//                    }
//                }
//            }
//            $head = '';
//            foreach ($result as $key => $value) {
//                if ($key < 1) {
//                    $v_lenght = iconv("UTF-8", "GBK//IGNORE", $value);
//                    $v_lenght = strlen($v_lenght);
//                    if ($v_lenght == 13) $value = $value . " ";
//                    $head .= $value . ' ' . $price . ' ' . $num . ' ' . $prices;
//                } else {
//                    $head .= $value . '<BR>';
//                }
//            }
//            $this->printerContent .= $head . $tail;
//            unset($price);
//        }
//        $this->printerContent .= '--------------------------------<BR>';
//        $this->printerContent .= 'Tổng cộng: ' . number_format($orderInfo['total_price'], 2) . 'đ<BR>';
//        $this->printerContent .= 'Phí vận chuyển: ' . number_format($orderInfo['pay_postage'], 2) . 'đ<BR>';
//        $this->printerContent .= 'Ưu đãi: ' . number_format($orderInfo['coupon_price'], 2) . 'đ<BR>';
//        $this->printerContent .= 'Khấu trừ: ' . number_format($orderInfo['deduction_price'], 2) . 'đ<BR>';
//        $this->printerContent .= 'Thực tế thanh toán: ' . number_format($orderInfo['pay_price'], 2) . 'đ<BR>';
//        $this->printerContent .= '<QR>' . $config['url'] . '</QR>';//dùng thẻ bao chuỗi mã QR đã phân giải là tự động tạo ra mã QR
//        return $this;
//    }
}
