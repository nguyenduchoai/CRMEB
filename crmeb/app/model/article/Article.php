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
namespace app\model\article;

use app\model\product\product\StoreProduct;
use crmeb\traits\ModelTrait;
use crmeb\basic\BaseModel;
use think\Model;

/**
 * TODO Model bài viết
 * Class Article
 * @package app\model\article
 */
class Article extends BaseModel
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
    protected $name = 'article';

    /**
     * Liên kết một-một với sản phẩm
     * @return \think\model\relation\HasOne
     */
    public function storeInfo()
    {
        return $this->hasOne(StoreProduct::class, 'id', 'product_id')
            ->field('store_name,image,price,id,ot_price');
    }

    /**
     * Liên kết một-một với chi tiết bài viết
     * @return \think\model\relation\HasOne
     */
    public function content()
    {
        return $this->hasOne(ArticleContent::class, 'nid', 'id')->bind(['content']);
    }

    /**
     * Liên kết một-một với chi tiết bài viết
     * @return \think\model\relation\HasOne
     */
    public function cateName()
    {
        return $this->hasOne(ArticleCategory::class, 'id', 'cid')->bind(['catename' => 'title']);
    }

    /**
     * Getter hình ảnh bài viết
     * @param $value
     * @return array|false|string[]
     */
    protected function getImageInputAttr($value)
    {
        return explode(',', $value) ?: [];
    }

    /**
     * Bộ lọc danh mục bài viết
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchCidAttr($query, $value, $data)
    {
        if ($value) {
            if (is_array($value)) {
                $query->whereIn('cid', $value);
            } else {
                $query->where('cid', $value);
            }
        }
    }

    /**
     * Bộ lọc tiêu đề bài viết
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchTitleAttr($query, $value, $data)
    {
        if ($value !== '') {
            $query->where('title', 'like', '%' . $value . '%');
        }
    }

    /**
     * Bộ lọc bài viết nổi bật
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsHotAttr($query, $value, $data)
    {
        if ($value !== '') {
            $query->where('is_hot', $value);
        }
    }

    /**
     * Bộ lọc bài viết trình chiếu
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsBannerAttr($query, $value, $data)
    {
        if ($value !== '') {
            $query->where('is_banner', $value);
        }
    }

}
