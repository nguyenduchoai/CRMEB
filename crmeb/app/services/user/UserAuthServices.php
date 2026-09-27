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
declare (strict_types=1);

namespace app\services\user;

use app\services\BaseServices;
use app\dao\user\UserAuthDao;
use crmeb\exceptions\AuthException;
use crmeb\services\CacheService;
use crmeb\utils\JwtAuth;

/**
 *
 * Class UserAuthServices
 * @package app\services\user
 */
class UserAuthServices extends BaseServices
{

    /**
     * UserAuthServices constructor.
     * @param UserAuthDao $dao
     */
    public function __construct(UserAuthDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy thông tin ủy quyền
     * @param $token
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function parseToken($token): array
    {
        $md5Token = is_null($token) ? '' : md5($token);

        if ($token === 'undefined') {
            throw new AuthException(110002);
        }
        if (!$token || !$tokenData = CacheService::get($md5Token))
            throw new AuthException(110002);

        if (!is_array($tokenData) || empty($tokenData) || !isset($tokenData['uid'])) {
            throw new AuthException(110002);
        }

        /** @var JwtAuth $jwtAuth */
        $jwtAuth = app()->make(JwtAuth::class);
        //Đặt phân tích token
        [$id, $type] = $jwtAuth->parseToken($token);


        try {
            $jwtAuth->verifyToken();
        } catch (\Throwable $e) {
            if (!request()->isCli()) CacheService::delete($md5Token);
            throw new AuthException(110003);
        }

        $user = $this->dao->get(['uid' => $id, 'is_del' => 0, 'status' => 1]);

        if (!$user || $user->uid != $tokenData['uid']) {
            if (!request()->isCli()) CacheService::delete($md5Token);
            throw new AuthException(110004);
        }

        $this->dao->update(['uid' => $id], ['last_time' => time()]);

        $tokenData['type'] = $type;
        return compact('user', 'tokenData');
    }

}
