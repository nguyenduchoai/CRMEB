<?php

namespace app\model\agent;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class SpreadApply extends BaseModel
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
    protected $name = 'spread_apply';

    public function searchUidAttr($query, $value, $data)
    {
        if ($value !== '') $query->where('uid', $value);
    }
}