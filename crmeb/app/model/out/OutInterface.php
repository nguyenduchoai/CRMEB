<?php

namespace app\model\out;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class OutInterface extends BaseModel
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
    protected $name = 'out_interface';

    public function searchTypeAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('type', $value);
        }
    }
}