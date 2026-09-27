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

namespace app\model\activity\bargain;

use app\model\user\User;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * TODO Model tham gia săn giảm giá
 * Class StoreBargainUser
 * @package app\model\activity
 */
class StoreBargainUser extends BaseModel
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
    protected $name = 'store_bargain_user';

    use ModelTrait;

    /**
     * Liên kết một-một
     * Sản phẩm liên kết chi tiết sản phẩm
     * @return \think\model\relation\HasOne
     */
    public function getBargain()
    {
        return $this->hasOne(StoreBargain::class, 'id', 'bargain_id')->bind(['title', 'image', 'datatime' => 'stop_time', 'people_num']);
    }

    /**
     * Liên kết một-một
     * Lấy người dùng săn giảm giá
     * @return \think\model\relation\HasOne
     */
    public function getUser()
    {
        return $this->hasOne(User::class, 'uid', 'uid')->bind(['avatar', 'nickname']);
    }

    /**
     * Bộ lọc người dùng
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchUidAttr($query, $value, $data)
    {
        $query->where('uid', $value);
    }

    /**
     * Bộ lọc ID sản phẩm
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchBargainIdAttr($query, $value, $data)
    {
        $query->where('bargain_id', $value);
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

    /**
     * Bộ lọc đã xóa hay chưa
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchIsDelAttr($query, $value, $data)
    {
        $query->where('is_del', $value);
    }
}
