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

namespace app\model\diy;


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

class Diy extends BaseModel
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
    protected $name = 'diy';

    protected $updateTime = false;

    /**
     * Getter thời gian thêm
     * @param $value
     * @return false|string
     */
    public function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * Getter thời gian sửa
     * @param $value
     * @return false|string
     */
    public function getUpdateTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : 'Chưa có';
    }

    /**
     * Bộ lọc loại
     * @param Model $query
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        if ($value !== '') {
            if ($value == -1) {
                $query->whereIn('type', [0, 2]);
            } else {
                $query->where('type', $value);
            }
        }
    }

    /**
     * Bộ lọc số phiên bản
     * @param Model $query
     * @param $value
     */
    public function searchVersionAttr($query, $value)
    {
        if ($value != '') $query->where('version', $value);
    }

    /**
     * Bộ lọc đang sử dụng hay không
     * @param Model $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        if ($value != '') $query->where('status', $value);
    }

    /**
     * Bộ lọc tên
     * @param Model $query
     * @param $value
     */
    public function searchNameAttr($query, $value)
    {
        if ($value != '') $query->where('name', $value);
    }

    /**
     * @param $query
     * @param $value
     */
    public function searchIsDelAttr($query, $value)
    {
        if ($value !== '') $query->where('is_del', $value);
    }

    public function searchIsProAttr($query, $value)
    {
        if ($value !== '') $query->where('is_pro', $value);
    }

}
