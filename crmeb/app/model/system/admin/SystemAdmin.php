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

namespace app\model\system\admin;

use crmeb\basic\BaseModel;
use crmeb\traits\JwtAuthModelTrait;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Model quản trị viên
 * Class SystemAdmin
 * @package app\model\system\admin
 */
class SystemAdmin extends BaseModel
{
    use ModelTrait;
    use JwtAuthModelTrait;

    /**
     * Khóa chính bảng dữ liệu
     * @var string
     */
    protected $pk = 'id';

    /**
     * Tên model
     * @var string
     */
    protected $name = 'system_admin';

    protected $insert = ['add_time'];

    /**
     * Dữ liệu quyền
     * @param $value
     * @return false|string[]
     */
    public static function getRolesAttr($value)
    {
        return explode(',', $value);
    }

    /**
     * Bộ lọc cấp quản trị viên
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchLevelAttr($query, $value)
    {
        if (is_array($value)) {
            $query->where('level', $value[0], $value[1]);
        } else {
            $query->where('level', $value);
        }
    }

    /**
     * Bộ lọc tài khoản và họ tên quản trị viên
     * @param Model $query
     * @param $value
     */
    public function searchAccountLikeAttr($query, $value)
    {
        if ($value) {
            $query->whereLike('account|real_name', '%' . $value . '%');
        }
    }

    /**
     * Bộ lọc tài khoản quản trị viên
     * @param Model $query
     * @param $value
     */
    public function searchAccountAttr($query, $value)
    {
        if ($value) {
            $query->where('account', $value);
        }
    }

    /**
     * Bộ lọc quyền quản trị viên
     * @param Model $query
     * @param $roles
     */
    public function searchRolesAttr($query, $roles)
    {
        if ($roles) {
            $query->where("CONCAT(',',roles,',')  LIKE '%,$roles,%'");
        }
    }

    /**
     * Bộ lọc đã xóa hay chưa
     * @param Model $query
     * @param $value
     */
    public function searchIsDelAttr($query)
    {
        $query->where('is_del', 0);
    }

    /**
     * Bộ lọc trạng thái
     * @param Model $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        if ($value != '' && $value != null) {
            $query->where('status', $value);
        }
    }

}
