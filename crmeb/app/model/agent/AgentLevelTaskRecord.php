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

namespace app\model\agent;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * Lịch sử hoàn thành nhiệm vụ hạng của cộng tác viên
 * Class AgentLevelTaskRecord
 * @package app\model\agent
 */
class AgentLevelTaskRecord extends BaseModel
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
    protected $name = 'agent_level_task_record';

    /**
     * Liên kết hạng cộng tác viên
     * @return \think\model\relation\HasOne
     */
    public function level()
    {
        return $this->hasOne(AgentLevel::class, 'id', 'level_id');
    }

    /**
     * Liên kết nhiệm vụ hạng cộng tác viên
     * @return \think\model\relation\HasOne
     */
    public function task()
    {
        return $this->hasOne(AgentLevelTask::class, 'id', 'task_id');
    }

    /**
     * Bộ lọc hạng cộng tác viên
     * @param $query Model
     * @param $value
     */
    public function searchLevelIdAttr($query, $value)
    {
        if ($value !== '') $query->where('level_id', $value);
    }

    /**
     * Bộ lọc nhiệm vụ hạng
     * @param $query Model
     * @param $value
     */
    public function searchTaskIdAttr($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('task_id', $value);
        } else {
            if ($value !== '') $query->where('task_id', $value);
        }
    }

    /**
     * Bộ lọc người dùng
     * @param $query Model
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        if ($value !== '') $query->where('uid', $value);
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


}
