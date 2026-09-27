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
namespace crmeb\services\printer;


use crmeb\exceptions\AdminException;
use crmeb\services\CacheService;
use crmeb\services\HttpService;
use think\helper\Str;

/**
 *
 * Class AccessToken
 * @package crmeb\services\printer
 */
class AccessToken extends HttpService
{

    /**
     * token
     * @var array
     */
    protected $accessToken;

    /**
     * API yêu cầu
     * @var string
     */
    protected $apiUrl;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * Số terminal
     * @var string
     */
    protected $machineCode;

    /**
     * id nhà phát triển
     * @var string
     */
    protected $partner;

    /**
     * Loại driver
     * @var string
     */
    protected $name;

    /**
     * Tên file cấu hình
     * @var string
     */
    protected $configFile;

    /**
     * api key
     * @var string
     */
    protected $apiKey;

    /**
     * Feieyun SN
     * @var string
     */
    protected $feySn;

    /**
     * Feieyun UYEK
     * @var string
     */
    protected $feyUkey;

    /**
     * Feieyun USER
     * @var string
     */
    protected $feyUser;

    public function __construct(array $config = [], string $name, string $configFile)
    {
        $this->clientId = $config['clientId'] ?? null;
        $this->apiKey = $config['apiKey'] ?? null;
        $this->partner = $config['partner'] ?? null;
        $this->machineCode = $config['terminal'] ?? null;
        $this->feyUser = $config['feyUser'] ?? null;
        $this->feyUkey = $config['feyUkey'] ?? null;
        $this->feySn = $config['feySn'] ?? null;
        $this->name = $name;
        $this->configFile = $configFile;
    }

    /**
     * Lấy token
     * @return mixed|null|string
     * @throws \Exception
     */
    public function getAccessToken()
    {
        if (isset($this->accessToken[$this->name])) {
            return $this->accessToken[$this->name];
        }

        $action = 'get' . Str::studly($this->name) . 'AccessToken';
        if (method_exists($this, $action)) {
            return $this->{$action}();
        } else {
            throw new \RuntimeException(__CLASS__ . '->' . $action . '(),Method not worn in');
        }
    }

    /**
     * Lấy token Yilianyun
     * @return mixed|null|string
     * @throws \Exception
     */
    protected function getYiLianYunAccessToken()
    {
        $this->accessToken[$this->name] = CacheService::remember('YLY_access_token', function () {
            $request = self::postRequest('https://open-api.10ss.net/oauth/oauth', [
                'client_id' => $this->clientId,
                'grant_type' => 'client_credentials',
                'sign' => strtolower(md5($this->clientId . time() . $this->apiKey)),
                'scope' => 'all',
                'timestamp' => time(),
                'id' => $this->createUuid(),
            ]);
            $request = json_decode($request, true);
            $request['error'] = $request['error'] ?? 0;
            $request['error_description'] = $request['error_description'] ?? '';
            if ($request['error'] == 0 && $request['error_description'] == 'success') {
                return $request['body']['access_token'] ?? '';
            }
            return '';
        }, 86400);
        if (!$this->accessToken[$this->name]){
            CacheService::delete('YLY_access_token');
            throw new AdminException(400718);
        }
        return $this->accessToken[$this->name];
    }

    /**
     * Tạo UUID4
     * @return string
     */
    public function createUuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }

    /**
     * Lấy thuộc tính
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (in_array($name, ['clientId', 'apiKey', 'accessToken', 'partner', 'terminal', 'machineCode', 'feyUser', 'feyUkey', 'feySn'])) {
            return $this->{$name};
        }
    }
}
