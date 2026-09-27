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

namespace app\dao\system\config;

use app\dao\BaseDao;
use app\model\system\config\SystemGroupData;

/**
 * Dữ liệu tổ hợp
 * Class SystemGroupDataDao
 * @package app\dao\system\config
 */
class SystemGroupDataDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return SystemGroupData::class;
    }

    /**
     * Lấy danh sách dữ liệu tổ hợp
     * @param array $where
     * @param int $page
     * @param int $limit
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGroupDataList(array $where, int $page, int $limit)
    {
        return $this->search($where)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->order('sort desc,id DESC')->select()->toArray();
    }

    /**
     * Lấy dữ liệu tổ hợp theo một gid
     * @param int $gid
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGroupDate(int $gid, int $limit = 0)
    {
        return $this->search(['gid' => $gid, 'status' => 1])->when($limit, function ($query) use ($limit) {
            $query->limit($limit);
        })->field('value,id')->order('sort DESC,id DESC')->select()->toArray();
    }

    /**
     * Lấy dữ liệu flash sale theo id
     * @param array $ids
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function idByGroupList(array $ids, string $field)
    {
        return $this->getModel()->whereIn('id', $ids)->field($field)->select()->toArray();
    }

    /**
     * Xóa dữ liệu tổ hợp theo gid
     * @param int $gid
     * @return bool
     */
    public function delGroupDate(int $gid)
    {
        return $this->getModel()->where('gid', $gid)->delete();
    }

    /**
     * Lưu theo lô
     * @param array $data
     * @return mixed|\think\Collection
     * @throws \Exception
     */
    public function saveAll(array $data)
    {
        return $this->getModel()->saveAll($data);
    }
}
