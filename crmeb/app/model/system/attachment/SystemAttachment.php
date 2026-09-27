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

namespace app\model\system\attachment;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Model quản lý tệp đính kèm
 * Class SystemAttachment
 * @package app\model\system\attachment
 */
class SystemAttachment extends BaseModel
{
    use ModelTrait;

    /**
     * Khóa chính bảng dữ liệu
     * @var string
     */
    protected $pk = 'att_id';

    /**
     * Tên model
     * @var string
     */
    protected $name = 'system_attachment';

    /**
     * Bộ lọc loại hình ảnh
     * @param Model $query
     * @param $value
     */
    public function searchModuleTypeAttr($query, $value)
    {
        $query->where('module_type', $value ?: 1);
    }

    /**
     * Bộ lọc pid
     * @param Model $query
     * @param $value
     */
    public function searchPidAttr($query, $value)
    {
        if ($value) $query->where('pid', $value);
    }

    /**
     * Tìm kiếm gần đúng theo name
     * @param Model $query
     * @param $value
     */
    public function searchLikeNameAttr($query, $value)
    {
        if ($value) $query->where('name', 'LIKE', "$value%");
    }

    /**
     * Tìm kiếm gần đúng theo real_name
     * @param Model $query
     * @param $value
     */
    public function searchRealNameAttr($query, $value)
    {
        if ($value != '') $query->where('real_name', 'LIKE', "%$value%");
    }

    public function searchTypeAttr($query, $value)
    {
        if ($value !== '') $query->where('type', $value);
    }
}
