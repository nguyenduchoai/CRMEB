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
 * Hạng cộng tác viên
 * Class AgentLevel
 * @package app\model\agent
 */
class AgentLevel extends BaseModel
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
    protected $name = 'agent_level';

    /**
     * Liên kết nhiệm vụ theo hạng
     * @return \think\model\relation\HasMany
     */
    public function task()
    {
        return $this->hasMany(AgentLevelTask::class, 'level_id', 'id')->where('is_del', 0);
    }

    /**
     * Tìm kiếm theo từ khóa
     * @param $query
     * @param $value
     */
    public function searchKeywordAttr($query, $value)
    {
        if ($value !== '') $query->whereLike('id|name', "%" . trim($value) . "%");
    }

    /**
     * Bộ lọc hạng
     * @param $query Model
     * @param $value
     */
    public function searchGradeAttr($query, $value)
    {
        if ($value !== '') $query->where('grade', $value);
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
