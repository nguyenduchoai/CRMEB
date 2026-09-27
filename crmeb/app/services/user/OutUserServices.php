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

use app\dao\user\UserDao;
use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\agent\AgentLevelServices;
use app\services\BaseServices;
use app\services\system\SystemUserLevelServices;
use crmeb\exceptions\ApiException;

/**
 *
 * Class OutUserServices
 * @package app\services\user
 */
class OutUserServices extends BaseServices
{

    /**
     * UserServices constructor.
     * @param UserDao $dao
     */
    public function __construct(UserDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Danh sách người dùng
     * @param array $where
     * @return array
     */
    public function getUserList(array $where): array
    {
        /** @var UserWechatuserServices $userWechatUser */
        $userWechatUser = app()->make(UserWechatuserServices::class);
        $fields = 'u.uid, u.real_name, u.mark, u.nickname, u.avatar, u.phone, u.now_money, u.brokerage_price, u.integral, u.exp, u.sign_num, u.user_type, 
        u.status, u.level, u.agent_level, u.spread_open, u.spread_uid, u.spread_time, u.user_type, u.is_promoter, u.pay_count, u.is_ever_level, u.is_money_level, 
        u.overdue_time, u.add_time';
        [$list, $count] = $userWechatUser->getWhereUserList($where, $fields);
        if ($list) {
            $uids = array_column($list, 'uid');
            $levelName = app()->make(SystemUserLevelServices::class)->getUsersLevel(array_unique(array_column($list, 'level')));
            $userLevel = app()->make(UserLevelServices::class)->getUsersLevelInfo($uids);
            $spreadNames = $this->dao->getColumn([['uid', 'in', array_unique(array_column($list, 'spread_uid'))]], 'nickname', 'uid');
            foreach ($list as &$item) {
                $item['spread_uid_nickname'] = $item['spread_uid'] ? ($spreadNames[$item['spread_uid']] ?? '') . '/' . $item['spread_uid'] : '';
                //Loại người dùng
                if ($item['user_type'] == 'routine') {
                    $item['user_type'] = 'Mini Program';
                } else if ($item['user_type'] == 'wechat') {
                    $item['user_type'] = 'OA WeChat';
                } else if ($item['user_type'] == 'h5') {
                    $item['user_type'] = 'H5';
                } else if ($item['user_type'] == 'pc') {
                    $item['user_type'] = 'PC';
                } else if ($item['user_type'] == 'app' || $item['user_type'] == 'apple') {
                    $item['user_type'] = 'APP';
                } else $item['user_type'] = 'Khác';

                //Hạng người dùng
                $item['level_name'] = "";
                $levelInfo = $userLevel[$item['uid']] ?? null;
                if ($levelInfo) {
                    if ($levelInfo['is_forever'] || time() < $levelInfo['valid_time']) {
                        $item['level_name'] = $levelName[$item['level']] ?? '';
                    }
                }
            }
        }
        return compact('list', 'count');
    }

    /**
     * Lấy chi tiết người dùng
     * @param $uid
     * @return mixed
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/06/20
     */
    public function userInfo($uid)
    {
        $userType = ['h5' => 'H5', 'wechat' => 'OA WeChat', 'routine' => 'Mini Program', 'app' => 'APP', 'pc' => 'PC'];
        $fields = ['uid', 'real_name', 'mark', 'nickname', 'avatar', 'phone', 'now_money', 'brokerage_price', 'integral', 'exp', 'sign_num', 'user_type', 'status', 'level',
            'agent_level', 'spread_open', 'spread_uid', 'spread_time', 'user_type', 'is_promoter', 'pay_count', 'is_ever_level', 'is_money_level', 'overdue_time', 'add_time'];
        $data = app()->make(UserServices::class)->get($uid, $fields);
        $data['user_type'] = $userType[$data['user_type']];
        $data['status'] = $data['status'] ? 'Bình thường' : 'Vô hiệu hóa';
        $data['level'] = app()->make(SystemUserLevelServices::class)->value($data['level'], 'name') ?? 'Không có';
        $data['agent_level'] = app()->make(AgentLevelServices::class)->value($data['agent_level'], 'name') ?? 'Không có';
        $data['spread_open'] = $data['spread_open'] ? 'Bật tiếp thị liên kết' : 'Tắt tiếp thị liên kết';
        $data['spread_name'] = app()->make(UserServices::class)->value($data['spread_uid'], 'nickname') ?? 'Không có';
        $data['spread_time'] = date('Y-m-d H:i:s', $data['spread_time']);
        $data['add_time'] = date('Y-m-d H:i:s', $data['add_time']);
        return $data;
    }

    /**
     * Thêm/sửa người dùng
     * @param int $uid
     * @param array $data
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveUser(int $uid, array $data): int
    {
        if (empty($data['real_name'])) {
            throw new ApiException(400760);
        }
        if (empty($data['phone'])) {
            throw new ApiException(400132);
        }

        if (!check_phone($data['phone'])) {
            throw new ApiException(400252);
        }
        if ($uid < 1 && $this->count(['phone' => $data['phone'], 'is_del' => 0])) {
            throw new ApiException(400314);
        }

        if ($data['pwd']) {
            $data['pwd'] = md5($data['pwd']);
        } else {
            if ($uid < 1) {
                $data['pwd'] = md5(123456);
            } else {
                unset($data['pwd']);
            }
        }
        return $this->transaction(function () use ($uid, $data) {
            if ($uid) {
                $userInfo = $this->dao->update($uid, $data);
            } else {
                if (trim($data['real_name']) != '') {
                    $data['nickname'] = $data['real_name'];
                } else {
                    $data['nickname'] = substr_replace($data['phone'], '****', 3, 4);
                }

                $data['avatar'] = sys_config('h5_avatar');
                $data['user_type'] = 'h5';
                $data['add_time'] = time();
                $userInfo = $this->dao->save($data);
                $uid = (int)$userInfo->uid;
            }
            if (!$userInfo) {
                throw new ApiException(100006);
            }

            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);

            $level = (int)$data['level'];
            if ($level) {
                if (!$userServices->saveGiveLevel($uid, (int)$data['level'])) {
                    throw new ApiException(400219);
                }
            }
            return $uid;
        });
    }

    /**
     * Tặng (điểm thưởng/số dư/thành viên trả phí)
     * @param int $id
     * @param array $data
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function otherGive(int $id, array $data): bool
    {
        return $this->transaction(function () use ($id, $data) {
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);

            $days = (int)$data['days'];
            $coupon = (int)$data['coupon'];
            unset($data['days'], $data['coupon']);
            if ($days > 0) {
                $userServices->saveGiveLevelTime($id, $days);
            }

            if ($coupon) {
                /** @var StoreCouponIssueServices $issueService */
                $issueService = app()->make(StoreCouponIssueServices::class);
                $coupon = $issueService->get($data['id']);
                if (!$coupon) {
                    throw new ApiException(100026);
                } else {
                    $coupon = $coupon->toArray();
                }
                $issueService->setCoupon($coupon, [$id]);
            }

            $data['adminId'] = 0;
            $data['is_other'] = true;
            $data['money'] = (string)$data['money'];
            $data['integration'] = (string)$data['integration'];
            return $userServices->updateInfo($id, $data);
        });
    }

    /**
     * Sửa dữ liệu người dùng
     * @param $uid
     * @param $value
     * @param $type
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function changeUserData($uid, $value, $type)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $res = $userServices->update($uid, [$type => $value]);
        if ($res) throw new ApiException('Sửa thất bại');
        return true;
    }
}