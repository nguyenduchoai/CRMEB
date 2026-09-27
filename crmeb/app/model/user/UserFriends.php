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


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

class UserFriends extends BaseModel
{

    use ModelTrait;

    /**
     * Tên bảng
     * @var string
     */
    protected $name = 'user_friends';

    /**
     * Khóa chính
     * @var string
     */
    protected $pk = 'id';

    /**
     *
     * @return \think\model\relation\HasOne
     */
    public function level()
    {
        return $this->hasOne(User::class, 'uid', 'uid')->field(['uid', 'level'])->bind([
            'level' => 'level'
        ]);
    }

    /**
     * @return \think\model\relation\HasOne
     */
    public function nickname()
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
     * Cập nhật thời gian thêm
     * @param $value
     * @return false|string
     */
    public function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}
