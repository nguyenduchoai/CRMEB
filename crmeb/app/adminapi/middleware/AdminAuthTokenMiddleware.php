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

namespace app\adminapi\middleware;


use app\Request;
use app\services\system\admin\AdminAuthServices;
use crmeb\interfaces\MiddlewareInterface;
use think\facade\Config;
use crmeb\services\CacheService;

/**
 * Middleware xác thực đăng nhập quản trị
 * Class AdminAuthTokenMiddleware
 * @package app\adminapi\middleware
 */
class AdminAuthTokenMiddleware implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/04/07
     */
    public function handle(Request $request, \Closure $next)
    {
        $token = trim(ltrim($request->header(Config::get('cookie.token_name', 'Authori-zation')), 'Bearer'));
        if (!$token) {
            $token = trim(ltrim($request->get('token')));
        }
        /** @var AdminAuthServices $service */
        $service = app()->make(AdminAuthServices::class);
        $adminInfo = $service->parseToken($token);
        $request->macro('isAdminLogin', function () use (&$adminInfo) {
            return !is_null($adminInfo);
        });
        $request->macro('adminId', function () use (&$adminInfo) {
            return $adminInfo['id'];
        });

        $request->macro('adminInfo', function () use (&$adminInfo) {
            return $adminInfo;
        });

        return $next($request);
    }
}
