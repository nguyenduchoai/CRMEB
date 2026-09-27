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

namespace crmeb\services\easywechat\v3pay;


use crmeb\exceptions\PayException;
use crmeb\services\CacheService;

/**
 * Class Certficates
 * @package crmeb\services\easywechat\v3pay
 */
trait Certficates
{

    /**
     * @param string|null $key
     * @return array|mixed|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getCertficatescAttr(string $key = null)
    {
        $cacheKey = '_wx_v3' . $this->app['config']['v3_payment']['serial_no'];
        if (CacheService::has($cacheKey)) {
            $res = CacheService::get($cacheKey);
            if ($key && $res) {
                return $res[$key] ?? null;
            } else {
                return $res;
            }
        }
        $certficates = $this->getCertficates();
        CacheService::set($cacheKey, $certficates, 3600 * 24 * 30);
        if ($key && $certficates) {
            return $certficates[$key] ?? null;
        }
        return $certficates;
    }

    /**
     * get certficates.
     *
     * @return array
     */
    public function getCertficates()
    {
        $response = $this->request('v3/certificates', 'GET', [], false);
        if (isset($response['code'])) {
            throw new PayException($response['message']);
        }
        $certificates = $response['data'][0];
        $certificates['certificates'] = $this->decrypt($certificates['encrypt_certificate']);
        unset($certificates['encrypt_certificate']);
        return $certificates;
    }
}
