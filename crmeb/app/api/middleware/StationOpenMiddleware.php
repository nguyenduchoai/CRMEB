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

namespace app\api\middleware;


use app\Request;
use crmeb\exceptions\ApiException;
use crmeb\interfaces\MiddlewareInterface;

/**
 * Class StationOpenMiddleware
 * @package app\api\middleware
 */
class StationOpenMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next)
    {
        if (!sys_config('station_open', 1)) {
            throw new ApiException('Trang web đang nâng cấp, vui lòng truy cập lại sau', [], 402);
        }
        return $next($request);
    }
}
