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

namespace app\dao\shipping;

use app\dao\BaseDao;
use app\model\other\Express;

/**
 * Thông tin vận chuyển
 * Class ExpressDao
 * @package app\dao\other
 */
class ExpressDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return Express::class;
    }

    /**
     * Lấy danh sách vận chuyển
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getExpressList(array $where, string $field, int $page, int $limit)
    {
        return $this->search($where)->field($field)->order('sort DESC,is_show DESC,id ASC')
            ->when($page > 0 && $limit > 0, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })->select()->toArray();
    }

    /**
     * Lấy thông tin vận chuyển theo điều kiện chỉ định, trả về dạng mảng
     * @param array $where
     * @param string $field
     * @param string $key
     * @return array
     */
    public function getExpress(array $where, string $field, string $key)
    {
        return $this->search($where)->order('id DESC')->column($field, $key);
    }

    /**
     * Lấy một thông tin theo code
     * @param string $code
     * @param string $field
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getExpressByCode(string $code, string $field = '*')
    {
        return $this->getModel()->field($field)->where('code', $code)->find();
    }
}
