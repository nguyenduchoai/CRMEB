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

namespace crmeb\services;

use think\facade\Log;

/**
 * Class HttpService
 * @package crmeb\services
 */
class HttpService
{
    /**
     * Thông tin lỗi
     * @var string
     */
    private static $curlError;

    /**
     * Thông tin header
     * @var string
     */
    private static $headerStr;

    /**
     * Trạng thái yêu cầu
     * @var int
     */
    private static $status;

    /**
     * @return string
     */
    public static function getCurlError()
    {
        return self::$curlError;
    }

    /**
     * @return mixed
     */
    public static function getStatus()
    {
        return self::$status;
    }

    /**
     * Giả lập gửi yêu cầu GET
     * @param $url
     * @param array $data
     * @param bool $header
     * @param int $timeout
     * @return bool|string
     */
    public static function getRequest($url, $data = array(), $header = false, $timeout = 10)
    {
        if (!empty($data)) {
            $url .= (stripos($url, '?') === false ? '?' : '&');
            $url .= (is_array($data) ? http_build_query($data) : $data);
        }
        return self::request($url, 'get', array(), $header, $timeout);
    }

    /**
     * Yêu cầu curl
     * @param $url
     * @param string $method
     * @param array $data
     * @param bool $header
     * @param int $timeout
     * @return bool|string
     */
    public static function request($url, $method = 'get', $data = array(), $header = false, $timeout = 15)
    {
        self::$status = null;
        self::$curlError = null;
        self::$headerStr = null;

        $curl = curl_init($url);
        $method = strtoupper($method);
        //Phương thức yêu cầu
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        //Tham số kèm theo
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, is_array($data) ? http_build_query($data) : $data);
        } elseif ($method == 'GET' && count($data)) {
            $url .= '?' . http_build_query($data);
            curl_setopt($curl, CURLOPT_URL, $url);
        }
        //Thời gian timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        //Đặt header
        if ($header !== false) curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        //Trả về dữ liệu thu thập
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //Xuất thông tin header
        curl_setopt($curl, CURLOPT_HEADER, true);
        //Khi TRUE thì theo dõi chuỗi yêu cầu của handle, khả dụng từ PHP 5.1.3. Cái này rất quan trọng, nó cho phép bạn xem header của yêu cầu
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        //Yêu cầu https
        if (1 == strpos("$" . $url, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        self::$curlError = curl_error($curl);

        list($content, $status) = [curl_exec($curl), curl_getinfo($curl), curl_close($curl)];
        self::$status = $status;
        self::$headerStr = trim(substr($content, 0, $status['header_size']));
        $content = trim(substr($content, $status['header_size']));
        return (intval($status["http_code"]) === 200) ? $content : false;
    }

    /**
     * Giả lập gửi yêu cầu POST
     * @param $url
     * @param $data
     * @param bool $header
     * @param int $timeout
     * @return bool|string
     */
    public static function postRequest($url, $data = array(), $header = false, $timeout = 10)
    {
        return self::request($url, 'post', $data, $header, $timeout);
    }

    /**
     * Lấy header dạng chuỗi
     * @return mixed
     */
    public static function getHeaderStr()
    {
        return self::$headerStr;
    }

    /**
     * Lấy header dạng mảng
     * @return array
     */
    public static function getHeader()
    {
        $headArr = explode("\r\n", self::$headerStr);
        return $headArr;
    }

}
