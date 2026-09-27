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

namespace app\model\product\product;

use app\model\order\StoreOrderCartInfo;
use app\model\user\User;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 *  Model đánh giá sản phẩm
 * Class StoreProductReply
 * @package app\model\product\product
 */
class StoreProductReply extends BaseModel
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
    protected $name = 'store_product_reply';

    /**
     * Liên kết một-một
     * Đánh giá sản phẩm liên kết sản phẩm
     * @return \think\model\relation\HasOne
     */
    public function productInfo()
    {
        return $this->hasOne(StoreProduct::class, 'id', 'product_id');
    }

    /**
     * Liên kết một-một
     * Đánh giá sản phẩm liên kết đơn hàng
     * @return \think\model\relation\HasOne
     */
    public function cartInfo()
    {
        return $this->hasOne(StoreOrderCartInfo::class, 'unique', 'unique')->bind(['cart_info']);
    }

    /**
     * Liên kết một-một
     * Đánh giá sản phẩm liên kết đơn hàng
     * @return \think\model\relation\HasOne
     */
    public function userInfo()
    {
        return $this->hasOne(User::class, 'uid', 'uid')->field('is_money_level,uid')->bind(['is_money_level']);
    }


    /**
     * Setter hình ảnh đánh giá
     * @param $value
     * @return false|string
     */
    protected function setPicsAttr($value)
    {
        return is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Getter hình ảnh đánh giá
     * @param $value
     * @return mixed
     */
    protected function getPicsAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * Bộ lọc người dùng
     * @param Model $query
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        $query->where('uid', $value);
    }

    /**
     * Bộ lọc sản phẩm
     * @param Model $query
     * @param $value
     */
    public function searchProductIdAttr($query, $value)
    {
        $query->where('product_id', $value);
    }

    /**
     * Bộ lọc đã xóa hay chưa
     * @param Model $query
     * @param $value
     */
    public function searchIsDelAttr($query, $value)
    {
        $query->where('is_del', $value ?? 0);
    }

    /**
     * Bộ lọc đã trả lời hay chưa
     * @param Model $query
     * @param $value
     */
    public function searchIsReplyAttr($query, $value)
    {
        $query->where('is_reply', $value);
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchUniqueAttr($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('unique', $value);
        } else {
            $query->where('unique', $value);
        }
    }

    /**
     * Bộ lọc oid id đơn hàng
     * @param Model $query
     * @param $value
     */
    public function searchOidAttr($query, $value)
    {
        $query->where('oid', $value);
    }

    /**
     * Bộ lọc điểm đánh giá sản phẩm
     * @param Model $query
     * @param $value
     */
    public function searchProductScoreAttr($query, $value)
    {
        $query->where('product_score', $value);
    }

    /**
     * Bộ lọc trạng thái
     * @param Model $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        if ($value !== '') $query->where('status', $value);
    }
}
