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
namespace crmeb\services\easywechat\subscribe;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;

/**
 * Tin nhắn đăng ký Mini Program
 * Class ProgramSubscribe
 * @package crmeb\utils
 * @method $this
 * @method $this template(string $template_id) Đặt id mẫu
 * @method $this withTemplateId(string $template_id) Đặt id mẫu
 * @method $this andTemplateId(string $template_id) Đặt id mẫu
 * @method $this andTemplate(string $template_id) Đặt id mẫu
 * @method $this andUses(string $template_id) Đặt id mẫu
 * @method $this to(string $touser) Đặt opendid
 * @method $this andReceiver(string $touser) Đặt opendid
 * @method $this withReceiver(string $touser) Đặt opendid
 * @method $this with(array $data) Đặt nội dung gửi
 * @method $this andData(array $data) Đặt nội dung gửi
 * @method $this withData(array $data) Đặt nội dung gửi
 * @method $this data(array $data) Đặt nội dung gửi
 * @method $this withUrl(string $page) Đặt đường dẫn chuyển trang
 */
class ProgramSubscribe extends AbstractAPI
{

    /**
     * API thêm mẫu
     */
    const API_SET_TEMPLATE_ADD = 'https://api.weixin.qq.com/wxaapi/newtmpl/addtemplate';

    /**
     * API xóa tin nhắn mẫu
     */
    const API_SET_TEMPLATE_DEL = 'https://api.weixin.qq.com/wxaapi/newtmpl/deltemplate';

    /**
     * Lấy danh sách tin nhắn mẫu
     */
    const API_GET_TEMPLATE_LIST = 'https://api.weixin.qq.com/wxaapi/newtmpl/gettemplate';

    /**
     * Lấy phân loại tin nhắn mẫu
     */
    const API_GET_TEMPLATE_CATE = 'https://api.weixin.qq.com/wxaapi/newtmpl/getcategory';

    /**
     * Lấy từ khóa tin nhắn mẫu
     */
    const API_GET_TEMPLATE_KEYWORKS = 'https://api.weixin.qq.com/wxaapi/newtmpl/getpubtemplatekeywords';

    /**
     * Lấy mẫu chung
     */
    const API_GET_PUBLIC_TEMPLATE = 'https://api.weixin.qq.com/wxaapi/newtmpl/getpubtemplatetitles';

    /**
     * Gửi tin nhắn mẫu
     */
    const API_SUBSCRIBE_SEND = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send';

    /**
     * Attributes
     * @var array
     */
    protected $message = [
        'touser' => '',
        'template_id' => '',
        'page' => '',
        'data' => [],
    ];

    /**
     * Message backup.
     *
     * @var array
     */
    protected $messageBackup;

    protected $required = ['template_id', 'touser'];

    /**
     * ProgramSubscribeService constructor.
     * @param AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        parent::__construct($accessToken);

        $this->messageBackup = $this->message;

    }

    /**
     * Lấy danh sách mẫu hiện có
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function getTemplateList()
    {
        return $this->parseJSON('get', [self::API_GET_TEMPLATE_LIST]);
    }

    /**
     * Lấy danh sách mẫu công khai
     * @param string $ids
     * @param int $start
     * @param int $limit
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function getPublicTemplateList(string $ids, int $start = 0, int $limit = 10)
    {
        $params = [
            'ids' => $ids,
            'start' => $start,
            'limit' => $limit
        ];
        return $this->parseJSON('get', [self::API_GET_PUBLIC_TEMPLATE, $params]);
    }

    /**
     * Lấy phân loại mẫu
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function getTemplateCate()
    {
        return $this->parseJSON('get', [self::API_GET_TEMPLATE_CATE]);
    }

    /**
     * Lấy danh sách từ khóa dưới tiêu đề mẫu
     * @param string $tid id tiêu đề mẫu, có thể lấy qua API
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function getPublicTemplateKeywords(string $tid)
    {
        $params = [
            'tid' => $tid
        ];
        return $this->parseJSON('get', [self::API_GET_TEMPLATE_KEYWORKS, $params]);
    }

    /**
     * Thêm tin nhắn mẫu đăng ký
     * @param string $tid id tiêu đề mẫu, có thể lấy qua API, hoặc đăng nhập backend Mini Program để xem và lấy
     * @param array $kidList Số thứ tự mẫu, thứ tự từ khóa có thể kết hợp tự do (ví dụ [3,5,4] hoặc [4,5,3]), hỗ trợ tối đa 5, tối thiểu 2 từ khóa kết hợp
     * @param string $sceneDesc Mô tả tình huống dịch vụ, trong vòng 15 ký tự
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function addTemplate(string $tid, array $kidList, string $sceneDesc = '')
    {
        $params = [
            'tid' => $tid,
            'kidList' => $kidList,
            'sceneDesc' => $sceneDesc,
        ];
        return $this->parseJSON('json', [self::API_SET_TEMPLATE_ADD, $params]);
    }

    /**
     * Xóa tin nhắn mẫu
     * @param string $priTmplId
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function delTemplate(string $priTmplId)
    {
        $params = [
            'priTmplId' => $priTmplId
        ];
        return $this->parseJSON('json', [self::API_SET_TEMPLATE_DEL, $params]);
    }

    /**
     * Gửi tin nhắn đăng ký
     * @param array $data
     * @return \EasyWeChat\Support\Collection|null
     * @throws InvalidArgumentException
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function send(array $data = [])
    {
        $params = array_merge($this->message, $data);

        foreach ($params as $key => $value) {
            if (in_array($key, $this->required, true) && empty($value) && empty($this->message[$key])) {
                throw new InvalidArgumentException("Attribute '$key' can not be empty!");
            }

            $params[$key] = empty($value) ? $this->message[$key] : $value;
        }

        $params['data'] = $this->formatData($params['data']);

        $this->message = $this->messageBackup;

        return $this->parseJSON('json', [self::API_SUBSCRIBE_SEND, $params]);
    }

    /**
     * Đặt data gửi tin nhắn đăng ký
     * @param array $data
     * @return array
     */
    protected function formatData(array $data)
    {
        $return = [];

        foreach ($data as $key => $item) {
            if (is_scalar($item)) {
                $value = $item;
            } elseif (is_array($item) && !empty($item)) {
                if (isset($item['value'])) {
                    $value = strval($item['value']);
                } elseif (count($item) < 2) {
                    $value = array_shift($item);
                } else {
                    [$value] = $item;
                }
            } else {
                $value = 'error data item.';
            }

            $return[$key] = ['value' => $value];
        }

        return $return;
    }


    /**
     * Magic access..
     *
     * @param $method
     * @param $args
     * @return $this
     */
    public function __call($method, $args)
    {
        $map = [
            'template' => 'template_id',
            'templateId' => 'template_id',
            'uses' => 'template_id',
            'to' => 'touser',
            'receiver' => 'touser',
            'url' => 'page',
            'link' => 'page',
            'data' => 'data',
            'with' => 'data',
        ];

        if (0 === stripos($method, 'with') && strlen($method) > 4) {
            $method = lcfirst(substr($method, 4));
        }

        if (0 === stripos($method, 'and')) {
            $method = lcfirst(substr($method, 3));
        }

        if (isset($map[$method])) {
            $this->message[$map[$method]] = array_shift($args);
        }

        return $this;
    }

}
