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


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

class MemberCard extends BaseModel
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
    protected $name = 'member_card';

    protected $insert = ['add_time', 'update_time'];

    protected $hidden = ['update_time', 'add_time'];

    protected $updateTime = false;

    /**
     * Bộ lọc số thẻ
     * @param Model $query
     * @param $value
     */
    public function searchCardNumberAttr($query, $value)
    {
        if ($value) {
            $query->whereLike('card_number', '%' . $value . '%');
        }

    }

    /**
     * Bộ lọc uid người dùng
     * @param Model $query
     * @param $value
     */
    public function searchUseUidAttr($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('use_uid', $value);
        } else {
            $query->where('use_uid', $value);
        }
    }

    /**
     * Bộ lọc số điện thoại
     * @param Model $query
     * @param $value
     */
    public function searchPhoneAttr($query, $value)
    {
        if ($value) {
            $query->whereIn('use_uid', function ($query) use ($value) {
                $query->name('user')->whereLike('phone', $value . '%')->field('uid')->select();
            });
        }
    }

    /**
     * Bộ lọc id lô
     * @param Model $query
     * @param $value
     */
    public function searchBatchCardIdAttr($query, $value)
    {
        $query->where('card_batch_id', $value);
    }

    /**
     * Bộ lọc use_time người dùng
     * @param Model $query
     * @param $value
     */
    public function searchUseTimeAttr($query, $value)
    {
        if ($value > 0) {
            $query->where('use_time', '>', 0);
        }
        if ($value == 0) {
            $query->where('use_time', 0);
        }

    }

    public function searchIsStatusAttr($query, $value)
    {
        if ($value) {
            $query->where('status', $value);
        }

    }
}
