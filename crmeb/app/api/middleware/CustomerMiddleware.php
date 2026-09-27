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
namespace app\api\middleware;

use app\services\kefu\service\StoreServiceServices;
use app\services\order\DeliveryServiceServices;
use app\services\system\store\SystemStoreStaffServices;
use crmeb\interfaces\MiddlewareInterface;
use app\Request;

class CustomerMiddleware implements MiddlewareInterface
{

    public function handle(Request $request, \Closure $next)
    {
        $uid = $request->uid();
        /** @var StoreServiceServices $services */
        $services = app()->make(StoreServiceServices::class);
        /** @var SystemStoreStaffServices $storeServices */
        $storeServices = app()->make(SystemStoreStaffServices::class);
        $rule = trim(strtolower($request->rule()->getRule()));
        /** @var DeliveryServiceServices $deliveryService */
        $deliveryService = app()->make(DeliveryServiceServices::class);
        $isDelivery = $deliveryService->checkoutIsService($uid);
        $withRule = ['/api/order/order_verific', "/api/admin/order/detail/<orderId>"];
        if (((!$services->checkoutIsService(['uid' => $uid, 'status' => 1, 'customer' => 1]) && !$storeServices->verifyStatus($uid)) && !$isDelivery) && !(in_array($rule, $withRule)))
            return app('json')->fail('Không đủ quyền');
        return $next($request);
    }
}
