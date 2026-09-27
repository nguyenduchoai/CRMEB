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

namespace crmeb\services\oauth;

/**
 * Đăng nhập bên thứ ba
 * Interface OAuthInterface
 * @package crmeb\services\oauth
 */
interface OAuthInterface
{

    /**
     * Lấy thông tin người dùng
     * @param string $openid
     * @return mixed
     */
    public function getUserInfo(string $openid);

    /**
     * Ủy quyền
     * @param string|null $code
     * @param array $options
     * @return mixed
     */
    public function oauth(string $code = null, array $options = []);

}
