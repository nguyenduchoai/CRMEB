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

namespace app\dao\system\admin;

use app\dao\BaseDao;
use app\model\system\admin\SystemAdmin;

/**
 * Class SystemAdminDao
 * @package app\dao\system\admin
 */
class SystemAdminDao extends BaseDao
{
    protected function setModel(): string
    {
        return SystemAdmin::class;
    }

    /**
     * Lấy danh sách quản trị viên
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getList(array $where, int $page, int $limit)
    {
        return $this->search($where)->page($page, $limit)->select()->toArray();
    }

    /**
     * Tìm thông tin quản trị viên theo tên quản trị viên
     * @param string $account
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function accountByAdmin(string $account)
    {
        return $this->search(['account' => $account, 'is_del' => 0])->find();
    }

    /**
     * Tài khoản hiện tại có khả dụng hay không
     * @param string $account
     * @param int $id
     * @return int
     */
    public function isAccountUsable(string $account, int $id)
    {
        return $this->search(['account' => $account, 'is_del' => 0])->where('id', '<>', $id)->count();
    }

    /**
     * Lấy adminid
     * @param int $level
     * @return array
     */
    public function getAdminIds(int $level)
    {
        return $this->getModel()->where('level', '>=', $level)->column('id', 'id');
    }

    /**
     * Lấy tên và id quản trị viên có cấp thấp hơn
     * @param string $field
     * @param int $level
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOrdAdmin(string $field = 'real_name,id', int $level = 0)
    {
        return $this->getModel()->where('level', '>=', $level)->field($field)->select()->toArray();
    }

    /**
     * Lấy dữ liệu quản trị viên theo điều kiện
     * @param $where
     * @return mixed
     */
    public function getInfo($where)
    {
        return $this->getModel()->where($where)->find();
    }

    /**
     * Kiểm tra có quản trị viên nào đang dùng vai trò này không
     * @param int $id
     * @return bool
     */
    public function checkRoleUse(int $id): bool
    {
        return (bool)$this->getModel()->where('level', '<>', 0)->where('is_del', 0)->whereFindInSet('roles', $id)->count();
    }
}
