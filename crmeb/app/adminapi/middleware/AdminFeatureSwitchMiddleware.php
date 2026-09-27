<?php

namespace app\adminapi\middleware;


use app\Request;
use crmeb\interfaces\MiddlewareInterface;
use crmeb\utils\FeatureSwitch;

/**
 * Chặn route của tính năng đang tắt trong .env (xem crmeb\utils\FeatureSwitch).
 * Chạy trước khi khởi tạo controller, nên controller/service của tính năng bị tắt không được tạo.
 * Cách dùng: ->middleware(AdminFeatureSwitchMiddleware::class, FeatureSwitch::CRUD_MAKE)
 * Class AdminFeatureSwitchMiddleware
 * @package app\adminapi\middleware
 */
class AdminFeatureSwitchMiddleware implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @param string ...$features Các tính năng đều phải đang bật
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, string ...$features)
    {
        foreach ($features as $feature) {
            FeatureSwitch::check($feature);
        }
        return $next($request);
    }
}
