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
declare (strict_types=1);

namespace app\model\agent;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Nhiệm vụ hạng cộng tác viên
 * Class AgentLevelTask
 * @package app\model\agent
 */
class AgentLevelTask extends BaseModel
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
    protected $name = 'agent_level_task';

    /**
     * Liên kết hạng cộng tác viên
     * @return \think\model\relation\HasOne
     */
    public function level()
    {
        return $this->hasOne(AgentLevel::class, 'id', 'level_id');
    }

    /**
     * Liên kết lịch sử hoàn thành nhiệm vụ
     * @return \think\model\relation\HasMany
     */
    public function record()
    {
        return $this->hasMany(AgentLevelTaskRecord::class, 'task_id', 'id');
    }

    /**
     * Bộ lọc từ khóa
     * @param $query Model
     * @param $value
     */
    public function searchKeywordAttr($query, $value)
    {
        if ($value !== '') $query->where('id|name|desc', 'like', '%' . $value . '%');
    }

    /**
     * Bộ lọc loại nhiệm vụ
     * @param $query Model
     * @param $value
     */
    public function searchTypeAttr($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('type', $value);
        } else {
            if ($value !== '') $query->where('type', $value);
        }

    }

    /**
     * Bộ lọc hạng cộng tác viên
     * @param $query Model
     * @param $value
     */
    public function searchLevelIdAttr($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('Level_id', $value);
        } else {
            if ($value !== '') $query->where('Level_id', $value);
        }

    }


    /**
     * Bộ lọc trạng thái
     * @param $query Model
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        if ($value !== '') $query->where('status', $value);
    }

    /**
     * Bộ lọc đã xóa hay chưa
     * @param $query Model
     * @param $value
     */
    public function searchIsDelAttr($query, $value)
    {
        if ($value !== '') $query->where('is_del', $value);
    }
}
