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
 * Model danh sách dữ liệu tổ hợp
 * Class SystemGroupData
 * @package app\model\system\config
 */
class SystemGroupData extends BaseModel
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
    protected $name = 'system_group_data';

    /**
     * Bộ lọc trạng thái
     * @param $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        if ($value != '') {
            $query->where('status', $value);
        }
    }

    /**
     * Bộ lọc Gid
     * @param Model $query
     * @param $value
     */
    public function searchGidAttr($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('gid', $value);
        } else {
            $query->where('gid', $value);
        }
    }
}
