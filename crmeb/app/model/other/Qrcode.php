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

namespace app\model\other;


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Class Qrcode
 * @package app\model\other
 */
class Qrcode extends BaseModel
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
    protected $name = 'qrcode';

    /**
     * Bộ lọc type
     * @param Model $query
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        if ($value != '') {
            $query->whereLike('type', $value);
        }
    }

    /**
     * Bộ lọc status
     * @param Model $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        if ($value != '') {
            $query->whereLike('status', $value);
        }
    }

    /**
     * Bộ lọc third_type
     * @param Model $query
     * @param $value
     */
    public function searchThirdTypeAttr($query, $value)
    {
        if ($value != '') {
            $query->whereLike('third_type', $value);
        }
    }

    /**
     * Bộ lọc third_id
     * @param Model $query
     * @param $value
     */
    public function searchThirdIdAttr($query, $value)
    {
        if ($value != '') {
            $query->whereLike('third_id', $value);
        }
    }

}
