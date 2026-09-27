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

namespace app\model\user;

use app\model\order\StoreOrder;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

class UserBrokerage extends BaseModel
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
    protected $name = 'user_brokerage';

    /**
     * Liên kết bảng đơn hàng
     * @return UserBill|model\relation\HasOne
     */
    public function order()
    {
        return $this->hasOne(StoreOrder::class, 'id', 'link_id')->field(['id', 'total_num'])->bind(['total_num']);
    }

    /**
     * Người dùng liên kết
     * @return model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

    /**
     * uid người dùng
     * @param Model $query
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        if ($value !== '') {
            if (is_array($value))
                $query->whereIn('uid', $value);
            else
                $query->where('uid', $value);
        }
    }

    /**
     * Liên kết id
     * @param Model $query
     * @param $value
     */
    public function searchLinkIdAttr($query, $value)
    {
        if (is_array($value))
            $query->whereIn('link_id', $value);
        else
            $query->where('link_id', $value);
    }

    /**
     * Chi ra|Nhận được
     * @param Model $query
     * @param $value
     */
    public function searchPmAttr($query, $value)
    {
        if ($value !== '') $query->where('pm', $value);
    }


    /**
     * Loại
     * @param Model $query
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        if (is_array($value))
            $query->whereIn('type', $value);
        else
            $query->where('type', $value);
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchNotTypeAttr($query, $value)
    {
        if (is_array($value))
            $query->whereNotIn('type', $value);
        else
            $query->where('type', '<>', $value);
    }

    /**
     * Trạng thái: 0: chờ xác nhận, 1: có hiệu lực, -1: không hiệu lực
     * @param Model $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        $query->where('status', $value);
    }

    /**
     * Đã nhận hàng hay chưa: 0: chưa nhận hàng, 1: đã nhận hàng
     * @param Model $query
     * @param $value
     */
    public function searchTakeAttr($query, $value)
    {
        $query->where('take', $value);
    }

    /**
     * Tìm kiếm gần đúng
     * @param Model $query
     * @param $value
     */
    public function searchLikeAttr($query, $value)
    {
        $query->where(function ($query) use ($value) {
            $query->where('uid|title', 'like', "%$value%")->whereOr('uid', 'in', function ($query) use ($value) {
                $query->name('user')->whereLike('uid|account|nickname|phone', '%' . $value . '%')->field('uid')->select();
            });
        });
    }

    /**
     * Thời gian
     * @param Model $query
     * @param $value
     */
    public function searchAddTimeAttr($query, $value)
    {
        if (is_string($value)) $query->whereTime($query, $value);
        if (is_array($value) && count($value) == 2) $query->whereTime('add_time', 'between', $value);
    }
}
