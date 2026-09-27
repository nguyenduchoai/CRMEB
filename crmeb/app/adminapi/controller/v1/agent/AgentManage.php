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
use app\services\agent\AgentManageServices;
use app\services\user\UserServices;
use think\facade\App;

/**
 * Controller quản lý cộng tác viên
 * Class AgentManage
 * @package app\adminapi\controller\v1\agent
 */
class AgentManage extends AuthController
{
    /**
     * AgentManage constructor.
     * @param App $app
     * @param AgentManageServices $services
     */
    public function __construct(App $app, AgentManageServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách quản lý CTV
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['nickname', ''],
            ['data', ''],
        ]);
        return app('json')->success($this->services->agentSystemPage($where));
    }

    /**
     * Thống kê phần đầu trang CTV
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get_badge()
    {
        $where = $this->request->getMore([
            ['data', '', '', 'time'],
            ['nickname', ''],
        ]);
        return app('json')->success(['res' => $this->services->getSpreadBadge($where)]);
    }

    /**
     * Danh sách người được giới thiệu
     * @return mixed
     */
    public function get_stair_list()
    {
        $where = $this->request->getMore([
            ['uid', 0],
            ['data', ''],
            ['nickname', ''],
            ['type', '']
        ]);
        return app('json')->success($this->services->getStairList($where));
    }

    /**
     * Thống kê phần đầu danh sách người giới thiệu
     * @return mixed
     */
    public function get_stair_badge()
    {
        $where = $this->request->getMore([
            ['uid', ''],
            ['data', ''],
            ['nickname', ''],
            ['type', ''],
        ]);
        return app('json')->success(['res' => $this->services->getSairBadge($where)]);
    }

    /**
     * Thống kê danh sách đơn hàng giới thiệu
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get_stair_order_list()
    {
        $where = $this->request->getMore([
            ['uid', 0],
            ['data', ''],
            ['order_id', ''],
            ['type', ''],
        ]);
        return app('json')->success($this->services->getStairOrderList((int)$where['uid'], $where));
    }

    /**
     * Xem mã QR giới thiệu OA WeChat
     * @param string $uid
     * @param string $action
     * @return mixed
     */
    public function look_code($uid = '', $action = '')
    {
        if (!$uid || !$action) return app('json')->fail(100100);
        try {
            if (method_exists($this, $action)) {
                $res = $this->$action($uid);
                if ($res)
                    return app('json')->success($res);
                else
                    return app('json')->fail(100016);
            } else
                return app('json')->fail(100029);
        } catch (\Exception $e) {
            return app('json')->fail(400212, ['line' => $e->getLine(), 'messag' => $e->getMessage()]);
        }
    }

    /**
     * Lấy mã QR OA WeChat
     * @param $uid
     * @return array
     */
    public function wechant_code($uid)
    {
        $qr_code = $this->services->wechatCode((int)$uid);
        if (isset($qr_code['url']))
            return ['code_src' => $qr_code['url']];
        else
            return app('json')->fail(100016);
    }

    /**
     * Xem mã QR giới thiệu Mini Program
     * @param string $uid
     */
    public function look_xcx_code($uid = '')
    {
        if (!strlen(trim($uid))) {
            return app('json')->fail(100100);
        }
        return app('json')->success($this->services->lookXcxCode((int)$uid));
    }

    /**
     * Xem mã QR giới thiệu H5
     * @param string $uid
     * @return mixed|string
     */
    public function look_h5_code($uid = '')
    {
        if (!strlen(trim($uid))) return app('json')->fail(100100);
        return app('json')->success($this->services->lookH5Code((int)$uid));
    }

    /**
     * Gỡ quyền giới thiệu của một người dùng
     * @param $uid
     * @return mixed
     */
    public function delete_spread($uid)
    {
        if (!$uid) app('json')->fail(100100);
        return app('json')->success($this->services->delSpread((int)$uid) ? 100014 : 100015);
    }

    /**
     * Sửa người giới thiệu
     * @param UserServices $services
     * @return mixed
     */
    public function editSpread(UserServices $services)
    {
        [$uid, $spreadUid] = $this->request->postMore([
            [['uid', 'd'], 0],
            [['spread_uid', 'd'], 0],
        ], true);
        if (!$uid || !$spreadUid) {
            return app('json')->fail(100100);
        }
        if ($uid == $spreadUid) {
            return app('json')->fail(400213);
        }
        $userInfo = $services->get($uid);
        if (!$userInfo) {
            return app('json')->fail(400214);
        }
        if (!$services->count(['uid' => $spreadUid])) {
            return app('json')->fail(400215);
        }
        if ($userInfo->spread_uid == $spreadUid) {
            return app('json')->fail(400216);
        }
        $spreadInfo = $services->get($spreadUid);
        if ($spreadInfo->spread_uid == $uid) {
            return app('json')->fail(400217);
        }
        //Giảm số người được giới thiệu của người giới thiệu trước đó
        if ($userInfo->spread_uid) {
            $oldSpread = $services->get($userInfo->spread_uid);
            $oldSpread->spread_count = $oldSpread->spread_count - 1;
            $oldSpread->save();
        }
        $spreadInfo->spread_count = $spreadInfo->spread_count + 1;
        $spreadInfo->save();
        $userInfo->spread_uid = $spreadUid;
        $userInfo->spread_time = time();
        $userInfo->division_id = $spreadInfo->division_id;
        $userInfo->agent_id = $spreadInfo->agent_id;
        $userInfo->staff_id = $spreadInfo->staff_id;
        $userInfo->save();
        return app('json')->success(100001);
    }

    /**
     * Hủy tư cách giới thiệu của cộng tác viên
     * @param $uid
     * @return mixed
     */
    public function delete_system_spread($uid)
    {
        if (!$uid) app('json')->fail(100100);
        return app('json')->success($this->services->delSystemSpread((int)$uid) ? 100019 : 100020);
    }

    /**
     * Lấy biểu mẫu tặng cấp độ CTV
     * @param AgentLevelServices $services
     * @param $uid
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLevelForm(AgentLevelServices $services, $uid)
    {
        if (!$uid) app('json')->fail(100100);
        return app('json')->success($services->levelForm((int)$uid));
    }

    /**
     * Tặng cấp độ CTV
     * @param AgentLevelServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function giveAgentLevel(AgentLevelServices $services)
    {
        [$uid, $id] = $this->request->postMore([
            [['uid', 'd'], 0],
            [['id', 'd'], 0],
        ], true);
        if (!$uid || !$id) {
            return app('json')->fail(100100);
        }
        return app('json')->success($services->givelevel((int)$uid, (int)$id) ? 400218 : 400219);
    }
}
