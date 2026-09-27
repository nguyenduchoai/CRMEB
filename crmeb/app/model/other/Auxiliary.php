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

namespace app\model\other;


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * Bảng phụ trợ
 * Class Auxiliary
 * @package app\model\other
 */
class Auxiliary extends BaseModel
{

    use ModelTrait;

    /**
     * Tên bảng
     * @var string
     */
    protected $name = 'auxiliary';
    protected $insert = ['add_time'];
    protected $autoWriteTimestamp = false;
    /**
     * Khóa chính
     * @var string
     */
    protected $pk = 'id';

    /**Bộ lọc loại
     * @param $query
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        $query->where('type', $value);
    }

    /**Bộ lọc id liên kết theo loại
     * @param $query
     * @param $value
     */
    public function searchBindingIdAttr($query, $value)
    {
        $query->where('binding_id', $value);
    }

    /**Bộ lọc trạng thái theo loại
     * @param $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        $query->whereIn('status', $value);
    }
    /**Bộ lọc id liên kết loại
     * @param $query
     * @param $value
     */
    public function searchRelationIdAttr($query, $value)
    {
        $query->where('relation_id', $value);
    }
}
