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
namespace app\adminapi\controller\v1\agent;

use app\adminapi\controller\AuthController;
use app\services\agent\AgentLevelServices;
use app\services\agent\AgentLevelTaskServices;
use think\facade\App;

/**
 * Controller nhiệm vụ cấp độ CTV
 * Class AgentLevelTask
 * @package app\controller\admin\v1\agent
 */
class AgentLevelTask extends AuthController
{
    /**
     * AgentLevelTask constructor.
     * @param App $app
     * @param AgentLevelTaskServices $services
     */
    public function __construct(App $app, AgentLevelTaskServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Hiển thị danh sách nhiệm vụ theo hạng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['id', 0],
            ['status', ''],
            ['keyword', '']
        ]);
        if (!$where['id']) {
            return app('json')->fail(100100);
        }
        $where['level_id'] = $where['id'];
        unset($where['id']);
        return app('json')->success($this->services->getLevelTaskList($where));
    }

    /**
     * Form thêm nhiệm vụ theo hạng
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        [$level_id] = $this->request->postMore([
            ['level_id', 0]], true);
        if (!$level_id) {
            return app('json')->fail(100100);
        }
        return app('json')->success($this->services->createForm((int)$level_id));
    }

    /**
     * Lưu nhiệm vụ theo hạng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['level_id', 0],
            ['name', ''],
            ['type', ''],
            ['number', 0],
            ['desc', 0],
            ['sort', 0],
            ['status', 0]]);
        if (!$data['level_id']) return app('json')->fail(100100);
        if (!$data['name']) return app('json')->fail(400207);
        if (!$data['type']) return app('json')->fail(400208);
        if (!$data['number']) return app('json')->fail(400209);
        $this->services->checkTypeTask(0, $data);
        $data['add_time'] = time();
        $this->services->save($data);
        $levelInfo = app()->make(AgentLevelServices::class)->get((int)$data['level_id']);
        $levelInfo->task_num = $levelInfo->task_num + 1;
        $levelInfo->task_total_num = $levelInfo->task_total_num + 1;
        $levelInfo->save();
        return app('json')->success(400210);
    }

    /**
     * Hiển thị resource được chỉ định
     * @param $id
     */
    public function read($id)
    {

    }

    /**
     * Form sửa nhiệm vụ theo hạng
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit($id)
    {
        return app('json')->success($this->services->editForm((int)$id));
    }

    /**
     * Sửa nhiệm vụ theo hạng
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['type', ''],
            ['number', 0],
            ['desc', 0],
            ['sort', 0],
            ['status', 0]]);
        if (!$data['name']) return app('json')->fail(400207);
        if (!$data['type']) return app('json')->fail(400208);
        if (!$data['number']) return app('json')->fail(400209);
        if (!$levelTaskInfo = $this->services->getLevelTaskInfo((int)$id)) return app('json')->fail(400211);
        $this->services->checkTypeTask((int)$id, $data);
        $levelTaskInfo->name = $data['name'];
        $levelTaskInfo->type = $data['type'];
        $levelTaskInfo->number = $data['number'];
        $levelTaskInfo->desc = $data['desc'];
        $levelTaskInfo->sort = $data['sort'];
        $levelTaskInfo->status = $data['status'];
        $levelTaskInfo->save();
        return app('json')->success(100001);
    }

    /**
     * Xóa nhiệm vụ cấp độ
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail(100100);
        $levelTaskInfo = $this->services->getLevelTaskInfo((int)$id);
        if ($levelTaskInfo) {
            $res = $this->services->update($id, ['is_del' => 1]);
            if ($res) {
                $levelInfo = app()->make(AgentLevelServices::class)->get((int)$levelTaskInfo['level_id']);
                $levelInfo->task_num = $levelInfo->task_num - 1;
                $levelInfo->task_total_num = $levelInfo->task_total_num - 1;
                if ($levelInfo->task_num <= 0) $levelInfo->task_num = $levelInfo->task_total_num;
                $levelInfo->save();
            } else {
                return app('json')->fail(100008);
            }
        }
        return app('json')->success(100002);
    }

    /**
     * Sửa trạng thái
     * @param int $id
     * @param string $status
     * @return mixed
     */
    public function set_status($id = 0, $status = '')
    {
        if ($status == '' || $id == 0) return app('json')->fail(100100);
        $this->services->update($id, ['status' => $status]);
        return app('json')->success(100014);
    }

}
