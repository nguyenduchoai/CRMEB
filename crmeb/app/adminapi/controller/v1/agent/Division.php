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
use app\services\agent\DivisionAgentApplyServices;
use app\services\agent\DivisionServices;
use app\services\other\AgreementServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;
use think\facade\App;

/**
 * Controller đại lý khu vực
 * Class Division
 * @package app\adminapi\controller\v1\agent
 */
class Division extends AuthController
{
    /**
     * Division constructor.
     * @param App $app
     * @param DivisionServices $services
     */
    public function __construct(App $app, DivisionServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách đại lý khu vực
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function divisionList()
    {
        $where = $this->request->getMore([
            ['division_type', 0],
            ['keyword', '']
        ]);
        if ($where['division_type'] == 2) {
            $where['division_id'] = $this->adminInfo['division_id'];
        }
        $data = $this->services->getDivisionList($where);
        return app('json')->success($data);
    }

    /**
     * Danh sách cấp dưới
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function divisionDownList()
    {
        [$type, $uid] = $this->request->getMore([
            ['division_type', 0],
            ['uid', 0],
        ], true);
        $data = $this->services->divisionDownList($type, $uid);
        return app('json')->success($data);
    }

    /**
     * Thêm/sửa đại lý khu vực
     * @param $uid
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function divisionCreate($uid)
    {
        return app('json')->success($this->services->getDivisionForm((int)$uid));
    }

    /**
     * Lưu đại lý khu vực
     * @return mixed
     */
    public function divisionSave()
    {
        $data = $this->request->postMore([
            ['uid', 0],
            ['aid', 0],
            ['division_percent', 0],
            ['division_end_time', ''],
            ['division_status', 1],
            ['account', ''],
            ['pwd', ''],
            ['conf_pwd', ''],
            ['division_name', ''],
            ['roles', []],
            ['image', []]
        ]);
        $this->services->divisionSave($data);
        return app('json')->success(100000);
    }

    /**
     * Thêm/sửa đại lý
     * @param $uid
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function divisionAgentCreate($uid)
    {
        return app('json')->success($this->services->getDivisionAgentForm((int)$uid));
    }

    /**
     * Lưu đại lý
     * @param UserServices $userServices
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function divisionAgentSave(UserServices $userServices)
    {
        $data = $this->request->postMore([
            ['division_id', 0],
            ['uid', 0],
            ['division_percent', 0],
            ['division_end_time', ''],
            ['division_status', 1],
            ['division_name', ''],
            ['edit', 0],
            ['image', []],
        ]);
        if ((int)$data['uid'] == 0) $data['uid'] = $data['image']['uid'];
        $userInfo = $userServices->getUserInfo($data['uid'], 'is_division,is_agent,is_staff');
        if (!$userInfo) throw new AdminException(100100);
        if ($data['edit'] == 0) {
            if ($userInfo['is_division']) throw new AdminException('Người dùng này là đại lý khu vực, vui lòng không thêm làm đại lý');
            if ($userInfo['is_agent']) throw new AdminException('Người dùng này đã là đại lý, không thể thêm lại');
            if ($userInfo['is_staff']) throw new AdminException('Người dùng này là nhân viên cấp dưới, không thể thêm làm đại lý');
            $divisionUserInfo = $userServices->count(['uid' => (int)$data['division_id'], 'is_division' => 1, 'division_id' => $data['division_id']]);
            if (!$divisionUserInfo) throw new AdminException(100100);
        }
        $this->services->divisionAgentSave($data);
        return app('json')->success(100000);
    }

    /**
     * Thiết lập trạng thái
     * @param $status
     * @param $uid
     * @return mixed
     */
    public function setDivisionStatus($status, $uid)
    {
        $this->services->setDivisionStatus($status, $uid);
        return app('json')->success(100014);
    }

    /**
     * Xóa thành công
     * @param $type
     * @param $uid
     * @return mixed
     */
    public function delDivision($type, $uid)
    {
        $this->services->delDivision($type, $uid);
        return app('json')->success(100002);
    }

    /**
     * Danh sách yêu cầu ở quản trị
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function AdminApplyList()
    {
        $where = $this->request->getMore([
            ['uid', 0],
            ['division_id', 0],
            ['division_invite', ''],
            ['status', ''],
            ['keyword', ''],
            ['time', ''],
        ]);
        $where['division_id'] = $this->adminInfo['division_id'];
        /** @var DivisionAgentApplyServices $applyServices */
        $applyServices = app()->make(DivisionAgentApplyServices::class);
        $data = $applyServices->AdminApplyList($where);
        return app('json')->success($data);
    }

    /**
     * Biểu mẫu duyệt
     * @param $id
     * @param $type
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function examineApply($id, $type)
    {
        /** @var DivisionAgentApplyServices $applyServices */
        $applyServices = app()->make(DivisionAgentApplyServices::class);
        $data = $applyServices->examineApply($id, $type);
        return app('json')->success($data);
    }

    /**
     * Duyệt đại lý
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function applyAgentSave()
    {
        $data = $this->request->getMore([
            ['type', 0],
            ['id', 0],
            ['division_percent', ''],
            ['division_end_time', ''],
            ['division_status', ''],
            ['refusal_reason', 0]
        ]);
        /** @var DivisionAgentApplyServices $applyServices */
        $applyServices = app()->make(DivisionAgentApplyServices::class);
        $data = $applyServices->applyAgentSave($data);
        return app('json')->success(100014);
    }

    /**
     * Xóa duyệt đại lý
     * @param $id
     * @return mixed
     */
    public function delApply($id)
    {
        /** @var DivisionAgentApplyServices $applyServices */
        $applyServices = app()->make(DivisionAgentApplyServices::class);
        $applyServices->delApply($id);
        return app('json')->success(100002);
    }

    /**
     * Form thêm nhân viên
     * @param $uid
     * @return \think\Response
     * @throws \FormBuilder\Exception\FormBuilderException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2024/1/22
     */
    public function divisionStaffCreate($uid)
    {
        return app('json')->success($this->services->getDivisionStaffForm((int)$uid));
    }

    /**
     * Lưu nhân viên
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2024/1/22
     */
    public function divisionStaffSave()
    {
        $data = $this->request->getMore([
            ['uid', 0],
            ['division_percent', 0],
            ['agent_id', 0],
            ['image', []],
        ]);
        $this->services->divisionStaffSave($data);
        return app('json')->success(100000);
    }

    /**
     * Thống kê tiếp thị liên kết
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/4/8
     */
    public function divisionStatistics()
    {
        [$type, $time, $page, $limit, $sort, $order] = $this->request->getMore([
            ['type', 0],
            ['time', ''],
            ['page', 1],
            ['limit', 15],
            ['sort', 'order_sum'],
            ['order', 'desc'],
        ], true);
        $time = $time != '' ? explode('-', $time) : [];
        $data = $this->services->divisionStatistics($type, $time, $page, $limit, $sort, $order);
        return app('json')->success($data);

    }
}
