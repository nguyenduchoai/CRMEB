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
namespace app\outapi\controller;

use think\facade\App;
use app\services\user\OutUserServices;

/**
 * Controller người dùng
 * Class User
 * @package app\outapi\controller
 */
class User extends AuthController
{
    /**
     * User constructor.
     * @param App $app
     * @param OutUserServices $service
     * @method temp
     */
    public function __construct(App $app, OutUserServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách người dùng
     * @return mixed
     */
    public function lst()
    {
        $where = $this->request->getMore([
            ['nickname', ''],
            ['status', ''],
            ['field_key', ''],
        ]);
        return app('json')->success($this->services->getUserList($where));
    }

    /**
     * Lưu resource mới tạo
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['real_name', ''],
            ['phone', 0],
            ['mark', ''],
            ['pwd', ''],
            ['level', 0],
            ['spread_open', 0],
            ['is_promoter', 0],
            ['status', 1]
        ]);
        $uid = $this->services->saveUser(0, $data);
        if (!$uid) {
            return app('json')->fail(100022);
        }
        return app('json')->success(100021, ['uid' => $uid]);
    }

    /**
     * Cập nhật người dùng
     * @param $uid
     * @return mixed
     */
    public function update($uid)
    {
        $data = $this->request->postMore([
            ['real_name', ''],
            ['phone', 0],
            ['mark', ''],
            ['pwd', ''],
            ['level', 0],
            ['spread_open', 1],
            ['is_promoter', 0],
            ['status', 1]
        ]);
        if (!$uid) return app('json')->fail(100100);
        $this->services->saveUser((int)$uid, $data);
        return app('json')->success(100001);
    }

    /**
     * Liên quan đến tặng
     * @param int $uid
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function give($uid)
    {
        $data = $this->request->postMore([
            ['money_status', 0],
            ['money', 0],
            ['integration_status', 0],
            ['integration', 0],
            ['days', 0],
            ['coupon', 0]
        ]);
        if (!$uid) return app('json')->fail(100100);
        if (!$this->services->otherGive((int)$uid, $data)) {
            return app('json')->fail(100005);
        }
        return app('json')->success(100010);
    }

    /**
     * Lấy chi tiết người dùng
     * @param $uid
     * @return \think\Response
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/06/20
     */
    public function info($uid)
    {
        if (!$uid) return app('json')->fail(100100);
        $data = $this->services->userInfo($uid);
        return app('json')->success(compact('data'));
    }

    /**
     * Tặng số dư
     * @param int $uid
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function giveBalance($uid)
    {
        $data = $this->request->postMore([
            ['money_status', 0],
            ['money', 0],
            ['integration_status', 0],
            ['integration', 0],
            ['days', 0],
            ['coupon', 0]
        ]);
        if (!$uid) return app('json')->fail(100100);
        if (!$this->services->otherGive((int)$uid, $data)) {
            return app('json')->fail(100005);
        }
        return app('json')->success(100010);
    }

    /**
     * Tặng điểm thưởng
     * @param int $uid
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function givePoint($uid)
    {
        $data = $this->request->postMore([
            ['money_status', 0],
            ['money', 0],
            ['integration_status', 0],
            ['integration', 0],
            ['days', 0],
            ['coupon', 0]
        ]);
        if (!$uid) return app('json')->fail(100100);
        if (!$this->services->otherGive((int)$uid, $data)) {
            return app('json')->fail(100005);
        }
        return app('json')->success(100010);
    }

    /**
     * Chỉnh sửa số dư
     * @param $uid
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function changeBalance($uid)
    {
        [$money] = $this->request->postMore([
            ['money', 0],
        ], true);
        if (!$uid) return app('json')->fail(100100);
        $this->services->changeUserData((int)$uid, $money, 'now_money');
        return app('json')->success('Sửa thành công');
    }

    /**
     * Chỉnh sửa điểm thưởng
     * @param $uid
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function changePoint($uid)
    {
        [$integral] = $this->request->postMore([
            ['integral', 0],
        ], true);
        if (!$uid) return app('json')->fail(100100);
        $this->services->changeUserData((int)$uid, $integral, 'integral');
        return app('json')->success('Sửa thành công');
    }
}