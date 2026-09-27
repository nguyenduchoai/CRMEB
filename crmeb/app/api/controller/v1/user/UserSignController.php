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
namespace app\api\controller\v1\user;

use app\Request;
use app\services\user\UserSignServices;

/**
 * Người dùng điểm danh
 * Class UserController
 * @package app\api\controller\v1\user
 */
class UserSignController
{
    protected $services = NUll;

    /**
     * UserController constructor.
     * @param UserSignServices $services
     */
    public function __construct(UserSignServices $services)
    {
        $this->services = $services;
    }

    /**
     * Điểm danh - Cấu hình
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function sign_config(Request $request)
    {
        $uid = (int)$request->uid();
        return app('json')->success($this->services->signConfig($uid));
    }

    /**
     * Điểm danh - Danh sách
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function sign_list(Request $request)
    {
        list($page, $limit) = $request->getMore([
            ['page', 0],
            ['limit', 0]
        ], true);
        if (!$limit) return app('json')->success([]);
        $uid = (int)$request->uid();
        return app('json')->success($this->services->getUserSignList($uid));
    }

    /**
     * Điểm danh
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function sign_integral(Request $request)
    {
        if (sys_config('sign_status') == 0) {
            return app('json')->fail('Chức năng điểm danh chưa được bật');
        }
        $uid = (int)$request->uid();
        $integral = $this->services->sign($uid);
        return app('json')->success('Điểm danh nhận được {:integral} điểm thưởng', ['integral' => $integral], ['integral' => $integral]);
    }

    /**
     * Thông tin người dùng điểm danh
     * @param Request $request
     * @return mixed
     */
    public function sign_user(Request $request)
    {
        list($sign, $integral, $all) = $request->postMore([
            ['sign', 0],
            ['integral', 0],
            ['all', 0],
        ], true);
        $uid = (int)$request->uid();
        return app('json')->success($this->services->signUser($uid, $sign, $integral, $all));
    }

    /**
     * Danh sách điểm danh (theo năm tháng)
     * @param Request $request
     * @return mixed
     */
    public function sign_month(Request $request)
    {
        $uid = (int)$request->uid();
        return app('json')->success($this->services->getSignMonthList($uid));
    }

    /**
     * Người dùng thiết lập nhắc điểm danh
     * @param Request $request
     * @param $status
     * @return \think\Response
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/9
     */
    public function sign_remind(Request $request, $status)
    {
        $uid = (int)$request->uid();
        $this->services->setSignRemind($uid, $status);
        return app('json')->success('Cài đặt thành công');
    }

}
