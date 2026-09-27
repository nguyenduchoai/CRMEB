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

namespace crmeb\services\pay;

/**
 * Lớp interface thanh toán
 * Interface PayInterface
 * @package crmeb\services\pay
 */
interface PayInterface
{

    /**
     * Đặt loại thanh toán
     * @param string $type Loại thanh toán
     * @return $this
     */
    public function setPayType(string $type);

    /**
     * Tạo thanh toán
     * @param string $orderId Mã đơn hàng
     * @param string $totalFee Số tiền thanh toán
     * @param string $attach Nội dung callback
     * @param string $body Body thanh toán
     * @param string $detail Chi tiết
     * @param string $tradeType Loại thanh toán
     * @param array $options Tham số khác
     * @return mixed
     */
    public function create(string $orderId, string $totalFee, string $attach, string $body, string $detail, array $options = []);

    /**
     * Doanh nghiệp trả tiền vào số dư
     * @param string $openid openid
     * @param string $orderId ID đơn hàng
     * @param string $amount Số tiền thanh toán
     * @param array $options Tham số khác
     * @return mixed
     */
    public function merchantPay(string $openid, string $orderId, string $amount, array $options = []);

    /**
     * Hoàn tiền
     * @param string $outTradeNo Mã đơn hoàn tiền
     * @param string $totalAmount Số tiền hoàn
     * @param string $refund_id Hoàn tiền
     * @param array $options Tham số khác
     * @return mixed
     */
    public function refund(string $outTradeNo, array $options = []);

    /**
     * Truy vấn đơn hàng
     * @param string $outTradeNo Mã đơn hoàn tiền
     * @param string $outRequestNo Mã đơn merchant thanh toán
     * @param array $other Tham số khác
     * @return mixed
     */
    public function queryRefund(string $outTradeNo, string $outRequestNo, array $other = []);

    /**
     * Callback thanh toán
     * @return mixed
     */
    public function handleNotify();

}
