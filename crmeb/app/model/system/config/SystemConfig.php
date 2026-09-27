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

namespace app\model\system\config;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Model cấu hình hệ thống
 * Class SystemConfig
 * @package app\model\system\config
 */
class SystemConfig extends BaseModel
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
    protected $name = 'system_config';

    /**
     * Bộ lọc tên menu
     * @param Model $query
     * @param $value
     */
    public function searchMenuNameAttr($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('menu_name', $value);
        } else {
            $query->where('menu_name', $value);
        }
    }

    /**
     * Tìm kiếm theo tab id
     * @param Model $query
     * @param $value
     */
    public function searchTabIdAttr($query, $value)
    {
        if ($value != 0) {
            $query->where('config_tab_id', $value);
        }
    }

    /**
     * Bộ lọc trạng thái
     * @param Model $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        $query->where('status', $value ?: 1);
    }

    /**
     * Bộ lọc value
     * @param Model $query
     * @param $value
     */
    public function searchValueAttr($query, $value)
    {
        $query->where('value', $value);
    }

    /**
     * Bộ lọc info
     * @param Model $query
     * @param $value
     */
    public function searchConfigNameAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('info|menu_name', 'like', "%$value%");
        }
    }
}
