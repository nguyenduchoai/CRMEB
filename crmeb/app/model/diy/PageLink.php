<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\model\diy;


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

class PageLink extends BaseModel
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
    protected $name = 'page_link';

    /**
     * Bộ lọc danh mục
     * @param Model $query
     * @param $value
     */
    public function searchCateIdAttr($query, $value)
    {
        if ($value) {
            if (is_array($value)) {
                $query->whereIn('cate_id', $value);
            } else {
                $query->where('cate_id', $value);
            }
        }
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
     * Kiểm tra module
     * @param Model $query
     * @param $value
     */
    public function searchNoModelAttr($query, $value)
    {
        $query->when(!in_array('seckill', $value), function ($q1) {
            $q1->whereNotLike('name', '%flash sale%');
        })->when(!in_array('bargain', $value), function ($q2) {
            $q2->whereNotLike('name', '%săn giảm giá%');
        })->when(!in_array('combination', $value), function ($q3) {
            $q3->whereNotLike('name', '%mua chung%');
        });
    }
}
