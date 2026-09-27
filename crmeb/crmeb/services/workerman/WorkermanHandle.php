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

namespace crmeb\services\workerman;


use app\services\system\admin\AdminAuthServices;
use crmeb\exceptions\AuthException;
use Workerman\Connection\TcpConnection;

class WorkermanHandle
{
    protected $service;

    public function __construct(WorkermanService &$service)
    {
        $this->service = &$service;
    }

    public function login(TcpConnection &$connection, array $res, Response $response)
    {
        if (!isset($res['data']) || !$token = $res['data']) {
            return $response->close([
                'msg' => 'Ủy quyền thất bại!'
            ]);
        }

        try {
            /** @var AdminAuthServices $adminAuthService */
            $adminAuthService = app()->make(AdminAuthServices::class);
            $authInfo = $adminAuthService->parseToken($token);
        } catch (AuthException $e) {
            return $response->close([
                'msg' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
        }

        if (!$authInfo || !isset($authInfo['id'])) {
            return $response->close([
                'msg' => 'Ủy quyền thất bại!'
            ]);
        }

        $connection->adminInfo = $authInfo;
        $connection->adminId = $authInfo['id'];
        $this->service->setUser($connection);

        return $response->success();
    }
}
