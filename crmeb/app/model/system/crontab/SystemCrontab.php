<?php

namespace app\model\system\crontab;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class SystemCrontab extends BaseModel
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
    protected $name = 'system_timer';

    /**
     * Không tự động cập nhật update_time
     * @var bool
     */
    protected $updateTime = false;

    /**
     * Bộ lọc có phải tác vụ định kỳ tùy chỉnh hay không
     * @param $query
     * @param $value
     * @param $data
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/6
     */
    public function searchCustomAttr($query, $value, $data)
    {
        if ($value !== '') {
            if ($value == 0) {
                $query->where('mark', '<>', 'customTimer');
            } else {
                $query->where('mark', 'customTimer');
            }
        }
    }
}