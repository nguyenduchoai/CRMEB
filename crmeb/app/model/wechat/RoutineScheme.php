<?php

namespace app\model\wechat;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class RoutineScheme extends BaseModel
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
    protected $name = 'routine_scheme';

    public function searchTitleAttr($query, $value)
    {
        if ($value !== '') $query->where('title', 'like', '%' . $value . '%');
    }
}