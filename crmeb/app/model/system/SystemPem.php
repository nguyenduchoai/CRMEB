<?php

namespace app\model\system;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class SystemPem extends BaseModel
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
    protected $name = 'system_pem';
    protected $autoWriteTimestamp = false;
}