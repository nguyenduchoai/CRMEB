<?php

namespace app\model\system\log;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * @author Wu Xi
 * @email 442384644@qq.com
 * @date 2023/04/07
 */
class SystemFileInfo extends BaseModel
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
    protected $name = 'system_file_info';
}