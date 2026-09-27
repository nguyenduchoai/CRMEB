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

namespace app\model\activity\coupon;

use app\model\user\User;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * TODO Model người dùng nhận phiếu giảm giá ở phía người dùng
 * Class StoreCouponIssueUser
 * @package app\model\coupon
 */
class StoreCouponIssueUser extends BaseModel
{
    use ModelTrait;

    /**
     * Tên model
     * @var string
     */
    protected $name = 'store_coupon_issue_user';

    /**
     * Lấy tên và ảnh đại diện người nhận
     * @return \think\model\relation\HasOne
     */
    public function userInfo()
    {
        return $this->hasOne(User::class, 'uid', 'uid')->field('uid,nickname,avatar')->bind(['nickname','avatar']);
    }

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
     * Bộ lọc người dùng đã nhận
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchUidAttr($query, $value, $data)
    {
        $query->where('uid', $value);
    }

    /**
     * Bộ lọc phiếu giảm giá đã nhận
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIssueCouponIdAttr($query, $value, $data)
    {
        $query->where('issue_coupon_id', $value);
    }
}
