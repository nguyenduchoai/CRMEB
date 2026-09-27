<?php

namespace crmeb\services\upload\storage;

use Aws\Acm\Exception\AcmException;
use Aws\S3\S3Client;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\UploadException;
use crmeb\services\upload\BaseUpload;
use Guzzle\Http\EntityBody;

/**
 * Tải lên file COS của JD Cloud
 * Class Jdoss
 * @package crmeb\services\upload\storage
 */
class Jdoss extends BaseUpload
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
     * @var S3Client
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
        $this->secretKey = $config['secretKey'] ?? null;
        $this->uploadUrl = $this->checkUploadUrl($config['uploadUrl'] ?? '');
        $this->storageName = $config['storageName'] ?? null;
        $this->storageRegion = $config['storageRegion'] ?? null;
        $this->cdn = $config['cdn'] ?? null;
        $this->waterConfig['watermark_text_font'] = 'simfang仿宋.ttf';
    }

    /**
     * @return S3Client
     *
     * @date 2023/06/05
     * @author yyw
     */
    protected function app()
    {
        if (!$this->accessKey || !$this->secretKey) {
            throw new UploadException(400721);
        }
        $this->handle = new S3Client([
            'version' => 'latest',
            'region' => $this->storageRegion,
            'endpoint' => "http://s3.{$this->storageRegion}.jdcloud-oss.com",
            'signature_version' => 'v4',
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $this->accessKey,
                'secret' => $this->secretKey,
            ],
        ]);
        return $this->handle;
    }

    public function move(string $file = 'file')
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
            $uploadInfo = $this->app()->putObject([
                'Bucket' => $this->storageName,
                'Key' => $key,
                'SourceFile' => $fileHandle->getRealPath()
            ]);
            if (!isset($uploadInfo['ObjectURL'])) {
                return $this->setError('Upload failure');
            }
            $this->fileInfo->uploadInfo = $uploadInfo;
            $this->fileInfo->realName = $fileHandle->getOriginalName();
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
        try {
            if (!$key) {
                $key = $this->saveFileName();
            }
            $key = $this->getUploadPath($key);
            $fileContent = (string)EntityBody::factory($fileContent);
            $uploadInfo = $this->app()->putObject([
                'Bucket' => $this->storageName,
                'Key' => $key,
                'Body' => $fileContent
            ]);
            $uploadInfo = $uploadInfo->toArray();
            if (isset($uploadInfo['@metadata']['statusCode']) && $uploadInfo['@metadata']['statusCode'] !== 200) {
                return $this->setError('Upload failure');
            }
            $this->fileInfo->uploadInfo = $uploadInfo;
            $this->fileInfo->realName = $key;
            $this->fileInfo->filePath = ($this->cdn ?: $this->uploadUrl) . '/' . $key;
            $this->fileInfo->fileName = $key;
            $this->fileInfo->filePathWater = $this->water($this->fileInfo->filePath);
            $this->authThumb && $this->thumb($this->fileInfo->filePath);
            return $this->fileInfo;
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
    }

    public function delete(string $key)
    {
        try {
            return $this->app()->deleteObject([
                'Bucket' => $this->storageName,
                'Key' => $key
            ]);
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
    }


    public function listbuckets(string $region, bool $line = false, bool $shared = false)
    {
        try {
            $res = $this->app()->listBuckets();
            return $res ?? [];
        } catch (\Throwable $e) {
            return [];
        }
    }

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
            $app->headBucket([
                'Bucket' => $name
            ]);
        } catch (\Throwable $e) {
            //Bucket không tồn tại trả về 404
            if (strstr('404', $e->getMessage())) {
                return $this->setError('COS:' . $e->getMessage());
            }
        }
        //Tạo bucket
        try {
            $res = $app->createBucket([
                'Bucket' => $name,
                'ACL' => $acl,
            ]);
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

    public function getRegion()
    {
        return [
            [
                'value' => 'cn-north-1',
                'label' => 'Bắc Trung Quốc - Beijing'
            ],
            [
                'value' => 'cn-east-1',
                'label' => 'Đông Trung Quốc - Suqian'
            ],
            [
                'value' => 'cn-east-2',
                'label' => 'Đông Trung Quốc - Shanghai'
            ],
            [
                'value' => 'cn-south-1',
                'label' => 'Nam Trung Quốc - Guangzhou'
            ]
        ];
    }

    public function deleteBucket(string $name, string $region = '')
    {
        try {
            $this->storageRegion = $region;
            $this->app()->deleteBucket([
                'Bucket' => $name, // REQUIRED
            ]);
            return true;
        } catch (AcmException $e) {
            return $this->setError($e->getMessage());
        }
    }

    public function getDomian(string $name, string $region = null)
    {
        try {
            $this->storageRegion = $region;
            $res = $this->app()->getBucketPolicy([
                'Bucket' => $name
            ]);
            return $res['DomainName'] ?? [];
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
    }

    public function bindDomian(string $name, string $domain, string $region = null)
    {
        try {
            $this->storageRegion = $region;
            $this->app()->putBucketWebsite([
                'Bucket' => $name,
                'WebsiteConfiguration' => [
                    'RedirectAllRequestsTo' => [
                        'HostName' => $domain,
                        'Protocol' => 'http'
                    ]
                ]
            ]);
            return true;
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
    }

    public function setBucketCors(string $name, string $region)
    {
        $this->storageRegion = $region;
        try {
            $this->app()->putBucketCors([
                'Bucket' => $name, // REQUIRED
                'CORSConfiguration' => [ // REQUIRED
                    'CORSRules' => [ // REQUIRED
                        [
                            'AllowedHeaders' => ['*'],
                            'AllowedMethods' => ['POST', 'GET', 'PUT', 'DELETE', 'HEAD'], // REQUIRED
                            'AllowedOrigins' => ['*'], // REQUIRED
                            'ExposeHeaders' => ['Etag'],
                            'MaxAgeSeconds' => 0
                        ],
                    ],
                ]
            ]);
            return true;
        } catch (\Throwable $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * Lấy khóa tải lên OSS
     * @return mixed|void
     */
    public function getTempKeys($key = '', $path = '', $contentType = '', $expires = '+10 minutes')
    {
        try {
            $app = $this->app();
            $cmd = $app->getCommand(
                'PutObject', [
                    'Bucket' => $this->storageName,
                    'Key' => $key,
//                    'SourceFile' => $path,
                    'ContentType' => $contentType
                ]
            );
            $request = $app->createPresignedRequest($cmd, $expires, ['Scheme' => 'https']);
            return [
                'upload_url' => (string)$request->getUri(),
                'type' => 'JDOSS',
                'url' => $this->uploadUrl . '/' . $key
            ];
        } catch (\Throwable $e) {
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
                        throw new AdminException(400722);
                    }
                    $waterPath = $filePath .= '/watermark,image_' . base64_encode($waterConfig['watermark_image']) . ',t_' . $waterConfig['watermark_opacity'] . ',g_' . ($this->position[$waterConfig['watermark_position']] ?? 'nw') . ',x_' . $waterConfig['watermark_x'] . ',y_' . $waterConfig['watermark_y'];
                    break;
                case 2://Văn bản
                    if (!$waterConfig['watermark_text']) {
                        throw new AdminException(400723);
                    }
                    $waterConfig['watermark_text_color'] = str_replace('#', '', $waterConfig['watermark_text_color']);
                    $waterPath = $filePath .= '/watermark,text_' . base64_encode($waterConfig['watermark_text']) . ',color_' . $waterConfig['watermark_text_color'] . ',size_' . $waterConfig['watermark_text_size'] . ',g_' . ($this->position[$waterConfig['watermark_position']] ?? 'nw') . ',x_' . $waterConfig['watermark_x'] . ',y_' . $waterConfig['watermark_y'];
                    break;
            }
        }
        return $waterPath;
    }
}
