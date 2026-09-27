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

namespace app\model\shipping;


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Dữ liệu thành phố
 * Class SystemCity
 * @package app\model\shipping
 */
class SystemCity extends BaseModel
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
    protected $name = 'system_city';

    /**
     * Lấy điều kiện truy vấn danh mục con
     * @return \think\model\relation\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'city_id')->order('id ASC');
    }


    /**
     * Bộ lọc city
     * @param Model $query
     * @param $value
     */
    public function searchCityIdAttr($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('city_id', $value);
        } else {
            $query->where('city_id', $value);
        }
    }

    /**
     * Bộ lọc ParentId
     * @param Model $query
     * @param $value
     */
    public function searchParentIdAttr($query, $value)
    {
        $query->where('parent_id', $value);
    }


}
