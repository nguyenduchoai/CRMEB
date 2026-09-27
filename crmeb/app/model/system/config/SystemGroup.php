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

namespace app\model\system\config;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Model cấu hình dữ liệu tổ hợp
 * Class SystemGroup
 * @package app\model\system\config
 */
class SystemGroup extends BaseModel
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
    protected $name = 'system_group';

    /**
     * Bộ lọc tên cấu hình
     * @param Model $query
     * @param $value
     */
    public function searchConfigNameAttr($query, $value)
    {
        $query->where('config_name', $value);
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchTitleAttr($query, $value)
    {
        if ($value != '') {
            $query->whereLIke('id|name|info|config_name', "%$value%");
        }
    }

    /**
     * Tìm kiếm danh mục
     * @param Model $query
     * @param $value
     */
    public function searchCateIdAttr($query, $value)
    {
        $query->where('cate_id', $value);
    }
}
