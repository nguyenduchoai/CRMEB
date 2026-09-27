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

namespace crmeb\services\workerman\chat;


use app\services\kefu\service\StoreServiceRecordServices;
use app\services\kefu\service\StoreServiceServices;
use Channel\Client;
use crmeb\services\workerman\ChannelService;
use crmeb\services\workerman\Response;
use Workerman\Connection\TcpConnection;
use Workerman\Lib\Timer;
use Workerman\Worker;

class ChatService
{
    /**
     * @var Worker
     */
    protected $worker;

    /**
     * @var TcpConnection[]
     */
    protected $connections = [];

    /**
     * @var TcpConnection[]
     */
    protected $user = [];

    /**
     * CSKH trực tuyến
     * @var TcpConnection[]
     */
    protected $kefuUser = [];

    /**
     * @var ChatHandle
     */
    protected $handle;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var int
     */
    protected $timer;

    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
        $this->handle = new ChatHandle($this);
        $this->response = new Response();
    }

    public function setUser(TcpConnection $connection)
    {
        $this->user[$connection->user->uid] = $connection;
    }

    /**
     * Lấy CSKH đang online hiện tại
     * @return TcpConnection[]
     */
    public function kefuUser()
    {
        return $this->kefuUser;
    }

    /**
     * Thiết lập CSKH đang online hiện tại
     * @param TcpConnection $connection
     */
    public function setKefuUser(TcpConnection $connection, bool $isUser = true)
    {
        $this->kefuUser[$connection->kefuUser->uid] = $connection;
        if ($isUser) {
            $this->user[$connection->user->uid] = $connection;
        }
    }

    public function user($key = null)
    {
        return $key ? ($this->user[$key] ?? false) : $this->user;
    }

    public function onConnect(TcpConnection $connection)
    {
        $this->connections[$connection->id] = $connection;
        $connection->lastMessageTime = time();
    }

    public function onMessage(TcpConnection $connection, $res)
    {
        $connection->lastMessageTime = time();
        $res = json_decode($res, true);
        if (!$res || !isset($res['type']) || !$res['type'] || $res['type'] == 'ping') {
            return $this->response->connection($connection)->success('ping', ['now' => time()]);
        }
        if (!method_exists($this->handle, $res['type'])) return;
        try {
            $this->handle->{$res['type']}($connection, $res + ['data' => []], $this->response->connection($connection));
        } catch (\Throwable $e) {
        }
    }


    public function onWorkerStart(Worker $worker)
    {
        ChannelService::connet();

        Client::on('crmeb_chat', function ($eventData) use ($worker) {
            if (!isset($eventData['type']) || !$eventData['type']) return;
            $ids = isset($eventData['ids']) && count($eventData['ids']) ? $eventData['ids'] : array_keys($this->user);
            $fun = $eventData['fun'] ?? false;
            foreach ($ids as $id) {
                if (isset($this->user[$id])) {
                    if ($fun) {
                        $this->handle->{$eventData['type']}($this->user[$id], $eventData + ['data' => []], $this->response->connection($this->user[$id]));
                    } else {
                        $this->response->connection($this->user[$id])->success($eventData['type'], $eventData['data'] ?? null);
                    }
                }
            }
        });

        $this->timer = Timer::add(15, function () use (&$worker) {
            $time_now = time();
            foreach ($worker->connections as $connection) {
                if ($time_now - $connection->lastMessageTime > 120) {
                    //Timer kiểm tra người dùng hiện tại có offline không
                    if (isset($connection->user->uid) && !isset($connection->user->isTourist)) {
                        /** @var StoreServiceRecordServices $service */
                        $service = app()->make(StoreServiceRecordServices::class);
                        $service->updateRecord(['to_uid' => $connection->user->uid], ['online' => 0]);
                    }
                    $this->response->connection($connection)->close('timeout');
                    //Broadcast cho CSKH biết ai đã offline
                    foreach ($this->kefuUser as $uid => &$conn) {
                        if (isset($connection->user->uid) && $connection->user->uid != $uid) {
                            if (isset($conn->onlineUids) && ($key = array_search($connection->user->uid, $conn->onlineUids)) !== false) {
                                unset($conn->onlineUids[$key]);
                            }
                            $this->response->connection($conn)->send('user_online', ['to_uid' => $connection->user->uid, 'online' => 0]);
                        }
                    }
                }
            }
        });

        Timer::add(2, function () use (&$worker) {
            $uids = [];
            foreach ($this->user() as $uid => $connection) {
                if (!isset($connection->isTourist)) {
                    $uids[] = $uid;
                }
            }
            if ($uids) {
                //Ngoài người đang online hiện tại, tất cả còn lại đều offline
                /** @var StoreServiceRecordServices $service */
                $service = app()->make(StoreServiceRecordServices::class);
                $service->updateOnline(['notUid' => $uids], ['online' => 0]);
            }
            $kefuUid = array_keys($this->kefuUser());
            if ($kefuUid) {
                /** @var StoreServiceServices $kefuService */
                $kefuService = app()->make(StoreServiceServices::class);
                $kefuService->updateOnline(['notUid' => $kefuUid], ['online' => 0]);
            }
        });
    }


    public function onClose(TcpConnection $connection)
    {
        var_dump('close');
        unset($this->connections[$connection->id]);
        if (isset($connection->user->uid)) {
            unset($this->user[$connection->user->uid]);
        }
        if (isset($connection->kefuUser->uid)) {
            unset($this->kefuUser[$connection->kefuUser->uid]);
        }
    }
}
