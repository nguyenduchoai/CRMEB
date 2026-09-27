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

namespace crmeb\services\app;

use crmeb\services\easywechat\Application;

class WechatOpenService
{

    protected function options()
    {
        $options = [
            'app_id' => sys_config('wechat_open_app_id'),
            'secret' => sys_config('wechat_open_app_secret')
        ];
        return $options;
    }

    /**
     * @return Application
     */
    protected function application()
    {
        return new Application($this->options());
    }

    /**
     * @return mixed
     */
    public function serve()
    {
        return $this->application()->open_platform->server->serve();
    }

    /**
     * Dùng mã ủy quyền để đổi lấy thông tin xác thực gọi API và thông tin ủy quyền của OA WeChat
     * @param string $code
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthorizationInfo()
    {
        return $this->application()->oauth->user();
    }

    /**
     * Lấy thông tin cơ bản tài khoản OA WeChat của bên được ủy quyền
     * @param string $appid
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthorizerInfo()
    {
        return $this->application()->oauth->user();
    }


}
