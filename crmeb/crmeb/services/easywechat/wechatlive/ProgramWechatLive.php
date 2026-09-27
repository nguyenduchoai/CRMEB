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
namespace crmeb\services\easywechat\wechatlive;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\AccessToken;

/**
 * Class ProgramWechatLive
 * @package crmeb\services\wechatlive
 */
class ProgramWechatLive extends AbstractAPI
{

    /**
     * Lấy thông tin danh sách livestream
     */
    const API_WECHAT_LIVE = 'https://api.weixin.qq.com/wxa/business/getliveinfo';
    /**
     * Tạo phòng livestream
     */
    const CREATE_LIVE_ROOM = 'https://api.weixin.qq.com/wxaapi/broadcast/room/create';
    /**
     * Nhập sản phẩm vào phòng livestream
     */
    const LIVE_ROOM_ADD_GOODS = 'https://api.weixin.qq.com/wxaapi/broadcast/room/addgoods';

    /**
     * Lấy thông tin danh sách sản phẩm
     */
    const GOODS_LIST = 'https://api.weixin.qq.com/wxaapi/broadcast/goods/getapproved';
    /**
     * Thêm sản phẩm và duyệt
     */
    const GOODS_ADD = 'https://api.weixin.qq.com/wxaapi/broadcast/goods/add';
    /**
     * Rút lại yêu cầu duyệt
     */
    const GOODS_RESET_AUDIT = 'https://api.weixin.qq.com/wxaapi/broadcast/goods/resetaudit';
    /**
     * Gửi lại để duyệt
     */
    const GOODS_AUDIT = 'https://api.weixin.qq.com/wxaapi/broadcast/goods/autdit';
    /**
     * Xóa sản phẩm
     */
    const GOODS_DELETE = 'https://api.weixin.qq.com/wxaapi/broadcast/goods/delete';
    /**
     * Cập nhật sản phẩm
     */
    const GOODS_UPDATE = 'https://api.weixin.qq.com/wxaapi/broadcast/goods/update';
    /**
     * Lấy trạng thái sản phẩm
     */
    const GOODS_INFO = 'https://api.weixin.qq.com/wxa/business/getgoodswarehouse';
    /**
     * Lấy danh sách thành viên
     */
    const ROLE_LIST = 'https://api.weixin.qq.com/wxaapi/broadcast/role/getrolelist';
    /**
     * Tham số thêm phòng livestream
     * @var array
     */
    protected $create_data = [
        'name' => '',  // Tên phòng
        'coverImg' => '',   // Tải lên qua uploadfile, điền mediaID
        'startTime' => 0,   // Thời gian bắt đầu
        'endTime' => 0, // Thời gian kết thúc
        'anchorName' => '',  // Biệt danh streamer
        'anchorWechat' => '',  // ID WeChat của streamer
        'shareImg' => '',  //Tải lên qua uploadfile, điền mediaID
        'feedsImg' => '',   //Tải lên qua uploadfile, điền mediaID
        'isFeedsPublic' => 1, // Có mở thu thập chính thức không, 1 là mở, 0 là tắt
        'type' => 1, // Loại livestream, 1 là push stream, 0 là livestream bằng điện thoại
        'screenType' => 0,  // 1: ngang màn hình  0: dọc màn hình
        'closeLike' => 0, // Có tắt thích (like) không, 1 là tắt
        'closeGoods' => 0, // Có tắt kệ sản phẩm không, 1: tắt
        'closeComment' => 0, // Có mở bình luận không, 1: tắt
        'closeReplay' => 1, // Có tắt xem lại (playback) không, 1 là tắt
        'closeShare' => 0,   //  Có tắt chia sẻ không, 1 là tắt
        'closeKf' => 0 // Có tắt CSKH không, 1 là tắt
    ];

    /**
     * ProgramWechatLive constructor.
     * @param AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        parent::__construct($accessToken);
    }

    /**
     * Lấy danh sách phòng livestream
     * @param int $page
     * @param int $limit
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function getLiveInfo(int $page = 1, int $limit = 10)
    {
        $page = ($page - 1) * $limit;
        $params = [
            'start' => $page,
            'limit' => $limit
        ];
        return $this->parseJSON('json', [self::API_WECHAT_LIVE, $params]);
    }

    /**
     * Lấy video xem lại phòng livestream
     * @param int $room_id
     * @param int $page
     * @param int $limit
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function getLivePlayback(int $room_id, int $page = 1, int $limit = 10)
    {
        $page = ($page - 1) * $limit;
        $params = [
            'action' => 'get_replay',
            'room_id' => $room_id,
            'start' => $page,
            'limit' => $limit
        ];
        return $this->parseJSON('json', [self::API_WECHAT_LIVE, $params]);
    }

    /**
     * Tạo phòng livestream
     * @param $data
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function createRoom(array $data)
    {
        $params = array_merge($this->create_data, $data);
        return $this->parseJSON('json', [self::CREATE_LIVE_ROOM, $params]);
    }

    /**
     * Nhập sản phẩm vào phòng livestream
     * @param int $room_id
     * @param $ids
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function roomAddGoods(int $room_id, $ids)
    {
        $params = [
            'ids' => $ids,
            'roomId' => $room_id
        ];
        return $this->parseJSON('json', [self::LIVE_ROOM_ADD_GOODS, $params]);
    }

    /**
     * Lấy danh sách sản phẩm
     * @param $status
     * @param int $page
     * @param int $limit
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function getGoodsList($status, int $page = 0, $limit = 30)
    {
        $params = [
            'offset' => $page * $limit,
            'limit' => $limit,
            'status' => $status
        ];
        return $this->parseJSON('json', [self::GOODS_LIST, $params]);
    }

    /**
     * Lấy chi tiết sản phẩm
     * @param $ids
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function getGooodsInfo($ids)
    {
        $params = [
            'goods_ids' => $ids
        ];
        return $this->parseJSON('json', [self::GOODS_INFO, $params]);
    }

    /**
     * Thêm sản phẩm
     * @param string $coverImgUrl
     * @param string $name
     * @param int $priceType
     * @param string $url
     * @param $price
     * @param string $price2
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function addGoods(string $coverImgUrl, string $name, int $priceType, string $url, $price, $price2 = '')
    {
        $params = ['goodsInfo' => [
            'coverImgUrl' => $coverImgUrl,
            'name' => $name,
            'priceType' => $priceType,
            'price' => $price,
            'url' => $url
        ]];
        if ($priceType != 1) $params['goodsInfo']['price2'] = $price2;
        return $this->parseJSON('json', [self::GOODS_ADD, $params]);
    }

    /**
     * Rút lại yêu cầu duyệt sản phẩm
     * @param int $goodsId
     * @param int $auditId
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function resetauditGoods(int $goodsId, int $auditId)
    {
        $params = [
            'goodsId' => $goodsId,
            'auditId' => $auditId
        ];
        return $this->parseJSON('json', [self::GOODS_RESET_AUDIT, $params]);
    }

    /**
     * Gửi lại sản phẩm để duyệt
     * @param int $goodsId
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function auditGoods(int $goodsId)
    {
        $params = [
            'goodsId' => $goodsId
        ];
        return $this->parseJSON('json', [self::GOODS_AUDIT, $params]);
    }

    /**
     * Xóa sản phẩm
     * @param int $goodsId
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function deleteGoods(int $goodsId)
    {
        $params = [
            'goodsId' => $goodsId
        ];
        return $this->parseJSON('json', [self::GOODS_DELETE, $params]);
    }

    /**
     * Cập nhật sản phẩm
     * @param int $goodsId
     * @param string $coverImgUrl
     * @param string $name
     * @param int $priceType
     * @param string $url
     * @param $price
     * @param string $price2
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function updateGoods(int $goodsId, string $coverImgUrl, string $name, int $priceType, string $url, $price, $price2 = '')
    {
        $params = ['goodsInfo' => [
            'goodsId' => $goodsId,
            'coverImgUrl' => $coverImgUrl,
            'name' => $name,
            'priceType' => $priceType,
            'price' => $price,
            'url' => $url
        ]];
        if ($priceType != 1) $params['goodsInfo']['price2'] = $price2;
        return $this->parseJSON('json', [self::GOODS_UPDATE, $params]);
    }

    /**
     * Lấy danh sách thành viên
     * @param int $goodsId
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function getRoleList($role = 2, int $page = 0, $limit = 30, $keyword = '')
    {
        $params = [
            'role' => $role,
            'offset' => $page * $limit,
            'limit' => $limit,
            'keyword' => $keyword
        ];
        return $this->parseJSON('get', [self::ROLE_LIST, $params]);
    }
}
