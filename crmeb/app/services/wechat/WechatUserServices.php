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
use app\services\user\LoginServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\exceptions\AuthException;
use crmeb\services\app\WechatService;
use think\facade\Log;

/**
 *
 * Class WechatUserServices
 * @package app\services\wechat
 * @method delete($id, ?string $key = null)  Xóa
 * @method update($id, array $data, ?string $key = null) Cập nhật dữ liệu
 * @method getColumn(array $where, string $field, string $key = '') Lấy mảng của một trường
 * @method get($id, ?array $field = []) Dùng khóa chính để lấy một bản ghi
 * @method getOne(array $where, ?string $field = '*', array $with = []) Lấy một bản ghi
 * @method value(array $value, string $key) Lấy một dòng dữ liệu
 * @method getWechatTrendData($time, $where, $timeType, $key)
 * @method getWechatOpenid(int $uid, string $userType = 'wechat') Lấy openid của OA WeChat
 */
class WechatUserServices extends BaseServices
{

    /**
     * WechatUserServices constructor.
     * @param WechatUserDao $dao
     */
    public function __construct(WechatUserDao $dao)
    {
        $this->dao = $dao;
    }

    public function getColumnUser($user_ids, $column, $key, string $user_type = 'wechat')
    {
        return $this->dao->getColumn([['uid', 'IN', $user_ids], ['user_type', '=', $user_type]], $column, $key);
    }

    /**
     * Lấy một người dùng WeChat
     * @param array $where
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getWechatUserInfo(array $where, $field = '*')
    {
        return $this->dao->getOne($where, $field);
    }

    /**
     * Dùng uid để lấy openid WeChat
     * @param int $uid
     * @param string $userType
     * @return mixed
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/17
     */
    public function uidToOpenid(int $uid, string $userType = 'wechat')
    {
        return $this->dao->value(['uid' => $uid, 'user_type' => $userType], 'openid');
    }


    /**
     * TODO Dùng openid để lấy uid
     * @param $openid
     * @param string $openidType
     * @return mixed
     */
    public function openidToUid($openid, string $openidType = 'openid')
    {
        $uid = $this->dao->value([$openidType => $openid, 'is_del' => 0], 'uid');
        if (!$uid)
            throw new AdminException('Không tìm thấy uid tương ứng');
        return $uid;
    }

    /**
     * Người dùng bỏ theo dõi
     * @param $openid
     * @return bool
     */
    public function unSubscribe($openid)
    {
        if (!$this->dao->update($openid, ['subscribe' => 0, 'subscribe_time' => time()], 'openid'))
            throw new AdminException('Hủy theo dõi thất bại');
        return true;
    }

    /**
     * Người dùng đã tồn tại thì cập nhật, chưa tồn tại thì thêm mới
     * @param $openid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveUser($openid)
    {
        if ($this->getWechatUserInfo(['openid' => $openid, 'is_del' => 0])) {
            $this->updateUser($openid);
            return false;
        } else {
            $this->setNewUser($openid);
            return true;
        }
    }

    /**
     * Cập nhật thông tin người dùng
     * @param $openid
     * @return bool
     */
    public function updateUser($openid)
    {
        $userInfo = WechatService::getUserInfo($openid);
        $userInfo = is_object($userInfo) ? $userInfo->toArray() : $userInfo;
        if (isset($userInfo['nickname']) && $userInfo['nickname']) {
            $userInfo['nickname'] = filter_emoji($userInfo['nickname']);
        } else {
            mt_srand();
            $userInfo['nickname'] = 'wx' . rand(100000, 999999);
            $userInfo['avatar'] = sys_config('h5_avatar');
        }
        if (isset($userInfo['tagid_list'])) {
            $userInfo['tagid_list'] = implode(',', $userInfo['tagid_list']);
        }
        if (!$this->dao->update($openid, $userInfo, 'openid'))
            throw new AdminException('Cập nhật thất bại');
        return true;
    }

    /**
     * .Thêm người dùng mới
     * @param $openid
     * @return object
     */
    public function setNewUser($openid)
    {
        $userInfo = WechatService::getUserInfo($openid);
        if (!isset($userInfo['openid']))
            throw new AdminException('Vui lòng theo dõi OA WeChat');
        $userInfo = is_object($userInfo) ? $userInfo->toArray() : $userInfo;
        if (isset($userInfo['nickname']) && $userInfo['nickname']) {
            $userInfo['nickname'] = filter_emoji($userInfo['nickname']);
        } else {
            mt_srand();
            $userInfo['nickname'] = 'wx' . rand(100000, 999999);
            $userInfo['headimgurl'] = sys_config('h5_avatar');
        }
        if (isset($userInfo['tagid_list'])) {
            $userInfo['tagid_list'] = implode(',', $userInfo['tagid_list']);
        }
        $wechatInfo = [];
        $uid = 0;
        $userInfoData = null;
        if (isset($userInfo['unionid'])) {
            $wechatInfo = $this->getWechatUserInfo(['unionid' => $userInfo['unionid'], 'is_del' => 0]);
        }
        if (!$wechatInfo) {
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $userInfoData = $userServices->setUserInfo($userInfo);
            if (!$userInfoData) {
                throw new AdminException('Lưu trữ thông tin người dùng thất bại');
            }
            $uid = $userInfoData->uid;
        } else {
            $uid = $wechatInfo['uid'];
        }
        $userInfo['user_type'] = 'wechat';
        $userInfo['add_time'] = time();
        $userInfo['uid'] = $uid;
        if (!$this->dao->save($userInfo)) {
            throw new AdminException('Lưu trữ thông tin người dùng thất bại');
        }
        //TODO Giá trị trả về này cần hoàn thiện thêm
        return $userInfoData;
    }

    /**
     * Sau khi ủy quyền, lấy thông tin người dùng
     * @param $openid
     * @param $user_type
     * @return array|\think\Model|null
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/02/24
     */
    public function getAuthUserInfo($openid, $user_type)
    {
        $user = [];
        //Tương thích người dùng cũ
        $uids = $this->dao->getColumn(['unionid|openid' => $openid, 'is_del' => 0], 'uid,user_type', 'user_type');
        if ($uids) {
            $uid = $uids[$user_type]['uid'] ?? 0;
            if (!$uid) {
                $ids = array_column($uids, 'uid');
                $uid = $ids[0];
            }
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $user = $userServices->getUserInfo($uid);
        }
        return $user;
    }

    /**
     * Cập nhật thông tin người dùng WeChat
     * @param $event
     * @return bool
     */
    public function wechatUpdata($data)
    {
        [$uid, $userData] = $data;
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        if (!$userInfo = $userServices->getUserInfo((int)$uid)) {
            return false;
        }
        /** @var LoginServices $loginService */
        $loginService = app()->make(LoginServices::class);
        $loginService->updateUserInfo($userData, $userInfo);
        //Cập nhật thông tin người dùng
        /** @var WechatUserServices $wechatUser */
        $wechatUser = app()->make(WechatUserServices::class);

        $wechatUserInfo = [];
        if (isset($userData['nickname']) && $userData['nickname']) $wechatUserInfo['nickname'] = filter_emoji($userData['nickname'] ?? '');//Họ tên
        if (isset($userData['headimgurl']) && $userData['headimgurl']) $wechatUserInfo['headimgurl'] = $userData['headimgurl'] ?? '';//Ảnh đại diện
        if (isset($userData['sex']) && $userData['sex']) $wechatUserInfo['sex'] = $userData['gender'] ?? '';//Giới tính
        if (isset($userData['language']) && $userData['language']) $wechatUserInfo['language'] = $userData['language'] ?? '';//Ngôn ngữ
        if (isset($userData['city']) && $userData['city']) $wechatUserInfo['city'] = $userData['city'] ?? '';//Thành phố
        if (isset($userData['province']) && $userData['province']) $wechatUserInfo['province'] = $userData['province'] ?? '';//Tỉnh
        if (isset($userData['country']) && $userData['country']) $wechatUserInfo['country'] = $userData['country'] ?? '';//Quốc gia
        if (isset($wechatUserInfo['nickname']) || isset($wechatUserInfo['headimgurl'])) $wechatUserInfo['is_complete'] = 1;
        if ($wechatUserInfo) {
            if (isset($userData['openid']) && $userData['openid'] && false === $wechatUser->update(['uid' => $userInfo['uid'], 'openid' => $userData['openid']], $wechatUserInfo)) {
                throw new ApiException('Cập nhật thất bại');
            }
        }
        return true;
    }

    /**
     * Sau khi ủy quyền WeChat thành công
     * @param $data
     * @return array|mixed|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/02/24
     */
    public function wechatOauthAfter($data)
    {
        if (!$data) throw new ApiException('Lấy thông tin người dùng thất bại, vui lòng tải lại trang và thử lại');
        [$openid, $wechatInfo, $spreadId, $agent_id, $login_type, $userType] = $data;
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $spreadInfo = $userServices->getUserInfo((int)$spreadId);
        if (!$spreadInfo) {
            $spreadId = 0;
            $wechatInfo['staff_id'] = 0;
            $wechatInfo['agent_id'] = 0;
            $wechatInfo['division_id'] = 0;
        } else {
            $wechatInfo['staff_id'] = $spreadInfo['staff_id'];
            $wechatInfo['agent_id'] = $spreadInfo['agent_id'];
            $wechatInfo['division_id'] = $spreadInfo['division_id'];
        }
        if (isset($wechatInfo['subscribe_scene'])) {
            unset($wechatInfo['subscribe_scene']);
        }
        if (isset($wechatInfo['qr_scene'])) {
            unset($wechatInfo['qr_scene']);
        }
        if (isset($wechatInfo['qr_scene_str'])) {
            unset($wechatInfo['qr_scene_str']);
        }
        if ($login_type) {
            $wechatInfo['login_type'] = $login_type;
        }
        if (!isset($wechatInfo['nickname'])) {
            if (isset($wechatInfo['phone']) && $wechatInfo['phone']) {
                $wechatInfo['nickname'] = substr_replace($wechatInfo['phone'], '****', 3, 4);
            } else {
                $wechatInfo['nickname'] = 'wx' . rand(100000, 999999);
            }
        } else {
            $wechatInfo['is_complete'] = 1;
            $wechatInfo['nickname'] = filter_emoji($wechatInfo['nickname']);
        }

        $userInfo = [];
        $uid = 0;
        if (isset($wechatInfo['phone']) && $wechatInfo['phone']) {
            $userInfo = $userServices->getOne(['phone' => $wechatInfo['phone'], 'is_del' => 0]);
        }
        if (!$userInfo) {
            if (isset($wechatInfo['unionid']) && $wechatInfo['unionid']) {
                $uid = $this->dao->value(['unionid' => $wechatInfo['unionid'], 'is_del' => 0], 'uid');
                if ($uid) {
                    $userInfo = $userServices->getOne(['uid' => $uid, 'is_del' => 0]);
                }
            } else {
                $userInfo = $this->getAuthUserInfo($openid, $userType);
            }
        }
        if ($userInfo) {
            $uid = (int)$userInfo['uid'];
            $userInfo['new_user'] = 0;
        }
        $wechatInfo['user_type'] = $userType;
        //Bảng user tồn tại và bảng wechat_user cũng tồn tại
        if ($userInfo) {
            //Cập nhật bảng user và bảng wechat_user
            //Kiểm tra loại người dùng này có tồn tại trong wechatUser không
            $wechatUser = $this->dao->getOne(['uid' => $uid, 'user_type' => $userType, 'is_del' => 0]);
            //Khi openid lấy được khác với openid đăng nhập truyền vào, không cập nhật thông tin người dùng
            if ($wechatUser && $wechatUser['openid'] != $wechatInfo['openid']) {
                return $userInfo;
            }
            /** @var LoginServices $loginService */
            $loginService = app()->make(LoginServices::class);
            $this->transaction(function () use ($loginService, $wechatInfo, $userInfo, $uid, $userType, $spreadId, $wechatUser, $agent_id) {
                if ($agent_id) {
                    $wechatInfo['code'] = $agent_id;
                    $wechatInfo['is_staff'] = 1;
                } else {
                    $wechatInfo['code'] = $spreadId;
                }
                $loginService->updateUserInfo($wechatInfo, $userInfo);
                if ($wechatUser) {
                    if (!$this->dao->update($wechatUser['id'], $wechatInfo, 'id')) {
                        throw new ApiException('Sửa thất bại');
                    }
                } else {
                    $wechatInfo['uid'] = $uid;
                    if (!$this->dao->save($wechatInfo)) {
                        throw new ApiException('Sửa thất bại');
                    }
                }
            });
        } else {
            //Bảng user không có người dùng, bảng wechat_user không có người dùng thì tạo người dùng mới
            //Không tồn tại thì tạo người dùng
            $userInfo = $this->transaction(function () use ($userServices, $wechatInfo, $spreadId, $userType) {
                $userInfo = $userServices->setUserInfo($wechatInfo, (int)$spreadId, $userType);
                if (!$userInfo) {
                    throw new AuthException('Thêm người dùng thất bại');
                }
                $wechatInfo['uid'] = $userInfo->uid;
                $wechatInfo['add_time'] = $userInfo->add_time;
                if (!$this->dao->save($wechatInfo)) {
                    throw new AuthException('Thêm người dùng thất bại');
                }
                $userInfo['new_user'] = (int)sys_config('get_avatar', 0);
                return $userInfo;
            });
        }
        return $userInfo;
    }

    /**
     * Cập nhật thông tin người dùng (đồng bộ)
     * @param array $openids
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function syncWechatUser(array $openids)
    {
        if (!$openids) {
            return [];
        }
        $wechatUser = $this->dao->getList([['openid', 'in', $openids]]);
        $noBeOpenids = $openids;
        if ($wechatUser) {
            $beOpenids = array_column($wechatUser, 'openid');
            $noBeOpenids = array_diff($openids, $beOpenids);
//            $beWechatUserInfo = WechatService::getUserInfo($beOpenids);
            if ($beOpenids) {
                $data = [];
                foreach ($beOpenids as $openid) {
                    try {
                        $info = WechatService::getUserInfo($openid);
                        $info = is_object($info) ? $info->toArray() : $info;
                    } catch (\Throwable $e) {
                        $info = [];
                    }
                    if (!$info) continue;
                    $data['subscribe'] = $info['subscribe'] ?? 1;
                    if ($info['subscribe'] == 1) {
                        $data['unionid'] = $info['unionid'] ?? '';
                        $data['nickname'] = $info['nickname'] ?? '';
                        $data['sex'] = $info['sex'] ?? 0;
                        $data['language'] = $info['language'] ?? '';
                        $data['city'] = $info['city'] ?? '';
                        $data['province'] = $info['province'] ?? '';
                        $data['country'] = $info['country'] ?? '';
                        $data['headimgurl'] = $info['headimgurl'] ?? '';
                        $data['subscribe_time'] = $info['subscribe_time'] ?? '';
                        $data['groupid'] = $info['groupid'] ?? 0;
                        $data['remark'] = $info['remark'] ?? '';
                        $data['tagid_list'] = isset($info['tagid_list']) && $info['tagid_list'] ? implode(',', $info['tagid_list']) : '';
                    }
                    $this->dao->update(['openid' => $info['openid']], $data);
                }
            }
        }
        return $noBeOpenids;
    }

    /**
     * Người dùng theo dõi
     * @param $openid
     * @return bool
     */
    public function subscribe($openid): bool
    {
        if (!$this->dao->update($openid, ['subscribe' => 1, 'subscribe_time' => time()], 'openid'))
            throw new AdminException('Người dùng theo dõi thất bại');
        return true;
    }
}
