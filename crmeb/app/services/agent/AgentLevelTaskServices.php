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

namespace app\services\agent;

use app\dao\agent\AgentLevelTaskDao;
use app\services\BaseServices;
use app\services\order\StoreOrderServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;


/**
 * Class AgentLevelTaskServices
 * @package app\services\agent
 */
class AgentLevelTaskServices extends BaseServices
{
    /**
     * Loại nhiệm vụ
     * type được lưu trong cơ sở dữ liệu để phân biệt nhiệm vụ
     * name tên nhiệm vụ ({$num} trong tên nhiệm vụ sẽ tự động được thay bằng số đã đặt + đơn vị)
     * max_number giá trị tối đa được đặt, 0 là không giới hạn
     * min_number giá trị tối thiểu được đặt
     * unit đơn vị
     * */
    protected $TaskType = [
        [
            'type' => 1,
            'method' => 'spread',
            'name' => 'Mời {$num} bạn làm cấp dưới',
            'real_name' => 'Mời bạn bè làm cấp dưới',
            'max_number' => 0,
            'min_number' => 1,
            'unit' => 'người'
        ],
        [
            'type' => 2,
            'method' => 'consumePrice',
            'name' => 'Chi tiêu cá nhân đạt {$num}',
            'real_name' => 'Số tiền chi tiêu cá nhân',
            'max_number' => 0,
            'min_number' => 0,
            'unit' => 'đ'
        ],
        [
            'type' => 3,
            'method' => 'consumeCount',
            'name' => 'Chi tiêu cá nhân đạt {$num}',
            'real_name' => 'Số đơn chi tiêu cá nhân',
            'max_number' => 0,
            'min_number' => 0,
            'unit' => 'đơn'
        ],
        [
            'type' => 4,
            'method' => 'spreadConsumePrice',
            'name' => 'Cấp dưới chi tiêu đạt {$num}',
            'real_name' => 'Số tiền chi tiêu của cấp dưới',
            'max_number' => 0,
            'min_number' => 0,
            'unit' => 'đ'
        ],
        [
            'type' => 5,
            'method' => 'spreadConsumeCount',
            'name' => 'Cấp dưới chi tiêu đạt {$num}',
            'real_name' => 'Số đơn chi tiêu của cấp dưới',
            'max_number' => 0,
            'min_number' => 0,
            'unit' => 'đơn'
        ],
    ];

    /**
     * AgentLevelTaskServices constructor.
     * @param AgentLevelTaskDao $dao
     */
    public function __construct(AgentLevelTaskDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy thông tin một nhiệm vụ
     * @param int $id
     * @param string $field
     * @param array $with
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLevelTaskInfo(int $id, string $field = '*', array $with = [])
    {
        return $this->dao->getOne(['id' => $id, 'is_del' => 0], $field, $with);
    }

    /**
     * Lấy danh sách hạng
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLevelTaskList(array $where)
    {
        $where['is_del'] = 0;
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getTaskList($where, '*', [], $page, $limit);
        if ($list) {
            $allTyep = $this->getTaskTypeAll();
            $allTyep = array_combine(array_column($allTyep, 'type'), $allTyep);
            foreach ($list as &$item) {
                $item['type_name'] = $allTyep[$item['type']]['real_name'] ?? '';
            }
        }
        $count = $this->dao->count($where);
        return compact('count', 'list');
    }

    /**
     * Lấy nhiệm vụ theo loại của một hạng
     * @param int $level_id
     * @param int $type
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLevelTypeTask(int $level_id, int $type = 1)
    {
        return $this->dao->get(['level_id' => $level_id, 'type' => $type, 'is_del' => 0]);
    }

    /**
     * Form thêm nhiệm vụ theo hạng
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createForm(int $level_id)
    {
        /** @var AgentLevelServices $levelServices */
        $levelServices = app()->make(AgentLevelServices::class);
        if (!$levelServices->getLevelInfo($level_id)) {
            throw new AdminException('Cấp độ được chọn không tồn tại');
        }
        $taskList = $this->getTaskTypeAll();
        $setOptionLabel = function () use ($taskList) {
            $menus = [];
            foreach ($taskList as $task) {
                $menus[] = ['value' => $task['type'], 'label' => $task['real_name'] ?? '' . '(' . $task['unit'] ?? '' . ')'];
            }
            return $menus;
        };
        $field[] = Form::hidden('level_id', $level_id);
        $field[] = Form::select('type', 'Loại nhiệm vụ')->setOptions(Form::setOptions($setOptionLabel))->filterable(true);
        $field[] = Form::input('name', 'Tên nhiệm vụ')->col(24);
        $field[] = Form::number('number', 'Số lượng quy định', 0)->precision(0);
        $field[] = Form::textarea('desc', 'Mô tả nhiệm vụ');
        $field[] = Form::number('sort', 'Thứ tự sắp xếp', 0)->precision(0);
        $field[] = Form::radio('status', 'Hiển thị', 1)->options([['value' => 1, 'label' => 'Hiện'], ['value' => 0, 'label' => 'Ẩn']]);
        return create_form('Thêm nhiệm vụ cấp bậc', $field, Url::buildUrl('/agent/level_task'), 'POST');
    }

    /**
     * Lấy dữ liệu sửa nhiệm vụ
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function editForm(int $id)
    {
        $levelTaskInfo = $this->getLevelTaskInfo($id);
        if (!$levelTaskInfo)
            throw new AdminException('Dữ liệu không tồn tại');
        $field = [];
        $field[] = Form::hidden('id', $id);
        $taskList = $this->getTaskTypeAll();
        $setOptionLabel = function () use ($taskList) {
            $menus = [];
            foreach ($taskList as $task) {
                $menus[] = ['value' => $task['type'], 'label' => $task['real_name'] ?? '' . '(' . $task['unit'] ?? '' . ')'];
            }
            return $menus;
        };
        $field[] = Form::select('type', 'Loại nhiệm vụ', $levelTaskInfo['type'])->setOptions(Form::setOptions($setOptionLabel))->filterable(true);
        $field[] = Form::input('name', 'Tên nhiệm vụ', $levelTaskInfo['name']);
        $field[] = Form::number('number', 'Số lượng quy định', $levelTaskInfo['number'])->min(0);
        $field[] = Form::textarea('desc', 'Mô tả nhiệm vụ', $levelTaskInfo['desc']);
        $field[] = Form::number('sort', 'Thứ tự sắp xếp', $levelTaskInfo['sort'])->precision(0);
        $field[] = Form::radio('status', 'Hiển thị', $levelTaskInfo['status'])->options([['value' => 1, 'label' => 'Hiện'], ['value' => 0, 'label' => 'Ẩn']]);

        return create_form('Sửa nhiệm vụ cấp bậc', $field, Url::buildUrl('/agent/level_task/' . $id), 'PUT');
    }

    /**
     * Lấy loại nhiệm vụ
     * @return array[]
     */
    public function getTaskTypeAll()
    {
        return $this->TaskType;
    }

    /**
     * Lấy một nhiệm vụ
     * @param string $type Loại nhiệm vụ
     * @return array
     * */
    public static function getTaskType($type)
    {
        foreach (self::$TaskType as $item) {
            if ($item['type'] == $type) return $item;
        }
    }

    /**
     * Lấy tình trạng nhiệm vụ của người dùng ở một hạng phân phối
     * @param int $uid
     * @param int $level_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserLevelTaskList(int $uid, int $level_id)
    {
        //Cửa hàng có mở tiếp thị liên kết hay không
        if (!sys_config('brokerage_func_status')) {
            return [];
        }
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $user = $userServices->getUserInfo($uid);
        if (!$user) {
            throw new ApiException('Người dùng không tồn tại');
        }
        /** @var AgentLevelServices $levelServices */
        $levelServices = app()->make(AgentLevelServices::class);
        $levelInfo = $levelServices->getLevelInfo($level_id);
        if (!$levelInfo) {
            throw new ApiException('Cấp độ CTV không tồn tại');
        }
        $taskList = $this->dao->getTaskList(['level_id' => $level_id, 'is_del' => 0, 'status' => 1]);
        if ($taskList) {
            $userLevel = [];
            if ($user['agent_level'] ?? 0) $userLevel = $levelServices->getLevelInfo($user['agent_level']);
            $allTyep = $this->getTaskTypeAll();
            $allTyep = array_combine(array_column($allTyep, 'type'), $allTyep);
            foreach ($taskList as &$task) {
                $task['finish'] = 1;
                $task['task_type_title'] = 'Đã hoàn thành';
                $task['speed'] = 100;
                $task['new_number'] = $task['number'];
                //Nhiệm vụ của các hạng trước hạng hiện tại đều coi là đã hoàn thành
                if (!$userLevel || $userLevel['grade'] < $levelInfo['grade']) {
                    [$title, $num, $isComplete] = $this->checkLevelTaskFinish($uid, (int)$task['id']);
                    if (!$isComplete) {
                        $scale = in_array($task['type'], [2, 4]) ? 2 : 0;
                        $task['finish'] = 0;
                        $numdata = bcsub($task['number'], $num, $scale);
                        $task['task_type_title'] = 'Còn cần' . str_replace('{$num}', $numdata . $allTyep[$task['type']]['unit'] ?? '', $title);
                        $task['speed'] = bcmul((string)bcdiv((string)$num, (string)$task['number'], 2), '100', 0);
                        $task['new_number'] = $num;
                    }
                }
            }
        }
        return $taskList;
    }


    /**
     * Kiểm tra tình trạng hoàn thành của một nhiệm vụ
     * @param int $uid
     * @param int $task_id
     * @param array $levelTaskInfo
     * @return array|false
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkLevelTaskFinish(int $uid, int $task_id, $levelTaskInfo = [])
    {
        if (!$levelTaskInfo) {
            $levelTaskInfo = $this->getLevelTaskInfo($task_id);
        }
        if (!$levelTaskInfo) return false;
        $allTyep = $this->getTaskTypeAll();
        $allTyep = array_combine(array_column($allTyep, 'type'), $allTyep);
        $userNumber = 0;
        $msg = $allTyep[$levelTaskInfo['type']]['name'] ?? '';
        switch ($levelTaskInfo['type']) {
            case 1:
                /** @var UserServices $userServices */
                $userServices = app()->make(UserServices::class);
                $userNumber = $userServices->count(['spread_uid' => $uid, 'pid' => 0]);
                break;
            case 2:
                /** @var StoreOrderServices $storeOrderServices */
                $storeOrderServices = app()->make(StoreOrderServices::class);
                $where = ['uid' => $uid, 'paid' => 1, 'refund_status' => 0, 'pid' => 0];
                $userNumber = $storeOrderServices->sum($where, 'pay_price');
                break;
            case 3:
                /** @var StoreOrderServices $storeOrderServices */
                $storeOrderServices = app()->make(StoreOrderServices::class);
                $where = ['uid' => $uid, 'paid' => 1, 'refund_status' => 0, 'pid' => 0];
                $userNumber = $storeOrderServices->count($where);
                break;
            case 4:
                /** @var UserServices $userServices */
                $userServices = app()->make(UserServices::class);
                $spread_uids = $userServices->getColumn(['spread_uid' => $uid], 'uid');
                if ($spread_uids) {
                    /** @var StoreOrderServices $storeOrderServices */
                    $storeOrderServices = app()->make(StoreOrderServices::class);
                    $where = ['uid' => $spread_uids, 'paid' => 1, 'refund_status' => 0, 'pid' => 0];
                    $userNumber = $storeOrderServices->sum($where, 'pay_price');
                }
                break;
            case 5:
                /** @var UserServices $userServices */
                $userServices = app()->make(UserServices::class);
                $spread_uids = $userServices->getColumn(['spread_uid' => $uid], 'uid');
                if ($spread_uids) {
                    /** @var StoreOrderServices $storeOrderServices */
                    $storeOrderServices = app()->make(StoreOrderServices::class);
                    $where = ['uid' => $spread_uids, 'paid' => 1, 'refund_status' => 0, 'pid' => 0];
                    $userNumber = $storeOrderServices->count($where);
                }
                break;
            default:
                return false;
        }
        $isComplete = false;
        if ($userNumber >= $levelTaskInfo['number']) {
            /** @var AgentLevelTaskRecordServices $agentLevelTaskRecordServices */
            $agentLevelTaskRecordServices = app()->make(AgentLevelTaskRecordServices::class);
            $isComplete = true;
            if (!$agentLevelTaskRecordServices->get(['uid' => $uid, 'level_id' => $levelTaskInfo['level_id'], 'task_id' => $levelTaskInfo['id']])) {
                $data = ['uid' => $uid, 'level_id' => $levelTaskInfo['level_id'], 'task_id' => $levelTaskInfo['id'], 'add_time' => time()];
                $isComplete = $agentLevelTaskRecordServices->save($data);
            }
        }
        return [$msg, $userNumber, $isComplete];
    }

    /**
     * Kiểm tra nhiệm vụ hạng
     * @param int $id
     * @param array $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkTypeTask(int $id, array $data)
    {
        if (!$id && (!isset($data['level_id']) || !$data['level_id'])) {
            throw new AdminException('Tham số không hợp lệ');
        }
        if ($id) {
            $task = $this->getLevelTaskInfo($id);
            if (!$task) {
                throw new AdminException('Dữ liệu không tồn tại');
            }
            $data['level_id'] = $task['level_id'];
        }
        /** @var AgentLevelServices $agentLevelServices */
        $agentLevelServices = app()->make(AgentLevelServices::class);
        $levelInfo = $agentLevelServices->getLevelInfo($data['level_id']);
        if (!$levelInfo) {
            throw new AdminException('Dữ liệu không tồn tại');
        }
        $task = $this->dao->getOne(['level_id' => $data['level_id'], 'type' => $data['type'], 'is_del' => 0]);
        if (($id && $task && $task['id'] != $id) || (!$id && $task)) {
            throw new AdminException('Cấp độ này đã có nhiệm vụ loại này');
        }
        $taskList = $this->dao->getTypTaskList($data['type']);
        if ($taskList) {
            foreach ($taskList as $taskInfo) {
                if (is_null($taskInfo['grade'])) continue;
                if ($levelInfo['grade'] > $taskInfo['grade'] && $data['number'] <= $taskInfo['number']) {
                    throw new AdminException('Không được nhỏ hơn số lượng yêu cầu của nhiệm vụ cùng loại ở cấp độ thấp hơn');
                }
                if ($levelInfo['grade'] < $taskInfo['grade'] && $data['number'] >= $taskInfo['number']) {
                    throw new AdminException('Không được lớn hơn số lượng yêu cầu của nhiệm vụ cùng loại ở cấp độ cao hơn');
                }
            }
        }

        return true;
    }
}
