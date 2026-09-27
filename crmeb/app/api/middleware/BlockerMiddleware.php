<?php
/**
 *  +----------------------------------------------------------------------
 *  | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
 *  +----------------------------------------------------------------------
 *  | Author: CRMEB Team <admin@crmeb.com>
 *  +----------------------------------------------------------------------
 */

namespace app\api\middleware;


use app\Request;
use crmeb\exceptions\ApiException;
use crmeb\interfaces\MiddlewareInterface;
use crmeb\services\CacheService;
use think\facade\Config;

/**
 * Khóa Redis
 * Class BlockerMiddleware
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/2/8
 * @package app\api\middleware
 */
class BlockerMiddleware implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/11/21
     */
    public function handle(Request $request, \Closure $next)
    {
        if (Config::get('cache.default') == 'file') {
            return $next($request);
        }

        $uid = $request->uid();
        $key = md5($request->rule()->getRule() . $uid);
        if (!CacheService::setMutex($key)) {
            throw new ApiException('Yêu cầu quá thường xuyên, vui lòng thử lại sau');
        }

        $response = $next($request);

        $this->after($response, $key);

        return $response;
    }

    /**
     * @param $response
     * @param $key
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2022/11/22
     */
    public function after($response, $key)
    {
        CacheService::delMutex($key);
    }
}
