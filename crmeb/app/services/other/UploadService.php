<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\services\other;

use app\services\system\config\SystemStorageServices;
use crmeb\exceptions\UploadException;
use crmeb\services\CacheService;
use crmeb\services\SystemConfigService;
use crmeb\services\upload\Upload;
use crmeb\utils\DownloadImage;

/**
 * Class UploadService
 * @package crmeb\services
 */
class UploadService
{

    /**
     * @var array
     */
    protected static $upload = [];

    /**
     * @param null $type
     * @return Upload|mixed
     */
    public static function init($type = null)
    {
        if (is_null($type)) {
            $type = (int)sys_config('upload_type', 1);
        }
        if (isset(self::$upload['upload_' . $type])) {
            return self::$upload['upload_' . $type];
        }
        $type = (int)$type;
        $config = [];
        switch ($type) {
            case 2://Qiniu
                $config = [
                    'accessKey' => sys_config('qiniu_accessKey'),
                    'secretKey' => sys_config('qiniu_secretKey'),
                ];
                break;
            case 3:// oss Alibaba Cloud
                $config = [
                    'accessKey' => sys_config('accessKey'),
                    'secretKey' => sys_config('secretKey'),
                ];
                break;
            case 4:// cos Tencent Cloud
                $config = [
                    'accessKey' => sys_config('tengxun_accessKey'),
                    'secretKey' => sys_config('tengxun_secretKey'),
                    'appid' => sys_config('tengxun_appid'),
                ];
                break;
            case 5://JD Cloud
                $config = [
                    'accessKey' => sys_config('jd_accessKey'),
                    'secretKey' => sys_config('jd_secretKey'),
                    'storageRegion' => sys_config('jd_storageRegion'),
                ];
                break;
            case 6://Huawei Cloud
                $config = [
                    'accessKey' => sys_config('hw_accessKey'),
                    'secretKey' => sys_config('hw_secretKey'),
                ];
                break;
            case 7://Tianyi Cloud
                $config = [
                    'accessKey' => sys_config('ty_accessKey'),
                    'secretKey' => sys_config('ty_secretKey'),
                ];
                break;
            case 1:
                break;
            default:
                throw new UploadException(400733);
        }

        //Ngoài lưu trữ cục bộ, tất cả còn lại đều lấy thông tin cấu hình
        if (1 !== $type) {
            /** @var SystemStorageServices $make */
            $make = app()->make(SystemStorageServices::class);
            $res = $make->getConfig($type);
            $config['uploadUrl'] = $res['domain'];
            $config['storageName'] = $res['name'];
            if (5 !== $type) $config['storageRegion'] = $res['region'];
            $config['cdn'] = $res['cdn'];
        }

        $thumb = SystemConfigService::more([
            'image_thumb_status',
            'thumb_big_height',
            'thumb_big_width',
            'thumb_mid_height',
            'thumb_mid_width',
            'thumb_small_height',
            'thumb_small_width'
        ]);
        $water = SystemConfigService::more([
            'image_watermark_status',
            'watermark_type',
            'watermark_image',
            'watermark_opacity',
            'watermark_position',
            'watermark_rotate',
            'watermark_text',
            'watermark_text_angle',
            'watermark_text_color',
            'watermark_text_size',
            'watermark_x',
            'watermark_y'
        ]);
        $config = array_merge($config, ['thumb' => $thumb], ['water' => $water]);
        return self::$upload['upload_' . $type] = new Upload($type, $config);
    }

    /**
     * Khởi tạo đối tượng xử lý ảnh thu nhỏ và watermark
     * @param string $filePath
     * @param bool $is_remote_down
     * @return Upload
     */
    public static function getOssInit(string $filePath, bool $is_remote_down = false)
    {
        //Cục bộ
        $uploadUrl = sys_config('site_url');
        if ($uploadUrl && strpos($filePath, $uploadUrl) !== false) {
            $filePath = explode($uploadUrl, $filePath)[1] ?? '';
            return self::init(1)->setFilepath($filePath);
        }
        $fileArr = parse_url($filePath);
        $fileHost = $fileArr['scheme'] . '://' . $fileArr['host'];
        /** @var SystemStorageServices $storageServices */
        $storageServices = app()->make(SystemStorageServices::class);
        $storageArr = CacheService::remember('storage_list', function () use ($storageServices) {
            return $storageServices->selectList([], 'domain,type')->toArray();
        });
        foreach ($storageArr as $item) {
            if ($fileHost == $item['domain']) {
                return self::init($item['type'])->setFilepath($filePath);
            }
        }
        //Ảnh từ xa, tải về cục bộ để xử lý
        if ($is_remote_down) {
            try {
                /** @var DownloadImage $down */
                $down = app()->make(DownloadImage::class);
                $data = $down->path('thumb_water')->downloadImage($filePath);
                $filePath = $data['path'] ?? '';
            } catch (\Throwable $e) {
                //Tải xuống thất bại thì truyền vào địa chỉ gốc
            }
        }
        return self::init(1)->setFilepath($filePath);
    }
}
