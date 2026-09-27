<?php

namespace app\model\system\lang;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class LangCode extends BaseModel
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
    protected $name = 'lang_code';

    /**
     * Bộ lọc type_id
     * @param $query
     * @param $value
     */
    public function searchTypeIdAttr($query, $value)
    {
        if ($value !== '' && $value !== 0) $query->where('type_id', $value);
    }

    /**
     * Bộ lọc code
     * @param $query
     * @param $value
     */
    public function searchCodeAttr($query, $value)
    {
        if ($value !== '') $query->where('code', 'like', '%' . $value . '%');
    }

    /**
     * Bộ lọc remarks
     * @param $query
     * @param $value
     */
    public function searchRemarksAttr($query, $value)
    {
        if ($value !== '') $query->where('remarks|code|lang_explain', 'like', '%' . $value . '%');
    }

    /**
     * Bộ lọc is_admin
     * @param $query
     * @param $value
     */
    public function searchIsAdminAttr($query, $value)
    {
        if ($value !== '') $query->where('is_admin', $value);
    }
}