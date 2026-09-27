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
namespace app\adminapi\controller\v1\application\wechat;

use app\adminapi\controller\AuthController;
use app\jobs\notice\SyncMessageJob;
use app\services\message\SystemNotificationServices;
use crmeb\exceptions\AdminException;
use crmeb\services\app\WechatService;
use think\facade\App;

/**
 * Tin nhắn mẫu WeChat
 * Class WechatTemplate
 * @package app\adminapi\controller\v1\application\wechat
 */
class WechatTemplate extends AuthController
{
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
     * Đồng bộ tin nhắn mẫu WeChat
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function syncSubscribe()
    {
        if (!sys_config('wechat_appid') || !sys_config('wechat_appsecret')) {
            throw new AdminException('Vui lòng cấu hình appid, appSecret và các tham số khác của OA WeChat trước');
        }

        $tempIds = $this->services->getTempId('wechat');
        foreach ($tempIds as $v) {
            WechatService::deleleTemplate($v);
        }

        $tempKeys = $this->services->getTempKey('wechat');

        foreach ($tempKeys as $key => $content) {
            SyncMessageJob::dispatch('SyncWechat', [$key, $content['wechat_content']]);
        }
        return app('json')->success('Đồng bộ thành công');
    }
}
