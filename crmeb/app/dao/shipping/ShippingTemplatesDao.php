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

namespace app\dao\shipping;

use app\dao\BaseDao;
use app\model\shipping\ShippingTemplates;

/**
 *
 * Class ShippingTemplatesDao
 * @package app\dao\shipping
 */
class ShippingTemplatesDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return ShippingTemplates::class;
    }

    /**
     * Lấy danh sách mẫu để chọn
     * @return array
     */
    public function getSelectList()
    {
        return $this->search()->order('sort DESC,id DESC')->column('id,name');
    }

    /**
     * Lấy
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getShippingList(array $where, int $page, int $limit)
    {
        return $this->search($where)->order('sort DESC,id DESC')->page($page, $limit)->select()->toArray();
    }

    /**
     * Thêm dữ liệu và trả về id khóa chính
     * @param array $data
     * @return int|string
     */
    public function insertGetId(array $data)
    {
        return $this->getModel()->insertGetId($data);
    }

    /**
     * Lấy dữ liệu mẫu phí vận chuyển theo điều kiện chỉ định
     * @param array $where
     * @param string $field
     * @param string $key
     * @return array
     */
    public function getShippingColumn(array $where, string $field, string $key)
    {
        return $this->search($where)->column($field, $key);
    }
}
