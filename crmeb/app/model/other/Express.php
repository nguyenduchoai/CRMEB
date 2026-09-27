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
 * Model đơn vị vận chuyển
 * Class Express
 * @package app\model\other
 */
class Express extends BaseModel
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
    protected $name = 'express';

    /**
     * Đơn vị vận chuyển có hiển thị hay không
     * @param Model $query
     * @param $value
     */
    public function searchIsShowAttr($query, $value)
    {
        if ($value !== '') $query->where('is_show', $value);
    }

    /**
     * Thông tin đơn vị vận chuyển đã đầy đủ hay chưa
     * @param Model $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        $query->where('status', $value);
    }

    /**
     * Bộ lọc keyword
     * @param Model $query
     * @param $value
     */
    public function searchKeywordAttr($query, $value)
    {
        if ($value) {
            $query->whereLike('name|code', '%' . $value . '%');
        }
    }

    public function searchCodeAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('code', $value);
        }
    }
}
