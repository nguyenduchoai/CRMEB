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

namespace app\model\activity\seckill;

use app\model\product\product\StoreDescription;
use app\model\product\product\StoreProduct;
use app\model\product\sku\StoreProductAttrValue;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * TODO Model sản phẩm flash sale
 * Class StoreSeckill
 * @package app\model\activity
 */
class StoreSeckill extends BaseModel
{
    /**
     * Khóa chính bảng dữ liệu
     * @var string
     */
    protected $pk = 'id';

    /**
     * Tên model
     * @var string
     */
    protected $name = 'store_seckill';

    use ModelTrait;

    /**
     * Liên kết một-một
     * Sản phẩm liên kết chi tiết sản phẩm
     * @return \think\model\relation\HasOne
     */
    public function description()
    {
        return $this->hasOne(StoreDescription::class, 'product_id', 'id')->where('type', 1)->bind(['description']);
    }

    /**
     * Liên kết một-một
     * Sản phẩm liên kết chi tiết sản phẩm
     * @return \think\model\relation\HasOne
     */
    public function product()
    {
        return $this->hasOne(StoreProduct::class, 'id', 'product_id')->where('is_show', 1)->where('is_del', 0)->field(['id','cate_id','price'])->bind([
            'cate_id' => 'cate_id',
            'product_price' => 'price'
        ]);
    }

    public function attrs()
    {
        return $this->hasMany(StoreProductAttrValue::class, 'product_id', 'id')->where('type', 1);
    }

    /**
     * Getter thời gian thêm
     * @param $value
     * @return false|string
     */
    protected function getAddTimeAttr($value)
    {
        if ($value) return date('Y-m-d H:i:s', (int)$value);
        return '';
    }

    /**
     * Getter hình ảnh
     * @param $value
     * @return array|mixed
     */
    protected function getImagesAttr($value)
    {
        return json_decode($value, true) ?: [];
    }

    /**
     * Bộ lọc tên sản phẩm flash sale
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchStoreNameAttr($query, $value, $data)
    {
        if ($value) $query->where('title|id', 'like', '%' . $value . '%');
    }

    /**
     * Bộ lọc có đề xuất hay không
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsHotAttr($query, $value, $data)
    {
        $query->where('is_hot', $value ?? 1);
    }

    /**
     * Bộ lọc trạng thái
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsShowAttr($query, $value, $data)
    {
        $query->where('is_show', $value ?? 1);
    }

    /**
     * Bộ lọc đã xóa hay chưa
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsDelAttr($query, $value, $data)
    {
        $query->where('is_del', $value ?? 0);
    }

    /**
     * Bộ lọc trạng thái
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchStatusAttr($query, $value, $data)
    {
        if ($value != '') $query->where('status', $value);
    }

    /**
     * Bộ lọc ID sản phẩm
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchProductIdAttr($query, $value, $data)
    {
        if ($value) {
            if (is_array($value)) {
                $query->whereIn('product_id', $value);
            } else {
                $query->where('product_id', $value);
            }
        }
    }

    /**
     * Bộ lọc thời gian hiệu lực hoạt động
     * @param $query
     * @param $value
     */
    public function searchSeckillTimeAttr($query, $value)
    {
        if ($value == 1) {
            $time = time();
            $query->where('start_time', '<=', $time)->where('stop_time', '>=', $time - 86400);
        }
    }

    public function searchActivityIdAttr($query, $value)
    {
        if ($value != '') $query->where('activity_id', $value);
    }
}
