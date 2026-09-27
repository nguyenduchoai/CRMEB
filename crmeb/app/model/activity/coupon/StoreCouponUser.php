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
 * TODO Model phát phiếu giảm giá
 * Class StoreCouponUser
 * @package app\model\coupon
 */
class StoreCouponUser extends BaseModel
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
    protected $name = 'store_coupon_user';

    /**
     * Lấy loại
     * @var string[]
     */
    protected $gainType = ['send' => 'Phát từ trang quản trị', 'get' => 'Tự nhận'];

    /**
     * Getter loại
     * @param $value
     * @return string
     */
    public function getTypeAttr($value)
    {
        return $this->gainType[$value];
    }

    /**
     * Trạng thái sử dụng
     * @var string[]
     */
    protected $statusType = [0 => 'Chưa sử dụng', 1 => 'Đã sử dụng', 2 => 'Đã hết hạn'];

    /**
     * Getter trạng thái
     * @param $value
     * @return string
     */
    public function getStatusAttr($value)
    {
        return $this->statusType[$value];
    }

    /**
     * @return \think\model\relation\HasOne
     */
    public function issue()
    {
        return $this->hasOne(StoreCouponIssue::class, 'id', 'cid')->field(['id', 'end_use_time', 'start_use_time', 'type', 'coupon_time', 'product_id', 'category_id', 'receive_type'])->bind([
            'applicable_type' => 'type',
            'coupon_time' => 'coupon_time',
            'product_id',
            'category_id',
            'receive_type',
            'start_use_time',
            'end_use_time'
        ]);
    }

    /**
     * Lấy tên và ảnh đại diện người nhận
     * @return \think\model\relation\HasOne
     */
    public function userInfo()
    {
        return $this->hasOne(User::class, 'uid', 'uid')->field('uid,nickname,avatar')->bind(['nickname', 'avatar']);
    }

    /**
     * Bộ lọc ID phiếu giảm giá
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchCidAttr($query, $value, $data)
    {
        if (is_array($value)) {
            $query->where('cid', 'IN', $value);
        } else {
            $query->where('cid', $value);
        }
    }

    /**
     * Bộ lọc ID người dùng
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchUidAttr($query, $value, $data)
    {
        $query->where('uid', $value);
    }

    /**
     * Bộ lọc tên phiếu giảm giá
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchCouponTitleAttr($query, $value, $data)
    {
        $query->where('coupon_title', 'like', '%' . $value . '%');
    }

    /**
     * Bộ lọc hình thức nhận
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchTypeAttr($query, $value, $data)
    {
        $query->where('type', $value);
    }


    /**
     * Đã hết hiệu lực
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsFailAttr($query, $value, $data)
    {
        $query->where('is_fail', $value);
    }

    /**
     * Bộ lọc còn trong hạn sử dụng hay không
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchTimeAttr($query, $value, $data)
    {
        $query->whereTime('add_time', '>=', $value)->whereTime('end_time', '<=', $value);
    }
}
