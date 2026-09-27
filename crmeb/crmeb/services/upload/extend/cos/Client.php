<?php
/**
 *  +----------------------------------------------------------------------
 *  | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2016~2022 https://www.crmeb.com All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
 *  +----------------------------------------------------------------------
 *  | Author: CRMEB Team <admin@crmeb.com>
 *  +----------------------------------------------------------------------
 */

namespace crmeb\services\upload\extend\cos;

use crmeb\exceptions\UploadException;
use crmeb\services\upload\XML;

/**
 * Class Client
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2022/9/29
 * @package crmeb\services\upload\extend\cos
 */
class Client
{

    /**
     * @var string
     */
    protected $accessKey;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var string
     */
    protected $appid;

    /**
     * @var mixed|string
     */
    protected $bucket;

    /**
     * @var mixed|string
     */
    protected $region;

    /**
     * @var mixed|string
     */
    protected $uploadUrl;

    /**
     * @var string
     */
    protected $action = '';

    /**
     * @var array
     */
    protected $response = ['content' => null, 'code' => 200, 'header' => []];

    /**
     * @var array
     */
    protected $request = ['header' => [], 'body' => [], 'host' => ''];

    /**
     * @var string
     */
    protected $cosacl = 'public-read';

    /**
     * Client constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->accessKey = $config['accessKey'] ?? '';
        $this->secretKey = $config['secretKey'] ?? '';
        $this->appid = $config['appid'] ?? '';
        $this->bucket = $config['bucket'] ?? '';
        $this->region = $config['region'] ?? 'ap-chengdu';
        $this->uploadUrl = $config['uploadUrl'] ?? '';
    }

    /**
     * Lấy yêu cầu thực tế
     * @return array
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/10/17
     */
    public function getResponse()
    {
        $response = $this->response;

        $this->response = ['content' => null, 'http_code' => 200, 'header' => []];

        return $response;
    }

    /**
     * @return array
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/10/17
     */
    public function getRequest()
    {
        $request = $this->request;

        $this->request = ['header' => [], 'body' => [], 'host' => ''];

        return $request;
    }

    /**
     * Ghép địa chỉ yêu cầu
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/9/29
     */
    protected function makeUpUrl()
    {
        return $this->bucket . '.cos.' . $this->region . '.myqcloud.com';
    }

    /**
     * @return bool
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/9/29
     */
    protected function ssl()
    {
        return strstr($this->uploadUrl, 'https://') !== false;
    }

    /**
     * Kiểm tra tham số
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/9/29
     */
    protected function checkOptions()
    {
        if (!$this->bucket) {
            throw new UploadException('Vui lòng cung cấp tên bucket');
        }
        if (!$this->region) {
            throw new UploadException('Vui lòng cung cấp khu vực');
        }
        if (!$this->accessKey) {
            throw new UploadException('Vui lòng cung cấp SecretId');
        }
        if (!$this->secretKey) {
            throw new UploadException('Vui lòng cung cấp SecretKey');
        }
    }

    /**
     * Tải lên file
     * @param string $key
     * @param $body
     * @return string[]
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/9/29
     */
    public function putObject(string $key, $body)
    {

        $this->checkOptions();

        $url = $this->makeUpUrl();

        $header = [
            'Content-Type' => 'image/jpeg',
            'x-cos-acl' => $this->cosacl,
            'Content-MD5' => base64_encode(md5($body, true)),
            'Host' => $url
        ];

        $imageUrl = ($this->ssl() ? 'https://' : 'http://') . $url . '/' . $key;

        $res = $this->request($imageUrl, 'PUT', ['body' => $body], $header);

        if ($res && !empty($res['Message'])) {
            throw new UploadException($res['Message']);
        }

        return [
            'name' => $key,
            'path' => $imageUrl
        ];
    }

    /**
     * Xóa file
     * @param string $bucket
     * @param string $key
     * @return array|false
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/10/19
     */
    public function deleteObject(string $bucket, string $key)
    {
        $url = $this->getRequestHost($bucket);

        $header = [
            'Host' => $url
        ];

        $res = $this->request('https://' . $url . '/' . $key, 'delete', [], $header);

        if ($res && !empty($res['Message'])) {
            throw new UploadException($res['Message']);
        }

        return $res;
    }

    /**
     * Lấy danh sách bucket
     * @return array|false|\SimpleXMLElement|string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/10/19
     */
    public function listBuckets()
    {
        $url = 'service.cos.myqcloud.com';

        $header = [
            'Host' => $url
        ];

        $res = $this->request('https://' . $url . '/', 'get', [], $header);

        if ($res && !empty($res['Message'])) {
            throw new UploadException($res['Message']);
        }

        return $res;
    }

    /**
     * Kiểm tra bucket, không tồn tại thì trả về true
     * @param string $bucket
     * @param string $region
     * @return bool
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/10/17
     */
    public function headBucket(string $bucket, string $region = '')
    {
        $url = $this->getRequestHost($bucket, $region);

        $header = [
            'Host' => $url
        ];

        $this->request('https://' . $url, 'head', [], $header);

        $response = $this->getResponse();

        return $response['code'] == 404;
    }

    /**
     * Tạo bucket
     * @param string $bucket
     * @param string $region
     * @param string $acl
     * @return array|false|\SimpleXMLElement|string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/10/17
     */
    public function createBucket(string $bucket, string $region = '', string $acl = 'public-read')
    {
        return $this->noBodyRequest('put', $bucket, $region, $acl);
    }

    /**
     * Kết hợp thành xml
     * @param array $data
     * @param string $root
     * @param string $itemKey
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/10/17
     */
    protected function xmlBuild(array $xmlAttr, string $root = 'xml', string $itemKey = 'item')
    {
        $xml = '<' . $root . '>';
        $xml .= '<' . $itemKey . '>';

        foreach ($xmlAttr as $kk => $vv) {
            if (is_array($vv)) {
                foreach ($vv as $v) {
                    $xml .= '<' . $kk . '>' . $v . '</' . $kk . '>';
                }
            } else {
                $xml .= '<' . $kk . '>' . $vv . '</' . $kk . '>';
            }
        }
        $xml .= '</' . $itemKey . '>';
        $xml .= '</' . $root . '>';

        return $xml;
    }

    /**
     * Đặt CORS
     * @param string $bucket
     * @param string $region
     * @param array $data
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/10/17
     */
    public function putBucketCors(string $bucket, array $data, string $region = '')
    {
        $url = $this->getRequestHost($bucket, $region);

        $xml = $this->xmlBuild($data, 'CORSConfiguration', 'CORSRule');

        $header = [
            'Host' => $url,
            'Content-Type' => 'application/xml',
            'Content-Length' => strlen($xml),
            'Content-MD5' => base64_encode(md5($xml, true))
        ];

        $res = $this->request('https://' . $url . '/?cors', 'put', ['xml' => $xml], $header);

        if ($res && !empty($res['Message'])) {
            throw new UploadException($res['Message']);
        }

        return $res;
    }

    /**
     * Xóa
     * @param string $name
     * @param string $region
     * @return array|false|\SimpleXMLElement|string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/10/17
     */
    public function deleteBucket(string $name, string $region = '')
    {
        return $this->noBodyRequest('delete', $name, $region);
    }

    /**
     * Lấy trong bucket
     * @param string $name
     * @param string $region
     * @return array|false|\SimpleXMLElement|string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/10/17
     */
    public function getBucketDomain(string $name, string $region = '')
    {
        $this->action = 'domain';
        return $this->noBodyRequest('get', $name, $region);
    }

    /**
     * Gắn domain
     * @param string $bucket
     * @param string $region
     * @param array $data
     * @return array|false|\SimpleXMLElement|string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/10/19
     */
    public function putBucketDomain(string $bucket, string $region, array $data)
    {
        $url = $this->getRequestHost($bucket, $region);

        $xml = $this->xmlBuild($data, 'DomainConfiguration', 'DomainRule');

        $header = [
            'Host' => $url,
            'Content-Type' => 'application/xml',
            'Content-Length' => strlen($xml),
            'Content-MD5' => base64_encode(md5($xml, true))
        ];

        $res = $this->request('https://' . $url . '/?domain', 'put', ['xml' => $xml], $header);

        if ($res && !empty($res['Message'])) {
            throw new UploadException($res['Message']);
        }

        return $res;
    }

    /**
     * @param string $bucket
     * @param string $region
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/10/17
     */
    protected function getRequestHost(string $bucket, string $region = '')
    {
        if (!$this->accessKey) {
            throw new UploadException('Vui lòng cung cấp SecretId');
        }
        if (!$this->secretKey) {
            throw new UploadException('Vui lòng cung cấp SecretKey');
        }

        if (strstr($bucket, '-') === false) {
            $bucket = $bucket . '-' . $this->appid;
        }

        return $bucket . '.cos.' . ($region ?: $this->region) . '.myqcloud.com';
    }

    /**
     * @param string $method
     * @param string $bucket
     * @param string $region
     * @param string|null $acl
     * @return array|false|\SimpleXMLElement|string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/10/17
     */
    public function noBodyRequest(string $method, string $bucket, string $region = '', string $acl = null, bool $isExc = true)
    {

        $url = $this->getRequestHost($bucket, $region);

        $header = [
            'Host' => $url
        ];

        if ($acl) {
            $header['x-cos-acl'] = $acl;
        }

        if (in_array($method, ['put', 'post'])) {
            $header['Content-Length'] = 0;
        }

        $res = $this->request('https://' . $url . '/' . ($this->action ? '?' . $this->action : ''), $method, [], $header);
        $this->action = '';

        if ($isExc) {
            if ($res && !empty($res['Message'])) {
                throw new UploadException($res['Message']);
            }
        }

        return $res;
    }

    /**
     * Khởi tạo yêu cầu
     * @param string $url
     * @param string $method
     * @param array $data
     * @param array $header
     * @param int $timeout
     * @return array|false|\SimpleXMLElement|string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/9/29
     */
    public function request(string $url, string $method, array $data, array $header = [], int $timeout = 5)
    {

        $this->request['body'] = $data;
        $this->request['host'] = $url;


        $urlAttr = parse_url($url);
        $curl = curl_init($url);
        $method = strtoupper($method);
        //Phương thức yêu cầu
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

        //Thời gian timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        //Đặt header

        $header = array_merge($header, $this->getSign($url, $method, $urlAttr['path'] ?? '', [], $header));

        $this->request['header'] = $header;

        $clientHeader = [];
        foreach ($header as $key => $item) {
            $clientHeader[] = $key . ':' . $item;
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $clientHeader);


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

        //Yêu cầu post
        if ($method == 'PUT' && !empty($data['body'])) {
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            // Chú ý 'file' ở đây là tên key được chỉ định bởi địa chỉ tải lên
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data['body']);
        }

        if (!empty($data['xml'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data['xml']);
        }

        list($content, $status) = [curl_exec($curl), curl_getinfo($curl), curl_close($curl)];

        $content = trim(substr($content, $status['header_size']));

        $this->response['content'] = $content;
        $this->response['code'] = $status['http_code'];
        $this->response['header'] = $status;

        $res = XML::parse($content);
        if ($res) {
            return $res;
        }
        return (intval($status["http_code"]) === 200) ? $content : false;
    }

    /**
     * Lấy chữ ký (signature)
     * @param string $method
     * @param string $urlPath
     * @param array $query
     * @param array $headers
     * @return array
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/9/27
     */
    public function getSign(string $url, string $method, string $urlPath, array $query = [], array $headers = [])
    {
        return (new Signature($this->accessKey, $this->secretKey, ['signHost' => $url]))->signRequest($method, $urlPath, $query, $headers);
    }
}
