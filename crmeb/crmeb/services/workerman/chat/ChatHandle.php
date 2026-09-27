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

namespace crmeb\services\workerman\chat;

use app\services\kefu\LoginServices;
use app\services\kefu\service\StoreServiceLogServices;
use app\services\kefu\service\StoreServiceRecordServices;
use app\services\kefu\service\StoreServiceServices;
use app\services\order\StoreOrderServices;
use app\services\product\product\StoreProductServices;
use app\services\user\UserServices;
use app\services\wechat\WechatKeyServices;
use app\services\wechat\WechatReplyServices;
use app\services\wechat\WechatUserServices;
use app\services\user\UserAuthServices;
use crmeb\exceptions\AuthException;
use crmeb\services\app\WechatService;
use crmeb\services\workerman\Response;
use crmeb\utils\Arr;
use think\facade\Log;
use Workerman\Connection\TcpConnection;

/**
 * Class ChatHandle
 * @package crmeb\services\workerman\chat
 */
class ChatHandle
{
    /**
     * @var ChatService
     */
    protected $service;

    /**
     * ChatHandle constructor.
     * @param ChatService $service
     */
    public function __construct(ChatService &$service)
    {
        $this->service = &$service;
    }

    /**
     * Đăng nhập CSKH
     * @param TcpConnection $connection
     * @param array $res
     * @param Response $response
     * @return bool|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function kefu_login(TcpConnection &$connection, array $res, Response $response)
    {
        if (!isset($res['data']) || !$token = $res['data']) {
            return $response->close([
                'msg' => 'Ủy quyền thất bại!'
            ]);
        }
        try {
            /** @var LoginServices $services */
            $services = app()->make(LoginServices::class);
            $kefuInfo = $services->parseToken($token);
        } catch (AuthException $e) {
            return $response->close([
                'msg' => $e->getMessage()
            ]);
        }

        $connection->kefuUser = $kefuInfo;
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        $connection->user = $userService->get($kefuInfo['uid'], ['uid', 'nickname']);
        if (!isset($connection->user->uid)) {
            return $response->close([
                'msg' => 'Tài khoản CSKH bạn đăng nhập không tồn tại'
            ]);
        }
        /** @var StoreServiceRecordServices $service */
        $service = app()->make(StoreServiceRecordServices::class);
        $service->updateRecord(['to_uid' => $connection->user->uid], ['online' => 1]);
        /** @var StoreServiceServices $service */
        $service = app()->make(StoreServiceServices::class);
        $service->update(['uid' => $connection->user->uid], ['online' => 1]);
        $this->service->setKefuUser($connection);

        return $response->success();
    }

    /**
     * Người dùng đăng nhập
     * @param TcpConnection $connection
     * @param array $res
     * @param Response $response
     * @return bool|null
     */
    public function login(TcpConnection &$connection, array $res, Response $response)
    {
        if (!isset($res['data']) || !$token = $res['data']) {
            return $response->close([
                'msg' => 'Ủy quyền thất bại!'
            ]);
        }

        try {
            /** @var UserAuthServices $services */
            $services = app()->make(UserAuthServices::class);
            $authInfo = $services->parseToken($token);
        } catch (AuthException $e) {
            return $response->close([
                'msg' => $e->getMessage()
            ]);
        }

        $connection->user = $authInfo['user'];
        $connection->tokenData = $authInfo['tokenData'];
        $this->service->setUser($connection);

        /** @var StoreServiceRecordServices $service */
        $service = app()->make(StoreServiceRecordServices::class);
        $service->updateRecord(['to_uid' => $connection->user->uid], ['online' => 1, 'type' => $res['form_type'] ?? 1]);
        $connections = $this->service->kefuUser();
        foreach ($connections as &$conn) {
            if (!isset($conn->onlineUids) || !in_array($connection->user->uid, $conn->onlineUids ?? [])) {
                $response->connection($conn)->send('user_online', ['to_uid' => $connection->user->uid, 'online' => 1]);
            }
            if (!isset($conn->onlineUids)) {
                $conn->onlineUids = [];
            }
            array_push($conn->onlineUids, $connection->user->uid);
            $this->service->setKefuUser($conn, false);
        }

        return $response->connection($connection)->success();
    }

    /**
     *
     * @param TcpConnection $connection
     * @param array $res
     * @param Response $response
     */
    public function to_chat(TcpConnection &$connection, array $res, Response $response)
    {
        $tourist_uid = $res['data']['tourist_uid'] ?? 0;
        if ($tourist_uid) {
            $connection->isTourist = true;
            $connection->user = (object)['uid' => $tourist_uid];
            $connections = $this->service->user();
            if (!isset($connections[$tourist_uid])) {
                $this->service->setUser($connection);
            }
        }
        $connection->chatToUid = $res['data']['id'] ?? 0;
        if (isset($connection->user)) {
            $uid = $connection->user->uid;
            if ($connection->chatToUid && !isset($connection->isTourist)) {
                /** @var StoreServiceRecordServices $service */
                $service = app()->make(StoreServiceRecordServices::class);
                $service->update(['user_id' => $uid, 'to_uid' => $connection->chatToUid], ['mssage_num' => 0]);
                /** @var StoreServiceLogServices $logServices */
                $logServices = app()->make(StoreServiceLogServices::class);
                $logServices->update(['uid' => $connection->chatToUid, 'to_uid' => $uid], ['type' => 1]);
            }
            $response->send('mssage_num', ['uid' => $connection->chatToUid, 'num' => 0, 'recored' => (object)[]]);
        }
    }

    /**
     * Người dùng gửi tin nhắn đến CSKH
     * @param TcpConnection $connection
     * @param array $res
     * @param Response $response
     * @return bool|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function chat(TcpConnection &$connection, array $res, Response $response)
    {
        $to_uid = $res['data']['to_uid'] ?? 0;
        $msn_type = $res['data']['type'] ?? 0;
        $msn = $res['data']['msn'] ?? '';
        $formType = $res['form_type'] ?? 0;
        //Là khách vãng lai
        $isTourist = $res['data']['is_tourist'] ?? 0;
        $tourist_uid = $res['data']['tourist_uid'] ?? 0;
        $isTourist = $isTourist && $tourist_uid;
        $tourist_avatar = $res['data']['tourist_avatar'] ?? '';
        $uid = $isTourist ? $tourist_uid : $connection->user->uid;
        if (!$to_uid) {
            return $response->send('err_tip', ['msg' => 'Người dùng không tồn tại']);
        }
        if ($to_uid == $uid) {
            return $response->send('err_tip', ['msg' => 'Không thể trò chuyện với chính mình']);
        }
        /** @var StoreServiceLogServices $logServices */
        $logServices = app()->make(StoreServiceLogServices::class);
        if (!in_array($msn_type, $logServices::MSN_TYPE)) {
            return $response->send('err_tip', ['msg' => 'Sai định dạng']);
        }
        $msn = trim(strip_tags(str_replace(["\n", "\t", "\r", "&nbsp;"], '', htmlspecialchars_decode($msn))));
        $data = compact('to_uid', 'msn_type', 'msn', 'uid');
        $data['add_time'] = time();
        $data['is_tourist'] = $res['data']['is_tourist'] ?? 0;
        $connections = $this->service->user();
        $online = isset($connections[$to_uid]) && isset($connections[$to_uid]->chatToUid) && $connections[$to_uid]->chatToUid == $uid;
        $data['type'] = $online ? 1 : 0;
        $data = $logServices->save($data);
        $data = $data->toArray();
        $data['_add_time'] = $data['add_time'];
        $data['add_time'] = strtotime($data['add_time']);
        if (!$isTourist) {
            if (isset($this->service->kefuUser()[$data['uid']])) {
                /** @var StoreServiceServices $userService */
                $userService = app()->make(StoreServiceServices::class);
                $_userInfo = $userService->get(['uid' => $data['uid']], ['nickname', 'avatar']);
            } else {
                /** @var UserServices $userService */
                $userService = app()->make(UserServices::class);
                $_userInfo = $userService->getUserInfo($data['uid'], 'nickname,avatar');
            }
            $data['nickname'] = $_userInfo['nickname'];
            $data['avatar'] = set_file_url($_userInfo['avatar']);
        } else {
            $avatar = sys_config('tourist_avatar');
            $_userInfo['avatar'] = $tourist_avatar ?: Arr::getArrayRandKey(is_array($avatar) ? $avatar : []);
            $_userInfo['nickname'] = 'Khách' . $uid;
            $data['nickname'] = $_userInfo['nickname'];
            $data['avatar'] = set_file_url($_userInfo['avatar']);
        }

        //Loại tin nhắn sản phẩm
        $data['productInfo'] = [];
        if ($msn_type == StoreServiceLogServices::MSN_TYPE_GOODS && $msn) {
            /** @var StoreProductServices $productServices */
            $productServices = app()->make(StoreProductServices::class);
            $productInfo = $productServices->getProductInfo((int)$msn, 'store_name,IFNULL(sales,0) + IFNULL(ficti,0) as sales,image,slider_image,price,vip_price,ot_price,stock,id');
            $data['productInfo'] = $productInfo ? $productInfo->toArray() : [];
        }
        //Loại tin nhắn đơn hàng
        $data['orderInfo'] = [];
        if ($msn_type == StoreServiceLogServices::MSN_TYPE_ORDER && $msn) {
            /** @var StoreOrderServices $orderServices */
            $orderServices = app()->make(StoreOrderServices::class);
            $order = $orderServices->getUserOrderDetail($msn, $uid);
            if ($order) {
                $order = $orderServices->tidyOrder($order->toArray(), true, true);
                $order['add_time_y'] = date('Y-m-d', $order['add_time']);
                $order['add_time_h'] = date('H:i:s', $order['add_time']);
                $data['orderInfo'] = $order;
            }
        }
        //Tự trả lời tin nhắn cho chính mình
        $response->send('chat', $data);

        //Người dùng gửi tin nhắn đến CSKH, kiểm tra CSKH hiện tại có đang đăng nhập không
        /** @var StoreServiceRecordServices $serviceRecored */
        $serviceRecored = app()->make(StoreServiceRecordServices::class);
        $unMessagesCount = $logServices->getMessageNum(['uid' => $uid, 'to_uid' => $to_uid, 'type' => 0, 'is_tourist' => $isTourist ? 1 : 0]);
        //Ghi lại lịch sử chat giữa người dùng hiện tại và người khác
        $data['recored'] = $serviceRecored->saveRecord($uid, $to_uid, $msn, $formType ?? 0, $msn_type, $unMessagesCount, $isTourist, $data['nickname'], $data['avatar']);
        //Có đang online không
        if ($online) {
            $response->connection($this->service->user()[$to_uid])->send('reply', $data);
        } else {
            //Người dùng đang online nhưng không chat với người dùng hiện tại, gửi số lượng tin chưa đọc cho người dùng hiện tại
            if (isset($connections[$to_uid])) {
                $data['recored']['nickname'] = $_userInfo['nickname'];
                $data['recored']['avatar'] = set_file_url($_userInfo['avatar']);
                $response->connection($this->service->user()[$to_uid])->send('mssage_num', [
                    'uid' => $uid,
                    'num' => $unMessagesCount,
                    'recored' => $data['recored']
                ]);
            }
            if ($isTourist) {
                return true;
            }
            //Người dùng không online
            /** @var WechatUserServices $wechatUserServices */
            $wechatUserServices = app()->make(WechatUserServices::class);
            $userInfo = $wechatUserServices->getOne(['uid' => $to_uid, 'user_type' => 'wechat'], 'nickname,subscribe,openid,headimgurl');
            if ($userInfo && $userInfo['subscribe'] && $userInfo['openid']) {
                $description = 'Bạn có tin nhắn mới, vui lòng kiểm tra!';
                if ($formType) {
                    $head = 'Thông báo tin nhắn tiếp nhận của CSKH';
                    $url = sys_config('site_url') . '/kefu/mobile_chat?toUid=' . $uid . '&nickname=' . $_userInfo['nickname'];
                } else {
                    $head = 'Thông báo tin nhắn phản hồi của CSKH';
                    $url = sys_config('site_url') . '/pages/extension/customer_list/chat?uid=' . $uid;
                }
                $message = WechatService::newsMessage($head, $description, $url, $_userInfo['avatar']);
                $userInfo = $userInfo->toArray();
                try {
                    WechatService::staffService()->message($message)->to($userInfo['openid'])->send();
                } catch (\Exception $e) {

                    Log::error($userInfo['nickname'] . 'Gửi thất bại' . $e->getMessage());
                }
            }
        }
        if (!isset($this->service->kefuUser()[$uid])) {
            //Kiểm tra có tự động trả lời không
            $wechatKeyServices = app()->make(WechatKeyServices::class);
            $replyId = $wechatKeyServices->value(['keys' => $msn, 'key_type' => 1], 'reply_id');
            if(!$replyId) $replyId = $wechatKeyServices->value(['keys_like' => $msn, 'key_type' => 1], 'reply_id');
            if ($replyId) {
                //Truy vấn nội dung trả lời
                $autoReplyData = app()->make(WechatReplyServices::class)->get($replyId)->toArray();
                $msgData = json_decode($autoReplyData['data'], true);
                $autoReplyMsn = $autoReplyData['type'] == 'text' ? $msgData['content'] : $msgData['src'];
                $autoReply['to_uid'] = $uid;
                $autoReply['msn_type'] = $autoReplyData['type'] == 'text' ? 1 : 3;
                $autoReply['msn'] = $autoReplyMsn;
                $autoReply['uid'] = $to_uid;
                $autoReply['add_time'] = time();
                $autoReply['is_tourist'] = 0;
                $autoReply['type'] = 1;
                $autoReply = $logServices->save($autoReply);
                $autoReply = $autoReply->toArray();
                $autoReply['_add_time'] = $autoReply['add_time'];
                $autoReply['add_time'] = strtotime($autoReply['add_time']);
                $kefuInfo = app()->make(StoreServiceServices::class)->get(['uid' => $to_uid], ['nickname', 'avatar']);
                $replyMessagesCount = $logServices->getMessageNum(['uid' => $to_uid, 'to_uid' => $uid, 'type' => 0, 'is_tourist' => $isTourist ? 1 : 0]);
                $autoReply['recored'] = $serviceRecored->saveRecord($to_uid, $uid, $autoReplyMsn, $formType ?? 0, $autoReplyData['type'] == 'text' ? 1 : 3, $replyMessagesCount, $isTourist, $kefuInfo['nickname'], $kefuInfo['avatar']);
                $response->connection($this->service->user()[$uid])->send('reply', $autoReply);
            }
        }
    }

    /**
     * Online/offline
     * @param TcpConnection $connection
     * @param array $res
     * @param Response $response
     */
    public function online(TcpConnection &$connection, array $res, Response $response)
    {
        $online = $res['data']['online'] ?? 0;
        $connections = $this->service->user();
        if (isset($connection->user->uid)) {
            $uids = $connection->user->uid;
            /** @var StoreServiceServices $service */
            $service = app()->make(StoreServiceServices::class);
            $service->update(['uid' => $uids], ['online' => $online]);
            //Broadcast cho người dùng đang chat với mình
            foreach ($connections as $uid => $conn) {
                if ($uid !== $uids && $uids == ($conn->chatToUid ?? 0)) {
                    $response->connection($conn)->send('online', ['online' => $online, 'uid' => $uids]);
                }
            }
        }
    }

    /**
     * Chuyển tiếp CSKH
     * @param TcpConnection $connection
     * @param array $res
     * @param Response $response
     */
    public function transfer(TcpConnection &$connection, array $res, Response $response)
    {
        $data = $res['data'] ?? [];
        $uid = $data['recored']['uid'] ?? 0;
        if ($uid && $this->service->user($uid)) {
            $data['recored']['online'] = 1;
        } else {
            $data['recored']['online'] = 0;
        }
        $response->send('transfer', $data);
    }
}
