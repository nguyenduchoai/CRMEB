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

namespace app\model\system\statistics;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

class CapitalFlow extends BaseModel
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
    protected $name = 'capital_flow';

    /**
     * Bộ lọc loại giao dịch
     * @param $query
     * @param $value
     */
    public function searchTradingTypeAttr($query, $value)
    {
        if ($value) $query->where('trading_type', $value);
    }

    /**
     * Bộ lọc từ khóa
     * @param $query
     * @param $value
     */
    public function searchKeywordsAttr($query, $value)
    {
        if ($value !== '') $query->where('order_id|uid|nickname|phone', 'like', '%' . $value . '%');
    }

    /**
     * Bộ lọc id theo lô
     * @param $query
     * @param $value
     */
    public function searchIdsAttr($query, $value)
    {
        if ($value != '') $query->whereIn('id', $value);
    }
}
