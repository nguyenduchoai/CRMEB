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


/**
 * Class StorePink
 * @package app\model\order
 */
class StorePink extends BaseModel
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
    protected $name = 'store_pink';

    protected $insert = ['add_time'];

    /**
     * Setter thời gian tạo
     * @return int
     */
    protected function setAddTimeAttr()
    {
        return time();
    }
    /**
     * Bộ lọc id đơn hàng mua chung
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchOrderIdKeyAttr($query, $value, $data)
    {
        $query->whereIn('order_id_key', $value);
    }


}
