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

use crmeb\traits\ModelTrait;
use crmeb\basic\BaseModel;
use think\Model;

/**
 *  Model bộ nhớ đệm
 * Class Cache
 * @package app\model\other
 */
class Cache extends BaseModel
{
    use ModelTrait;

    const EXPIRE = 0;
    /**
     * Tên model
     * @var string
     */
    protected $name = 'cache';

    /**
     * Bộ lọc KEY bộ nhớ đệm
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchKeyAttr($query, $value, $data)
    {
        $query->where('key', $value);
    }
}
