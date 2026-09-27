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
     * @var AgentManageServices
     */
    protected $services;

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
        // Lấy tham số yêu cầu: biệt danh, khoảng ngày
        $where = $this->request->getMore([
            ['nickname', ''],
            ['data', ''],
        ]);
        // Gọi tầng service để lấy danh sách cộng tác viên
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
        // Lấy tham số yêu cầu
        $where = $this->request->getMore([
            ['data', '', '', 'time'],
            ['nickname', ''],
        ]);
        // Trả về dữ liệu thống kê tiếp thị liên kết
        return app('json')->success(['res' => $this->services->getSpreadBadge($where)]);
    }

    /**
     * Danh sách người được giới thiệu
     * @return mixed
     */
    public function get_stair_list()
    {
        // Lấy tham số yêu cầu: ID người dùng, khoảng ngày, biệt danh, loại (cấp 1/cấp 2)
        $where = $this->request->getMore([
            ['uid', 0],
            ['data', ''],
            ['nickname', ''],
            ['type', '']
        ]);
        // Gọi tầng service để lấy danh sách người được giới thiệu
        return app('json')->success($this->services->getStairList($where));
    }

    /**
     * Thống kê phần đầu danh sách người giới thiệu
     * @return mixed
     */
    public function get_stair_badge()
    {
        // Lấy tham số yêu cầu
        $where = $this->request->getMore([
            ['uid', ''],
            ['data', ''],
            ['nickname', ''],
            ['type', ''],
        ]);
        // Trả về dữ liệu thống kê người được giới thiệu
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
        // Lấy tham số yêu cầu
        $where = $this->request->getMore([
            ['uid', 0],
            ['data', ''],
            ['order_id', ''],
            ['type', ''],
        ]);
        // Gọi tầng service để lấy danh sách đơn hàng giới thiệu
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
        if (!$uid || !$action) return app('json')->fail('Tham số không hợp lệ');
        try {
            // Gọi phương thức một cách động
            if (method_exists($this, $action)) {
                $res = $this->$action($uid);
                if ($res)
                    return app('json')->success($res);
                else
                    return app('json')->fail('Lấy dữ liệu thất bại');
            } else
                return app('json')->fail('Không có phương thức này');
        } catch (\Exception $e) {
            return app('json')->fail('Lấy mã QR giới thiệu thất bại, vui lòng kiểm tra cấu hình WeChat của bạn', ['line' => $e->getLine(), 'messag' => $e->getMessage()]);
        }
    }

    /**
     * Lấy mã QR OA WeChat
     * @param $uid
     * @return array
     */
    public function wechant_code($uid)
    {
        // Gọi tầng service để tạo mã QR OA WeChat
        $qr_code = $this->services->wechatCode((int)$uid);
        if (isset($qr_code['url']))
            return ['code_src' => $qr_code['url']];
        else
            return app('json')->fail('Lấy dữ liệu thất bại');
    }

    /**
     * Xem mã QR giới thiệu Mini Program
     * @param string $uid
     */
    public function look_xcx_code($uid = '')
    {
        if (!strlen(trim($uid))) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        // Gọi tầng service để tạo mã QR Mini Program
        return app('json')->success($this->services->lookXcxCode((int)$uid));
    }

    /**
     * Xem mã QR giới thiệu H5
     * @param string $uid
     * @return mixed|string
     */
    public function look_h5_code($uid = '')
    {
        if (!strlen(trim($uid))) return app('json')->fail('Tham số không hợp lệ');
        // Gọi tầng service để tạo mã QR H5
        return app('json')->success($this->services->lookH5Code((int)$uid));
    }

    /**
     * Gỡ quyền giới thiệu của một người dùng
     * @param $uid
     * @return mixed
     */
    public function delete_spread($uid)
    {
        if (!$uid) app('json')->fail('Tham số không hợp lệ');
        // Gọi tầng service để gỡ quyền giới thiệu
        return app('json')->success($this->services->delSpread((int)$uid) ? 'Cài đặt thành công' : 'Cài đặt thất bại');
    }

    /**
     * Sửa người giới thiệu
     * @param UserServices $services
     * @return mixed
     */
    public function editSpread(UserServices $services)
    {
        // Lấy tham số yêu cầu: ID người dùng, ID người giới thiệu cấp trên
        [$uid, $spreadUid] = $this->request->postMore([
            [['uid', 'd'], 0],
            [['spread_uid', 'd'], 0],
        ], true);
        if (!$uid || !$spreadUid) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        if ($uid == $spreadUid) {
            return app('json')->fail('Người giới thiệu không thể là chính mình');
        }
        // Lấy thông tin người dùng
        $userInfo = $services->get($uid);
        if (!$userInfo) {
            return app('json')->fail('Người dùng không tồn tại');
        }
        // Kiểm tra người giới thiệu cấp trên có tồn tại không
        if (!$services->count(['uid' => $spreadUid])) {
            return app('json')->fail('Người giới thiệu không tồn tại');
        }
        if ($userInfo->spread_uid == $spreadUid) {
            return app('json')->fail('Người giới thiệu hiện tại đã là người được chọn');
        }
        $spreadInfo = $services->get($spreadUid);
        if ($spreadInfo->spread_uid == $uid) {
            return app('json')->fail('Người giới thiệu không thể là cấp dưới của chính mình');
        }
        //Giảm số người được giới thiệu của người giới thiệu trước đó
        if ($userInfo->spread_uid) {
            $oldSpread = $services->get($userInfo->spread_uid);
            $oldSpread->spread_count = $oldSpread->spread_count - 1;
            $oldSpread->save();
        }
        // Tăng số người được giới thiệu của người giới thiệu mới
        $spreadInfo->spread_count = $spreadInfo->spread_count + 1;
        $spreadInfo->save();
        // Cập nhật quan hệ giới thiệu của người dùng
        $userInfo->spread_uid = $spreadUid;
        $userInfo->spread_time = time();
        $userInfo->division_id = $spreadInfo->division_id;
        $userInfo->agent_id = $spreadInfo->agent_id;
        $userInfo->staff_id = $spreadInfo->staff_id;
        $userInfo->save();
        return app('json')->success('Sửa thành công');
    }

    /**
     * Hủy tư cách giới thiệu của cộng tác viên
     * @param $uid
     * @return mixed
     */
    public function delete_system_spread($uid)
    {
        if (!$uid) app('json')->fail('Tham số không hợp lệ');
        // Gọi tầng service để hủy tư cách cộng tác viên
        return app('json')->success($this->services->delSystemSpread((int)$uid) ? 'Hủy thành công' : 'Hủy thất bại');
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
        if (!$uid) app('json')->fail('Tham số không hợp lệ');
        // Gọi AgentLevelServices để lấy form
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
        // Lấy tham số: ID người dùng, ID cấp độ
        [$uid, $id] = $this->request->postMore([
            [['uid', 'd'], 0],
            [['id', 'd'], 0],
        ], true);
        if (!$uid || !$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        // Gọi AgentLevelServices để tặng cấp độ
        return app('json')->success($services->givelevel((int)$uid, (int)$id) ? 'Tặng thành công' : 'Tặng thất bại');
    }
}
