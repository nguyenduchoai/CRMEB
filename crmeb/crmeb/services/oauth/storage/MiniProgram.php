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

namespace crmeb\services\oauth\storage;


use crmeb\basic\BaseStorage;
use crmeb\services\app\MiniProgramService;
use crmeb\services\oauth\OAuthException;
use crmeb\services\oauth\OAuthInterface;

/**
 * Đăng nhập Mini Program
 * Class MiniProgram
 * @package crmeb\services\oauth\storage
 */
class MiniProgram extends BaseStorage implements OAuthInterface
{

    protected function initialize(array $config)
    {
        // TODO: Implement initialize() method.
    }

    /**
     * @param string $openid
     * @return array|mixed
     */
    public function getUserInfo(string $openid)
    {
        return [];
    }

    /**
     * Đăng nhập ủy quyền
     * @param string|null $code
     * @param array $options
     * @return mixed
     */
    public function oauth(string $code = null, array $options = [])
    {
        if (!$code) {
            throw new OAuthException(100104);
        }

        try {
            $userInfoCong = MiniProgramService::getUserInfo($code);
            $session_key = $userInfoCong['session_key'];
        } catch (\Exception $e) {
            throw new OAuthException($e->getMessage());
        }

        if (!isset($userInfoCong['openid'])) {
            throw new OAuthException(410075);
        }

        //Có ủy quyền âm thầm (silent) không
        if (isset($options['silence']) && $options['silence'] === true) {
            return $userInfoCong;
        }

        if (empty($options['iv']) || empty($options['encryptedData'])) {
            throw new OAuthException(100100);
        }

        try {
            //Giải mã để lấy thông tin người dùng
            $userInfo = MiniProgramService::encryptor($session_key, $options['iv'], $options['encryptedData']);
        } catch (\Exception $e) {
            if ($e->getCode() == '-41003') {
                throw new OAuthException(410077);
            }
        }

        return [$userInfoCong, $userInfo];
    }
}
