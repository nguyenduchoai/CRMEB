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

namespace app\services\kefu;


use crmeb\exceptions\AuthException;
use crmeb\services\oauth\OAuth;
use crmeb\utils\JwtAuth;
use app\services\BaseServices;
use crmeb\services\CacheService;
use app\dao\service\StoreServiceDao;
use app\services\wechat\WechatUserServices;

/**
 * Đăng nhập CSKH
 * Class LoginServices
 * @package app\services\kefu
 * @method get($id, ?array $field = [], ?array $with = []) Lấy một dòng dữ liệu
 */
class LoginServices extends BaseServices
{
    /**
     * LoginServices constructor.
     * @param StoreServiceDao $dao
     */
    public function __construct(StoreServiceDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Đăng nhập bằng tài khoản mật khẩu nhân viên CSKH
     * @param string $account
     * @param string $password
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function authLogin(string $account, string $password = null)
    {
        $kefuInfo = $this->dao->get(['account' => $account]);
        if (!$kefuInfo) {
            throw new AuthException(410141);
        }
        if ($password && !password_verify($password, $kefuInfo->password)) {
            throw new AuthException(410025);
        }
        if (!$kefuInfo->status) {
            throw new AuthException(410027);
        }
        $token = $this->createToken($kefuInfo->id, 'kefu');
        $kefuInfo->update_time = time();
        $kefuInfo->ip = request()->ip();
        $kefuInfo->online = 1;
        $kefuInfo->save();
        return [
            'token' => $token['token'],
            'exp_time' => $token['params']['exp'],
            'kefuInfo' => $kefuInfo->hidden(['password', 'ip', 'update_time', 'add_time', 'status', 'mer_id', 'customer', 'notify'])->toArray()
        ];
    }

    /**
     * Phân tích token
     * @param string $token
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function parseToken(string $token)
    {
        $noCli = !request()->isCli();
        //Kiểm tra token có hết hạn không
        $md5Token = md5($token);
        if (!$token || !CacheService::has($md5Token) || !(CacheService::get($md5Token, '', NULL, 'kefu'))) {
            throw new AuthException(110005);
        }
        if ($token === 'undefined') {
            throw new AuthException(110005);
        }

        /** @var JwtAuth $jwtAuth */
        $jwtAuth = app()->make(JwtAuth::class);
        //Đặt phân tích token
        [$id, $type] = $jwtAuth->parseToken($token);

        //Xác thực token
        try {
            $jwtAuth->verifyToken();
        } catch (\Throwable $e) {
            $noCli && CacheService::delete($md5Token);
            throw new AuthException(110006);
        }

        //Lấy thông tin quản trị viên
        $adminInfo = $this->dao->get($id);
        if (!$adminInfo || !$adminInfo->id) {
            $noCli && CacheService::delete($md5Token);
            throw new AuthException(110007);
        }

        $adminInfo->type = $type;
        return $adminInfo->hidden(['password', 'ip', 'status']);
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function wechatAuth()
    {
        /** @var OAuth $oauth */
        $oauth = app()->make(OAuth::class);
        $original = $oauth->oauth(null, ['open' => true]);
        if (!isset($original['unionid'])) {
            throw new AuthException(410132);
        }
        /** @var WechatUserServices $userService */
        $userService = app()->make(WechatUserServices::class);
        $uid = $userService->value(['unionid' => $original['unionid']], 'uid');
        if (!$uid) {
            throw new AuthException(410133);
        }
        $kefuInfo = $this->dao->get(['uid' => $uid]);
        if (!$kefuInfo) {
            throw new AuthException(410142);
        }
        if (!$kefuInfo->status) {
            throw new AuthException(410027);
        }
        $token = $this->createToken($kefuInfo->id, 'kefu');
        $kefuInfo->update_time = time();
        $kefuInfo->ip = request()->ip();
        $kefuInfo->save();
        return [
            'token' => $token['token'],
            'exp_time' => $token['params']['exp'],
            'kefuInfo' => $kefuInfo->hidden(['password', 'ip', 'update_time', 'add_time', 'status', 'mer_id', 'customer', 'notify'])->toArray()
        ];
    }

    /**
     * Kiểm tra có ai quét mã đăng nhập không
     * @param string $key
     * @return array|int[]
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function scanLogin(string $key)
    {
        $hasKey = CacheService::has($key);
        if ($hasKey === false) {
            $status = 0;//Không tồn tại thì cần làm mới mã QR
        } else {
            $keyValue = CacheService::get($key);
            if ($keyValue === '0') {
                $status = 1;//Đang quét mã
                $kefuInfo = $this->dao->get(['uniqid' => $key], ['account', 'uniqid']);
                if ($kefuInfo) {
                    $tokenInfo = $this->authLogin($kefuInfo->account);
                    $tokenInfo['status'] = 3;
                    $kefuInfo->uniqid = '';
                    $kefuInfo->save();
                    CacheService::delete($key);
                    return $tokenInfo;
                }
            } else {
                $status = 2;//Chưa quét mã
            }
        }
        return ['status' => $status];
    }
}
