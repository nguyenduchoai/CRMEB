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

use crmeb\exceptions\AdminException;
use crmeb\exceptions\UploadException;
use crmeb\services\upload\extend\obs\Client as TyClient;
use crmeb\services\upload\BaseUpload;
use DateTimeInterface;
use GuzzleHttp\Psr7\Utils;

class Obs extends BaseUpload
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
     * @var TyClient
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

    public function move(string $file = 'file', bool $isStream = false, string $fileContent = null)
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
        $key = $this->getUploadPath($key);

        try {
            $uploadInfo = $this->app()->putObject($key, $body, 'application/octet-stream');
            $this->fileInfo->uploadInfo = $uploadInfo;
            $this->fileInfo->realName = isset($fileHandle) ? $fileHandle->getOriginalName() : $key;
            $this->fileInfo->filePath = ($this->cdn ?: $this->uploadUrl) . '/' . $key;
            $this->fileInfo->fileName = $key;
            $this->fileInfo->filePathWater = $this->water($this->fileInfo->filePath);
            $this->authThumb && $this->thumb($this->fileInfo->filePath);
            return $this->fileInfo;
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
    }

    public function stream($fileContent, string $key = null)
    {
        if (!$key) {
            $key = $this->saveFileName();
        }
        return $this->move($key, true, $fileContent);
    }

    public function delete(string $filePath)
    {
        try {
            return $this->app()->deleteObject($filePath);
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
    }

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
        $this->storageRegion = $config['storageRegion'] ?? null;
        $this->cdn = $config['cdn'] ?? null;
        $this->waterConfig['watermark_text_font'] = 'simfang仿宋.ttf';
    }

    /**
     * Khởi tạo (instance) cos
     * @return TyClient
     */
    protected function app()
    {
        $this->handle = new TyClient([
            'accessKey' => $this->accessKey,
            'secretKey' => $this->secretKey,
            'region' => $this->storageRegion ?: 'cn-north-1',
            'bucket' => $this->storageName,
            'uploadUrl' => $this->uploadUrl
        ]);
        return $this->handle;
    }

    public function listbuckets(string $region = null, bool $line = false, bool $shared = false)
    {
        try {
            $res = $this->app()->listBuckets();
            return $res['Buckets']['Bucket'] ?? [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function createBucket(string $name, string $region, string $acl = TyClient::DEFAULT_OBS_ACL)
    {
        $regionData = $this->getRegion();
        $regionData = array_column($regionData, 'value');
        if (!in_array($region, $regionData)) {
            return $this->setError('COS: khu vực không hợp lệ!');
        }
        $this->storageRegion = $region;
        $app = $this->app();
        //Tạo bucket
        try {
            $app->createBucket($name, $region, $acl);
            $data = [
                'Statement' => [
                    'Sid' => 'PublicRead' . $name,
                    'Effect' => 'Allow',
                    'Principal' => [
                        'ID' => ['*']
                    ],
                    'Action' => ['HeadBucket', 'GetBucketLocation', 'ListBucketVersions', 'GetObject', 'RestoreObject', 'GetObjectVersion'],
                    'Resource' => [$name, $name . '/*']
                ]
            ];

            $app->putPolicy($name, $region, $data);
        } catch (\Throwable $e) {
            return $this->setError('COS:' . $e->getMessage());
        }
        return true;
    }

    public function getRegion()
    {
        return $this->app()->getRegion();
    }

    public function deleteBucket(string $name, string $region = '')
    {
        try {
            $this->app()->deleteBucket($name, $region);
            return true;
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
    }

    public function getDomian($name, $region)
    {
        try {
            $res = $this->app()->getBucketDomain($name, $region);
            if ($res) {
                $domainRules = $res->toArray()['ListBucketCustomDomainsResult'];
                return array_column($domainRules, 'DomainName');
            } else {
                return [];
            }

        } catch (\Throwable $e) {
        }
        return [];
    }


    public function bindDomian(string $name, string $domain, string $region = null)
    {
        $parseDomin = parse_url($domain);
        try {
            $this->app()->putBucketDomain($name, $region, [
                'domainname' => $parseDomin['host'],
            ]);
            return true;
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
    }

    public function setBucketCors(string $name, string $region)
    {
        try {
            $this->app()->putBucketCors($name, $region, [
                'AllowedHeader' => ['*'],
                'AllowedMethod' => ['PUT', 'GET', 'POST', 'DELETE', 'HEAD'],
                'AllowedOrigin' => ['*'],
                'ExposeHeader' => ['ETag'],
                'MaxAgeSeconds' => 0
            ]);
            return true;
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * @return array
     * @date 2023/6/13
     */
    public function getTempKeys($callbackUrl = '', $dir = '')
    {
//        return [
//            'access_key' => $this->accessKey,
//            'secret_key' => $this->secretKey,
//            'type' => 'OBS'
//        ];
        // TODO: Implement getTempKeys() method.
        $base64CallbackBody = base64_encode(json_encode([
            'callbackUrl' => $callbackUrl,
            'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => "application/x-www-form-urlencoded"
        ]));

        $policy = json_encode([
            'expiration' => $this->gmtIso8601(time() + 300),
            'conditions' =>
                [
                    [0 => 'content-length-range', 1 => 0, 2 => 1048576000],
                    ['bucket' => $this->storageName],
                    [0 => 'starts-with', 1 => '$key', 2 => $dir],
                ]
        ]);
        $base64Policy = base64_encode($policy);
        $signature = base64_encode(hash_hmac('sha1', $base64Policy, $this->secretKey, true));
        return [
            'accessid' => $this->accessKey,
            'host' => $this->uploadUrl,
            'policy' => $base64Policy,
            'signature' => $signature,
            'expire' => time() + 30,
            'callback' => $base64CallbackBody,
            'cdn' => $this->cdn,
            'type' => 'OBS'
        ];
    }

    /**
     * Lấy định dạng thời gian ISO
     * @param $time
     * @return string
     * @throws \Exception
     */
    protected function gmtIso8601($time): string
    {
        $dtStr = date("c", $time);
        $myDateTime = new \DateTime($dtStr);
        $expiration = $myDateTime->format(DateTimeInterface::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
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
}
