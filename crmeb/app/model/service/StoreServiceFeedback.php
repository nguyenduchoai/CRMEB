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

namespace app\model\service;


use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Tin nhắn phản hồi CSKH
 * Class StoreServiceFeedback
 * @package app\model\service
 */
class StoreServiceFeedback extends BaseModel
{

    use ModelTrait;

    /**
     * @var string
     */
    protected $name = 'store_service_feedback';

    /**
     * @var string
     */
    protected $pk = 'id';

    /**
     * @param $value
     * @return false|string
     */
    public function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * Tìm kiếm theo tiêu đề
     * @param Model $query
     * @param $value
     */
    public function searchTitleAttr($query, $value)
    {
        $value && $query->whereLike('rela_name|phone|content|uid', "%" . $value . "%");
    }

}
