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

namespace app\model\system\log;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Model log
 * Class SystemLog
 * @package app\model\system\log
 */
class SystemLog extends BaseModel
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
    protected $name = 'system_log';

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    /**
     * Bộ lọc hình thức truy cập
     * @param Model $query
     * @param $value
     */
    public function searchPagesAttr($query, $value)
    {
        if ($value !== '') {
            $query->whereLike('page', '%' . $value . '%');
        }
    }

    /**
     * Bộ lọc đường dẫn truy cập
     * @param Model $query
     * @param $value
     */
    public function searchPathAttr($query, $value)
    {
        if ($value !== '') {
            $query->whereLike('path', '%' . $value . '%');
        }
    }

    /**
     * Bộ lọc ip
     * @param Model $query
     * @param $value
     */
    public function searchIpAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('ip', 'LIKE', "%$value%");
        }
    }

    /**
     * Bộ lọc id quản trị viên
     * @param Model $query
     * @param $value
     */
    public function searchAdminIdAttr($query, $value)
    {
        if (!empty($value)) {
            $query->whereIn('admin_id', $value);
        }
    }
}
