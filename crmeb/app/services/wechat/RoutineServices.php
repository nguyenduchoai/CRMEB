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
declare (strict_types=1);

namespace app\services\wechat;

use app\services\BaseServices;
use app\dao\wechat\WechatUserDao;
use app\services\message\SystemNotificationServices;
use app\services\other\QrcodeServices;
use app\services\user\LoginServices;
use app\services\user\UserServices;
use app\services\user\UserVisitServices;
use crmeb\exceptions\ApiException;
use crmeb\services\CacheService;
use crmeb\services\app\MiniProgramService;
use crmeb\services\oauth\OAuth;

/**
 *
 * Class RoutineServices
 * @package app\services\wechat
 */
class RoutineServices extends BaseServices
{

    /**
     * RoutineServices constructor.
     * @param WechatUserDao $dao
     */
    public function __construct(WechatUserDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Trả về key cache thông tin người dùng, trả về có bắt buộc liên kết số điện thoại không
     * @param $code
     * @param $spread
     * @param $spid
     * @return array
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/12
     */
    public function authType($code, $spread, $spid)
    {
        $agent_id = 0;
        $userInfoConfig = app()->make(OAuth::class, ['mini_program'])->oauth($code, ['silence' => true]);
        if (!isset($userInfoConfig['openid'])) {
            throw new ApiException('Ủy quyền ngầm thất bại');
        }
        $routineInfo = ['unionid' => $userInfoConfig['unionid'] ?? ''];
        $info = app()->make(QrcodeServices::class)->getOne(['id' => $spread, 'status' => 1]);
        if ($spread && $info) {
            if ($info['third_type'] == 'agent') {
                $agent_id = $info['third_id'];
            } else {
                $spid = $info['third_id'];
            }
        }
        $openid = $userInfoConfig['openid'];
        $routineInfo['openid'] = $openid;
        $routineInfo['spid'] = $spid;
        $routineInfo['code'] = $spread;
        $routineInfo['session_key'] = $userInfoConfig['session_key'];
        $routineInfo['headimgurl'] = sys_config('h5_avatar');
        $createData = [$openid, $routineInfo, $spid, $agent_id, 'routine', 'routine'];
        $userInfoKey = md5($openid . '_' . time() . '_routine');
        CacheService::set($userInfoKey, $createData, 7200);
        $bindPhone = false;
        $user = app()->make(WechatUserServices::class)->getAuthUserInfo($openid, 'routine');
        if (sys_config('store_user_mobile') && (($user && $user['phone'] == '') || !$user)) $bindPhone = true;
        return ['bindPhone' => $bindPhone, 'key' => $userInfoKey];
    }

    /**
     * Lấy token theo cache
     * @param $key
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/12
     */
    public function authLogin($key)
    {
        $createData = CacheService::get($key);
        //Ghi thông tin người dùng
        $user = app()->make(WechatUserServices::class)->wechatOauthAfter($createData);
        $token = $this->createToken((int)$user['uid'], 'api');
        if ($token) {
            app()->make(UserVisitServices::class)->loginSaveVisit($user);
            return [
                'token' => $token['token'],
                'expires_time' => $token['params']['exp'],
                'bindName' => (int)sys_config('get_avatar') && $user['avatar'] == sys_config('h5_avatar'),
            ];
        } else {
            throw new ApiException('Đăng nhập thất bại');
        }
    }

    /**
     * Tự động lấy số điện thoại để liên kết
     * @param $code
     * @param $iv
     * @param $encryptedData
     * @param $spread
     * @param $spid
     * @param string $key
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function authBindingPhone($code, $iv, $encryptedData, $spread, $spid, $key = '')
    {
        $wechatInfo = [];
        $agent_id = 0;
        $userType = $login_type = 'routine';
        if ($key) {
            [$openid, $wechatInfo, $spreadId, $agent_id, $login_type, $userType] = CacheService::get($key);
        }

        /** @var OAuth $oauth */
        $oauth = app()->make(OAuth::class, ['mini_program']);
        [$userInfoCong, $userInfo] = $oauth->oauth($code, [
            'iv' => $iv,
            'encryptedData' => $encryptedData
        ]);
        $session_key = $userInfoCong['session_key'];
        if (!$userInfo || !isset($userInfo['purePhoneNumber'])) {
            throw new ApiException('Lấy thông tin người dùng thất bại');
        }

        $spreadId = $spid ?? 0;
        /** @var QrcodeServices $qrcode */
        $qrcode = app()->make(QrcodeServices::class);
        if ($spread && ($info = $qrcode->getOne(['id' => $spread, 'status' => 1]))) {
            $spreadId = $info['third_id'];
        }
        $openid = $userInfoCong['openid'];
        $wechatInfo['openid'] = $openid;
        $wechatInfo['unionid'] = $userInfoCong['unionid'] ?? '';
        $wechatInfo['spid'] = $spreadId;
        $wechatInfo['code'] = $spread;
        $wechatInfo['session_key'] = $session_key;
        $wechatInfo['phone'] = $userInfo['purePhoneNumber'];
        /** @var WechatUserServices $wechatUserServices */
        $wechatUserServices = app()->make(WechatUserServices::class);
        //Ghi thông tin người dùng
        $user = $wechatUserServices->wechatOauthAfter([$openid, $wechatInfo, $spreadId, $agent_id, $login_type, $userType]);
        $token = $this->createToken((int)$user['uid'], 'api');
        if ($token) {
            app()->make(UserVisitServices::class)->loginSaveVisit($user);
            return [
                'token' => $token['token'],
                'expires_time' => $token['params']['exp'],
                'bindName' => (int)sys_config('get_avatar') && $user['avatar'] == sys_config('h5_avatar'),
            ];
        } else {
            throw new ApiException('Đăng nhập thất bại');
        }
    }

    /**
     * Đăng nhập bằng số điện thoại trên Mini Program
     * @param $key
     * @param $phone
     * @param string $spread_code
     * @param string $spread_spid
     * @param string $code
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/12
     */
    public function phoneLogin($key, $phone, $spread = '', $agent_id = '', $spid = '', $code = '')
    {
        if ($code == '') {
            [$openid, $routineInfo, $spid, $agent_id, $login_type, $userType] = CacheService::get($key);
            $routineInfo['phone'] = $phone;
            $createData = [$openid, $routineInfo, $spid, $agent_id, $login_type, $userType];
        } else {
            $userInfoConfig = app()->make(OAuth::class, ['mini_program'])->oauth($code, ['silence' => true]);
            if (!isset($userInfoConfig['openid'])) {
                throw new ApiException('Ủy quyền ngầm thất bại');
            }
            $routineInfo = ['unionid' => $userInfoConfig['unionid'] ?? ''];
            $info = app()->make(QrcodeServices::class)->getOne(['id' => $spread, 'status' => 1]);
            if ($spread && $info) {
                $spid = $info['third_id'];
            }
            $openid = $userInfoConfig['openid'];
            $routineInfo['openid'] = $openid;
            $routineInfo['spid'] = $spid;
            $routineInfo['code'] = $spread;
            $routineInfo['session_key'] = $userInfoConfig['session_key'];
            $routineInfo['headimgurl'] = sys_config('h5_avatar');
            $routineInfo['phone'] = $phone;
            $createData = [$openid, $routineInfo, $spid, $agent_id, 'routine', 'routine'];
        }
        //Ghi thông tin người dùng
        $user = app()->make(WechatUserServices::class)->wechatOauthAfter($createData);
        $token = $this->createToken((int)$user['uid'], 'api');
        if ($token) {
            app()->make(UserVisitServices::class)->loginSaveVisit($user);
            return [
                'token' => $token['token'],
                'expires_time' => $token['params']['exp'],
                'bindName' => (int)sys_config('get_avatar') && $user['avatar'] == sys_config('h5_avatar'),
            ];
        } else {
            throw new ApiException('Đăng nhập thất bại');
        }
    }

    /**
     * Liên kết số điện thoại trên Mini Program
     * @param $code
     * @param $iv
     * @param $encryptedData
     * @return bool
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/02/24
     */
    public function bindingPhone($code, $iv, $encryptedData)
    {
        [$userInfoCong, $userInfo] = app()->make(OAuth::class, ['mini_program'])->oauth($code, [
            'iv' => $iv,
            'encryptedData' => $encryptedData
        ]);
        if (!$userInfo || !isset($userInfo['purePhoneNumber'])) {
            throw new ApiException('Lấy thông tin người dùng thất bại');
        }
        $uid = app()->make(WechatUserServices::class)->openidToUid($userInfoCong['openid']);
        $userServices = app()->make(UserServices::class);
        if ($userServices->count(['phone' => $userInfo['purePhoneNumber'], 'is_del' => 0])) {
            throw new ApiException('Số điện thoại đã được đăng ký');
        }
        $res = $userServices->update(['uid' => $uid], ['phone' => $userInfo['purePhoneNumber']]);
        if ($res) return true;
        throw new ApiException('Liên kết thất bại');
    }

    /**
     * Sau khi Mini Program tạo người dùng thì trả về uid
     * @param $routine
     * @return array
     */
    public function routineOauth($routine)
    {
        $routineInfo['nickname'] = filter_emoji($routine['nickName']);//Họ tên
        $routineInfo['sex'] = $routine['gender'];//Giới tính
        $routineInfo['language'] = $routine['language'];//Ngôn ngữ
        $routineInfo['city'] = $routine['city'];//Thành phố
        $routineInfo['province'] = $routine['province'];//Tỉnh
        $routineInfo['country'] = $routine['country'];//Quốc gia
        $routineInfo['headimgurl'] = $routine['avatarUrl'];//Ảnh đại diện
        $routineInfo['openid'] = $routine['openId'];
        $routineInfo['session_key'] = $routine['session_key'];//Khóa phiên (session key)
        $routineInfo['unionid'] = $routine['unionId'];//Định danh duy nhất của người dùng trên nền tảng mở (open platform)
        $routineInfo['user_type'] = 'routine';//Loại người dùng
        $routineInfo['phone'] = $routine['phone'] ?? $routine['purePhoneNumber'] ?? '';
        $spid = $routine['spid'] ?? 0;//uid quan hệ liên kết
        //Lấy biết có quét mã vào Mini Program không
        /** @var QrcodeServices $qrcode */
        $qrcode = app()->make(QrcodeServices::class);
        if (isset($routine['code']) && $routine['code'] && ($info = $qrcode->get($routine['code']))) {
            $spid = $info['third_id'];
        }
        return [$routine['openId'], $routineInfo, $spid, $routine['login_type'] ?? 'routine', 'routine'];
    }

    /**
     * Callback thanh toán Mini Program
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Core\Exceptions\FaultException
     */
    public function notify()
    {
        return MiniProgramService::handleNotify();
    }

    /**
     * Lấy id tin nhắn đăng ký Mini Program
     * @return bool|mixed|null
     */
    public function tempIds()
    {
        return CacheService::remember('TEMP_IDS_LIST', function () {
            /** @var SystemNotificationServices $sysNotify */
            $sysNotify = app()->make(SystemNotificationServices::class);
            return $sysNotify->getColumn([['routine_tempid', '<>', '']], 'routine_tempid', 'mark');
        });
    }

    /**
     * Lấy danh sách livestream Mini Program
     * @param $page
     * @param $limit
     * @return array|bool|mixed
     */
    public function live($page, $limit)
    {
        $list = CacheService::remember('WECHAT_LIVE_LIST_' . $page . '_' . $limit, function () use ($page, $limit) {
            $list = MiniProgramService::getLiveInfo((int)$page, (int)$limit);
            foreach ($list as &$item) {
                $item['_start_time'] = date('m-d H:i', $item['start_time']);
            }
            return $list;
        }, 600) ?: [];
        return $list;
    }

    /**
     * Cập nhật thông tin người dùng
     * @param $uid
     * @param array $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateUserInfo($uid, array $data)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $user = $userServices->getUserInfo($uid);
        if (!$user) {
            throw new ApiException('Dữ liệu không tồn tại');
        }
        $userInfo = [];
        $userInfo['nickname'] = filter_emoji($data['nickName'] ?? '');//Họ tên
        $userInfo['sex'] = $data['gender'] ?? '';//Giới tính
        $userInfo['language'] = $data['language'] ?? '';//Ngôn ngữ
        $userInfo['city'] = $data['city'] ?? '';//Thành phố
        $userInfo['province'] = $data['province'] ?? '';//Tỉnh
        $userInfo['country'] = $data['country'] ?? '';//Quốc gia
        $userInfo['headimgurl'] = $data['avatarUrl'] ?? '';//Ảnh đại diện
        $userInfo['is_complete'] = 1;
        /** @var LoginServices $loginService */
        $loginService = app()->make(LoginServices::class);
        $loginService->updateUserInfo($userInfo, $user);
        //Cập nhật thông tin người dùng
        if (!$this->dao->update(['uid' => $user['uid'], 'user_type' => 'routine'], $userInfo)) {
            throw new ApiException('Cập nhật thất bại');
        }
        return true;
    }
}
