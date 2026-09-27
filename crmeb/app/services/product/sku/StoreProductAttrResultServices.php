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

namespace app\services\product\sku;


use app\dao\product\sku\StoreProductAttrResultDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * Class StoreProductAttrResultService
 * @package app\services\product\sku
 * @method value(array $where, string $field)
 */
class StoreProductAttrResultServices extends BaseServices
{
    /**
     * StoreProductAttrResultServices constructor.
     * @param StoreProductAttrResultDao $dao
     */
    public function __construct(StoreProductAttrResultDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy thuộc tính, phân loại
     * @param array $where
     * @return mixed
     */
    public function getResult(array $where)
    {
        return json_decode($this->dao->value($where, 'result'), true);
    }

    /**
     * Xóa thuộc tính
     * @param int $id
     * @param int $type
     * @return bool
     */
    public function del(int $id, int $type)
    {
        return $this->dao->del($id, $type);
    }

    /**
     * Sửa thuộc tính
     * @param array $data
     * @param int $id
     * @param int $type
     */
    public function setResult(array $data, int $id, int $type)
    {
        $res = $this->dao->save(['product_id' => $id, 'result' => json_encode($data), 'change_time' => time(), 'type' => $type]);
        if (!$res) throw new AdminException(100006);
    }
}
