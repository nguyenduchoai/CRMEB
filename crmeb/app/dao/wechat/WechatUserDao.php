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

namespace app\dao\wechat;

use app\dao\BaseDao;
use app\model\wechat\WechatUser;

/**
 *
 * Class UserWechatUserDao
 * @package app\dao\user
 */
class WechatUserDao extends BaseDao
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return WechatUser::class;
    }

    /**
     * Lấy dữ liệu bảng wechat_user
     * @param array $where
     * @param string $field
     * @param string $order
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, string $field = '*', string $order = 'id desc', int $page = 0, int $limit = 0)
    {
        return $this->getModel()->where($where)->field($field)->order($order)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->select()->toArray();
    }

    /**
     * Lấy dữ liệu thống kê người dùng WeChat
     * @param $time
     * @param $where
     * @param $timeType
     * @param $key
     * @return mixed
     */
    public function getWechatTrendData($time, $where, $timeType, $key)
    {
        return $this->getModel()->where('user_type', 'wechat')
            ->where($where)
            ->where(function ($query) use ($time) {
                if ($time[0] == $time[1]) {
                    $query->whereDay('add_time', $time[0]);
                } else {
                    $time[1] = date('Y/m/d', strtotime($time[1]) + 86400);
                    $query->whereTime('add_time', 'between', $time);
                }
            })->field("FROM_UNIXTIME(add_time,'$timeType') as days,count(uid) as $key")
            ->group('days')->select()->toArray();
    }

    /**
     * Toàn bộ người dùng theo khu vực
     * @param $time
     * @param $userType
     * @return mixed
     */
    public function getRegionAll($time, $userType)
    {
        return $this->getModel()->when($userType != '', function ($query) use ($userType) {
            $query->where('user_type', $userType);
        })->where(function ($query) use ($time) {
            $query->whereTime('add_time', '<', strtotime($time[1]) + 86400)->whereOr('add_time', NULL);
        })->field('count(uid) as allNum,province')
            ->group('province')->select()->toArray();
    }

    /**
     * Người dùng mới theo khu vực
     * @param $time
     * @param $userType
     * @return mixed
     */
    public function getRegionNew($time, $userType)
    {
        return $this->getModel()->when($userType != '', function ($query) use ($userType) {
            $query->where('user_type', $userType);
        })->where(function ($query) use ($time) {
            if ($time[0] == $time[1]) {
                $query->whereDay('add_time', $time[0]);
            } else {
                $time[1] = date('Y/m/d', strtotime($time[1]) + 86400);
                $query->whereTime('add_time', 'between', $time);
            }
        })->field('count(uid) as newNum,province')
            ->group('province')->select()->toArray();
    }

    /**
     * Lấy giới tính người dùng
     * @param $time
     * @param $userType
     * @return mixed
     */
    public function getSex($time, $userType)
    {
        return $this->getModel()->when($userType != '', function ($query) use ($userType) {
            $query->where('user_type', $userType);
        })->where(function ($query) use ($time) {
            if ($time[0] == $time[1]) {
                $query->whereDay('add_time', $time[0]);
            } else {
                $time[1] = date('Y/m/d', strtotime($time[1]) + 86400);
                $query->whereTime('add_time', 'between', $time);
            }
        })->field('count(uid) as value,sex as name')
            ->group('sex')->select()->toArray();
    }

    /**
     * Lấy openid của OA WeChat hoặc Mini Program
     * @param int $uid
     * @return mixed
     */
    public function getWechatOpenid(int $uid, string $userType = 'wechat')
    {
        return $this->getModel()->where('uid', $uid)->where('user_type', $userType)->value('openid');
    }
}
