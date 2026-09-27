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

namespace app\model\service;


use app\model\user\User;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * CSKH
 * Class StoreService
 * @package app\model\service
 */
class StoreService extends BaseModel
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
    protected $name = 'store_service';

    /**
     * @var bool
     */
    protected $updateTime = false;


    protected function getAddTimeAttr($value)
    {
        if ($value) return date('Y-m-d H:i:s', $value);
        return $value;
    }

    /**
     * Liên kết một-nhiều với tên người dùng
     * @return mixed
     */
    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid')->field(['uid', 'nickname'])->bind([
            'nickname' => 'nickname'
        ]);
    }

    /**
     * Bộ lọc uid
     * @param Model $query
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        $query->where('uid', $value);
    }

    /**
     * Bộ lọc status
     * @param Model $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        $query->where('status', $value);
    }

    /**
     * Bộ lọc account
     * @param Model $query
     * @param $value
     */
    public function searchAccountAttr($query, $value)
    {
        $query->where('account', $value);
    }

    /**
     * Bộ lọc phone
     * @param Model $query
     * @param $value
     */
    public function searchPhoneAttr($query, $value)
    {
        $query->where('phone', $value);
    }

    /**
     * customer
     * @param Model $query
     * @param $value
     */
    public function searchCustomerAttr($query, $value)
    {
        $query->where('customer', $value);
    }

    /**
     * Bộ lọc biệt danh người dùng
     * @param Model $query
     * @param $value
     */
    public function searchNicknameAttr($query, $value)
    {
        $query->whereLike('nickname', '%' . $value . '%');
    }

    /**
     * Bộ lọc uid người dùng
     * @param Model $query
     * @param $value
     */
    public function searchNoUidAttr($query, $value)
    {
        if ($value) $query->whereNotIn('uid', $value);
    }

    /**
     * Bộ lọc CSKH đang online
     * @param $query
     * @param $value
     */
    public function searchOnlineAttr($query, $value)
    {
        if ($value) $query->where('online', $value);
    }
}
