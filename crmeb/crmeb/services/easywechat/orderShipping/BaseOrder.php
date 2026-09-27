<?php

namespace crmeb\services\easywechat\orderShipping;

use crmeb\exceptions\AdminException;
use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Support\Collection;


class BaseOrder extends AbstractAPI
{

    public $config;
    public $accessToken;

    const BASE_API = 'https://api.weixin.qq.com/';

    const ORDER = 'wxa/sec/order/';
    const EXPRESS = 'cgi-bin/express/delivery/open_msg/';

    const PATH = '/pages/goods/order_details/index';


    public function __construct(AccessToken $accessToken, $config)
    {
        parent::__construct($accessToken);
        $this->config = $config;
        $this->accessToken = $accessToken;
    }

    private function resultHandle(Collection $result)
    {
        if (empty($result)) {
            throw new AdminException('API WeChat trả về lỗi');
        }
        $res = $result->toArray();
        if ($res['errcode'] == 0) {
            return $res;
        } else {
            throw  new AdminException("Lỗi API WeChat: code = {$res['errcode']} msg = {$res['errmsg']}");
        }
    }

    /**
     * Giao hàng
     * @param $params
     * @return array
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     *
     * @date 2023/05/09
     * @author yyw
     */
    public function shipping($params)
    {
        return $this->resultHandle($this->parseJSON('POST', [self::BASE_API . self::ORDER . 'upload_shipping_info', json_encode($params, JSON_UNESCAPED_UNICODE)]));
    }

    /**
     * Hợp đơn
     * @param $params
     * @return array
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     *
     * @date 2023/05/09
     * @author yyw
     */
    public function combinedShipping($params)
    {
        return $this->resultHandle($this->parseJSON('POST', [self::BASE_API . self::ORDER . 'upload_combined_shipping_info', json_encode($params)]));
    }


    /**
     * Nhắc tin nhắn ký nhận
     * @param $params
     * @return array
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     *
     * @date 2023/05/09
     * @author yyw
     */
    public function notifyConfirm($params)
    {
        return $this->resultHandle($this->parseJSON('POST', [self::BASE_API . self::ORDER . 'notify_confirm_receive', json_encode($params)]));
    }


    /**
     * Truy vấn Mini Program đã mở dịch vụ quản lý thông tin giao hàng chưa
     * @return array
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     *
     * @date 2023/05/09
     * @author yyw
     */
    public function isManaged()
    {
        $params = [
            'appid' => $this->config['config']['mini_program']['app_id']
        ];
        return $this->resultHandle($this->parseJSON('POST', [self::BASE_API . self::ORDER . 'is_trade_managed', json_encode($params)]));
    }

    /**
     * Đặt liên kết chuyển trang
     * @param $path
     * @return array
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     *
     * @date 2023/05/10
     * @author yyw
     */
    public function setMesJumpPath($path)
    {
        $params = [
            'path' => $path
        ];
        return $this->resultHandle($this->parseJSON('POST', [self::BASE_API . self::ORDER . 'set_msg_jump_path', json_encode($params)]));
    }

    /**
     * Lấy danh sách id vận chuyển get_delivery_list
     * @return array
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     *
     * @date 2023/05/09
     * @author yyw
     */
    public function getDeliveryList()
    {
        return $this->resultHandle($this->parseJSON('POST', [self::BASE_API . self::EXPRESS . 'get_delivery_list', "{}"]));
    }
}
