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
namespace crmeb\services\upload\storage;

use crmeb\services\upload\BaseUpload;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\UploadException;
use GuzzleHttp\Psr7\Utils;
use Qcloud\Cos\Client;
use QCloud\COSSTS\Sts;
use crmeb\services\upload\extend\cos\Client as CrmebClient;

/**
 * Tải lên file Tencent Cloud COS
 * Class COS
 * @package crmeb\services\upload\storage
 */
class Cos extends BaseUpload
{

    /**
     * id ứng dụng
     * @var string
     */
    protected $appid;

    /**
     * accessKey
     * @var mixed
     */
    protected $accessKey;

    /**
     * secretKey
     * @var mixed
     */
    protected $secretKey;

    /**
     * Handle
     * @var CrmebClient
     */
    protected $handle;

    /**
     * Domain của space Domain
     * @var mixed
     */
    protected $uploadUrl;

    /**
     * Tên space lưu trữ  space công khai
     * @var mixed
     */
    protected $storageName;

    /**
     * COS sử dụng  region trực thuộc
     * @var mixed|null
     */
    protected $storageRegion;

    /**
     * @var string
     */
    protected $cdn;

    /**
     * Vị trí hình mờ
     * @var string[]
     */
    protected $position = [
        '1' => 'northwest',//: Trên trái
        '2' => 'north',//: Trên giữa
        '3' => 'northeast',//: Trên phải
        '4' => 'west',//: Giữa trái
        '5' => 'center',//: Chính giữa
        '6' => 'east',//: Giữa phải
        '7' => 'southwest',//: Dưới trái
        '8' => 'south',//: Dưới giữa
        '9' => 'southeast',//: Dưới phải
    ];

    /**
     * Khởi tạo
     * @param array $config
     * @return mixed|void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->accessKey = $config['accessKey'] ?? null;
        $this->appid = $config['appid'] ?? null;
        $this->secretKey = $config['secretKey'] ?? null;
        $this->uploadUrl = $this->checkUploadUrl($config['uploadUrl'] ?? '');
        $this->storageName = $config['storageName'] ?? null;
        $this->storageRegion = $config['storageRegion'] ?? null;
        $this->cdn = $config['cdn'] ?? null;
        $this->waterConfig['watermark_text_font'] = 'simfang仿宋.ttf';
    }

    /**
     * Khởi tạo (instance) cos
     * @return CrmebClient
     */
    protected function app()
    {
        $this->handle = new CrmebClient([
            'accessKey' => $this->accessKey,
            'secretKey' => $this->secretKey,
            'region' => $this->storageRegion ?: 'ap-chengdu',
            'bucket' => $this->storageName,
            'appid' => $this->appid,
            'uploadUrl' => $this->uploadUrl
        ]);
        return $this->handle;
    }

    /**
     * Tải lên file
     * @param string|null $file
     * @param bool $isStream Có phải tải lên bằng stream không
     * @param string|null $fileContent Nội dung stream
     * @return array|bool|\StdClass
     */
    protected function upload(string $file = null, bool $isStream = false, string $fileContent = null)
    {
        if (!$isStream) {
            $fileHandle = app()->request->file($file);
            if (!$fileHandle) {
                return $this->setError('Tệp tải lên không tồn tại');
            }
            if ($this->validate) {
                if (!in_array(strtolower(pathinfo($fileHandle->getOriginalName(), PATHINFO_EXTENSION)), $this->validate['fileExt'])) {
                    return $this->setError('Phần mở rộng tệp không hợp lệ');
                }
                if (filesize($fileHandle) > $this->validate['filesize']) {
                    return $this->setError('Tệp quá lớn');
                }
                if (!in_array($fileHandle->getOriginalMime(), $this->validate['fileMime'])) {
                    return $this->setError('Loại tệp không hợp lệ');
                }
            }
            $key = $this->saveFileName($fileHandle->getRealPath(), $fileHandle->getOriginalExtension());
            $body = fopen($fileHandle->getRealPath(), 'rb');
            $body = (string)Utils::streamFor($body);
        } else {
            $key = $file;
            $body = $fileContent;
        }
        try {
            $key = $this->getUploadPath($key);
            $this->fileInfo->uploadInfo = $this->app()->putObject($key, $body);
            $this->fileInfo->filePath = ($this->cdn ?: $this->uploadUrl) . '/' . $key;
            $this->fileInfo->realName = isset($fileHandle) ? $fileHandle->getOriginalName() : $key;
            $this->fileInfo->fileName = $key;
            $this->fileInfo->filePathWater = $this->water($this->fileInfo->filePath);
            $this->authThumb && $this->thumb($this->fileInfo->filePath);
            return $this->fileInfo;
        } catch (UploadException $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * Tải lên bằng file stream
     * @param  $fileContent
     * @param string|null $key
     * @return array|bool|mixed|\StdClass
     */
    public function stream($fileContent, string $key = null)
    {
        if (!$key) {
            $key = $this->saveFileName();
        }
        return $this->upload($key, true, $fileContent);
    }

    /**
     * Tải tệp lên
     * @param string $file
     * @param bool $realName
     * @return array|bool|mixed|\StdClass
     */
    public function move(string $file = 'file', $realName = false)
    {
        return $this->upload($file);
    }

    /**
     * Ảnh thu nhỏ
     * @param string $filePath
     * @param string $fileName
     * @param string $type
     * @return array|mixed
     */
    public function thumb(string $filePath = '', string $fileName = '', string $type = 'all')
    {
        $filePath = $this->getFilePath($filePath);
        $data = ['big' => $filePath, 'mid' => $filePath, 'small' => $filePath];
        $this->fileInfo->filePathBig = $this->fileInfo->filePathMid = $this->fileInfo->filePathSmall = $this->fileInfo->filePathWater = $filePath;
        if ($filePath) {
            $config = $this->thumbConfig;
            foreach ($this->thumb as $v) {
                if ($type == 'all' || $type == $v) {
                    $height = 'thumb_' . $v . '_height';
                    $width = 'thumb_' . $v . '_width';
                    $key = 'filePath' . ucfirst($v);
                    if (sys_config('image_thumbnail_status', 1) && isset($config[$height]) && isset($config[$width]) && $config[$height] && $config[$width]) {
                        $this->fileInfo->$key = $filePath . '?imageMogr2/thumbnail/' . $config[$width] . 'x' . $config[$height];
                        $this->fileInfo->$key = $this->water($this->fileInfo->$key);
                        $data[$v] = $this->fileInfo->$key;
                    } else {
                        $this->fileInfo->$key = $this->water($this->fileInfo->$key);
                        $data[$v] = $this->fileInfo->$key;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Watermark
     * @param string $filePath
     * @return mixed|string
     */
    public function water(string $filePath = '')
    {
        $filePath = $this->getFilePath($filePath);
        $waterConfig = $this->waterConfig;
        $waterPath = $filePath;
        if ($waterConfig['image_watermark_status'] && $filePath) {
            if (strpos($filePath, '?') === false) {
                $filePath .= '?watermark';
            } else {
                $filePath .= '&watermark';
            }
            switch ($waterConfig['watermark_type']) {
                case 1://Hình ảnh
                    if (!$waterConfig['watermark_image']) {
                        throw new AdminException('Vui lòng cấu hình ảnh watermark trước');
                    }
                    $waterPath = $filePath .= '/1/image/' . base64_encode($waterConfig['watermark_image']) . '/gravity/' . ($this->position[$waterConfig['watermark_position']] ?? 'northwest') . '/blogo/1/dx/' . $waterConfig['watermark_x'] . '/dy/' . $waterConfig['watermark_y'];
                    break;
                case 2://Văn bản
                    if (!$waterConfig['watermark_text']) {
                        throw new AdminException('Vui lòng cấu hình văn bản watermark trước');
                    }
                    $waterPath = $filePath .= '/2/text/' . base64_encode($waterConfig['watermark_text']) . '/font/' . base64_encode($waterConfig['watermark_text_font']) . '/fill/' . base64_encode($waterConfig['watermark_text_color']) . '/fontsize/' . $waterConfig['watermark_text_size'] . '/gravity/' . ($this->position[$waterConfig['watermark_position']] ?? 'northwest') . '/dx/' . $waterConfig['watermark_x'] . '/dy/' . $waterConfig['watermark_y'];
                    break;
            }
        }
        return $waterPath;
    }

    /**
     * TODO xóa resource
     * @param $key
     * @return mixed
     */
    public function delete(string $filePath)
    {
        try {
            return $this->app()->deleteObject($this->storageName, $filePath);
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * Tạo chữ ký
     * @return array|mixed
     * @throws \Exception
     */
    public function getTempKeys()
    {
        $sts = new Sts();
        $config = [
            'url' => 'https://sts.tencentcloudapi.com/',
            'domain' => 'sts.tencentcloudapi.com',
            'proxy' => '',
            'secretId' => $this->accessKey, // Khóa cố định
            'secretKey' => $this->secretKey, // Khóa cố định
            'bucket' => $this->storageName, // Đổi thành bucket của bạn
            'region' => $this->storageRegion, // Đổi thành khu vực chứa bucket
            'durationSeconds' => 1800, // Thời hạn hiệu lực của khóa
            'allowPrefix' => '*', // Ở đây đổi thành tiền tố đường dẫn được phép, có thể dựa vào trạng thái đăng nhập của người dùng trên website để xác định đường dẫn cụ thể được phép tải lên, ví dụ: a.jpg hoặc a/* hoặc * (dùng ký tự đại diện * có rủi ro an toàn nghiêm trọng, vui lòng đánh giá kỹ trước khi dùng)
            // Danh sách quyền của khóa. Tải lên đơn giản và tải lên theo phần cần các quyền sau, các quyền khác xem tại https://cloud.tencent.com/document/product/436/31923
            'allowActions' => [
                // Tải lên đơn giản
                'name/cos:PutObject',
                'name/cos:PostObject',
                // Tải lên theo phần (multipart)
                'name/cos:InitiateMultipartUpload',
                'name/cos:ListMultipartUploads',
                'name/cos:ListParts',
                'name/cos:UploadPart',
                'name/cos:CompleteMultipartUpload'
            ]
        ];
        // Lấy khóa tạm thời, tính chữ ký
        $result = $sts->getTempKeys($config);
        $result['url'] = $this->uploadUrl . '/';
        $result['cdn'] = $this->cdn;
        $result['type'] = 'COS';
        $result['bucket'] = $this->storageName;
        $result['region'] = $this->storageRegion;
        return $result;
    }

    /**
     * Chữ ký dùng để tính khóa tạm thời
     * @param $opt
     * @param $key
     * @param $method
     * @param $config
     * @return string
     */
    public function getSignature($opt, $key, $method, $config)
    {
        $formatString = $method . $config['domain'] . '/?' . $this->json2str($opt, 1);
        $sign = hash_hmac('sha1', $formatString, $key);
        $sign = base64_encode($this->_hex2bin($sign));
        return $sign;
    }

    public function _hex2bin($data)
    {
        $len = strlen($data);
        return pack("H" . $len, $data);
    }

    // Chuyển obj thành query string
    public function json2str($obj, $notEncode = false)
    {
        ksort($obj);
        $arr = array();
        if (!is_array($obj)) {
            return $this->setError($obj . " must be a array");
        }
        foreach ($obj as $key => $val) {
            array_push($arr, $key . '=' . ($notEncode ? $val : rawurlencode($val)));
        }
        return join('&', $arr);
    }

    // Chữ đầu của key trong API v2 viết thường, v3 đổi thành viết hoa, ở đây đã làm tương thích ngược
    public function backwardCompat($result)
    {
        if (!is_array($result)) {
            return $this->setError($result . " must be a array");
        }
        $compat = array();
        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $compat[lcfirst($key)] = $this->backwardCompat($value);
            } elseif ($key == 'Token') {
                $compat['sessionToken'] = $value;
            } else {
                $compat[lcfirst($key)] = $value;
            }
        }
        return $compat;
    }

    /**
     * Danh sách bucket
     * @param string|null $region
     * @param bool $line
     * @param bool $shared
     * @return array|mixed
     *  "Name" => "record-1254950941"
     * "Location" => "ap-chengdu"
     * "CreationDate" => "2019-05-16T08:33:29Z"
     * "BucketType" => "cos"
     */
    public function listbuckets(string $region = null, bool $line = false, bool $shared = false)
    {
        try {
            $res = $this->app()->listBuckets();
            return $res['Buckets']['Bucket'] ?? [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Tạo bucket
     * @param string $name
     * @param string $region
     * @param string $acl public-read = đọc chung, ghi riêng
     * @return bool|mixed
     */
    public function createBucket(string $name, string $region = '', string $acl = 'public-read')
    {
        $regionData = $this->getRegion();
        $regionData = array_column($regionData, 'value');
        if (!in_array($region, $regionData)) {
            return $this->setError('COS: khu vực không hợp lệ!');
        }
        $this->storageRegion = $region;
        $app = $this->app();
        //Kiểm tra bucket
        try {
            $app->headBucket($name);
        } catch (\Throwable $e) {
            //Bucket không tồn tại trả về 404
            if (strstr('404', $e->getMessage())) {
                return $this->setError('COS:' . $e->getMessage());
            }
        }
        //Tạo bucket
        try {
            $res = $app->createBucket($name . '-' . $this->appid, '', $acl);
        } catch (\Throwable $e) {
            if (strstr('[curl] 6', $e->getMessage())) {
                return $this->setError('COS: khu vực không hợp lệ!!');
            } else if (strstr('Access Denied.', $e->getMessage())) {
                return $this->setError('COS: không có quyền truy cập');
            }
            return $this->setError('COS:' . $e->getMessage());
        }
        return $res;
    }

    /**
     * Xóa bucket
     * @param string $name
     * @return bool|mixed
     */
    public function deleteBucket(string $name)
    {
        try {
            $this->app()->deleteBucket($name);
            return true;
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * @param string $name
     * @param string|null $region
     * @return array|object
     */
    public function getDomian(string $name, string $region = null)
    {
        $this->storageRegion = $region;
        try {
            $res = $this->app()->getBucketDomain($name);
            $domainRules = $res['DomainRules'];
            return array_column($domainRules, 'Name');
        } catch (\Throwable $e) {
        }
        return [];
    }

    /**
     * Gắn domain
     * @param string $name
     * @param string $domain
     * @param string|null $region
     * @return bool|mixed
     */
    public function bindDomian(string $name, string $domain, string $region = null)
    {
        $this->storageRegion = $region;
        $parseDomin = parse_url($domain);
        try {
            $res = $this->app()->putBucketDomain($name, '', [
                'Name' => $parseDomin['host'],
                'Status' => 'ENABLED',
                'Type' => 'REST',
                'ForcedReplacement' => 'CNAME'
            ]);
            if (method_exists($res, 'toArray')) {
                $res = $res->toArray();
            }
            if ($res['RequestId'] ?? null) {
                return true;
            }
        } catch (\Throwable $e) {
            if ($message = $this->setMessage($e->getMessage())) {
                return $this->setError($message);
            }
            return $this->setError($e->getMessage());
        }
        return false;
    }

    /**
     * Xử lý
     * @param string $message
     * @return string
     */
    protected function setMessage(string $message)
    {
        $data = [
            'The specified bucket does not exist.' => 'Bucket được chỉ định không tồn tại.',
            'Please add CNAME/TXT record to DNS then try again later. Please allow up to 10 mins before your DNS takes effect.' => 'Vui lòng thêm bản ghi CNAME vào DNS, sau đó thử lại. Trước khi DNS có hiệu lực, vui lòng chờ tối đa 10 phút.'
        ];
        $msg = $data[$message] ?? '';
        if ($msg) {
            return $msg;
        }
        foreach ($data as $item) {
            if (strstr($message, $item)) {
                return $item;
            }
        }
        return '';
    }

    /**
     * Đặt CORS
     * @param string $name
     * @param string $region
     * @return bool
     */
    public function setBucketCors(string $name, string $region)
    {
        $this->storageRegion = $region;
        try {
            $res = $this->app()->putBucketCors($name, [
                'AllowedHeader' => ['*'],
                'AllowedMethod' => ['PUT', 'GET', 'POST', 'DELETE', 'HEAD'],
                'AllowedOrigin' => ['*'],
                'ExposeHeader' => ['ETag', 'Content-Length', 'x-cos-request-id'],
                'MaxAgeSeconds' => 12
            ]);
            if (isset($res['RequestId'])) {
                return true;
            }
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
        return false;
    }

    /**
     * Khu vực
     * @return mixed|\string[][]
     */
    public function getRegion()
    {
        return [
            [
                'value' => 'ap-chengdu',
                'label' => 'Chengdu'
            ],
            [
                'value' => 'ap-shanghai',
                'label' => 'Shanghai'
            ],
            [
                'value' => 'ap-guangzhou',
                'label' => 'Guangzhou'
            ],
            [
                'value' => 'ap-nanjing',
                'label' => 'Nanjing'
            ],
            [
                'value' => 'ap-beijing',
                'label' => 'Beijing'
            ],
            [
                'value' => 'ap-chongqing',
                'label' => 'Chongqing'
            ],
            [
                'value' => 'ap-shenzhen-fsi',
                'label' => 'Shenzhen (Tài chính)'
            ],
            [
                'value' => 'ap-shanghai-fsi',
                'label' => 'Shanghai (Tài chính)'
            ],
            [
                'value' => 'ap-beijing-fsi',
                'label' => 'Beijing (Tài chính)'
            ],
            [
                'value' => 'ap-hongkong',
                'label' => 'Hồng Kông (Trung Quốc)'
            ],
            [
                'value' => 'ap-singapore',
                'label' => 'Singapore'
            ],
            [
                'value' => 'ap-mumbai',
                'label' => 'Mumbai'
            ],
            [
                'value' => 'ap-jakarta',
                'label' => 'Jakarta'
            ],
            [
                'value' => 'ap-seoul',
                'label' => 'Seoul'
            ],
            [
                'value' => 'ap-bangkok',
                'label' => 'Bangkok'
            ],
            [
                'value' => 'ap-tokyo',
                'label' => 'Tokyo'
            ],
            [
                'value' => 'na-siliconvalley',
                'label' => 'Silicon Valley (Miền Tây nước Mỹ)'
            ],
            [
                'value' => 'na-ashburn',
                'label' => 'Virginia (Miền Đông nước Mỹ)'
            ],
            [
                'value' => 'na-toronto',
                'label' => 'Toronto'
            ],
            [
                'value' => 'sa-saopaulo',
                'label' => 'São Paulo'
            ],
            [
                'value' => 'eu-frankfurt',
                'label' => 'Frankfurt'
            ],
            [
                'value' => 'eu-moscow',
                'label' => 'Moscow'
            ]
        ];
    }
}
