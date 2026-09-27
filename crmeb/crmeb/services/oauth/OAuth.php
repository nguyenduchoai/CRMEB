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

namespace crmeb\services\oauth;


use crmeb\basic\BaseManager;
use crmeb\services\oauth\storage\MiniProgram;
use crmeb\services\oauth\storage\TouTiao;
use crmeb\services\oauth\storage\Wechat;
use think\facade\Config;

/**
 * Đăng nhập bên thứ ba
 * Class OAuth
 * @package crmeb\services\oauth
 * @mixin Wechat
 * @mixin TouTiao
 * @mixin MiniProgram
 */
class OAuth extends BaseManager
{

    /**
     * Tên space
     * @var string
     */
    protected $namespace = '\\crmeb\\services\\oauth\\storage\\';

    /**
     * @return mixed
     */
    protected function getDefaultDriver()
    {
        return Config::get('oauth.default', 'wechat');
    }
}
