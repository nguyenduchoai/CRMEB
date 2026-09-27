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

namespace app\model\user;

use app\model\agent\AgentLevel;
use app\model\order\StoreOrder;
use app\model\system\SystemUserLevel;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Class User
 * @package app\model\user
 */
class User extends BaseModel
{
    use ModelTrait;

    /**
     * @var string
     */
    protected $pk = 'uid';

    protected $name = 'user';

    protected $insert = ['add_time', 'add_ip', 'last_time', 'last_ip'];

    protected $hidden = [
        'add_ip', 'account', 'clean_time', 'last_ip', 'pwd'
    ];

    /**
     * Tự động chuyển loại
     * @var string[]
     */
    protected $type = [
        'birthday' => 'int'
    ];

    protected $updateTime = false;

    protected function setAddTimeAttr($value)
    {
        return time();
    }

    protected function setAddIpAttr($value)
    {
        return app('request')->ip();
    }

    protected function setLastTimeAttr($value)
    {
        return time();
    }

    protected function setLastIpAttr($value)
    {
        return app('request')->ip();
    }

//    protected function getPhoneAttr($value)
//    {
//        return $value && app('request')->hasMacro('adminInfo') && app('request')->adminInfo()['level'] != 0 ? substr_replace($value, '****', 3, 4) : $value;
//    }

    /**
     * Liên kết bảng cấu hình đăng nhập thành viên
     * @return \think\model\relation\HasOne
     */
    public function systemUserLevel()
    {
        return $this->hasOne(SystemUserLevel::class, 'id', 'level');
    }

    /**
     * Liên kết nhóm người dùng
     * @return \think\model\relation\HasOne
     */
    public function userGroup()
    {
        return $this->hasOne(UserGroup::class, 'id', 'group_id');
    }

    /**
     * Liên kết bản thân
     * @return \think\model\relation\HasOne
     */
    public function spreadUser()
    {
        return $this->hasOne(self::class, 'uid', 'spread_uid');
    }

    /**
     * Liên kết bản thân
     * @return \think\model\relation\HasOne
     */
    public function spreadCount()
    {
        return $this->hasMany(User::class, 'spread_uid', 'uid');
    }

    /**
     * Liên kết quan hệ nhãn người dùng
     * @return \think\model\relation\HasMany
     */
    public function LabelRelation()
    {
        return $this->hasMany(UserLabelRelation::class, 'uid', 'uid');
    }

    /**
     * Liên kết nhãn người dùng
     * @return \think\model\relation\HasManyThrough
     */
    public function label()
    {
        return $this->hasManyThrough(UserLabel::class, UserLabelRelation::class, 'uid', 'id', 'uid', 'label_id');
    }

    /**
     * Liên kết địa chỉ người dùng
     * @return \think\model\relation\HasMany
     */
    public function address()
    {
        return $this->hasMany(UserAddress::class, 'uid', 'uid');
    }

    /**
     * Liên kết rút tiền
     * @return \think\model\relation\HasMany
     */
    public function extract()
    {
        return $this->hasMany(UserExtract::class, 'uid', 'uid');
    }

    /**
     * Đơn hàng liên quan
     * @return User|\think\model\relation\HasMany
     */
    public function order()
    {
        return $this->hasMany(StoreOrder::class, 'uid', 'uid');
    }

    /**
     * Liên kết hạng cộng tác viên
     * @return \think\model\relation\HasOne
     */
    public function agentLevel()
    {
        return $this->hasOne(AgentLevel::class, 'id', 'agent_level')->where('is_del', 0)->where('status', 1);
    }

    /**
     * Liên kết dữ liệu hoa hồng
     * @return \think\model\relation\HasMany
     */
    public function bill()
    {
        return $this->hasMany(UserBill::class, 'uid', 'uid');
    }

    /**
     * uid người dùng
     * @param Model $query
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        if (is_array($value))
            $query->whereIn('uid', $value);
        else
            $query->where('uid', $value);
    }

    /**
     * Bộ lọc tài khoản
     * @param Model $query
     * @param $value
     */
    public function searchAccountAttr($query, $value)
    {
        $query->where('account', $value);
    }

    /**
     * Bộ lọc mật khẩu
     * @param Model $query
     * @param $value
     */
    public function searchPwdAttr($query, $value)
    {
        $query->where('pwd', $value);
    }

    /**
     * Bộ lọc truy vấn theo phạm vi uid
     * @param Model $query
     * @param $value
     */
    public function searchUidsAttr($query, $value)
    {
        $query->whereIn('uid', $value);
    }

    /**
     * Bộ lọc điều kiện gần đúng
     * @param Model $query
     * @param $value
     */
    public function searchLikeAttr($query, $value)
    {
        $query->where('account|nickname|phone|real_name|uid', 'like', '%' . $value . '%');
    }

    /**
     * Bộ lọc số điện thoại
     * @param Model $query
     * @param $value
     */
    public function searchPhoneAttr($query, $value)
    {
        $query->where('phone', $value);
    }

    /**
     * Bộ lọc nhóm
     * @param Model $query
     * @param $value
     */
    public function searchGroupIdAttr($query, $value)
    {
        $query->where('group_id', $value);
    }

    /**
     * Bộ lọc có phải người giới thiệu hay không
     * @param Model $query
     * @param $value
     */
    public function searchIsPromoterAttr($query, $value)
    {
        $query->where('is_promoter', $value);
    }

    /**
     * Bộ lọc trạng thái
     * @param Model $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        $query->where('status', $value);
    }

    /**
     * Bộ lọc hạng thành viên
     * @param Model $query
     * @param $value
     */
    public function searchLevelAttr($query, $value)
    {
        $query->where('level', $value);
    }

    /**
     * Bộ lọc uid người giới thiệu
     * @param Model $query
     * @param $value
     */
    public function searchSpreadUidAttr($query, $value)
    {
        $query->where('spread_uid', $value);
    }

    /**
     * Bộ lọc uid người giới thiệu không bằng
     * @param Model $query
     * @param $value
     */
    public function searchNotSpreadUidAttr($query, $value)
    {
        $query->where('spread_uid', '<>', $value);
    }

    /**
     * Bộ lọc thời gian người giới thiệu
     * @param Model $query
     * @param $value
     */
    public function searchSpreadTimeAttr($query, $value)
    {
        if ($value) {
            if (is_array($value)) {
                if (count($value) == 2) $query->where('spread_time', $value[0], $value[1]);
            } else {
                $query->where('spread_time', $value);
            }
        }
    }

    /**
     * Bộ lọc loại người dùng
     * @param Model $query
     * @param $value
     */
    public function searchUserTypeAttr($query, $value)
    {
        if ($value != '') $query->where('user_type', $value);
    }

    /**
     * Bộ lọc số lần mua
     * @param Model $query
     * @param $value
     */
    public function searchPayCountAttr($query, $value)
    {
        $query->where('pay_count', $value);
    }

    /**
     * Tư cách giới thiệu của người dùng
     * @param Model $query
     * @param $value
     */
    public function searchSpreadOpenAttr($query, $value)
    {
        if ($value != '') $query->where('spread_open', $value);
    }

    /**
     * Bộ lọc nickname
     * @param $query
     * @param $value
     */
    public function searchNicknameAttr($query, $value)
    {
        $query->where('nickname', "like", "%" . $value . "%");
    }

    /**
     * Bộ lọc division_type
     * @param $query
     * @param $value
     */
    public function searchDivisionTypeAttr($query, $value)
    {
        if ($value !== '') $query->where('division_type', $value);
    }

    /**
     * Bộ lọc division_id
     * @param $query
     * @param $value
     */
    public function searchDivisionIdAttr($query, $value)
    {
        if ((int)$value !== 0) $query->where('division_id', $value);
    }

    /**
     * Bộ lọc agent_id
     * @param $query
     * @param $value
     */
    public function searchAgentIdAttr($query, $value)
    {
        if ($value !== '') $query->where('agent_id', $value);
    }

    /**
     * Bộ lọc staff_id
     * @param $query
     * @param $value
     */
    public function searchStaffIdAttr($query, $value)
    {
        if ($value !== '') $query->where('staff_id', $value);
    }

    /**
     * Bộ lọc is_division
     * @param $query
     * @param $value
     */
    public function searchIsDivisionAttr($query, $value)
    {
        if ($value !== '') $query->where('is_division', $value);
    }

    /**
     * Bộ lọc is_agent
     * @param $query
     * @param $value
     */
    public function searchIsAgentAttr($query, $value)
    {
        if ($value !== '') $query->where('is_agent', $value);
    }

    /**
     * Bộ lọc is_staff
     * @param $query
     * @param $value
     */
    public function searchIsStaffAttr($query, $value)
    {
        if ($value !== '') $query->where('is_staff', $value);
    }

    /**
     * @param $query
     * @param $value
     */
    public function searchKeywordAttr($query, $value)
    {
        if ($value !== '') $query->where('uid|nickname', 'like', '%' . $value . '%');
    }

    /**
     * Bộ lọc đã hủy tài khoản
     * @param $query
     * @param $value
     */
    public function searchIsDelAttr($query, $value)
    {
        if ($value !== '') $query->where('is_del', $value);
    }

    /**
     * Bộ lọc không bằng uid
     * @param $query
     * @param $value
     */
    public function searchNotUidAttr($query, $value)
    {
        if ($value !== '') $query->where('uid', '<>', $value);
    }
}
