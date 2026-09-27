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

namespace app\model\activity\coupon;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * TODO Model phát hành phiếu giảm giá
 * Class StoreCouponIssue
 * @package app\model\coupon
 */
class StoreCouponIssue extends BaseModel
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
    protected $name = 'store_coupon_issue';

    /**
     * Người dùng có sở hữu hay không
     * @return \think\model\relation\HasOne
     */
    public function used()
    {
        return $this->hasMany(StoreCouponIssueUser::class, 'issue_coupon_id', 'id');
    }

    /**
     * id
     * @param Model $query
     * @param $value
     */
    public function searchIdAttr($query, $value)
    {
        if (is_array($value))
            $query->whereIn('id', $value);
        else
            $query->where('id', $value);
    }

    /**
     * Bộ lọc mẫu phiếu giảm giá
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchCidAttr($query, $value, $data)
    {
        $query->where('cid', $value);
    }

    /**
     * Phiếu giảm giá có không giới hạn số lượng hay không
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsPermanentAttr($query, $value, $data)
    {
        $query->where('is_permanent', $value);
    }

    /**
     * Phiếu giảm giá có phải phiếu cho người mới hay không
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsGiveSubscribeAttr($query, $value, $data)
    {
        $query->where('is_give_subscribe', $value);
    }

    /**
     * Phiếu giảm giá có phải tặng theo đơn tối thiểu hay không
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsFullGiveAttr($query, $value, $data)
    {
        $query->where('is_full_give', $value);
    }

    /**
     * Trạng thái phiếu giảm giá
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchStatusAttr($query, $value, $data)
    {
        if ($value != '') $query->where('status', $value);
    }

    /**
     * Phiếu giảm giá đã xóa hay chưa
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsDelAttr($query, $value, $data)
    {
        $query->where('is_del', $value ?? 0);
    }

    /**
     * Tên phiếu giảm giá
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchCouponTitleAttr($query, $value, $data)
    {
        if ($value) $query->whereLike('coupon_title', '%' . $value . '%');
    }

    /**
     * Loại phiếu giảm giá
     * @param Model $query
     * @param $value
     */
    public function searchCouponTypeAttr($query, $value)
    {
        if ($value != '') $query->where('type', $value);
    }
}
