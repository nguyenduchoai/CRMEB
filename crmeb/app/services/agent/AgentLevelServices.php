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

use app\dao\agent\AgentLevelDao;
use app\services\BaseServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;


/**
 * Class AgentLevelServices
 * @package app\services\agent
 */
class AgentLevelServices extends BaseServices
{
    /**
     * AgentLevelServices constructor.
     * @param AgentLevelDao $dao
     */
    public function __construct(AgentLevelDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy thông tin một hạng
     * @param int $id
     * @param string $field
     * @param array $with
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLevelInfo(int $id, string $field = '*', array $with = [])
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
    public function getLevelList(array $where)
    {
        $where['is_del'] = 0;
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, '*', ['task' => function ($query) {
            $query->field('count(*) as sum');
        }], $page, $limit);
        $count = $this->dao->count($where);
        foreach ($list as &$item) {
            $item['one_brokerage_ratio'] = $item['one_brokerage_percent'];
            $item['two_brokerage_ratio'] = $item['two_brokerage_percent'];
            if (strpos($item['image'], '/statics/system_images/') !== false) {
                $item['image'] = set_file_url($item['image']);
            }
        }
        return compact('count', 'list');
    }

    /**
     * Lấy danh sách hạng cộng tác viên của cửa hàng
     * @param int $uid
     * @return array
     */
    public function getUserlevelList(int $uid)
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
        //Kiểm tra nâng hạng
        $this->checkUserLevelFinish($uid);

        $list = $this->dao->getList(['is_del' => 0, 'status' => 1]);
        foreach ($list as &$item) {
            $item['image'] = set_file_url($item['image']);
        }
        $agent_level = $user['agent_level'] ?? 0;
        //Không có hạng thì mặc định lấy hạng thấp nhất
        if (!$agent_level) {
            $levelInfo = $list[0] ?? [];
            $levelInfo['grade'] = -1;
        } else {
            $levelInfo = $this->getLevelInfo($agent_level) ?: [];
        }
        $sum_task = $finish_task = 0;
        if ($levelInfo) {
            /** @var AgentLevelTaskServices $levelTaskServices */
            $levelTaskServices = app()->make(AgentLevelTaskServices::class);
            $sum_task = $levelTaskServices->count(['level_id' => $levelInfo['id'], 'is_del' => 0, 'status' => 1]);
            /** @var AgentLevelTaskRecordServices $levelTaskRecordServices */
            $levelTaskRecordServices = app()->make(AgentLevelTaskRecordServices::class);
            $finish_task = $levelTaskRecordServices->count(['level_id' => $levelInfo['id'], 'uid' => $uid]);
        }
        $levelInfo['sum_task'] = $sum_task;
        $levelInfo['finish_task'] = $finish_task;
        return ['user' => $user, 'level_list' => $list, 'level_info' => $levelInfo];
    }

    /**
     * Lấy hạng tiếp theo
     * @param int $level_id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNextLevelInfo(int $level_id = 0)
    {
        $grade = 0;
        if ($level_id) {
            $grade = $this->dao->value(['id' => $level_id, 'is_del' => 0, 'status' => 1], 'grade') ?: 0;
        }
        return $this->dao->getOne([['grade', '>', $grade], ['is_del', '=', 0], ['status', '=', 1]]);
    }

    /**
     * Kiểm tra người dùng có thể nâng hạng không
     * @param int $uid
     * @param array $uids
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkUserLevelFinish(int $uid, array $uids = [])
    {
        //Cửa hàng có mở tiếp thị liên kết hay không
        if (!sys_config('brokerage_func_status')) {
            return false;
        }
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->getUserInfo($uid);
        if (!$userInfo) {
            return false;
        }
        $list = $this->dao->getList(['is_del' => 0, 'status' => 1]);
        if (!$list) {
            return false;
        }
        if (!$uids) {
            //Lấy uid cấp trên || nếu mở tự mua thì trả về uid của chính mình
            $spread_uid = $userServices->getSpreadUid($uid, $userInfo);
            $two_spread_uid = 0;
            if ($spread_uid > 0 && $one_user_info = $userServices->getUserInfo($spread_uid)) {
                $two_spread_uid = $userServices->getSpreadUid($spread_uid, $one_user_info, false);
            }
            $uids = array_unique([$uid, $spread_uid, $two_spread_uid]);
        }
        foreach ($uids as $uid) {
            if ($uid <= 0) continue;
            if ($uid != $userInfo['uid']) {
                $userInfo = $userServices->getUserInfo($uid);
            }
            $now_grade = 0;
            if ($userInfo['agent_level']) {
                $now_grade = $this->dao->value(['id' => $userInfo['agent_level']], 'grade') ?: 0;
            }
            foreach ($list as $levelInfo) {
                if (!$levelInfo || $levelInfo['grade'] <= $now_grade) {
                    continue;
                }
                /** @var AgentLevelTaskServices $levelTaskServices */
                $levelTaskServices = app()->make(AgentLevelTaskServices::class);
                $task_list = $levelTaskServices->getTaskList(['level_id' => $levelInfo['id'], 'is_del' => 0, 'status' => 1]);
                if (!$task_list) {
                    continue;
                }
                foreach ($task_list as $task) {
                    $levelTaskServices->checkLevelTaskFinish($uid, (int)$task['id'], $task);
                }
                /** @var AgentLevelTaskRecordServices $levelTaskRecordServices */
                $levelTaskRecordServices = app()->make(AgentLevelTaskRecordServices::class);
                $ids = array_column($task_list, 'id');
                $finish_task = $levelTaskRecordServices->count(['level_id' => $levelInfo['id'], 'uid' => $uid, 'task_id' => $ids]);
                //Hoàn thành nhiệm vụ thì lên hạng này
                if ($finish_task >= $levelInfo['task_num']) {
                    $userServices->update($uid, ['agent_level' => $levelInfo['id']]);
                } else {
                    break;
                }
            }
        }

        return true;
    }

    /**
     * Nâng hạng phân phối
     * @param $storeBrokerageRatio
     * @param $storeBrokerageTwo
     * @param $spread_one_uid
     * @param $spread_two_uid
     * @return array|int[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAgentLevelBrokerage($storeBrokerageRatio, $storeBrokerageTwo, $spread_one_uid, $spread_two_uid)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $one_agent_level = $userServices->value(['uid' => $spread_one_uid], 'agent_level') ?? 0;
        $two_agent_level = $userServices->value(['uid' => $spread_two_uid], 'agent_level') ?? 0;

        if ($one_agent_level) {
            $oneLevelInfo = $this->getLevelInfo($one_agent_level);
            if ($oneLevelInfo && $oneLevelInfo['status'] == 1) {
                $storeBrokerageRatio = $oneLevelInfo['one_brokerage_percent'];
            }
        }

        if ($two_agent_level) {
            $twoLevelInfo = $this->getLevelInfo($two_agent_level);
            if ($twoLevelInfo && $twoLevelInfo['status'] == 1) {
                $storeBrokerageTwo = $twoLevelInfo['two_brokerage_percent'];
            }
        }

        return [$storeBrokerageRatio, $storeBrokerageTwo];
    }

    /**
     * Form thêm hạng
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createForm()
    {
        $field[] = Form::input('name', 'Tên cấp bậc')->maxlength(8)->col(24);
        $field[] = Form::number('grade', 'Cấp bậc', 0)->min(0)->precision(0);
        $field[] = Form::frameImage('image', 'Ảnh nền', Url::buildUrl(config('app.admin_prefix', 'admin') . '/widget.images/index', array('fodder' => 'image')))->icon('el-icon-picture-outline')->width('950px')->height('560px')->props(['footer' => false]);
        $field[] = Form::number('one_brokerage_percent', 'Tỷ lệ hoa hồng cấp 1', 0)->appendRule('suffix', [
            'type' => 'div',
            'class' => 'tips-info',
            'domProps' => ['innerHTML' => 'Sau khi đạt cấp này, hoa hồng cấp 1 sẽ được tính theo tỷ lệ này']
        ])->max(100)->precision(2);
        $field[] = Form::number('two_brokerage_percent', 'Tỷ lệ hoa hồng cấp 2', 0)->appendRule('suffix', [
            'type' => 'div',
            'class' => 'tips-info',
            'domProps' => ['innerHTML' => 'Sau khi đạt cấp này, hoa hồng cấp 2 sẽ được tính theo tỷ lệ này']
        ])->min(0)->max(100)->precision(2);
        $field[] = Form::radio('status', 'Hiển thị', 1)->options([['value' => 1, 'label' => 'Hiện'], ['value' => 0, 'label' => 'Ẩn']]);
        return create_form('Thêm cấp CTV', $field, Url::buildUrl('/agent/level'), 'POST');
    }

    /**
     * Lấy form sửa hạng
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function editForm(int $id)
    {
        $levelInfo = $this->getLevelInfo($id);
        if (!$levelInfo)
            throw new AdminException('Dữ liệu không tồn tại');
        $field = [];
        $field[] = Form::hidden('id', $id);
        $field[] = Form::input('name', 'Tên cấp bậc', $levelInfo['name'])->maxlength(8)->col(24);
        $field[] = Form::number('grade', 'Cấp bậc', $levelInfo['grade'])->min(0)->precision(0);
        $field[] = Form::frameImage('image', 'Ảnh nền', Url::buildUrl(config('app.admin_prefix', 'admin') . '/widget.images/index', array('fodder' => 'image')), $levelInfo['image'])->icon('el-icon-picture-outline')->width('950px')->height('560px')->props(['footer' => false]);
        $field[] = Form::number('one_brokerage_percent', 'Tỷ lệ hoa hồng cấp 1', $levelInfo['one_brokerage_percent'])->appendRule('suffix', [
            'type' => 'div',
            'class' => 'tips-info',
            'domProps' => ['innerHTML' => 'Sau khi đạt cấp này, hoa hồng cấp 1 sẽ được tính theo tỷ lệ này']
        ])->max(100)->precision(2);
        $field[] = Form::number('two_brokerage_percent', 'Tỷ lệ hoa hồng cấp 2', $levelInfo['two_brokerage_percent'])->appendRule('suffix', [
            'type' => 'div',
            'class' => 'tips-info',
            'domProps' => ['innerHTML' => 'Sau khi đạt cấp này, hoa hồng cấp 2 sẽ được tính theo tỷ lệ này']
        ])->min(0)->max(100)->precision(2);
        $field[] = Form::radio('status', 'Hiển thị', $levelInfo['status'])->options([['value' => 1, 'label' => 'Hiện'], ['value' => 0, 'label' => 'Ẩn']]);

        return create_form('Sửa cấp CTV', $field, Url::buildUrl('/agent/level/' . $id), 'PUT');
    }

    /**
     * Form tặng hạng phân phối
     * @param int $uid
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function levelForm(int $uid)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->getUserInfo($uid);
        if (!$userInfo) {
            throw new AdminException('Người dùng không tồn tại');
        }
        $levelList = $this->dao->getList(['is_del' => 0, 'status' => 1], '*', [], 0, 0, $userInfo['agent_level']);
        $setOptionLabel = function () use ($levelList) {
            $menus = [];
            foreach ($levelList as $level) {
                $menus[] = ['value' => $level['id'], 'label' => $level['name']];
            }
            return $menus;
        };
        $field[] = Form::hidden('uid', $uid);
        $field[] = Form::select('id', 'Cấp độ CTV', $userInfo['agent_level'] != 0 ? $userInfo['agent_level'] : '')->setOptions(Form::setOptions($setOptionLabel))->filterable(true);
        return create_form('Sửa cấp độ CTV', $field, Url::buildUrl('/agent/give_level'), 'post');
    }

    /**
     * Tặng cấp độ CTV
     * @param int $uid
     * @param int $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function givelevel(int $uid, int $id)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->getUserInfo($uid, 'uid');
        if (!$userInfo) {
            throw new AdminException('Người dùng không tồn tại');
        }
        $levelInfo = $this->getLevelInfo($id, 'id');
        if (!$levelInfo) {
            throw new AdminException('Cấp độ CTV không tồn tại');
        }
        if ($userServices->update($uid, ['agent_level' => $id]) === false) {
            throw new AdminException('Tặng thất bại');
        }
        return true;
    }

    /**
     * Lấy form số lượng nhiệm vụ của hạng phân phối chỉ định
     * @param int $id ID hạng phân phối
     * @return array|string
     */
    public function getTaskNumForm($id)
    {
        // Lấy thông tin của hạng phân phối chỉ định
        $levelInfo = $this->getLevelInfo($id);
        // Tạo ô nhập số lượng nhiệm vụ
        $field[] = Form::input('task_num', 'Số nhiệm vụ cần hoàn thành', $levelInfo['task_num'])->maxlength(8)->col(24)->info('Mặc định phải hoàn thành tất cả nhiệm vụ để lên cấp, có thể thiết lập số nhiệm vụ cần hoàn thành để lên cấp');
        // Tạo form và trả về chuỗi HTML
        return create_form('Đặt số lượng nhiệm vụ hoàn thành', $field, Url::buildUrl('/agent/set_task_num/' . $id), 'post');
    }

    /**
     * Đặt số lượng nhiệm vụ cho hạng phân phối chỉ định
     * @param int $id ID hạng phân phối
     * @param array $data Mảng chứa số lượng nhiệm vụ
     * @return bool Trả về true nghĩa là đặt thành công
     * @throws AdminException Nếu hạng phân phối không tồn tại, hoặc số lượng nhiệm vụ trống, hoặc số lượng nhiệm vụ lớn hơn số lượng nhiệm vụ hiện có thì báo lỗi ngoại lệ
     */
    public function setTaskNum($id, $data)
    {
        // Kiểm tra hạng phân phối có tồn tại không
        if (!$id) throw new AdminException('Cấp độ CTV không tồn tại');
        // Kiểm tra số lượng nhiệm vụ có trống không
        if (!$data['task_num']) throw new AdminException('Vui lòng nhập số lượng nhiệm vụ');
        // Lấy số lượng nhiệm vụ hiện có của hạng phân phối hiện tại
        $count = app()->make(AgentLevelTaskServices::class)->count(['level_id' => $id, 'is_del' => 0, 'status' => 1]);
        // Kiểm tra số lượng nhiệm vụ có lớn hơn số lượng nhiệm vụ hiện có không
        if ($data['task_num'] > $count) throw new AdminException('Số lượng nhiệm vụ không được lớn hơn số nhiệm vụ hiện có');
        // Cập nhật số lượng nhiệm vụ của hạng phân phối
        $this->dao->update($id, ['task_num' => $data['task_num']]);
        // Trả về true nghĩa là đặt thành công
        return true;
    }

    /**
     * Lấy mảng hạng phân phối
     * @return array
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/6/16
     */
    public function getAgentLevelArr()
    {
        return $this->dao->getColumn(['status'=>1,'is_del'=>0], 'name', 'grade');
    }
}
