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
namespace app\adminapi\controller\v1\application\routine;

use app\jobs\notice\SyncMessageJob;
use app\services\message\SystemNotificationServices;
use app\services\other\QrcodeServices;
use app\services\system\attachment\SystemAttachmentServices;
use crmeb\exceptions\AdminException;
use think\exception\ValidateException;
use think\facade\App;
use app\adminapi\controller\AuthController;
use crmeb\services\FileService;
use app\services\other\UploadService;
use crmeb\services\app\MiniProgramService;

/**
 * Class RoutineTemplate
 * @package app\adminapi\controller\v1\application\routine
 */
class RoutineTemplate extends AuthController
{
    protected $cacheTag = '_system_wechat';

    /**
     * Phương thức khởi tạo
     * WechatTemplate constructor.
     * @param App $app
     * @param SystemNotificationServices $services
     */
    public function __construct(App $app, SystemNotificationServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Đồng bộ tin nhắn đăng ký
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function syncSubscribe()
    {
        if (!sys_config('routine_appId') || !sys_config('routine_appsecret')) {
            throw new AdminException(400236);
        }

        $list = MiniProgramService::getSubscribeTemplateList();
        foreach ($list->data as $v) {
            MiniProgramService::delSubscribeTemplate($v['priTmplId']);
        }

        $tempKeys = $this->services->getTempKey('routine');
        foreach ($tempKeys as $key => $content) {
            SyncMessageJob::dispatch('SyncSubscribe', [$key, $content]);
        }

        return app('json')->success(100038);
    }

    /**
     * Tải Mini Program
     * @return mixed
     */
    public function downloadTemp()
    {
        [$name, $is_live] = $this->request->postMore([
            ['name', ''],
            ['is_live', 0]
        ], true);
        if (sys_config('routine_appId', '') == '') throw new AdminException(400236);
        try {
            @unlink(public_path() . 'statics/download/routine.zip');
            //Sao chép file gốc
            /** @var FileService $fileService */
            $fileService = app(FileService::class);
            $fileService->copyDir(public_path() . 'statics/mp_view', public_path() . 'statics/download');
            //Thay thế appid và tên
            $this->updateConfigJson(sys_config('routine_appId'), $name != '' ? $name : sys_config('routine_name'));
            //Có mở livestream không
            if ($is_live == 0) $this->updateAppJson();
            //Thay thế url
            $this->updateUrl('https://' . $_SERVER['HTTP_HOST']);
            //Nén file
            $fileService->addZip(public_path() . 'statics/download', public_path() . 'statics/download/routine.zip', public_path() . 'statics/download');
            $data['url'] = sys_config('site_url') . '/statics/download/routine.zip';
            return app('json')->success($data);
        } catch (\Throwable $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * Thay thế url
     * @param $url
     */
    public function updateUrl($url)
    {
        $fileUrl = app()->getRootPath() . "public/statics/download/common/vendor.js";
        $string = file_get_contents($fileUrl); //Tải file cấu hình
        $string = str_replace('https://demo.crmeb.com', $url, $string); // Dùng regex tìm rồi thay thế
        $newFileUrl = app()->getRootPath() . "public/statics/download/common/vendor.js";
        @file_put_contents($newFileUrl, $string); // Ghi file cấu hình

    }

    /**
     * Kiểm tra có mở livestream không (đã ngừng dùng)
     * @param int $iszhibo
     */
    public function updateAppJson()
    {
        $fileUrl = app()->getRootPath() . "public/statics/download/app.json";
        $string = file_get_contents($fileUrl); //Tải file cấu hình
        $pats = '/,
      "plugins": \{
        "live-player-plugin": \{
          "version": "(.*?)",
          "provider": "(.*?)"
        }
      }/';
        $string = preg_replace($pats, '', $string); // Dùng regex tìm rồi thay thế
        $newFileUrl = app()->getRootPath() . "public/statics/download/app.json";
        @file_put_contents($newFileUrl, $string); // Ghi file cấu hình
    }

    /**
     * Thay thế appid
     * @param string $appid
     * @param string $projectanme
     */
    public function updateConfigJson($appId = '', $projectName = '')
    {
        $fileUrl = app()->getRootPath() . "public/statics/download/project.config.json";
        $string = file_get_contents($fileUrl); //Tải file cấu hình
        // Thay thế appid
        $appIdOld = '/"appid"(.*?),/';
        $appIdNew = '"appid"' . ': ' . '"' . $appId . '",';
        $string = preg_replace($appIdOld, $appIdNew, $string); // Dùng regex tìm rồi thay thế
        // Thay thế tên Mini Program
        $projectNameOld = '/"projectname"(.*?),/';
        $projectNameNew = '"projectname"' . ': ' . '"' . $projectName . '",';
        $string = preg_replace($projectNameOld, $projectNameNew, $string); // Dùng regex tìm rồi thay thế
        $newFileUrl = app()->getRootPath() . "public/statics/download/project.config.json";
        @file_put_contents($newFileUrl, $string); // Ghi file cấu hình
    }

    /**
     * Lấy mã Mini Program
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDownloadInfo()
    {
        $data['routine_name'] = sys_config('routine_name', '');
        if (sys_config('routine_appId') == '') {
            $data['code'] = '';
        } else {
            $name = $data['routine_name'] . '.jpg';
            /** @var SystemAttachmentServices $systemAttachmentModel */
            $systemAttachmentModel = app()->make(SystemAttachmentServices::class);
            $imageInfo = $systemAttachmentModel->getInfo(['name' => $name]);
            if (!$imageInfo) {
                /** @var QrcodeServices $qrcode */
                $qrcode = app()->make(QrcodeServices::class);
                $resForever = $qrcode->qrCodeForever(0, 'code');
                if ($resForever) {
                    $resCode = MiniProgramService::appCodeUnlimitService($resForever->id, '', 280);
                    $res = ['res' => $resCode, 'id' => $resForever->id];
                } else {
                    $res = false;
                }
                if (!$res) throw new ValidateException(400237);
                $upload = UploadService::init(1);
                if ($upload->to('routine/code')->setAuthThumb(false)->stream((string)$res['res'], $name) === false) {
                    return $upload->getError();
                }
                $imageInfo = $upload->getUploadInfo();
                $imageInfo['image_type'] = 1;
                $systemAttachmentModel->attachmentAdd($imageInfo['name'], $imageInfo['size'], $imageInfo['type'], $imageInfo['dir'], $imageInfo['thumb_path'], 1, $imageInfo['image_type'], $imageInfo['time'], 2);
                $qrcode->update($res['id'], ['status' => 1, 'time' => time(), 'qrcode_url' => $imageInfo['dir']]);
                $data['code'] = sys_config('site_url') . $imageInfo['dir'];
            } else $data['code'] = sys_config('site_url') . $imageInfo['att_dir'];
        }
        $data['appId'] = sys_config('routine_appId');
        $data['help'] = 'https://doc.crmeb.com/web/single/crmeb_v4/978';
        return app('json')->success($data);
    }
}
