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
class MessageSystem extends BaseModel
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
    protected $name = 'message_system';


    protected $insert = ['add_time'];

    /**
     * Bộ lọc ID
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIdAttr($query, $value, $data)
    {
        $query->where('id', $value);
    }
    /**
     * Bộ lọc UID
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchUidAttr($query, $value, $data)
    {
        $query->where('uid', $value);
    }
    /**
     * Bộ lọc Look
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchLookAttr($query, $value, $data)
    {
        $query->where('look', $value);
    }
    /**
     * Bộ lọc del
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsDelAttr($query, $value, $data)
    {
        $query->where('is_del', $value);
    }

}
