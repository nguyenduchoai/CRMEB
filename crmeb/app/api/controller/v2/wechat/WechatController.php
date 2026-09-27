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
namespace app\api\controller\v2\wechat;

use app\Request;
use app\services\wechat\WechatServices;
use crmeb\services\CacheService;

/**
 * Class WechatController
 * @package app\api\controller\v2\wechat
 */
class WechatController
{
    protected $services = NUll;

    /**
     * WechatController constructor.
     * @param WechatServices $services
     */
    public function __construct(WechatServices $services)
    {
        $this->services = $services;
    }

    /**
     * Đăng nhập ủy quyền OA WeChat, trả về token
     * @param $spread
     * @return \think\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/12
     */
    public function authLogin($spread = '', $agent_id = '')
    {
        $data = $this->services->authLogin($spread, $agent_id);
        return app('json')->success($data);
    }

    /**
     * Ủy quyền liên kết số điện thoại qua OA WeChat
     * @param string $key
     * @param string $phone
     * @param string $captcha
     * @return \think\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/12
     */
    public function authBindingPhone($key = '', $phone = '', $captcha = '')
    {
        //Xác thực mã xác thực
        $verifyCode = CacheService::get('code_' . $phone);
        if (!$verifyCode)
            return app('json')->fail(410009);
        $verifyCode = substr($verifyCode, 0, 6);
        if ($verifyCode != $captcha) {
            CacheService::delete('code_' . $phone);
            return app('json')->fail(410010);
        }
        CacheService::delete('code_' . $phone);
        $data = $this->services->authBindingPhone($key, $phone);
        return app('json')->success($data);
    }
}
