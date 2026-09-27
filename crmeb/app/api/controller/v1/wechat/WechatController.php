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

namespace app\api\controller\v1\wechat;


use app\Request;
use app\services\wechat\WechatServices as WechatAuthServices;
use crmeb\services\CacheService;

/**
 * OA WeChat
 * Class WechatController
 * @package app\api\controller\wechat
 */
class WechatController
{
    protected $services = NUll;

    /**
     * WechatController constructor.
     * @param WechatAuthServices $services
     */
    public function __construct(WechatAuthServices $services)
    {
        $this->services = $services;
    }

    /**
     * Dịch vụ OA WeChat
     * @return \think\Response
     */
    public function serve()
    {
        return $this->services->serve();
    }

    /**
     * Dịch vụ Mini Program và OA WeChat
     * @return \think\Response
     */
    public function miniServe()
    {
        return $this->services->miniServe();
    }

    /**
     * Callback thanh toán bất đồng bộ
     */
    public function notify()
    {
        return $this->services->notify();
    }

    public function v3notify()
    {
        return $this->services->v3notify();
    }

    /**
     * Lấy thông tin cấu hình quyền OA WeChat
     * @param Request $request
     * @return mixed
     */
    public function config(Request $request)
    {
        return app('json')->success($this->services->config($request->get('url')));
    }

    /**
     * Đăng nhập WeChat trên App
     * @param Request $request
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function appAuth(Request $request)
    {
        [$userInfo, $phone, $captcha] = $request->postMore([
            ['userInfo', []],
            ['phone', ''],
            ['code', '']
        ], true);
        if ($phone) {
            if (!$captcha) {
                return app('json')->fail('Vui lòng nhập mã xác thực');
            }
            //Xác thực mã xác thực
            $verifyCode = CacheService::get('code_' . $phone);
            if (!$verifyCode)
                return app('json')->fail('Vui lòng lấy mã xác thực trước');
            $verifyCode = substr($verifyCode, 0, 6);
            if ($verifyCode != $captcha) {
                CacheService::delete('code_' . $phone);
                return app('json')->fail('Mã xác thực không đúng');
            }
        }
        $token = $this->services->appAuth($userInfo, $phone);
        if ($token) {
            return app('json')->success('Đăng nhập thành công', $token);
        } else if ($token === false) {
            return app('json')->success('Đăng nhập thành công', ['isbind' => true]);
        } else {
            return app('json')->fail('Đăng nhập thất bại');
        }
    }

    /**
     * Mã QR theo dõi
     * @return mixed
     * @throws \Exception
     */
    public function follow()
    {
        $data = $this->services->follow();
        if ($data) {
            return app('json')->success($data);
        } else {
            return app('json')->fail('Lấy dữ liệu thất bại');
        }

    }
}
