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
     * @var AgentLevelServices
     */
    protected $services;

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
        // Lấy tham số yêu cầu, gồm trạng thái và từ khóa
        $where = $this->request->getMore([
            ['status', ''],
            ['keyword', '']
        ]);
        // Gọi tầng service để lấy danh sách cấp độ
        return app('json')->success($this->services->getLevelList($where));
    }

    /**
     * Form thêm hạng CTV
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        // Gọi tầng service để tạo form thêm mới
        return app('json')->success($this->services->createForm());
    }

    /**
     * Lưu hạng CTV
     * @return mixed
     */
    public function save()
    {
        // Lấy và xác thực dữ liệu yêu cầu
        $data = $this->request->postMore([
            ['name', ''],
            ['grade', 0],
            ['image', ''],
            ['one_brokerage_percent', 0],
            ['two_brokerage_percent', 0],
            ['status', 0]]);
        if (!$data['name']) return app('json')->fail('Vui lòng nhập tên cấp độ');
        if (!$data['grade']) return app('json')->fail('Vui lòng nhập cấp độ');
        if (!$data['image']) return app('json')->fail('Vui lòng chọn biểu tượng cấp độ');
        // Kiểm tra tỷ lệ trả hoa hồng cấp 2 có lớn hơn cấp 1 không
        if ($data['two_brokerage_percent'] > $data['one_brokerage_percent']) {
            return app('json')->fail('Tỷ lệ trả hoa hồng cấp 2 không được lớn hơn cấp 1');
        }
        // Kiểm tra cấp độ đã tồn tại chưa
        $grade = $this->services->get(['grade' => $data['grade'], 'is_del' => 0]);
        if ($grade) {
            return app('json')->fail('Cấp độ này đã tồn tại');
        }
        $data['add_time'] = time();
        // Lưu dữ liệu
        $this->services->save($data);
        return app('json')->success('Thêm cấp độ thành công');
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
        // Gọi tầng service để tạo form chỉnh sửa
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
        // Lấy và xác thực dữ liệu yêu cầu
        $data = $this->request->postMore([
            ['name', ''],
            ['grade', 0],
            ['image', ''],
            ['one_brokerage_percent', 0],
            ['two_brokerage_percent', 0],
            ['status', 0]]);
        if (!$data['name']) return app('json')->fail('Vui lòng nhập tên cấp độ');
        if (!$data['grade']) return app('json')->fail('Vui lòng nhập cấp độ');
        if (!$data['image']) return app('json')->fail('Vui lòng chọn biểu tượng cấp độ');
        // Kiểm tra tỷ lệ trả hoa hồng cấp 2 có lớn hơn cấp 1 không
        if ($data['two_brokerage_percent'] > $data['one_brokerage_percent']) {
            return app('json')->fail('Tỷ lệ trả hoa hồng cấp 2 không được lớn hơn cấp 1');
        }
        // Kiểm tra cấp độ cần chỉnh sửa có tồn tại không
        if (!$levelInfo = $this->services->getLevelInfo((int)$id)) return app('json')->fail('Cấp độ cần sửa không tồn tại');
        // Kiểm tra cấp độ có bị trùng không
        $grade = $this->services->get(['grade' => $data['grade'], 'is_del' => 0]);
        if ($grade && $grade['id'] != $id) {
            return app('json')->fail('Cấp độ này đã tồn tại');
        }

        // Cập nhật thông tin cấp độ
        $levelInfo->name = $data['name'];
        $levelInfo->grade = $data['grade'];
        $levelInfo->image = $data['image'];
        $levelInfo->one_brokerage_percent = $data['one_brokerage_percent'];
        $levelInfo->two_brokerage_percent = $data['two_brokerage_percent'];
        $levelInfo->status = $data['status'];
        $levelInfo->save();
        return app('json')->success('Sửa thành công');
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
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        //Kiểm tra dữ liệu hạng CTV có tồn tại không
        $levelInfo = $this->services->getLevelInfo((int)$id);
        if ($levelInfo) {
            //Cập nhật dữ liệu thành đã xóa
            $res = $this->services->update($id, ['is_del' => 1]);
            if (!$res)
                return app('json')->fail('Xóa thất bại');
            //Xóa (đánh dấu đã xóa) các nhiệm vụ của hạng này
            /** @var AgentLevelTaskServices $agentLevelTaskServices */
            $agentLevelTaskServices = app()->make(AgentLevelTaskServices::class);
            $agentLevelTaskServices->update(['level_id' => $id], ['is_del' => 1]);
        }
        return app('json')->success('Xóa thành công');
    }

    /**
     * Sửa trạng thái
     * @param int $id
     * @param string $status
     * @return mixed
     */
    public function set_status($id = 0, $status = '')
    {
        if ($status == '' || $id == 0) return app('json')->fail('Tham số không hợp lệ');
        // Cập nhật trạng thái
        $this->services->update($id, ['status' => $status]);
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Lấy số lượng form nhiệm vụ
     * @param int $id ID nhiệm vụ
     * @return \think\response\Json
     */
    public function getTaskNumForm($id)
    {
        // Kiểm tra ID nhiệm vụ có bằng 0 không, nếu bằng 0 thì trả về thông báo lỗi
        if ($id == 0) return app('json')->fail('Tham số không hợp lệ');
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
        return app('json')->success('Cài đặt thành công');
    }
}
