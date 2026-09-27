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

namespace app\services\product\product;


use app\dao\product\product\StoreDescriptionDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * Class StoreDescriptionService
 * @package app\services\product\product
 * @method value($where, ?string $field = null) Lấy trường
 */
class StoreDescriptionServices extends BaseServices
{
    /**
     * StoreDescriptionServices constructor.
     * @param StoreDescriptionDao $dao
     */
    public function __construct(StoreDescriptionDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy chi tiết sản phẩm
     * @param array $where
     * @return string
     */
    public function getDescription(array $where)
    {
        $info = $this->dao->getDescription($where);
        if ($info) return htmlspecialchars_decode($info->description);
        return '';
    }

    /**
     * Lưu chi tiết sản phẩm
     * @param int $id
     * @param string $description
     * @param int $type
     * @return bool
     */
    public function saveDescription(int $id, string $description, int $type = 0)
    {
        $description = htmlspecialchars($description);
        $info = $this->dao->count(['product_id' => $id, 'type' => $type]);
        if ($info) {
            $res = $this->dao->update(['product_id' => $id, 'type' => $type], ['description' => $description]);
        } else {
            $res = $this->dao->save(['product_id' => $id, 'description' => $description, 'type' => $type]);
        }
        if (!$res) throw new AdminException(400560);
    }

}
