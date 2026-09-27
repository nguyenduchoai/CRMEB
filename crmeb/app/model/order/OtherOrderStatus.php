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

namespace app\model\order;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/** TODO Model lịch sử thay đổi trạng thái đơn hàng
 * Class OtherOrderStatus
 * @package app\model\order
 */
class OtherOrderStatus extends BaseModel
{
    use ModelTrait;

    /**
     * Tên model
     * @var string
     */
    protected $name = 'other_order_status';

    protected $autoWriteTimestamp = 'int';

    protected $createTime = 'change_time';

    /**
     * Bộ lọc ID đơn hàng
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchOidAttr($query, $value, $data)
    {
        $query->where('oid', $value);
    }

    /**
     * Bộ lọc loại thay đổi
     * @param Model $query
     * @param $value
     */
    public function searchChangeTypeAttr($query, $value)
    {
        $query->where('change_type', $value);
    }
}
