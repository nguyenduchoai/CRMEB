<?php

namespace app\model\system;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * @author: Wu Xi
 * @email: 442384644@qq.com
 * @date: 2023/7/28
 */
class SystemSignReward extends BaseModel
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
    protected $name = 'system_sign_reward';

    /**
     * Bộ lọc loại
     * @param $query
     * @param $value
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/7/28
     */
    public function searchTypeAttr($query, $value)
    {
        if ($value !== '') $query->where('type', $value);
    }
}