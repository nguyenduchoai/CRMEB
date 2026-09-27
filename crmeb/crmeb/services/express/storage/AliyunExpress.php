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

namespace crmeb\services\express\storage;

use crmeb\services\express\BaseExpress;
use crmeb\services\HttpService;

/**
 * Class AliyunExpress
 * @package crmeb\services\express\storage
 */
class AliyunExpress extends BaseExpress
{
    /**
     * @var string[]
     */
    protected static $api = [
        'query' => 'https://wuliu.market.alicloudapi.com/kdi'
    ];

    /**
     * @param string $no
     * @param string $type
     * @return bool|mixed
     */
    public function query(string $no = '', string $type = '', string $appCode = '')
    {
        if (!$appCode) return false;
        $res = HttpService::getRequest(self::$api['query'], compact('no', 'type'), ['Authorization:APPCODE ' . $appCode]);
        return json_decode($res, true) ?: false;
    }

    public function open()
    {
        // TODO: Implement open() method.
    }

    public function dump($data)
    {
        // TODO: Implement dump() method.
    }

    public function temp(string $com)
    {
        // TODO: Implement temp() method.
    }
}
