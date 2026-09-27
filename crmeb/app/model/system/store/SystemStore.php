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

namespace app\model\system\store;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Danh sách cửa hàng
 * Class SystemStore
 * @package app\model\system\store
 */
class SystemStore extends BaseModel
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
    protected $name = 'system_store';

    /**
     * Getter kinh độ vĩ độ
     * @param $value
     * @param $data
     * @return string
     */
    public static function getLatlngAttr($value, $data)
    {
        return $data['latitude'] . ',' . $data['longitude'];
    }

    /**
     * Bộ lọc loại cửa hàng
     * @param Model $query
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        switch ((int)$value) {
            case 1:
                $query->where(['is_del' => 0, 'is_show' => 0]);
                break;
            case 0:
                $query->where(['is_del' => 0, 'is_show' => 1]);
                break;
            default:
                $query->where('is_del', 1);
                break;
        }
    }

    /**
     * Bộ lọc số điện thoại, id, biệt danh
     * @param Model $query
     * @param $value
     */
    public function searchKeywordsAttr($query, $value)
    {
        if ($value) {
            $query->where('id|name|introduction|phone', 'LIKE', "%$value%");
        }
    }
}
