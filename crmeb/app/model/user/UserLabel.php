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

namespace app\model\user;

use app\model\other\Category;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Class UserLabel
 * @package app\model\user
 */
class UserLabel extends BaseModel
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
    protected $name = 'user_label';

    /**
     * Danh mục nhãn
     * @param \think\Model $query
     * @param $value
     */
    public function searchLabelCateAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('label_cate', $value);
        }
    }

    /**
     * Liên kết danh mục nhãn
     * @return \think\model\relation\HasOne
     */
    public function cateName()
    {
        return $this->hasOne(Category::class, 'id', 'label_cate')->where('type', 0)->field(['id', 'name'])->bind(['cate_name' => 'name']);
    }

    /**
     * Bộ lọc ids
     * @param Model $query
     * @param $value
     */
    public function searchIdsAttr($query, $value)
    {
        if ($value) $query->whereIn('id', $value);
    }
}
