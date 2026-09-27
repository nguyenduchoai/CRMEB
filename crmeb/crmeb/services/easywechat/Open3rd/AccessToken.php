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

namespace crmeb\services\easywechat\open3rd;


use crmeb\exceptions\ApiException;
use crmeb\services\HttpService;

/**
 * Class AccessTokenServeService
 * @package crmeb\services
 */
class AccessToken extends HttpService
{
    /**
     * appid nền tảng bên thứ ba
     * @var string
     */
    protected $component_appid;

    /**
     * appsecret nền tảng bên thứ ba
     * @var string
     */
    protected $component_appsecret;

    /**
     * ticket được đẩy từ backend WeChat
     * @var string
     */
    protected $component_verify_ticket;

    /**
     * @var Cache|null
     */
    protected $cache;

    /**
     * token nền tảng bên thứ ba
     * @var string
     */
    protected $component_access_token;

    /**
     * appid của bên ủy quyền
     * @var string
     */
    protected $authorizer_appid;

    /**
     * Token gọi API (chỉ có giá trị trả về này khi OA WeChat/Mini Program được ủy quyền có quyền API)
     * @var string
     */
    protected $authorizer_access_token;

    /**
     * Refresh token (chỉ có giá trị trả về này khi OA WeChat được ủy quyền có quyền API), refresh token chủ yếu dùng để nền tảng bên thứ ba lấy và làm mới authorizer_access_token của người dùng đã ủy quyền. Nếu bị mất, chỉ có thể để người dùng ủy quyền lại thì mới lấy được refresh token mới. Sau khi người dùng ủy quyền lại, refresh token trước đó sẽ mất hiệu lực
     * @var string
     */
    protected $authorizer_refresh_token;

    /**
     * @var string
     */
    protected $cacheTokenPrefix = "component_access_token_crmeb";

    /**
     * Lấy token nền tảng bên thứ ba
     * @var string
     */
    const TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';

    /**
     * Dùng mã ủy quyền để lấy thông tin ủy quyền
     */
    const AUTH_INFO = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth';
    /**
     * Lấy, làm mới token gọi API
     */
    const AUTHORIZER_TOKEN = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token';

    /**
     * AccessTokenServeService constructor.
     * @param string $component_appid
     * @param string $component_appsecret
     * @param string $component_verify_ticket
     * @param string $authorizer_appid
     * @param Cache|null $cache
     */
    public function __construct(string $component_appid, string $component_appsecret, string $component_verify_ticket, string $authorizer_appid = '', $cache = null)
    {
        if (!$cache) {
            $cache = app()->make(\crmeb\services\CacheService::class);
        }
        $this->component_appid = $component_appid;
        $this->component_appsecret = $component_appsecret;
        $this->component_verify_ticket = $component_verify_ticket;
        $this->authorizer_appid = $authorizer_appid;
        $this->cache = $cache;
    }

    /**
     * Lấy cấu hình
     * @return array
     */
    public function getConfig()
    {
        return [
            'component_appid' => $this->component_appid,
            'component_appsecret' => $this->component_appsecret,
            'component_verify_ticket' => $this->component_verify_ticket,
        ];
    }

    /**
     * @return string
     */
    public function getComponentAppid()
    {
        return $this->component_appid;
    }

    /**
     * @return string
     */
    public function getComponentAppsecret()
    {
        return $this->component_appsecret;
    }

    /**
     * @return string
     */
    public function getAuthorizerAppid()
    {
        return $this->authorizer_appid;
    }

    /**
     * Lấy token cache của bên thứ ba
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getComponentToken()
    {
        $accessTokenKey = md5($this->component_appid . '_' . $this->component_appid . '_' . $this->component_verify_ticket . '_' . $this->cacheTokenPrefix);
        $component_access_token = $this->cache->get($accessTokenKey);
        if (!$component_access_token) {
            $getToken = $this->getTokenFromServer();
            $this->cache->set($accessTokenKey, $getToken['component_access_token'], $getToken['expires_in'] ? $getToken['expires_in'] - 200 : 7000);
            $component_access_token = $getToken['component_access_token'];
        }
        $this->component_access_token = $component_access_token;

        return $component_access_token;

    }

    /**
     * Lấy token từ server
     * @return mixed
     */
    public function getTokenFromServer()
    {
        $config = $this->getConfig();
        if (!$config['component_appid'] || !$config['component_appsecret']) {
            throw new ApiException('Vui lòng cấu hình component_appid, component_appsecret của bên thứ ba trước');
        }
        if (!$config['component_verify_ticket']) {
            throw new ApiException('Chưa cấu hình WeChat Open Platform hoặc chưa nhận được ticket được đẩy về, vui lòng đợi 10 phút rồi thử lại');
        }
        $res = $this->postRequest(self::TOKEN_URL, $config);
        $res = json_decode($res, true);
        if (!$res || $res['errcode'] != 0 || !isset($res['component_access_token']) || !$res['component_access_token']) {
            throw new ApiException('Lấy component_access_token thất bại, lý do:' . $res['errmsg']);
        }
        return $res;
    }

    /**
     * Lấy token của bên ủy quyền
     * @param $authorizer_appid
     * @return mixed
     */
    public function getAccessToken($authorizer_appid)
    {
        $accessTokenKey = md5('authorizer_access_token' . $authorizer_appid . '_' . $this->cacheTokenPrefix);
        $authorizer_access_token = $this->cache->get($accessTokenKey);
        if (!$authorizer_access_token) {
            $refreshTokenKey = md5('authorizer_refresh_token' . $authorizer_appid . '_' . $this->cacheTokenPrefix);
            $authorizer_refresh_token = $this->cache->get($refreshTokenKey);
            if (!$authorizer_refresh_token) {
                throw new ApiException('Vui lòng ủy quyền lại');
            }
            $res = $this->freshAuthorizationToken($authorizer_appid, $authorizer_refresh_token);
            $this->cache->set(md5('authorizer_access_token' . $res['authorizer_appid'] . '_' . $this->cacheTokenPrefix), $res['authorizer_access_token'], $res['expires_in'] ? $res['expires_in'] - 200 : 7000);
            $this->cache->set(md5('authorizer_refrssh_token' . $res['authorizer_appid'] . '_' . $this->cacheTokenPrefix), $res['authorizer_refresh_token'], 30 * 24 * 3600);
            $authorizer_access_token = $res['authorizer_access_token'];
        }
        return $authorizer_access_token;
    }

    /**
     * Lấy thông tin ủy quyền
     * @param $authorization_code Mã ủy quyền
     * @return authorizer_appid    string    appid của bên ủy quyền
     * @return authorizer_access_token string    Token gọi API (chỉ có giá trị trả về này khi OA WeChat/Mini Program được ủy quyền có quyền API)
     * @return authorizer_refresh_token    string    Refresh token (chỉ có giá trị trả về này khi OA WeChat được ủy quyền có quyền API), refresh token chủ yếu dùng để nền tảng bên thứ ba lấy và làm mới authorizer_access_token của người dùng đã ủy quyền. Nếu bị mất, chỉ có thể để người dùng ủy quyền lại thì mới lấy được refresh token mới. Sau khi người dùng ủy quyền lại, refresh token trước đó sẽ mất hiệu lực
     * @return array|bool|mixed
     */
    public function getAuthorizationInfo($authorization_code)
    {
        $res = $this->httpRequest(self::AUTH_INFO, ['authorization_code' => $authorization_code], false);
        if (!$res || $res['errcode'] != 0 || !isset($res['authorization_info']) || !$res['authorization_info']) {
            throw new ApiException('Lấy authorizer_access_token thất bại');
        }
        $res = $res['authorization_info'];
        $this->cache->set(md5('authorizer_access_token' . $res['authorizer_appid'] . '_' . $this->cacheTokenPrefix), $res['authorizer_access_token'], $res['expires_in'] ? $res['expires_in'] - 200 : 7000);
        //Chỉ có giá trị trả về này khi OA WeChat được ủy quyền có quyền API
        if (isset($res['authorizer_refrsh_token'])) {
            $this->cache->set(md5('authorizer_refresh_token' . $res['authorizer_appid'] . '_' . $this->cacheTokenPrefix), $res['authorizer_refrsh_token'], 30 * 24 * 3600);
        }
        return $res;
    }

    /**
     *  Lấy/làm mới token gọi API
     * @param $authorizer_appid appid của bên ủy quyền
     * @param $authorizer_refresh_token Refresh token, có được khi lấy thông tin ủy quyền
     * @return authorizer_access_token    string    Token của bên ủy quyền
     * @return authorizer_refresh_token    string    Refresh token
     * @return array|bool|mixed
     */
    public function freshAuthorizationToken($authorizer_appid, $authorizer_refresh_token)
    {
        $res = $this->postRequest(self::AUTHORIZER_TOKEN, ['component_access_token' => $this->component_access_token, 'authorizer_appid' => $authorizer_appid, 'authorizer_refresh_token' => $authorizer_refresh_token]);
        if (!$res || $res['errcode'] != 0 || !isset($res['authorizer_access_token']) || !$res['authorizer_access_token']) {
            throw new ApiException('Làm mới authorizer_access_token thất bại, vui lòng ủy quyền lại');
        }
        return $res;
    }


    /**
     * Gửi yêu cầu
     * @param string $url
     * @param array $data
     * @param string $method
     * @param bool $isHeader
     * @return array|mixed
     */
    public function httpRequest(string $url, array $data = [], bool $is_atuh = true, string $method = 'POST')
    {
        if (!$is_atuh) {
            $this->getComponentToken();
            if (!$this->component_access_token) {
                throw new ApiException('Cấu hình đã thay đổi hoặc component_access_token đã hết hiệu lực');
            }
            $url .= '?component_access_token=' . $this->component_access_token;
            $data = array_merge($data, ['component_appid' => $this->component_appid]);
        } else {
            if (!$this->authorizer_appid) {
                throw new ApiException('Thiếu authorizer_appid của bên ủy quyền');
            }
            $access_token = $this->getAccessToken($this->authorizer_appid);
            if (!$access_token) {
                throw new ApiException('Cấu hình đã thay đổi hoặc ủy quyền đã hết hiệu lực, vui lòng ủy quyền lại');
            }
            $url .= '?access_token=' . $access_token;
        }
        $res = $this->request($url, $method, $data);
        if (!$res) {
            throw new ApiException('Gửi yêu cầu đến máy chủ WeChat gặp lỗi, vui lòng thử lại sau');
        }
        return json_decode($res, true) ?: false;
    }

    /**
     * Chuyển dữ liệu XML thành mảng array
     * @param string $xml
     * @return array
     */
    public function xmlToArray($xml)
    {
        // Cấm tham chiếu thực thể (entity) xml bên ngoài
        libxml_disable_entity_loader(true);
        $res = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return (array)$res;
    }

    /**
     * Xóa phần đệm (padding) của bản rõ sau khi giải mã
     * @param decrypted Bản rõ sau khi giải mã
     * @return Bản rõ sau khi xóa phần đệm (padding)
     */
    public function decode($text)
    {

        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }

    /**
     * Giải mã bản mã hóa (ciphertext)
     * @param string $encodingAesKey Giải mã
     * @param string $encrypted Bản mã hóa cần giải mã
     * @return string Bản rõ có được sau khi giải mã
     */
    public function decrypt($encodingAesKey, $encrypted)
    {
        try {
            //Dùng BASE64 để decode chuỗi cần giải mã
            $ciphertext_dec = base64_decode($encrypted);
            $iv = substr(base64_decode($encodingAesKey . "="), 0, 16);
            $decrypted = openssl_decrypt($ciphertext_dec, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        } catch (\Throwable $e) {
            throw new ApiException($e->getMessage());
        }
        try {
            //Bỏ ký tự đệm (padding)
            $result = $this->decode($decrypted);
            //Bỏ chuỗi ngẫu nhiên 16 ký tự, network byte order và AppId
            if (strlen($result) < 16)
                return "";
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
        } catch (\Throwable $e) {
            throw new ApiException($e->getMessage());
        }
        return $xml_content;
    }
}
