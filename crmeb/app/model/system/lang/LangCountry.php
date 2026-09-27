<?php

namespace app\model\system\lang;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class LangCountry extends BaseModel
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
    protected $name = 'lang_country';

    /**
     * Bộ lọc type_id
     * @param $query
     * @param $value
     */
    public function searchTypeIdAttr($query, $value)
    {
        if ($value !== '') $query->where('type_id', $value);
    }

    /**
     * Bộ lọc code/name
     * @param $query
     * @param $value
     */
    public function searchKeywordAttr($query, $value)
    {
        if ($value !== '') $query->where('name|code', 'like', '%' . $value . '%');
    }
}