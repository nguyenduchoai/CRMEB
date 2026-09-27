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
use crmeb\services\app\WechatOpenService;
use crmeb\services\app\WechatService;
use crmeb\services\oauth\OAuthException;
use crmeb\services\oauth\OAuthInterface;

/**
 * Đăng nhập OA WeChat
 * Class Wechat
 * @package crmeb\services\oauth\storage
 */
class Wechat extends BaseStorage implements OAuthInterface
{

    protected function initialize(array $config)
    {
        // TODO: Implement initialize() method.
    }


    /**
     * Lấy thông tin người dùng
     * @param string $openid
     * @return mixed
     */
    public function getUserInfo(string $openid)
    {
        return WechatService::oauth2Service()->getUserInfo($openid)->toArray();
    }

    /**
     * Ủy quyền
     * @param string|null $code
     * @param array $options
     * @return \EasyWeChat\Support\Collection|mixed
     */
    public function oauth(string $code = null, array $options = [])
    {
        $open = false;
        if (!empty($options['open'])) {
            $open = true;
        }

        if (!$open) {
            try {
                $wechatInfo = WechatService::oauth2Service()->oauth();
            } catch (\Throwable $e) {
                throw new OAuthException($e->getMessage());
            }
        } else {
            /** @var WechatOpenService $service */
            $service = app()->make(WechatOpenService::class);
            $wechatInfo = $service->getAuthorizationInfo();
            if (!$wechatInfo) {
                throw new OAuthException('Ủy quyền thất bại');
            }
        }

        return $wechatInfo;
    }
}
