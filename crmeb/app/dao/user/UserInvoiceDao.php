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

namespace app\dao\user;


use app\dao\BaseDao;
use app\model\user\UserInvoice;

/**
 * Class UserInvoiceDao
 * @package app\dao\user
 */
class UserInvoiceDao extends BaseDao
{

    protected function setModel(): string
    {
        return UserInvoice::class;
    }

    /**
     * @param array $where
     * @param string $field
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, string $field = '*', int $page, int $limit)
    {
        return $this->search($where)->field($field)->page($page, $limit)->order('is_default desc,id desc')->select()->toArray();
    }

    /**
     * Thiết lập mặc định (cá nhân thường|doanh nghiệp thường|doanh nghiệp chuyên dụng)
     * @param int $uid
     * @param int $id
     * @param $header_type
     * @param $type
     * @return bool
     */
    public function setDefault(int $uid, int $id, $header_type, $type)
    {
        if (false === $this->getModel()->where('uid', $uid)->where('header_type', $header_type)->where('type', $type)->update(['is_default' => 0])) {
            return false;
        }
        if (false === $this->getModel()->where('id', $id)->update(['is_default' => 1])) {
            return false;
        }
        return true;
    }
}
