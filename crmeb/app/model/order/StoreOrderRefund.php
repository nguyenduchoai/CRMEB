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
namespace app\model\order;

use app\model\user\User;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

class StoreOrderRefund extends BaseModel
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
    protected $name = 'store_order_refund';

    /**
     * Getter thông tin giỏ hàng
     * @param $value
     * @return array|mixed
     */
    public function getCartInfoAttr($value)
    {
        return is_string($value) ? json_decode($value, true) ?? [] : [];
    }

    /**
     * Getter hình ảnh
     * @param $value
     * @return array|mixed
     */
    public function getRefundImgAttr($value)
    {
        return is_string($value) ? json_decode($value, true) ?? [] : [];
    }

    /**
     * Liên kết một-một với bảng đơn hàng
     * @return StoreOrderRefund|\think\model\relation\HasOne
     */
    public function order()
    {
        return $this->hasOne(StoreOrder::class, 'id', 'store_order_id');
    }

    /**
     * Liên kết một-một với bảng người dùng
     * @return \think\model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid')->field(['uid', 'avatar', 'nickname', 'phone'])->bind([
            'avatar' => 'avatar',
            'nickname' => 'nickname',
            'phone' => 'phone'
        ]);
    }

    /**
     * Bộ lọc ID đơn hàng
     * @param $query
     * @param $value
     */
    public function searchStoreOrderIdAttr($query, $value)
    {
        if ($value !== '') {
            if (is_array($value)) {
                $query->whereIn('store_order_id', $value);
            } else {
                $query->where('store_order_id', $value);
            }
        }
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        if ($value !== '' && !is_null($value)) {
            if (is_array($value)) {
                $query->whereIn('uid', $value);
            } else {
                $query->where('uid', $value);
            }
        }
    }

    /**
     * is_cancel
     * @param Model $query
     * @param $value
     */
    public function searchIsCancelAttr($query, $value)
    {
        if ($value !== '' && !is_null($value)) $query->where('is_cancel', $value);
    }

    /**
     * Bộ lọc is_del
     * @param Model $query
     * @param $value
     */
    public function searchIsDelAttr($query, $value)
    {
        if ($value !== '' && !is_null($value)) $query->where('is_del', $value);
    }

    /**
     * Bộ lọc is_system_del
     * @param Model $query
     * @param $value
     */
    public function searchIsSystemDelAttr($query, $value)
    {
        if ($value !== '' && !is_null($value)) $query->where('is_system_del', $value);
    }

    /**
     * refund_type
     * @param $query
     * @param $value
     */
    public function searchRefundTypeAttr($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('refund_type', $value);
        } else {
            if ($value > 0) $query->where('refund_type', $value);
        }
    }

    /**
     * @param $query
     * @param $value
     */
    public function searchRefundStatusAttr($query, $value)
    {
        if ($value == 1) {
            $query->whereIn('refund_type', [1, 2, 4, 5]);
        } elseif ($value == 2) {
            $query->where('refund_type', 6);
        }
    }

    /**
     * Liên kết một-một với bảng đơn hàng
     * @return StoreOrderRefund|\think\model\relation\HasOne
     */
    public function orderData()
    {
        return $this->hasOne(StoreOrder::class, 'id', 'store_order_id')->field('id, order_id, pay_type, paid, real_name,user_phone, user_address,pay_uid, pay_time')
            ->bind([
                'store_order_sn' => 'order_id',
                'pay_type',
                'paid',
                'real_name',
                'user_phone',
                'user_address',
                'pay_uid',
                'pay_time'
            ]);
    }

    /**
     * @param $query
     * @param $value
     */
    public function searchKeywordsAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('order_id|refund_phone', 'like', '%' . $value . '%');
        }
    }
}
