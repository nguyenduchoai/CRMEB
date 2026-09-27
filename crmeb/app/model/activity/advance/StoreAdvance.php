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

namespace app\model\activity\advance;

use app\model\product\product\StoreDescription;
use app\model\product\product\StoreProduct;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * TODO Model sản phẩm đặt trước
 * Class StoreSeckill
 * @package app\model\activity
 */
class StoreAdvance extends BaseModel
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
    protected $name = 'store_advance';

    use ModelTrait;

    /**
     * Liên kết một-một
     * Sản phẩm liên kết chi tiết sản phẩm
     * @return \think\model\relation\HasOne
     */
    public function description()
    {
        return $this->hasOne(StoreDescription::class, 'product_id', 'id')->where('type', 6)->bind(['description']);
    }

    /**
     * Liên kết một-một
     * Sản phẩm liên kết chi tiết sản phẩm
     * @return \think\model\relation\HasOne
     */
    public function product()
    {
        return $this->hasOne(StoreProduct::class, 'id', 'product_id')->where('is_show', 1)->where('is_del', 0)->field(['id', 'cate_id'])->bind([
            'cate_id' => 'cate_id'
        ]);
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
     * @param $query
     */
    public function searchTitleAttr($query, $value)
    {
        if ($value !== '') $query->where('title|id', 'like', '%' . $value . '%');
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
        if ($value !== '') $query->where('status', $value);
    }

    /**
     * Bộ lọc trạng thái
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchTypeAttr($query, $value, $data)
    {
        if ($value !== '') $query->where('type', $value);
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
