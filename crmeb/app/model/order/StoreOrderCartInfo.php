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

namespace app\model\order;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * TODO Model lịch sử đơn hàng
 * Class StoreOrderCartInfo
 * @package app\model\order
 */
class StoreOrderCartInfo extends BaseModel
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
    protected $name = 'store_order_cart_info';

    /**
     * Getter thông tin giỏ hàng
     * @param $value
     * @return array|mixed
     */
    public function getCartInfoAttr($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Bộ lọc ID đơn hàng
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchOidAttr($query, $value, $data)
    {
        if ($value !== '') $query->where('oid', $value);
    }

    /**
     * Bộ lọc ID giỏ hàng
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchCartIdAttr($query, $value, $data)
    {
        if (is_array($value)) {
            $query->whereIn('cart_id', $value);
        } else {
            $query->where('cart_id', $value);
        }
    }

    /**
     * Bộ lọc ID giỏ hàng gốc
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchOldCartIdAttr($query, $value, $data)
    {
        if (is_array($value)) {
            $query->whereIn('old_cart_id', $value);
        } else {
            $query->where('old_cart_id', $value);
        }
    }

    /**
     *  Bộ lọc trạng thái tách đơn
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchSplitStatusAttr($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('split_status', $value);
        } else {
            if (in_array($value, [0, 1, 2])) {
                $query->where('split_status', $value);
            }
        }
    }
}
