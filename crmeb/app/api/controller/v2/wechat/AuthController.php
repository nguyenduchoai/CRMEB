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
use app\services\wechat\RoutineServices;
use crmeb\services\CacheService;


/**
 * Class AuthController
 * @package app\api\controller\v2\wechat
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
     * Trả về key cache thông tin người dùng, trả về có bắt buộc liên kết số điện thoại không
     * @param $code
     * @param string $spread_code
     * @param string $spread_spid
     * @return \think\Response
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/12
     */
    public function authType($code, $spread_code = '', $spread_spid = '')
    {
        $data = $this->services->authType($code, $spread_code, $spread_spid);
        return app('json')->success($data);
    }

    /**
     * Lấy token theo cache
     * @param $key
     * @return \think\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/12
     */
    public function authLogin($key)
    {
        $data = $this->services->authLogin($key);
        return app('json')->success($data);
    }

    /**
     * Ủy quyền lấy số điện thoại người dùng Mini Program, liên kết trực tiếp
     * @param string $code
     * @param string $iv
     * @param string $encryptedData
     * @param string $spread_code
     * @param string $spread_spid
     * @param string $key
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function authBindingPhone($code = '', $iv = '', $encryptedData = '', $spread_code = '', $spread_spid = '', $key = '')
    {
        if (!$code || !$iv || !$encryptedData)
            return app('json')->fail(100100);
        $data = $this->services->authBindingPhone($code, $iv, $encryptedData, $spread_code, $spread_spid, $key);
        if ($data) {
            return app('json')->success(410001, $data);
        } else
            return app('json')->fail(410019);
    }

    /**
     * Đăng nhập bằng số điện thoại trên Mini Program
     * @param string $key
     * @param string $phone
     * @param string $captcha
     * @param string $spread_code
     * @param string $spread_spid
     * @param string $code
     * @return \think\Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/12
     */
    public function phoneLogin($key = '', $phone = '', $captcha = '', $spread_code = '', $spread_spid = '', $code = '')
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
        $data = $this->services->phoneLogin($key, $phone, $spread_code, 0, $spread_spid, $code);
        return app('json')->success($data);
    }

    /**
     * Liên kết số điện thoại trên Mini Program
     * @param string $code
     * @param string $iv
     * @param string $encryptedData
     * @return \think\Response
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/02/24
     */
    public function bindingPhone($code = '', $iv = '', $encryptedData = '')
    {
        if (!$code || !$iv || !$encryptedData) return app('json')->fail(100100);
        $this->services->bindingPhone($code, $iv, $encryptedData);
        return app('json')->success(410016);
    }
}
