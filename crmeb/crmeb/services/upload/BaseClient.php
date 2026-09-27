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

namespace crmeb\services\upload;

/**
 * Yêu cầu cơ bản
 * Class BaseClient
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/5/18
 * @package crmeb\services\upload
 */
abstract class BaseClient
{

    /**
     * Có phân tích thành xml không
     * @var bool
     */
    protected $isXml = true;

    /**
     *
     * @var []callable
     */
    protected $curlFn = [];

    /**
     * @param callable $curlFn
     * @return $this
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/18
     */
    public function middleware(callable $curlFn)
    {
        $this->curlFn[] = $curlFn;
        return $this;
    }

    /**
     * Khởi tạo yêu cầu
     * @param string $url
     * @param string $method
     * @param array $data
     * @param array $clientHeader
     * @param int $timeout
     * @return array|extend\cos\SimpleXMLElement
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/18
     */
    protected function requestClient(string $url, string $method, array $data = [], array $clientHeader = [], int $timeout = 10)
    {
        $headers = [];
        foreach ($clientHeader as $key => $item) {
            $headers[] = $key . ':' . $item;
        }
        $curl = curl_init($url);
        //Phương thức yêu cầu
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        //Yêu cầu post
        if (!empty($data['body'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data['body']);
        } else if (!empty($data['json'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data['json']));
        } else {
            $curlFn = $this->curlFn;
            foreach ($curlFn as $item) {
                if ($item instanceof \Closure) {
                    $curlFn($curl);
                }
            }
        }
        //Thời gian timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        //Đặt header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

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
        [$content, $status] = [curl_exec($curl), curl_getinfo($curl)];
        $content = trim(substr($content, $status['header_size']));
        if ($this->isXml) {
            return XML::parse($content);
        } else {
            return json_decode($content, true);
        }
    }
}
