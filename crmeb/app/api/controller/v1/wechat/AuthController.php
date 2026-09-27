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

namespace app\api\controller\v1\wechat;


use app\Request;
use app\services\activity\live\LiveRoomServices;
use app\services\wechat\RoutineServices;

/**
 * Liên quan Mini Program
 * Class AuthController
 * @package app\api\controller\wechat
 */
class AuthController
{
    protected $services = NUll;

    /**
     * AuthController constructor.
     * @param RoutineServices $services
     */
    public function __construct(RoutineServices $services)
    {
        $this->services = $services;
    }

    /**
     * Đăng nhập ủy quyền Mini Program
     * @param Request $request
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/02/24
     */
    public function mp_auth(Request $request)
    {
        [$code, $cache_key, $login_type, $spread_spid, $spread_code, $iv, $encryptedData] = $request->postMore([
            ['code', ''],
            ['cache_key', ''],
            ['login_type', ''],
            ['spread_spid', 0],
            ['spread_code', ''],
            ['iv', ''],
            ['encryptedData', ''],
        ], true);
        $token = $this->services->mp_auth($code, $cache_key, $login_type, $spread_spid, $spread_code, $iv, $encryptedData);
        if ($token) {
            if (isset($token['key']) && $token['key']) {
                return app('json')->success(410022, $token);
            } else {
                return app('json')->success(410001, [
                    'userInfo' => $token['userInfo']
                ]);
            }
        } else
            return app('json')->fail(410019);
    }

    /**
     * Lấy logo ủy quyền
     * @return mixed
     */
    public function get_logo()
    {
        $logo = sys_config('wap_login_logo');
        if (strstr($logo, 'http') === false && $logo) $logo = sys_config('site_url') . $logo;
        return app('json')->success(['logo_url' => str_replace('\\', '/', $logo)]);
    }

    /**
     * Callback thanh toán Mini Program
     */
    public function notify()
    {
        return $this->services->notify();
    }

    /**
     * Lấy id tin nhắn đăng ký Mini Program
     * @return mixed
     */
    public function temp_ids()
    {
        return app('json')->success($this->services->tempIds());
    }

    /**
     * Lấy danh sách livestream Mini Program
     * @param Request $request
     * @param LiveRoomServices $liveRoom
     * @return mixed
     */
    public function live(Request $request, LiveRoomServices $liveRoom)
    {
        return app('json')->success($liveRoom->userList([]));
    }

    /**
     * Lấy phát lại livestream
     * @param $id
     * @param LiveRoomServices $lvieRoom
     * @return mixed
     */
    public function livePlaybacks($id, LiveRoomServices $lvieRoom)
    {
        return app('json')->success($lvieRoom->getPlaybacks((int)$id));
    }
}
