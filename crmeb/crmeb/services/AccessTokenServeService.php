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

namespace crmeb\services;


use crmeb\exceptions\ApiException;

/**
 * Class AccessTokenServeService
 * @package crmeb\services
 */
class AccessTokenServeService extends HttpService
{
    /**
     * Cấu hình
     * @var string
     */
    protected $account;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $cacheTokenPrefix = "_crmeb_plat";

    /**
     * @var string
     */
    protected $apiHost = 'https://sms.crmeb.net/api/';

    /**
     * Địa chỉ sandbox
     * @var string
     */
    protected $sandBoxApi = 'https://api_v2.crmeb.net/api/';

    /**
     * Chế độ sandbox
     * @var bool
     */
    protected $sandBox = false;

    /**
     * Interface đăng nhập
     */
    const USER_LOGIN = "v2/user/login";


    /**
     * AccessTokenServeService constructor.
     * @param string $account
     * @param string $secret
     */
    public function __construct(string $account, string $secret)
    {
        $this->account = $account;
        $this->secret = $secret;
    }

    /**
     * Lấy cấu hình
     * @return array
     */
    public function getConfig()
    {
        return [
            'access_key' => $this->account,
            'secret_key' => $this->secret
        ];
    }

    /**
     * Lấy token từ cache
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken()
    {
        $accessTokenKey = md5($this->account . '_v2_' . $this->secret . $this->cacheTokenPrefix);
        $cacheToken = CacheService::get($accessTokenKey);
        if (!$cacheToken) {
            $getToken = $this->getTokenFromServer();
            CacheService::set($accessTokenKey, $getToken['access_token'], $getToken['expires_in'] - 60);
            $cacheToken = $getToken['access_token'];
        }
        $this->accessToken = $cacheToken;

        return $cacheToken;

    }

    /**
     * Lấy token từ server
     * @return mixed
     */
    public function getTokenFromServer()
    {
        $params = [
            'access_key' => $this->account,
            'secret_key' => $this->secret,
        ];
        $response = $this->postRequest($this->get(self::USER_LOGIN), $params);
        $response = json_decode($response, true);
        if (!$response) {
            throw new ApiException('Lấy token thất bại{:msg}', ['msg' => '']);
        }
        if ($response['status'] === 200) {
            return $response['data'];
        } else {
            throw new ApiException('Lấy token thất bại{:msg}', ['msg' => ':' . $response['msg']]);
        }
    }

    /**
     * Gửi yêu cầu
     * @param string $url
     * @param array $data
     * @param string $method
     * @param bool $isHeader
     * @return array|mixed
     */
    public function httpRequest(string $url, array $data = [], string $method = 'POST', bool $isHeader = true, array $header = [])
    {
        if ($isHeader) {
            $this->getToken();
            if (!$this->accessToken) {
                throw new ApiException('Cấu hình đã thay đổi hoặc token đã hết hạn');
            }
            $header = array_merge($header, ['Authorization:Bearer-' . $this->accessToken]);
        }

        $res = $this->request($this->get($url), $method, $data, $header);
        if (!$res) {
            throw new ApiException('Lỗi nền tảng: đã xảy ra sự cố, vui lòng thử lại sau');

        }
        $result = json_decode($res, true) ?: false;
        if (!isset($result['status']) || $result['status'] != 200) {
            throw new ApiException($result['msg']);
        }
        return $result['data'] ?? [];

    }

    /**
     * @param string $apiUrl
     * @return string
     */
    public function get(string $apiUrl = '')
    {
        if ($this->sandBox) {
            return $this->sandBoxApi . $apiUrl;
        }
        return $this->apiHost . $apiUrl;
    }
}
