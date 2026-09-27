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
namespace crmeb\services\easywechat\Open3rd;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Đăng ký nền tảng bên thứ ba
 * Class ProgramProvider
 * @package crmeb\utils
 */
class ProgramProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['mini_program.component_access_token'] = function ($pimple) {
            return new AccessToken(
                $pimple['config']['open3rd']['component_appid'],
                $pimple['config']['open3rd']['component_appsecret'],
                $pimple['config']['open3rd']['component_verify_ticket'],
                $pimple['config']['open3rd']['authorizer_appid']
            );
        };

        $pimple['mini_program.open3rd'] = function ($pimple) {
            return new ProgramOpen3rd($pimple['mini_program.component_access_token']);
        };
    }
}
