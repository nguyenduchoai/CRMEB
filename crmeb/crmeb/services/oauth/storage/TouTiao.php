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

namespace crmeb\services\oauth\storage;


use crmeb\basic\BaseStorage;
use crmeb\services\oauth\OAuthInterface;

/**
 * Đăng nhập Mini Program Toutiao
 * Class TouTiao
 * @package crmeb\services\oauth\storage
 */
class TouTiao extends BaseStorage implements OAuthInterface
{

    protected function initialize(array $config)
    {
        // TODO: Implement initialize() method.
    }

    public function getUserInfo(string $openid)
    {
        // TODO: Implement getUserInfo() method.
    }

    public function oauth(string $code = null, array $options = [])
    {
        // TODO: Implement oauth() method.
    }
}
