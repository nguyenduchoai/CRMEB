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

namespace app\dao\user;


use app\dao\BaseDao;
use app\model\user\MemberShip;

class MemberShipDao extends BaseDao
{
    /** Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        // TODO: Implement setModel() method.
        return MemberShip::class;
    }

    /**API lấy loại thẻ thành viên ở trang quản trị
     * @param array $where
     * @param int $page
     * @param int $limit
     * @param array $field
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSearchList(array $where, int $page = 0, int $limit = 0, array $field = ['*'])
    {
        return $this->search($where)->order('sort desc,id desc')
            ->field($field)
            ->page($page, $limit)
            ->select();

    }

    /**API lấy loại thành viên
     * @return mixed
     */
    public function getApiList(array $where)
    {
        return $this->search()->where($where)->order('sort desc,id DESC')->select()->toArray();
    }


}
