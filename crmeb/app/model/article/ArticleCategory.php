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

namespace app\model\article;

use crmeb\traits\ModelTrait;
use crmeb\basic\BaseModel;
use think\Model;

/**
 * TODO Model danh mục bài viết
 * Class ArticleCategory
 * @package app\model\article
 */
class ArticleCategory extends BaseModel
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
    protected $name = 'article_category';

    /**
     * Lấy điều kiện truy vấn danh mục con
     * @return \think\model\relation\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'pid', 'id')->where(['hidden' => 0, 'is_del' => 0, 'status' => 1])->order('sort DESC,id DESC')->field('id,pid,title');
    }

    /**
     * Bộ lọc trạng thái danh mục
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchStatusAttr($query, $value, $data)
    {
        if ($value !== '') $query->where('status', $value);
    }

    /**
     * Bộ lọc tên danh mục
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchTitleAttr($query, $value, $data)
    {
        $query->where('title', 'like', '%' . $value . '%');
    }

    /**
     * Bộ lọc ẩn
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchHiddenAttr($query, $value, $data)
    {
        $query->where('hidden', $value);
    }

    /**
     * Bộ lọc đã xóa
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsDelAttr($query, $value, $data)
    {
        $query->where('is_del', $value);
    }

    /**
     * Bộ lọc cấp trên
     * @param $query
     * @param $value
     */
    public function searchPidAttr($query, $value)
    {
        if ($value !== '') $query->where('pid', $value);
    }
}
