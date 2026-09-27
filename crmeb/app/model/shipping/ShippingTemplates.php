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

namespace app\model\shipping;

use crmeb\traits\ModelTrait;
use crmeb\basic\BaseModel;
use think\Model;

/**
 *  Model mẫu phí vận chuyển
 * Class ShippingTemplates
 * @package app\model\shipping
 */
class ShippingTemplates extends BaseModel
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
    protected $name = 'shipping_templates';

    /**
     * Getter loại
     * @param $value
     * @return string
     */
    public function getTypeAttr($value)
    {
        $status = [1 => 'Theo số lượng', 2 => 'Theo trọng lượng', 3 => 'Theo thể tích'];
        return $status[$value];
    }

    /**
     * Getter có mở miễn phí vận chuyển hay không
     * @param $value
     * @return string
     */
    public function getAppointAttr($value)
    {
        $status = [1 => 'Bật', 0 => 'Tắt'];
        return $status[$value];
    }

    /**
     * Getter thời gian thêm
     * @param $value
     * @return false|string
     */
    public function getAddTimeAttr($value)
    {
        $value = date('Y-m-d H:i:s', $value);
        return $value;
    }

    /**
     * Liên kết một-nhiều với khu vực áp phí vận chuyển
     * @return \think\model\relation\HasMany
     */
    public function region()
    {
        return $this->hasMany(ShippingTemplatesRegion::class, 'temp_id', 'id');
    }

    /**
     * Liên kết một-nhiều với khu vực miễn phí vận chuyển
     * @return \think\model\relation\HasMany
     */
    public function free()
    {
        return $this->hasMany(ShippingTemplatesFree::class, 'temp_id', 'id');
    }

    /**
     * Bộ lọc ID
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIdAttr($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('id', $value);
        } else {
            $query->where('id', $value);
        }
    }

    /**
     * Bộ lọc tên mẫu
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchNameAttr($query, $value)
    {
        if ($value) {
            $query->where('name', 'like', '%' . $value . '%');
        }
    }

}
