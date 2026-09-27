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

namespace app\services\user;

use app\dao\user\UserDao;
use app\services\BaseServices;
use app\services\yihaotong\SmsRecordServices;
use app\services\message\notice\SmsService;
use app\services\wechat\WechatUserServices;
use crmeb\exceptions\ApiException;
use crmeb\services\CacheService;
use crmeb\services\HttpService;
use Firebase\JWT\JWT;
use think\facade\Config;

/**
 *
 * Class LoginServices
 * @package app\services\user
 */
class LoginServices extends BaseServices
{

    /**
     * LoginServices constructor.
     * @param UserDao $dao
     */
    public function __construct(UserDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Đăng nhập tài khoản H5
     * @param $account
     * @param $password
     * @param $spread
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function login($account, $password, $spread, $agent_id)
    {
        $user = $this->dao->getOne(['account|phone' => $account, 'is_del' => 0]);
        if ($user) {
            if ($user->pwd !== md5((string)$password))
                throw new ApiException('Tài khoản hoặc mật khẩu không đúng');
            if ($user->pwd === md5('123456'))
                throw new ApiException('Vui lòng đổi mật khẩu ban đầu rồi thử đăng nhập lại');
        } else {
            throw new ApiException('Tài khoản hoặc mật khẩu không đúng');
        }
        if (!$user['status'])
            throw new ApiException('Bạn đã bị cấm đăng nhập, vui lòng liên hệ quản trị viên');

        //Cập nhật thông tin người dùng
        if ($agent_id) {
            $this->updateUserInfo(['code' => $agent_id, 'is_staff' => 1], $user);
        } else {
            $this->updateUserInfo(['code' => $spread], $user);
        }
        $token = $this->createToken((int)$user['uid'], 'api');
        if ($token) {
            return ['token' => $token['token'], 'expires_time' => $token['params']['exp']];
        } else
            throw new ApiException('Đăng nhập thất bại');
    }

    /**
     * Cập nhật thông tin người dùng
     * @param $user
     * @param $userInfo
     * @param false $is_new
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateUserInfo($user, $userInfo, $is_new = false)
    {
        $data = [];
        $data['phone'] = !isset($user['phone']) || !$user['phone'] ? $userInfo->phone : $user['phone'];
        $data['last_time'] = time();
        $data['last_ip'] = app()->request->ip();
        $spreadUid = $user['code'] ?? 0;
        //Nếu quét mã mời của nhân viên, người giới thiệu, đại lý, đại lý khu vực đều sẽ thay đổi.
        if (isset($user['is_staff']) && !$userInfo['is_agent'] && !$userInfo['is_division']) {
            $spreadInfo = $this->dao->get($spreadUid);
            if ($userInfo['uid'] != $spreadUid) {
                $data['spread_uid'] = $spreadUid;
                $data['spread_time'] = $userInfo->last_time;
            }
            $data['agent_id'] = $spreadInfo->agent_id;
            $data['division_id'] = $spreadInfo->division_id;
            $data['staff_id'] = $userInfo['uid'];
            $data['is_staff'] = $user['is_staff'] ?? 0;
            $data['division_type'] = 3;
            $data['division_status'] = 1;
            $data['division_change_time'] = time();
            $data['division_end_time'] = $spreadInfo->division_end_time;
            //Nếu nhân viên cửa hàng đổi đại lý, thì với những người dùng mà nhân viên đó đã giới thiệu dưới đại lý cũ, người giới thiệu trực tiếp của họ sẽ đổi từ nhân viên cửa hàng hiện tại thành đại lý cũ
            if ($userInfo->agent_id != 0 && $userInfo->agent_id != $spreadInfo->agent_id) {
                $this->dao->update(['staff_id' => $userInfo['uid'], 'spread_uid' => $userInfo['uid']], ['spread_uid' => $spreadInfo['agent_id'], 'staff_id' => 0]);
                $this->dao->getSearch(['staff_id' => $userInfo['uid'], 'not_spread_uid' => $userInfo['uid']])->update(['staff_id' => 0]);

            }
            //Sự kiện sau khi liên kết người dùng
            event('UserRegisterListener', [$spreadUid, $userInfo['user_type'], $userInfo['nickname'], $userInfo['uid'], $is_new]);
            //Đẩy tin nhắn
            event('NoticeListener', [['spreadUid' => $spreadUid, 'user_type' => $userInfo['user_type'], 'nickname' => $userInfo['nickname']], 'bind_spread_uid']);

            //Sự kiện tùy chỉnh - Liên kết quan hệ
            event('CustomEventListener', ['user_spread', [
                'uid' => $userInfo['uid'],
                'nickname' => $userInfo['nickname'],
                'spread_uid' => $spreadUid,
                'spread_time' => date('Y-m-d H:i:s'),
                'user_type' => $userInfo['user_type'],
            ]]);

        } else {
            if ($is_new) {
                if ($spreadUid) {
                    $spreadInfo = $this->dao->get($spreadUid);
                    $spreadUid = (int)$spreadUid;
                    $data['spread_uid'] = $spreadUid;
                    $data['spread_time'] = time();
                    $data['agent_id'] = $spreadInfo->agent_id;
                    $data['division_id'] = $spreadInfo->division_id;
                    $data['staff_id'] = $spreadInfo->staff_id;
                    //Sự kiện sau khi liên kết người dùng
                    event('UserRegisterListener', [$spreadUid, $userInfo['user_type'], $userInfo['nickname'], $userInfo['uid'], 1]);
                    //Đẩy tin nhắn
                    event('NoticeListener', [['spreadUid' => $spreadUid, 'user_type' => $userInfo['user_type'], 'nickname' => $userInfo['nickname']], 'bind_spread_uid']);

                    //Sự kiện tùy chỉnh - Liên kết quan hệ
                    event('CustomEventListener', ['user_spread', [
                        'uid' => $userInfo['uid'],
                        'nickname' => $userInfo['nickname'],
                        'spread_uid' => $spreadUid,
                        'spread_time' => date('Y-m-d H:i:s'),
                        'user_type' => $userInfo['user_type'],
                    ]]);
                }
            } else {
                //Liên kết vĩnh viễn
                $store_brokerage_binding_status = sys_config('store_brokerage_binding_status', 1);
                if ($userInfo->spread_uid && $store_brokerage_binding_status == 1 && !isset($user['is_staff'])) {
                    $data['login_type'] = $user['login_type'] ?? $userInfo->login_type;
                } else {
                    //Liên kết quan hệ tiếp thị liên kết = tất cả người dùng
                    if (sys_config('brokerage_bindind', 1) == 1) {
                        //Loại liên kết tiếp thị liên kết là theo khoảng thời gian và đã hết hạn || tạm thời
                        $store_brokerage_binding_time = sys_config('store_brokerage_binding_time', 30);
                        if (!$userInfo['spread_uid'] || $store_brokerage_binding_status == 3 || ($store_brokerage_binding_status == 2 && ($userInfo['spread_time'] + $store_brokerage_binding_time * 24 * 3600) < time())) {
                            if ($spreadUid && $user['code'] != $userInfo->uid && $userInfo->uid != $this->dao->value(['uid' => $spreadUid], 'spread_uid')) {
                                $spreadInfo = $this->dao->get($spreadUid);
                                $spreadUid = (int)$spreadUid;
                                $data['spread_uid'] = $spreadUid;
                                $data['spread_time'] = time();
                                $data['agent_id'] = $spreadInfo->agent_id;
                                $data['division_id'] = $spreadInfo->division_id;
                                $data['staff_id'] = $spreadInfo->staff_id;
                                //Sự kiện sau khi liên kết người dùng
                                event('UserRegisterListener', [$spreadUid, $userInfo['user_type'], $userInfo['nickname'], $userInfo['uid'], 0]);
                                //Đẩy tin nhắn
                                event('NoticeListener', [['spreadUid' => $spreadUid, 'user_type' => $userInfo['user_type'], 'nickname' => $userInfo['nickname']], 'bind_spread_uid']);

                                //Sự kiện tùy chỉnh - Liên kết quan hệ
                                event('CustomEventListener', ['user_spread', [
                                    'uid' => $userInfo['uid'],
                                    'nickname' => $userInfo['nickname'],
                                    'spread_uid' => $spreadUid,
                                    'spread_time' => date('Y-m-d H:i:s'),
                                    'user_type' => $userInfo['user_type'],
                                ]]);
                            }
                        }
                    }
                }
            }
        }
        if (!$this->dao->update($userInfo['uid'], $data, 'uid')) {
            throw new ApiException('Sửa thất bại');
        }
        return true;
    }

    public function verify(SmsService $services, $phone, $type, $time)
    {
        if ($this->dao->getOne(['account' => $phone, 'is_del' => 0]) && $type == 'register') {
            throw new ApiException('Số điện thoại đã được đăng ký');
        }
        $code = rand(100000, 999999);
        $data['code'] = $code;
        $data['time'] = $time;
        $res = $services->send(true, $phone, $data, 'verify_code');
        if ($res !== true)
            throw new ApiException('Nền tảng SMS gửi mã xác thực thất bại');
        return $code;
    }

    /**
     * Người dùng H5 đăng ký
     * @param $account
     * @param $password
     * @param $spread
     * @param string $user_type
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function register($account, $password, $spread, $user_type = 'h5')
    {
        if ($this->dao->getOne(['account|phone' => $account, 'is_del' => 0])) {
            throw new ApiException('Số điện thoại đã được đăng ký');
        }
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $phone = $account;
        $data['account'] = $account;
        $data['pwd'] = md5((string)$password);
        $data['phone'] = $phone;
        if ($spread) {
            $data['spread_uid'] = $spread;
            $data['spread_time'] = time();
            $spreadInfo = $userServices->get($spread);
            $data['division_id'] = $spreadInfo['division_id'];
            $data['agent_id'] = $spreadInfo['agent_id'];
            $data['staff_id'] = $spreadInfo['staff_id'];
        }
        $data['real_name'] = '';
        $data['birthday'] = 0;
        $data['card_id'] = '';
        $data['mark'] = '';
        $data['addres'] = '';
        $data['user_type'] = $user_type;
        $data['add_time'] = time();
        $data['add_ip'] = app('request')->ip();
        $data['last_time'] = time();
        $data['last_ip'] = app('request')->ip();
        $data['nickname'] = substr_replace($account, '****', 3, 4);
        $data['avatar'] = sys_config('h5_avatar');
        $data['city'] = '';
        $data['language'] = '';
        $data['province'] = '';
        $data['country'] = '';
        $data['status'] = 1;
        if (!$re = $this->dao->save($data)) {
            throw new ApiException('Đăng ký thất bại');
        } else {
            $userServices->rewardNewUser((int)$re->uid);
            //Sự kiện sau khi tạo người dùng
            event('UserRegisterListener', [$spread, $user_type, $data['nickname'], $re->uid, 1]);

            //Sự kiện tùy chỉnh - Người dùng đăng ký
            event('CustomEventListener', ['user_register', [
                'uid' => $re->uid,
                'nickname' => $data['nickname'],
                'phone' => $data['phone'],
                'add_time' => date('Y-m-d H:i:s'),
                'user_type' => $user_type,
            ]]);

            if ($spread) {
                //Đẩy tin nhắn
                event('NoticeListener', [['spreadUid' => $spread, 'user_type' => $user_type, 'nickname' => $data['nickname']], 'bind_spread_uid']);

                //Sự kiện tùy chỉnh - Liên kết quan hệ
                event('CustomEventListener', ['user_spread', [
                    'uid' => $re->uid,
                    'nickname' => $data['nickname'],
                    'spread_uid' => $spread,
                    'spread_time' => date('Y-m-d H:i:s'),
                    'user_type' => $user_type,
                ]]);
            }
            return $re;
        }
    }

    /**
     * Đặt lại mật khẩu
     * @param $account
     * @param $password
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function reset($account, $password)
    {
        $user = $this->dao->getOne(['account|phone' => $account, 'is_del' => 0], 'uid');
        if (!$user) {
            throw new ApiException('Người dùng không tồn tại');
        }
        if (!$this->dao->update($user['uid'], ['pwd' => md5((string)$password)], 'uid')) {
            throw new ApiException('Đổi mật khẩu thất bại');
        }
        return true;
    }

    /**
     * Đăng nhập bằng số điện thoại
     * @param $phone
     * @param $spread
     * @param string $user_type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function mobile($phone, $spread, string $user_type = 'h5', $agent_id = 0)
    {
        //Truy vấn cơ sở dữ liệu
        $user = $this->dao->getOne(['account|phone' => $phone, 'is_del' => 0]);
        if (!$user) {
            $user = $this->register($phone, '123456', $spread, $user_type);
            if (!$user) {
                throw new ApiException('Đăng nhập thất bại, không thể tạo người dùng mới, vui lòng thử lại sau');
            }
        }

        if (!$user->status)
            throw new ApiException('Bạn đã bị cấm đăng nhập, vui lòng liên hệ quản trị viên');

        // Đặt quan hệ giới thiệu
        if ($agent_id) {
            $this->updateUserInfo(['code' => $agent_id, 'is_staff' => 1], $user);
        } else {
            $this->updateUserInfo(['code' => $spread], $user);
        }

        $token = $this->createToken((int)$user['uid'], 'api');
        if ($token) {
            return ['token' => $token['token'], 'expires_time' => $token['params']['exp']];
        } else {
            throw new ApiException('Đăng nhập thất bại');
        }
    }

    /**
     * Chuyển đổi đăng nhập
     * @param $user
     * @param $from
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function switchAccount($user, $from)
    {
        if ($from === 'h5') {
            $where = [['phone', '=', $user['phone']], ['user_type', '<>', 'h5'], ['is_del', '=', 0]];
            $login_type = 'wechat';
        } else {
            //Truy vấn cơ sở dữ liệu
            $where = [['account|phone', '=', $user['phone']], ['user_type', '=', 'h5'], ['is_del', '=', 0]];
            $login_type = 'h5';
        }
        $switch_user = $this->dao->getOne($where);
        if (!$switch_user) {
            return app('json')->fail('Người dùng không tồn tại, không thể chuyển đổi');
        }
        if (!$switch_user->status) {
            return app('json')->fail('Bạn đã bị cấm đăng nhập, vui lòng liên hệ quản trị viên');
        }
        $edit_data = ['login_type' => $login_type];
        if (!$this->dao->update($switch_user['uid'], $edit_data, 'uid')) {
            throw new ApiException('Lỗi khi sửa loại đăng nhập của người dùng mới');
        }
        $token = $this->createToken((int)$switch_user['uid'], 'api');
        if ($token) {
            return ['token' => $token['token'], 'expires_time' => $token['params']['exp']];
        } else {
            throw new ApiException('Đăng nhập thất bại');
        }
    }

    /**
     * Liên kết số điện thoại (âm thầm, chưa ghi vào thông tin người dùng)
     * @param $phone
     * @param string $key
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bindind_phone($phone, string $key = '')
    {
        if (!$key) {
            throw new ApiException('Vui lòng tải lại trang hoặc ủy quyền lại');
        }
        [$openid, $wechatInfo, $spreadId, $agent_id, $login_type, $userType] = $createData = CacheService::get($key);
        if (!$createData) {
            throw new ApiException('Vui lòng tải lại trang hoặc ủy quyền lại');
        }
        $wechatInfo['phone'] = $phone;
        /** @var WechatUserServices $wechatUser */
        $wechatUser = app()->make(WechatUserServices::class);
        //Cập nhật thông tin người dùng
        $user = $wechatUser->wechatOauthAfter([$openid, $wechatInfo, $spreadId, $agent_id, $login_type, $userType]);
        $token = $this->createToken((int)$user['uid'], 'api');
        if ($token) {
            return [
                'token' => $token['token'],
                'userInfo' => $user,
                'expires_time' => $token['params']['exp'],
            ];
        } else
            return app('json')->fail('Đăng nhập thất bại');
    }

    /**
     * Người dùng liên kết số điện thoại
     * @param int $uid
     * @param $phone
     * @param $step
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function userBindindPhone(int $uid, $phone, $step)
    {
        $userInfo = $this->dao->get($uid);
        if (!$userInfo) {
            throw new ApiException('Người dùng không tồn tại');
        }
        if ($this->dao->getOne([['phone', '=', $phone], ['user_type', '<>', 'h5'], ['is_del', '=', 0]])) {
            throw new ApiException('Số điện thoại này đã được liên kết, không thể liên kết nhiều lần');
        }
        if ($userInfo->phone) {
            throw new ApiException('Tài khoản của bạn đã liên kết số điện thoại');
        }
        $data = [];
        if ($this->dao->getOne(['account' => $phone, 'phone' => $phone, 'user_type' => 'h5', 'is_del' => 0])) {
            if (!$step) return ['msg' => 'Đã có tài khoản H5, có muốn liên kết vào tài khoản này không', 'data' => ['is_bind' => 1]];
        } else {
            $data['account'] = $phone;
        }
        $data['phone'] = $phone;
        if ($this->dao->update($userInfo['uid'], $data, 'uid') || $userInfo->phone == $phone)
            return ['msg' => 'Liên kết thành công', 'data' => []];
        else
            throw new ApiException('Liên kết thất bại');
    }

    /**
     * Người dùng liên kết số điện thoại
     * @param int $uid
     * @param $phone
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateBindindPhone(int $uid, $phone)
    {
        $userInfo = $this->dao->get(['uid' => $uid, 'is_del' => 0]);
        if (!$userInfo) {
            throw new ApiException('Người dùng không tồn tại');
        }
        if ($userInfo->phone == $phone) {
            throw new ApiException('Số điện thoại mới trùng với số hiện tại, không cần thay đổi');
        }
        if ($this->dao->getOne([['phone', '=', $phone], ['is_del', '=', 0]])) {
            throw new ApiException('Số điện thoại này đã được đăng ký');
        }
        $data = [];
        $data['phone'] = $phone;
        $data['account'] = $phone;
        if ($this->dao->update($userInfo['uid'], $data, 'uid'))
            return ['msg' => 'Sửa thành công', 'data' => []];
        else
            throw new ApiException('Sửa thất bại');
    }

    /**
     * Đăng ký đăng nhập từ xa
     * @param string $out_token
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/21
     */
    public function remoteRegister(string $out_token = '')
    {
        $info = JWT::jsonDecode(JWT::urlsafeB64Decode($out_token));
        $userInfo = $this->dao->get(['uid' => $info->uid]);
        $data = [];
        if (!$userInfo) {
            $data['uid'] = $info->uid;
            $data['account'] = $info->phone != '' ? $info->phone : 'out_' . $info->uid;
            $data['phone'] = $info->phone;
            $data['pwd'] = md5('123456');
            $data['real_name'] = $info->nickname;
            $data['birthday'] = 0;
            $data['card_id'] = '';
            $data['mark'] = '';
            $data['addres'] = '';
            $data['user_type'] = 'h5';
            $data['add_time'] = time();
            $data['add_ip'] = app('request')->ip();
            $data['last_time'] = time();
            $data['last_ip'] = app('request')->ip();
            $data['nickname'] = $info->nickname;
            $data['avatar'] = $info->avatar;
            $data['city'] = '';
            $data['language'] = '';
            $data['province'] = '';
            $data['country'] = '';
            $data['status'] = 1;
            $data['now_money'] = $info->now_money;
            $data['integral'] = $info->integral;
            $data['exp'] = $info->exp;
            $this->dao->save($data);
        } else {
            $data['nickname'] = $info->nickname;
            $data['avatar'] = $info->avatar;
            $data['now_money'] = $info->now_money;
            $data['integral'] = $info->integral;
            $data['exp'] = $info->exp;
            $this->dao->update($info->uid, $data);
        }
        $token = $this->createToken((int)$info->uid, 'api');
        if ($token) {
            return ['token' => $token['token'], 'expires_time' => $token['params']['exp']];
        } else {
            throw new ApiException('Đăng nhập thất bại');
        }
    }
}
