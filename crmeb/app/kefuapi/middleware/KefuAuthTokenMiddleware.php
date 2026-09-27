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

namespace app\kefuapi\middleware;


use app\Request;
use app\services\kefu\LoginServices;
use crmeb\interfaces\MiddlewareInterface;
use think\facade\Config;

/**
 * Class KefuAuthTokenMiddleware
 * @package app\kefu\middleware
 */
class KefuAuthTokenMiddleware implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function handle(Request $request, \Closure $next)
    {
        $token = trim(ltrim($request->header(Config::get('cookie.token_name', 'Authori-zation')), 'Bearer'));
        /** @var LoginServices $services */
        $services = app()->make(LoginServices::class);
        $kefuInfo = $services->parseToken($token);
        $request->macro('kefuId', function () use (&$kefuInfo) {
            return (int)$kefuInfo['id'];
        });

        $request->macro('kefuInfo', function () use (&$kefuInfo) {
            return $kefuInfo;
        });

        return $next($request);
    }
}
