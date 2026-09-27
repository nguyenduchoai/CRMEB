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
namespace crmeb\services\easywechat\wechatTemplate;

use EasyWeChat\Core\AccessToken;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ProgramProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['wechat.access_token'] = function ($pimple) {
            return new AccessToken(
                $pimple['config']['app_id'],
                $pimple['config']['secret'],
                $pimple['cache']
            );
        };

        $pimple['new_notice'] = function ($pimple) {
            return new ProgramTemplate($pimple['wechat.access_token']);
        };
    }
}