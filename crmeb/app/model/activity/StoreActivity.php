<?php

namespace app\model\activity;

use app\model\activity\seckill\StoreSeckill;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class StoreActivity extends BaseModel
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
    protected $name = 'store_activity';

    public function seckill()
    {
        return $this->hasMany(StoreSeckill::class, 'activity_id', 'id')->where('is_show', 1)->where('is_del', 0);
    }
}
