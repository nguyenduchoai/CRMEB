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

namespace app\model\activity\combination;

use app\model\product\product\StoreDescription;
use app\model\product\product\StoreProduct;
use crmeb\traits\ModelTrait;
use crmeb\basic\BaseModel;
use think\Model;

/**
 * TODO Model sản phẩm mua chung
 * Class StoreCombination
 * @package app\model\activity
 */
class StoreCombination extends BaseModel
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
    protected $name = 'store_combination';

    use ModelTrait;

    /**
     * Lấy giá gốc (liên kết một-một)
     * @return \think\model\relation\HasOne
     */
    public function getPrice()
    {
        return $this->hasOne(StoreProduct::class, 'id', 'product_id')->bind(['ot_price', 'product_price' => 'price']);
    }
    /**
     * Lấy danh mục sản phẩm (liên kết một-một)
     * @return \think\model\relation\HasOne
     */
    public function getCategory()
    {
        return $this->hasOne(StoreProduct::class, 'id', 'product_id')->bind(['cate_id']);
    }
    /**
     * Liên kết một-một
     * Sản phẩm liên kết chi tiết sản phẩm
     * @return \think\model\relation\HasOne
     */
    public function total()
    {
        return $this->hasOne(StoreProduct::class, 'id', 'product_id')->where('is_show', 1)->where('is_del', 0)->field(['(sales+ficti) as total', 'id', 'price'])->bind([
            'total' => 'total', 'product_price' => 'price'
        ]);
    }

    /**
     * Liên kết một-một
     * Sản phẩm liên kết chi tiết sản phẩm
     * @return \think\model\relation\HasOne
     */
    public function description()
    {
        return $this->hasOne(StoreDescription::class, 'product_id', 'id')->where('type', 3)->bind(['description']);
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
     * Getter ảnh trình chiếu
     * @param $value
     * @return mixed
     */
    public function getImagesAttr($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Bộ lọc tên sản phẩm mua chung
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
    public function searchIsHostAttr($query, $value, $data)
    {
        $query->where('is_host', $value ?? 1);
    }

    /**
     * Bộ lọc trạng thái
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsShowAttr($query, $value, $data)
    {
        if ($value != '') $query->where('is_show', $value ?: 0);
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
}
