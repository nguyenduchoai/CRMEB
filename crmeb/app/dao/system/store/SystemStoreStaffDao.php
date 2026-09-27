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

namespace app\dao\system\store;


use app\dao\BaseDao;
use app\model\system\store\SystemStoreStaff;

/**
 * Nhân viên cửa hàng
 * Class SystemStoreStaffDao
 * @package app\dao\system\store
 */
class SystemStoreStaffDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return SystemStoreStaff::class;
    }

    /**
     * Lấy danh sách nhân viên cửa hàng
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStoreStaffList(array $where, int $page, int $limit)
    {
        return $this->search($where)->with(['store', 'user'])->page($page, $limit)->order('add_time DESC')->select()->toArray();
    }

}
