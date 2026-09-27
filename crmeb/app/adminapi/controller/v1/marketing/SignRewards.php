<?php
/**
 * @author: Wu Xi
 * @email: 442384644@qq.com
 * @date: 2023/7/31
 */

namespace app\adminapi\controller\v1\marketing;

use app\adminapi\controller\AuthController;
use app\services\system\SystemSignRewardServices;
use think\facade\App;

class SignRewards extends AuthController
{
    /**
     * @param App $app
     * @param SystemSignRewardServices $services
     */
    public function __construct(App $app, SystemSignRewardServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách phần thưởng điểm danh
     * @return \think\Response
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/7/31
     */
    public function index()
    {
        [$type] = $this->request->getMore([
            ['type', 0]
        ], true);
        $data = $this->services->getList($type);
        return app('json')->success($data);
    }

    /**
     * Thêm thưởng điểm danh
     * @return \think\Response
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/7/31
     */
    public function addRewards()
    {
        [$type] = $this->request->getMore([
            ['type', 0]
        ], true);
        $data = $this->services->rewardsForm(0, $type);
        return app('json')->success($data);
    }

    /**
     * Sửa thưởng điểm danh
     * @param $id
     * @return \think\Response
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/7/31
     */
    public function editRewards($id)
    {
        $data = $this->services->rewardsForm($id);
        return app('json')->success($data);
    }

    /**
     * Lưu phần thưởng điểm danh
     * @param $id
     * @return \think\Response
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/7/31
     */
    public function saveRewards($id)
    {
        $data = $this->request->postMore([
            ['type', 0],
            ['days', 0],
            ['point', 0],
            ['exp', 0]
        ]);
        $this->services->saveRewards($id, $data);
        return app('json')->success(100000);
    }

    /**
     * Xóa phần thưởng điểm danh
     * @param $id
     * @return \think\Response
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/7/31
     */
    public function delRewards($id)
    {
        $this->services->delete($id);
        return app('json')->success(100002);
    }
}