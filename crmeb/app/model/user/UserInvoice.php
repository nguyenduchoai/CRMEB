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

/**
 * Class UserInvoice
 * @package app\model\live
 */
class UserInvoice extends BaseModel
{
    use ModelTrait;

    protected $pk = 'id';

    protected $name = 'user_invoice';

    protected $autoWriteTimestamp = 'int';

    protected $createTime = 'add_time';

    protected function setAddTimeAttr()
    {
        return time();
    }

    /**
     * Getter thời gian thêm
     * @param $value
     * @return false|string
     */
    public function getAddTimeAttr($value)
    {
        if (!empty($value)) {
            return date('Y-m-d H:i:s', (int)$value);
        }
        return '';
    }


    /**
     * @param Model $query
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        if ($value !== '') $query->where('uid', $value);
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchHeaderTypeAttr($query, $value)
    {
        if ($value !== '') $query->where('header_type', $value);
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        if ($value !== '') $query->where('type', $value);
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchIsDefaultAttr($query, $value)
    {
        if ($value !== '') $query->whereLike('is_default', $value);
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchIsDelAttr($query, $value)
    {
        if ($value !== '') $query->where('is_del', $value);
    }

}
