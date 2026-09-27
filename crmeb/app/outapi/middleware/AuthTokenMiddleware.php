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

namespace app\outapi\middleware;


use app\Request;
use app\services\out\OutAccountServices;
use app\services\out\OutInterfaceServices;
use crmeb\interfaces\MiddlewareInterface;
use think\facade\Config;

/**
 * Class AuthTokenMiddleware
 * @package app\outapi\middleware
 */
class AuthTokenMiddleware implements MiddlewareInterface
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
        /** @var OutAccountServices $services */
        $services = app()->make(OutAccountServices::class);
        $outInfo = $services->parseToken($token);
        $request->macro('outId', function () use (&$outInfo) {
            return (int)$outInfo['id'];
        });

        $request->macro('outInfo', function () use (&$outInfo) {
            return $outInfo;
        });
        /** @var OutInterfaceServices $outInterfaceServices */
        $outInterfaceServices = app()->make(OutInterfaceServices::class);
        $outInterfaceServices->verifyAuth($request);

        return $next($request);
    }
}
