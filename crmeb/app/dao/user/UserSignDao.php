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

namespace app\dao\user;

use app\dao\BaseDao;
use app\model\user\UserSign;

/**
 *
 * Class UserSignDao
 * @package app\dao\user
 */
class UserSignDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return UserSign::class;
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, string $field, int $page, int $limit)
    {
        return $this->search($where)->field($field)->order('id desc')->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->select()->toArray();
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getListGroup(array $where, string $field, int $page, int $limit, string $group)
    {
        return $this->search($where)->field($field)->order('id desc')->group($group)->page($page, $limit)->select()->toArray();
    }

    /**
     * Lấy số lần điểm danh lũy kế theo tuần hoặc tháng
     * @param $type
     * @param $uid
     * @return int
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/1
     */
    public function getCumulativeDays($type, $uid)
    {
        return $this->getModel()->where('uid', $uid)->where(function ($query) use ($type) {
            if ($type == 1) {
                $query->whereWeek('add_time');
            } elseif($type == 0) {
                $query->whereMonth('add_time');
            }
        })->count();
    }

    /**
     * Lấy danh sách điểm danh của tuần này hoặc tháng này
     * @param $type
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/8
     */
    public function getUserSignList($type, $uid): array
    {
        return $this->getModel()->where('uid', $uid)->where(function ($query) use ($type) {
            if ($type == 1) {
                $query->whereWeek('add_time');
            } else {
                $query->whereMonth('add_time');
            }
        })->order('id asc')->select()->toArray();
    }
}
