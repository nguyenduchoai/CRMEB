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

namespace crmeb\traits;


use crmeb\utils\Queue;
use think\facade\Env;

/**
 * Thêm nhanh vào hàng đợi tin nhắn
 * Trait QueueTrait
 * @package crmeb\traits
 */
trait QueueTrait
{
    /**
     * Tên cột
     * @return null
     */
    protected static function queueName()
    {
        return null;
    }

    /**
     * Thêm vào hàng đợi
     * @param $action
     * @param array $data
     * @param string|null $queueName
     * @return mixed
     */
    public static function dispatch($action, array $data = [], string $queueName = null)
    {
        if (sys_config('queue_open', 0) == 1 && Env::get('cache.driver', 'file') == 'redis') {
            $queue = Queue::instance()->job(__CLASS__);
            if (is_array($action)) {
                $queue->data(...$action);
            } else if (is_string($action)) {
                $queue->do($action)->data(...$data);
            }
            if ($queueName) {
                $queue->setQueueName($queueName);
            } else if (static::queueName()) {
                $queue->setQueueName(static::queueName());
            }
            return $queue->push();
        } else {
            $className = '\\' . __CLASS__;
            $res = new $className();
            if (is_array($action)) {
                $res->doJob(...$action);
            } else {
                $res->$action(...$data);
            }
        }
    }

    /**
     * Thêm vào hàng đợi tin nhắn có độ trễ
     * @param int $secs
     * @param $action
     * @param array $data
     * @param string|null $queueName
     * @return mixed
     */
    public static function dispatchSecs(int $secs, $action, array $data = [], string $queueName = null)
    {
        if (sys_config('queue_open', 0) == 1 && Env::get('cache.driver', 'file') == 'redis') {
            $queue = Queue::instance()->job(__CLASS__)->secs($secs);
            if (is_array($action)) {
                $queue->data(...$action);
            } else if (is_string($action)) {
                $queue->do($action)->data(...$data);
            }
            if ($queueName) {
                $queue->setQueueName($queueName);
            } else if (static::queueName()) {
                $queue->setQueueName(static::queueName());
            }
            return $queue->push();
        }
    }
}
