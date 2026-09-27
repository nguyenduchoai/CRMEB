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
declare (strict_types=1);

namespace app\model\activity\lottery;

use app\model\user\User;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 *
 * Class LuckLotteryRecordDao
 * @package app\model\activity\lottery
 */
class LuckLotteryRecord extends BaseModel
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
    protected $name = 'luck_lottery_record';

    /**
     * Setter thông tin nhận hàng
     * @param $value
     * @return false|string
     */
    protected function setReceiveInfoAttr($value)
    {
        if ($value) {
            return is_array($value) ? json_encode($value) : $value;
        }
        return '';
    }

    /**
     * Getter thông tin nhận hàng
     * @param $value
     * @param $data
     * @return mixed
     */
    protected function getReceiveInfoAttr($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Setter thông tin giao hàng
     * @param $value
     * @return false|string
     */
    protected function setDeliverInfoAttr($value)
    {
        if ($value) {
            return is_array($value) ? json_encode($value) : $value;
        }
        return '';
    }

    /**
     * Getter thông tin giao hàng
     * @param $value
     * @param $data
     * @return mixed
     */
    protected function getDeliverInfoAttr($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Liên kết quay thưởng
     * @return \think\model\relation\HasOne
     */
    public function lottery()
    {
        return $this->hasOne(LuckLottery::class, 'id', 'lottery_id');
    }

    /**
     * Liên kết giải thưởng
     * @return \think\model\relation\HasOne
     */
    public function prize()
    {
        return $this->hasOne(LuckPrize::class, 'id', 'prize_id');
    }

    /**
     * Người dùng liên kết
     * @return \think\model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid')->field('uid,real_name,nickname,phone');
    }

    /**
     * Bộ lọc uid người dùng
     * @param $query Model
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        if ($value) $query->where('uid', $value);
    }

    /**
     * Bộ lọc từ khóa
     * @param $query Model
     * @param $value
     */
    public function searchKeywordAttr($query, $value)
    {
        if ($value !== '') {
            $query->where(function ($query1) use ($value) {
                $query1->where('id|uid|lottery_id|prize_id', 'LIKE', '%' . $value . '%')->whereOr('uid', 'IN', function ($query1) use ($value) {
                    $query1->name('user')->field('uid')->where('account|nickname|phone|real_name|uid', 'LIKE', "%$value%")->select();
                })->whereOr('lottery_id', 'IN', function ($query1) use ($value) {
                    $query1->name('luck_lottery')->field('id')->where('name|desc|content', 'LIKE', "%$value%")->select();
                })->whereOr('prize_id', 'IN', function ($query1) use ($value) {
                    $query1->name('luck_prize')->field('id')->where('name|prompt', 'LIKE', "%$value%")->select();
                });
            });
        }
    }

    /**
     * Bộ lọc id quay thưởng
     * @param $query Model
     * @param $value
     */
    public function searchLotteryIdAttr($query, $value)
    {
        if ($value !== '') $query->where('lottery_id', $value);
    }

    /**
     * Bộ lọc id giải thưởng
     * @param $query Model
     * @param $value
     */
    public function searchPrizeIdAttr($query, $value)
    {
        if ($value) $query->where('prize_id', $value);
    }

    /**
     * Bộ lọc loại giải thưởng
     * @param $query Model
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        if ($value) {
            if (is_array($value)) {
                $query->whereIn('type', $value);
            } else {
                $query->where('type', $value);
            }
        }
    }

    /**
     * Bộ lọc giải thưởng không thuộc loại này
     * @param $query Model
     * @param $value
     */
    public function searchNotTypeAttr($query, $value)
    {
        if ($value) {
            if (is_array($value)) {
                $query->whereNotIn('type', $value);
            } else {
                $query->where('type', '<>', $value);
            }
        }
    }

    /**
     * Đã nhận hay chưa
     * @param $query Model
     * @param $value
     */
    public function searchIsReceiveAttr($query, $value)
    {
        if ($value !== '') $query->where('is_reveive', $value);
    }

    /**
     * Có xử lý giao hàng hay không
     * @param $query Model
     * @param $value
     */
    public function searchIsDeliverAttr($query, $value)
    {
        if ($value !== '') $query->where('is_deliver', $value);
    }
}
