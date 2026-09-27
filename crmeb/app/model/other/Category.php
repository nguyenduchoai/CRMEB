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


use app\model\user\UserLabel;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Bảng danh mục
 * Class Category
 * @package app\model\other
 */
class Category extends BaseModel
{

    use ModelTrait;

    /**
     * Tên bảng
     * @var string
     */
    protected $name = 'category';

    /**
     * Khóa chính
     * @var string
     */
    protected $pk = 'id';

    /**
     * Tìm kiếm theo tên danh mục
     * @param Model $query
     * @param $value
     */
    public function searchNameAttr($query, $value)
    {
        $query->whereLike('name', '%' . $value . '%');
    }

    /**
     *  Người phụ trách
     * @param Model $query
     * @param $value
     */
    public function searchOwnerIdAttr($query, $value)
    {
        $query->where('owner_id', $value);
    }

    /**
     *  Loại
     * @param Model $query
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        $query->where('type', $value);
    }

    /**
     * Liên kết một-nhiều
     * @return \think\model\relation\HasMany
     */
    public function label()
    {
        return $this->hasMany(UserLabel::class, 'label_cate', 'id');
    }
}
