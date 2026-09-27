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
use Guzzle\Http\EntityBody;
use OSS\Core\OssException;
use OSS\Model\CorsConfig;
use OSS\Model\CorsRule;
use OSS\OssClient;


/**
 * Tải lên OSS Alibaba Cloud
 * Class OSS
 */
class Oss extends BaseUpload
{
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
     * @var OssClient
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
     * Vị trí hình mờ
     * @var string[]
     */
    protected $position = [
        '1' => 'nw',//: Trên trái
        '2' => 'north',//: Trên giữa
        '3' => 'ne',//: Trên phải
        '4' => 'west',//: Giữa trái
        '5' => 'center',//: Chính giữa
        '6' => 'east',//: Giữa phải
        '7' => 'sw',//: Dưới trái
        '8' => 'south',//: Dưới giữa
        '9' => 'se',//: Dưới phải
    ];

    /**
     * @var string
     */
    protected $cdn;

    /**
     * Khởi tạo
     * @param array $config
     * @return mixed|void
     */
    protected function initialize(array $config)
    {
        parent::initialize($config);
        $this->accessKey = $config['accessKey'] ?? null;
        $this->secretKey = $config['secretKey'] ?? null;
        $this->uploadUrl = $this->checkUploadUrl($config['uploadUrl'] ?? '');
        $this->storageName = $config['storageName'] ?? null;
        $this->cdn = $config['cdn'] ?? null;
        $this->storageRegion = $config['storageRegion'] ?? null;
    }

    /**
     * Khởi tạo oss
     * @return OssClient
     * @throws OssException
     */
    protected function app()
    {
        if (!$this->accessKey || !$this->secretKey) {
            throw new UploadException('Vui lòng cấu hình accessKey và secretKey trước');
        }
        $this->handle = new OssClient($this->accessKey, $this->secretKey, $this->storageRegion);
        //Không tự động tạo nữa
//        if (!$this->handle->doesBucketExist($this->storageName)) {
//            $this->handle->createBucket($this->storageName, OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE);
//        }
        return $this->handle;
    }

    /**
     * Tải lên file
     * @param string $file
     * @param bool $realName
     * @return array|bool|mixed|\StdClass
     */
    public function move(string $file = 'file', $realName = false)
    {
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
        $key = $this->getUploadPath($key);
        try {
            $uploadInfo = $this->app()->uploadFile($this->storageName, $key, $fileHandle->getRealPath());
            if (!isset($uploadInfo['info']['url'])) {
                return $this->setError('Upload failure');
            }
            $this->fileInfo->uploadInfo = $uploadInfo;
            $this->fileInfo->realName = $fileHandle->getOriginalName();
            $this->fileInfo->filePath = ($this->cdn ?: $this->uploadUrl) . '/' . $key;
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
     * @param $fileContent
     * @param string|null $key
     * @return array|bool|mixed
     * @throws OssException
     */
    public function stream($fileContent, string $key = null)
    {
        try {
            if (!$key) {
                $key = $this->saveFileName();
            }
            $key = $this->getUploadPath($key);
            $fileContent = (string)EntityBody::factory($fileContent);
            $uploadInfo = $this->app()->putObject($this->storageName, $key, $fileContent);
            if (!isset($uploadInfo['info']['url'])) {
                return $this->setError('Upload failure');
            }
            $this->fileInfo->uploadInfo = $uploadInfo;
            $this->fileInfo->realName = $key;
            $this->fileInfo->filePath = ($this->cdn ?: $this->uploadUrl) . '/' . $key;
            $this->fileInfo->fileName = $key;
            $this->fileInfo->filePathWater = $this->water($this->fileInfo->filePath);
            $this->authThumb && $this->thumb($this->fileInfo->filePath);
            return $this->fileInfo;
        } catch (UploadException $e) {
            return $this->setError($e->getMessage());
        }
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
                        $this->fileInfo->$key = $filePath . '?x-oss-process=image/resize,h_' . $config[$height] . ',w_' . $config[$width];
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
            if (strpos($filePath, '?x-oss-process') === false) {
                $filePath .= '?x-oss-process=image';
            }
            switch ($waterConfig['watermark_type']) {
                case 1://Hình ảnh
                    if (!$waterConfig['watermark_image']) {
                        throw new AdminException('Vui lòng cấu hình ảnh watermark trước');
                    }
                    $waterPath = $filePath .= '/watermark,image_' . base64_encode($waterConfig['watermark_image']) . ',t_' . $waterConfig['watermark_opacity'] . ',g_' . ($this->position[$waterConfig['watermark_position']] ?? 'nw') . ',x_' . $waterConfig['watermark_x'] . ',y_' . $waterConfig['watermark_y'];
                    break;
                case 2://Văn bản
                    if (!$waterConfig['watermark_text']) {
                        throw new AdminException('Vui lòng cấu hình văn bản watermark trước');
                    }
                    $waterConfig['watermark_text_color'] = str_replace('#', '', $waterConfig['watermark_text_color']);
                    $waterPath = $filePath .= '/watermark,text_' . base64_encode($waterConfig['watermark_text']) . ',color_' . $waterConfig['watermark_text_color'] . ',size_' . $waterConfig['watermark_text_size'] . ',g_' . ($this->position[$waterConfig['watermark_position']] ?? 'nw') . ',x_' . $waterConfig['watermark_x'] . ',y_' . $waterConfig['watermark_y'];
                    break;
            }
        }
        return $waterPath;
    }

    /**
     * Xóa tài nguyên
     * @param $key
     * @return mixed
     */
    public function delete(string $key)
    {
        try {
            return $this->app()->deleteObject($this->storageName, $key);
        } catch (OssException $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * Lấy khóa tải lên OSS
     * @return mixed|void
     */
    public function getTempKeys($callbackUrl = '', $dir = '')
    {
        // TODO: Implement getTempKeys() method.
        $base64CallbackBody = base64_encode(json_encode([
            'callbackUrl' => $callbackUrl,
            'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => "application/x-www-form-urlencoded"
        ]));

        $policy = json_encode([
            'expiration' => $this->gmtIso8601(time() + 30),
            'conditions' =>
                [
                    [0 => 'content-length-range', 1 => 0, 2 => 1048576000],
                    [0 => 'starts-with', 1 => '$key', 2 => $dir]
                ]
        ]);
        $base64Policy = base64_encode($policy);
        $signature = base64_encode(hash_hmac('sha1', $base64Policy, $this->secretKey, true));
        return [
            'accessid' => $this->accessKey,
            'host' => $this->uploadUrl,
            'cdn' => $this->cdn,
            'policy' => $base64Policy,
            'signature' => $signature,
            'expire' => time() + 30,
            'callback' => $base64CallbackBody,
            'type' => 'OSS'
        ];
    }

    /**
     * Lấy định dạng thời gian ISO
     * @param $time
     * @return string
     */
    protected function gmtIso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }

    /**
     * Lấy tất cả bucket đang quản lý
     * @param string|null $region
     * @param bool $line
     * @param bool $shared
     * @return mixed|\OSS\Model\BucketListInfo
     * @throws OssException
     */
    public function listbuckets(string $region = 'oss-cn-hangzhou.aliyuncs.com', bool $line = false, bool $shared = false)
    {
        $handle = new OssClient($this->accessKey, $this->secretKey, $region);
        $response = $handle->listBuckets();
        $data = $response->getBucketList();
        $list = [];
        foreach ($data as $item) {
            $list[] = [
                'location' => $item->getLocation(),
                'name' => $item->getName(),
                'createTime' => $item->getCreateDate()
            ];
        }
        return $list;
    }

    /**
     * @param string $name
     * @param string $region
     * @param string $acl
     * @return mixed|void
     */
    public function createBucket(string $name, string $region = '', string $acl = OssClient::OSS_ACL_TYPE_PUBLIC_READ)
    {
        $regionData = $this->getRegion();
        if (!in_array($region, array_column($regionData, 'value'))) {
            return $this->setError('OSS: khu vực không hợp lệ');
        }
        try {
            $handle = new OssClient($this->accessKey, $this->secretKey, $region);
            if ($handle->doesBucketExist($name)) {
                return $this->setError('OSS: bucket đã tồn tại, vui lòng đổi tên bucket');
            }
            return $handle->createBucket($name, $acl);
        } catch (\Throwable $e) {
            if (strstr('The bucket you access does not belong to you', $e->getMessage())) {
                return $this->setError('OSS: bucket đã được sử dụng, vui lòng đổi tên bucket');
            }
            return $this->setError('OSS:' . $e->getMessage());
        }
    }

    /**
     * @param string $name
     * @param string $region
     * @return bool|mixed|null
     */
    public function deleteBucket(string $name, string $region = '')
    {
        try {
            $handle = new OssClient($this->accessKey, $this->secretKey, $region);
            return $handle->deleteBucket($name);
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * @param string $name
     * @param string|null $region
     * @return array|bool
     */
    public function getDomian(string $name, string $region = null)
    {
        try {
            $handle = new OssClient($this->accessKey, $this->secretKey, $region);
            $res = $handle->getBucketCname($name);
            $data = $res->getCnames();
            return array_column($data, 'Domain');
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
        $parseDomin = parse_url($domain);
        try {
            $handle = new OssClient($this->accessKey, $this->secretKey, $region);
            $res = $handle->getBucketCname($name);
            $data = $res->getCnames();
            if (in_array($parseDomin['host'], array_column($data, 'Domain'))) {
                return true;
            }
            return $handle->addBucketCname($name, $parseDomin['host']);
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * Đặt CORS
     * @param string $name
     * @param string $region
     * @return bool|null
     */
    public function setBucketCors(string $name, string $region)
    {
        try {
            $handle = new OssClient($this->accessKey, $this->secretKey, $region);
            $corsConfig = new CorsConfig();
            $rule = new CorsRule();
            // Thiết lập header phản hồi cho phép request cross-domain. Có thể thiết lập nhiều AllowedHeader, mỗi AllowedHeader chỉ được dùng tối đa một ký tự đại diện dấu hoa thị (*).
            // Khuyến nghị khi không có yêu cầu đặc biệt thì đặt AllowedHeader là dấu hoa thị (*).
            $rule->addAllowedHeader("*");
            // Thiết lập header phản hồi cho phép người dùng truy cập từ ứng dụng. Có thể thiết lập nhiều ExposeHeader, ExposeHeader không hỗ trợ dùng ký tự đại diện dấu hoa thị (*).
            $rule->addExposeHeader("ETag");
            // Thiết lập nguồn (origin) được phép cho request cross-domain. Có thể thiết lập nhiều AllowedOrigin, mỗi AllowedOrigin chỉ được dùng tối đa một ký tự đại diện dấu hoa thị (*).
            // Khi đặt AllowedOrigin là dấu hoa thị (*) thì nghĩa là cho phép nguồn từ tất cả các domain.
            $rule->addAllowedOrigin("*");
            // Thiết lập phương thức request cross-domain được phép.
            $rule->addAllowedMethod("POST");
            $rule->addAllowedMethod("GET");
            $rule->addAllowedMethod("DELETE");
            $rule->addAllowedMethod("PUT");
            $rule->addAllowedMethod("HEAD");
            // Thiết lập thời gian cache cho kết quả trả về của request prefetch (OPTIONS) của trình duyệt đối với tài nguyên cụ thể, đơn vị là giây.
            $rule->setMaxAgeSeconds(600);
            // Mỗi Bucket hỗ trợ thêm tối đa 10 quy tắc.
            $corsConfig->addRule($rule);
            return $handle->putBucketCors($name, $corsConfig);
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * Trung tâm dữ liệu
     * @return mixed|\string[][]
     */
    public function getRegion()
    {
        return [
            [
                'value' => 'oss-cn-hangzhou.aliyuncs.com',
                'label' => 'Đông Trung Quốc 1 (Hangzhou)'
            ],
            [
                'value' => 'oss-cn-shanghai.aliyuncs.com',
                'label' => 'Đông Trung Quốc 2 (Shanghai)'
            ],
            [
                'value' => 'oss-cn-qingdao.aliyuncs.com',
                'label' => 'Bắc Trung Quốc 1 (Qingdao)'
            ],
            [
                'value' => 'oss-cn-beijing.aliyuncs.com',
                'label' => 'Bắc Trung Quốc 2 (Beijing)'
            ],
            [
                'value' => 'oss-cn-zhangjiakou.aliyuncs.com',
                'label' => 'Bắc Trung Quốc 3 (Zhangjiakou)'
            ],
            [
                'value' => 'oss-cn-huhehaote.aliyuncs.com',
                'label' => 'Bắc Trung Quốc 5 (Hohhot)'
            ],
            [
                'value' => 'oss-cn-wulanchabu.aliyuncs.com',
                'label' => 'Bắc Trung Quốc 6 (Ulanqab)'
            ],
            [
                'value' => 'oss-cn-shenzhen.aliyuncs.com',
                'label' => 'Nam Trung Quốc 1 (Shenzhen)'
            ],
            [
                'value' => 'oss-cn-heyuan.aliyuncs.com',
                'label' => 'Nam Trung Quốc 2 (Heyuan)'
            ],
            [
                'value' => 'oss-cn-guangzhou.aliyuncs.com',
                'label' => 'Nam Trung Quốc 3 (Guangzhou)'
            ],
            [
                'value' => 'oss-cn-chengdu.aliyuncs.com',
                'label' => 'Tây Nam Trung Quốc 1 (Chengdu)'
            ],
            [
                'value' => 'oss-cn-hongkong.aliyuncs.com',
                'label' => 'Trung Quốc (Hồng Kông)'
            ],
            [
                'value' => 'oss-us-west-1.aliyuncs.com',
                'label' => 'Mỹ (Silicon Valley)*'
            ],
            [
                'value' => 'oss-us-east-1.aliyuncs.com',
                'label' => 'Mỹ (Virginia)*'
            ],
            [
                'value' => 'oss-ap-southeast-1.aliyuncs.com',
                'label' => 'Singapore*'
            ],
            [
                'value' => 'oss-ap-southeast-2.aliyuncs.com',
                'label' => 'Úc (Sydney)*'
            ],
            [
                'value' => 'oss-ap-southeast-3.aliyuncs.com',
                'label' => 'Malaysia (Kuala Lumpur)*'
            ],
            [
                'value' => 'oss-ap-southeast-5.aliyuncs.com',
                'label' => 'Indonesia (Jakarta)*'
            ],
            [
                'value' => 'oss-ap-northeast-1.aliyuncs.com',
                'label' => 'Nhật Bản (Tokyo)*'
            ],
            [
                'value' => 'oss-ap-south-1.aliyuncs.com',
                'label' => 'Ấn Độ (Mumbai)*'
            ],
            [
                'value' => 'oss-eu-central-1.aliyuncs.com',
                'label' => 'Đức (Frankfurt)*'
            ],
            [
                'value' => 'oss-eu-west-1.aliyuncs.com',
                'label' => 'Anh (London)'
            ],
            [
                'value' => 'oss-me-east-1.aliyuncs.com',
                'label' => 'UAE (Dubai)*'
            ],
            [
                'value' => 'oss-ap-southeast-6.aliyuncs.com',
                'label' => 'Philippines (Manila)'
            ]
        ];
    }
}
