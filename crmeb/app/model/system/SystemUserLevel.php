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

namespace app\model\system;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Model cấu hình hạng hệ thống
 * Class SystemUserLevel
 * @package app\model\system
 */
class SystemUserLevel extends BaseModel
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
    protected $name = 'system_user_level';

    /**
     * Getter thời gian
     * @param $value
     * @return false|string
     */
    public function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s', (int)$value);
    }

    /**
     * Getter tỷ lệ ưu đãi
     * @param $value
     * @return int
     */
    public function getDiscountAttr($value)
    {
        return (int)$value;
    }

    /**
     * Hiển thị
     * @param Model $query
     * @param $value
     */
    public function searchIsShowAttr($query, $value)
    {
        $query->where('is_show',$value);
    }

    /**
     * Bộ lọc đã xóa hay chưa
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsDelAttr($query, $value)
    {
        $query->where('is_del', $value ?? 0);
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchTitleAttr($query, $value)
    {
        $query->where('title','LIKE', "%$value%");
    }

}
