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
use app\model\user\UserLevel;

/**
 *
 * Class UserLevelDao
 * @package app\dao\user
 */
class UserLevelDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return UserLevel::class;
    }

    /**
     * Lấy thông tin chi tiết hạng thành viên của người dùng theo uid
     * @param int $uid
     * @param string $field
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserLevel(int $uid, string $field = '*')
    {
        return $this->getModel()->where('uid', $uid)->where('is_del', 0)->where('status', 1)->field($field)->with(['levelInfo'])->order('grade desc,add_time desc')->find();
    }

    /**
     * Lấy chiết khấu theo hạng người dùng
     * @param int $uid
     * @return mixed
     */
    public function getDiscount(int $uid)
    {
        $level = $this->getModel()->where(['uid' => $uid, 'is_del' => 0, 'status' => 1])->with(['levelInfo' => function ($query) {
            $query->field('id,discount')->bind(['discount_num' => 'discount']);
        }])->order('id desc')->find();
        return $level ? $level->toArray()['discount_num'] : NULL;
    }
}
