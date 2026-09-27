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
namespace app\api\controller\v2\agent;


use app\Request;
use app\services\agent\AgentLevelServices;
use app\services\agent\AgentLevelTaskServices;

/**
 * Class AgentLevel
 * @package app\controller\api\v2\agent
 */
class AgentLevel
{
    protected $services;

    public function __construct(AgentLevelServices $services)
    {
        $this->services = $services;
    }

    /**
     * Kiểm tra người dùng có thể trở thành thành viên không
     * @param Request $request
     * @return mixed
     */
    public function detection(Request $request)
    {
        return app('json')->success($this->services->detection((int)$request->uid()));
    }

    /**
     * Danh sách hạng CTV
     * @param Request $request
     * @return mixed
     */
    public function levelList(Request $request)
    {
        return app('json')->success($this->services->getUserlevelList((int)$request->uid()));
    }

    /**
     * Lấy nhiệm vụ hạng
     * @param Request $request
     * @param AgentLevelTaskServices $services
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function levelTaskList(Request $request, AgentLevelTaskServices $services, $id)
    {
        return app('json')->success($services->getUserLevelTaskList((int)$request->uid(), (int)$id));
    }

    /**
     * Chi tiết thành viên
     * @param Request $request
     * @return mixed
     */
    public function userLevelInfo(Request $request)
    {
        return app('json')->success($this->services->getUserLevelInfo((int)$request->uid()));
    }

}
