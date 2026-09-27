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

namespace app\model\wechat;

use app\model\user\User;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * Class WechatUser
 * @package app\model\wechat
 */
class WechatUser extends BaseModel
{
    use ModelTrait;

    /**
     * Khóa chính bảng dữ liệu
     * @var string
     */
    protected $pk = 'uid';

    /**
     * Tên model
     * @var string
     */
    protected $name = 'wechat_user';

    protected $insert = ['add_time'];

    public static function setAddTimeAttr()
    {
        return time();
    }

    protected function getAddTimeAttr($value)
    {
        if ($value) return date('Y-m-d H:i', (int)$value);
        return $value;
    }

    /**
     * Liên kết user
     * @return \think\model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

    /**
     * Liên kết OA WeChat
     * @param Model $query
     * @param $value
     */
    public function searchUnionidAttr($query, $value)
    {
        return $query->where('unionid', $value);
    }

    /**
     * id duy nhất của OA WeChat
     * @param Model $query
     * @param $value
     */
    public function searchOpenidAttr($query, $value)
    {
        return $query->where('openid', $value);
    }

    /**
     * Nhóm
     * @param Model $query
     * @param $value
     */
    public function searchGroupIdAttr($query, $value)
    {
        return $query->where('group_id', $value);
    }

    /**
     * Giới tính
     * @param Model $query
     * @param $value
     */
    public function searchSexAttr($query, $value)
    {
        return $query->where('sex', $value);
    }

    /**
     * Có theo dõi không
     * @param Model $query
     * @param $value
     */
    public function searchSubscribeAttr($query, $value)
    {
        return $query->where('subscribe', $value);
    }

    /**
     * Loại người dùng
     * @param Model $query
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        return $query->where('user_type', $value);
    }

    /**
     * Loại người dùng
     * @param Model $query
     * @param $value
     */
    public function searchUserTypeAttr($query, $value)
    {
        return $query->where('user_type', $value);
    }

    /**
     * Bộ lọc is_del
     * @param $query
     * @param $value
     * @return void
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/03
     */
    public function searchIsDelAttr($query, $value)
    {
        if($value !== '') return $query->where('is_del', $value);
    }

}
