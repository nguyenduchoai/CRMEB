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
namespace app\api\controller\v1\user;

use app\model\system\SystemUserTask;
use app\Request;
use app\services\user\UserLevelServices;

/**
 * Lớp hạng thành viên
 * Class UserLevelController
 * @package app\api\controller\user
 */
class UserLevelController
{
    protected $services = NUll;

    /**
     * UserLevelController constructor.
     * @param UserLevelServices $services
     */
    public function __construct(UserLevelServices $services)
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
     * Danh sách hạng thành viên
     * @param Request $request
     * @return mixed
     */
    public function grade(Request $request)
    {
        return app('json')->success(['list'=>$this->services->grade((int)$request->uid()),'task'=>['list'=>[],'task'=>[]]]);
    }

    /**
     * Lấy nhiệm vụ hạng
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function task(Request $request, $id)
    {
        return app('json')->success((new SystemUserTask())->getTashList($id, $request->uid()));
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

    /**
     * Danh sách điểm kinh nghiệm
     * @param Request $request
     * @return mixed
     */
    public function expList(Request $request)
    {
        return app('json')->success($this->services->expList((int)$request->uid()));
    }

}
