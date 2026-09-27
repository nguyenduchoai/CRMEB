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

namespace crmeb\services\serve\storage;


use crmeb\basic\BaseStorage;
use crmeb\exceptions\AdminException;
use crmeb\services\AccessTokenServeService;

/**
 * Class Crmeb
 * @package crmeb\services\serve\storage
 */
class Crmeb extends BaseStorage
{

    protected $accessToken;

    /**
     * Crmeb constructor.
     * @param string $name
     * @param AccessTokenServeService $service
     * @param string|null $configFile
     */
    public function __construct(string $name, AccessTokenServeService $service, string $configFile = null)
    {
        $this->accessToken = $service;
    }

    protected function initialize(array $config)
    {
        // TODO: Implement initialize() method.
    }

    /**
     * Lấy thông tin người dùng
     * @param string $account
     * @param string $secret
     * @return array|mixed
     */
    public function getUser()
    {
        return $this->accessToken->httpRequest('v2/user/info');
    }

    /**
     * Bản ghi lượng sử dụng
     * @param int $page
     * @param int $limit
     * @param int $type
     * @return array|mixed
     */
    public function record(int $page, int $limit, int $type, $status = '')
    {
        $typeContent = [1 => 'sms', 2 => 'expr_dump', 3 => 'expr_query', 4 => 'copy'];
        if (!isset($typeContent[$type])) {
            throw new AdminException(100100);
        }
        $data = ['page' => $page, 'limit' => $limit, 'type' => $typeContent[$type]];
        if ($type == 1 && $status != '') {
            $data['status'] = $status;
        }
        return $this->accessToken->httpRequest('user/record', $data);
    }
}
