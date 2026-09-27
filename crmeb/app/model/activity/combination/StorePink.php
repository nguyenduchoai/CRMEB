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

namespace app\model\activity\combination;

use app\model\user\User;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * TODO Model mua chung
 * Class StorePink
 * @package app\model\activity
 */
class StorePink extends BaseModel
{
    /**
     * Khóa chính bảng dữ liệu
     * @var string
     */
    protected $pk = 'id';

    /**
     * Tên model
     * @var string
     */
    protected $name = 'store_pink';

    use ModelTrait;

    /**
     * Liên kết một-một với người dùng
     * @return \think\model\relation\HasOne
     */
    public function getUser()
    {
        return $this->hasOne(User::class, 'uid', 'uid')->bind(['nickname', 'avatar']);
    }

    public function getProduct()
    {
        return $this->hasOne(StoreCombination::class, 'id', 'cid')->bind(['title']);
    }

    /**
     * Bộ lọc mã đơn hàng
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchOrderIdAttr($query, $value, $data)
    {
        $query->where('order_id', $value);
    }

    /**
     * Bộ lọc mã đơn hàng
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchOrderIdKeyAttr($query, $value, $data)
    {
        $query->where('order_id_key', $value);
    }

    /**
     * Bộ lọc ID sản phẩm mua chung
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchCidAttr($query, $value, $data)
    {
        $query->where('cid', $value);
    }

    /**
     * Bộ lọc ID sản phẩm
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchPidAttr($query, $value, $data)
    {
        $query->where('pid', $value);
    }

    /**
     * Bộ lọc có là trưởng nhóm hay không
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchKIdAttr($query, $value, $data)
    {
        $query->where('k_id', $value);
    }

    /**
     * Bộ lọc có hoàn tiền hay không
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsRefundAttr($query, $value, $data)
    {
        $query->where('is_refund', $value ?? 0);
    }

    /**
     * Bộ lọc trạng thái
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchStatusAttr($query, $value, $data)
    {
        if ($value != '') $query->where('status', $value);
    }
}
