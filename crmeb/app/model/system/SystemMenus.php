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

namespace app\model\system;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Model quy tắc menu
 * Class SystemMenus
 * @package app\model\system
 */
class SystemMenus extends BaseModel
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
    protected $name = 'system_menus';

    /**
     * Setter tham số
     * @param $value
     * @return false|string
     */
    public function setParamsAttr($value)
    {
        $value = $value ? explode('/', $value) : [];
        $params = array_chunk($value, 2);
        $data = [];
        foreach ($params as $param) {
            if (isset($param[0]) && isset($param[1])) $data[$param[0]] = $param[1];
        }
        return json_encode($data);
    }

    /**
     * Getter tham số
     * @param $_value
     * @return mixed
     */
    public function getParamsAttr($_value)
    {
        return json_decode($_value, true);
    }

    /**
     * Getter pid
     * @param $value
     * @return mixed|string
     */
    public function getPidStrAttr($value)
    {
        return !$value ? 'Cấp cao nhất' : $this->where('pid', $value)->value('menu_name');
    }

    /**
     * Bộ truy vấn điều kiện mặc định
     * @param Model $query
     * @param $value
     */
    public function searchDefaultAttr($query)
    {
        $query->where(['is_show' => 1, 'access' => 1]);
    }

    /**
     * Bộ lọc có hiển thị hay không
     * @param Model $query
     * @param $value
     */
    public function searchIsShowAttr($query, $value)
    {
        if ($value != '') {
            $query->where('is_show', $value);
        }
    }

    /**
     * Bộ lọc đã xóa hay chưa
     * @param Model $query
     * @param $value
     */
    public function searchIsDelAttr($query, $value)
    {
        $query->where('is_del', $value);
    }

    /**
     * Bộ lọc Pid
     * @param Model $query
     * @param $value
     */
    public function searchPidAttr($query, $value)
    {
        $query->where('pid', $value ?? 0);
    }

    /**
     * Bộ lọc phân loại
     * @param Model $query
     * @param $value
     */
    public function searchRuleAttr($query, $value)
    {
        $query->whereIn('id', $value)->where('is_del', 0)->whereOr('pid', 0);
    }

    /**
     * Tìm kiếm menu
     * @param Model $query
     * @param $value
     */
    public function searchKeywordAttr($query, $value)
    {
        if ($value != '') {
            $query->whereLike('menu_name|id|pid', "%$value%");
        }
    }

    /**
     * Bộ lọc phương thức
     * @param Model $query
     * @param $value
     */
    public function searchActionAttr($query, $value)
    {
        $query->where('action', $value);
    }

    /**
     * Bộ lọc controller
     * @param Model $query
     * @param $value
     */
    public function searchControllerAttr($query, $value)
    {
        $query->where('controller', lcfirst($value));
    }

    /**
     * Bộ lọc địa chỉ truy cập
     * @param Model $query
     * @param $value
     */
    public function searchUrlAttr($query, $value)
    {
        $query->where('api_url', $value);
    }

    /**
     * Bộ lọc tham số
     * @param Model $query
     * @param $value
     */
    public function searchParamsAttr($query, $value)
    {
        $query->where(function ($query) use ($value) {
            $query->where('params', $value)->whereOr('params', "'[]'");
        });
    }

    /**
     * Bộ lọc định danh quyền
     * @param Model $query
     * @param $value
     */
    public function searchUniqueAttr($query, $value)
    {
        $query->where('is_del', 0);
        if ($value) {
            $query->whereIn('id', $value);
        }
    }

    /**
     * Tìm kiếm quy cách menu
     * @param Model $query
     * @param $value
     */
    public function searchRouteAttr($query, $value)
    {
        $query->where('auth_type', 1)->where('is_del', 0);
        if ($value) {
            $query->whereIn('id', $value);
        }
    }

    /**
     * Bộ lọc Id
     * @param Model $query
     * @param $value
     */
    public function searchIdAttr($query, $value)
    {
        $query->whereIn('id', $value);
    }

    /**
     * is_show_path
     * @param Model $query
     * @param $value
     */
    public function searchIsShowPathAttr($query, $value)
    {
        $query->where('is_show_path', $value);
    }

    /**
     * auth_type
     * @param Model $query
     * @param $value
     */
    public function searchAuthTypeAttr($query, $value)
    {
        if ($value !== '') {
            if ($value == 3) {
                $query->whereIn('auth_type', [1, 3]);
            } else {
                $query->where('auth_type', $value);
            }
        }
    }

    /**
     * Kiểm tra module
     * @param Model $query
     * @param $value
     */
    public function searchNoModelAttr($query, $value)
    {
        $query->when(!in_array('seckill', $value), function ($q1) {
            $q1->whereNotLike('menu_name', '%flash sale%');
        })->when(!in_array('bargain', $value), function ($q2) {
            $q2->whereNotLike('menu_name', '%săn giảm giá%');
        })->when(!in_array('combination', $value), function ($q3) {
            $q3->whereNotLike('menu_name', '%mua chung%');
        });
    }
}
