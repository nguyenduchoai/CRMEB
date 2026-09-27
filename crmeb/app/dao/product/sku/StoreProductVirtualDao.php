<?php


namespace app\dao\product\sku;


use app\dao\BaseDao;
use app\model\product\sku\StoreProductVirtual;

class StoreProductVirtualDao extends BaseDao
{
    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreProductVirtual::class;
    }
}