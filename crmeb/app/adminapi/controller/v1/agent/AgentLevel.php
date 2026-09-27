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
 * Controller cấp độ CTV
 * Class AgentLevel
 * @package app\controller\admin\v1\agent
 */
class AgentLevel extends AuthController
{
    /**
     * AgentLevel constructor.
     * @param App $app
     * @param AgentLevelServices $services
     */
    public function __construct(App $app, AgentLevelServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách hạng CTV ở quản trị
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['keyword', '']
        ]);
        return app('json')->success($this->services->getLevelList($where));
    }

    /**
     * Form thêm hạng CTV
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        return app('json')->success($this->services->createForm());
    }

    /**
     * Lưu hạng CTV
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['grade', 0],
            ['image', ''],
            ['one_brokerage_percent', 0],
            ['two_brokerage_percent', 0],
            ['status', 0]]);
        if (!$data['name']) return app('json')->fail(400200);
        if (!$data['grade']) return app('json')->fail(400201);
        if (!$data['image']) return app('json')->fail(400202);
        if ($data['two_brokerage_percent'] > $data['one_brokerage_percent']) {
            return app('json')->fail(400203);
        }
        $grade = $this->services->get(['grade' => $data['grade'], 'is_del' => 0]);
        if ($grade) {
            return app('json')->fail(400204);
        }
        $data['add_time'] = time();
        $this->services->save($data);
        return app('json')->success(400205);
    }

    /**
     * Hiển thị resource được chỉ định
     * @param $id
     */
    public function read($id)
    {

    }

    /**
     * Form sửa hạng CTV
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit($id)
    {
        return app('json')->success($this->services->editForm((int)$id));
    }

    /**
     * Sửa cấp độ CTV
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
            ['grade', 0],
            ['image', ''],
            ['one_brokerage_percent', 0],
            ['two_brokerage_percent', 0],
            ['status', 0]]);
        if (!$data['name']) return app('json')->fail(400200);
        if (!$data['grade']) return app('json')->fail(400201);
        if (!$data['image']) return app('json')->fail(400202);
        if ($data['two_brokerage_percent'] > $data['one_brokerage_percent']) {
            return app('json')->fail(400203);
        }
        if (!$levelInfo = $this->services->getLevelInfo((int)$id)) return app('json')->fail(400206);
        $grade = $this->services->get(['grade' => $data['grade'], 'is_del' => 0]);
        if ($grade && $grade['id'] != $id) {
            return app('json')->fail(400204);
        }

        $levelInfo->name = $data['name'];
        $levelInfo->grade = $data['grade'];
        $levelInfo->image = $data['image'];
        $levelInfo->one_brokerage_percent = $data['one_brokerage_percent'];
        $levelInfo->two_brokerage_percent = $data['two_brokerage_percent'];
        $levelInfo->status = $data['status'];
        $levelInfo->save();
        return app('json')->success(100001);
    }

    /**
     * Xóa cấp độ CTV
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail(100100);
        //Kiểm tra dữ liệu hạng CTV có tồn tại không
        $levelInfo = $this->services->getLevelInfo((int)$id);
        if ($levelInfo) {
            //Cập nhật dữ liệu thành đã xóa
            $res = $this->services->update($id, ['is_del' => 1]);
            if (!$res)
                return app('json')->fail(100008);
            //Xóa (đánh dấu đã xóa) các nhiệm vụ của hạng này
            /** @var AgentLevelTaskServices $agentLevelTaskServices */
            $agentLevelTaskServices = app()->make(AgentLevelTaskServices::class);
            $agentLevelTaskServices->update(['level_id' => $id], ['is_del' => 1]);
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

    /**
     * Lấy số lượng form nhiệm vụ
     * @param int $id ID nhiệm vụ
     * @return \think\response\Json
     */
    public function getTaskNumForm($id)
    {
        // Kiểm tra ID nhiệm vụ có bằng 0 không, nếu bằng 0 thì trả về thông báo lỗi
        if ($id == 0) return app('json')->fail(100100);
        // Gọi tầng service để lấy số lượng form nhiệm vụ
        $result = $this->services->getTaskNumForm($id);
        // Trả về thông báo thành công và số lượng form nhiệm vụ
        return app('json')->success($result);
    }

    /**
     * Thiết lập số lượng nhiệm vụ
     * @param int $id ID nhiệm vụ
     * @return \think\response\Json
     */
    public function setTaskNum($id)
    {
        // Lấy số lượng nhiệm vụ từ request
        $data = $this->request->postMore([
            ['task_num', 0]
        ]);
        // Gọi tầng service để thiết lập số lượng nhiệm vụ
        $res = $this->services->setTaskNum($id, $data);
        return app('json')->success(100014);
    }
}
