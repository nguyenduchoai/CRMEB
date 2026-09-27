<?php

namespace crmeb\services\invoice\storage;

use crmeb\services\invoice\BaseInvoice;

class Yihaotong extends BaseInvoice
{
    /**
     * Lấy địa chỉ iframe của trang xuất hóa đơn
     */
    const INVOICE_ISSUANCE_URL = 'v2/invoice/invoice_issuance_url';

    /**
     * Tải xuống hóa đơn
     */
    const DOWNLOAD_INVOICE = 'v2/invoice/download_invoice';

    /**
     * Xem chi tiết hóa đơn
     */
    const INVOICE_INFO = 'v2/invoice/invoice_info';

    /**
     * Lấy danh mục sản phẩm
     */
    const CATEGORY = 'v2/invoice/category';

    /**
     * Xuất hóa đơn
     */
    const INVOICE_ISSUANCE = 'v2/invoice/invoice_issuance';

    /**
     * Yêu cầu hóa đơn đỏ
     */
    const APPLY_RED_INVOICE = 'v2/invoice/apply_red_invoice';

    /**
     * Xuất hóa đơn điều chỉnh giảm
     */
    const RED_INVOICE_ISSUANCE = 'v2/invoice/red_invoice_issuance';


    /**
     * @param array $config
     * @return mixed|void
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/13
     */
    protected function initialize(array $config = [])
    {
        parent::initialize($config);
    }

    /**
     * Lấy địa chỉ iframe của trang xuất hóa đơn
     * @param array $params
     * @return array|mixed
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/13
     */
    public function invoiceIssuanceUrl(array $params = [])
    {
        return $this->accessToken->httpRequest(self::INVOICE_ISSUANCE_URL, $params);
    }

    /**
     * Tải xuống hóa đơn
     * @param string $invoiceNum
     * @return array|mixed
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/13
     */
    public function downloadInvoice(string $invoiceNum = '')
    {
        return $this->accessToken->httpRequest(self::DOWNLOAD_INVOICE . '/' . $invoiceNum, [], 'GET');
    }

    /**
     * Xem chi tiết hóa đơn
     * @param string $invoiceNum
     * @return array|mixed
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/14
     */
    public function invoiceInfo(string $invoiceNum = '')
    {
        return $this->accessToken->httpRequest(self::INVOICE_INFO . '/' . $invoiceNum, [], 'GET');
    }

    /**
     * Lấy danh mục sản phẩm
     * @return array|mixed
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/15
     */
    public function category(array $params = [])
    {
        return $this->accessToken->httpRequest(self::CATEGORY, $params, 'GET');
    }

    /**
     * Xuất hóa đơn
     * @param string $unique
     * @param array $params
     * @return array|mixed
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/15
     */
    public function invoiceIssuance(string $unique = '', array $params = [])
    {
        return $this->accessToken->httpRequest(self::INVOICE_ISSUANCE . '/' . $unique, $params);
    }

    /**
     * Yêu cầu hóa đơn đỏ
     * @param array $params
     * @return array|mixed
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/16
     */
    public function applyRedInvoice(array $params = [])
    {
        return $this->accessToken->httpRequest(self::APPLY_RED_INVOICE, $params);
    }

    /**
     * Xuất hóa đơn điều chỉnh giảm
     * @param array $params
     * @return array|mixed
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/16
     */
    public function redInvoiceIssuance(array $params = [])
    {
        return $this->accessToken->httpRequest(self::RED_INVOICE_ISSUANCE . '/' . $params['invoiceNum'], $params);
    }
}