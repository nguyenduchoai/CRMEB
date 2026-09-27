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

namespace app\model\product\product;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Model danh mục sản phẩm
 * Class StoreCategory
 * @package app\model\product\product
 */
class StoreCategory extends BaseModel
{
    use ModelTrait;

    /**
     * Khóa chính bảng dữ liệu
     * @var string
     */
    protected $pk = 'id';

    /**
     * Tên model
     * @var string
     */
    protected $name = 'store_category';

    /**
     * Getter thời gian thêm
     * @param $value
     * @return false|string
     */
    protected function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * Lấy điều kiện truy vấn danh mục con
     * @return \think\model\relation\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'pid', 'id')->where('is_show', 1)->order('sort DESC,id DESC');
    }

    /**
     * Bộ lọc danh mục có hiển thị hay không
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsShowAttr($query, $value, $data)
    {
        if ($value !== '') $query->where('is_show', $value);
    }

    /**
     * Bộ lọc danh mục có hiển thị hay không
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchPidAttr($query, $value, $data)
    {
        if ($value !== '') $query->where('pid', $value);
    }

    /**
     * Bộ lọc danh mục có hiển thị hay không
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchCateNameAttr($query, $value, $data)
    {
        if ($value !== '') $query->where('cate_name', 'like', '%' . $value . '%');
    }

    /**
     * Bộ lọc danh mục có hiển thị hay không
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIdAttr($query, $value, $data)
    {
        if ($value) $query->whereIn('id', is_array($value) ? $value : (string)$value);
    }
}
