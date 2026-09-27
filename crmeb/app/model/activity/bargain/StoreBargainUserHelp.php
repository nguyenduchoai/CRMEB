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

namespace app\model\activity\bargain;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * TODO Model giúp giảm giá
 * Class StoreBargainUserHelp
 * @package app\model\activity
 */
class StoreBargainUserHelp extends BaseModel
{
    /**
     * Khóa chính bảng dữ liệu
     * @var string
     */
    protected $pk = 'id';

    /**
     * Tên model
     * @var string
     */
    protected $name = 'store_bargain_user_help';

    use ModelTrait;

    /**
     * Bộ lọc người dùng
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchUidAttr($query, $value, $data)
    {
        $query->where('uid', $value);
    }

    /**
     * Bộ lọc ID sản phẩm
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchBargainIdAttr($query, $value, $data)
    {
        $query->where('bargain_id', $value);
    }

    /**
     * Bộ lọc ID săn giảm giá
     * @param $query
     * @param $value
     */
    public function searchBargainUserIdAttr($query, $value)
    {
        $query->where('bargain_user_id', $value);
    }

    /**
     * Bộ lọc ID săn giảm giá
     * @param $query
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        $query->where('type', $value);
    }
}

