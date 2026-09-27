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
 *  Model miễn phí vận chuyển
 * Class ShippingTemplatesFree
 * @package app\model\shipping
 */
class ShippingTemplatesFree extends BaseModel
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
    protected $name = 'shipping_templates_free';

    /**
     * Bộ lọc ID thành phố
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchCityIdAttr($query, $value)
    {
        $query->where('city_id', $value);
    }

    /**
     * Tìm kiếm theo id mẫu
     * @param Model $query
     * @param $value
     */
    public function searchTempIdAttr($query, $value)
    {
        $query->where('temp_id', $value);
    }

    /**
     * Bộ lọc uniqid
     * @param Model $query
     * @param $value
     */
    public function searchUniqidAttr($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('uniqid', $value);
        } else {
            $query->where('uniqid', $value);
        }
    }
}
