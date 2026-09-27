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
 * Class UserLabelRelation
 * @package app\model\user
 */
class UserLabelRelation extends BaseModel
{
    use ModelTrait;

    /**
     * Tên model
     * @var string
     */
    protected $name = 'user_label_relation';

    /**
     * @return \think\model\relation\HasOne
     */
    public function label()
    {
        return $this->hasOne(UserLabel::class, 'id', 'label_id')->bind([
            'label_name' => 'label_name'
        ]);
    }

    /**
     * Bộ lọc uid
     * @param Model $query
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        $query->whereIn('uid', $value);
    }
}
