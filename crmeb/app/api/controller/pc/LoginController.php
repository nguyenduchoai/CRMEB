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

namespace app\api\controller\pc;

use app\services\pc\LoginServices;
use crmeb\services\CacheService;

class LoginController
{
    protected $services;

    public function __construct(LoginServices $services)
    {
        $this->services = $services;
    }

    /**
     * Lấy KEY đăng nhập bằng quét mã
     * @return mixed
     */
    public function getLoginKey()
    {
        $key = md5(time() . uniqid());
        $time = time() + 600;
        CacheService::set($key, 1, 600);
        return app('json')->success(['key' => $key, 'time' => $time]);
    }

    /**
     * Đăng nhập bằng quét mã
     * @param string $key
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function scanLogin(string $key)
    {
        return app('json')->success($this->services->scanLogin($key));
    }

    /**
     * Đăng nhập quét mã qua nền tảng mở
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function wechatAuth()
    {
        return app('json')->success($this->services->wechatAuth());
    }

    /**
     * Lấy id nền tảng công khai
     * @return mixed
     */
    public function getAppid()
    {
        return app('json')->success([
            'appid' => sys_config('wechat_open_app_id'),
            'version' => get_crmeb_version()
        ]);
    }
}
