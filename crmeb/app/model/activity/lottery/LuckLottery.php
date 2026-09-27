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

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Hoạt động quay thưởng
 * Class LuckLottery
 * @package app\model\activity\lottery
 */
class LuckLottery extends BaseModel
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
    protected $name = 'luck_lottery';

    /**
     * Setter hạng người dùng quay thưởng
     * @param $value
     * @return false|string
     */
    protected function setUserLevelAttr($value)
    {
        if ($value) {
            return is_array($value) ? json_encode($value) : $value;
        }
        return '';
    }

    /**
     * Getter hạng người dùng quay thưởng
     * @param $value
     * @param $data
     * @return mixed
     */
    protected function getUserLevelAttr($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Setter nhãn người dùng quay thưởng
     * @param $value
     * @return false|string
     */
    protected function setUserLabelAttr($value)
    {
        if ($value) {
            return is_array($value) ? json_encode($value) : $value;
        }
        return '';
    }

    /**
     * Getter nhãn người dùng quay thưởng
     * @param $value
     * @param $data
     * @return mixed
     */
    protected function getUserLabelAttr($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Liên kết giải thưởng
     * @return \think\model\relation\HasOne
     */
    public function prize()
    {
        return $this->hasMany(LuckPrize::class, 'lottery_id', 'id')->where('status', 1)->where('is_del', 0)->order('sort asc,id asc');
    }

    /**
     * Bộ lọc từ khóa
     * @param $query Model
     * @param $value
     */
    public function searchKeywordAttr($query, $value)
    {
        if ($value !== '') $query->where('id|name|desc|content', 'like', '%' . $value . '%');
    }

    /**
     * Bộ lọc hình thức quay thưởng
     * @param $query Model
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        if ($value) $query->where('type', $value);
    }

    /**
     * Bộ lọc loại quay thưởng
     * @param $query Model
     * @param $value
     */
    public function searchFactorAttr($query, $value)
    {
        if ($value !== '') $query->where('factor', $value);
    }

    /**
     * Bộ lọc trạng thái
     * @param $query Model
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        if ($value !== '') $query->where('status', $value);
    }

    /**
     * Bộ lọc đã xóa hay chưa
     * @param $query Model
     * @param $value
     */
    public function searchIsDelAttr($query, $value)
    {
        if ($value !== '') $query->where('is_del', $value);
    }

    public function records()
    {
        return $this->hasMany(LuckLotteryRecord::class, 'lottery_id', 'id');
    }
}
