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
declare (strict_types=1);

namespace app\model\service;

use app\model\other\Category;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Câu trả lời mẫu CSKH
 * @mixin Model
 */
class StoreServiceSpeechcraft extends BaseModel
{
    use ModelTrait;

    /**
     * Tên bảng
     * @var string
     */
    protected $name = 'store_service_speechcraft';

    /**
     * Khóa chính
     * @var string
     */
    protected $pk = 'id';

    /**
     * Định dạng thời gian
     * @param $value
     * @param $data
     * @return false|string
     */
    public function getAddTimeAttr($value, $data)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * Liên kết danh mục nhãn
     * @return \think\model\relation\HasOne
     */
    public function cateName()
    {
        return $this->hasOne(Category::class, 'id', 'cate_id')->where('type', 1)->field(['id', 'name'])->bind(['cate_name' => 'name']);
    }

    /**
     * Tìm kiếm mẫu câu
     * @param Model $query
     * @param $value
     */
    public function searchTitleAttr($query, $value)
    {
        if ($value !== '') $query->whereLike('title', '%' . $value . '%');
    }

    /**
     * Tìm kiếm theo CSKH phụ trách
     * @param Model $query
     * @param $value
     */
    public function searchKefuIdAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('kefu_id', $value);
        }
    }

    /**
     * Tìm kiếm theo danh mục
     * @param Model $query
     * @param $value
     */
    public function searchCateIdAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('cate_id', $value);
        }
    }

    /**
     * @param Model $query
     * @param $value
     */
    public function searchMessageAttr($query, $value)
    {
        if ($value !== '') $query->where('message', $value);

    }
}
