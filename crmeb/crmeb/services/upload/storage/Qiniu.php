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
use Qiniu\Auth;
use Qiniu\Http\Client;
use Qiniu\Http\Error;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use Qiniu\Config;


/**
 * TODO Tải lên Qiniu Cloud
 * Class Qiniu
 */
class Qiniu extends BaseUpload
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
     * @var object
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
        '1' => 'NorthWest',//: Trên trái
        '2' => 'North',//: Trên giữa
        '3' => 'NorthEast',//: Trên phải
        '4' => 'West',//: Giữa trái
        '5' => 'Center',//: Chính giữa
        '6' => 'East',//: Giữa phải
        '7' => 'SouthWest',//: Dưới trái
        '8' => 'South',//: Dưới giữa
        '9' => 'SouthEast',//: Dưới phải
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
    public function initialize(array $config)
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
     * Khởi tạo instance Qiniu Cloud
     * @return object|Auth
     */
    protected function app()
    {
        if (!$this->accessKey || !$this->secretKey) {
            throw new UploadException('Vui lòng cấu hình accessKey và secretKey trước');
        }
        $this->handle = new Auth($this->accessKey, $this->secretKey);
        return $this->handle;
    }

    /**
     * Tải lên file
     * @param string $file
     * @param bool $realName
     * @return array|bool|mixed|\StdClass|string
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
        $token = $this->app()->uploadToken($this->storageName);
        try {
            $uploadMgr = new UploadManager();
            [$result, $error] = $uploadMgr->putFile($token, $key, $fileHandle->getRealPath());
            if ($error !== null) {
                return $this->setError($error->message());
            }
            $this->fileInfo->uploadInfo = $result;
            $this->fileInfo->realName = $fileHandle->getOriginalName();
            $this->fileInfo->filePath = $this->uploadUrl . '/' . $key;
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
     * @return array|bool|mixed|\StdClass
     */
    public function stream($fileContent, string $key = null)
    {
        if (!$key) {
            $key = $this->saveFileName();
        }
        $key = $this->getUploadPath($key);
        $token = $this->app()->uploadToken($this->storageName, $key);
        try {
            $uploadMgr = new UploadManager();
            [$result, $error] = $uploadMgr->put($token, $key, $fileContent);
            if ($error !== null) {
                return $this->setError($error->message());
            }
            $this->fileInfo->uploadInfo = $result;
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
                        $this->fileInfo->$key = $filePath . '?imageView2/2/w/' . $config[$width] . '/h/' . $config[$height];
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
                $filePath .= '|watermark';
            }
            switch ($waterConfig['watermark_type']) {
                case 1://Hình ảnh
                    if (!$waterConfig['watermark_image']) {
                        throw new AdminException('Vui lòng cấu hình ảnh watermark trước');
                    }
                    $waterPath = $filePath .= '/1/image/' . base64_encode($waterConfig['watermark_image']) . '/gravity/' . ($this->position[$waterConfig['watermark_position']] ?? 'SouthEest') . '/dissolve/' . $waterConfig['watermark_opacity'] . '/dx/' . $waterConfig['watermark_x'] . '/dy/' . $waterConfig['watermark_y'];
                    break;
                case 2://Văn bản
                    if (!$waterConfig['watermark_text']) {
                        throw new AdminException('Vui lòng cấu hình văn bản watermark trước');
                    }
                    $waterPath = $filePath .= '/2/text/' . base64_encode($waterConfig['watermark_text']) . '/fill/' . base64_encode($waterConfig['watermark_text_color']) . '/fontsize/' . $waterConfig['watermark_text_size'] . '/gravity/' . ($this->position[$waterConfig['watermark_position']] ?? 'SouthEest') . '/dx/' . $waterConfig['watermark_x'] . '/dy/' . $waterConfig['watermark_y'];
                    break;
            }
        }
        return $waterPath;
    }

    /**
     * Lấy thông tin cấu hình tải lên
     * @return array
     */
    public function getSystem()
    {
        $token = $this->app()->uploadToken($this->storageName);
        $domain = $this->uploadUrl;
        $key = $this->saveFileName();
        return compact('token', 'domain', 'key');
    }

    /**
     * TODO xóa resource
     * @param $key
     * @param $bucket
     * @return mixed
     */
    public function delete(string $key)
    {
        $bucketManager = new BucketManager($this->app(), new Config());
        return $bucketManager->delete($this->storageName, $key);
    }

    /**
     * Lấy khóa tải lên Qiniu Cloud
     * @return mixed|string
     */
    public function getTempKeys()
    {
        $token = $this->app()->uploadToken($this->storageName);
        $domain = $this->uploadUrl;
        $cdn = $this->cdn;
        $key = $this->saveFileName(NULL, 'mp4');
        $type = 'QINIU';
        return compact('token', 'domain', 'key', 'type', 'cdn');
    }

    /**
     * Lấy danh sách tất cả bucket hiện tại
     * @param string|null $region
     * @param bool $line
     * @param bool $shared
     * @return bool|mixed
     */
    public function listbuckets(string $region = null, bool $line = false, bool $shared = false)
    {
        $bucket = new BucketManager($this->app());
        [$response, $error] = $bucket->listbuckets($region, $line ? 'true' : 'false', $shared ? 'true' : 'false');
        if ($error !== null) {
            return $this->setError($error->message());
        }
        return $response;
    }

    /**
     * @param string $name
     * @param string $region
     * @return bool|mixed
     */
    public function createBucket(string $name, string $region = 'z0')
    {
        $regionData = $this->getRegion();
        if (!in_array($region, array_column($regionData, 'value'))) {
            return $this->setError('Qiniu Cloud: khu vực không hợp lệ');
        }
        $url = 'https://' . Config::UC_HOST . '/mkbucketv3/' . $name . '/region/' . $region;
        $body = null;
        $headers = $this->app()->authorizationV2($url, 'POST', $body, 'application/json');
        $headers["Content-Type"] = 'application/json';
        $ret = Client::post($url, $body, $headers);
        if (!$ret->ok()) {
            $error = new Error($url, $ret);
            if ('bucket exists' === $error->message()) {
                return $this->setError('Qiniu Cloud: bucket đã tồn tại');
            }
            return $this->setError('Qiniu Cloud:' . $error->message());
        }
        return ($ret->body === null) ? array() : $ret->json();
    }

    /**
     * Lấy khu vực
     * @return mixed|\string[][]
     */
    public function getRegion()
    {
        return [
            [
                'value' => 'z0',
                'label' => 'Đông Trung Quốc'
            ],
            [
                'value' => 'z1',
                'label' => 'Bắc Trung Quốc'
            ],
            [
                'value' => 'z2',
                'label' => 'Nam Trung Quốc'
            ],
            [
                'value' => 'na0',
                'label' => 'Bắc Mỹ'
            ],
            [
                'value' => 'as0',
                'label' => 'Đông Nam Á'
            ],
            [
                'value' => 'cn-east-2',
                'label' => 'Đông Trung Quốc - Zhejiang 2'
            ],
        ];
    }

    /**
     * Xóa không gian lưu trữ (bucket)
     * @param string $name
     * @return bool|mixed
     */
    public function deleteBucket(string $name)
    {
        $bucket = new BucketManager($this->app());
        [$response, $error] = $bucket->deleteBucket($name);
        if ($error !== null) {
            return $this->setError($error->message());
        }
        return $response;
    }

    /**
     * Lấy domain Qiniu
     * @param string $name
     * @return array|bool|mixed|null
     */
    public function getDomian(string $name)
    {
        $url = 'https://' . Config::UC_HOST . '/v2/domains?tbl=' . $name;
        $body = null;
        $headers = $this->app()->authorizationV2($url, 'POST', $body, 'application/x-www-form-urlencoded');
        $headers["Content-Type"] = 'application/x-www-form-urlencoded';
        $ret = Client::post($url, $body, $headers);
        if (!$ret->ok()) {
            $error = new Error($url, $ret);
            return $this->setError('Qiniu Cloud:' . $error->message());
        }
        return ($ret->body === null) ? array() : $ret->json();
    }

    public function getDomianInfo(string $host)
    {
        $url = 'https://' . Config::API_HOST . '/domain/' . $host;
        $headers = $this->app()->authorization($url, null, 'application/x-www-form-urlencoded');
        $headers["Content-Type"] = 'application/x-www-form-urlencoded';
        $ret = Client::get($url, $headers);
        if (!$ret->ok()) {
            $error = new Error($url, $ret);
            return $this->setError('Qiniu Cloud:' . $error->message());
        }
        return ($ret->body === null) ? array() : $ret->json();
    }

    /**
     *
     * @param string $name
     * @param string $domain
     * @param string|null $region
     * @return array|bool|mixed|null
     */
    public function bindDomian(string $name, string $domain, string $region = null)
    {
        $parseDomin = parse_url($domain);
        $url = 'https://' . Config::API_HOST . '/domain/' . $parseDomin['host'];
        $body = [
            'type' => 'normal',
            'platform' => 'web',
            'geocover' => 'china',
            'source' => [
                'sourceType' => 'qiniuBucket',
                'sourceQiniuBucket' => $name,
                'TestURLPath' => 'qiniu_do_not_delete.gif',
            ],
            'protocol' => $parseDomin['scheme'],
            'cache' => [
                'cacheControls' => [
                    [
                        'time' => 1,
                        'timeunit' => 4,
                        'type' => 'all',
                        'rule' => '*'
                    ]
                ],
                'ignoreParam' => false
            ]
        ];
        $bodyJson = json_encode($body);
        $headers = $this->app()->authorization($url, $bodyJson, 'application/json');
        $headers["Content-Type"] = 'application/json';
        $ret = Client::post($url, $bodyJson, $headers);
        if (!$ret->ok()) {
            $error = new Error($url, $ret);
            return $this->setError('Qiniu Cloud:' . $error->message());
        }
        return ($ret->body === null) ? array() : $ret->json();
    }

    /**
     * Cross-domain
     * @param string $name
     * @param string $region
     * @return bool
     */
    public function setBucketCors(string $name, string $region)
    {
        return true;
    }
}
