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

namespace crmeb\services\upload\extend\obs;


use crmeb\exceptions\UploadException;
use crmeb\services\upload\BaseClient;
use crmeb\services\upload\extend\cos\XML;

/**
 * Tải lên Huawei Cloud
 * Class Client
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/5/18
 * @package crmeb\services\upload\extend\obs
 */
class Client extends BaseClient
{
    const HEADER_PREFIX = 'x-obs-';

    const INTEREST_HEADER_KEY_LIST = ['content-type', 'content-md5', 'date'];

    const ALTERNATIVE_DATE_HEADER = 'x-obs-date';

    const ALLOWED_RESOURCE_PARAMTER_NAMES = [
        'acl',
        'policy',
        'torrent',
        'logging',
        'location',
        'storageinfo',
        'quota',
        'storagepolicy',
        'requestpayment',
        'versions',
        'versioning',
        'versionid',
        'uploads',
        'uploadid',
        'partnumber',
        'website',
        'notification',
        'lifecycle',
        'deletebucket',
        'delete',
        'cors',
        'restore',
        'tagging',
        'response-content-type',
        'response-content-language',
        'response-expires',
        'response-cache-control',
        'response-content-disposition',
        'response-content-encoding',
        'x-image-process',

        'backtosource',
        'storageclass',
        'replication',
        'append',
        'position',
        'x-oss-process'
    ];

    //acl bucket
    const OBS_ACL = [
        [
            'value' => 'public-read',
            'label' => 'Đọc công khai (khuyên dùng)',
        ],
        [
            'value' => 'public-read-write',
            'label' => 'Đọc/ghi công khai',
        ],
    ];
    //acl mặc định
    const DEFAULT_OBS_ACL = 'public-read';

    protected $isCname = false;

    protected $pathStyle;

    /**
     * @var
     */
    protected $accessKeyId;

    /**
     * @var
     */
    protected $secretKey;

    /**
     * Tên bucket
     * @var string
     */
    protected $bucketName;

    /**
     * Khu vực
     * @var string
     */
    protected $region;

    /**
     * @var mixed|string
     */
    protected $uploadUrl;

    /**
     * @var string
     */
    protected $baseUrl = 'obs.cn-north-1.myhuaweicloud.com';

    protected $type = 'hw';

    /**
     * Client constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->accessKeyId = $config['accessKey'] ?? '';
        $this->secretKey = $config['secretKey'] ?? '';
        $this->bucketName = $config['bucket'] ?? '';
        $this->region = $config['region'] ?? 'ap-chengdu';
        $this->uploadUrl = $config['uploadUrl'] ?? '';
        $this->type = $config['type'] ?? 'hw';
    }

    /**
     * Kiểm tra
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/18
     */
    protected function checkOptions()
    {
        if (!$this->bucketName) {
            throw new UploadException('Vui lòng cung cấp tên bucket');
        }
        if (!$this->region) {
            throw new UploadException('Vui lòng cung cấp khu vực');
        }
        if (!$this->accessKeyId) {
            throw new UploadException('Vui lòng cung cấp SecretId');
        }
        if (!$this->secretKey) {
            throw new UploadException('Vui lòng cung cấp SecretKey');
        }

        return $this;
    }

    /**
     * Tải lên ảnh
     * @param string $key
     * @param $body
     * @return mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/18
     */
    public function putObject(string $key, $body, string $contentType = 'image/jpeg')
    {
        $header = [
            'Host' => $this->getRequestUrl($this->bucketName, $this->region),
            'Content-Type' => $contentType,
            'Content-Length' => strlen($body),
        ];

        $res = $this->checkOptions()->request('https://' . $header['Host'] . '/' . $key, 'PUT', [
            'bucket' => $this->bucketName,
            'body' => $body
        ], $header);

        return $this->response($res);
    }

    /**
     * Xóa object đã tải lên
     * @param string $key
     * @return mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/18
     */
    public function deleteObject(string $key)
    {
        $header = [
            'Host' => $this->getRequestUrl($this->bucketName, $this->region),
        ];

        $res = $this->request('https://' . $header['Host'] . '/' . $key, 'DELETE', [
            'bucket' => $this->bucketName
        ], $header);

        return $this->response($res);
    }

    /**
     * Lấy bucket
     * @return false|string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/16
     */
    public function listBuckets()
    {
        $header = [
            'Host' => $this->getRequestUrl('', $this->region),
        ];
        $res = $this->request('https://' . $header['Host'] . '/', 'GET', [], []);
        return $this->response($res);
    }

    public function headBucket(string $bucket, string $region)
    {
        $header = [
            'Host' => $this->getRequestUrl($bucket, $region),
        ];
        $res = $this->request('https://' . $header['Host'] . '/', 'HEAD', [], []);
        return $this->response($res);
    }

    /**
     * Đặt policy của bucket
     * @param string $bucket
     * @param string $region
     * @param array $data
     * @return mixed
     *
     * @date 2023/06/08
     * @author yyw
     */
    public function putPolicy(string $bucket, string $region, array $data)
    {
        $header = [
            'Host' => $this->getRequestUrl($bucket, $region),
            "Content-Type" => "application/json"
        ];
        $res = $this->request('https://' . $header['Host'] . '/?policy', 'PUT', [
            'bucket' => $bucket,
            'json' => $data
        ], $header);

        return $this->response($res);
    }

    /**
     * Tạo bucket
     * @param string $bucket
     * @param string $region
     * @param string $acl
     * @return mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/18
     */
    public function createBucket(string $bucket, string $region, string $acl = self::DEFAULT_OBS_ACL)
    {
        $header = [
            'x-obs-acl' => $acl,
            'Host' => $this->getRequestUrl($bucket, $region),
            "Content-Type" => "application/xml"
        ];
        $xml = "<CreateBucketConfiguration><Location>{$region}</Location></CreateBucketConfiguration>";
        $res = $this->request('https://' . $header['Host'] . '/', 'PUT', [
            'bucket' => $bucket,
            'body' => $xml
        ], $header);

        return $this->response($res);
    }

    /**
     * Xóa bucket
     * @param string $bucket
     * @param string $region
     * @return mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/18
     */
    public function deleteBucket(string $bucket, string $region)
    {
        $header = [
            'Host' => $this->getRequestUrl($bucket, $region),
        ];
        $res = $this->request('https://' . $header['Host'] . '/', 'DELETE', [
            'bucket' => $bucket
        ], $header);

        return $this->response($res);
    }

    /**
     * Lấy domain tùy chỉnh của bucket
     * @param string $bucket
     * @param string $region
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/18
     */
    public function getBucketDomain(string $bucket, string $region)
    {
        $header = [
            'Host' => $this->getRequestUrl($bucket, $region),
        ];
        $res = $this->request('https://' . $header['Host'] . '/?customdomain', 'GET', [
            'bucket' => $bucket
        ], $header);

        return $this->response($res);
    }

    /**
     * Đặt domain tùy chỉnh của bucket
     * @param string $bucket
     * @param string $region
     * @param array $data
     * @return mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/18
     */
    public function putBucketDomain(string $bucket, string $region, array $data = [])
    {
        $header = [
            'Host' => $this->getRequestUrl($bucket, $region),
        ];
        $res = $this->request('https://' . $header['Host'] . '/?customdomain=' . $data['domainname'], 'PUT', [
            'bucket' => $bucket
        ], $header);

        return $this->response($res);
    }

    /**
     * Đặt CORS
     * @return bool
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/18
     */
    public function putBucketCors(string $bucket, string $region, array $data = [])
    {
        $xml = $this->xmlBuild($data, 'CORSConfiguration', 'CORSRule');
        $header = [
            'Host' => $this->getRequestUrl($bucket, $region),
            'Content-Type' => 'application/xml',
            'Content-Length' => strlen($xml),
            'Content-MD5' => base64_encode(md5($xml, true))
        ];
        $res = $this->request('https://' . $header['Host'] . '/?cors', 'PUT', [
            'bucket' => $bucket,
            'body' => $xml
        ], $header);

        return $this->response($res);
    }

    /**
     * Xóa CORS
     * @param string $bucket
     * @param string $region
     * @return mixed
     *
     * @date 2023/06/08
     * @author yyw
     */
    public function deleteBucketCors(string $bucket, string $region)
    {
        $header = [
            'Host' => $this->getRequestUrl($bucket, $region),
        ];
        $res = $this->request('https://' . $header['Host'] . '/?cors', 'DELETE', [
            'bucket' => $bucket,
        ], $header);

        return $this->response($res);
    }

    /**
     * @param $res
     * @return mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/18
     */
    protected function response($res)
    {
        if (!empty($res['Code']) && !empty($res['Message'])) {
            throw new UploadException($res['Message']);
        }
        return $res;
    }

    /**
     * Lấy domain yêu cầu
     * @param string $bucket
     * @param string $region
     * @return string
     *
     * @date 2023/06/08
     * @author yyw
     */
    protected function getRequestUrl(string $bucket = '', string $region = '')
    {
        if ($this->type == 'hw') {
            $url = '.myhuaweicloud.com';  // Huawei
        } else {
            $url = '.ctyun.cn';  // Tianyi
        }
        if ($bucket) {
            return $bucket . '.obs.' . $region . $url;
        } else {
            return 'obs.' . $region . $url;
        }
    }


    /**
     * Tên region
     * @return \string[][]
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/17
     */
    public function getRegion()
    {
        return [
            [
                'value' => 'cn-north-1',
                'label' => 'Bắc Trung Quốc - Beijing 1',
            ],
//            [
//                'value' => 'cn-north-4',
//                'label' => 'North China - Beijing 4',
//            ],
            [
                'value' => 'cn-north-9',
                'label' => 'Bắc Trung Quốc - Ulanqab 1',
            ],
            [
                'value' => 'cn-east-2',
                'label' => 'Đông Trung Quốc - Shanghai 2',
            ],
            [
                'value' => 'cn-east-3',
                'label' => 'Đông Trung Quốc - Shanghai 1',
            ],
            [
                'value' => 'cn-south-1',
                'label' => 'Nam Trung Quốc - Guangzhou',
            ],
            [
                'value' => 'ap-southeast-1',
                'label' => 'Trung Quốc - Hồng Kông',
            ],
            [
                'value' => 'cn-south-4',
                'label' => 'Nam Trung Quốc - Guangzhou - Chỉ dành cho khách mời',
            ],
            [
                'value' => 'cn-southwest-2',
                'label' => 'Tây Nam Trung Quốc - Guiyang 1',
            ],
            [
                'value' => 'la-north-2',
                'label' => 'Mỹ Latinh - Mexico City 2',
            ],
            [
                'value' => 'na-mexico-1',
                'label' => 'Mỹ Latinh - Mexico City 1',
            ],
            [
                'value' => 'sa-brazil-1',
                'label' => 'Mỹ Latinh - São Paulo 1',
            ],
            [
                'value' => 'la-south-2',
                'label' => 'Mỹ Latinh - Santiago',
            ],
            [
                'value' => 'tr-west-1',
                'label' => 'Thổ Nhĩ Kỳ - Istanbul',
            ],
            [
                'value' => 'ap-southeast-2',
                'label' => 'Châu Á - Thái Bình Dương - Bangkok',
            ],
            [
                'value' => 'ap-southeast-3',
                'label' => 'Châu Á - Thái Bình Dương - Singapore',
            ],
            [
                'value' => 'af-south-1',
                'label' => 'Châu Phi - Johannesburg',
            ]
        ];
    }

    /**
     * Đặt tên bucket
     * @param string $bucketName
     * @return $this
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/16
     */
    public function setBucketName(string $bucketName)
    {
        $this->bucketName = $bucketName;
        return $this;
    }


    /**
     * Lấy chữ ký (signature)
     * @param array $result
     * @return array
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/17
     */
    protected function getSign(array $result)
    {
        $result['headers']['Date'] = gmdate('D, d M Y H:i:s \G\M\T');
        $canonicalstring = $this->makeCanonicalstring($result['method'], $result['headers'], $result['pathArgs'], $result['dnsParam'], $result['uriParam']);

        $result['cannonicalRequest'] = $canonicalstring;

        $signature = base64_encode(hash_hmac('sha1', $canonicalstring, $this->secretKey, true));

        $authorization = 'OBS ' . $this->accessKeyId . ':' . $signature;

        $result['headers']['Authorization'] = $authorization;

        return $result;
    }

    /**
     * Xử lý dữ liệu chữ ký
     * @param $method
     * @param $headers
     * @param $pathArgs
     * @param $bucketName
     * @param $objectKey
     * @param null $expires
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/17
     */
    public function makeCanonicalstring($method, $headers, $pathArgs, $bucketName, $objectKey, $expires = null)
    {
        $buffer = [];
        $buffer[] = $method;
        $buffer[] = "\n";
        $interestHeaders = [];

        foreach ($headers as $key => $value) {
            $key = strtolower($key);
            if (in_array($key, self::INTEREST_HEADER_KEY_LIST) || strpos($key, self::HEADER_PREFIX) === 0) {
                $interestHeaders[$key] = $value;
            }
        }

        if (array_key_exists(self::ALTERNATIVE_DATE_HEADER, $interestHeaders)) {
            $interestHeaders['date'] = '';
        }

        if ($expires !== null) {
            $interestHeaders['date'] = strval($expires);
        }

        if (!array_key_exists('content-type', $interestHeaders)) {
            $interestHeaders['content-type'] = '';
        }

        if (!array_key_exists('content-md5', $interestHeaders)) {
            $interestHeaders['content-md5'] = '';
        }

        ksort($interestHeaders);

        foreach ($interestHeaders as $key => $value) {
            if (strpos($key, self::HEADER_PREFIX) === 0) {
                $buffer[] = $key . ':' . $value;
            } else {
                $buffer[] = $value;
            }
            $buffer[] = "\n";
        }

        $uri = '';

        $bucketName = $this->isCname ? $headers['Host'] : $bucketName;

        if ($bucketName) {
            $uri .= '/';
            $uri .= $bucketName;
            if (!$this->pathStyle) {
                $uri .= '/';
            }
        }

        if ($objectKey) {
            if (!($pos = strripos($uri, '/')) || strlen($uri) - 1 !== $pos) {
                $uri .= '/';
            }
            $uri .= $objectKey;
        }

        $buffer[] = $uri === '' ? '/' : $uri;


        if (!empty($pathArgs)) {
            ksort($pathArgs);
            $_pathArgs = [];
            foreach ($pathArgs as $key => $value) {
                if (in_array(strtolower($key), self::ALLOWED_RESOURCE_PARAMTER_NAMES) || strpos($key, self::HEADER_PREFIX) === 0) {
                    $_pathArgs[] = $value === null || $value === '' ? $key : $key . '=' . urldecode($value);
                }
            }
            if (!empty($_pathArgs)) {
                $buffer[] = '?';
                $buffer[] = implode('&', $_pathArgs);
            }
        }

        return implode('', $buffer);
    }

    /**
     * Khởi tạo yêu cầu
     * @param string $url
     * @param string $method
     * @param array $data
     * @param array $clientHeader
     * @param int $timeout
     * @return false|string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/16
     */
    public function request(string $url, string $method, array $data = [], array $clientHeader = [], int $timeout = 10)
    {
        $method = strtoupper($method);
        $urlAttr = pathinfo($url);
        $urlParse = parse_url($urlAttr['dirname'] ?? '');

        $uriParam = '';
        if ($urlAttr['dirname'] !== 'https:') {
            if (isset($urlParse['path'])) {
                $uriParam .= substr($urlParse['path'], 1) . '/';
            }
            if (isset($urlAttr['basename'])) {
                $uriParam .= $urlAttr['basename'];
            }
        }

        $result = $this->getSign([
            'method' => $method,
            'headers' => $clientHeader,
            'pathArgs' => '',
            'dnsParam' => $data['bucket'] ?? '',
            'uriParam' => $uriParam,
        ]);

        return $this->requestClient($url, $method, $data, $result['headers'], $timeout);
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

}
