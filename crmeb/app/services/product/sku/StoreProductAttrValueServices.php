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


use app\dao\product\sku\StoreProductAttrValueDao;
use app\models\store\StoreProductAttrValue;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use app\services\product\product\StoreProductServices;
use crmeb\services\CacheService;
use crmeb\services\workerman\ChannelService;

/**
 * Class StoreProductAttrValueService
 * @package app\services\product\sku
 * @method getProductAttrValue(array $where)
 * @method value(array $where, string $field = '') Lấy dữ liệu theo một khóa
 * @method decStockIncSales(array $where, int $num, string $stock = 'stock', string $sales = 'sales') Giảm tồn kho, tăng lượt bán
 * @method count(array $where) Lấy số lượng theo điều kiện chỉ định
 */
class StoreProductAttrValueServices extends BaseServices
{
    /**
     * StoreProductAttrValueServices constructor.
     * @param StoreProductAttrValueDao $dao
     */
    public function __construct(StoreProductAttrValueDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy phân loại đơn
     * @param array $where
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOne(array $where)
    {
        return $this->dao->getOne($where);
    }

    /**
     * Lấy dữ liệu theo điều kiện chỉ định, trả về dạng mảng
     * @param array $where
     * @param string $field
     * @param string $key
     * @return array
     */
    public function getColumn(array $where, string $field = '*', string $key = 'suk')
    {
        return $this->dao->getColumn($where, $field, $key);
    }

    /**
     * Xóa một dòng dữ liệu
     * @param int $id
     * @param int $type
     */
    public function del(int $id, int $type)
    {
        $this->dao->del($id, $type);
    }

    /**
     * Lưu theo lô
     * @param array $data
     */
    public function saveAll(array $data)
    {
        $res = $this->dao->saveAll($data);
        if (!$res) throw new AdminException(100006);
        return $res;
    }

    /**
     * Lấy SKU
     * @param array $where
     * @return array
     */
    public function getSkuArray(array $where)
    {
        return $this->dao->getColumn($where, 'id,bar_code,bar_code_number,cost,price,vip_price,ot_price,stock,image as pic,weight,volume,brokerage,brokerage_two,quota,unique', 'suk');
    }

    /**
     * Bảng xếp hạng giao dịch
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function purchaseRanking()
    {
        $dlist = $this->dao->attrValue();
        /** @var StoreProductServices $proServices */
        $proServices = app()->make(StoreProductServices::class);
        $slist = $proServices->getProductLimit(['is_del' => 0], $limit = 20, 'id as product_id,store_name,sales * price as val');
        $data = array_merge($dlist, $slist);
        $last_names = array_column($data, 'val');
        array_multisort($last_names, SORT_DESC, $data);
        $list = array_splice($data, 0, 20);
        return $list;
    }

    /**Lấy số lượng thuộc tính của sản phẩm
     * @param $product_id
     * @param $unique
     * @param $type
     * @return int
     */
    public function getAttrvalueCount($product_id, $unique, $type)
    {
        return $this->dao->count(['product_id' => $product_id, 'unique' => $unique, 'type' => $type]);
    }

    /**
     * Lấy tồn kho theo giá trị duy nhất
     * @param string $unique
     * @return int
     */
    public function uniqueByStock(string $unique)
    {
        if (!$unique) return 0;
        return $this->dao->uniqueByStock($unique);
    }

    /**
     * Giảm lượt bán, tăng tồn kho
     * @param $productId
     * @param $unique
     * @param $num
     * @param int $type
     * @return mixed
     */
    public function decProductAttrStock($productId, $unique, $num, $type = 0)
    {
        $res = $this->dao->decStockIncSales([
            'product_id' => $productId,
            'unique' => $unique,
            'type' => $type
        ], $num);
        if ($res) {
            $this->workSendStock($productId, $unique, $type);
        }
        return $res;
    }

    /**
     * Giảm lượt bán, tăng tồn kho
     * @param $productId
     * @param $unique
     * @param $num
     * @return bool
     */
    public function incProductAttrStock(int $productId, string $unique, int $num, int $type = 0)
    {
        return $this->dao->incStockDecSales(['unique' => $unique, 'product_id' => $productId, 'type' => $type], $num);
    }

    /**
     * Nhắc nhở tin nhắn cảnh báo tồn kho
     * @param int $productId
     * @param string $unique
     * @param int $type
     */
    public function workSendStock(int $productId, string $unique, int $type)
    {
        $stock = $this->dao->value([
            'product_id' => $productId,
            'unique' => $unique,
            'type' => $type
        ], 'stock');
        $replenishment_num = sys_config('store_stock') ?? 0;//Ngưỡng cảnh báo tồn kho
        if ($replenishment_num >= $stock) {
            try {
                ChannelService::instance()->send('STORE_STOCK', ['id' => $productId]);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * Lấy tồn kho flash sale
     * @param int $productId
     * @param string $unique
     * @param bool $isNew
     * @return array|mixed|\think\Model|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSeckillAttrStock(int $productId, string $unique, bool $isNew = false)
    {
        $key = md5('seclkill_attr_stock_' . $productId . '_' . $unique);
        $stock = CacheService::get($key);
        if (!$stock || $isNew) {
            $stock = $this->dao->getOne(['product_id' => $productId, 'unique' => $unique, 'type' => 1], 'suk,quota');
            if ($stock) {
                CacheService::set($key, $stock, 60);
            }
        }
        return $stock;
    }

    /**
     * @param $product_id
     * @param string $suk
     * @param string $unique
     * @param bool $is_new
     * @return int|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getProductAttrStock(int $productId, string $suk = '', string $unique = '', $isNew = false)
    {
        if (!$suk && !$unique) return 0;
        $key = md5('product_attr_stock_' . $productId . '_' . $suk . '_' . $unique);
        $stock = CacheService::get($key);
        if (!$stock || $isNew) {
            $where = ['product_id' => $productId, 'type' => 0];
            if ($suk) {
                $where['suk'] = $suk;
            }
            if ($unique) {
                $where['unique'] = $unique;
            }
            $stock = $this->dao->value($where, 'stock');
            CacheService::set($key, $stock, 60);
        }
        return $stock;
    }
}
