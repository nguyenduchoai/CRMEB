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

namespace app\model\service;


use app\model\user\User;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Bản ghi người dùng trò chuyện với CSKH
 * Class StoreServiceRecord
 * @package app\model\service
 */
class StoreServiceRecord extends BaseModel
{
    use ModelTrait;

    protected $name = 'store_service_record';

    protected $pk = 'id';

    /**
     * Thời gian cập nhật
     * @var bool | string | int
     */
    protected $updateTime = false;

    /**
     * Liên kết người dùng
     * @return \think\model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'to_uid')->field(['nickname', 'uid', 'avatar'])->bind([
            'wx_nickname' => 'nickname',
            'wx_avatar' => 'avatar',
        ]);
    }

    /**
     * Người dùng CSKH
     * @return \think\model\relation\HasOne
     */
    public function service()
    {
        return $this->hasOne(StoreService::class, 'uid', 'to_uid')->field(['nickname', 'uid', 'avatar'])->bind([
            'kefu_nickname' => 'nickname',
            'kefu_avatar' => 'avatar',
        ]);
    }

    /**
     * Bộ lọc id người gửi
     * @param Model $query
     * @param $value
     */
    public function searchUserIdAttr($query, $value)
    {
        $query->where('user_id', $value);
    }

    /**
     * Bộ lọc uid người nhận
     * @param Model $query
     * @param $value
     */
    public function searchToUidAttr($query, $value)
    {
        $query->where('to_uid', $value);
    }

    /**
     * Bộ lọc biệt danh người dùng
     * @param Model $query
     * @param $value
     */
    public function searchTitleAttr($query, $value)
    {
        if ($value) {
            $query->whereIn('to_uid', function ($query) use ($value) {
                $query->name('user')->whereLike('nickname|uid', '%' . $value . '%')->field('uid');
            });
        }
    }

    /**
     * Có phải khách vãng lai hay không
     * @param Model $query
     * @param $value
     */
    public function searchIsTouristAttr($query, $value)
    {
        $query->where('is_tourist', $value);
    }
}
